<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentItem extends Model
{
    protected $fillable = [
        'enrollment_id',
        'program_id',
        'schedule_id',
        'status',
        'program_name_snapshot',
        'program_slug_snapshot',
        'schedule_label_snapshot',
        'schedule_mode_snapshot',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
