<?php

namespace App\Repository\Achievement;

use App\Models\Comment;
use App\Models\User;

interface AchievementRepositoryInterface
{
    public function commentWritten(Comment $comment);
    public function achievementUnlocked(string $achievement,User $user);
}
