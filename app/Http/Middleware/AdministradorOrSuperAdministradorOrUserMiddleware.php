<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdministradorOrSuperAdministradorOrUserMiddleware
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

        if (Auth::check() && (Auth::user()->role === 'ADMINISTRATOR' || Auth::user()->role === 'SUPER_ADMINISTRATOR' || Auth::user()->role === 'USUARIO_CAJA')) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
