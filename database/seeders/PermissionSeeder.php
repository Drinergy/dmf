<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Permission;
use App\Models\User;
use App\Support\PermissionCodes;
use Illuminate\Database\Seeder;

/**
 * Seeds permission definitions and assigns a legacy-compatible preset to existing assistants.
 *
 * @author CKD
 *
 * @created 2026-04-25
 */
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PermissionCodes::definitions() as $code => $label) {
            Permission::query()->firstOrCreate(
                ['code' => $code],
                ['label' => $label],
            );
        }

        $preset = PermissionCodes::legacyAssistantPreset();

        User::query()
            ->where('role', UserRole::Assistant->value)
            ->each(function (User $user) use ($preset): void {
                if ($user->permissions()->count() > 0) {
                    return;
                }

                $user->syncPermissionsByCode($preset);
            });
    }
}
