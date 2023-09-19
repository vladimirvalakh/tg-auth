<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
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
    Route::get('/site/{site}/edit', [HomeController::class, 'siteEdit'])->name('site.edit');
    Route::get('/site/{site}/destroy', [HomeController::class, 'siteDestroy'])->name('site.destroy');
    Route::put('/site/{site}/update', [HomeController::class, 'siteUpdate'])->name('site.update');

    Route::get('/category/{category}/view', [CategoryController::class, 'view'])->name('category.view');
    Route::get('/category/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::get('/category/{category}/destroy', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::put('/category/{category}/update', [CategoryController::class, 'update'])->name('category.update');

    Route::get('/profile/{profile}/view', [ProfileController::class, 'view'])->name('profile.view');
    Route::get('/profiles', [ProfileController::class, 'list'])->name('profiles');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/delete', [ProfileController::class, 'delete'])->name('profile.delete');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('role/update', [ProfileController::class, 'roleUpdate'])->name('role.update');
});

require __DIR__.'/auth.php';
