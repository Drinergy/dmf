<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Program;
use Tests\TestCase;

class EnrollmentMessengerUrlValidationTest extends TestCase
{
    private function uniqueProgramSlug(string $prefix): string
    {
        return 'tst-'.$prefix.'-'.bin2hex(random_bytes(4));
    }

    private function createProgram(): Program
    {
        return Program::create([
            'name' => 'URL Validation Program',
            'slug' => $this->uniqueProgramSlug('url'),
            'category' => 'Individual Programs (Theoretical)',
            'tag' => null,
            'price_full' => 10000,
            'price_early' => null,
            'early_deadline' => null,
            'early_bird_label' => null,
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }

    private function basePayload(string $programSlug, array $overrides = []): array
    {
        return array_merge([
            'program' => $programSlug,
            'schedule_id' => null,
            'payment_type' => 'full',

            'first_name' => 'Juan',
            'middle_name' => null,
            'surname' => 'Dela Cruz',
            'birthday' => '2000-01-01',
            'sex' => 'Male',

            'phone' => '09171234567',
            'email' => 'juan@example.com',
            'facebook_messenger_name' => 'Juan Dela Cruz',
            'facebook_messenger_url' => null,

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

            'data_accuracy_ack' => '1',
        ], $overrides);
    }

    public function test_enrollment_store_rejects_invalid_messenger_url(): void
    {
        $program = $this->createProgram();

        $response = $this->post(route('enroll.store'), $this->basePayload($program->slug, [
            'facebook_messenger_url' => 'not-a-url',
        ]));

        $response->assertSessionHasErrors(['facebook_messenger_url']);
    }

    public function test_enrollment_store_accepts_valid_http_url(): void
    {
        $program = $this->createProgram();

        $response = $this->post(route('enroll.store'), $this->basePayload($program->slug, [
            'facebook_messenger_url' => 'https://www.facebook.com/example.profile',
        ]));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('enroll.payment'));
    }
}
