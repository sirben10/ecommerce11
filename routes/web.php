<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');

// Login Groups
// User Login
Route::middleware('auth')->group(function(){
    Route::get('/acount-dashboard', [UserController::class, 'index'])->name('user.index');
});

// Admin Login and rights
Route::middleware(['auth', AuthAdmin::class])->group(function(){
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // Brand Route
    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');

    // Add Brand Route
    Route::get('/admin/brand/add', [AdminController::class, 'add_brand'])->name('admin.add-brand');

    Route::post('/admin/brand/store', [AdminController::class, 'brand_store'])->name('admin.brandstore');
});
// End Login Groups

