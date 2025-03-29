<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceFee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // invoices statistics
    public function invoicesStatistics(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $invoices = Invoice::whereClientId(Auth::user()->id);

        if ($request->start_date) {
            // on prends pas en core les heures
            $invoices->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->start_date)));
        }

        if ($request->end_date) {
            // on prends pas en core les heures
            $invoices->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->end_date)));
        }

        $invoices = $invoices->get();

        // amounts
        $total_amount = $invoices->sum('amount');
        $total_paied_amount = $invoices->where('status', 'paid')->sum('amount');
        $total_pending_amount = $invoices->where('status', 'pending')->sum('amount');
        $total_failed_amount = $invoices->where('status', 'failed')->sum('amount');
        $total_cancelled_amount = $invoices->where('status', 'cancelled')->sum('amount');

        // count
        $total_invoices = $invoices->count();
        $total_paied_invoices = $invoices->where('status', 'paid')->count();
        $total_pending_invoices = $invoices->where('status', 'pending')->count();
        $total_failed_invoices = $invoices->where('status', 'failed')->count();
        $total_cancelled_invoices = $invoices->where('status', 'cancelled')->count();


        // fees
        $fees = InvoiceFee::whereHas('invoice', function ($query) use ($request) {
            $query->whereClientId(Auth::user()->id);
            if ($request->start_date) {
                $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->start_date)));
            }
            if ($request->end_date) {
                $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->end_date)));
            }
            $query->where('status', 'paid');
        });
        $total_fees = $fees->sum('amount');
        $total_fees_amount = $fees->sum('amount');
        $total_fees_count = $fees->count();

        $data = [
            'total_amount' => (int) $total_amount,
            'total_paied_amount' => (int) $total_paied_amount,
            'total_pending_amount' => (int) $total_pending_amount,
            'total_failed_amount' => (int) $total_failed_amount,
            'total_cancelled_amount' => (int) $total_cancelled_amount,
            'total_invoices' => (int) $total_invoices,
            'total_paied_invoices' => (int) $total_paied_invoices,
            'total_pending_invoices' => (int) $total_pending_invoices,
            'total_failed_invoices' => (int) $total_failed_invoices,
            'total_cancelled_invoices' => (int) $total_cancelled_invoices,
            'total_fees' => (int) $total_fees,
            'total_fees_amount' => (int) $total_fees_amount,
            'total_fees_count' => (int) $total_fees_count,
        ];
        return response()->json($data);
    }

    // last ten invoices
    public function lastTenInvoices()
    {
        $invoices = Invoice::with(['shippingCompany', 'fees'])->whereClientId(Auth::user()->id)->orderBy('created_at', 'desc')->take(10)->get();
        return response()->json($invoices);
    }

    // fees statistics
    public function feesStatistics()
    {
        $fees = InvoiceFee::whereHas('invoice', function ($query) {
            $query->whereClientId(Auth::user()->id);
        })->get();

        $total_fees = $fees->sum('amount');
        $total_fees_amount = $fees->sum('amount');
        $total_fees_count = $fees->count();

        $data = [
            'total_fees' => (int) $total_fees,
            'total_fees_amount' => (int) $total_fees_amount,
            'total_fees_count' => (int) $total_fees_count,
        ];
        return response()->json($data);
    }
}
