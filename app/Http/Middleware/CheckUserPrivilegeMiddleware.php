<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPrivilegeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!in_array($this->getPrivilegeId($role), Auth::user()->privileges)) {
            if ($request->is('api/*')) {
                return response()->error('Permission denided!', 401);
            } else {
                abort(403);
            }
        }
        
        return $next($request);
    }

    private function getPrivilegeId($privilegeName) {
        foreach (config('userprivis') as $title) {
            if (isset($title[$privilegeName])) {
                return $title[$privilegeName];
            }
        }

        return null;
    }
}
