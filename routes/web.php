<?php

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

// Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/print/{id}', [App\Http\Controllers\HomeController::class, 'print'])->name('print');
Route::get('/privacy', [App\Http\Controllers\HomeController::class, 'privacy'])->name('privacy');

// TEMP
Route::post('/send-whatsapp-message', [App\Http\Controllers\API\ApiController::class, 'chatgpt_send_test'])->name('whatsapp.send');
