<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class ShippingCompany extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'name',
        'api_key',
        'api_secret',
        'api_endpoint',
        'is_active',
        'description',
        'contact_email',
        'contact_phone',
        'logo',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the invoices for the shipping company.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the API logs for the shipping company.
     */
    public function apiLogs()
    {
        return $this->hasMany(ApiLog::class);
    }

    /**
     * Get the service fees associated with this shipping company.
     */
    public function serviceFees()
    {
        return $this->hasMany(ServiceFee::class);
    }

    /**
     * Get the invoice references associated with this shipping company.
     */
    public function invoiceReferences()
    {
        return $this->hasMany(InvoiceReference::class);
    }
}
