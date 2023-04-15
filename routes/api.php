<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\BarathonienController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryEstablishmentController;
use App\Http\Controllers\CategoryEventController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InseeController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\StatusController;
use Illuminate\Support\Facades\Route;

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
Route::post('login', [ApiAuthController::class, 'login'])->name('user.login');
Route::post('register', [ApiAuthController::class, 'register'])->name('user.register');
Route::post('register/barathonien', [BarathonienController::class, 'store'])->name('user.register.barathonien');
Route::post('register/owner', [OwnerController::class, 'store'])->name('user.register.owner');
Route::post('register/admin', [AdministratorController::class, 'store'])->name('user.register.admin');
Route::post('register/employee', [EmployeeController::class, 'store'])->name('user.register.employee');

/*
|--------------------------------------------------------------------------
| Common Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('logout', [ApiAuthController::class, 'logout'])->name('user.logout');


    // get top 10 tags
    Route::get(
        'barathonien/top/categories',
        [CategoryController::class, 'getTopTenCategories']
    )->name('barathonien.topCateg');

    /*
    |************************************************************************************************
    | Administration Routes
    |************************************************************************************************
    */

    Route::get('barathonien/list', [BarathonienController::class, 'getBarathonienList'])->name('barathonien.list');
    Route::get('pro/list', [OwnerController::class, 'getOwnerList'])->name('owner.list');
    Route::get(
        'administrator/list',
        [AdministratorController::class, 'getAdministratorList']
    )->name('administrator.list');
    Route::get('employee/list', [EmployeeController::class, 'getEmployeeList'])->name('employee.list');
    Route::get(
        'establishments/list',
        [EstablishmentController::class, 'getAllEstablishments']
    )->name('admin.establishment.list');
    Route::get('events/list', [EventController::class, 'getEventList'])->name('admin.event.list');


    /*
    |--------------------------------------------------------------------------
    | Status Routes
    |--------------------------------------------------------------------------
    */

    Route::get('owner-status', [StatusController::class, 'ownerStatus'])->name('owner-status');
    Route::get('establishment-status', [StatusController::class, 'establishmentStatus'])->name('establishment-status');
    Route::get('events-status', [StatusController::class, 'eventsStatus'])->name('events-status');

    /*
    |--------------------------------------------------------------------------
    | Validation Routes
    |--------------------------------------------------------------------------
    */

    Route::get('admin/pro-to-validate', [OwnerController::class, 'getOwnerToValidate'])->name('admin.pro-to-validate');
    Route::get(
        'admin/establishment-to-validate',
        [EstablishmentController::class, 'getEstablishmentToValidate']
    )->name('admin.establishment-to-validate');
    Route::get('admin/event-to-validate', [EventController::class, 'getEventsToValidate'])->name('admin.event-to-validate');

    Route::put(
        'establishment/{establishment_id}/validation/{status_code}',
        [EstablishmentController::class, 'validateEstablishment']
    )->name('establishment.validation');
    Route::put(
        'pro/{owner_id}/validation/{status_code}',
        [OwnerController::class, 'validateOwner']
    )->name('pro.validation');
    Route::put(
        'event/{event_id}/validation/{status_code}',
        [EventController::class, 'validateEvent']
    )->name('event.validation');


    /*
    |--------------------------------------------------------------------------
    | Events Routes
    |--------------------------------------------------------------------------
    */

    //get Event by user's city
    Route::get(
        'barathonien/{id}/city/events',
        [EventController::class, 'getEventsByUserCity']
    )->name('barathonien.eventsByUserCity');

    //get Events booking by the User
    Route::get(
        'barathonien/{id}/booking/events',
        [EventController::class, 'getEventsBookingByUser']
    )->name('barathonien.eventsBookByUser');

    // get an event by user choice
    Route::get(
        'barathonien/event/{idevent}/user/{iduser}',
        [EventController::class, 'getEventByUserChoice']
    )->name('barathonien.eventByUserChoice');

    Route::get('pro/events/{establishmentId}', [EventController::class, 'getEventsByEstablishmentId'])->name('pro.eventsByEstablishmentId');

    Route::post('pro/events', [EventController::class, 'store'])->name('pro.postEvents');
    Route::put('pro/establishment/{establishmentId}/event/{eventId}', [EventController::class, 'update'])->name('pro.putEvent');
    Route::delete('/pro/event/{event_id}', [EventController::class, 'destroy'])->name('pro.event.delete');

    Route::put(
        '/pro/event/{eventId}/category',
        [CategoryEventController::class, 'associateCategoriesToEvent']
    )->name('event.update');

    Route::post(
        '/pro/event/{eventId}/category',
        [CategoryEventController::class, 'associateCategoriesToEvent']
    )->name('event.store');

    Route::get(
        '/pro/establishment/{establishmentId}/event/{eventId}',
        [EventController::class, 'show']
    )->name('event.show');
    /*
    |--------------------------------------------------------------------------
    | Booking Routes
    |--------------------------------------------------------------------------
    */

    Route::post('barathonien/booking', [BookingController::class, 'store'])->name('barathonien.postBooking');

    Route::delete(
        'barathonien/booking/{id}',
        [BookingController::class, 'destroy']
    )->name('barathonien.deleteBooking');

    /*
    |--------------------------------------------------------------------------
    | Pro Routes
    |--------------------------------------------------------------------------
    */
    Route::post('/pro/category/create', [CategoryController::class, 'store'])->name('categories.store');

    Route::get(
        '/categories/establishment/{establishmentId}',
        [CategoryEstablishmentController::class, 'getAllCategoriesByEstablishmentId']
    )->name('categories.establishment');
    Route::get(
        '/categories/establishment',
        [CategoryController::class, 'getAllEstablishmentCategories']
    )->name('categories.establishment.all');
    Route::get(
        '/categories/event',
        [CategoryController::class, 'getAllEventCategories']
    )->name('categories.event.all');

    Route::put(
        '/pro/establishment/{establishment_id}/category',
        [CategoryEstablishmentController::class, 'associateCategoriesToEstablishment']
    )->name('categories.update');

    Route::post(
        '/pro/establishment/{establishment_id}/category',
        [CategoryEstablishmentController::class, 'associateCategoriesToEstablishment']
    )->name('categories.store');

    /*
    |--------------------------------------------------------------------------
    | Barathonien Routes
    |--------------------------------------------------------------------------
    */

    Route::get('barathonien/{user_id}', [BarathonienController::class, 'show'])->name('barathonien.show');
    Route::put('barathonien/{user_id}', [BarathonienController::class, 'update'])->name('barathonien.update');
    Route::delete(
        'barathonien/{user_id}',
        [BarathonienController::class, 'destroy']
    )->name('barathonien.delete');
    Route::get(
        'barathonien/restore/{user_id}',
        [BarathonienController::class, 'restore']
    )->name('barathonien.restore');


    /*
    |--------------------------------------------------------------------------
    | Pro Routes
    |--------------------------------------------------------------------------
    */

    Route::get('pro/{user_id}', [OwnerController::class, 'show'])->name('owner.show');
    Route::put('pro/{user_id}', [OwnerController::class, 'update'])->name('owner.update');
    Route::delete('pro/{user_id}', [OwnerController::class, 'destroy'])->name('owner.delete');
    Route::get('pro/restore/{user_id}', [OwnerController::class, 'restore'])->name('owner.restore');


    /*
    |--------------------------------------------------------------------------
    | Administrator Routes
    |--------------------------------------------------------------------------
    */
    Route::get('administrator/{user_id}', [AdministratorController::class, 'show'])->name('administrator.show');
    Route::put(
        'administrator/{user_id}',
        [AdministratorController::class, 'update']
    )->name('administrator.update');
    Route::delete(
        'administrator/{user_id}',
        [AdministratorController::class, 'destroy']
    )->name('administrator.delete');
    Route::get(
        'administrator/restore/{user_id}',
        [AdministratorController::class, 'restore']
    )->name('administrator.restore');


    /*
    |--------------------------------------------------------------------------
    | Employee Routes
    |--------------------------------------------------------------------------
    */

    Route::get('employee/{user_id}', [EmployeeController::class, 'show'])->name('employee.show');
    Route::put('employee/{user_id}', [EmployeeController::class, 'update'])->name('employee.update');
    Route::delete('employee/{user_id}', [EmployeeController::class, 'destroy'])->name('employee.delete');
    Route::get('employee/restore/{user_id}', [EmployeeController::class, 'restore'])->name('employee.restore');

    /*
    |--------------------------------------------------------------------------
    | Establishment Routes
    |--------------------------------------------------------------------------
    */
    Route::get(
        '/pro/{owner_id}/establishment',
        [EstablishmentController::class, 'getEstablishmentListByOwnerId']
    )->name('establishment.list');
    Route::get(
        '/pro/{owner_id}/establishment/{establishment_id}',
        [EstablishmentController::class, 'show']
    )->name('establishment.show');
    Route::put(
        '/pro/{owner_id}/establishment/{establishment_id}',
        [EstablishmentController::class, 'update']
    )->name('establishment.update');
    Route::post(
        '/pro/{owner_id}/establishment/',
        [EstablishmentController::class, 'store']
    )->name('establishment.store');
    Route::delete(
        '/pro/{owner_id}/establishment/{establishment_id}',
        [EstablishmentController::class, 'destroy']
    )->name('establishment.delete');
    Route::get(
        '/pro/{owner_id}/establishment/{establishment_id}/restore',
        [EstablishmentController::class, 'restore']
    )->name('establishment.restore');


    /*
    |--------------------------------------------------------------------------
    | API SIRENE
    |--------------------------------------------------------------------------
    */

    Route::get('check-siren/{siren}', [InseeController::class, 'getSiren'])->name('check-siren');
    Route::get('check-siret/{siret}', [InseeController::class, 'getSiret'])->name('check-siret');


});
