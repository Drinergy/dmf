<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            // ── Reference & Status ────────────────────────────────
            $table->string('reference_number', 20)->unique();   // e.g. DMF-A1B2C3D4
            $table->string('status', 30)->default('pending');   // pending | confirmed | cancelled

            // ── Personal Information ──────────────────────────────
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('surname');
            $table->date('birthday');
            $table->string('sex', 10);                          // Male | Female

            // ── Contact & Address ─────────────────────────────────
            $table->string('phone', 20);
            $table->string('email');
            $table->string('facebook')->nullable();

            $table->string('addr_street');
            $table->string('addr_city');
            $table->string('addr_province');
            $table->string('addr_zip', 10);

            $table->string('deliv_street')->nullable();
            $table->string('deliv_city')->nullable();
            $table->string('deliv_province')->nullable();
            $table->string('deliv_zip', 10)->nullable();

            // ── Academic Background ───────────────────────────────
            $table->string('school');
            $table->string('year_level', 20);
            $table->string('year_graduated', 10)->nullable();
            $table->string('taker_status', 20);                 // First taker | Re-taker

            // ── Program & Payment ─────────────────────────────────
            $table->foreignId('program_id')->constrained('programs')->onDelete('restrict');
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->onDelete('set null');

            // ── Payment Preference ────────────────────────────────
            $table->string('payment_type', 20);                 // full | downpayment
            $table->unsignedInteger('base_amount');             // Price at time of enrollment
            $table->unsignedInteger('convenience_fee')->default(50);
            $table->unsignedInteger('total_amount');

            $table->timestamps();

            // Indexes
            $table->index('email');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
