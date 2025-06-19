<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SingleActionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Basic Controller
Route::get('/home', [HomeController::class, 'home']);
Route::get('/about', [AboutController::class, 'about']);

// Single action controller
Route::get('/single-action-controller', SingleActionController::class);

// Resource controller
Route::resource('users', UserController::class);
// Route::resource('users', UserController::class)->except(['show', 'destroy']);
// Route::resource('users', UserController::class)->only(['show', 'destroy']);
