<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order_detail extends Model
{
    public function Order()
    {
        return $this->belongsTo("App\Model\Order");
    }

    public function Products()
    {
        return $this->hasMany("App\Model\Product", 'product_code');
    }
}
