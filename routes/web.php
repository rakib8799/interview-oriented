<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

// Basic Routing, Route parameters (required and optional), Named routes, Route Groups
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::group(['prefix' => '/users', 'as' => 'users.'], function () {
    Route::get('/', function () {
        return view('users');
    })->name('index');
    Route::group(['prefix' => '/user', 'as' => 'user.'], function (){
        Route::get('/{id}/{slug}', function (int $id, string $slug) {
            $responseData = [
                'id' => $id,
                'slug' => $slug
            ];
            return view('user', $responseData);
        })->name('show');
        Route::get('/{userId}/{userSlug?}', function (int $userId, ?string $userSlug = null) {
            return "User ID - $userId and Slug - " . ($userSlug ?? 'N/A');
        })->name('optional.show');
    });
});

// Route methods - GET, POST, PATCH, PUT, DELETE

// Regular Expression for constraints
Route::get('/user/{name}/{id}', function(string $name, int $id) {
    return "Name: $name and ID: $id";
})->where(['name' => '[A-Za-z]+', 'id' => '[0-9]+']);

// Route::get('/user/{name}/{id}', function (string $name, int $id) {
//     return "Name: $name and ID: $id";
// })->whereAlpha('name')->whereNumber('id');

Route::get('/categories/{categoryId}', function (string $categoryId) {
    return "Category ID: $categoryId";
})->whereIn('categoryId', ['shoe', 'shirt', 'pant']);


// Route Model Binding
Route::get('/home/users/{user}', function (User $user) {
    // dd($user);
    return "User Email: $user->email";
});

// Redirect route
Route::redirect('/profile-picture', '/profile');

Route::get('/profile', function () {
    return "PROFILE";
});

// Fallback route
Route::fallback(function () {
    return "OOPs! we couldn't find the page!";
});
