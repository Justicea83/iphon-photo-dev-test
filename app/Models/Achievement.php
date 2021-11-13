<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parental\HasChildren;

/**
 * @property mixed id
 */
class Achievement extends Model
{
    use HasFactory,HasChildren;

    protected $fillable = [
        ''
    ];
}
