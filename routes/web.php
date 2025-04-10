<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::resource('projects', ProjectController::class);
Route::resource('tasks', TaskController::class);
Route::resource('tags', TagController::class);
Route::resource('users', UserController::class);
Route::resource('posts', PostController::class);
Route::resource('categorys', CategoryController::class);
Route::resource('products', ProductController::class);
Route::resource('orders', OrderController::class);
Route::resource('products', ProductController::class);
Route::resource('articles', ArticleController::class);
Route::resource('products', ProductController::class);
Route::resource('users', UserController::class);
Route::resource('orders', OrderController::class);
Route::resource('posts', PostController::class);
Route::resource('comments', CommentController::class);
Route::resource('tags', TagController::class);
Route::resource('posts', PostController::class);