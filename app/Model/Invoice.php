<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

use App\Model\Quotation;
use App\Model\Order_confirm;

class Invoice extends Model
{
    protected $fillable = [
        'quotation_no',
        'customers_id',
        'date_of_issue',
        'consignee',
        'country',
        'company'
    ]; //保存したいカラム名が複数の場合

    //主キーquotation_no、インクリメントしない、文字型
    protected $primaryKey = 'quotation_no';
    public $incrementing = false;
    protected $keyType = 'string';

    //quotaions(子)テーブルとリレーション
    public function quotations()
    {
        return $this->hasOne('App\Model\Quotation','quotation_no');
    }
    //order_confirms(孫)テーブルとリレーション
    public function order_confirms()
    {
        return $this->hasOne('App\Model\Order_confirm', 'quotation_no');
    }

}
