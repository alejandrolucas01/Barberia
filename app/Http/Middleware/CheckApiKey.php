<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = env('PERSONAL_API_KEY');
        if ($apiKey && $request->header('X-API-KEY') !== $apiKey) {
            return response()->json(['error' => 'Unauthorized. Invalid API Key.'], 401);
        }

        return $next($request);
    }
}
