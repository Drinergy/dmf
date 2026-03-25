<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            // Review Packages
            [
                'slug' => 'package-a',
                'name' => 'Package A',
                'category' => 'Review Packages',
                'tag' => 'Complete Package',
                'inclusions' => ['Online Lecture Review (₱15,500)', 'Online Final Coaching (₱7,000)', 'Face-to-Face Practical (₱18,000)'],
                'price_full' => 40500,
                'price_dp' => 18000,
                'early_bird_label' => '₱38,500 if settled on or before May 15, 2026',
                'price_early' => 38500,
                'early_deadline' => '2026-05-15',
                'sort_order' => 10,
            ],
            [
                'slug' => 'package-b',
                'name' => 'Package B',
                'category' => 'Review Packages',
                'tag' => 'Hybrid Package',
                'inclusions' => ['Hybrid Lecture Review (₱18,000)', 'Online Final Coaching (₱7,000)', 'Face-to-Face Practical (₱18,000)'],
                'price_full' => 43000,
                'price_dp' => 20000,
                'early_bird_label' => '₱41,000 if settled on or before May 30, 2026',
                'price_early' => 41000,
                'early_deadline' => '2026-05-30',
                'sort_order' => 20,
            ],
            [
                'slug' => 'package-c',
                'name' => 'Package C',
                'category' => 'Review Packages',
                'tag' => 'Lecture + Coaching',
                'inclusions' => ['Online Lecture Review (₱15,500)', 'Online Final Coaching (₱7,000)'],
                'price_full' => 22500,
                'price_dp' => 13000,
                'early_bird_label' => '₱21,500 if settled on or before May 15, 2026',
                'price_early' => 21500,
                'early_deadline' => '2026-05-15',
                'sort_order' => 30,
            ],
            [
                'slug' => 'package-d',
                'name' => 'Package D',
                'category' => 'Review Packages',
                'tag' => 'Lecture + Practical',
                'inclusions' => ['Hybrid Lecture Review (₱18,000)', 'Face-to-Face Practical (₱18,000)'],
                'price_full' => 36000,
                'price_dp' => 17000,
                'early_bird_label' => '₱35,000 if settled on or before May 30, 2026',
                'price_early' => 35000,
                'early_deadline' => '2026-05-30',
                'sort_order' => 40,
            ],
            [
                'slug' => 'package-e',
                'name' => 'Package E',
                'category' => 'Review Packages',
                'tag' => 'Lecture + Practical',
                'inclusions' => ['Online Lecture Review (₱15,500)', 'Face-to-Face Practical (₱18,000)'],
                'price_full' => 33500,
                'price_dp' => 15000,
                'early_bird_label' => '₱32,500 if settled on or before May 15, 2026',
                'price_early' => 32500,
                'early_deadline' => '2026-05-15',
                'sort_order' => 50,
            ],
            [
                'slug' => 'package-f',
                'name' => 'Package F',
                'category' => 'Review Packages',
                'tag' => 'Lecture + Coaching',
                'inclusions' => ['Hybrid Lecture Review (₱18,000)', 'Online Final Coaching (₱7,000)'],
                'price_full' => 25000,
                'price_dp' => 12000,
                'early_bird_label' => '₱24,000 if settled on or before May 30, 2026',
                'price_early' => 24000,
                'early_deadline' => '2026-05-30',
                'sort_order' => 60,
            ],

            // Individual Programs (Theoretical)
            [
                'slug' => 'hybrid-intensive',
                'name' => 'Hybrid Face-to-Face Intensive Lecture Review',
                'category' => 'Individual Programs (Theoretical)',
                'tag' => 'July-Nov (Sat-Sun)',
                'inclusions' => [],
                'price_full' => 18000,
                'price_dp' => 10000,
                'early_bird_label' => null,
                'price_early' => null,
                'early_deadline' => null,
                'sort_order' => 70,
            ],
            [
                'slug' => 'online-comprehensive',
                'name' => 'Online Comprehensive Lecture Review',
                'category' => 'Individual Programs (Theoretical)',
                'tag' => 'June-Nov (Tue,Thu,Sat)',
                'inclusions' => [],
                'price_full' => 15500,
                'price_dp' => 8000,
                'early_bird_label' => null,
                'price_early' => null,
                'early_deadline' => null,
                'sort_order' => 80,
            ],
            [
                'slug' => 'online-final-coaching',
                'name' => 'Online Final Coaching',
                'category' => 'Individual Programs (Theoretical)',
                'tag' => 'Sept-Nov (Mon,Wed,Fri)',
                'inclusions' => [],
                'price_full' => 7000,
                'price_dp' => 4000,
                'early_bird_label' => null,
                'price_early' => null,
                'early_deadline' => null,
                'sort_order' => 90,
            ],

            // Individual Programs (Practical)
            [
                'slug' => 'practical-full-course',
                'name' => 'Full Course Face-to-Face Practical Review',
                'category' => 'Individual Programs (Practical)',
                'tag' => null,
                'inclusions' => [],
                'price_full' => 18000,
                'price_dp' => 5000,
                'early_bird_label' => null,
                'price_early' => null,
                'early_deadline' => null,
                'sort_order' => 100,
            ],
        ];

        foreach ($programs as $p) {
            $program = Program::firstOrCreate(['slug' => $p['slug']], $p);

            $category = Category::query()
                ->where('name', $p['category'])
                ->first();

            if ($category && $program->category_id !== $category->id) {
                $program->update(['category_id' => $category->id]);
            }
        }

        // Deactivate legacy practical per-batch programs (batches now live in `schedules`)
        Program::query()
            ->whereIn('slug', ['practical-aug', 'practical-sep', 'practical-oct', 'practical-octnov'])
            ->update(['is_active' => false]);
    }
}
