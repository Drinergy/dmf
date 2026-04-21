<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_items', function (Blueprint $table) {
            if (Schema::hasColumn('enrollment_items', 'schedule_id')) {
                $table->unsignedBigInteger('schedule_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        // No-op.
    }
};
