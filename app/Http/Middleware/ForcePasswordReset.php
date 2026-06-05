<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordReset
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->needs_password_reset) {
            if (!$request->is('settings/password') && !$request->is('livewire/*') && !$request->is('logout')) {
                return redirect()->route('user-password.edit');
            }
        }

        return $next($request);
    }
}
