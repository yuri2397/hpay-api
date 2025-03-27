<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $invoice->invoice_number }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <!-- Inclure Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.5;
            color: #1f2937;
            font-size: 14px;
        }

        .watermark {
            position: fixed;
            top: 40%;
            left: 25%;
            transform: rotate(-45deg);
            opacity: 0.08;
            font-size: 100px;
            z-index: -1000;
            color: #1f2937;
        }

        .page-break {
            page-break-after: always;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f3f4f6;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border: 1px solid #e5e7eb;
        }

        .amount {
            text-align: right;
        }
    </style>
</head>

<body class="bg-white">
    <!-- Filigrane si c'est une copie -->
    @if ($invoice->status !== 'pending')
        <div class="watermark">COPIE</div>
    @endif

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- En-tête de la facture -->
        <div class="flex justify-between items-start mb-8">
            <div>
                @if ($logo)
                    <img src="{{ $logo }}" alt="Logo" class="h-16 mb-4">
                @endif
                <p class="font-bold">{{ $payment['payer']['name'] ?? 'Client' }}</p>
                <p><span class="font-bold">Ville:</span> {{ $payment['payer']['city'] ?? '' }}</p>
                <p><span class="font-bold">Adresse:</span> {{ $payment['payer']['address1'] ?? '' }}</p>
                <p><span class="font-bold">Pays:</span> {{ $payment['payer']['country'] ?? '' }}</p>

            </div>

            <div class="text-right">
                <h2 class="text-xl font-bold text-gray-800">FACTURE</h2>
                <p class="text-gray-600"><strong>B/L:</strong> {{ $invoiceDetails['transportDocumentReference'] ?? 'N/A' }}</p>
                <p class="text-gray-600"><strong>N°</strong> {{ $invoice->invoice_number }}</p>
                <p class="text-gray-600"><strong>Date:</strong>
                    {{ isset($invoiceDetails['invoiceDate']) ? date('d/m/Y', strtotime($invoiceDetails['invoiceDate'])) : date('d/m/Y') }}
                </p>

            </div>
        </div>
        <div class="mt-4 text-center">
            <h1 class="text-2xl font-bold text-gray-800">{{ $issuer['name'] ?? 'CMA CGM' }}</h1>
        </div>

        <!-- Informations client et livraison -->
        <div class="flex justify-between mb-8">

        </div>

        <!-- Tableau des frais -->
        <table class="mb-8 w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 border-b text-left">Description</th>
                    <th class="py-2 px-4 border-b text-left">Type</th>
                    <th class="py-2 px-4 border-b text-right">Montant ({{ $invoice->currency }})</th>
                </tr>
            </thead>
            <tbody>
                {{-- @foreach ($charges as $charge)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $charge['chargeDescription'] ?? 'Frais' }}</td>
                    <td class="py-2 px-4 border-b">{{ $charge['chargeType'] ?? '-' }}</td>
                    <td class="py-2 px-4 border-b text-right">{{ number_format($charge['chargeAmountInvoicingCurrency'] ?? 0, 2, ',', ' ') }}</td>
                </tr>
                @endforeach --}}

                <!-- Ligne pour les taxes -->
                {{-- @if ($taxAmount > 0)
                <tr>
                    <td class="py-2 px-4 border-b">Taxes</td>
                    <td class="py-2 px-4 border-b">TAX</td>
                    <td class="py-2 px-4 border-b text-right">{{ number_format($taxAmount, 2, ',', ' ') }}</td>
                </tr>
                @endif
--}}
                {{-- invoiceAmount de la facture : c'est le montant net de la facture --}}
                <tr>
                    <td class="py-2 px-4 border-b ">Montant total facture</td>
                    <td class="py-2 px-4 border-b ">{{ $invoice->currency }}</td>
                    <td class="py-2 px-4 border-b text-right  font-bold">
                        {{ number_format($netInvoiceAmount, 2, ',', ' ') }}</td>
                </tr>
                <!-- Ligne pour les frais de commission -->
                <tr>
                    <td class="py-2 px-4 border-b ">Frais de service</td>
                    <td class="py-2 px-4 border-b ">{{ env('APP_NAME') }}</td>
                    <td class="py-2 px-4 border-b text-right font-bold">
                        {{ number_format($commissionAmount, 2, ',', ' ') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 font-bold">
                    <td class="py-2 px-4 border-b" colspan="2">Total</td>
                    <td class="py-2 px-4 border-b text-right">{{ number_format($totalAmount, 2, ',', ' ') }}
                        {{ $invoice->currency }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Informations de paiement -->
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Informations de paiement</h3>
            <div class="border rounded p-4 bg-gray-50">
                <p><strong>Statut:</strong>
                    @if ($invoice->status == 'pending')
                        <span class="text-yellow-600">En attente de paiement</span>
                    @elseif($invoice->status == 'paid')
                        <span class="text-green-600">Payée</span>
                    @else
                        <span class="text-red-600">{{ ucfirst($invoice->status) }}</span>
                    @endif
                </p>
                <p><strong>Méthode de paiement:</strong> Virement bancaire</p>
                <p><strong>Payable à:</strong> {{ $payment['payableTo']['name'] ?? 'CMA CGM' }}</p>
                <p><strong>Date d'échéance:</strong>
                    {{ isset($invoiceDetails['invoiceDueDate']) ? date('d/m/Y', strtotime($invoiceDetails['invoiceDueDate'])) : 'N/A' }}
                </p>
            </div>
        </div>
    </div>
</body>

</html>
