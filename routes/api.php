<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GuestPageController;
use App\Http\Controllers\API\InstructorPageController;
use App\Http\Controllers\API\AdminPageController;
use App\Http\Controllers\API\ClientPageController;
use App\Http\Controllers\API\PayementController;
use App\Http\Controllers\API\UserController;
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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/profile', 'getProfile');
        Route::post('/edit-profile', 'editProfile');
        Route::post('/change-password', 'changePassword');
        Route::post('/become-instructor', 'becomeInstructor');
    });
    Route::controller(PayementController::class)->group(function () {
        Route::post('/purchase', 'purchaseProcess');
        Route::post('/subscribe', 'subscribe');
    });
});



Route::controller(GuestPageController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/instructors', 'getInstructors');
    Route::get('/discipline', 'discipline');
    // ---- NO CHANGER ------- 
    Route::get('/packs', 'getPacks');
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
        Route::get('/discipline/{id}', 'getCoursesByDiscipline');
        Route::get('/disciplines', 'getDisciplines');
        Route::post('/create-course', 'createCourse');
        Route::get('/courses', 'getCourses');
        Route::get('/courses/{id}', 'getCourseById');
        Route::post('/create-pack', 'createpack');
        Route::post('/courses/{id}/edit', 'editCourse');
        Route::put('/packs/{id}/edit', 'editPack');
        Route::get('/packs', 'getPack');
        Route::get('/packs/{id}', 'getPackById');
        Route::delete('/delete-course/{id}', 'deleteCourse');
        Route::delete('/delete-pack/{id}', 'deletePack');
        Route::get('/profile', 'getProfile');
        Route::get('/disciplines','getDisciplines');
        //route for required password change 
        Route::post('/change-password', 'changePassword')->middleware('password.change');
    });
});

//Admin Route
Route::group(['prefix' => 'admin',  /*'middleware' => ['auth:sanctum', 'role:admin']*/], function () {
    Route::controller(AdminPageController::class)->group(function () {
        Route::get('/dashbord', 'dashbord');
        Route::get('/disciplines', 'getdisciplines');
        Route::post('/create-discipline', 'createDiscipline');
        Route::get('/discipline/{disciplineId}', 'getDisciplineDetails');
        Route::put('/discipline/{disciplineId}/edit', 'editDiscipline');
        Route::delete('/discipline/{id}/delete', 'deleteDiscipline');
        Route::post('/discipline/{id}/create-classe', 'createClasse');
        Route::put('/classe/{id}/edit', 'editClasse');
        Route::delete('/classe/{id}/delete', 'deleteClasse');
        Route::get('/instructors', 'getInstructor');
        Route::get('/instructors/pending-application', 'getAllApplications');
        Route::post('/instructors/pending-application/{id}/accept', 'acceptInstructorApplication');
        Route::post('/instructors/pending-application/{id}/refuse', 'refuseInstructorApplication');
        Route::post('/instructors/{id}/activate-instructor', 'activateInstructor');
        Route::post('/instructors/{id}/desactivate-instructor', 'desactivateInstructor');
        Route::get('/courses', 'getCourses');
        Route::get('/courses/pending-courses', 'getPendingCourses');
        Route::get('/packs', 'getPacks');
        Route::get('/packs/pending-packs', 'getPendingPacks');
        Route::post('/course/{id}/approve', 'approveCourese');
        Route::post('/course/{id}/refuse', 'refuseCourse');
        Route::post('/packs/{id}/approve', 'approvePack');
        Route::post('/packs/{id}/refuse', 'refusePack');
        Route::get('/client', 'getClients');
        Route::get('/client/subscriber', 'getSubscribersInformation');
    });
})->middleware('auth:sanctum', 'role:admin');

Route::group(['prefix' => 'client',  'middleware' => ['auth:sanctum', 'role:client']], function () {
    Route::controller(ClientPageController::class)->group(function () {
        Route::get('/', 'getCourses');
        Route::get('/course/{id}', 'getCourse');
    });
});
