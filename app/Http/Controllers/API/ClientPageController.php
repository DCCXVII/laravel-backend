<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Models\Course;
use App\Models\User;

class ClientPageController extends Controller
{

    public function getCourses()
    {
        $user = auth()->user();
        $courses = Course::Where('status', '=', 'accepté')->get();
        if ($user->hasPermissionTo('access-all-content')) {
    
            // User has permission, fetch courses with required information


        } else {
            foreach ($courses as $course) {
                $course['url'] = null;
            }
        }
        return response()->json($courses);
    }

    public function getCourse($id)
    {

        $user = auth()->user();
        $course = Course::Where('status', '=', 'accepté')->where('id', $id)->get();
        if ($user->hasPermissionTo('access-all-content')) {
            return response()->json($course);
        }
       
           
            if ($user->hasPurchasedItem($id, 'course')) {
                return response()->json($course);
               
            }
        
        
        foreach ($course as $c) {
            $c['url'] = null;
        }
        return response()->json($course);
    }
}
