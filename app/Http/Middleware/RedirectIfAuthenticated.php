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
        if (Auth::guard($guard)->check()) {
            return redirect()->route('home_page')->with(['alert' => [
                'type' => 'warning',
                'title' => 'Từ chối truy cập!',
                'content' => 'Bạn không có quyền truy cập vào trang này.'
            ]]);
        }

        return $next($request);
    }
}
