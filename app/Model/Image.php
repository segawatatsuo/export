<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['img_path','user_id','about','order_no']; //保存したいカラム名が複数の場合
}
