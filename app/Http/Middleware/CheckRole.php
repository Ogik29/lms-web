<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response // memerlukan ... untuk menerima multiple role (array)
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, $roles)) {
            // Redirect ke halaman yang sesuai atau tampilkan error 403
            // return redirect('/login');
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
