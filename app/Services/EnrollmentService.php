<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Schedule;
use Illuminate\Support\Collection;

class EnrollmentService
{
    /**
     * Get all active programs grouped by category for the frontend.
     */
    public function getGroupedActivePrograms(): Collection
    {
        return Program::query()
            ->where('is_active', true)
            ->with([
                'categoryModel:id,name',
                'schedules' => fn ($q) => $q->where('is_active', true)->orderBy('created_at', 'desc'),
            ])
            ->orderBy('sort_order')
            ->get()
            ->groupBy(fn (Program $program) => $program->category_label);
    }

    /**
     * Create a new enrollment record from validated form data.
     */
    public function createEnrollment(array $data): Enrollment
    {
        $program = Program::where('slug', $data['program'])->firstOrFail();
        $scheduleId = $data['schedule_id'] ?? null;

        if (empty($scheduleId)) {
            $activeSchedules = $program->schedules()
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get(['id']);

            if ($activeSchedules->count() === 1) {
                $scheduleId = $activeSchedules->first()->id;
            }
        }

        $baseAmount = ($data['payment_type'] === 'full')
            ? $program->active_price
            : $program->price_dp;

        $convenienceFee = EnrollmentPricingService::CONVENIENCE_FEE_PESOS;
        $totalAmount = $baseAmount + $convenienceFee;

        $list = (int) $program->price_full;
        $early = $program->price_early !== null ? (int) $program->price_early : null;
        $deadline = $program->early_deadline;
        $dpSnapshot = (int) $program->price_dp;

        $discountAmount = 0;
        $discountLabel = null;
        if ($early !== null && $deadline !== null && $program->isEarlyBirdActive()) {
            $discountAmount = max(0, $list - $early);
            $discountLabel = 'Early bird';
        }

        $enrollment = Enrollment::create([
            'reference_number' => Enrollment::generateReference(),
            'status' => 'pending',

            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'surname' => $data['surname'],
            'birthday' => $data['birthday'],
            'sex' => $data['sex'],

            'phone' => $data['phone'],
            'email' => $data['email'],
            'facebook' => $data['facebook'] ?? null,

            'addr_street' => $data['addr_street'],
            'addr_city' => $data['addr_city'],
            'addr_province' => $data['addr_province'],
            'addr_zip' => $data['addr_zip'],
            'deliv_street' => $data['deliv_street'] ?? null,
            'deliv_city' => $data['deliv_city'] ?? null,
            'deliv_province' => $data['deliv_province'] ?? null,
            'deliv_zip' => $data['deliv_zip'] ?? null,

            'school' => $data['school'],
            'year_level' => $data['year_level'],
            'year_graduated' => $data['year_graduated'] ?? null,
            'taker_status' => $data['taker_status'],

            'program_id' => $program->id,
            'schedule_id' => $scheduleId,

            'payment_type' => $data['payment_type'],
            'base_amount' => $baseAmount,
            'convenience_fee' => $convenienceFee,
            'total_amount' => $totalAmount,

            'tuition_list_amount' => $list,
            'tuition_price_early' => $early,
            'tuition_early_deadline' => $deadline,
            'tuition_price_dp' => $dpSnapshot,
            'tuition_discount_amount' => $discountAmount,
            'tuition_discount_label' => $discountLabel,
            'amount_paid_tuition' => 0,
            'balance_tuition_due' => 0,
        ]);

        $enrollment->balance_tuition_due = EnrollmentPricingService::balanceTuitionDue($enrollment);
        $enrollment->saveQuietly();

        return $enrollment;
    }

    public function getScheduleForEnrollmentData(array $data): ?Schedule
    {
        $scheduleId = $data['schedule_id'] ?? null;
        if (empty($scheduleId)) {
            return null;
        }

        return Schedule::query()->with('program')->find($scheduleId);
    }
}
