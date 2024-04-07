<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Userinformation extends Model
{
    protected $fillable = [
        'user_id',
        'consignee',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'country_codes',
        'zip',
        'phone',
        'person',
        'bill_company_address_line1',
        'bill_company_address_line2',
        'bill_company_city',
        'bill_company_state',
        'bill_company_country',
        'bill_company_zip',
        'bill_company_phone',
        'president',
        'industry',
        'business_items',
        'customer_name',
        'fax',
        'fedex',
        'sns',
        'trading_term',
        'trading_history',
        'trading_rank',
        'importer_name',
        'user_id',
        'initial',
        'website'
    ];

    public function Quotation()
    {
        return $this->belongsTo('App\Model\Quotation');
    }
}
