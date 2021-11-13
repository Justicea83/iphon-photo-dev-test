<?php

namespace App\Repository\Achievement;

use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;

interface AchievementRepositoryInterface
{
    public function commentWritten(Comment $comment);
    public function lessonWatched(Lesson $lesson, User $user);
    public function achievementUnlocked(string $achievementName,User $user);
    public function badgeUnlocked(string $badgeName,User $user);
    public function getUserAchievements(User $user) : array;
}
