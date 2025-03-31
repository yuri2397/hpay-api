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

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_PAID,
        self::STATUS_FAILED,
        self::STATUS_CANCELLED,
    ];
    protected $fillable = [
        'shipping_company_id',
        'reference',
        'invoice_type',
        'invoice_number',
        'amount',
        'currency',
        'status',
        'invoice_data',
        'client_id',
        'client_type',
    ];

    protected $appends = [
        'formatted_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'invoice_data' => 'array',
    ];

    protected $hidden = [
        'invoice_data',
        'updated_at',
        'deleted_at',
    ];


    /**
     * Get the shipping company that owns the invoice.
     */
    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    /**
     * Get the fees associated with the invoice.
     */
    public function fees()
    {
        return $this->hasOne(InvoiceFee::class);
    }

    /**
     * Get the client that owns the invoice.
     */
    public function client()
    {
        return $this->morphTo();
    }

    /**
     * Get the payment associated with the invoice.
     */
    public function payment()
    {
        return $this->morphTo();
    }

    /**
     * Get the commission transactions associated with this invoice.
     */
    public function commissionTransactions()
    {
        return $this->morphMany(CommissionTransaction::class, 'commissionable');
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

    // get formmated amount avec le symbole de la monnaie et ajouter les espaces par millier
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2, ',', ' ') . ' ' . $this->currency;
    }

}
