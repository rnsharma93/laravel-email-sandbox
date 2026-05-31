<?php

namespace Ram\EmailSandbox\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthorizeEmailSandbox
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (app()->isLocal()) {
            return $next($request);
        }

        if (Auth::check() && Gate::allows('accessEmailSandbox')) {
            return $next($request);
        }

        abort(403);
    }
}
