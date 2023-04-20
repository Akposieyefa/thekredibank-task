<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
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


Route::group(['middleware' => 'api', 'prefix' => 'v1'], function ($router) {

    Route::group(['middleware' => ['jwt.verify']], function() { //jwt verified routes


        Route::controller(AuthController::class)->group(function () { //auth controller
            Route::post('logout',  'logoutUser');
            Route::post('refresh',  'refreshAuthToken');
            Route::get('profiles', 'getUserProfile');
            Route::post('login','loginUser')->withoutMiddleware('jwt.verify');
        });

        Route::controller(CustomerController::class)->group(function () { //customers controller
            Route::post('customers',  'store');
            Route::get('customers',  'index');
            Route::get('customers/{slug}',  'show');
            Route::patch('customers/{slug}',  'update');
            Route::delete('customers/{slug}',  'destroy');
            Route::post('approve/{slug}',  'approve');
            Route::get('customers-request',  'getTemporaryCustomersRequest');
        });

    });

});

