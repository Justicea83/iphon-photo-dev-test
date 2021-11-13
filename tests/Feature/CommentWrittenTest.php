<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Models\Comment;
use App\Models\User;
use App\Repository\Achievement\AchievementRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Tests\CreatesApplication;
use Tests\TestCase;

class CommentWrittenTest extends TestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate', ['-vvv' => true]);
        Artisan::call('db:seed', ['-vvv' => true]);
    }

    public function test_can_dispatch_comment_written_with_error()
    {
        //test if any other object throws an exception
        $this->expectError();
        $comment = User::factory()->create();
        CommentWritten::dispatch($comment);
    }

    public function test_comment_written_event_listener_runs()
    {
        /** @var Comment $comment */
        $comment = Comment::factory()->create();

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $this->assertNull($repository->commentWritten($comment));
    }

    /**
     * @throws Exception
     */
    public function test_first_written_comment_fires_event()
    {
        $this->expectsEvents([AchievementUnlocked::class]);

        /** @var Comment $comment */
        $comment = Comment::factory()->create();

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->commentWritten($comment);
    }


    /**
     * @throws Exception
     */
    public function test_third_written_comment_fires_event()
    {
        $this->expectsEvents([AchievementUnlocked::class]);

        /** @var User $user */
        $user = User::factory()->create();
        //this user already has 2 comments
        Comment::factory(2)->for($user)->create();

        /** @var Comment $comment */
        $comment = Comment::factory()->for($user)->create();

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->commentWritten($comment);
    }

    /**
     * @throws Exception
     */
    public function test_fifth_written_comment_fires_event()
    {
        $this->expectsEvents([AchievementUnlocked::class]);

        /** @var User $user */
        $user = User::factory()->create();
        //this user already has 2 comments
        Comment::factory(4)->for($user)->create();

        /** @var Comment $comment */
        $comment = Comment::factory()->for($user)->create();

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->commentWritten($comment);
    }

    /**
     * @throws Exception
     */
    public function test_tenth_written_comment_fires_event()
    {
        $this->expectsEvents([AchievementUnlocked::class]);

        /** @var User $user */
        $user = User::factory()->create();
        //this user already has 2 comments
        Comment::factory(9)->for($user)->create();

        /** @var Comment $comment */
        $comment = Comment::factory()->for($user)->create();

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->commentWritten($comment);
    }

    /**
     * @throws Exception
     */
    public function test_twentieth_written_comment_fires_event()
    {
        $this->expectsEvents([AchievementUnlocked::class]);

        /** @var User $user */
        $user = User::factory()->create();
        //this user already has 2 comments
        Comment::factory(19)->for($user)->create();

        /** @var Comment $comment */
        $comment = Comment::factory()->for($user)->create();

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->commentWritten($comment);
    }


}
