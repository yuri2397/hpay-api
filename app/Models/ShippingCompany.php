<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class ShippingCompany extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    const CMACGM = 'cmacgm';
    const DPWORLD = 'dpworld';
    const DHL = 'dhl';
    const FEDEX = 'fedex';
    const UPS = 'ups';
    const USPS = 'usps';

    const CODE_LIST = [
        self::CMACGM,
        self::DPWORLD,
        self::DHL,
        self::FEDEX,
        self::UPS,
        self::USPS,
    ];
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
        'code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'api_key',
        'api_secret',
        'api_endpoint',
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
