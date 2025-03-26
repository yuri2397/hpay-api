<?php

namespace App\Http\Controllers;

use App\Models\ShippingCompany;
use App\Models\Invoice;
use App\Services\CmaCgmApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class CmaCgmInvoiceController extends Controller
{
    /**
     * Service API CMA CGM
     *
     * @var \App\Services\CmaCgmApiService
     */
    protected $apiService;

    /**
     * Créer une nouvelle instance du contrôleur.
     *
     * @param \App\Services\CmaCgmApiService $apiService
     * @return void
     */
    public function __construct(CmaCgmApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Récupérer une facture par son numéro.
     *
     * @param Request $request
     * @param string $invoiceNo
     * @return \Illuminate\Http\Response
     */
    public function getInvoice(Request $request, string $invoiceNo)
    {
        DB::beginTransaction();
        try {
            // Récupérer la compagnie maritime CMA CGM
            $shippingCompany = $this->getCmaCgmCompany();

            // Initialiser le service avec la compagnie maritime
            $apiService = new CmaCgmApiService($shippingCompany);

            // Récupérer les paramètres optionnels
            $behalfOf = $request->input('behalf_of');

            // Appeler l'API
            $response = $apiService->getInvoiceData($invoiceNo, $behalfOf);

            // Vérifier si nous avons reçu les données attendues
            if (!isset($response['invoice'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format de réponse invalide de l\'API CMA CGM'
                ], 500);
            }

            // Extraire les informations de base de la facture
            $invoiceData = $response['invoice'];
            $payment = $response['payment'] ?? null;
            $totalChargesAmount = $response['totalChargesAmount'] ?? 0;
            $taxAmount = $response['taxAmount'] ?? 0;

            $totalToPay = $totalChargesAmount + $taxAmount;

            // Vérifier si la facture existe déjà dans notre système
            $existingInvoice = Invoice::where('invoice_number', $invoiceNo)
                ->where('shipping_company_id', $shippingCompany->id)
                ->first();

            if ($existingInvoice) {
                // Mettre à jour la facture existante
                $existingInvoice->update([
                    'reference' => $invoiceData['transportDocumentReference'] ?? null,
                    'invoice_type' => $invoiceData['invoiceType'] ?? 'Invoice',
                    'amount' => $totalToPay,
                    'currency' => $invoiceData['currencyCode'] ?? 'XOF',
                    'invoice_data' => $response, // Stocker la réponse complète
                ]);

                $invoice = $existingInvoice;
            } else {
                // Créer une nouvelle facture
                $invoice = Invoice::create([
                    'reference' => Uuid::uuid4(),
                    'shipping_company_id' => $shippingCompany->id,
                    'invoice_data' => $response, // Stocker la réponse complète
                    'client_id' => auth()->id(),
                    'client_type' => User::class,
                    'invoice_number' => $invoiceNo,
                    'invoice_type' => $invoiceData['invoiceType'] ?? 'Invoice',
                    'amount' => $totalToPay,
                    'currency' => $invoiceData['currencyCode'] ?? 'XOF',
                    'status' => 'pending',
                ]);
            }

            $invoice->invoice_data = null;

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice' => $invoice,
                'payer' => $payment['payer'] ?? null,
                'payable_to' => $payment['payableTo'] ?? null,
                'total_charges' => $totalChargesAmount,
                'tax_amount' => $taxAmount,
                'total_to_pay' => $totalToPay,
                'payment_status' => $payment['paymentStatus'] ?? null,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la facture: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer la liste des factures pour un envoi.
     *
     * @param Request $request
     * @param string $transportDocumentReference
     * @return \Illuminate\Http\Response
     */
    public function getShipmentInvoices(Request $request, string $transportDocumentReference)
    {
        try {
            // Récupérer la compagnie maritime CMA CGM
            $shippingCompany = $this->getCmaCgmCompany();

            // Initialiser le service avec la compagnie maritime
            $apiService = new CmaCgmApiService($shippingCompany);

            // Récupérer les paramètres optionnels
            $behalfOf = $request->input('behalf_of');
            $range = $request->header('Range');

            // Appeler l'API
            $response = $apiService->getShipmentInvoices($transportDocumentReference, $behalfOf, $range);

            // Synchroniser les factures avec notre base de données
            $invoices = [];
            foreach ($response as $invoiceData) {
                $invoiceNo = $invoiceData['invoiceNo'] ?? null;

                if ($invoiceNo) {
                    // Vérifier si la facture existe déjà
                    $existingInvoice = Invoice::where('invoice_number', $invoiceNo)
                        ->where('shipping_company_id', $shippingCompany->id)
                        ->first();

                    if ($existingInvoice) {
                        // Mettre à jour la facture existante
                        $existingInvoice->update([
                            'reference' => $invoiceData['transportDocumentReference'] ?? null,
                            'invoice_type' => $invoiceData['invoiceType'] ?? 'Invoice',
                            'amount' => $invoiceData['invoiceAmount'] ?? 0,
                            'currency' => $invoiceData['currencyCode'] ?? 'USD',
                            'invoice_data' => $invoiceData,
                            'client_id' => auth()->id(),
                            'client_type' => User::class,
                        ]);

                        $invoices[] = $existingInvoice;
                    } else {
                        // Créer une nouvelle facture
                        $invoice = Invoice::create([
                            'shipping_company_id' => $shippingCompany->id,
                            'invoice_number' => $invoiceNo,
                            'reference' => $invoiceData['transportDocumentReference'] ?? null,
                            'invoice_type' => $invoiceData['invoiceType'] ?? 'Invoice',
                            'amount' => $invoiceData['invoiceAmount'] ?? 0,
                            'currency' => $invoiceData['currencyCode'] ?? 'USD',
                            'status' => 'pending',
                            'invoice_data' => $invoiceData,
                            'is_api_fetched' => true,
                        ]);

                        $invoices[] = $invoice;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $invoices,
                'total_count' => count($response) // Nombre total de factures retournées par l'API
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des factures: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Télécharger le PDF d'une facture.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoicePdf(string $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            if (!$invoice->document_path || !Storage::disk('local')->exists($invoice->document_path)) {
                // Si nous n'avons pas le PDF stocké localement, essayons de le récupérer via l'API
                $shippingCompany = $this->getCmaCgmCompany();
                $apiService = new CmaCgmApiService($shippingCompany);

                $response = $apiService->getInvoiceCopy($invoice->invoice_number);

                if (isset($response['invoiceDocument']) && !empty($response['invoiceDocument'])) {
                    $pdfContent = base64_decode($response['invoiceDocument']);
                    $pdfPath = 'invoices/cmacgm/' . $invoice->invoice_number . '_' . Str::random(8) . '.pdf';
                    Storage::disk('local')->put($pdfPath, $pdfContent);

                    // Mettre à jour le chemin du document
                    $invoice->update(['document_path' => $pdfPath]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'PDF de facture non disponible'
                    ], 404);
                }
            }

            // Retourner le fichier PDF
            $filename = 'facture_' . $invoice->invoice_number . '.pdf';
            return Storage::disk('local')->download($invoice->document_path, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement du PDF: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer la compagnie maritime CMA CGM.
     *
     * @return \App\Models\ShippingCompany
     * @throws \Exception
     */
    protected function getCmaCgmCompany()
    {
        $shippingCompany = ShippingCompany::where('name', 'CMA CGM')
            ->where('is_active', true)
            ->first();

        if (!$shippingCompany) {
            throw new \Exception('Compagnie maritime CMA CGM non trouvée ou inactive');
        }

        return $shippingCompany;
    }
}
