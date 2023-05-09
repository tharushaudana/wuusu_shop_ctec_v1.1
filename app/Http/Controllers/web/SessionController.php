<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\ApiSession;
use App\Models\WebSession;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index()
    {
        return response()->success([
            'websessions' => Auth::user()->webSessions(),
            'apisessions' => Auth::user()->apiSessions(),
        ]);
    }

    public function destroyWebSession(WebSession $session)
    {
        $session->delete();

        return response()->success(null, 'Selected Web session has been terminated.');
    }

    public function destroyApiSession(ApiSession $session)
    {
        $session->token()->delete();

        return response()->success(null, 'Selected API session has been terminated.');
    }
}
