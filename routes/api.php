<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryEstablishmentController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\EventController;
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
    | Barathonien Routes
    |--------------------------------------------------------------------------
    */

    //get Event by user's city
    Route::get('/barathonien/{id}/city/events', [EventController::class, 'getEventsByUserCity'])->name('barathonien.eventsByUserCity');

    //get Events booking by the User
    Route::get('/barathonien/{id}/booking/events', [EventController::class, 'getEventsBookingByUser'])->name('barathonien.eventsBookByUser');

    // get top 10 tags
    Route::get('/barathonien/top/categories', [CategoryController::class, 'getTopTenCategories'])->name('barathonien.topCateg');

    // get an event by user choice
    Route::get('/barathonien/event/{idevent}/user/{iduser}', [EventController::class, 'getEventByUserChoice'])->name('barathonien.eventByUserChoice');

    // POST booking
    Route::post('/barathonien/booking', [BookingController::class, 'store'])->name('barathonien.postBooking');

    // DELETE booking
    Route::delete('/barathonien/booking/{id}', [BookingController::class, 'destroy'])->name('barathonien.deleteBooking');


    /*
    |--------------------------------------------------------------------------
    | Establishments
    |--------------------------------------------------------------------------
    */
    Route::get('/pro/{owner_id}/establishment', [EstablishmentController::class, 'getEstablishmentList'])->name('establishment.list');
    Route::get('/pro/{owner_id}/establishment/{establishment_id}', [EstablishmentController::class, 'show'])->name('establishment.show');
    Route::put('/pro/{owner_id}/establishment/{establishment_id}', [EstablishmentController::class, 'update'])->name('establishment.update');
    Route::post('/pro/{owner_id}/establishment/create', [EstablishmentController::class, 'store'])->name('establishment.store');
    Route::delete('/pro/{owner_id}/establishment/{establishment_id}', [EstablishmentController::class, 'destroy'])->name('establishment.delete');
    Route::get('/pro/{owner_id}/establishment/{establishment_id}/restore', [EstablishmentController::class, 'restore'])->name('establishment.restore');

    /*
    |--------------------------------------------------------------------------
    | Categories for professional
    |--------------------------------------------------------------------------
    */
    Route::get('/pro/category', [CategoryController::class, 'getCategoriesList'])->name('categories.list');
    Route::post('/pro/category/create', [CategoryController::class, 'store'])->name('categories.store');


    /*
    |--------------------------------------------------------------------------
    | Pro Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/categories/establishment/{establishmentId}', [CategoryEstablishmentController::class, 'getAllCategoriesByEstablishmentId'])->name('categories.establishment');
    Route::get('/categories/establishment', [CategoryController::class, 'getAllEstablishmentCategories'])->name('categories.establishment.all');
    Route::get('/categories/event', [CategoryController::class, 'getAllEventCategories'])->name('categories.event.all');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

        Route::get('/barathonien/list', [BarathonienController::class, 'getBarathonienList'])->name('barathonien.list');

    });

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('user.logout');
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
});
