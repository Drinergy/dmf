<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Package;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'slug' => 'package-a',
                'name' => 'Package A',
                'category' => 'Review Packages',
                'tag' => 'Complete Package',
                'price_full' => 40500,
                'early_bird_label' => '₱38,500 if settled on or before May 15, 2026',
                'price_early' => 38500,
                'early_deadline' => '2026-05-15',
                'sort_order' => 10,
                'includes' => [
                    'online-comprehensive',
                    'online-final-coaching',
                    'practical-full-course',
                ],
            ],
            [
                'slug' => 'package-b',
                'name' => 'Package B',
                'category' => 'Review Packages',
                'tag' => 'Hybrid Package',
                'price_full' => 43000,
                'early_bird_label' => '₱41,000 if settled on or before May 30, 2026',
                'price_early' => 41000,
                'early_deadline' => '2026-05-30',
                'sort_order' => 20,
                'includes' => [
                    'hybrid-intensive',
                    'online-final-coaching',
                    'practical-full-course',
                ],
            ],
            [
                'slug' => 'package-c',
                'name' => 'Package C',
                'category' => 'Review Packages',
                'tag' => 'Lecture + Coaching',
                'price_full' => 22500,
                'early_bird_label' => '₱21,500 if settled on or before May 15, 2026',
                'price_early' => 21500,
                'early_deadline' => '2026-05-15',
                'sort_order' => 30,
                'includes' => [
                    'online-comprehensive',
                    'online-final-coaching',
                ],
            ],
            [
                'slug' => 'package-d',
                'name' => 'Package D',
                'category' => 'Review Packages',
                'tag' => 'Lecture + Practical',
                'price_full' => 36000,
                'early_bird_label' => '₱35,000 if settled on or before May 30, 2026',
                'price_early' => 35000,
                'early_deadline' => '2026-05-30',
                'sort_order' => 40,
                'includes' => [
                    'hybrid-intensive',
                    'practical-full-course',
                ],
            ],
            [
                'slug' => 'package-e',
                'name' => 'Package E',
                'category' => 'Review Packages',
                'tag' => 'Lecture + Practical',
                'price_full' => 33500,
                'early_bird_label' => '₱32,500 if settled on or before May 15, 2026',
                'price_early' => 32500,
                'early_deadline' => '2026-05-15',
                'sort_order' => 50,
                'includes' => [
                    'online-comprehensive',
                    'practical-full-course',
                ],
            ],
            [
                'slug' => 'package-f',
                'name' => 'Package F',
                'category' => 'Review Packages',
                'tag' => 'Lecture + Coaching',
                'price_full' => 25000,
                'early_bird_label' => '₱24,000 if settled on or before May 30, 2026',
                'price_early' => 24000,
                'early_deadline' => '2026-05-30',
                'sort_order' => 60,
                'includes' => [
                    'hybrid-intensive',
                    'online-final-coaching',
                ],
            ],
        ];

        foreach ($packages as $p) {
            $package = Package::firstOrCreate(['slug' => $p['slug']], [
                'name' => $p['name'],
                'slug' => $p['slug'],
                'category' => $p['category'],
                'tag' => $p['tag'],
                'price_full' => $p['price_full'],
                'price_early' => $p['price_early'],
                'early_deadline' => $p['early_deadline'],
                'early_bird_label' => $p['early_bird_label'],
                'sort_order' => $p['sort_order'],
                'is_active' => true,
            ]);

            $category = Category::query()->where('name', $p['category'])->first();
            if ($category && $package->category_id !== $category->id) {
                $package->update(['category_id' => $category->id]);
            }

            $slugToId = Program::query()->pluck('id', 'slug')->all();

            $sort = 0;
            foreach ($p['includes'] as $programSlug) {
                $programId = $slugToId[$programSlug] ?? null;
                if (! $programId) {
                    continue;
                }

                DB::table('package_program')->insertOrIgnore([
                    'package_id' => $package->id,
                    'program_id' => $programId,
                    'sort_order' => $sort,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $sort++;
            }
        }
    }
}
