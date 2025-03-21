<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
class ServiceFee extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'name',
        'percentage',
        'fixed_amount',
        'min_amount',
        'max_amount',
        'is_active',
        'description',
        'shipping_company_id',
        'payment_method_id',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the shipping company associated with the service fee.
     */
    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    /**
     * Get the payment method associated with the service fee.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the payments that use this service fee.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calculate the fee for a given amount.
     *
     * @param float $amount
     * @return float
     */
    public function calculateFee($amount)
    {
        $percentageFee = $amount * ($this->percentage / 100);
        $totalFee = $percentageFee + $this->fixed_amount;

        // Apply minimum if set
        if ($this->min_amount !== null && $totalFee < $this->min_amount) {
            $totalFee = $this->min_amount;
        }

        // Apply maximum if set
        if ($this->max_amount !== null && $totalFee > $this->max_amount) {
            $totalFee = $this->max_amount;
        }

        return $totalFee;
    }

    /**
     * Scope a query to only include active service fees.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
