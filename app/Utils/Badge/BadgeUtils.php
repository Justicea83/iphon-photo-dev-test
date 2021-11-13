<?php

namespace App\Utils\Badge;

class BadgeUtils
{

    public const MILESTONE = 'badge';

    public const BADGE_BEGINNER = "beginner";
    public const BADGE_BEGINNER_ACHIEVEMENT_COUNT = 0;

    public const BADGE_INTERMEDIATE = "intermediate";
    public const BADGE_INTERMEDIATE_ACHIEVEMENT_COUNT = 4;

    public const BADGE_ADVANCED = "advanced";
    public const BADGE_ADVANCED_ACHIEVEMENT_COUNT = 8;

    public const BADGE_MASTER = "master";
    public const BADGE_MASTER_ACHIEVEMENT_COUNT = 10;

    public const ALL_BADGES = [
        [
            'name' => self::BADGE_BEGINNER,
            'achievement_count' => self::BADGE_BEGINNER_ACHIEVEMENT_COUNT
        ],
        [
            'name' => self::BADGE_INTERMEDIATE,
            'achievement_count' => self::BADGE_INTERMEDIATE_ACHIEVEMENT_COUNT
        ],
        [
            'name' => self::BADGE_ADVANCED,
            'achievement_count' => self::BADGE_ADVANCED_ACHIEVEMENT_COUNT
        ],
        [
            'name' => self::BADGE_MASTER,
            'achievement_count' => self::BADGE_MASTER_ACHIEVEMENT_COUNT
        ],
    ];

    public static function getMilestoneName(int $milestone): string
    {
        $name = self::MILESTONE;
        return "${name}_$milestone";
    }
}
