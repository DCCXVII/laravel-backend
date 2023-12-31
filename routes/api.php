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
    Route::post('login', 'login'); // checked
    Route::post('register', 'register'); // checked

});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/profile', 'getProfile');   // checked
        Route::post('/edit-profile', 'editProfile'); // checked
        Route::post('/change-password', 'changePassword'); // checked
        Route::post('/become-instructor', 'becomeInstructor'); // checked
    });
    Route::controller(PayementController::class)->group(function () {
        Route::post('/purchase', 'purchaseProcess');
        Route::post('/subscribe', 'subscribe');
    });
});

Route::group(['prefix' => 'client',  'middleware' => ['auth:sanctum', 'role:client']], function () {
    Route::controller(ClientPageController::class)->group(function () {
        Route::get('/', 'getCourses');
        Route::get('/course/{id}', 'getCourse');
    });
});


Route::controller(GuestPageController::class)->group(function () {
    Route::get('/', 'index'); // checked 
    Route::get('/instructors', 'getInstructors'); // checked
    Route::get('/disciplines', 'getDisciplines'); // checked
    Route::get('/subscriptions','getSubscriptions');
    Route::get('/packs', 'getPacks');
    Route::get('/explore', 'explore');
});


//Instrucor Route
Route::group(['prefix' => 'instructor', 'middleware' => ['auth:sanctum', 'role:instructor']], function () {
    Route::controller(InstructorPageController::class)->group(function () {
        Route::get('/dashbord', 'dashbord'); // checked
        Route::get('/disciplines/{id}', 'getCoursesByDiscipline');
        Route::get('/disciplines', 'getDisciplines'); // checked
        Route::post('/create-course', 'createCourse'); // checked
        Route::get('/courses', 'getCourses'); // checked
        Route::get('/courses/{id}', 'getCourseById'); // checked
        Route::post('/create-pack', 'createpack'); // checked
        Route::post('/courses/{id}/edit', 'editCourse'); // checked
        Route::put('/packs/{id}/edit', 'editPack'); // checked
        Route::get('/packs', 'getPack'); // checked
        Route::get('/packs/{id}', 'getPackById'); // checked
        Route::delete('/delete-course/{id}', 'deleteCourse'); // checked
        Route::delete('/delete-pack/{id}', 'deletePack'); // checked
        Route::post('create-live', 'createLive'); 
        // Route::get('/profile', 'getProfile');
        // Route::get('/disciplines', 'getDisciplines');
        //route for required password change 
        // Route::post('/change-password', 'changePassword')->middleware('password.change');
    });
});

//Admin Route
Route::group(['prefix' => 'admin',  /*'middleware' => ['auth:sanctum', 'role:admin']*/], function () {
    Route::controller(AdminPageController::class)->group(function () {
        Route::get('/dashbord', 'dashbord'); // checked
        Route::get('/disciplines', 'getdisciplines'); // checked
        Route::post('/create-discipline', 'createDiscipline'); // checked
        Route::get('/discipline/{disciplineId}', 'getDisciplineDetails'); // checked
        Route::put('/discipline/{disciplineId}/edit', 'editDiscipline'); // checked
        Route::delete('/discipline/{id}/delete', 'deleteDiscipline');
        Route::get('/discipline/{id}/classes', 'getClasses'); // checked
        Route::post('/discipline/{id}/create-classe', 'createClasse'); // checked
        Route::put('/classe/{id}/edit', 'editClasse'); // checked
        Route::delete('/classe/{id}/delete', 'deleteClasse');
        Route::get('/instructors', 'getInstructor'); // checked
        Route::get('/instructors/pending-application', 'getAllApplications'); // checked
        Route::post('/instructors/pending-application/{id}/accept', 'acceptInstructorApplication'); // checked
        Route::post('/instructors/pending-application/{id}/refuse', 'refuseInstructorApplication');
        Route::post('/instructors/{id}/remove-instructor', 'removeInstructor'); // checked
        Route::post('/instructors/{id}/activate-instructor', 'activateInstructor'); // checked
        Route::post('/instructors/{id}/desactivate-instructor', 'desactivateInstructor'); // checked
        Route::get('/courses', 'getCourses'); // checked
        Route::get('/courses/pending-courses', 'getPendingCourses'); // checked
        Route::get('/packs', 'getPacks'); // checked
        Route::get('/packs/pending-packs', 'getPendingPacks'); // checked
        Route::post('/course/{id}/approve', 'approveCourese');// checked
        Route::post('/course/{id}/refuse', 'refuseCourse');
        Route::post('/packs/{id}/approve', 'approvePack');
        Route::post('/packs/{id}/refuse', 'refusePack');
        Route::get('/client', 'getClients'); // checked
        Route::get('/client/subscriber', 'getSubscribersInformation');
        Route::post('/create-subscription', 'createSubscription');
    });
})->middleware('auth:sanctum', 'role:admin');




/*Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware('auth:sanctum')
    ->name('verification.verify');
// Route::post('/email/verifivartion-notification', [EmailVerificationController::class, 'sendVerification'])->middleware('auth:sanctum');
// Route::middleware('auth:sanctum')->get('/users', function (Request $request) {
//     return $request->user();
// });*/