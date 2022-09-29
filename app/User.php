<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Auth\Passwords\CanResetPassword;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\ActiveAccountNotification;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'password', 'active_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'active_token',
    ];

    /**
     * Relationships
    **/

    public function comments() {
        return $this->hasMany('App\Models\Comment');
    }
    public function notices() {
        return $this->hasMany('App\Models\Notice');
    }
    public function orders() {
        return $this->hasMany('App\Models\Order');
    }
    public function posts() {
        return $this->hasMany('App\Models\Post');
    }
    public function product_votes() {
        return $this->hasMany('App\Models\ProductVote');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the active account notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendActiveAccountNotification($token)
    {
        $this->notify(new ActiveAccountNotification($token));
    }
}
