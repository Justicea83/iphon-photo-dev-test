<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\LessonWatched;
use App\Models\Lesson;
use App\Models\User;
use App\Repository\Achievement\AchievementRepositoryInterface;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Ramsey\Collection\Collection;
use Tests\CreatesApplication;
use Tests\TestCase;

class LessonWatchedTest extends TestCase
{
    use CreatesApplication,RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate', ['-vvv' => true]);
        Artisan::call('db:seed', ['-vvv' => true]);
    }

    public function test_dispatch_lesson_watch_with_no_arguments()
    {
        $this->expectError();
        LessonWatched::dispatch();
    }

    public function test_dispatch_lesson_watch_with_expected_arguments()
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();
        try {
            LessonWatched::dispatch($lesson,$user);
        }catch (Exception $e){
            $this->fail($e->getMessage());
        }
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function test_first_lesson_watched_event_fires()
    {
        $this->expectsEvents([AchievementUnlocked::class]);

        /** @var Lesson $lesson */
        $lesson = Lesson::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();

        $user->lessons()->attach($lesson->id, ['watched' => true]);

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->lessonWatched($lesson,$user);
    }

    /**
     * @throws Exception
     */
    public function test_five_lessons_watched_event_fires()
    {
        $this->count_lessons_watched(4);
    }

    /**
     * @throws Exception
     */
    public function test_ten_lessons_watched_event_fires()
    {
        $this->count_lessons_watched(9);

    }

    /**
     * @throws Exception
     */
    public function test_twenty_five_lessons_watched_event_fires()
    {
        $this->count_lessons_watched(24);

    }

    /**
     * @throws Exception
     */
    public function test_fifty_lessons_watched_event_fires()
    {
        $this->count_lessons_watched(49);
    }

    /**
     * @throws Exception
     */
    private function count_lessons_watched(int $count){
        $this->expectsEvents([AchievementUnlocked::class]);

        /** @var Collection $lessons */
        $lessons = Lesson::factory($count)->create();
        /** @var User $user */
        $user = User::factory()->create();

        $user->lessons()->attach($lessons->map(fn ($item) => $item->id ), ['watched' => true]);

        /** @var Lesson $lesson */
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, ['watched' => true]);

        /** @var AchievementRepositoryInterface $repository */
        $repository = $this->app->make(AchievementRepositoryInterface::class);

        $repository->lessonWatched($lesson,$user);
    }
}
