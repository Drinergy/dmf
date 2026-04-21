<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Review Packages', 'sort_order' => 10],
            ['name' => 'Individual Programs (Theoretical)', 'sort_order' => 20],
            ['name' => 'Individual Programs (Practical)', 'sort_order' => 30],
        ];

        foreach ($categories as $c) {
            Category::firstOrCreate(['name' => $c['name']], [
                'sort_order' => $c['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}
