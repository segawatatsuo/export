<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    protected $fillable = [
        'tts',
        'ttb',
        'memo'
    ]; //保存したいカラム名が複数の場合
}
