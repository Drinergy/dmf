<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Program;
use App\Services\EnrollmentFinancialService;
use App\Services\EnrollmentPricingService;
use App\Services\EnrollmentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentFinancialLedgerTest extends TestCase
{
    use RefreshDatabase;

    private function createProgram(array $overrides = []): Program
    {
        return Program::create(array_merge([
            'name' => 'Ledger Program',
            'slug' => 'ledger-program',
            'category' => 'Individual Programs (Theoretical)',
            'tag' => null,
            'price_full' => 10_000,
            'price_dp' => 5_000,
            'price_early' => 8_000,
            'early_deadline' => '2026-07-15',
            'early_bird_label' => 'Early',
            'inclusions' => ['A'],
            'is_active' => true,
            'sort_order' => 0,
        ], $overrides));
    }

    private function baseEnrollmentPayload(Program $program, array $overrides = []): array
    {
        return array_merge([
            'program' => $program->slug,
            'schedule_id' => null,
            'first_name' => 'Ana',
            'middle_name' => null,
            'surname' => 'Santos',
            'birthday' => '2000-01-01',
            'sex' => 'Female',
            'phone' => '09171234567',
            'email' => 'ana@example.com',
            'facebook' => null,
            'addr_street' => '1 Main',
            'addr_city' => 'Manila',
            'addr_province' => 'Metro Manila',
            'addr_zip' => '1000',
            'deliv_street' => null,
            'deliv_city' => null,
            'deliv_province' => null,
            'deliv_zip' => null,
            'school' => 'U',
            'year_level' => 'Graduate',
            'year_graduated' => '2024',
            'taker_status' => 'First taker',
            'payment_type' => 'downpayment',
        ], $overrides);
    }

    public function test_downpayment_initial_payment_sets_partially_paid_and_balance(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01', 'Asia/Manila')->startOfDay());

        $program = $this->createProgram();
        $enrollment = app(EnrollmentService::class)->createEnrollment($this->baseEnrollmentPayload($program));

        $this->assertSame(10_000, $enrollment->fresh()->tuition_list_amount);
        $this->assertSame(8_000, $enrollment->fresh()->tuition_price_early);

        Payment::query()->create([
            'enrollment_id' => $enrollment->id,
            'purpose' => Payment::PURPOSE_INITIAL,
            'payment_method' => 'gcash',
            'amount' => (5_000 + EnrollmentPricingService::CONVENIENCE_FEE_PESOS) * 100,
            'currency' => 'PHP',
            'tuition_amount' => 5_000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        app(EnrollmentFinancialService::class)->recalculateEnrollmentFinancials($enrollment->fresh());

        $enrollment->refresh();

        $this->assertSame('partially_paid', $enrollment->status);
        $this->assertSame(5_000, $enrollment->amount_paid_tuition);
        $this->assertSame(3_000, $enrollment->balance_tuition_due);
        $this->assertSame(3_000, $enrollment->computed_balance_tuition_due);

        Carbon::setTestNow();
    }

    public function test_recalculate_is_idempotent(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01', 'Asia/Manila')->startOfDay());

        $program = $this->createProgram();
        $enrollment = app(EnrollmentService::class)->createEnrollment($this->baseEnrollmentPayload($program));

        Payment::query()->create([
            'enrollment_id' => $enrollment->id,
            'purpose' => Payment::PURPOSE_INITIAL,
            'payment_method' => 'gcash',
            'amount' => (5_000 + EnrollmentPricingService::CONVENIENCE_FEE_PESOS) * 100,
            'currency' => 'PHP',
            'tuition_amount' => 5_000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $financial = app(EnrollmentFinancialService::class);
        $financial->recalculateEnrollmentFinancials($enrollment->fresh());
        $financial->recalculateEnrollmentFinancials($enrollment->fresh());

        $enrollment->refresh();

        $this->assertSame(5_000, $enrollment->amount_paid_tuition);
        $this->assertSame('partially_paid', $enrollment->status);

        Carbon::setTestNow();
    }

    public function test_recalculate_infers_tuition_from_charged_amount_when_tuition_amount_is_zero(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01', 'Asia/Manila')->startOfDay());

        $program = $this->createProgram();
        $enrollment = app(EnrollmentService::class)->createEnrollment($this->baseEnrollmentPayload($program));

        Payment::query()->create([
            'enrollment_id' => $enrollment->id,
            'purpose' => Payment::PURPOSE_INITIAL,
            'payment_method' => 'gcash',
            'amount' => (5_000 + EnrollmentPricingService::CONVENIENCE_FEE_PESOS) * 100,
            'currency' => 'PHP',
            'tuition_amount' => 0,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        app(EnrollmentFinancialService::class)->recalculateEnrollmentFinancials($enrollment->fresh());
        $enrollment->refresh();

        $this->assertSame(5_000, $enrollment->amount_paid_tuition);
        $this->assertSame('partially_paid', $enrollment->status);
        $this->assertSame(3_000, $enrollment->balance_tuition_due);

        Carbon::setTestNow();
    }

    public function test_full_payment_enrollment_confirmed_with_zero_balance(): void
    {
        $program = $this->createProgram([
            'slug' => 'full-ledger',
            'price_early' => null,
            'early_deadline' => null,
            'early_bird_label' => null,
        ]);

        $enrollment = app(EnrollmentService::class)->createEnrollment($this->baseEnrollmentPayload($program, [
            'payment_type' => 'full',
        ]));

        Payment::query()->create([
            'enrollment_id' => $enrollment->id,
            'purpose' => Payment::PURPOSE_INITIAL,
            'payment_method' => 'gcash',
            'amount' => ($enrollment->base_amount + EnrollmentPricingService::CONVENIENCE_FEE_PESOS) * 100,
            'currency' => 'PHP',
            'tuition_amount' => $enrollment->base_amount,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        app(EnrollmentFinancialService::class)->recalculateEnrollmentFinancials($enrollment->fresh());
        $enrollment->refresh();

        $this->assertSame('confirmed', $enrollment->status);
        $this->assertSame(0, $enrollment->balance_tuition_due);
    }

    public function test_signed_balance_route_returns_403_without_signature(): void
    {
        $program = $this->createProgram(['slug' => 'bal-route']);
        $enrollment = app(EnrollmentService::class)->createEnrollment($this->baseEnrollmentPayload($program, [
            'program' => 'bal-route',
        ]));

        $this->get(route('enroll.balance', ['reference_number' => $enrollment->reference_number]))
            ->assertForbidden();
    }
}
