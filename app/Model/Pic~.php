<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Pic extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'country',
        'company_name',
        'default_destination',
    ];

    public function user() //関数名は単数形がベスト
    {
     return $this->belongsTo('App\Model\User');
    }

    public function consignee() 
    {
        return $this->hasOne('App\Model\Consignee');
    }

}
