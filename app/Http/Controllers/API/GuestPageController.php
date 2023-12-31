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
use App\Models\Subscription;

class GuestPageController extends Controller
{
    public function index(Request $request)
    {
        $discipline = Discipline::with('classes')->select('id', 'titre', 'discipline_description')->get();
        $instructors = User::role('instructor')->select('id', 'name', 'email', 'description', 'img_url')->get();
        $subscriptions = Subscription::all();
        return response([
            "discipline" => $discipline,
            "instructors" => $instructors,
            'subscriptions' => $subscriptions
        ], 201);
    }

    public function getDisciplines(Request $request)
    {
        $query = Discipline::query();

        if ($request->has('id')) {
            $query->where('id', $request->input('id'));
            $query->with(['classes.courses' => function ($query) {
                $query->where('status', 'accepted');
            }]);
        }

        if ($request->has('instructor_id')) {
            $instructorId = $request->input('instructor_id');

            $query->whereHas('classes.courses', function ($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId);
                $query->where('status', 'accepté');
            });
        }

        $query->with(['classes.courses' => function ($query) {
            $query->where('status', 'accepté');
        }]);
        
        $query->withCount('classes'); // Add the count of classes


        $discipline = $query->get();

        return response()->json([
            'success' => true,
            'data' => $discipline,
            'message' => 'Disciplines fetched.'
        ]);
    }

    public function getSubscriptions(Request $request)
    {
        $query = Subscription::query();

        if ($request->has('id')) {
            $query->where('id', $request->input('id'));
        }

        $subscriptions = $query->get();

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
            'message' => 'Subscriptions fetched.'
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



        $courses = $query->where('status', '=', 'accepté')->get();

        foreach ($courses as $course) {
            $course['url'] = null;
        }

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

        $instructors = $query->select('id', 'name', 'email', 'img_url', 'description')->get();

        return response()->json([
            'instructors' => $instructors,
        ]);
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
