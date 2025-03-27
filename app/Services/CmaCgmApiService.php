<?php

namespace App\Services;

use App\Models\ApiLog;
use App\Models\Invoice;
use App\Models\ShippingCompany;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CmaCgmApiService
{
    /**
     * URL de base de l'API v1 pour les copies de factures
     *
     * @var string
     */
    protected $baseUrlV1;

    /**
     * URL de base de l'API v2 pour les données de factures
     *
     * @var string
     */
    protected $baseUrlV2;

    /**
     * URL pour obtenir le token OAuth2
     *
     * @var string
     */
    protected $tokenUrl;

    /**
     * Client ID pour l'authentification OAuth2
     *
     * @var string
     */
    protected $clientId;

    /**
     * Client Secret pour l'authentification OAuth2
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * Token d'accès OAuth2
     *
     * @var string|null
     */
    protected $accessToken;

    /**
     * Date d'expiration du token
     *
     * @var \Carbon\Carbon|null
     */
    protected $tokenExpires;

    /**
     * La compagnie maritime associée
     *
     * @var \App\Models\ShippingCompany|null
     */
    protected $shippingCompany;

    /**
     * Créer une nouvelle instance du service.
     *
     * @param \App\Models\ShippingCompany|null $shippingCompany
     * @return void
     */
    public function __construct(?ShippingCompany $shippingCompany = null)
    {
        $this->shippingCompany = $shippingCompany;

        // Configuration par défaut
        $this->baseUrlV1 = config('services.cmacgm.api_endpoint_v1');
        $this->baseUrlV2 = config('services.cmacgm.api_endpoint_v2');
        $this->tokenUrl = config('services.cmacgm.token_url');

        // Si une compagnie maritime est fournie, utiliser ses identifiants
        $this->clientId = config('services.cmacgm.client_id');
        $this->clientSecret = config('services.cmacgm.client_secret');
    }


    /**
     * Obtenir les données détaillées d'une facture par son numéro (API v2).
     *
     * @param string $invoiceNo Numéro de facture CMA CGM
     * @param string|null $behalfOf Code du client final (obligatoire pour les tiers)
     * @return array
     * @throws \Exception
     */
    public function getInvoiceData(string $invoiceNo, ?string $behalfOf = null)
    {
        $endpoint = "/invoices/{$invoiceNo}/data";
        $params = [];

        if ($behalfOf) {
            $params['behalfOf'] = $behalfOf;
        }


        if (!$this->accessToken || !$this->tokenExpires || $this->tokenExpires->isPast()) {
            $this->accessToken = $this->getAccessToken('invoicepartnerdata:load:be');
        }

        $response = Http::asForm()->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->accessToken
        ])->get($this->baseUrlV2 . $endpoint, $params);

        Log::info('CMA CGM API Response', ['response' => $response->json()]);

        return $response->json();
    }

    /**
     * Obtenir la liste des factures liées à un document de transport.
     * Fonctionne pour les deux versions de l'API.
     *
     * @param string $transportDocumentReference Référence du document de transport CMA CGM
     * @param string|null $behalfOf Code du client final (obligatoire pour les tiers)
     * @param string|null $range Pagination (ex: "0-49")
     * @param string $version Version de l'API ('v1' ou 'v2')
     * @return array
     * @throws \Exception
     */
    public function getShipmentInvoices(string $transportDocumentReference, ?string $behalfOf = null, ?string $range = null, string $version = 'v2')
    {
        $endpoint = "/shipments/{$transportDocumentReference}/invoices";
        $params = [];
        $headers = [];

        if ($behalfOf) {
            $params['behalfOf'] = $behalfOf;
        }

        if ($range) {
            $headers['Range'] = $range;
        } else {
            $headers['Range'] = "0-49"; // Valeur par défaut
        }


        // return $this->makeRequest('GET', $endpoint, $params, $scope, $headers, $version);
    }



    /**
     * Obtenir ou renouveler le token d'accès OAuth2.
     *
     * @param string $scope Scope requis
     * @return string
     * @throws \Exception
     */
    protected function getAccessToken(string $scope)
    {
        // Vérifier si nous avons un token valide
        if ($this->accessToken && $this->tokenExpires && $this->tokenExpires->isFuture()) {
            return $this->accessToken;
        }

        try {
            // Exactement comme dans la documentation curl de CMA CGM
            $response = Http::asForm()->post($this->tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => $scope
            ]);

            // Log de la demande de token pour débogage
            Log::debug('CMA CGM Token Request', [
                'request_headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'request_body' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => 'CENSORED',
                    'client_secret' => 'CENSORED',
                    'scope' => $scope
                ],
                'response_status' => $response->status(),
                'response_body' => $response->json() ? json_encode($response->json()) : $response->body()
            ]);

            if (!$response->successful()) {
                $errorData = $response->json();
                $error = $errorData['error'] ?? 'unknown_error';
                $errorDescription = $errorData['error_description'] ?? 'Unknown error';

                throw new \Exception("OAuth2 Error: {$error} - {$errorDescription}", $response->status());
            }

            $data = $response->json();

            if (!isset($data['access_token'])) {
                throw new \Exception("Invalid token response: access_token not found in response");
            }

            $this->accessToken = $data['access_token'];
            $expiresIn = $data['expires_in'] ?? 3600; // Par défaut 1 heure si non spécifié

            // Définir l'expiration avec une marge de sécurité (30 secondes avant l'expiration réelle)
            $this->tokenExpires = now()->addSeconds($expiresIn - 30);

            return $this->accessToken;
        } catch (\Exception $e) {
            Log::error('CMA CGM OAuth2 Error', [
                'client_id' => $this->clientId,
                'scope' => $scope,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception("Failed to obtain access token: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Générer un PDF de facture en utilisant Tailwind CSS.
     *
     * @param Invoice $invoice La facture à convertir en PDF
     * @param bool $returnView Si vrai, retourne la vue au lieu du contenu PDF
     * @return mixed Contenu PDF ou vue selon le paramètre $returnView
     * @throws \Exception
     */
    public function generateInvoicePdf(Invoice $invoice, bool $returnView = false)
    {
        try {
            // Récupérer les données de la facture
            $invoiceData = $invoice->invoice_data;

            if (empty($invoiceData)) {
                // Si les données ne sont pas déjà stockées, essayer de les récupérer via l'API
                $invoiceData = $this->getInvoiceData($invoice->invoice_number);

                // Mettre à jour la facture avec les données récupérées
                $invoice->update(['invoice_data' => $invoiceData]);
            }

            // Récupérer les frais de commission associés à cette facture
            $fees = \App\Models\InvoiceFee::where('invoice_id', $invoice->id)->get();
            $commissionAmount = $fees->sum('amount');

            // Extraire les informations nécessaires pour le PDF
            $invoiceDetails = $invoiceData['invoice'] ?? [];
            $issuer = $invoiceData['issuer'] ?? [];
            $charges = $invoiceData['charges'] ?? [];
            $payment = $invoiceData['payment'] ?? [];
            $totalChargesAmount = $invoiceData['totalChargesAmount'] ?? 0;
            $taxAmount = $invoiceData['taxAmount'] ?? 0;
            $netInvoiceAmount = $totalChargesAmount + $taxAmount;
            $totalAmount = $totalChargesAmount + $taxAmount + $commissionAmount;

            // Récupérer le logo de CMA CGM (s'il existe)
            $logo = config('app.logo');
            $shippingCompany = $invoice->shippingCompany;

            // Préparer les données pour la vue
            $data = [
                'invoice' => $invoice,
                'issuer' => $issuer,
                'invoiceDetails' => $invoiceDetails,
                'charges' => $charges,
                'payment' => $payment,
                'totalChargesAmount' => $totalChargesAmount,
                'netInvoiceAmount' => $netInvoiceAmount,
                'taxAmount' => $taxAmount,
                'commissionAmount' => $commissionAmount,
                'totalAmount' => $totalAmount,
                'logo' => $logo,
                'company' => $shippingCompany
            ];

            // Générer la vue du PDF
            return view('pdfs.cma-cgm-invoice', $data);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du PDF: ' . $e->getMessage(), ['invoice_id' => $invoice->id]);
            throw new \Exception('Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }
}
