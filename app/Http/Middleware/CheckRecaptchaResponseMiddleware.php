<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class CheckRecaptchaResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $request->input('g-recaptcha-response');

        if (is_null($response)) {
            return response()->error('Recaptcha response is required!', 400);
        }

        $http_response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $response,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ]);

        if (!$http_response->json('success')) {
            return response()->error('Recaptcha cheking failed!', 400);
        }

        if ($http_response->json('score') < 0.5) {
            return response()->error('Recaptcha score is very low!', 400);
        }

        return $next($request);
    }
}
