<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
	public function handle($request, Closure $next, $guard = null)
	{
			switch ($guard) {
			case 'admin' :
				if (Auth::guard($guard)->check()) {
					return redirect()->route('admin.dashboard');
				}
				break;
            case 'email_users' :
                    if (Auth::guard($guard)->check()) {
                        return redirect()->route('email_users.dashboard');
                    }
                    break;
				default:
					if (Auth::guard($guard)->check()) {
						return redirect()->route('dashboard.index');
					}
					break;
			}
		 return $next($request);
	}
}
