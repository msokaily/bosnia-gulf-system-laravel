<?php

use App\Http\Controllers\AccommodationsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CarCompaniesController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
// header('Access-Control-Allow-Credentials: true');
// header('Access-Control-Allow-Headers: Authorization, Content-Type');

Route::group(['namespace' => 'API'], function () {
    // Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});
Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::resource('users', UsersController::class);
    Route::get('/profile', [UsersController::class, 'profile']);
    Route::resource('cars', CarsController::class);
    Route::resource('car-companies', CarCompaniesController::class);
    Route::resource('partners', PartnersController::class);
    Route::resource('accommodations', AccommodationsController::class);
    Route::get('constants', [HomeController::class, 'constants']);
});
