<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetUserPrivilegesRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index() 
    {
        return response()->success(['users' => User::where('id', '!=', Auth::user()->id)->get()]);
    }

    public function show(User $user) 
    {
        return response()->success(['user' => $user]);
    }

    public function showPrivileges(User $user) 
    {
        return response()->success(['privileges' => $user->privileges()->get()]);
    }

    public function setPrivileges(SetUserPrivilegesRequest $request, User $user) 
    {
        if ($user->id == Auth::user()->id) return response()->error('You cannot set privileges to your self account!', 401);

        $user->privileges()->set($request->privileges);

        return response()->success();
    }

    public function destroy(User $user) 
    {
        $user->delete();

        return response()->success(null, 'User has been deleted.');
    }
}
