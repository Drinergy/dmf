<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'name', 'slug', 'category', 'tag',
        'category_id',
        'price_full', 'price_early', 'early_deadline',
        'early_bird_label',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'early_deadline' => 'date',
        'is_active' => 'boolean',
    ];

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_program', 'program_id', 'package_id')
            ->withPivot(['sort_order']);
    }

    public function categoryModel(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->categoryModel?->name ?: (string) $this->category;
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

    public function getDownpaymentAmountAttribute(): int
    {
        return (int) round(((int) $this->price_full) * 0.5);
    }
}
