<?php

namespace App\Http\Controllers;

use App\Models\ShippingCompany;
use App\Models\Invoice;
use App\Models\ShippingCompanySetting;
use App\Services\CmaCgmApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use App\Models\InvoiceFee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

            // Calcul de la commission basé sur le pourcentage configuré
            $commissionPercentage = ShippingCompanySetting::getSetting($shippingCompany, 'commission_percentage', 0.9);
            $commissionAmount = ($totalChargesAmount + $taxAmount) * ($commissionPercentage / 100);

            // Arrondir à 2 décimales
            $commissionAmount = round($commissionAmount, 2);

            // Calculer le montant total à payer incluant les frais
            $totalToPay = $totalChargesAmount + $taxAmount + $commissionAmount;

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

                // Vérifier si des frais de commission existent déjà
                $existingFee = InvoiceFee::where('invoice_id', $invoice->id)->first();

                if ($existingFee) {
                    // Mettre à jour les frais existants
                    $existingFee->update([
                        'amount' => $commissionAmount,
                        'currency' => $invoice->currency,
                        'notes' => 'Frais de commission (' . $commissionPercentage . '%)'
                    ]);
                } else {
                    // Créer de nouveaux frais de commission
                    InvoiceFee::create([
                        'invoice_id' => $invoice->id,
                        'amount' => $commissionAmount,
                        'currency' => $invoice->currency,
                        'notes' => 'Frais de commission (' . $commissionPercentage . '%)'
                    ]);
                }
            } else {
                // Créer une nouvelle facture
                $invoice = Invoice::create([
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
                InvoiceFee::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $commissionAmount,
                    'currency' => $invoice->currency,
                    'notes' => 'Frais de commission (' . $commissionPercentage . '%)'
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
                'commission_amount' => $commissionAmount,
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
     * Payer une facture
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function payInvoice(Request $request, string $id) {}

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
            if (is_array($response)) {
                return response()->json([
                    'success' => true,
                    'data' => $response,
                    'total_count' => count($response) // Nombre total de factures retournées par l'API
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Aucune facture trouvée pour cet envoi'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des factures: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Télécharger ou prévisualiser le PDF d'une facture.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoicePdf(Request $request, string $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            // Initialiser le service
            $shippingCompany = $this->getCmaCgmCompany();
            $apiService = new CmaCgmApiService($shippingCompany);

            return $apiService->generateInvoicePdf($invoice);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement ou de la génération du PDF: ' . $e->getMessage(),
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
