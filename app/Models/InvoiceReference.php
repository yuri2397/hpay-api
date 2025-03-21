<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
class InvoiceReference extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'client_id',
        'shipping_company_id',
        'reference_type',
        'reference_value',
        'api_response',
        'is_processed',
        'processed_at',
    ];

    protected $casts = [
        'api_response' => 'array',
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
    ];


    /**
     * Get the client that owns the invoice reference.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the shipping company that owns the invoice reference.
     */
    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    /**
     * Scope a query to only include unprocessed references.
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    /**
     * Scope a query to only include processed references.
     */
    public function scopeProcessed($query)
    {
        return $query->where('is_processed', true);
    }
}
