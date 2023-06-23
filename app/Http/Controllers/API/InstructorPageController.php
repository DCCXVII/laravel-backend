<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use getid3\GetId3Core;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Discipline;

use App\Models\Course;
use App\Models\Pack;

class InstructorPageController extends Controller
{
    //

    public function dashbord(Request $request)
    {
        return response()->json([
            'message' => 'sing up successfully'
        ]);
    }

    public function createCourse(Request $request)
    {
        $instructorId = auth()->user()->id;
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string',
            'video' => 'required|mimetypes:video/mp4',
            'background_image' => 'required|image|mimes:jpeg,png,jpg',
            'description' => 'required|string',
            'niveau' => 'required|in:Débutant,Intermédiaire,avancée',
            'price' => 'required|numeric',
            'discipline_id' => 'required|exists:disciplines,id',
            'classe_id' => 'required|exists:classes,id',
        ]);


        if ($validator->fails()) {
            // Handle validation errors
            return response()->json(['errors' => $validator->errors()], 422);
        }


        if ($request->hasFile('video') &&  $request->hasFile('background_image')) {

            $video = $request->file('video');
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('videos'), $videoName);
            $videoUrl = url('/videos/' . $videoName);
            $videoPath = public_path('videos/' . $videoName);
            // Get video duration using FFProbe
            $getID3 = new \getID3;
            $file = $getID3->analyze($videoPath);

            $duration = date('H:i:s.v', $file['playtime_seconds']);
            // return response()->json($duration);


            // Get the uploaded file from the request
            $file = $request->file('background_image');

            // Generate a unique file name
            $fileName = time() . '.' . $file->getClientOriginalExtension();


            $request->background_image->move(public_path('images'), $fileName);;

            // Construct the full URL of the uploaded file
            $imageUrl = url('/images/' . $fileName);

            // Store the course with video URL and duration
            $course = new Course();
            $course->titre = $request->titre;
            $course->url = $videoUrl;
            $course->background_image = $imageUrl;
            $course->views_number = 0;
            $course->sells_number = 0;
            $course->duration = $duration;
            $course->description = $request->description;
            $course->niveau =  $request->niveau;
            $course->price =  $request->price;
            $course->discipline_id =  $request->discipline_id;
            $course->classe_id =  $request->classe_id;
            $course->instructor_id = $instructorId;
            $course->save();
            return response()->json([
                'message' => 'Course created successfully',
                'course' => $course,
            ], 201);
        }

        return response()->json(['message' => 'Video file not found'], 400);
    }

    public function getCourses()
    {
        $instructorId = auth()->user()->id;
        $courses = Course::where('instructor_id', $instructorId)

            ->get();

        return response()->json(
            $courses,
            200
        );
    }
    public function getCourseById($id)
    {
        $courses = Course::where('id', $id)->get();
        return response()->json($courses, 200);
    }


    public function getCoursesByDiscipline($disciplineId)
    {
        $courses = Course::where('discipline_id', $disciplineId)->get();

        return response()->json($courses);
    }

    /*    public function editCourse(Request $request, $id)
    {
        $validatedData = $request->validate([
            'titre' => 'string',
            'video' => 'required|mimetypes:video/mp4|max:50000',
            'description' => '|string',
            'nivaeu' => 'in:Débutant,Intermédiaire,avancée',
            'price' => 'numeric',
            'discipline_id' => 'exists:disciplines,id',
            'classe_id' => 'exists:classes,id',
        ]);

        $course = Course::findOrFail($id);
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $extension = $video->getClientOriginalExtension();
            $fileName = Str::uuid() . '.' . $extension;

            $videoPath = $video->storeAs('videos', $fileName, 'public');
            // Save the video path to the course model
            $course = new Course();
            // Set other course attributes
            $course->url = $videoPath;
            $course->save();
        }
        $course->update($validatedData);

        return response()->json([
            'message' => 'Course updated successfully',
            'course' => $course
        ], 200);
    } */


    public function editCourse(Request $request, $courseId)
    {


        $validator = Validator::make($request->all(), [
            'titre' => 'nullable|string',
            'video' => 'nullable|mimetypes:video/mp4',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg',
            'description' => 'nullable|string',
            'niveau' => 'nullable|in:Débutant,Intermédiaire,avancée',
            'price' => 'nullable|numeric',
            'discipline_id' => 'nullable|exists:disciplines,id',
            'classe_id' => 'nullable|exists:classes,id',
        ]);

        if ($validator->fails()) {
            // Handle validation errors
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the course by ID
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        // Update the course details based on the provided request data
        if ($request->has('titre')) {
            $course->titre = $request->titre;
        }

        if ($request->has('description')) {
            $course->description = $request->description;
        }

        if ($request->has('niveau')) {
            $course->niveau = $request->niveau;
        }

        if ($request->has('price')) {
            $course->price = $request->price;
        }

        if ($request->has('discipline_id')) {
            $course->discipline_id = $request->discipline_id;
        }

        if ($request->has('classe_id')) {
            $course->classe_id = $request->classe_id;
        }

        // Check if a new video file is uploaded
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('videos'), $videoName);
            $videoUrl = url('/videos/' . $videoName);
            $videoPath = public_path('videos/' . $videoName);

            // Get video duration using FFProbe
            $getID3 = new \getID3;
            $file = $getID3->analyze($videoPath);
            $duration = date('H:i:s.v', $file['playtime_seconds']);

            // Update video URL and duration
            $course->url = $videoUrl;
            $course->duration = $duration;
        }

        // Check if a new background image is uploaded
        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $fileName);
            $imageUrl = url('/images/' . $fileName);

            // Update background image URL
            $course->background_img = $imageUrl;
        }

        // Save the updated course
        $course->save();

        return response()->json([
            'message' => 'Course updated successfully',
            'course' => $course,
        ], 200);
    }



    public function createpack(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string',
            'description' => 'required|string',
            'niveau' => 'required|in:débutant,intermédiaire,avancée',
            'price' => 'required|numeric',
            //'classe_id' => 'required|exists:classes,id',
            'discipline_id' => 'required|exists:disciplines,id',
            'background_image' => 'required|image|mimes:jpeg,png,jpg',
            // 'teaser' => 'nullable|mimetypes:video/mp4',
            'courses'  => 'required|array',
            'courses.*' => 'exists:courses,id',


        ]);

        if ($validator->fails()) {
            // Handle validation errors
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pack = new Pack();

        $pack->instructor_id = auth()->user()->id; // Add instructor_id
        $pack->titre = $request->titre;
        $pack->views_number = 0;
        $pack->sells_number = 0;
        $pack->niveau = $request->niveau;
        $pack->price = $request->price;
        $pack->description = $request->description;
        $pack->discipline_id = $request->discipline_id;
        $pack->views_number = 0;
        $pack->sells_number = 0;

        if ($request->hasFile('background_image')) {
            // if ($request->hasFile('teaser')) {
            //     $video = $request->file('teaser');
            //     $videoName = time() . '.' . $video->getClientOriginalExtension();
            //     $video->move(public_path('videos'), $videoName);
            //     $videoUrl = url('/videos/' . $videoName);

            //     // Update video URL and duration
            //     $pack->teaser_url = $videoUrl;
            // }



            // $pack->teaser_url = "null";
            $file = $request->file('background_image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $fileName);
            $imageUrl = url('/images/' . $fileName);

            // Update background image URL
            $pack->background_image = $imageUrl;
        }
        $pack->save();

        $pack->courses()->attach($request->courses);

        return response()->json([
            'message' => 'Pack created successfully',
            'pack' => $pack
        ], 201);
    }

    public function getPackById($id)
    {
        $pack = Pack::where('id', $id)->with('courses')->get();
        return response()->json(
            $pack,
            200
        );
    }
    public function getPack()
    {
        $instructorId = auth()->user()->id;
        $packs = Pack::where('coach_id', $instructorId)->with('courses')->get();

        return response()->json(
            $packs,
            200
        );
    }

    public function editPack(Request $request, $id)
    {
        $validatedData = $request->validate([
            'titre' => 'string',
            'description' => 'string',
            'niveau' => 'in:débutant,intermédiaire,avancée',
            'price' => 'numeric',
            'classe_id' => 'exists:classes,id',
            'discipline_id' => 'exists:disciplines,id',
            'background-image' => 'string',
            'teaser_url' => 'string',
            'courses'  => 'array',
            'courses.*' => 'exists:courses,id',


        ]);

        $pack = Pack::findOrFail($id);
        $pack->update($validatedData);

        // Sync the courses with the pack
        if (!empty($validatedData['courses'])) {
            $pack->courses()->sync($validatedData['courses']);
        }

        $pack = Pack::findOrFail($id)::with('courses')->get();

        return response()->json([
            'message' => 'Pack updated successfully',
            'pack' => $pack
        ], 200);
    }

    public function deletePack($id)
    {
        $pack = Pack::findOrFail($id);
        $pack->courses()->detach(); // Remove associations from course_pack table
        $pack->delete();

        return response()->json([
            'message' => 'Pack deleted successfully',
        ], 200);
    }

    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->packs()->detach(); // Remove associations from course_pack table
        $course->delete();


        return response()->json([
            'message' => 'Course deleted successfully',
        ], 200);
    }


    public function changePassword(Request $request)
    {

        $user = Auth::user();

        // Validate the request data
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        // Check if the current password matches the user's actual password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        // Update the user's password
        $user->password = Hash::make($request->new_password);
        $user->password_changed = true;
        $user->save();

        return response()->json([
            'change_password_required' => false,
            'message' => 'Password changed successfully'
        ]);
    }
    public function getDisciplines()
    {
        $disciplines = Discipline::with('classes')->get();

        return response()->json(
            $disciplines
        );
    }
}
