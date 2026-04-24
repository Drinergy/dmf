<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the permissions and role_permissions tables.
 *
 * These tables are intentionally sparse on day 1 (role-only enforcement is
 * in use). They are seeded with permission codes that map to the capabilities
 * assistants have, so the plumbing exists when per-user overrides are needed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('permission_code');
            $table->timestamps();

            $table->unique(['role', 'permission_code']);
            $table->foreign('permission_code')
                ->references('code')
                ->on('permissions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};
