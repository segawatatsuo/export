<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ImageList extends Model
{
    protected $fillable = [
        'product_id',
        'name'
    ];
    /*リレーション*/
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
