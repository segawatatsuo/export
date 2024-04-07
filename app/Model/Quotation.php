<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class Quotation extends Model
{
    protected $fillable = [
        'quotation_no',
        'customers_id',
        'date_of_issue',
        'consignee',
        'country',
        'company'
    ]; //保存したいカラム名が複数の場合

    //商品テーブルとリレーション
    public function Products()
    {
        return $this->hasMany('App\Model\Product');
    }

    //Userinformationテーブルとリレーション(Userinformationのuser_idとQuotationのconsignee_noでリレーション)
    public function Userinformations()
    {
        return $this->hasOne('App\Model\Userinformation', 'user_id', 'consignee_no');
    }

    //quotation_detailsテーブルとリレーション
    public function Quotation_details()
    {
        return $this->hasMany('App\Model\Quotation_detail');
    }

    //invoicesテーブルとリレーション
    public function invoices()
    {
        return $this->belongsTo('App\Model\Invoice','quotation_no');
    }

    //order_confirmsテーブルとリレーション
    public function order_confirms()
    {
        return $this->belongsTo('App\Model\Order_confirm','quotation_no');
    }


}
