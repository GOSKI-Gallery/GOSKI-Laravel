<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Post\PostLocationController;
use App\Http\Controllers\PushTokenController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('landing');
    })->name('landingPage');

    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->middleware('throttle:5,1')->name('authenticate');

    Route::get('/register', function () {
        return view('register');
    })->name('register');
    Route::post('/register', [UserController::class, 'register'])->middleware('throttle:2,1')->name('register');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/users', [AdminController::class, 'index'])->name('admin.users.index');

        Route::get('/users/{id}', [AdminController::class, 'detail'])->name('admin.users.detail');

        Route::get('/users/{id}/remove', [AdminController::class, 'remove'])->name('admin.users.remove');
        Route::post('/users/{id}', [AdminController::class, 'destroy'])->name('admin.users.destroy');

        Route::get('/posts', [AdminController::class, 'postsIndex'])->name('admin.posts.index');
        Route::get('/posts/{id}', [AdminController::class, 'postsDetail'])->name('admin.posts.detail');

        Route::post('/posts/{id}/approve', [AdminController::class, 'approvePost'])->name('admin.posts.approve');
        Route::delete('/posts/{id}', [AdminController::class, 'destroyPost'])->name('admin.posts.destroy');
    });

    Route::get('/feed', [PostController::class, 'index'])->name('feed');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

    Route::get('/posts/{postId}/location', [PostLocationController::class, 'show'])->name('post.location.show');

    Route::post('/follow/{userId}', [FollowController::class, 'follow'])->name('user.follow');
    Route::post('/unfollow/{userId}', [FollowController::class, 'unfollow'])->name('user.unfollow');
    Route::post('/posts/{postId}/like', [LikeController::class, 'toggleLike'])->name('post.like.toggle');

    Route::get('/posts/{postId}/comments', [CommentController::class, 'index'])->name('post.comments.index');
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store'])->name('post.comments.store');
    Route::delete('/posts/comments/{commentId}', [CommentController::class, 'destroy'])->name('post.comments.destroy');

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/profile/{userId}', [UserController::class, 'show'])->name('profile.show');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read', [NotificationsController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}', [NotificationsController::class, 'delete'])->name('notifications.delete');

    Route::get('/push-tokens', [PushTokenController::class, 'index'])->name('push.tokens.index');
    Route::delete('/push-tokens/{token}', [PushTokenController::class, 'destroy'])->name('push.tokens.destroy');
});
