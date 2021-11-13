<?php

namespace App\Providers;

use App\Repository\Achievement\AchievementRepository;
use App\Repository\Achievement\AchievementRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AchievementServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->scoped(AchievementRepositoryInterface::class,AchievementRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
