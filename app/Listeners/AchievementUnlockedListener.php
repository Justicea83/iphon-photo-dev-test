<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Repository\Achievement\AchievementRepositoryInterface;

class AchievementUnlockedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle(AchievementUnlocked $event,AchievementRepositoryInterface $repository)
    {
        $repository->achievementUnlocked($event->achievementName,$event->user);
    }
}
