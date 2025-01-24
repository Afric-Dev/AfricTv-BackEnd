<?php

use Illuminate\Support\Facades\Route;

//ADMIN
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\AdsController;


Route::get('/', function () {
    return view('welcome');
});


//ADMIN
Route::get('admin/login', [AdminAuthController::class, 'index'])->name('admin-login');
Route::post('post/login', [AdminAuthController::class, 'postLogin'])->name('admin-login.post'); 

Route::get('admin/logout', [AdminAuthController::class, 'logout'])->name('admin-logout');

Route::middleware('auth:admin')->prefix('admin')->group(function () {
 Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('admin-dashboard');
 Route::get('/users', [UsersController::class, 'index'])->name('admin-users');
 Route::get('/posts', [PostController::class, 'index'])->name('admin-posts');
 Route::get('/videos', [PostController::class, 'videos'])->name('admin-videos');
 Route::get('/admins', [AdminController::class, 'index'])->name('admin-admin');
 Route::get('/ads', [AdsController::class, 'index'])->name('admin-ads');
 Route::get('/admin/{id}', [AdminController::class, 'show'])->name('admin.show');
 Route::get('/add/admin', [AdminController::class, 'add'])->name('admin.add');
 Route::post('/create-admins', [AdminAuthController::class, 'editRegistration'])->name('admin-add-edit');
 Route::post('/create-new-admins', [AdminAuthController::class, 'postRegistration'])->name('admin-add');
 Route::get('/user/{id}', [UsersController::class, 'show'])->name('user.show');
 Route::get('/user/edit/{id}', [UsersController::class, 'show'])->name('user.edit');
 Route::post('/users/{id}/update-unique-id', [UsersController::class, 'updateUniqueId']);
 Route::get('/users/{id}/update-status-id', [UsersController::class, 'updateStatus'])->name('updateStatus');
 Route::post('/users/{id}/update-verification', [UsersController::class, 'updateVerification'])->name('updateVerification');
 Route::get('/post/{id}/update-status-id', [PostController::class, 'updateStatus'])->name('post.updateStatus');
 Route::get('/videos/{id}/update-status-id', [PostController::class, 'updateVideoStatus'])->name('videos.updateStatus');
 Route::get('/ads/refresh', [AdsController::class, 'updateAdsInactive'])->name('ads.updateAdsInactive');

});