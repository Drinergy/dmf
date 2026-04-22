<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Program;
use App\Services\EnrollmentService;
use Tests\TestCase;

class EnrollmentPackageEnrollmentItemsTest extends TestCase
{
    private function uniqueProgramSlug(string $prefix): string
    {
        return 'tst-'.$prefix.'-'.bin2hex(random_bytes(4));
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

            'payment_type' => 'full',
        ], $overrides);
    }

    public function test_package_purchase_creates_enrollment_items_for_each_included_program(): void
    {
        $online = Program::create([
            'name' => 'Online Comprehensive Lecture Review',
            'slug' => $this->uniqueProgramSlug('online'),
            'category' => 'Individual Programs (Theoretical)',
            'tag' => null,
            'price_full' => 15500,
            'price_early' => null,
            'early_deadline' => null,
            'early_bird_label' => null,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $coaching = Program::create([
            'name' => 'Online Final Coaching',
            'slug' => $this->uniqueProgramSlug('coaching'),
            'category' => 'Individual Programs (Theoretical)',
            'tag' => null,
            'price_full' => 7000,
            'price_early' => null,
            'early_deadline' => null,
            'early_bird_label' => null,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $package = Package::create([
            'name' => 'Package Test',
            'slug' => $this->uniqueProgramSlug('package'),
            'category' => 'Review Packages',
            'tag' => null,
            'price_full' => 22500,
            'price_early' => null,
            'early_deadline' => null,
            'early_bird_label' => null,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $package->programs()->attach($online->id, ['sort_order' => 0]);
        $package->programs()->attach($coaching->id, ['sort_order' => 1]);

        $enrollment = app(EnrollmentService::class)->createEnrollment($this->basePayload([
            'program' => $package->slug,
            'schedule_id' => null,
        ]));
        $this->assertSame(Package::class, $enrollment->purchasable_type);
        $this->assertSame($package->id, $enrollment->purchasable_id);

        $items = $enrollment->items()->orderBy('id')->get();
        $this->assertCount(2, $items);
        $this->assertSame([$online->id, $coaching->id], $items->pluck('program_id')->all());
        $this->assertSame([$online->slug, $coaching->slug], $items->pluck('program_slug_snapshot')->all());
        $this->assertSame([null, null], $items->pluck('schedule_id')->all());
    }
}
