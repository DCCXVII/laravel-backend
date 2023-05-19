<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discipline;
use App\Models\User;
use App\Models\Course;

class GuestPageController extends Controller
{
    //
    public function index(Request $request)
    {
        $discipline = Discipline::with('classes')->select('id', 'titre', 'discipline_description')->get();
        $instructors = User::role('instructor')->select('id', 'name', 'email', 'description', 'img_url')->get();

        return response([
            "discipline" => $discipline,
            "instructors" => $instructors,

        ], 201);
    }

    public function discipline(Request $request)
    {
        $query = Discipline::query();

        if ($request->has('id')) {
            $query->where('id', $request->input('id'));
            $query->with('classes.courses');
        }

        if ($request->has('instructor_id')) {
            $instructorId = $request->input('instructor_id');

            $query->whereHas('classes.courses', function ($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId);
            });
        }

        $query->with('classes.classes');

        $discipline = $query->get();
        //$courses = Course::where('dicipline_id', $request['id']);
        return response()->json([
            'discipline' => $discipline //,
            //'course' => $courses
        ]);
    }

    public function explore(Request $request)
    {

        $query = Course::query()->with(['discipline:name', 'classe:name', 'User:name']);

        if ($request->has('id')) {
            $query->where('id', $request->input('id'));
        }
    
        if ($request->has('discipline_id')) {
            $query->where('discipline_id', $request->input('discipline_id'));
        }
    
        if ($request->has('classe_id')) {
            $query->where('classe_id', $request->input('classe_id'));
        }
    
        if ($request->has('instructor_id')) {
            $query->where('instructor_id', $request->input('User_id'));
        }
    
        if ($request->has('duration')) {
            $duration = $request->input('duration');
            $query->whereTime('duration', '>=', $duration);
        }
    
        if ($request->has('difficulty')) {
            $query->where('niveau', $request->input('difficulty'));
        }
    
        $courses = $query->select('id', 'titre', 'description', 'price', 'background_image', 'niveau')->get();
    
        return response()->json([
            'courses' => $courses,
        ]);
    }
}
