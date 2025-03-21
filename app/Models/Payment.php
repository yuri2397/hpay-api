<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Payment extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'client_id',
        'end_client_info',
        'payment_method_id',
        'transaction_id',
        'amount',
        'invoice_amount',
        'fee_amount',
        'service_fee_id',
        'currency',
        'status',
        'payment_date',
        'notes',
        'payment_response',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'invoice_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'end_client_info' => 'array',
        'payment_response' => 'array',
        'metadata' => 'array',
        'payment_date' => 'datetime',
    ];

    /**
     * Get the client that made the payment.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the payment method used for this payment.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the service fee applied to this payment.
     */
    public function serviceFee()
    {
        return $this->belongsTo(ServiceFee::class);
    }

    /**
     * Get the invoices associated with this payment.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the commission transactions associated with this payment.
     */
    public function commissionTransactions()
    {
        return $this->hasMany(CommissionTransaction::class);
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include processing payments.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include refunded payments.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }
}
