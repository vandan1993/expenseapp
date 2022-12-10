<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
     use HttpResponse;

     public function login(Request $request)
     {
        Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ])->stopOnFirstFailure()->validate();

        if(!Auth::attempt($request->only(['email' , 'password']))){
            return $this->error('', 'Credentionals do not match' ,401);
        }

        $user = User::where('email' , $request->email)->first();
        $user->tokens()->delete();

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Token for user ' . $user->name)->plainTextToken
        ] ,  "Successfully Login");
     }

     public function registerUser(Request $request)
     {
         Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users|email',
            'number' => 'required|unique:users|digits_between:10,12',
            'password' => 'required|string|min:6'
        ])->stopOnFirstFailure()->validate();

        $data = $request->all();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'number' => $data['number'],
            'password' => Hash::make($data['password'])
        ]);

        return $this->success([
            'user' => $user ,
            'token' => $user->createToken('Token for user ' . $user->name)->plainTextToken
        ] , "Successfully Register");

     }


     public function logout()
     {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([], 'Logout Successfully');
     }
     
}
