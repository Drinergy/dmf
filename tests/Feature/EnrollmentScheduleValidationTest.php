<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\Schedule;
use Tests\TestCase;

class EnrollmentScheduleValidationTest extends TestCase
{
    /**
     * Avoid colliding with real programs from seeders (programs.slug is unique).
     */
    private function uniqueProgramSlug(string $prefix): string
    {
        return 'tst-'.$prefix.'-'.bin2hex(random_bytes(4));
    }

    private function createProgram(array $overrides = []): Program
    {
        return Program::create(array_merge([
            'name' => 'Test Program',
            'slug' => $this->uniqueProgramSlug('generic'),
            'category' => 'Individual Programs (Theoretical)',
            'tag' => null,
            'price_full' => 10000,
            'price_early' => null,
            'early_deadline' => null,
            'early_bird_label' => null,
            'is_active' => true,
            'sort_order' => 0,
        ], $overrides));
    }

    private function createSchedule(Program $program, array $overrides = []): Schedule
    {
        return Schedule::create(array_merge([
            'program_id' => $program->id,
            'label' => 'August 2026',
            'mode' => 'Face-to-Face',
            'start_date' => null,
            'end_date' => null,
            'slots' => null,
            'is_active' => true,
        ], $overrides));
    }

    private function basePayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Juan',
            'middle_name' => null,
            'surname' => 'Dela Cruz',
            'birthday' => '2000-01-01',
            'sex' => 'Male',

            'phone' => '09171234567',
            'email' => 'juan@example.com',
            'facebook' => 'Juan Dela Cruz',

            'addr_street' => '123 Sample Street',
            'addr_city' => 'Manila',
            'addr_province' => 'Metro Manila',
            'addr_zip' => '1000',

            'deliv_street' => null,
            'deliv_city' => null,
            'deliv_province' => null,
            'deliv_zip' => null,

            'school' => 'Sample University',
            'year_level' => 'Graduate',
            'year_graduated' => '2024',
            'taker_status' => 'First taker',

            'payment_type' => 'full',
        ], $overrides);
    }

    public function test_schedule_is_not_required_when_program_has_one_active_schedule(): void
    {
        $program = $this->createProgram(['slug' => $this->uniqueProgramSlug('one-sched')]);
        $this->createSchedule($program);

        $response = $this->post(route('enroll.store'), $this->basePayload([
            'program' => $program->slug,
            // schedule_id omitted
        ]));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('enroll.payment'));
    }

    public function test_schedule_is_required_when_program_has_multiple_active_schedules(): void
    {
        $program = $this->createProgram(['slug' => $this->uniqueProgramSlug('multi-sched')]);
        $this->createSchedule($program, ['label' => 'August 2026']);
        $this->createSchedule($program, ['label' => 'September 2026']);

        $response = $this->post(route('enroll.store'), $this->basePayload([
            'program' => $program->slug,
            // schedule_id omitted
        ]));

        $response->assertSessionHasErrors(['schedule_id']);
    }

    public function test_schedule_must_belong_to_selected_program(): void
    {
        $programA = $this->createProgram(['slug' => $this->uniqueProgramSlug('prog-a'), 'name' => 'Program A']);
        $programB = $this->createProgram(['slug' => $this->uniqueProgramSlug('prog-b'), 'name' => 'Program B']);

        $this->createSchedule($programA); // ensures schedule is required for A
        $scheduleB = $this->createSchedule($programB, ['label' => 'September 2026']);

        $response = $this->post(route('enroll.store'), $this->basePayload([
            'program' => $programA->slug,
            'schedule_id' => $scheduleB->id, // wrong program
        ]));

        $response->assertSessionHasErrors(['schedule_id']);
    }

    public function test_schedule_can_be_null_when_program_has_no_active_schedules(): void
    {
        $program = $this->createProgram(['slug' => $this->uniqueProgramSlug('no-sched'), 'category' => 'Review Packages']);

        $response = $this->post(route('enroll.store'), $this->basePayload([
            'program' => $program->slug,
            'schedule_id' => null,
        ]));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('enroll.payment'));
    }
}
