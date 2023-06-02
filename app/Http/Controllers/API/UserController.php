<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{

    public function getProfile()
    {
        $user = Auth::user();

        // Return the user profile data
        return response()->json($user);
    }


    public function editProfile(Request $request)
    {
        $user = Auth::user();

        // Validate the request data



        $user = User::find(auth()->user()->id);


        if ($request->name) {
            $user->name = $request->name;
        }

        if ($request->hasFile('profile_image')) {
            // Get the uploaded file from the request
            $file = $request->file('profile_image');

            // Generate a unique file name
            $fileName = time() . '.' . $file->getClientOriginalExtension();

            // Specify the directory where the file should be stored
            $directory = public_path('images');
            // Move the uploaded file to the specified directory
            $request->profile_image->move(public_path('images'), $fileName);


            // Construct the full URL of the uploaded file
            $imageUrl = url('/images/' . $fileName);

            // Store the image URL in the database (assuming you have a 'users' table)

            $user->img_url = $imageUrl;
        }

        if ($request->has('description')) {
            $user->description = $request->description;
        }

        if ($request->hasFile('background_image')) {
            // Get the uploaded file from the request
            $file = $request->file('background_image');
            // Generate a unique file name
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            // Specify the directory where the file should be stored
            $request->background_image->move(public_path('images'), $fileName);
            // Move the uploaded file to the specified directory


            // Construct the full URL of the uploaded file
            $imageUrl = url('/images/' . $fileName);
            // Store the image URL in the database (assuming you have a 'users' table)

            $user->background_img = $imageUrl;
        }


        // Save the changes
        $user->save();

        // Return a success response
        return response()->json(['message' => 'Profile updated successfully']);
    }


    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // Validate the request
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        // Check if the current password matches the user's password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 401);
        }

        // Update the user's password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }


    public function becomeInstructor(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'cv' => 'required|mimes:pdf',
        ]);

        if ($validator->fails()) {
            // Handle validation errors
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the authenticated user
        $user = auth()->user();

        // Update the user's information
        $application = new Application();



        // Save the user's updated information


        // Handle the uploaded CV file
        if ($request->hasFile('cv')) {
            $application->phone_number = $request->phone_number;
            $application->user_id = $user->id;

            $cv = $request->file('cv');

            // Generate a unique file name
            $fileName = time() . '.' . $cv->getClientOriginalExtension();

            // Move the CV file to a directory
            $cv->move(public_path('cv'), $fileName);

            // Save the CV file path in the user's record
            $application->cv_path =  url('/cv/' . $fileName);

            // Save the user's updated information again
            $application->save();
        }

        // Return a success response
        return response()->json(['message' => 'Request to become an instructor submitted successfully']);
    }
}
