<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Catalog resource authorization from per-assistant permission codes.
 *
 * @author CKD
 *
 * @created 2026-04-25
 */
trait ChecksCatalogPermissions
{
    /**
     * Resource key segment after `catalog.` (e.g. categories, packages).
     */
    abstract protected static function catalogResourceKey(): string;

    public static function canViewAny(): bool
    {
        return static::catalogCan('view');
    }

    public static function canCreate(): bool
    {
        return static::catalogCan('create');
    }

    public static function canEdit(Model $record): bool
    {
        return static::catalogCan('update');
    }

    public static function canDelete(Model $record): bool
    {
        return static::catalogCan('delete');
    }

    /**
     * Use for table bulk actions (e.g. delete) — Filament does not call canDelete() per row for those.
     */
    public static function currentUserCanCatalogAction(string $action): bool
    {
        return static::catalogCan($action);
    }

    private static function catalogCan(string $action): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->isAssistant()) {
            return false;
        }

        $code = 'catalog.'.static::catalogResourceKey().'.'.$action;

        return $user->hasPermission($code);
    }
}
