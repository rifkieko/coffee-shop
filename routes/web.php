<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\MidtransRedirectController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/menu', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/menu/search', [CatalogController::class, 'lookup'])->name('catalog.search');
Route::get('/menu/items/{menuItem}', [CatalogController::class, 'show'])->name('catalog.show');

Route::post('/midtrans/webhook', MidtransWebhookController::class)->name('midtrans.webhook');
Route::get('/midtrans/finish', [MidtransRedirectController::class, 'finish'])->name('midtrans.finish');
Route::get('/midtrans/unfinish', [MidtransRedirectController::class, 'unfinish'])->name('midtrans.unfinish');
Route::get('/midtrans/error', [MidtransRedirectController::class, 'error'])->name('midtrans.error');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::put('/cart/items/{item}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])->name('cart.items.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/summary', [CartController::class, 'summary'])->name('cart.summary');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/orders/{order}/{token}', [CheckoutController::class, 'payment'])->name('checkout.payment');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route removed: public order history page is deprecated

Route::middleware(['auth', 'verified', 'profile.completed', 'role:customer'])->group(function () {
    Route::get('/tables/{table:slug}/order', [CustomerOrderController::class, 'create'])->name('customer.orders.create');
    Route::post('/tables/{table:slug}/order', [CustomerOrderController::class, 'store'])->name('customer.orders.store');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::get('/orders/{order}/payment', [CustomerOrderController::class, 'payment'])->name('customer.orders.payment');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('menu-items', MenuItemController::class);
    Route::patch('menu-items/{menu_item}/stock', [MenuItemController::class, 'updateStock'])->name('menu-items.update-stock');

    Route::resource('tables', TableController::class)->except(['show']);
    Route::post('tables/{table}/regenerate-token', [TableController::class, 'regenerateToken'])->name('tables.regenerate-token');

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/payment', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.update-payment');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

require __DIR__.'/auth.php';
