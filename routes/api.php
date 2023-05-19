<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GuestPageController;
use Illuminate\Http\Request;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
});

Route::controller(GuestPageController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/discipline/{id}', 'getDiscipline');
    Route::get('/instrunctor/', 'getInstructor');
    Route::get('/series/', 'getPacks');
    Route::get('/explore','explore');
});
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware('auth:sanctum')
   ->name('verification.verify');
/*Route::post('/email/verifivartion-notification', [EmailVerificationController::class, 'sendVerification'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/users', function (Request $request) {
    return $request->user();
});*/
Route::middleware('auth:sanctum', 'role:client')->get('/user', function (Request $request) {
    return $request->user();
});
