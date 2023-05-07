<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\SetUserPrivilegesRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->success([
            'users' => User::where('id', '!=', Auth::user()->id)->get()
        ]);
    }

    public function store(RegisterUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->success(null, $user->name.' has been registered.');
    }

    public function show(User $user) 
    {
        if ($user->id == Auth::user()->id) {
            return redirect(route('web.pages.profile'));
        } else {
            return response()->success([
                'user' => $user,
                'privileges' => $user->privileges()->get()
            ]);
        }
    }

    public function setPrivileges(SetUserPrivilegesRequest $request, User $user) 
    {
        if ($user->id == Auth::user()->id) abort(403);

        $user->privileges()->set($request->privileges);

        return response()->success(null, 'Privileges are updated successfully.');
    }

    public function destroy(User $user)
    {

    }
}
