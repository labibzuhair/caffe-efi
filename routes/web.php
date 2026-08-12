<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Middleware\RoleMiddleware;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\ProductManager;
use App\Livewire\Admin\SettingManager;
use App\Livewire\Admin\TableManager;
use App\Livewire\Admin\UserManager;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $categories = Category::where('is_active', true)
        ->with([
            'products' => function ($query) {
                $query;
            }
        ])
        ->get();

    return view('welcome', compact('categories'));
})->name('home');

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');


Route::middleware(['auth'])->group(function () {

    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/profile', 'profile')->name('profile');

    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        Route::get('/admin/categories', CategoryManager::class)->name('admin.categories');
        Route::get('/admin/products', ProductManager::class)->name('admin.products');
        Route::get('/admin/tables', TableManager::class)->name('admin.tables');
        Route::get('/admin/users', UserManager::class)->name('admin.users');
        Route::get('/admin/settings', SettingManager::class)->name('admin.settings');
        Route::get('/admin/expenses', \App\Livewire\Admin\ExpenseManager::class)->name('admin.expenses');
        Route::get('/admin/reports', \App\Livewire\Admin\ReportManager::class)->name('admin.reports');
    });

    Route::middleware([RoleMiddleware::class . ':admin,kitchen'])->group(function () {
        Route::get('/dapur', \App\Livewire\Kitchen\Dashboard::class)->name('dapur');
    });

    Route::middleware([RoleMiddleware::class . ':admin,cashier'])->group(function () {
        Route::get('/kasir', \App\Livewire\Cashier\Dashboard::class)->name('kasir');

        Route::get('/cetak-struk/{order}', function (\App\Models\Order $order) {
            $order->load(['items.product', 'items.selectedAddons', 'session.table']);
            return view('cetak-struk', compact('order'));
        })->name('cetak.struk');
    });

});



Route::get('/meja/{qr_token}', \App\Livewire\Customer\TableSession::class)->name('meja.scan');

Route::get('/pesan/aktif', \App\Livewire\Customer\ActiveOrder::class)->name('customer.active-order');

Route::get('/pesan', \App\Livewire\Customer\OrderMenu::class)->name('customer.menu');

require __DIR__ . '/auth.php';
