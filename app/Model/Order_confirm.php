<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order_confirm extends Model
{
    protected $fillable = [
        'consignee',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'zip',
        'phone',
        'regist_date',
        'trading_term',
        'trading_history',
        'trading_rank',
        'gender',
        'person',
        'bill_company_address',
        'president',
        'website',
        'industry',
        'business_items',
        'customer_name',
        'email',
        'fax',
        'fedex',
        'sns'
    ]; //保存したいカラム名が複数の場合

    public function order_details_confirm()
    {
        return $this->hasMany("App\Model\Order_detail_confirm");
    }

    
    //主キーquotation_no、インクリメントしない、文字型
    protected $primaryKey = 'quotation_no';
    public $incrementing = false;
    protected $keyType = 'string';

    //invoicesテーブルとリレーション
    public function invoices()
    {
        return $this->hasOne('App\Model\Invoice', 'quotation_no');
    }
}
