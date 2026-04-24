<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class EnrollmentLandingPageTest extends TestCase
{
    public function test_landing_includes_scroll_animation_markup_for_js(): void
    {
        $content = $this->get('/')->getContent() ?: '';

        $this->assertStringContainsString('land-scroll-anim', $content, 'Enables opt-in scroll and hero motion styles.');
        $this->assertStringContainsString('land-hero-1', $content);
        $this->assertStringContainsString('land-stagger', $content);
    }

    public function test_landing_includes_hybrid_intensive_program_overview_copy(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Hybrid Face-to-Face Intensive Lecture Review');
        $response->assertSee('lecturer availability');
        $response->assertSee('Handouts are given at the venue');
        $response->assertSee('short quiz at the end of each session');
        $response->assertSee('Online pre-board exam (3 days)');
        $response->assertSee('DMF shirt');
    }

    public function test_landing_includes_online_comprehensive_lecture_overview_copy(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Online Comprehensive Lecture Review');
        $response->assertSee('Detailed discussion of all board exam subjects');
        $response->assertSee('up to 4 hours per session');
        $response->assertSee('Review book shipped to your address');
        $response->assertSee('Free: ');
        $response->assertSee('Online pre-board exam (3 days)');
    }

    public function test_landing_includes_online_final_coaching_overview_copy(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Online Final Coaching');
        $response->assertSee('rationalization');
        $response->assertSee('BEQs');
        $response->assertSee('video recordings of sessions');
        $response->assertSee('Test-taking and exam-answering strategies');
        $response->assertSee('Free: ');
        $response->assertSee('Online pre-board examination');
    }

    public function test_landing_includes_face_to_face_practical_overview_copy(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Full Course Face-to-Face Practical Review');
        $response->assertSee('2 days of detailed online discussion');
        $response->assertSee('13 days of whole-day, hands-on training with topnotch lecturers');
        $response->assertSee('2 whole days of practical pre-board exam');
        $response->assertSee('Included: ');
        $response->assertSee('DMF shirt and CD kit');
    }

    public function test_landing_renders_success_stories_with_graduate_attribution_text(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('What Our Graduates Say');
        $response->assertSee('Dr. Maria Santos');
        $response->assertSee('Board Passer 2024');
    }
}
