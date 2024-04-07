<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Limit extends Model
{
    protected $fillable = ['Delivery_type','lower_limit','upper_limit','Minimum_orders'];
}
