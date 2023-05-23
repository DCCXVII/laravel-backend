<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GuestPageController;
use App\Http\Controllers\API\InstructorPageController;
use App\Http\Controllers\API\AdminPageController;
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
    Route::get('/explore', 'explore');
});
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware('auth:sanctum')
    ->name('verification.verify');
/*Route::post('/email/verifivartion-notification', [EmailVerificationController::class, 'sendVerification'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/users', function (Request $request) {
    return $request->user();
});*/
Route::group(['prefix' => 'instructor', 'middleware' => ['auth:sanctum', 'role:instructor']], function () {
    Route::controller(InstructorPageController::class)->group(function () {
        Route::get('/dashbord', 'dashbord');
        //route for required password change 
        Route::post('/change-password', 'changePassword')->middleware('password.change');
    });
});

//Admin Route
Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum', 'role:admin']], function () {
    Route::controller(AdminPageController::class)->group(function () {
        Route::get('/dashbord', 'dashbord');
        Route::get('/disciplines','getdisciplines');
        Route::post('/cretae-discipline','createDiscipline');
        Route::get('/discipline/{disciplineId}','getDisciplineDetails');
        Route::get('/instructors','getInstructor');
        Route::post('/instructors/create-instructor','craeteInstructor');
        Route::post('/instructors/{id}/activate-instructor','activateInstructor');
        Route::post('/instructors/{id}/desactivate-instructor','desactivateInstructor');
        Route::get('/courses','getCourses');
        Route::get('/courses/pending-courses','getPendingCourses');
        Route::post('courses/pending-courses/{courseId}/change-state','changeCourseState');
        Route::get('/packs','getPacks');
        Route::get('/packs/pending-packs','getPendingPacks');
        Route::post('/packs/pending-packs/{packId}/change-state','changePackState');
        Route::get('/client','getClinets');
        Route::get('/clinet/subscriber','getSubscriber');

    });
})->middleware('auth:sanctum', 'role:admin');

Route::middleware('auth:sanctum', 'role:client')->get('/user', function (Request $request) {
    return $request->user();
});
