<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!Auth::check()) {
            return redirect()->route('login')->with(['alert' => [
                'type' => 'warning',
                'title' => 'Từ chối truy cập!',
                'content' => 'Bạn không có quyền truy cập. Hãy đăng nhập tài khoản Admin để truy cập trang này.'
            ]]);
        } else if(!Auth::user()->admin) {
            return redirect()->route('home_page')->with(['alert' => [
                'type' => 'warning',
                'title' => 'Từ chối truy cập!',
                'content' => 'Tài khoản của bạn không có quyền truy cập. Trang này chỉ dành cho tài khoản Admin.'
            ]]);
        } else {
            return $next($request);
        }
    }
}
