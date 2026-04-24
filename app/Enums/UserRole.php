<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * User role enum for admin panel access control.
 *
 * Roles are stored as strings in the database. New roles can be added here
 * and wired to permissions via the role_permissions table without schema changes.
 *
 * @author CKD
 *
 * @created 2026-04-24
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Assistant = 'assistant';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Assistant => 'Assistant',
        };
    }

    /**
     * Returns roles that may be assigned to non-owner accounts (assistants).
     *
     * @return array<int, self>
     */
    public static function assignable(): array
    {
        return [self::Assistant];
    }

    /**
     * Filament Select options for role assignment forms.
     *
     * @return array<string, string>
     */
    public static function assignableOptions(): array
    {
        return collect(self::assignable())
            ->mapWithKeys(fn (self $role) => [$role->value => $role->label()])
            ->all();
    }
}
