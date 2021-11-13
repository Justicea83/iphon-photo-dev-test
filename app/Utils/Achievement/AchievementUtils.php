<?php

namespace App\Utils\Achievement;

use App\Models\CommentAchievement;

class AchievementUtils
{
    const ACHIEVEMENT_TYPE_COMMENT = 'comment';
    const ACHIEVEMENT_TYPE_LESSON = 'lesson';

    public static function getMilestoneFromName(string $name): array
    {
        $payload = explode('_',$name);
        return [
            'type' => $payload[0],
            'milestone' => $payload[1]
        ];
    }
}
