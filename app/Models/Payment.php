<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'enrollment_id',
        'paymongo_checkout_session_id',
        'paymongo_payment_intent_id',
        'paymongo_payment_id',
        'payment_method',
        'amount', 'currency',
        'status', 'paid_at',
        'paymongo_payload',
    ];

    protected $casts = [
        'paid_at'           => 'datetime',
        'paymongo_payload'  => 'array',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /** Mark this payment as successfully paid. */
    public function markPaid(string $paymongoPaymentId, array $rawPayload = []): void
    {
        $this->update([
            'status'                 => 'paid',
            'paymongo_payment_id'    => $paymongoPaymentId,
            'paid_at'                => now(),
            'paymongo_payload'       => $rawPayload,
        ]);

        $this->enrollment->update(['status' => 'confirmed']);
    }
}
