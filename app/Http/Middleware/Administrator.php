<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Administrator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica si el usuario está autenticado y tiene el rol de ADMINISTRATOR
        if (Auth::check() && Auth::user()->role === 'ADMINISTRATOR') {
            return $next($request);
        }

        // Redirigir o devolver un error si el usuario no tiene el rol
        return redirect('/home')->with('error', 'No tienes acceso a esta página.');
    }
}
