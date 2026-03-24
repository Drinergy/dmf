<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [
            [
                'name' => 'Weekday Morning',
                'slug' => 'weekday_am',
                'time_description' => 'Mon–Fri | 8:00 AM – 12:00 NN',
                'mode' => 'Face-to-Face & Online',
                'is_active' => true,
            ],
            [
                'name' => 'Weekday Afternoon',
                'slug' => 'weekday_pm',
                'time_description' => 'Mon–Fri | 1:00 PM – 5:00 PM',
                'mode' => 'Face-to-Face & Online',
                'is_active' => true,
            ],
            [
                'name' => 'Weekend Morning',
                'slug' => 'weekend_am',
                'time_description' => 'Sat–Sun | 8:00 AM – 12:00 NN',
                'mode' => 'Face-to-Face & Online',
                'is_active' => true,
            ],
            [
                'name' => 'Online Asynchronous',
                'slug' => 'online_async',
                'time_description' => 'Anytime — Self-paced Online',
                'mode' => 'Online Only',
                'is_active' => true,
            ],
        ];

        foreach ($schedules as $schedule) {
            \App\Models\Schedule::firstOrCreate(['slug' => $schedule['slug']], $schedule);
        }
    }
}
