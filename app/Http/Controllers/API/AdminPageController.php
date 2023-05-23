<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\Pack;
use GuzzleHttp\Psr7\Message;
use Spatie\Permission\Models\Role;


class AdminPageController extends Controller
{
   
    

    public function dashbord(Request $request)
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

        return response()->json([
            'disciplines' => $disciplines,
        ]);
    }

    public function createDiscipline(Request $request)
    {
        // Create a new discipline
        $discipline = new Discipline();
        $discipline->titre = $request->input('titre');
        $discipline->discipline_description = $request->input('discipline_description');
        $discipline->background_img = $request->input('bakcground_img');

        $discipline->save();

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

        return response()->json($discipline);
    }





    public function updateDiscipline(Request $request, $id)
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

    public function getClasses()
    {
        $classes = Classe::all();
        return response()->json($classes);
    }



    public function createClass(Request $request, $disciplineId)
    {
        $request->validate([
            'titre' => 'required',
            'classe_description' => 'required',
            'background_img' => 'required'
        ]);
        $discipline = Discipline::findOrFail($disciplineId);

        $class = new Classe();
        $class->titre = $request->input('titre');
        $class->classe_description = $request->input('classe_description');
        $class->background_img = $request->input('background_img');
        $class->discipline()->associate($discipline);
        $class->save();

        return response()->json($class);
    }

    public function getDisciplineDetails($disciplineId)
    {
        $discipline = Discipline::withCount('classes')->findOrFail($disciplineId);

        return response()->json($discipline);
    }

    public function getInstructor(){
       
        $instructorRole = Role::where('name', 'instructor')->firstOrFail();
        $instructors = User::role($instructorRole)->get();

        return response()->json([
            'instructors' => $instructors,
        ]);
    }

    public function activateInstructor($id)
{
    $instructorRole = Role::where('name', 'instructor')->firstOrFail();
    $instructor= User::role($instructorRole)->findOrFail($id);
    $instructor->status = true;
    $instructor->save();

    return response()->json($instructor);
}

public function desactivateInstructor($id)
{
    $instructorRole = Role::where('name', 'instructor')->firstOrFail();
    $instructor= User::role($instructorRole)->findOrFail($id);
    $instructor->status = false;
    $instructor->save();

    return response()->json($instructor);
}
public function getCourses(){

    $courses = Course::where('state', '!=', 'en_attente')->get();
    return response()->json($courses);
}

public function getPendingCourses(){
     $courses = Course::where('state', '=', 'en_attente')->get();
     return response()->json($courses);
}

public function changeCourseState(Request $request,$courseId){
 
   $request->validate([
         'newState'=>'string,required',
   ]);
   $course =Course::findOrFail($courseId);
   $course->status=$request->input('newState');
   $course->save();
   return response()->json($course);
}

public function getPacks(){

    $packs = Pack::where('state', '!=', 'en_attente')->get();
    return response()->json($packs);
}

public function getPendingPacks(){
     $packs = Pack::where('state', '=', 'en_attente')->get();
     return response()->json($packs);
}

public function changePackState(Request $request,$packId){
 
   $request->validate([
         'newState'=>'string,required',
   ]);
   $pack =Pack::findOrFail($packId);
   $pack->status=$request->input('newState');
   $pack->save();
   return response()->json($pack);
}



}
