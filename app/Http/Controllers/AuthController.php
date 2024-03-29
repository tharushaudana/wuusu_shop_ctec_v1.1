<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginUserRequest $request) {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->error('Credentials not match!', 401);
        }

        $user = User::where('email', $request->email)->first();

        return response()->success([
            'user' => $user,
            'token' => $this->createNewUserToken($user)
        ]);
    }

    public function register(RegisterUserRequest $request) {
        $request->validated($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->success([
            'user' => $user,
            'token' => $this->createNewUserToken($user)
        ]);
    }

    public function logout() {
        Auth::user()->currentAccessToken()->delete();

        return response()->success(null, 'You have successfully logged out.');
    }

    private function createNewUserToken($user) {
        return $user->createToken('Api Token of '.$user->name)->plainTextToken;
    }
}
