<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/products', [ProductController::class, 'index'])->name('product.index');

Route::get('/product/{id}/allergenenInfo', [ProductController::class, 'allergenenInfo'])->name('product.allergenenInfo');

Route::get('/product/{id}/leverantieInfo', [ProductController::class, 'leverantieInfo'])->name('product.leverantieInfo');

// Suppliers
Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
Route::get('/suppliers/{id}/products', [SupplierController::class, 'products'])->name('suppliers.products');

// Delivery create/store
Route::get('/suppliers/{supplierId}/product/{productId}/delivery', [SupplierController::class, 'createDelivery'])->name('suppliers.delivery');
Route::post('/suppliers/{supplierId}/product/{productId}/delivery', [SupplierController::class, 'storeDelivery'])->name('suppliers.delivery.store');



Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
