<?php

namespace App\Utils\Achievement;

use App\Models\CommentAchievement;

class CommentAchievementUtils
{
    public const MILESTONE = 'comment';

    public const FIRST_MILESTONE = 1;
    public const THIRD_MILESTONE = 3;
    public const FIFTH_MILESTONE = 5;
    public const TENTH_MILESTONE = 10;
    public const TWENTIETH_MILESTONE = 20;

    const ALL_MILESTONES = [
        self::FIRST_MILESTONE,
        self::THIRD_MILESTONE,
        self::FIFTH_MILESTONE,
        self::TENTH_MILESTONE,
        self::TWENTIETH_MILESTONE,
    ];

    public static function getMilestoneName(int $milestone): string
    {
        $name = self::MILESTONE;
        return "${name}_$milestone";
    }


}
