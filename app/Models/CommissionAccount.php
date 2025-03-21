<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasUuid;

class CommissionAccount extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'client_id',
        'balance',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];


    /**
     * Get the client that owns the commission account.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the commission transactions for the account.
     */
    public function transactions()
    {
        return $this->hasMany(CommissionTransaction::class);
    }

    /**
     * Add commission to the account and create a transaction record.
     *
     * @param float $amount
     * @param string $description
     * @param Invoice|null $invoice
     * @param Payment|null $payment
     * @return CommissionTransaction
     */
    public function addCommission($amount, $description = null, $invoice = null, $payment = null)
    {
        $this->balance += $amount;
        $this->save();

        return $this->recordTransaction('commission', $amount, $description, $invoice, $payment);
    }

    /**
     * Withdraw from the account and create a transaction record.
     *
     * @param float $amount
     * @param string $description
     * @return CommissionTransaction
     */
    public function withdraw($amount, $description = null)
    {
        $this->balance -= $amount;
        $this->save();

        return $this->recordTransaction('withdrawal', -$amount, $description);
    }

    /**
     * Adjust the account balance and create a transaction record.
     *
     * @param float $amount
     * @param string $description
     * @return CommissionTransaction
     */
    public function adjust($amount, $description = null)
    {
        $this->balance += $amount;
        $this->save();

        return $this->recordTransaction('adjustment', $amount, $description);
    }

    /**
     * Record a commission transaction.
     *
     * @param string $type
     * @param float $amount
     * @param string|null $description
     * @param Invoice|null $invoice
     * @param Payment|null $payment
     * @return CommissionTransaction
     */
    protected function recordTransaction($type, $amount, $description = null, $invoice = null, $payment = null)
    {
        return CommissionTransaction::create([
            'commission_account_id' => $this->id,
            'invoice_id' => $invoice ? $invoice->id : null,
            'payment_id' => $payment ? $payment->id : null,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $this->balance,
            'description' => $description,
            'currency' => $this->currency,
        ]);
    }
}
