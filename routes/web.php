<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
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
Route::get('/categories', [HomeController::class, 'categories'])->name('categories')->middleware(['auth', 'verified']);
Route::get('/users', [HomeController::class, 'users'])->name('users')->middleware(['auth', 'verified']);
Route::get('/roles', [HomeController::class, 'roles'])->name('roles')->middleware(['auth', 'verified']);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('role/update', [ProfileController::class, 'roleUpdate'])->name('role.update');
});

require __DIR__.'/auth.php';
