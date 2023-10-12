<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ModalController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/auth/telegram', [ProfileController::class, 'telegram'])->name('auth.telegram');


Route::get('/', [HomeController::class, 'sites'])->name('sites')->middleware(['auth', 'verified']);
Route::get('/categories', [CategoryController::class, 'list'])->name('categories')->middleware(['auth', 'verified']);
Route::get('/roles', [HomeController::class, 'roles'])->name('roles')->middleware(['auth', 'verified']);


Route::middleware('auth')->group(function () {
    Route::get('/site/{site}/view', [HomeController::class, 'siteView'])->name('site.view');
    Route::get('/site/{site}/data', [HomeController::class, 'siteData'])->name('site.data');
    Route::get('/site/{site}/edit', [HomeController::class, 'siteEdit'])->name('site.edit');
    Route::get('/site/add', [HomeController::class, 'siteAdd'])->name('site.add');
    Route::post('/site/store', [HomeController::class, 'siteStore'])->name('site.store');
    Route::get('/site/{site}/destroy', [HomeController::class, 'siteDestroy'])->name('site.destroy');
    Route::put('/site/{site}/update', [HomeController::class, 'siteUpdate'])->name('site.update');
    Route::post('/site/rent', [OrderController::class, 'store'])->name('order.store');
    Route::get('/site/{site}/get_30days_orders', [ModalController::class, 'get30daysOrders'])->name('site.get_30days_orders');
    Route::get('/site/{site}/show-last-10-orders', [ModalController::class, 'showLast10orders'])->name('site.show_last_10_orders');

    Route::get('/category/{category}/view', [CategoryController::class, 'view'])->name('category.view');
    Route::get('/category/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::get('/category/{category}/destroy', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::put('/category/{category}/update', [CategoryController::class, 'update'])->name('category.update');

    Route::get('/profile/{profile}/view', [ProfileController::class, 'view'])->name('profile.view');
    Route::get('/profiles', [ProfileController::class, 'list'])->name('profiles');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/delete', [ProfileController::class, 'delete'])->name('profile.delete');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('profile/first-screen-update', [ProfileController::class, 'firstScreenUpdate'])->name('profile.first.screen.update');

    Route::get('/orders', [OrderController::class, 'list'])->name('orders');
    Route::get('/order/{order}/edit', [OrderController::class, 'edit'])->name('order.edit');
    Route::put('/order/{order}/update', [OrderController::class, 'update'])->name('order.update');
    Route::get('/order/{order}/destroy', [OrderController::class, 'destroy'])->name('order.destroy');
    Route::get('/order/{order}/approve', [OrderController::class, 'approve'])->name('order.approve');
    Route::get('/order/add', [OrderController::class, 'add'])->name('order.add');

    Route::get('/region/{city_id}/cities', [LocationController::class, 'getRegionCities'])->name('region.cities');

    Route::patch('role/update', [ProfileController::class, 'roleUpdate'])->name('role.update');
    Route::patch('profile/city-update', [ProfileController::class, 'cityUpdate'])->name('profile.city.update');
});

require __DIR__.'/auth.php';
