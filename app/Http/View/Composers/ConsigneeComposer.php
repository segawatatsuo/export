<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;

use Illuminate\Support\Facades\Auth;

use App\Model\Consignee;
use App\Model\Pic;

class ConsigneeComposer
{
    public function compose(View $view)
    {
        $user_id = Auth::id();

        if ($user_id == null) {
            $view->with('consignee_name', '');
        } else {

            $consn = Consignee::where('user_id', $user_id)->where('default_destination', '1')->first();
            $pic = Pic::where('user_id', $user_id)->where('default_destination', '1')->first();

            if (isset($consn->consignee)) {
                $consignee_name = $consn->consignee;
                $consignee_address_line1 = $consn->address_line1;
                $consignee_address_line2 = $consn->address_line2;
                $consignee_city = $consn->city;
                $consignee_state = $consn->state;
                $consignee_country = $consn->country_codes;
                $consignee_phone = $consn->phone;
                $consignee_zip = $consn->post_code;
                $consignee_person = $consn->name;

                $person_in_charge_name = $pic->name;
                $person_in_charge_email = $pic->email;
                $person_in_charge_country = $pic->country;
                $person_in_charge_company_name = $pic->company_name;

                $hash = array( 
                    'consignee_name' => $consignee_name, 
                    'consignee_address_line1' => $consignee_address_line1, 
                    'consignee_address_line2' => $consignee_address_line2 ,
                    'consignee_city' => $consignee_city,
                    'consignee_state' => $consignee_state,
                    'consignee_country' => $consignee_country,
                    'consignee_phone' => $consignee_phone,
                    'consignee_zip' => $consignee_zip,
                    'consignee_person' => $consignee_person,
                    'person_in_charge_name' => $person_in_charge_name,
                    'person_in_charge_email' => $person_in_charge_email,
                    'person_in_charge_country' => $person_in_charge_country,
                    'person_in_charge_company_name' => $person_in_charge_company_name,
                );
                $view->with( $hash);
            //一番最初にPerson in chargeで名前を登録しただけではまだconsigneeがないので
            } else {
                //$view->with('consignee_name', '');
                $consignee_name = "";
                $consignee_address_line1 = "";
                $consignee_address_line2 = "";
                $consignee_city = "";
                $consignee_state = "";
                $consignee_country ="";
                $consignee_phone = "";
                $consignee_zip = "";
                $consignee_person = "";
                $person_in_charge_name = "";
                $person_in_charge_email = "";
                $person_in_charge_country = "";
                $person_in_charge_company_name ="";

                $hash = array( 
                    'consignee_name' => $consignee_name, 
                    'consignee_address_line1' => $consignee_address_line1, 
                    'consignee_address_line2' => $consignee_address_line2 ,
                    'consignee_city' => $consignee_city,
                    'consignee_state' => $consignee_state,
                    'consignee_country' => $consignee_country,
                    'consignee_phone' => $consignee_phone,
                    'consignee_zip' => $consignee_zip,
                    'consignee_person' => $consignee_person,
                    'person_in_charge_name' => $person_in_charge_name,
                    'person_in_charge_email' => $person_in_charge_email,
                    'person_in_charge_country' => $person_in_charge_country,
                    'person_in_charge_company_name' => $person_in_charge_company_name,
                );
                $view->with( $hash);
            }
        }
    }
}
