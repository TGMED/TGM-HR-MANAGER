<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserClocksIn
{
    /**
     * Super admins run the clock, they do not punch it. Guard the routes that
     * only make sense for staff who actually work a shift.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->clocksIn() === false) {
            if ($request->isMethod('GET')) {
                return redirect()->route('dashboard');
            }

            abort(403, 'Administrators do not clock in.');
        }

        return $next($request);
    }
}
