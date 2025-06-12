<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Session;

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
    // Edit Brand
    Route::get('/admin/brand/edit/{id}', [AdminController::class, 'edit_brand'])->name('admin.edit-brand');
    // Update Brand
    Route::put('/admin/brand/update', [AdminController::class, 'brand_update'])->name('admin.update-brand');
//    Delete Brand Route
    Route::delete('/admin/brand/{id}/delete', [AdminController::class, 'delete_brand'])->name('admin.delete-brand');

    //    Category Route
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name( 'admin.categories');
    //   New Category Route
    Route::get('/admin/category/add', [AdminController::class, 'add_category'])->name( 'admin.new-category');
    //   Store Category Route
    Route::post('/admin/category/store', [AdminController::class, 'store_category'])->name( 'admin.store-category');
    //   Edit Category Route
    Route::get('/admin/category/{id}/edit', [AdminController::class, 'edit_category'])->name( 'admin.edit-category');
    //   Update Category Route
    Route::put('/admin/category/update', [AdminController::class, 'update_category'])->name( 'admin.update-category');
    //   Delete Category Route
    Route::delete('/admin/category/{id}/delete', [AdminController::class, 'delete_category'])->name( 'admin.delete-category');

    // Products Route
    Route::get('/admin/products', [AdminController::class, 'products'])->name( 'admin.products');
    // Add Product Route
    Route::get('/admin/product/add', [AdminController::class, 'add_product'])->name( 'admin.product.add');
    // Store Product Route
    Route::post('/admin/product/store', [AdminController::class, 'store_product'])->name( 'admin.product.store');

});
// End Login Groups

