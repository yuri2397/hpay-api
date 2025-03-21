<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class CommissionTransaction extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'commission_account_id',
        'invoice_id',
        'payment_id',
        'type',
        'amount',
        'currency',
        'balance_after',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];


    /**
     * Get the commission account that owns the transaction.
     */
    public function commissionAccount()
    {
        return $this->belongsTo(CommissionAccount::class);
    }

    /**
     * Get the invoice related to the transaction.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the payment related to the transaction.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Scope a query to only include deposit transactions.
     */
    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    /**
     * Scope a query to only include withdrawal transactions.
     */
    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdrawal');
    }

    /**
     * Scope a query to only include commission transactions.
     */
    public function scopeCommissions($query)
    {
        return $query->where('type', 'commission');
    }

    /**
     * Scope a query to only include adjustment transactions.
     */
    public function scopeAdjustments($query)
    {
        return $query->where('type', 'adjustment');
    }
}
