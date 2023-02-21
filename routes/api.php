<?php

use App\Http\Controllers\EmployeeController;
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
Route::post('/register/employee', [EmployeeController::class, 'store'])->name('user.register.employee');

/*
|--------------------------------------------------------------------------
| Common Routes
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('user.logout');
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

/*
|--------------------------------------------------------------------------
| Barathonien Routes
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| Pro Routes
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::get('/barathonien/list', [BarathonienController::class, 'getBarathonienList'])->name('barathonien.list');
Route::get('/barathonien/{user_id}', [BarathonienController::class, 'show'])->name('barathonien.show');
Route::post('/barathonien/update/{user_id}', [BarathonienController::class, 'update'])->name('barathonien.update');
Route::delete('/barathonien/delete/{user_id}', [BarathonienController::class, 'destroy'])->name('barathonien.delete');
Route::get('/barathonien/restore/{user_id}', [BarathonienController::class, 'restore'])->name('barathonien.restore');


Route::get('/pro/list', [OwnerController::class, 'getOwnerList'])->name('owner.list');
Route::get('/pro/{user_id}', [OwnerController::class, 'show'])->name('owner.show');
Route::post('/pro/update/{user_id}', [OwnerController::class, 'update'])->name('owner.update');
Route::delete('/pro/delete/{user_id}', [OwnerController::class, 'destroy'])->name('owner.delete');
Route::get('/pro/restore/{user_id}', [OwnerController::class, 'restore'])->name('owner.restore');

Route::get('/employee/list', [EmployeeController::class, 'getEmployeeList'])->name('employee.list');
Route::get('/employee/{user_id}', [EmployeeController::class, 'show'])->name('employee.show');
Route::post('/employee/update/{user_id}', [EmployeeController::class, 'update'])->name('employee.update');
Route::delete('/employee/delete/{user_id}', [EmployeeController::class, 'destroy'])->name('employee.delete');
Route::get('/employee/restore/{user_id}', [EmployeeController::class, 'restore'])->name('employee.restore');

});
