<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Comment;
use App\Models\User;
use App\Repository\Achievement\AchievementRepositoryInterface;
use App\Utils\Achievement\CommentAchievementUtils;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\CreatesApplication;
use Tests\TestCase;

class AchievementUnlockedTest extends TestCase
{
    use CreatesApplication,RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate', ['-vvv' => true]);
        Artisan::call('db:seed', ['-vvv' => true]);
    }

    public function test_user_get_achievement_after_first_comment(){

        /** @var Comment $comment */
        $comment = Comment::factory()->create();

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->commentWritten($comment);

        $repository->achievementUnlocked(CommentAchievementUtils::getMilestoneName($comment->user->comments()->count()),$comment->user);

        $this->assertEquals(1,$comment->user->achievements()->count());
    }

    public function test_user_get_achievement_after_fifth_comment(){

        $user = User::factory()->create();
        Comment::factory(4)->for($user)->create();

        /** @var Comment $comment */
        $comment = Comment::factory()->for($user)->create();

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->commentWritten($comment);

        $repository->achievementUnlocked(CommentAchievementUtils::getMilestoneName($comment->user->comments()->count()),$comment->user);

        $this->assertEquals(1,$comment->user->achievements()->count());
    }

    public function test_user_already_has_one_achievement(){
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Comment $comment */
        $comment = Comment::factory()->for($user)->create();

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->commentWritten($comment);

        //this ensures the first achievement
        $repository->achievementUnlocked(CommentAchievementUtils::getMilestoneName($user->comments()->count()),$user);

        //the user then creates a second comment
        /** @var Comment $secondComment */
        $secondComment = Comment::factory()->for($comment->user)->create();
        $repository->commentWritten($secondComment);
        $repository->achievementUnlocked(CommentAchievementUtils::getMilestoneName($user->comments()->count()),$user);


        /** @var Comment $thirdComment */
        $thirdComment = Comment::factory()->for($user)->create();
        $repository->commentWritten($thirdComment);
        $repository->achievementUnlocked(CommentAchievementUtils::getMilestoneName($user->comments()->count()),$user);

        $this->assertEquals(2,$comment->user->achievements()->count());

    }

    /**
     * @throws Exception
     */
    public function test_expect_event_after_any_achievement()
    {
        $this->expectsEvents([AchievementUnlocked::class,BadgeUnlocked::class]);

        /** @var User $user */
        $user = User::factory()->create();

        /** @var Comment $comment */
        $comment = Comment::factory()->for($user)->create();

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->commentWritten($comment);

        $repository->achievementUnlocked(CommentAchievementUtils::getMilestoneName($user->comments()->count()),$user);

    }
}
