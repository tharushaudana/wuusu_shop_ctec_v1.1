<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginUserRequest $request) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->error('Credentials not match!', 400);
        } else {
            return response()->success();
        }
    }

    public function register(RegisterUserRequest $request) {

    }

    public function logout() {
        Auth::logout();
        return response()->success();
    }
}
