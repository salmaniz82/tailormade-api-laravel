<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        /*
        dd(auth()->user()->role);
         if (!Auth::check() || Auth::user()->role !== 'admin' ) {
            return redirect()->route('login');
        }
        */

        if (!auth()->user()->role || auth()->user()->role->role !== 'admin') {
            return redirect()->route('login'); // or abort(403);
        }

        return $next($request);
    }
}
