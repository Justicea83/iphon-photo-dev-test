<?php

namespace Database\Seeders;

use App\Models\CommentAchievement;
use App\Models\LessonAchievement;
use App\Utils\Achievement\CommentAchievementUtils;
use App\Utils\Achievement\LessonAchievementUtils;
use Illuminate\Database\Seeder;

class AchievementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedCommentAchievements();
        $this->seedLessonAchievements();
    }

    private function seedCommentAchievements()
    {
        if (CommentAchievement::query()->count() > 0) return;
        foreach (CommentAchievementUtils::ALL_MILESTONES as $milestone) {
            CommentAchievement::query()->firstOrCreate([
                'milestone' => $milestone,
            ]);
        }
    }

    private function seedLessonAchievements()
    {
        if (LessonAchievement::query()->count() > 0) return;
        foreach (LessonAchievementUtils::ALL_MILESTONES as $milestone) {
            LessonAchievement::query()->firstOrCreate([
                'milestone' => $milestone
            ]);
        }
    }
}
