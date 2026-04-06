<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;


Route::get('/', function () {
    return view('landing');
})->name('landingPage');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', function () { return view('register'); });
Route::post('/register', [UserController::class, 'register'])->name('register');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/users', [AdminController::class, 'index'])->name('admin.users.index');

    Route::get('/users/{id}', [AdminController::class, 'detail'])->name('admin.users.detail');

    Route::get('/users/{id}/remove', [AdminController::class, 'remove'])->name('admin.users.remove');
    Route::post('/users/{id}', [AdminController::class, 'remove'])->name('admin.users.delete');
});

Route::get('/feed', [PostController::class, 'index'])->name('feed');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

Route::post('/follow/{userId}', [FollowController::class, 'follow'])->name('user.follow');
Route::post('/unfollow/{userId}', [FollowController::class, 'unfollow'])->name('user.unfollow');
Route::post('/posts/{postId}/like', [LikeController::class, 'toggleLike'])->name('post.like.toggle');
