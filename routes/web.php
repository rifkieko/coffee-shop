<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/menu', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/menu/search', [CatalogController::class, 'lookup'])->name('catalog.search');
Route::get('/menu/items/{menuItem}', [CatalogController::class, 'show'])->name('catalog.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::put('/cart/items/{item}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])->name('cart.items.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/summary', [CartController::class, 'summary'])->name('cart.summary');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/orders/{order:order_number}', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::post('/checkout/orders/{order:order_number}/confirm', [CheckoutController::class, 'confirmPayment'])->name('checkout.confirm-payment');
Route::get('/checkout/orders/{order:order_number}/status', [CheckoutController::class, 'status'])->name('checkout.status');
Route::get('/checkout/orders/{order:order_number}/paid', [CheckoutController::class, 'paid'])->name('checkout.paid');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

// Route removed: public order history page is deprecated

Route::middleware(['auth', 'verified', 'profile.completed', 'role:customer'])->group(function () {
    Route::get('/orders/history', [CustomerOrderController::class, 'history'])->name('customer.orders.history');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::get('/orders/{order}/payment', [CustomerOrderController::class, 'payment'])->name('customer.orders.payment');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('menu-items', MenuItemController::class);
    Route::patch('menu-items/{menu_item}/stock', [MenuItemController::class, 'updateStock'])->name('menu-items.update-stock');

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/payment', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.update-payment');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('reports/sales', [SalesReportController::class, 'index'])->name('reports.sales');
    Route::get('reports/sales/export', [SalesReportController::class, 'export'])->name('reports.sales.export');
});

require __DIR__.'/auth.php';
