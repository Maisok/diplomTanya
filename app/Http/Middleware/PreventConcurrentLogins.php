<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventConcurrentLogins
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            // Если пользователь уже авторизован в другом guard'е
            if ($guard === 'staff' && Auth::guard('web')->check()) {
                Auth::guard('web')->logout();
                return redirect()->route('staff.login');
            }

            if ($guard === 'web' && Auth::guard('staff')->check()) {
                Auth::guard('staff')->logout();
                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}