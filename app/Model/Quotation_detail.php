<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Quotation_detail extends Model
{
    //

    public function Quotation()
    {
        return $this->belongsTo('App\Model\Quotation');
    }
}
