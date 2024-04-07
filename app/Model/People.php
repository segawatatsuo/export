<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    protected $fillable = [
        'name',
        'section',
        'email',
        'memo'
    ]; //保存したいカラム名が複数の場合
}
