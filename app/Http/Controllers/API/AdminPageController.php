<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Classe;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\Pack;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\InstructorApplicationAccepted;



class AdminPageController extends Controller
{



    public function dashbord()
    {
        //total des client
        $clientRole = Role::where('name', 'client')->firstOrFail();
        $totalClients = $clientRole->users->count();

        //total des instructeur
        $instructorRole = Role::where('name', 'instructor')->firstOrFail();
        $totalInstructors = $instructorRole->users->count();

        // Retrieve the total number of courses
        $totalCourses = Course::count();

        // Retrieve the number of pending courses to be approved
        $pendingCourses = Course::where('status', 'en_attente')->count();

        // Retrieve the total number of course packs
        $totalPacks = Pack::count();

        // Retrieve the number of pending Packs to be approved
        $pendingPacks = Pack::where('status', 'en_attente')->count();

        // get the total'course number of views 
        $totalViewCourses = Course::sum('views_number');

        // get the total'packs number of views 
        $totalViewPacks = Pack::sum('views_number');

        //get Discipline 
        $totalDiscipline = Discipline::count();

        //get Classes 
        $totalClasses = Classe::count();

        $totalViews = $totalViewCourses + $totalViewPacks;



        return response()->json([
            'total_Clients' => $totalClients,
            'total_Instructors' => $totalInstructors,
            'total_Course' => $totalCourses,
            'total_Packs'  => $totalPacks,
            'total_discipline' => $totalDiscipline,
            'total_classes' => $totalClasses,
            'pending_courses' => $pendingCourses,
            'pending_packs'   => $pendingPacks,
            'total_courses_view' => $totalViewCourses,
            'total_packs_view'  => $totalViewPacks,
            'total_view' => $totalViews,


        ]);
    }

    public function getDisciplines()
    {
        $disciplines = Discipline::with('classes')->get();

        return response()->json(
            $disciplines
        );
    }

    public function createDiscipline(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'discipline_description' => 'required|string|max:255',
            'background_img' => 'required|image|mimes:jpeg,png,jpg,gif'

        ]);
        // Create a new discipline

        if ($validator->fails()) {
            // Handle validation errors
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $discipline = new Discipline();
        $discipline->titre = $request->titre;
        $discipline->discipline_description = $request->discipline_description;

        if ($request->hasFile('background_img')) {
            // Get the uploaded file from the request
            $file = $request->file('background_img');

            // Generate a unique file name
            $fileName = time() . '.' . $file->getClientOriginalExtension();


            $request->background_img->move(public_path('images'), $fileName);;

            // Construct the full URL of the uploaded file
            $imageUrl = url('/images/' . $fileName);

            // Store the image URL in the database (assuming you have a 'users' table)

            $discipline->background_img = $imageUrl;
        }

        $discipline->save();
        return response()->json($discipline);
    }

    /*
            // Check if classes are provided
            if ($request->has('classes')) {
                $classesData = $request->input('classes');

                // Create classes associated with the discipline
                foreach ($classesData as $classData) {
                    $class = new Classe();
                    $class->titre =$classData['titre'];
                    $class->classe_description = $classData['classe_description'];
                    $class->background_img =$classData['background_img'];
                    $class->discipline()->associate($discipline);
                    $class->save();
                }*/




    public function editeDiscipline(Request $request, $id)
    {
        // Find the discipline by ID
        $discipline = Discipline::findOrFail($id);

        // Update the discipline properties
        $discipline->titre = $request->input('titre');
        $discipline->discipline_description = $request->input('discipline_description');
        $discipline->background_img = $request->input('background_img');

        $discipline->save();

        return response()->json($discipline);
    }





    /* public function updateDiscipline(Request $request, $id)
    {
        $data = $request->validate([
            'titre',
            'discipline_description',
            'background_img'
        ]);

        $discipline = Discipline::findOrFail($id);
        $discipline->update($data);

        return response()->json($discipline);
    }

    public function deleteDiscipline($id)
    {
        $discipline = Discipline::findOrFail($id);
        $discipline->delete();

        return response()->json(null, 204);
    }
*/

    public function deleteDiscipline($id)
    {
        $discipline = Discipline::findOrFail($id);

        // Delete all the courses associated with the discipline
        $discipline->courses()->delete();

        // Delete all the classes associated with the discipline
        $discipline->classes()->delete();

        // Delete the discipline
        $discipline->delete();

        return response()->json(['message' => 'Discipline and associated courses and classes deleted successfully']);
    }

    public function getClasses()
    {
        $classes = Classe::all();
        return response()->json($classes);
    }



    public function createClasse(Request $request, $id)
    {
        $request->validate([
            'titre' => 'required',
            'classe_description' => 'required',
            'background_img' => 'required'
        ]);
        $discipline = Discipline::findOrFail($id);

        $class = new Classe();
        $class->titre = $request->input('titre');
        $class->classe_description = $request->input('classe_description');
        $class->background_img = $request->input('background_img');
        $class->discipline()->associate($discipline);
        $class->save();

        return response()->json($class);
    }

    public function editClasse(Request $request, $id)
    {
        // Find the discipline by ID
        $classe = Classe::findOrFail($id);

        // Update the discipline properties
        $classe->titre = $request->input('titre');
        $classe->discipline_description = $request->input('discipline_description');
        $classe->background_img = $request->input('background_img');

        $classe->update();

        return response()->json($classe);
    }
    public function deleteClasse($id)
    {
        $classe = Classe::findOrFail($id);

        // Delete all the courses associated with the discipline
        $classe->courses()->delete();


        // Delete the discipline
        $classe->delete();

        return response()->json(['message' => 'classes and associated courses successfully']);
    }
    public function getCourseAndPackCounts($instructorId)
    {
        $courseCount = Course::where('instructor_id', $instructorId)->count();
        $packCount = Pack::where('instructor_id', $instructorId)->count();

        return response()->json([
            'course_count' => $courseCount,
            'pack_count' => $packCount
        ]);
    }


    /* public function getDisciplineDetails($disciplineId)
        {
            $discipline = Discipline::withCount('classes')->findOrFail($disciplineId);

            return response()->json($discipline);
        }
    */
    public function getInstructor()
    {

        $instructorRole = Role::where('name', 'instructor')->firstOrFail();
        $instructor = User::role($instructorRole)->get();;
        $courseCount = Course::where('instructor_id', $instructor->id)->count();
        $packCount = Pack::where('instructor_id', $instructor->id)->count();
<

        return response()->json([
            'instructors' => $instructor,
         ]);
    }

    public function acceptInstructor($id)
    {
        $instructorRole = Role::where('name', 'instructor')->firstOrFail();
        $instructor = User::role($instructorRole)->findOrFail($id);
        $instructor->status = true;
        $instructor->save();

        return response()->json($instructor);
    }

    public function desactivateInstructor($id)
    {
        $instructorRole = Role::where('name', 'instructor')->firstOrFail();
        $instructor = User::role($instructorRole)->findOrFail($id);
        $instructor->status = false;
        $instructor->save();

        return response()->json($instructor);
    }
    public function getCourses()
    {

        $courses = Course::where('state', '!=', 'en_attente')->get();
        return response()->json($courses);
    }

    public function getPendingCourses()
    {
        $courses = Course::where('state', '=', 'en_attente')->get();
        return response()->json($courses);
    }

    public function changeCourseState(Request $request, $courseId)
    {

        $request->validate([
            'newState' => 'string,required',
        ]);
        $course = Course::findOrFail($courseId);
        $course->status = $request->input('newState');
        $course->save();
        return response()->json($course);
    }

    public function getPacks()
    {

        $packs = Pack::where('status', '!=', 'en_attente')->get();
        return response()->json($packs);
    }

    public function getPendingPacks()
    {
        $packs = Pack::where('status', '=', 'en_attente')->get();
        return response()->json($packs);
    }

    public function changePackState(Request $request, $packId)
    {

        $request->validate([
            'newState' => 'string,required',
        ]);
        $pack = Pack::findOrFail($packId);
        $pack->status = $request->input('newState');
        $pack->save();
        return response()->json($pack);
    }

    public function approveCourese($id)
    {
        $course = Course::findOrFail($id);
        $course->status = 'accepté';
        $course->save();

        return response()->json(['message' => 'Course approved successfully']);
    }

    public function refuseCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->status = 'refusé';
        $course->save();

        return response()->json(['message' => 'Course refused successfully']);
    }

    public function approvePack($id)
    {
        $pack = Pack::findOrFail($id);
        $pack->status = 'accepté';
        $pack->save();

        return response()->json(['message' => 'Pack approved successfully']);
    }

    public function refusePack($id)
    {
        $pack = Pack::findOrFail($id);
        $pack->status = 'refusé';
        $pack->save();

        //add notification syst

        return response()->json(['message' => 'Pack refused successfully']);
    }

    public function getAllApplications()
    {
        $applications = Application::join('users', 'users.id', '=', 'applications.user_id')
            ->select('users.name', 'users.email', 'applications.*')
            ->get();

        return response()->json($applications);
    }

    public function acceptInstructorApplication($id)
    {
        // Find the application
        $application = Application::findOrFail($id);

        // Get the associated user
        $user = User::findOrFail($application->user_id);

        // Update user role to "instructor" (assuming you have a "roles" column in the "users" table)
        $role = Role::findByName('instructor');
        $user->assignRole($role);


        // Delete the application
        $application->delete();

        // Send email notification to the user
        Mail::to($user->email)->send(new InstructorApplicationAccepted($user));

        // Return a response or redirect as needed
        return response()->json(['message' => 'Instructor application accepted']);
    }

    public function refuseInstructorApplication($id)
    {
        $application = Application::findOrFail($id);
        $application->delete();
    }
}
