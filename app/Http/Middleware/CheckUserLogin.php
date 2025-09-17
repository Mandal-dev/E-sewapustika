<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckUserLogin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if custom session 'user' exists
        if (!Session::has('user')) {
            // Redirect to login page if not logged in
            return redirect()->route('login.page')
                ->with('error', 'You must be logged in to access this page.');
        }

        // If logged in, continue request
        return $next($request);
    }
}
