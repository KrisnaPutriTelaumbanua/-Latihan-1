<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [App\Http\Controllers\AuthController::class, 'index'])->name('auth.index')->middleware('guest');
Route::post('/login',[App\Http\Controllers\AuthController::class, 'verify'])->name('auth.verify');

Route::group(['middleware' => 'auth:user'], function (){
    Route::prefix('admin')->group(function (){
        Route::get('/',[App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/profile',[App\Http\Controllers\DashboardController::class, 'profile'])->name('dashboard.profile'); // Mengubah nama rute untuk profil

    });
    Route::get('/logout',[App\Http\Controllers\AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:user'); // Menambahkan middleware auth:user untuk logout
});
