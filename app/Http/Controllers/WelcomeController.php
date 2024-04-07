<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Model\User;
use App\Model\Product;
use App\Model\Preference;
use App\Model\Quotation;
use App\Model\Quotation_detail;
use App\Model\Payment_method;
use App\Model\AdminMail;
use Carbon\Carbon;

use Illuminate\Support\Str;

use App\Model\Userinformation; //Userinformationモデル

class WelcomeController extends Controller
{
    public function index()
    {
        //AdminMail
        $adminmails=AdminMail::where('id',1)->get();
        $adminmail=$adminmails[0]['mail'];
        session()->put('adminmail',$adminmail);
        //dd(session('adminmail'));

        //$type;
        //為替 Preferenceテーブルの1レコード目(tts,ttb)にあるので使う際に$exchange->tts
        $exchange = Preference::first();

        //AIRSTOCKINGのPREMIUM-SILKだけ抽出
        $premium_silks = Product::where('category', 'Air Stocking')->where('group', 'PREMIUM-SILK')->get();

        //AIRSTOCKINGのDIAMOND LEGSだけ抽出
        $diamond_legs = Product::where('category', 'Air Stocking')->where('group', 'DIAMOND LEGS')->get();

        $user_id = Auth::id();

        //振込先情報をセッションに入れる
        $payee=Payment_method::where('selection', '選択')->get();
        session(['bank' => $payee[0]['bank']]);
        session(['branch' => $payee[0]['branch']]);
        session(['swift_code' => $payee[0]['swift_code']]);
        session(['account' => $payee[0]['account']]);
        session(['name' => $payee[0]['name']]);
        //dd(session('branch'));

        //$user_id=16;
        //Userinformationsテーブルから住所等を取り出す
        //$Userinformations = User::find($user_id)->userinformations;
        $Userinformations = null;

        if ($Userinformations != null) {
            $consignee = $Userinformations->consignee;
            $address_line1 = $Userinformations->address_line1;
            $address_line2 = $Userinformations->address_line2;
            $city = $Userinformations->city;
            $state = $Userinformations->state;
            $country = $Userinformations->country;
            $country_codes = $Userinformations->country_codes;
            $phone = $Userinformations->phone;
            $fax = $Userinformations->fax;
        } else {
            $consignee = "consignee";
            $address_line1 = "address_line1";
            $address_line2 = "address_line2";
            $city = "city";
            $state = "state";
            $country = "country";
            $country_codes = "country_codes";
            $phone = "phone";
            $fax = "fax";
        }

        $user = array(
            'user_id' => $user_id, 'consignee' => $consignee, 'address_line1' => $address_line1,
            'address_line2' => $address_line2, 'city' => $city, 'state' => $state, 'country' => $country, 'country_codes' => $country_codes, 'phone' => $phone, 'fax' => $fax
        );

        //ビューのplan.blade.phpに
        return view('welcome', compact('premium_silks', 'diamond_legs', 'exchange', 'user'));
    }
}
