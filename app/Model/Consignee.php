<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Consignee extends Model
{
    protected $guarded = [
        'id',
    ];

    public function user() //関数名は単数形がベスト
    {
     return $this->belongsTo('App\Model\User');
    }

    public function pic() //関数名は単数形がベスト
    {
     return $this->belongsTo('App\Model\Pic');
    }

}
