<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Group middleware with alias
Route::middleware(['check.age'])->group(function () {
    Route::get('/restricted', function () {
        return "This content is for adults only!";
    });

    Route::get('/adult-store', function () {
        return "Welcome to the adult store!";
    });
});

Route::get('/home', function () {
    return view('home');
})->name('home');

Route::middleware('admin')->group(function () {
    Route::get('/tags', function (Request $request) {
        return "Tag - $request->id";
    });
    Route::get('/posts', function (Request $request) {
        return "Post - $request->id";
    });
});

Route::resource('users', UserController::class)->only(['index', 'show']);
