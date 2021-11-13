<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parental\HasParent;

class LessonAchievement extends Achievement
{
    use HasFactory,HasParent;

    protected $fillable = [
        'milestone'
    ];
}
