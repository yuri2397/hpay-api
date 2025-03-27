<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class InvoiceFee extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoices_fees';

    protected $fillable = [
        'invoice_id',
        'amount',
        'currency',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the invoice that owns the fee.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Scope a query to only include fees for a specific invoice.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $invoiceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForInvoice($query, $invoiceId)
    {
        return $query->where('invoice_id', $invoiceId);
    }

    /**
     * Scope a query to only include fees with an amount greater than a specified value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $amount
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAmountGreaterThan($query, $amount)
    {
        return $query->where('amount', '>', $amount);
    }

    /**
     * Scope a query to sum total fees for an invoice.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $invoiceId
     * @return float
     */
    public static function totalForInvoice($invoiceId)
    {
        return static::forInvoice($invoiceId)->sum('amount');
    }
}