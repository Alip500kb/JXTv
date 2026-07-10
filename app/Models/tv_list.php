<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class tv_list extends Model
{
    use Notifiable, HasFactory, HasUuids;


    protected $fillable = [
        'acara',
        'thumb',
        'category',
        'rate',
        'url',
        'status',
        'country',
        'updated_at'
    ];
}
