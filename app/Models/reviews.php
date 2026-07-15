<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class reviews extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'tv_id',
        'rating',
        'comment'
    ];

    protected $primaryKey = 'id';
    public $incrementing = true;
}
