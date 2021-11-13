<?php

namespace App\Repository\Achievement;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\CommentAchievement;
use App\Models\Lesson;
use App\Models\LessonAchievement;
use App\Models\User;
use App\Utils\Achievement\AchievementUtils;
use App\Utils\Achievement\CommentAchievementUtils;
use App\Utils\Achievement\LessonAchievementUtils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class AchievementRepository implements AchievementRepositoryInterface
{
    public function commentWritten(Comment $comment)
    {
        $commentCount = $comment->user->comments()->count();
        Log::info(get_class(),['comment_count' => $commentCount]);
        switch ($commentCount) {
            case CommentAchievementUtils::THIRD_MILESTONE:
            case CommentAchievementUtils::FIFTH_MILESTONE:
            case CommentAchievementUtils::TENTH_MILESTONE:
            case CommentAchievementUtils::TWENTIETH_MILESTONE:
            case CommentAchievementUtils::FIRST_MILESTONE:
                AchievementUnlocked::dispatch(CommentAchievementUtils::getMilestoneName($commentCount),$comment->user);
                break;
        }
    }

    public function lessonWatched(Lesson $lesson, User $user)
    {
        $lessonsCount = $user->lessons()->where('watched',true)->count();
        Log::info(get_class(),['lesson_count' => $lessonsCount]);
        switch ($lessonsCount) {
            case LessonAchievementUtils::FIRST_MILESTONE:
            case LessonAchievementUtils::FIFTH_MILESTONE:
            case LessonAchievementUtils::TENTH_MILESTONE:
            case LessonAchievementUtils::TWENTY_FIFTH_MILESTONE:
            case LessonAchievementUtils::FIFTIETH_MILESTONE:
                AchievementUnlocked::dispatch(LessonAchievementUtils::getMilestoneName($lessonsCount),$user);
                break;
        }
    }

    public function achievementUnlocked(string $achievement, User $user)
    {
        ['type' => $type, 'milestone' => $milestone] = AchievementUtils::getMilestoneFromName($achievement);

        /** @var Achievement $achievementModel */
        $achievementModel = $this->resolveAchievementType($type)->query()->where('milestone',$milestone)->first();

        if($achievementModel == null) return;


        $user->achievements()->attach($achievementModel->id);
        //TODO test if achievements are created
        //check the achievements count

        // TODO: Implement achievementUnlocked() method.
    }


    private function resolveAchievementType(string $type): Model
    {
        switch ($type) {
            case AchievementUtils::ACHIEVEMENT_TYPE_COMMENT:
                return App::make(CommentAchievement::class);
            case AchievementUtils::ACHIEVEMENT_TYPE_LESSON:
                return App::make(LessonAchievement::class);
        }
        throw new InvalidArgumentException("$type is not allowed");
    }


}
