<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class watch_history extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'tv_id',
        'duration_watched',
        'last_watched_at',
    ];  

    public $timestamps = false;
}
