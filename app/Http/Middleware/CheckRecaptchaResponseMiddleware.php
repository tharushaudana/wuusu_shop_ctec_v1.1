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
            return redirect()->back()->withErrors(['g-recaptcha-response' => 'Recaptcha response is required.']);
        }

        $http_response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $response,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ]);

        if (!$http_response->json('success')) {
            return redirect()->back()->withErrors(['recaptcha_v3' => 'Recaptcha cheking failed.']);
        }

        if ($http_response->json('score') < 0.5) {
            return redirect()->back()->withErrors(['recaptcha_v3' => 'Recaptcha score is very low.']);
        }

        return $next($request);
    }
}
