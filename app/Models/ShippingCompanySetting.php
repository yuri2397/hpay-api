<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class ShippingCompanySetting extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'shipping_company_id',
        'key',
        'value',
    ];

    /**
     * Get the shipping company that owns the setting.
     */
    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    /**
     * Scope a query to find settings by key.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $key
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Get a setting for a specific shipping company by key.
     *
     * @param ShippingCompany $shippingCompany
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getSetting(ShippingCompany $shippingCompany, string $key, $default = null)
    {
        $setting = static::where('shipping_company_id', $shippingCompany->id)
            ->where('key', $key)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting for a specific shipping company.
     *
     * @param ShippingCompany $shippingCompany
     * @param string $key
     * @param mixed $value
     * @return ShippingCompanySetting
     */
    public static function setSetting(ShippingCompany $shippingCompany, string $key, $value)
    {
        return static::updateOrCreate(
            [
                'shipping_company_id' => $shippingCompany->id,
                'key' => $key
            ],
            ['value' => $value]
        );
    }
}