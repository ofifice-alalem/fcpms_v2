<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->hasRole('consultant') && !$user->hasRole('admin') && !$user->hasRole('hr')) {
            abort(403, 'غير مصرح لك بالدخول لهذه البوابة الإدارية.');
        }
        return $next($request);
    }
}
