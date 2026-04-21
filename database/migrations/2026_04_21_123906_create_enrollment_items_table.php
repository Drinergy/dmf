<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('enrollment_items')) {
            return;
        }

        Schema::create('enrollment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->restrictOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->nullOnDelete();

            $table->string('status', 30)->default('pending');

            // Snapshots to retain history even if program/package composition changes.
            $table->string('program_name_snapshot');
            $table->string('program_slug_snapshot');
            $table->string('schedule_label_snapshot')->nullable();
            $table->string('schedule_mode_snapshot')->nullable();

            $table->timestamps();

            $table->index(['enrollment_id', 'program_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_items');
    }
};
