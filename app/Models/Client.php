<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
class Client extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'name',
        'type',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'tax_id',
        'client_references',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'client_references' => 'array',
        'type' => 'string',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    /**
     * Get the invoices for the client.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the payments for the client.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the commission account for the client (if it's a carrier).
     */
    public function commissionAccount()
    {
        return $this->hasOne(CommissionAccount::class);
    }

    /**
     * Check if the client is a carrier.
     */
    public function isCarrier()
    {
        return $this->type === 'carrier';
    }

    /**
     * Get the invoice references for the client.
     */
    public function invoiceReferences()
    {
        return $this->hasMany(InvoiceReference::class);
    }
}
