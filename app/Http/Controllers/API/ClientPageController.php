<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Models\Course;

class ClientPageController extends Controller
{

    public function getCourses()
    {
        $user = auth()->user();

        if ($user->hasPermissionTo('access-all-content')) {
            // User has permission, fetch courses with required information
            $courses = Course::all();
            return response()->json($courses);
        } else {
           $courses::
        }
    }
}
