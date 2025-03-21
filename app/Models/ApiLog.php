<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasUuid;

class ApiLog extends Model
{
    use HasFactory;
    use HasUuid;


    protected $fillable = [
        'shipping_company_id',
        'endpoint',
        'method',
        'request_data',
        'response_data',
        'status_code',
        'is_success',
        'error_message',
        'request_time',
        'response_time',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'request_time' => 'datetime',
        'response_time' => 'datetime',
    ];


    /**
     * Get the shipping company that owns the API log.
     */
    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    /**
     * Calculate the response time in milliseconds.
     */
    public function getResponseTimeInMillisecondsAttribute()
    {
        if (!$this->response_time || !$this->request_time) {
            return null;
        }

        return $this->response_time->diffInMilliseconds($this->request_time);
    }

    /**
     * Scope a query to only include successful API calls.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('is_success', true);
    }

    /**
     * Scope a query to only include failed API calls.
     */
    public function scopeFailed($query)
    {
        return $query->where('is_success', false);
    }
}
