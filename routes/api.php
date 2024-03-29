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
use App\Http\Controllers\MailController;
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
Route::post('mail/change/password', [MailController::class, 'changePassword']);


Route::middleware('auth:sanctum')->group(function () {


    //Get the events with location
    Route::get(
        'barathonien/events',
        [EventController::class, 'getEventsLocation']
    )->name('barathonien.eventslocation');

    Route::controller(MailController::class)->group(function () {
        Route::get('send', 'hello');
        Route::get('pro/mail/welcome/{id}', 'welcomePro');
        Route::get('barathonien/mail/welcome/{id}', 'welcomeBarathonien');
        Route::get('pro/mail/valide/{id}/{status}', 'statusPro');
        Route::get('pro/mail/valide/establishment/{id}/{status}', 'statusEstablishmentPro');
        Route::get('pro/mail/valide/event/{id}/{status}', 'statusEventPro');
        Route::post('category/mail/new/{userId}/', 'sendMailNewCategory');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(InseeController::class)->group(function () {
        Route::get('/check-siren/{siren}', 'getSiren')->name('check-siren');
        Route::get('/check-siren-local/{siren}', 'getSirenFromLocal')->name('check-siren-local');
        Route::get('/check-siret-local/{siret}', 'getSiretFromLocal')->name('check-siret-local');
        Route::get('/check-siret/{siret}', 'getSiret')->name('check-siret');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(BarathonienController::class)->group(function () {
        Route::get('/barathonien/list', 'getBarathonienList')->name('barathonien.list');
        Route::get('/barathonien/{user_id}', 'show')->name('barathonien.show');
        Route::put('/barathonien/{user_id}', 'update')->name('barathonien.update');
        Route::delete('/barathonien/{user_id}', 'destroy')->name('barathonien.delete');
        Route::get('/barathonien/restore/{user_id}', 'restore')->name('barathonien.restore');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(OwnerController::class)->group(function () {
        Route::get('/pro/list', 'getOwnerList')->name('owner.list');
        Route::get('/admin/pro-to-validate', 'getOwnerToValidate')->name('admin.pro-to-validate');
        Route::patch('/pro/{owner_id}/validation/{status_code}', 'validateOwner')->name('pro.validation');
        Route::get('pro/{user_id}', 'show')->name('owner.show');
        Route::put('pro/{user_id}', 'update')->name('owner.update');
        Route::delete('pro/{user_id}', 'destroy')->name('owner.delete');
        Route::get('pro/restore/{user_id}', 'restore')->name('owner.restore');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AdministratorController::class)->group(function () {
        Route::get('/administrator/list', 'getAdministratorList')->name('administrator.list');
        Route::get('/administrator/{user_id}', 'show')->name('administrator.show');
        Route::put('/administrator/{user_id}', 'update')->name('administrator.update');
        Route::delete('/administrator/{user_id}', 'destroy')->name('administrator.delete');
        Route::get('/administrator/restore/{user_id}', 'restore')->name('administrator.restore');
        Route::post('register/admin', 'store')->name('user.register.admin');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(EmployeeController::class)->group(function () {
        Route::get('/employee/list', 'getEmployeeList')->name('employee.list');
        Route::get('employee/{user_id}', 'show')->name('employee.show');
        Route::put('employee/{user_id}', 'update')->name('employee.update');
        Route::delete('employee/{user_id}', 'destroy')->name('employee.delete');
        Route::get('employee/restore/{user_id}', 'restore')->name('employee.restore');
        Route::post('register/employee', 'store')->name('user.register.employee');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(EstablishmentController::class)->group(function () {
        Route::get('/establishments/list', 'getAllEstablishments')->name('admin.establishment.list');
        Route::get('/admin/establishment-to-validate', 'getEstablishmentToValidate')->name('admin.establishment-to-validate');
        Route::patch('/establishment/{establishment_id}/validation/{status_code}', 'validateEstablishment')->name('establishment.validation');
        Route::get('/pro/{owner_id}/establishment', 'getEstablishmentListByOwnerId')->name('establishment.list');
        Route::get('/pro/{owner_id}/establishment/{establishment_id}', 'show')->name('establishment.show');
        Route::put('/pro/establishment/{establishment_id}', 'update')->name('establishment.update');
        Route::post('/pro/{owner_id}/establishment', 'store')->name('establishment.store');
        Route::delete('/pro/establishment/{establishment_id}', 'destroy')->name('establishment.delete');
        Route::get('/pro/establishment/{establishment_id}/restore', 'restore')->name('establishment.restore');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(EventController::class)->group(function () {
        Route::get('/events/list', 'getEventList')->name('admin.event.list');
        Route::get('/admin/event-to-validate', 'getEventsToValidate')->name('admin.event-to-validate');
        Route::patch('/event/{event_id}/validation/{status_code}', 'validateEvent')->name('event.validation');
        Route::get('/barathonien/{id}/city/events', 'getEventsByUserCity')->name('barathonien.eventsByUserCity');
        Route::get('/barathonien/{id}/booking/events', 'getEventsBookingByUser')->name('barathonien.eventsBookByUser');
        Route::get('/barathonien/event/{idevent}/user/{iduser}', 'getEventByUserChoice')->name('barathonien.eventByUserChoice');
        Route::get('/pro/events/{establishmentId}', 'getEventsByEstablishmentId')->name('pro.eventsByEstablishmentId');
        Route::post('/pro/events', 'store')->name('pro.postEvents');
        Route::put('/pro/event/{eventId}', 'update')->name('pro.putEvent');
        Route::delete('/pro/event/{eventId}', 'destroy')->name('pro.event.delete');
        Route::get('/pro/event/{eventId}', 'show')->name('event.show');
        Route::get('admin/event/{eventId}/history', 'showEventWithHistory')->name('event.show.history');
        
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(StatusController::class)->group(function () {
        Route::get('/owner-status', 'ownerStatus')->name('owner-status');
        Route::get('/establishment-status', 'establishmentStatus')->name('establishment-status');
        Route::get('/events-status', 'eventsStatus')->name('events-status');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/barathonien/top/categories', 'getTopTenCategories')->name('barathonien.topCateg');
        Route::post('/category/create', 'store')->name('categories.store');
        Route::get('/categories/establishment', 'getAllEstablishmentCategories')->name('categories.establishment.all');
        Route::get('/categories/event', 'getAllEventCategories')->name('categories.event.all');
        Route::get('/categories', 'getAllCategories')->name('categories.all');
        Route::get('/category/{id}', 'show')->name('categories.show');
        Route::put('/category/{id}', 'update')->name('categories.update');
        Route::delete('/category/{id}', 'destroy')->name('categories.delete');
        Route::get('/category/restore/{id}', 'restore')->name('categories.restore');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(CategoryEventController::class)->group(function () {
        Route::put('/pro/event/{eventId}/category', 'associateCategoriesToEvent')->name('event.toCategory');
        Route::get('/pro/event/{eventId}/category', 'getAllCategoriesByEventId')->name('pro.event.eventById');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(CategoryEstablishmentController::class)->group(function () {
        Route::get('/categories/establishment/{establishmentId}', 'getAllCategoriesByEstablishmentId')->name('categories.establishment');
        Route::put('/pro/establishment/{establishment_id}/category', 'associateCategoriesToEstablishment')->name('categories.establishment.update');
        Route::post('/pro/establishment/{establishment_id}/category', 'associateCategoriesToEstablishment')->name('categories.establishment.store');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(BookingController::class)->group(function () {
        Route::post('/barathonien/booking', 'store')->name('barathonien.postBooking');
        Route::delete('/barathonien/booking/{id}', 'destroy')->name('barathonien.deleteBooking');
    });
});

Route::controller(BookingController::class)->group(function () {
    Route::post('/pro/book/{id}', 'valideTicket')->name('pro.valideTicket');
    Route::get('/pro/event/{idEvent}/barathonien/{id}', 'getEventandUser')->name('pro.getEventandUser');
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::controller(ApiAuthController::class)->group(function () {
        Route::post('/logout', 'logout')->name('user.logout');
        Route::put('user/{user_id}/password', 'updateUserPassword')->name('user.update-user-password');
    });
});
