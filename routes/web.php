<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishListController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Surfsidemedia\Shoppingcart\Facades\Cart;

// use App\Http\Controllers\Session;

Auth::routes();
// Homepage Route
Route::get('/', [HomeController::class, 'index'])->name('home.index');

// Shop Page Route
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

// Product detail Route
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');

// Shopping Cart Route
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
// Add to Cart Route
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');
// Update  Cart Route
Route::put('/cart/increase_quantity/{rowId}', [CartController::class, 'increase_cart_qty'])->name('cart.qty.increase');
Route::put('/cart/decrease_quantity/{rowId}', [CartController::class, 'decrease_cart_qty'])->name('cart.qty.decrease');
// Remove  Cart  Item Route
Route::delete('/cart/remove/{rowId}', [CartController::class, 'remove_cart_item'])->name('cart.remove.item');
// Empty  Cart Route
Route::delete('/cart/clear', [CartController::class, 'empty_cart'])->name('cart.empty');

// Apply Coupon
Route::post('/cart/apply-coupon', [CartController::class, 'apply_coupon_code'])->name('cart.coupon.apply');
// Remove Coupon Code
Route::delete('/cart/remove-coupon', [CartController::class, 'remove_coupon_code'])->name('cart.coupon.remove');

// Add to Wish List Route
Route::post('/wishlist/add', [WishListController::class, 'add_to_whichlist'])->name('wishlist.add');
// Show All Wishlist
Route::get('/wishlist', [WishListController::class, 'index'])->name('wishlist.index');
// Delete  Wishlist Item
Route::delete('/wishlist/item/remove/{rowId}', [WishListController::class, 'remove_item'])->name('wishlist.item.remove');
// Clear  Wishlist
Route::delete('/wishlist/clear', [WishListController::class, 'empty_wishlist'])->name('wishlist.items.clear');
// Move to Cart Route
Route::post('/wishlist/move-to-cart/{rowId}', [WishListController::class, 'move_wishlist_to_cart'])->name('wishlist.move.to.cart');


// CHECKOUT ROUTE
Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
// Place an Order Route
Route::post('/place-an-order', [CartController::class, 'place_an_order'])->name('cart.place.an.order');
// Order Confirmtion
Route::get('/order-confirmation', [CartController::class, 'order_confirmation'])->name('cart.order.confirmation');


// Contact Page Route
Route::get('/contact-us', [HomeController::class, 'contact'])->name('home.contact');
// Contact Store Route
Route::post('/contact/store', [HomeController::class, 'contact_store'])->name('home.contact.store');




// Login Groups
// User Login
Route::middleware('auth')->group(function () {
    // Index Route
    Route::get('/acount-dashboard', [UserController::class, 'index'])->name('user.index');
    // Order Route
    Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');
    // Order Details Route
    Route::get('/account-order/{order_id}/details', [UserController::class, 'order_details'])->name('user.order.details');
    // Cancel Order Route
    Route::put('/account-order/cancel-order', [UserController::class, 'order_cancel'])->name('user.order.cancel');
});

// Admin Login and rights
Route::middleware(['auth', AuthAdmin::class])->group(function () {
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
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    //   New Category Route
    Route::get('/admin/category/add', [AdminController::class, 'add_category'])->name('admin.new-category');
    //   Store Category Route
    Route::post('/admin/category/store', [AdminController::class, 'store_category'])->name('admin.store-category');
    //   Edit Category Route
    Route::get('/admin/category/{id}/edit', [AdminController::class, 'edit_category'])->name('admin.edit-category');
    //   Update Category Route
    Route::put('/admin/category/update', [AdminController::class, 'update_category'])->name('admin.update-category');
    //   Delete Category Route
    Route::delete('/admin/category/{id}/delete', [AdminController::class, 'delete_category'])->name('admin.delete-category');

    // Products Route
    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    // Add Product Route
    Route::get('/admin/product/add', [AdminController::class, 'add_product'])->name('admin.product.add');
    // Store Product Route
    Route::post('/admin/product/store', [AdminController::class, 'store_product'])->name('admin.product.store');
    // Edit Product Route
    Route::get('/admin/product/{id}/edit', [AdminController::class, 'edit_product'])->name('admin.product.edit');
    // Update Product Route
    Route::put('/admin/product/update', [AdminController::class, 'update_product'])->name('admin.product.update');
    // Delete Product Route
    Route::delete('/admin/product/{id}/delete', [AdminController::class, 'delete_product'])->name('admin.product.delete');

    // GET COUPONS
    Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
    // Add COUPON
    Route::get('/admin/coupon/add', [AdminController::class, 'add_coupon'])->name('admin.coupon.add');
    // Store COUPON
    Route::post('/admin/coupon/store', [AdminController::class, 'store_coupon'])->name('admin.coupon.store');
    // Edit COUPON
    Route::get('/admin/coupon/{id}/edit', [AdminController::class, 'edit_coupon'])->name('admin.coupon.edit');
    // Update COUPON
    Route::put('/admin/coupon/update', [AdminController::class, 'update_coupon'])->name('admin.coupon.update');
    // Delete COUPON
    Route::delete('/admin/coupon/{id}/delete', [AdminController::class, 'delete_coupon'])->name('admin.coupon.delete');
    // GET ALL ORDERS
    Route::get('/admin/orders', [AdminController::class, 'show_orders'])->name('admin.orders');
    // SHOW ORDER DETAILS
    Route::get('/admin/order/{order_id}/details', [AdminController::class, 'order_details'])->name('admin.order.details');
    // Update Status
    Route::put('/admin/order/update-status', [AdminController::class, 'update_order_status'])->name('admin.order.status.update');

    // Slides Route
    Route::get('admin/slides', [AdminController::class, 'slides'])->name('admin.slides');
    // Add Slide Page Route
    Route::get('admin/slide/add', [AdminController::class, 'add_slide'])->name('admin.slide.add');
    // Store Slide Route
    Route::post('admin/slide/store', [AdminController::class, 'store_slide'])->name('admin.slide.store');
    // Edit Slide Route
    Route::get('admin/slide/{id}/edit', [AdminController::class, 'edit_slide'])->name('admin.slide.edit');
    // update Slide Route
    Route::put('admin/slide/update', [AdminController::class, 'update_slide'])->name('admin.slide.update');
    // Delete Slide Route
    Route::delete('admin/slide/{id}/delete', [AdminController::class, 'delete_slide'])->name('admin.slide.delete');

    // Contacts Route
    Route::get('admin/contacts', [AdminController::class, 'contacts'])->name('admin.contacts');
    // Delete Contact Route
    Route::delete('admin/contact/{id}/delete', [AdminController::class, 'delete_contact'])->name('admin.contact.delete');
});
// End Login Groups
