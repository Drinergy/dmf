<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Schedule;
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
                'program_slug' => 'hybrid-intensive',
                'label' => 'July to November 2026 | Saturday and Sunday (9am to 5pm) + few Wednesday sessions (Online)',
                'mode' => 'Hybrid Face-to-Face',
                'slots' => 150,
            ],
            [
                'program_slug' => 'online-comprehensive',
                'label' => 'June to November 2026 | Tuesday, Thursday, Saturday (5pm to 9pm)',
                'mode' => 'Pure Online',
                'slots' => null,
            ],
            [
                'program_slug' => 'online-final-coaching',
                'label' => 'September to November 2026 | Monday, Wednesday, Friday (11am to 4pm)',
                'mode' => 'Pure Online',
                'slots' => null,
            ],
            [
                'program_slug' => 'practical-full-course',
                'label' => 'August 2026',
                'mode' => 'Face-to-Face',
                'slots' => null,
            ],
            [
                'program_slug' => 'practical-full-course',
                'label' => 'September 2026',
                'mode' => 'Face-to-Face',
                'slots' => null,
            ],
            [
                'program_slug' => 'practical-full-course',
                'label' => 'October 2026',
                'mode' => 'Face-to-Face',
                'slots' => null,
            ],
            [
                'program_slug' => 'practical-full-course',
                'label' => 'October-November 2026',
                'mode' => 'Face-to-Face',
                'slots' => null,
            ],
        ];

        foreach ($schedules as $schedule) {
            $program = Program::query()
                ->where('slug', $schedule['program_slug'])
                ->first();

            if (!$program) {
                continue;
            }

            Schedule::firstOrCreate(
                [
                    'program_id' => $program->id,
                    'label'      => $schedule['label'],
                    'mode'       => $schedule['mode'],
                ],
                [
                    'start_date' => null,
                    'end_date'   => null,
                    'slots'      => $schedule['slots'],
                    'is_active'  => true,
                ]
            );
        }
    }
}
