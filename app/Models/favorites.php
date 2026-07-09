<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class favorites extends Model
{

    protected $fillable = [
        'user_id',
        'tv_id'
    ];

    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = 'user_id';
}
