<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'address_line1' => 'required',
            'address_line2' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zip' => 'required',
            'phone' => 'required',
            'email' => 'required',
            /*
            'president' => 'required',
            'company_name' => 'required',
            'bill_company_address_line1' => 'required',
            'bill_company_address_line2' => 'required',
            'bill_company_city' => 'required',
            'bill_company_state' => 'required',
            'bill_company_country' => 'required',
            'bill_company_zip' => 'required',
            'bill_company_phone' => 'required',
            */
          ];
    }
}
