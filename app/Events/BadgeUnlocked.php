<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BadgeUnlocked
{
    use Dispatchable, SerializesModels;

    public User $user;
    public string $badgeName;

    public function __construct(string $badgeName,User $user)
    {
        $this->badgeName = $badgeName;
        $this->user = $user;
    }

}
