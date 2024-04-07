<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order_detail_confirm extends Model
{
    public function Order_confirm()
    {
        return $this->belongsTo("App\Model\Order_confirm");
    }

    public function Products()
    {
        return $this->hasMany("App\Model\Product", 'product_code');
    }
}
