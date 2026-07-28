<?php

use App\Http\Controllers\Auths;
use App\Http\Controllers\tver;
use App\Http\Controllers\user_manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route authentication
Route::post('/signup', [Auths::class, 'signup'])->middleware('throttle:10,1');
Route::get('/login', [Auths::class, 'login'])->middleware('throttle:6,1');
Route::get('/logout', [Auths::class, 'logout'])->middleware('auth:sanctum');

//Route user infos
Route::get('/user_info', [user_manager::class, 'user_info'])->middleware('auth:sanctum');

//Route tv
Route::get('/tv', [tver::class, 'get_all'])->middleware(['throttle:36,1']);
Route::post('/tv', [tver::class, 'up_tv']); //admin only
Route::patch('/tv/{id}', [tver::class, 'tv_edit'])->middleware('auth:sanctum'); //admin only 

//ini bukan vibe coding astaga

// Route user interaction
Route::post('/tv_rate/{id}', [tver::class, 'tv_rate'])->middleware(['auth:sanctum','throttle:6,1']);    
Route::post('/fav/{id}', [tver::class, 'favorite'])->middleware(['auth:sanctum', 'throttle:15,1']);
Route::get('/recommend', [tver::class, 'get_recommend'])->middleware(['auth:sanctum', 'throttle:36,1']);
Route::patch('/add_time/{id}', [tver::class, 'add_duration_watch'])->middleware('auth:sanctum');
Route::delete('/history', [tver::class, 'rm_history'])->middleware('auth:sanctum');
Route::get('/history', [tver::class, 'get_user_histories'])->middleware('auth:sanctum'); 
//yang diperbolehkan untuk mengecek history selain user itu sendiri 
//adalah orang selain watcher   
Route::delete('/tv_rate', [tver::class, 'rm_review'])->middleware('auth:sanctum'); //butuh request id komentar atau review