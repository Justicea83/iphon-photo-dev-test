<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use App\Repository\Achievement\AchievementRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CommentWrittenListener
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


    public function handle(CommentWritten $event,AchievementRepositoryInterface $repository)
    {
        $repository->commentWritten($event->comment);
    }
}
