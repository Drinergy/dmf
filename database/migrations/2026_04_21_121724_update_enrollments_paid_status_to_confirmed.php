<?php

declare(strict_types=1);

use App\Enums\EnrollmentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('enrollments')
            ->where('status', 'paid')
            ->update(['status' => EnrollmentStatus::CONFIRMED->value]);
    }

    public function down(): void
    {
        // Irreversible data cleanup.
    }
};
