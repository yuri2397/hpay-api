<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\HasUuid;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'shipping_company_id',
        'client_id',
        'payment_id',
        'end_client_info',
        'reference',
        'invoice_type',
        'invoice_number',
        'amount',
        'currency',
        'status',
        'invoice_data',
        'document_path',
        'is_api_fetched',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'end_client_info' => 'array',
        'invoice_data' => 'array',
        'is_api_fetched' => 'boolean',
    ];


    /**
     * Get the shipping company that owns the invoice.
     */
    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    /**
     * Get the client that owns the invoice.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the payment associated with the invoice.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the commission transactions associated with this invoice.
     */
    public function commissionTransactions()
    {
        return $this->hasMany(CommissionTransaction::class);
    }

    /**
     * Scope a query to only include pending invoices.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include failed invoices.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include cancelled invoices.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
