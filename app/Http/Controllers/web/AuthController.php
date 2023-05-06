<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login_Get() {
        return view('dashboard.login');
    }

    public function login_Post(LoginUserRequest $request) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return view('dashboard.login')->with('error', 'Login details are incorrect!');
        } else {
            $redirect_to = $request->query('redirect_to');

            if (!is_null($redirect_to)) {
                return redirect(url($redirect_to));
            } else {
                return redirect(route('web.pages.home'));
            }
        }
    }

    public function register_Get() {

    }

    public function register_Post(RegisterUserRequest $request) {

    }

    public function logout() {
        Auth::logout();
        return redirect(route('web.login'));
    }
}
