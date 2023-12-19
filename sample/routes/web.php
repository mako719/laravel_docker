<?php

use App\Http\Controllers\RegisterController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/register', [\App\Http\Controllers\RegisterController::class, 'create'])
    ->middleware('guest')
    ->name('register');
Route::post('/register', [\App\Http\Controllers\RegisterController::class, 'store'])
    ->middleware('guest');

Route::get('/login', [\App\Http\Controllers\LoginController::class, 'index'])
    ->middleware('guest')
    ->name('login');
Route::post('/login', [\App\Http\Controllers\LoginController::class, 'authenticate'])
    ->middleware('guest');
Route::get('/logout', [\App\Http\Controllers\LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

