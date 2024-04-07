<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Etd extends Model
{
    protected $fillable = [
        'fedex',
        'air',
        'ship'
    ];
    
}
