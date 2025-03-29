<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ShippingCompany;
use App\Models\ShippingCompanySetting;
use App\Services\CmaCgmApiService;
use Ramsey\Uuid\Uuid;

class InvoiceController extends Controller
{
    // index
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|string|in:' . implode(',', Invoice::STATUS_LIST),
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
            'search' => 'nullable|string',
            'with' => 'nullable|array',
            'with.*' => 'nullable|in:shippingCompany,fees,client,payment',
        ]);

        $page = $request->page ?? 1;
        $per_page = $request->per_page ?? 10;

        $invoices = Invoice::whereClientId(Auth::user()->id)->with($request->with ?? []);

        if ($request->status) {
            $invoices->where('status', $request->status);
        }

        if ($request->date_from) {
            $invoices->where('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $invoices->where('created_at', '<=', $request->date_to);
        }

        if ($request->search) {
            $invoices->where('invoice_number',  $request->search);
        }

        if ($request->with) {
            $invoices = $invoices->with($request->with);
        }

        $invoices = $invoices->orderBy('created_at', 'desc')->paginate($per_page, ['*'], 'page', $page);

        return response()->json($invoices);
    }

    // show invoice
    public function show(Request $request, Invoice $invoice)
    {
        $invoiceData = $invoice->invoice_data;

        return response()->json([
            'invoice' => $invoice->load(['shippingCompany', 'fees']),
            'invoice_data' => $invoiceData,
        ]);
    }

    // download invoice
    public function downloadInvoice(Request $request, CmaCgmApiService $cmaCgmApiService, Invoice $invoice)
    {
        $request->validate([
            'company_code' => 'required|string|in:cmacgm,dpworld,dhl,fedex,ups,usps',
        ]);
        $pdf = null;
        switch ($request->company_code) {
            case 'cmacgm':
                $pdf = $cmaCgmApiService->generateInvoicePdf($invoice);
                break;
            default:
                return response()->json(['message' => 'Company not found'], 404);
        }
        return $pdf;
    }

    // search invoice by invoice number
    public function searchInvoiceByNumber(Request $request, $invoiceNumber)
    {
        $request->validate([
            'shipping_company_code' => 'required|string|in:' . implode(',', ShippingCompany::CODE_LIST),
        ]);

        $shippingCompany = ShippingCompany::where('code', $request->shipping_company_code)->first();

        if (!$shippingCompany) {
            return response()->json(['message' => 'Aucune compagnie maritime trouvée avec ce code'], 404);
        }

        // active shipping company
        if (!$shippingCompany->is_active) {
            return response()->json(['message' => 'La compagnie maritime est inactive'], 404);
        }

        switch ($shippingCompany->code) {
            case ShippingCompany::CMACGM:
                return $this->getCmaCgmInvoice($shippingCompany, $invoiceNumber);
            default:
                return response()->json(['message' => 'Company not found'], 404);
        }
    }

    private function getCmaCgmInvoice(ShippingCompany $shippingCompany, $invoiceNo)
    {
        // Initialiser le service avec la compagnie maritime
        $apiService = new CmaCgmApiService($shippingCompany);

        // Récupérer les paramètres optionnels
        // $behalfOf = $request->input('behalf_of');

        // Appeler l'API de CMA CGM pour récupérer les données de la facture

        $response = $apiService->getInvoiceData($invoiceNo);
        // Vérifier si nous avons reçu les données attendues
        if (!isset($response['invoice'])) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune facture trouvée avec ce numéro de facture. Veuillez vérifier le numéro de facture et réessayer.'
            ], 422);
        }

        // Extraire les informations de base de la facture
        $invoiceData = $response['invoice'];
        $totalChargesAmount = $response['totalChargesAmount'] ?? 0;
        $taxAmount = $response['taxAmount'] ?? 0;

        // Calcul de la commission basé sur le pourcentage configuré
        $commissionPercentage = ShippingCompanySetting::getSetting($shippingCompany, 'commission_percentage', 0.9);
        $commissionAmount = ($totalChargesAmount + $taxAmount) * ($commissionPercentage / 100);

        // Arrondir à 2 décimales
        $commissionAmount = round($commissionAmount, 2);

        // Calculer le montant total à payer incluant les frais
        $totalToPay = $totalChargesAmount + $taxAmount + $commissionAmount;

        // Créer une nouvelle facture
        $invoice = Invoice::make([
            'reference' => Uuid::uuid4(),
            'shipping_company_id' => $shippingCompany->id,
            'invoice_data' => $response, // Stocker la réponse complète
            'client_id' => Auth::user()->id,
            'client_type' => User::class,
            'invoice_number' => $invoiceNo,
            'invoice_type' => $invoiceData['invoiceType'] ?? 'Invoice',
            'amount' => $totalToPay,
            'currency' => $invoiceData['currencyCode'] ?? 'XOF',
            'status' => 'pending',
        ]);

        // Créer les frais de commission pour la nouvelle facture
        $invoiceFee = InvoiceFee::make([
            'invoice_id' => $invoice->id,
            'amount' => $commissionAmount,
            'currency' => $invoice->currency,
            'notes' => 'Frais de commission (' . $commissionPercentage . '%)'
        ]);

        $invoice['fees'] = $invoiceFee;
        $invoice['shipping_company_id'] = $shippingCompany->id;
        $invoice['shipping_company'] = $shippingCompany;
        $totalToPay = $totalChargesAmount + $taxAmount + $commissionAmount;
        $invoice['amount'] = $totalToPay;

        return [
            'invoice' => $invoice,
            'invoice_data' => $response,
            'total_charges_amount' => $totalChargesAmount,
            'tax_amount' => $taxAmount,
            'commission_amount' => $commissionAmount,
            'total_to_pay' => $totalToPay,
        ];
    }
}
