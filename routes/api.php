<?php

use App\Http\Controllers\Auths;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/signup', [Auths::class, 'signup'])->middleware('throttle:6,3');
Route::get('/login', [Auths::class, 'login'])->middleware('throttle:6,1');
Route::get('/logout', [Auths::class, 'logout'])->middleware('auth:sanctum');
Route::get('/log_info', [Auths::class, 'log_info'])->middleware('auth:sanctum');