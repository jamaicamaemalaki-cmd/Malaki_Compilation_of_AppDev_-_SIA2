<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SongController;
use App\Http\Controllers\ItemController;

Route::resource('items', ItemController::class);
Route::resource('songs', SongController::class);

Route::get('/', function () {
    return view('dashboard');
});
