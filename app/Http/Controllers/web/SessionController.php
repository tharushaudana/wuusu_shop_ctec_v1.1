<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\ApiSession;
use App\Models\WebSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index()
    {
        return view('dashboard.pages.sessions', [
            'websessions' => Auth::user()->webSessions(),
            'apisessions' => Auth::user()->apiSessions(),
        ]);
    }

    public function destroyWebSession(WebSession $session)
    {
        $session->delete();

        return redirect()->back()->with('success', 'Selected session has been terminated.');
    }

    public function destroyApiSession(ApiSession $session)
    {
        $session->token()->delete();

        return redirect()->back()->with('success', 'Selected session has been terminated.');
    }
}
