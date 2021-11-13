<?php

namespace App\Utils\Achievement;

class LessonAchievementUtils
{
    public const MILESTONE = 'lesson';

    public const FIRST_MILESTONE = 1;
    public const FIFTH_MILESTONE = 5;
    public const TENTH_MILESTONE = 10;
    public const TWENTY_FIFTH_MILESTONE = 25;
    public const FIFTIETH_MILESTONE = 50;

    const ALL_MILESTONES = [
        self::FIRST_MILESTONE,
        self::FIFTH_MILESTONE,
        self::TENTH_MILESTONE,
        self::TWENTY_FIFTH_MILESTONE,
        self::FIFTIETH_MILESTONE,
    ];

    public static function getMilestoneName(int $milestone): string
    {
        $name = self::MILESTONE;
        return "${name}_$milestone";
    }
}
