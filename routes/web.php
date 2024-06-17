<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Tpv;
use App\Livewire\Crear;
use App\Livewire\Test;
use App\Livewire\Fichar;
use App\Livewire\GestionInvetario;
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

Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    //Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    //Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
});

// Rutas que requieren autenticación
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('tpv');
    });

    Route::get('/tpv', Tpv::class)->name('tpv');
    Route::middleware('checkPrivileges:1')->get('/crear', Crear::class);
    Route::middleware('checkPrivileges:1')->get('/gestion-inventario', GestionInvetario::class)->name('gestion-inventario');
    Route::get('/test', Test::class);
    Route::get('/crear', Crear::class);
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});