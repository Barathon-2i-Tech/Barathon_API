<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\BarathonienController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;


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
Route::post('/register/barathonien', [BarathonienController::class, 'create'])->name('user.register.barathonien');
Route::post('/register/owner', [OwnerController::class, 'create'])->name('user.register.owner');
Route::post('/register/admin', [AdministratorController::class, 'create'])->name('user.register.admin');

/*
|--------------------------------------------------------------------------
| Common Routes
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth:sanctum']], function () {

    //Route Barathonien

        //get Event by user's city
        Route::get('/barathonien/{id}/city/event', [EventController::class, 'getEventByUserCity']);

        //get Event booking by the User
        Route::get('/barathonien/{id}/booking/event', [EventController::class, 'getEventBookingByUser']);

        // get top 10 tags
        Route::get('/barathonien/top/categories', [CategoryController::class, 'getTopTenCategories']);


    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('user.logout');
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
});
