<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIsNullMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (is_null($request->route($role))) {
            if ($request->is('api/*')) {
                return response()->error('Not found!', 404);
            } else {
                abort(404);
            }
        }
        
        return $next($request);
    }
}
