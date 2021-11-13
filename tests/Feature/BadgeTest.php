<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use App\Repository\Achievement\AchievementRepositoryInterface;
use App\Utils\Achievement\CommentAchievementUtils;
use App\Utils\Achievement\LessonAchievementUtils;
use App\Utils\Badge\BadgeUtils;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Ramsey\Collection\Collection;
use Tests\CreatesApplication;
use Tests\TestCase;

class BadgeTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate', ['-vvv' => true]);
        Artisan::call('db:seed', ['-vvv' => true]);
    }

    //TODO test user with beginner badge
    public function test_user_gets_beginner_badge_after_first_comment()
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Comment $comment */
        $comment = Comment::factory()->for($user)->create();

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->commentWritten($comment);

        $repository->achievementUnlocked(CommentAchievementUtils::getMilestoneName($user->comments()->count()),$user);

        $repository->badgeUnlocked(BadgeUtils::getMilestoneName($user->achievements()->count()),$user);

        $this->assertEquals(1, $user->badges()->count());
        $this->assertEquals(BadgeUtils::BADGE_BEGINNER,$user->badges()->first()->name);
    }

    public function test_user_gets_beginner_badge_after_lesson_watch()
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Lesson $lesson */
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, ['watched' => true]);

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->lessonWatched($lesson,$user);

        $repository->achievementUnlocked(LessonAchievementUtils::getMilestoneName($user->lessons()->count()),$user);

        $repository->badgeUnlocked(BadgeUtils::getMilestoneName($user->achievements()->count()),$user);

        $this->assertEquals(1, $user->badges()->count());
        $this->assertEquals(BadgeUtils::BADGE_BEGINNER,$user->badges()->first()->name);
    }

    public function test_user_badge_count_after_20_comments(){
        $user = $this->badgeCount(20);
        $this->assertEquals(2,$user->badges()->count());
    }

    public function test_user_badge_count_after_100_comments(){
        $user = $this->badgeCount(100);
        $this->assertEquals(2,$user->badges()->count());
    }

    public function badgeCount(int $numberOfComments): User
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        foreach (range(1, $numberOfComments) as $ignored) {

            /** @var Comment $comment */
            $comment = Comment::factory()->for($user)->create();

            $repository->commentWritten($comment);

            $repository->achievementUnlocked(CommentAchievementUtils::getMilestoneName($user->comments()->count()),$user);

            $repository->badgeUnlocked(BadgeUtils::getMilestoneName($user->achievements()->count()),$user);

        }

        return $user;
    }
}
