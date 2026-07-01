<?php

use App\Http\Controllers\Auths;
use App\Http\Controllers\tver;
use App\Http\Controllers\user_manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/signup', [Auths::class, 'signup'])->middleware('throttle:6,3');
Route::get('/login', [Auths::class, 'login'])->middleware('throttle:6,1');
Route::get('/logout', [Auths::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user_info', [user_manager::class, 'user_info'])->middleware('auth:sanctum');
Route::get('/tv', [tver::class, 'get_all'])->middleware('throttle:36,1');
Route::post('/tv', [tver::class, 'up_tv']);
Route::post('/tv_rate/{$id}', [tver::class, 'tv_rate'])->middleware(['auth:sanctum','throttle:6,1']);