<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class reviews extends Model
{

    protected $fillable = [
        'user_id',
        'tv_id',
        'rating',
        'comment'
    ];

}
