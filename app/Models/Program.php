<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'name', 'slug', 'category', 'tag',
        'price_full', 'price_dp', 'price_early', 'early_deadline',
        'early_bird_label', 'inclusions',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'inclusions'     => 'array',
        'early_deadline' => 'date',
        'is_active'      => 'boolean',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function isEarlyBirdActive(): bool
    {
        return $this->price_early !== null
            && $this->early_deadline !== null
            && now()->timezone('Asia/Manila')->startOfDay()->lte($this->early_deadline);
    }

    public function getActivePriceAttribute(): int
    {
        return $this->isEarlyBirdActive() ? $this->price_early : $this->price_full;
    }
}
