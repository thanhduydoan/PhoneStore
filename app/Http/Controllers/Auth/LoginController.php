<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if(!$user->active) {
            auth()->logout();
            return back()->withInput()->with(['alert' => [
                'type' => 'warning',
                'title' => 'Tài khoản chưa được kích hoạt!',
                'content' => 'Hãy kiểm tra email để kích hoạt tài khoản.'
            ]]);
        } else if($user->admin) {
            return redirect()->route('admin.dashboard')->with(['alert' => [
                'type' => 'success',
                'title' => 'Đăng nhập thành công',
                'content' => 'Chào mừng bạn đến với trang quản trị website PhoneStore'
            ]]);
        } else {
            return redirect()->route('home_page')->with(['alert' => [
                'type' => 'success',
                'title' => 'Đăng nhập thành công',
                'content' => 'Chào mừng bạn đến với website PhoneStore của chúng tôi'
            ]]);
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()->withInput()->with('message', trans('auth.failed'));
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        return redirect()->route('home_page')->with(['alert' => [
            'type' => 'success',
            'title' => 'Đăng xuất thành công',
            'content' => 'Chúc bạn một ngày vui vẻ.'
        ]]);
    }
}
