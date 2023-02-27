<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\baseController as Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class authController extends Controller {

    // Register 
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required:same:password'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return $this->sendSuccess('User Created Successfully', $user);

    }

    // Login
    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->sendError('Invalid Email Or Password', [], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('login_token')->plainTextToken;

        return $this->sendSuccess('Login Successfully', ['token' => $token, 'user' => $user]);

    }

    // Logout
    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return $this->sendSuccess('Successfully Logged Out');
    }

}
