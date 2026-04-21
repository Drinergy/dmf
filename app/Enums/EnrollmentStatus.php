<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Single source of truth for Enrollment status values and admin UI labels.
 *
 * IMPORTANT:
 * - DB values are persisted as strings (this enum is a string-backed cast).
 * - Do not rename enum values once deployed without a forward migration.
 */
enum EnrollmentStatus: string
{
    case PENDING = 'pending';
    case PARTIALLY_PAID = 'partially_paid';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Awaiting payment',
            self::PARTIALLY_PAID => 'Enrolled — DP paid (balance due)',
            self::CONFIRMED => 'Enrolled (fully paid)',
            self::CANCELLED => 'Cancelled',
            self::FAILED => 'Failed',
        };
    }

    public function filamentColor(): string|array
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PARTIALLY_PAID, self::CONFIRMED => 'success',
            self::CANCELLED, self::FAILED => 'danger',
        };
    }

    /**
     * Filament SelectFilter options array: [value => label].
     *
     * @return array<string, string>
     */
    public static function filterOptions(): array
    {
        return [
            self::PENDING->value => self::PENDING->label(),
            self::PARTIALLY_PAID->value => self::PARTIALLY_PAID->label(),
            self::CONFIRMED->value => self::CONFIRMED->label(),
            self::CANCELLED->value => self::CANCELLED->label(),
        ];
    }

    public static function tryFromMixed(mixed $value): ?self
    {
        if ($value instanceof self) {
            return $value;
        }

        return is_string($value) ? self::tryFrom($value) : null;
    }
}
