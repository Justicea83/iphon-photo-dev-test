<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Models\Comment;
use App\Models\User;
use App\Repository\Achievement\AchievementRepositoryInterface;
use App\Utils\Achievement\CommentAchievementUtils;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\CreatesApplication;
use Tests\TestCase;

class AchievementUnlockedTest extends TestCase
{
    use CreatesApplication;

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
}
