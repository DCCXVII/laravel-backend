<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class InstructorPageController extends Controller
{
    //

    public function dashbord(Request $request)
    {
        return response()->json([
            'message' => 'sing up successfully'
        ]);
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
}
