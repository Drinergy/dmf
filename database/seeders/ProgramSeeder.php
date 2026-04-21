<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            // Individual Programs (Theoretical)
            [
                'slug' => 'hybrid-intensive',
                'name' => 'Hybrid Face-to-Face Intensive Lecture Review',
                'category' => 'Individual Programs (Theoretical)',
                'tag' => 'July-Nov (Sat-Sun)',
                'price_full' => 18000,
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
                'price_full' => 15500,
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
                'price_full' => 7000,
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
                'price_full' => 18000,
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
