<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'size:10', 'regex:/^0[^6421][0-9]{8}$/', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'active_token' => str::random(40),
        ]);
    }

    public function activation($token) {
        $user = User::where('active_token', $token)->first();
        if(isset($user)) {
            if(!$user->active) {
                $user->active = 1;
                $user->save();
                return redirect()->route('home_page')->with(['alert' => [
                    'type' => 'success',
                    'title' => 'Kích hoạt tài khoản thành công',
                    'content' => 'Chúc mừng bạn đã kích hoạt tài khoản thành công. Bạn có thể đẳng nhập ngay bây giờ.'
                ]]);
            }
            else {
                return redirect()->route('home_page')->with(['alert' => [
                    'type' => 'warning',
                    'title' => 'Tài khoản đã được kích hoạt',
                    'content' => 'Tài khoản đã được kích hoạt từ trước. Bạn có thể đẳng nhập ngày bây giờ.'
                ]]);
            }
        } else {
            return redirect()->route('home_page')->with(['alert' => [
                'type' => 'error',
                'title' => 'Kích hoạt tài khoản không thành công',
                'content' => 'Mã kích hoạt không đúng. vui lòng kiểm tra lại email đăng ký!'
            ]]);
        }
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        $user->sendActiveAccountNotification($user->active_token);

        $this->guard()->logout();

        return redirect()->route('home_page')->with(['alert' => [
            'type' => 'success',
            'title' => 'Đăng ký tài khoản thành công',
            'content' => 'Vui lòng kiểm tra email đăng ký để kích hoạt tài khoản.'
        ]]);
    }
}
