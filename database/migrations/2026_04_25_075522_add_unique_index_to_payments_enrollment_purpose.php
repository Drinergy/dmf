<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add the unique constraint first so MySQL has an index to back the FK
        // while the old non-unique index is dropped.
        Schema::table('payments', function (Blueprint $table) {
            $table->unique(['enrollment_id', 'purpose'], 'payments_enrollment_purpose_unique');
        });

        // MySQL prevents dropping an index that is the sole index backing a FK.
        // Adding the unique index above satisfies the FK, so the drop can proceed.
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['enrollment_id', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_enrollment_purpose_unique');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['enrollment_id', 'purpose']);
        });
    }
};
