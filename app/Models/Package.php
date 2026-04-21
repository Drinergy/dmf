<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'tag',
        'category_id',
        'category',
        'price_full',
        'price_early',
        'early_deadline',
        'early_bird_label',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'early_deadline' => 'date',
        'is_active' => 'boolean',
    ];

    public function categoryModel(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'package_program', 'package_id', 'program_id')
            ->withPivot(['sort_order'])
            ->orderBy('package_program.sort_order');
    }

    public function isEarlyBirdActive(): bool
    {
        return $this->price_early !== null
            && $this->early_deadline !== null
            && now()->timezone('Asia/Manila')->startOfDay()->lte($this->early_deadline);
    }

    public function getActivePriceAttribute(): int
    {
        return $this->isEarlyBirdActive() ? (int) $this->price_early : (int) $this->price_full;
    }

    public function getDownpaymentAmountAttribute(): int
    {
        return (int) round(((int) $this->price_full) * 0.5);
    }
}
