<?php

declare(strict_types=1);

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Program;
use App\Services\EnrollmentFinancialService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('purpose', 20)->default('initial')->after('enrollment_id');
            $table->unsignedInteger('tuition_amount')->default(0)->after('currency');
        });

        Payment::query()->orderBy('id')->chunkById(100, function ($payments): void {
            foreach ($payments as $payment) {
                $base = (int) ($payment->enrollment?->base_amount ?? 0);
                $payment->forceFill([
                    'purpose' => 'initial',
                    'tuition_amount' => $base,
                ])->saveQuietly();
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['enrollment_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['enrollment_id', 'purpose']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->unsignedInteger('tuition_list_amount')->nullable()->after('total_amount');
            $table->unsignedInteger('tuition_price_early')->nullable()->after('tuition_list_amount');
            $table->date('tuition_early_deadline')->nullable()->after('tuition_price_early');
            $table->unsignedInteger('tuition_price_dp')->nullable()->after('tuition_early_deadline');
            $table->unsignedInteger('tuition_discount_amount')->default(0)->after('tuition_price_dp');
            $table->string('tuition_discount_label')->nullable()->after('tuition_discount_amount');
            $table->unsignedInteger('amount_paid_tuition')->default(0)->after('tuition_discount_label');
            $table->unsignedInteger('balance_tuition_due')->default(0)->after('amount_paid_tuition');
        });

        Enrollment::query()->with('program')->orderBy('id')->chunkById(100, function ($enrollments): void {
            foreach ($enrollments as $enrollment) {
                $program = $enrollment->program;
                if (! $program instanceof Program) {
                    continue;
                }

                $list = (int) $program->price_full;
                $early = $program->price_early !== null ? (int) $program->price_early : null;
                $deadline = $program->early_deadline;
                $dp = (int) $program->price_dp;
                $discountAmount = 0;
                $discountLabel = null;
                if ($early !== null && $deadline !== null && $program->isEarlyBirdActive()) {
                    $discountAmount = max(0, $list - $early);
                    $discountLabel = 'Early bird';
                }

                $enrollment->forceFill([
                    'tuition_list_amount' => $list,
                    'tuition_price_early' => $early,
                    'tuition_early_deadline' => $deadline,
                    'tuition_price_dp' => $dp,
                    'tuition_discount_amount' => $discountAmount,
                    'tuition_discount_label' => $discountLabel,
                ])->saveQuietly();
            }
        });

        $financial = app(EnrollmentFinancialService::class);

        Enrollment::query()->orderBy('id')->chunkById(100, function ($enrollments) use ($financial): void {
            foreach ($enrollments as $enrollment) {
                $financial->recalculateEnrollmentFinancials($enrollment);
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'tuition_list_amount',
                'tuition_price_early',
                'tuition_early_deadline',
                'tuition_price_dp',
                'tuition_discount_amount',
                'tuition_discount_label',
                'amount_paid_tuition',
                'balance_tuition_due',
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['enrollment_id', 'purpose']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unique('enrollment_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['purpose', 'tuition_amount']);
        });
    }
};
