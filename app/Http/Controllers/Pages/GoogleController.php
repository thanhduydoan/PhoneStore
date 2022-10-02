<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function getGoogleSignInUrl()
    {
        return Socialite::driver('google')->redirect();
    }

    public function loginCallback()
    {
        try {
            $userSocial = Socialite::driver('google')->user();

            $userByEmail = User::where('email', $userSocial->email)->whereNull('google_id')->first();
            if (!empty($userByEmail)) {
                return redirect()->route('register')->with('message', 'Email đã tồn tại trên hệ thống');
            }

            $user = User::where('google_id', $userSocial->id)->first();

            if (empty($user)) {
                User::create([
                    'name' => $userSocial->name,
                    'email' => $userSocial->email,
                    'password' => bcrypt(1),
                    'active_token' => Str::random(40),
                    'admin' => 0,
                    'google_id' => $userSocial->id,
                ]);
                $user = User::latest()->first();
            }

            auth()->login($user);
            return redirect()->route('home_page')->with(['alert' => [
                'type' => 'success',
                'title' => 'Đăng nhập thành công',
                'content' => 'Chào mừng bạn đến với website PhoneStore của chúng tôi'
            ]]);
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->route('register')->with('message', 'Lỗi hệ thống');
        }
    }
}
