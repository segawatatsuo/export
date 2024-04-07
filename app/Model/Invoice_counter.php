<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Invoice_counter extends Model
{
    protected $fillable = [
        'last_update',
        'today',
        'count',
    ];
}
