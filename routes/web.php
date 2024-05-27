<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Tpv;
use App\Livewire\Crear;
use App\Livewire\Test;
use App\Livewire\Fichar;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tpv', Tpv::class)->name('tpv');
Route::get('/fichar', Fichar::class)->name('fichar');
Route::get('/crear', Crear::class);
Route::get('/test', Test::class);


Auth::routes();
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
