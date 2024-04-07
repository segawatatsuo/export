<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array 代入可能
     */
    protected $fillable = [
        'name', 'email', 'password','consignee','address_line1','address_line2','city','state','country','country_codes','zip','phone','person','person_gender','bill_company_address_line1','bill_company_address_line2','bill_company_city','bill_company_state','bill_company_country','bill_company_zip','bill_company_phone','president','president_gender','website','industry','business_items','customer_name','fax','fedex','sns','Administrator',
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
}
