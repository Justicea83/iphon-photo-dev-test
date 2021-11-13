<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Utils\BadgeUtils;
use Illuminate\Database\Seeder;

class BadgesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Badge::query()->count() > 0) return;
        foreach (BadgeUtils::ALL_BADGES as $badge) {
            Badge::query()->firstOrCreate($badge);
        }
    }
}
