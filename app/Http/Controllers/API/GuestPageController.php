<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discipline;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Pack;

class GuestPageController extends Controller
{
    //
    public function index(Request $request)
    {
        $discipline = Discipline::with('classes')->select('id', 'titre', 'discipline_description', 'background_img')->get();
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

        $query->with('classes.courses');

        $discipline = $query->get();
        //$courses = Course::where('dicipline_id', $request['id']);
        return response()->json([
            'discipline' => $discipline //,
            //'course' => $courses
        ]);
    }

    public function explore(Request $request)
    {

        $query = Course::query();

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

        $courses = $query->select('id', 'titre', 'description', 'price', 'duration', 'background_image', 'niveau', 'views_number', 'sells_number')->get();

        return response()->json([
            'courses' => $courses,
        ]);
    }

    public function getInstructors(Request $request)
    {

        $instructorRole = Role::where('name', 'instructor')->firstOrFail();
        $query = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->where('model_has_roles.role_id', $instructorRole->id);
        if ($request->has('id')) {
            $query->where('users.id', $request->input('id'));
        }

        $instructors = $query->select('users.*')->get();

        return response()->json([
            'instructors' => $instructors,
        ]);
    }

    public function getCoursesByDiscipline($disciplineId)
    {
        $courses = Course::where('discipline_id', $disciplineId)->select('id', 'titre', 'description', 'price', 'background_image', 'niveau', 'views_number', 'sells_number')->get();

        return response()->json($courses);
    }

    public function getCourseById($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        return response()->json($course);
    }


    public function getPacks()
    {
        $packs = Pack::select('id', 'titre', 'description', 'niveau', 'price', 'instructor_id', 'discipline_id', 'created_at', 'updated_at', 'views_number', 'sells_number', 'status', 'background_image')->get();

        return response()->json($packs, 200);
    }



    public function getInstructorById($id)
    {
        $instructorRole = Role::where('name', 'instructor')->firstOrFail();
        $instructor = $instructorRole->users->where('id', $id)->map(function ($user) {
            return $user->only(['id', 'name', 'email', 'background_img', 'description', 'img_url']);
        });
        return response()->json($instructor);
    }
}
