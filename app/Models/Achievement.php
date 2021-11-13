<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parental\HasChildren;

/**
 * @property mixed id
 * @property mixed milestone
 * @property mixed type
 */
class Achievement extends Model
{
    use HasFactory, HasChildren;

    protected $appends = ['description'];

    public function getDescriptionAttribute(): string
    {
        switch ($this->type) {
            case CommentAchievement::class:
                if ($this->milestone == 1) {
                    return 'First Comment Written';
                }
                return $this->milestone . ' Comments Written';
            case LessonAchievement::class:
                if ($this->milestone == 1) {
                    return 'First Lesson Watched';
                }
                return $this->milestone . ' Lessons Watched';
        }

    }
}
