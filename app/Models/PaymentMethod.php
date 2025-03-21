<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class PaymentMethod extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'name',
        'provider',
        'configuration',
        'is_active',
        'description',
    ];

    protected $casts = [
        'configuration' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the payments that use this payment method.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the service fees associated with this payment method.
     */
    public function serviceFees()
    {
        return $this->hasMany(ServiceFee::class);
    }

    /**
     * Scope a query to only include active payment methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
