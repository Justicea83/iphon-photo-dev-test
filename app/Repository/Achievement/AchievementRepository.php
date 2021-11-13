<?php

namespace App\Repository\Achievement;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Comment;
use App\Models\CommentAchievement;
use App\Models\Lesson;
use App\Models\LessonAchievement;
use App\Models\User;
use App\Utils\Achievement\AchievementUtils;
use App\Utils\Achievement\CommentAchievementUtils;
use App\Utils\Achievement\LessonAchievementUtils;
use App\Utils\Badge\BadgeUtils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class AchievementRepository implements AchievementRepositoryInterface
{
    private Badge $badgeModel;
    private Achievement $achievementModel;
    private LessonAchievement $lessonAchievementModel;
    private CommentAchievement $commentAchievementModel;

    public function __construct(Badge $badgeModel, Achievement $achievementModel, LessonAchievement $lessonAchievementModel, CommentAchievement $commentAchievementModel)
    {
        $this->badgeModel = $badgeModel;
        $this->achievementModel = $achievementModel;
        $this->commentAchievementModel = $commentAchievementModel;
        $this->lessonAchievementModel = $lessonAchievementModel;
    }

    public function commentWritten(Comment $comment)
    {
        $commentCount = $comment->user->comments()->count();
        switch ($commentCount) {
            case CommentAchievementUtils::THIRD_MILESTONE:
            case CommentAchievementUtils::FIFTH_MILESTONE:
            case CommentAchievementUtils::TENTH_MILESTONE:
            case CommentAchievementUtils::TWENTIETH_MILESTONE:
            case CommentAchievementUtils::FIRST_MILESTONE:
                AchievementUnlocked::dispatch(CommentAchievementUtils::getMilestoneName($commentCount), $comment->user);
                break;
        }
    }


    public function lessonWatched(Lesson $lesson, User $user)
    {
        //give user the first badge
        $lessonsCount = $user->lessons()->where('watched', true)->count();
        Log::info(get_class(), ['lesson_count' => $lessonsCount]);
        switch ($lessonsCount) {
            case LessonAchievementUtils::FIRST_MILESTONE:
            case LessonAchievementUtils::FIFTH_MILESTONE:
            case LessonAchievementUtils::TENTH_MILESTONE:
            case LessonAchievementUtils::TWENTY_FIFTH_MILESTONE:
            case LessonAchievementUtils::FIFTIETH_MILESTONE:
                AchievementUnlocked::dispatch(LessonAchievementUtils::getMilestoneName($lessonsCount), $user);
                break;
        }
    }

    public function achievementUnlocked(string $achievementName, User $user)
    {
        ['type' => $type, 'milestone' => $milestone] = AchievementUtils::getMilestoneFromName($achievementName);

        /** @var Achievement $achievementModel */
        $achievementModel = $this->resolveAchievementType($type)->query()->where('milestone', $milestone)->first();

        if ($achievementModel == null) return;

        $user->achievements()->attach($achievementModel->id);

        $achievementCount = $user->achievements()->count();

        switch ($achievementCount) {
            case BadgeUtils::BADGE_INTERMEDIATE_ACHIEVEMENT_COUNT:
            case BadgeUtils::BADGE_ADVANCED_ACHIEVEMENT_COUNT:
            case BadgeUtils::BADGE_MASTER_ACHIEVEMENT_COUNT:
            case  ($achievementCount < BadgeUtils::BADGE_INTERMEDIATE_ACHIEVEMENT_COUNT):
                BadgeUnlocked::dispatch(BadgeUtils::getMilestoneName($achievementCount), $user);
                break;
        }
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

    public function badgeUnlocked(string $badgeName, User $user)
    {
        ['milestone' => $milestone] = AchievementUtils::getMilestoneFromName($badgeName);

        Log::info(get_class(), ['milestone' => $this->transformBadgeMilestone($milestone)]);

        /** @var Badge $badge */
        $badge = $this->badgeModel->query()->where('achievement_count', $this->transformBadgeMilestone($milestone))->first();

        if ($badge == null) return;

        try {
            $user->badges()->attach($badge->id);
        } catch (QueryException $e) {

        }
    }

    //return zero if the milestone is less than 4
    private function transformBadgeMilestone(int $milestone): int
    {
        if ($milestone < BadgeUtils::BADGE_INTERMEDIATE_ACHIEVEMENT_COUNT)
            return 0;
        return $milestone;
    }

    public function getUserAchievements(User $user): array
    {
        $userAchievements = $user->achievements;

        $currentBadge = $this->getCurrentBadge($user);
        /** @var Badge $nextBadge */

        $nextBadge = $this->getNextBadge($user, $currentBadge);

        return [
            'unlocked_achievements' => $this->achievementModel->query()
                ->whereNotIn('achievements.id', $userAchievements->pluck('id'))
                ->get()
                ->pluck('description')
                ->toArray(),
            'next_available_achievements' => $this->getNextAvailableAchievements($user),
            'current_badge' => $currentBadge,
            'next_badge' => $nextBadge != null ? $nextBadge->name : '',
            'remaining_to_unlock_next_badge' => $this->remainingToUnlockNextBadge($user, $nextBadge)
        ];
    }

    private function getNextAvailableAchievements(User $user): array
    {
        $payload = [];
        $userCommentAchievements = $user->commentAchievements;
        $userLessonAchievements = $user->lessonAchievements;

        /** @var LessonAchievement $nextLessonAchievement */
        $nextLessonAchievement = $this->lessonAchievementModel->query()
            ->whereNotIn('id', $userLessonAchievements->pluck('id'))
            ->orderBy('milestone')
            ->first();
        if ($nextLessonAchievement != null) array_push($payload, $nextLessonAchievement->description);

        /** @var CommentAchievement $nextCommentAchievement */
        $nextCommentAchievement = $this->commentAchievementModel->query()
            ->whereNotIn('id', $userCommentAchievements->pluck('id'))
            ->orderBy('milestone')
            ->first();

        if ($nextCommentAchievement != null) array_push($payload, $nextCommentAchievement->description);
        return $payload;
    }

    private function getCurrentBadge(User $user): string
    {
        if ($user->badges()->count() == 0) {
            return BadgeUtils::BADGE_BEGINNER;
        }
        return $user->badges()->orderBy('achievement_count', 'desc')->first()->name;
    }

    private function getBadgeByName(string $name): Model
    {
        return $this->badgeModel->query()->where('name', $name)->first();
    }

    private function getNextBadge(User $user, string $currentBadgeName = null): Model
    {
        /** @var Badge $currentBadge */
        $currentBadge = $this->getBadgeByName($currentBadgeName ?? $this->getCurrentBadge($user));
        if ($currentBadge == null) throw new InvalidArgumentException("badge not found");
        return $this->badgeModel->query()->where('achievement_count', '>', $currentBadge->achievement_count)
            ->orderBy('achievement_count')
            ->first();
    }

    private function remainingToUnlockNextBadge(User $user, ?Badge $nextBadge): int
    {
        if ($nextBadge == null)
            return 0;
        return $nextBadge->achievement_count - $user->achievements()->count();
    }
}
