<?php

namespace App\Model;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array 代入可能
     */
    protected $fillable = [
        'name', 'email', 'password', 'country', 'company_name','group',
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

    //1対1リレーション
    //親側に子の名前のメソッドを作る
    public function Userinformations()
    {
        return $this->hasOne('App\Model\Userinformation');
    }

    public function consignees() //関数名は複数形がベスト
    {
        return $this->hasMany('App\Model\Consignee');
    }
    public function pics() //関数名は複数形がベスト
    {
        return $this->hasMany('App\Model\Pic');
    }

}
