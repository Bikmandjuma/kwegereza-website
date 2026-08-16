<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'name' => '7-Day Learning Streak',
                'slug' => '7-day-streak',
                'description' => "Wize iminsi 7 ikurikirana!",
                'icon' => '🔥',
                'criteria_type' => 'streak_days',
                'criteria_value' => 7,
            ],
            [
                'name' => '30-Day Learning Streak',
                'slug' => '30-day-streak',
                'description' => "Wize iminsi 30 ikurikirana!",
                'icon' => '🔥',
                'criteria_type' => 'streak_days',
                'criteria_value' => 30,
            ],
            [
                'name' => '10 Lessons Completed',
                'slug' => '10-lessons-completed',
                'description' => "Warangije amasomo 10 ya Darsat.",
                'icon' => '📿',
                'criteria_type' => 'darsat_completed',
                'criteria_value' => 10,
            ],
            [
                'name' => '25 Lessons Completed',
                'slug' => '25-lessons-completed',
                'description' => "Warangije amasomo 25 ya Darsat.",
                'icon' => '📗',
                'criteria_type' => 'darsat_completed',
                'criteria_value' => 25,
            ],
            [
                'name' => 'First Learning Path Completed',
                'slug' => 'first-course-completed',
                'description' => "Warangije isomo ryawe rya mbere rigenda.",
                'icon' => '🎓',
                'criteria_type' => 'courses_completed',
                'criteria_value' => 1,
            ],
            [
                'name' => 'Quiz Master',
                'slug' => 'quiz-master',
                'description' => "Watsinze ibizamini 5.",
                'icon' => '🏆',
                'criteria_type' => 'quizzes_passed',
                'criteria_value' => 5,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['slug' => $badge['slug']], $badge);
        }
    }
}
