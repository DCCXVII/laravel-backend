<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;



class AuthController extends BaseController
{


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('auth_token')->plainTextToken;
        $success['name']  =  $user->getAttribute('name');
        $success['email'] =  $user->getAttribute('email');
        $role = Role::findByName('client');
        $user->assignRole($role);
        /*  $user->sendEmailVerificationNotification(); */


        return $this->sendResponse($success, 'User created successfully.');
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('auth_token')->plainTextToken;

            $success['name'] =  $user->name;
            $success['email'] = $user->email;
            $success['image'] = $user->img_url;
            $success['role'] = $user->roles->first()->name;

            return $this->sendResponse($success, 'User signed in');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
        throw ValidationException::withMessages([
            'email' => 'Invalid credentials',
        ]);
    }
}
