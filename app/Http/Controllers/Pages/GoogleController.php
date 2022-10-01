<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\User;
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



            $user = User::where('google_id', $userSocial->id)->first();

            // validate email exit

            if(!empty($user)){
                // @todo
                // Login
                dd('Login to system');
            }else{
                // create new user
                User::create([
                    'name' => $userSocial->name,
                    'email' => $userSocial->email,
                    'password' => bcrypt(1),
                    'active_token' => Str::random(40),
                    'admin' => 0,
                    'google_id' => $userSocial->id,
                ]);

                dd('OK');

                // login to system
                // return redirect()->intended('dashboard');
            }

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
