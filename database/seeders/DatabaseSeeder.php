<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (User::query()->count() <= 0)
            User::factory(20)->create();
        if (Lesson::query()->count() <= 0)
            Lesson::factory()
                ->count(20)
                ->create();

    }
}
