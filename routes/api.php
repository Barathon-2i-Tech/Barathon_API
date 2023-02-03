<?php

use App\Http\Controllers\EstablishmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\BarathonienController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AdministratorController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

    /*
    |--------------------------------------------------------------------------
    | Register and Login Methods
    |--------------------------------------------------------------------------
    */
    Route::post('/login', [ApiAuthController::class, 'login'])->name('user.login');
    Route::post('/register', [ApiAuthController::class, 'register'])->name('user.register');
    Route::post('/register/barathonien', [BarathonienController::class, 'store'])->name('user.register.barathonien');
    Route::post('/register/owner', [OwnerController::class, 'store'])->name('user.register.owner');
    Route::post('/register/admin', [AdministratorController::class, 'store'])->name('user.register.admin');

Route::group(['middleware' => ['auth:sanctum']], function () {
    /*
    |--------------------------------------------------------------------------
    | Establishment
    |--------------------------------------------------------------------------
    */
    Route::get('/pro/{owner_id}/establishment', [EstablishmentController::class, 'getEstablishmentList'])->name('establishment.list');
    Route::get('/pro/{owner_id}/establishment/{establishment_id}', [EstablishmentController::class, 'show'])->name('establishment.show');
    Route::post('/pro/{owner_id}/establishment/{establishment_id}/update', [EstablishmentController::class, 'update'])->name('establishment.update');
    Route::post('/pro/{owner_id}/establishment/create', [EstablishmentController::class, 'store'])->name('establishment.store');
    Route::delete('/pro/{owner_id}/establishment/{establishment_id}/delete', [EstablishmentController::class, 'destroy'])->name('establishment.delete');
    Route::get('/pro/{owner_id}/establishment/{establishment_id}/restore', [EstablishmentController::class, 'restore'])->name('establishment.restore');
    /*
    |--------------------------------------------------------------------------
    | Common Routes
    |--------------------------------------------------------------------------
    */
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('user.logout');
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
});
