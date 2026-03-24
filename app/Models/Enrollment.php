<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        // Reference & status
        'reference_number', 'status',

        // Personal info
        'first_name', 'middle_name', 'surname', 'birthday', 'sex',

        // Contact & address
        'phone', 'email', 'facebook',
        'addr_street', 'addr_city', 'addr_province', 'addr_zip',
        'deliv_street', 'deliv_city', 'deliv_province', 'deliv_zip',

        // Academic
        'school', 'year_level', 'year_graduated', 'taker_status',

        // Program & Payment
        'program_id', 'schedule_id',

        // Payment
        'payment_type', 'base_amount', 'convenience_fee', 'total_amount',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    /* ── Relationships ─────────────────────────────────────────── */

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /* ── Helpers ───────────────────────────────────────────────── */

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->surname}");
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'DMF-' . strtoupper(substr(md5(uniqid('', true)), 0, 8));
        } while (static::where('reference_number', $ref)->exists());

        return $ref;
    }
}
