<?php

namespace App;

use App\Model\Codes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ['userType', 'userLevel'];

    /**
     * Specify Slack Webhook URL to route notifications to
     *
     * @return void
     */
    public function routeNotificationForSlack()
    {
        return env('SLACK_WEBHOOK_URL');
    }

    /**
     * 사용자 타입 코드 가지고 오기.
     *
     * @return void
     */
    public function userType()
    {
		return $this->hasOne(Codes::class, 'code_id', 'user_type');
    }

    /**
     * 사용자 레벨 타입 코드 가지고 오기.
     *
     * @return void
     */
    public function userLevel()
    {
		return $this->hasOne(Codes::class, 'code_id', 'user_level');
    }
}
