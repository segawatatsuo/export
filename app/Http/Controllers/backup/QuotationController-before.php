<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Model\User;
use App\Model\Product;
use App\Model\Preference;
use App\Model\Quotation;
use App\Model\Userinformation;
use Carbon\Carbon;

class QuotationController extends Controller
{
    //初めての人の住所登録
    public function entry(Request $request)
    {
        $user_id = $request->user_id;
        $quotation_no = $request->quotation_no;
        $user = new Userinformation();
        $user->user_id = $request->user_id;
        $user->consignee = $request->consignee;
        $user->address_line1 = $request->address_line1;
        $user->address_line2 = $request->address_line2;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->country_codes = $request->country_codes;
        $user->zip = $request->zip;
        $user->phone = $request->phone;
        $user->person = $request->person;
        $user->person_gender = $request->person_gender;
        $user->bill_company_address_line1 = $request->bill_company_address_line1;
        $user->bill_company_address_line2 = $request->bill_company_address_line2;
        $user->bill_company_city = $request->bill_company_city;
        $user->bill_company_state = $request->bill_company_state;
        $user->bill_company_country = $request->bill_company_country;
        $user->bill_company_zip = $request->bill_company_zip;
        $user->bill_company_phone = $request->bill_company_phone;
        $user->president = $request->president;
        $user->president_gender = $request->president_gender;

        dd($request->president_gender);

        $user->industry = $request->industry;
        $user->business_items = $request->business_items;
        $user->customer_name = $request->customer_name;
        $user->fax = $request->fax;
        $user->fedex = $request->fedex;
        $user->sns = $request->sns;
        $user->initial=$request->initial;
        $user->save();


        $quotation_no = $request->quotation_no;
        $uuid = $quotation_no;
        $main = [];
        //$quotation_no = $request->get('quotation_no');

        $item = Quotation::where('quotation_no', $request->quotation_no)->get();
        $quotations = Quotation::where('quotation_no', $quotation_no)->get();
        $preference_data = Preference::first();

        $shipper = $quotations[0]->shipper;
        $consignee = $quotations[0]->consignee;
        $port_of_loading = $quotations[0]->port_of_loading;
        $final_destination = $quotations[0]->final_destination;
        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry;

        $main = [$quotation_no, $preference_data, $shipper, $consignee, $port_of_loading, $final_destination, $sailing_on, $arriving_on, $expiry];

        $data = [];
        $items = [];

        foreach ($quotations as $quotation) {
            $product_code = $quotation->product_code;
            $product_name = $quotation->product_name;
            $quantity = $quotation->quantity;
            $ctn = $quotation->ctn;
            $quantity = $quotation->quantity;
            $unit_price = $quotation->unit_price;
            $amount = $quotation->amount;
            $data = [$product_code, $product_name, $quantity, $ctn, $unit_price, $amount];
            array_push($items, $data);
        }

        $quantity_total = $quotations[0]->quantity_total;
        $ctn_total = $quotations[0]->ctn_total;
        $amount_total = $quotations[0]->amount_total;
        $total = [$quantity_total, $ctn_total, $amount_total];

        return view('quotation', compact('uuid', 'preference_data', 'items', 'ctn_total', 'quantity_total', 'amount_total', 'sailing_on', 'user'));
    }

    public function quotation(Request $request)
    {
        //HTMLフォーム送信のnameがitemのものだけ取得
        $quotations = $request->get('item');

        $ctn_total = 0;
        //全体の注文数を出す
        foreach ($quotations as $key => $val) {
            if ($val != "") {
                $ctn_total += $val; //合計数(カートンTOTAL)
            }
        }

        //セッションメッセージ（10カートン以下の場合）
        if ($ctn_total < 10) {
            //session()->flash('flash_message', 'Please enter at least 10 cartons');
            //return redirect()->action('ProductController@plan');
            return redirect()->action('ProductController@plan')->with('flash_message', 'Please enter at least 10 cartons in total');
        }

        //itemを分解する
        $items = [];
        $quantity_total = 0;
        $amount_total = 0;

        foreach ($quotations as $key => $val) {
            if ($val != "") {
                preg_match_all('/(.+?)\|(.+?)\|(.*)/', $key, $match);
                $hinban = $match[1][0]; //品番
                $hinmei = $match[2][0]; //品名
                $ctn = $val; //カートン数

                $tanka = $match[3][0]; //単価
                if ($ctn_total >= 10 and $ctn_total <= 100) {
                    $tanka = $tanka * 0.7;
                } elseif ($ctn_total >= 101 and $ctn_total <= 1000 and $ctn < 20) {
                    $tanka = $tanka * 0.7;
                } elseif ($ctn_total >= 101 and $ctn_total <= 1000 and $ctn >= 20) {
                    $tanka = $tanka * 0.6;
                } elseif ($ctn_total >= 1001 and $ctn >= 20) {
                    $tanka = $tanka * 0.5;
                } elseif ($ctn_total >= 1001 and $ctn >= 20) {
                    $tanka = $tanka * 0.7;
                }

                $quantity = $ctn * 24; //本数(カートン数*24本)
                $amount = $quantity * $tanka; //金額＝数量＊単価

                $quantity_total += $quantity; //本数合計
                $amount_total += $amount; //金額合計

                $data = [$hinban, $hinmei, $tanka, $ctn, $quantity, $amount];
                array_push($items, $data);
            }
        }
        //Sailing on(出航予定月)
        $date = new Carbon();
        $date = Carbon::now();
        if ($date->day <= 23) {
            $year = $date->format('Y');
            $month = $date->format('M');
            $sailing_on = $month . ',' . $year;
        } else {
            $date = $date->addMonth();
            $year = $date->format('Y');
            $month = $date->format('M');
            $sailing_on = $month . ',' . $year;
        }

        //ユニークキー（見積番号）を作成
        $uuid = strtoupper(uniqid());
        //shipper他いろいろなpreferenceデータを引っ張ってくる
        $preference_data = Preference::first();
        $user_id = Auth::id();

        //quotationsテーブルにデータを作成する
        foreach ($items as $item) {
            $db = new Quotation();
            $db->quotation_no = $uuid;
            $db->date_of_issue = Carbon::now();
            $db->shipper = $preference_data->shipper;
            $db->consignee_no = $user_id;
            $db->port_of_loading = $preference_data->port_of_loading;
            $db->sailing_on = $sailing_on;
            //arriving_on
            $db->expiry = $preference_data->expiry;
            $db->product_code = $item[0];
            $db->product_name = $item[1];
            $db->unit_price = $item[2];
            $db->ctn = $item[3];
            $db->quantity = $item[4];
            $db->amount = $item[5];
            $db->quantity_total = $quantity_total;
            $db->ctn_total = $ctn_total;
            $db->amount_total = $amount_total;
            //初回の人はまだこの時点ではconsigneeはない
            if ($db->consignee) {
                $db->consignee = $consignee;
            }
            if ($db->final_destination) {
                $db->final_destination = $state . ',' . $country;
            }

            $db->save();
        }

        //Userinformationsテーブルからマスターのidと同じuser_idを探し住所等を取り出す
        $user_id = Auth::id();
        $Userinformations = User::find($user_id)->Userinformations;


        //Userinformationsがnullの場合（住所登録が住んでいない場合）なら、quotation_noを持たせて住所入力フォームへ移動
        if ($Userinformations == null) {
            return view('entryform', compact('uuid', 'user_id'));
        }

        //住所登録が済んでいる場合
        $user_id = Auth::id();
        $Userinformations = Quotation::find($user_id)->userinformations;
        $consignee = $Userinformations->consignee;
        $address_line1 = $Userinformations->address_line1;
        $address_line2 = $Userinformations->address_line2;
        $city = $Userinformations->city;
        $state = $Userinformations->state;
        $country = $Userinformations->country;
        $country_codes = $Userinformations->country_codes;
        $phone = $Userinformations->phone;
        $fax = $Userinformations->fax;

        $user = array(
            'user_id' => $user_id, 'consignee' => $consignee, 'address_line1' => $address_line1,
            'address_line2' => $address_line2, 'city' => $city, 'state' => $state, 'country' => $country, 'country_codes' => $country_codes, 'phone' => $phone, 'fax' => $fax
        );

        return view('quotation', compact('quotations', 'uuid', 'preference_data', 'items', 'ctn_total', 'quantity_total', 'amount_total', 'sailing_on', 'user'));
        //return view('quotation', compact('uuid', 'preference_data', 'items', 'ctn_total', 'quantity_total', 'amount_total', 'sailing_on', 'user'));
    }



    //PDFの出力(FORMからhidenでuuidを受け取る)
    public function generate_pdf(Request $request)
    {
        $main = [];
        //送信formから
        $quotation_no = $request->get('quotation_no');
        //Quotationから見積り内容をget
        $quotations = Quotation::where('quotation_no', $quotation_no)->get();
        //送信formから
        $final_destination = $request->final_destination;

        //Preferenceから
        $preference_data = Preference::first();

        $shipper = $quotations[0]->shipper;
        $consignee = $quotations[0]->consignee;
        $port_of_loading = $quotations[0]->port_of_loading;

        //$final_destination = $quotations[0]->final_destination;
        $final_destination = $final_destination;

        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry;

        $main = [$quotation_no, $preference_data, $shipper, $consignee, $port_of_loading, $final_destination, $sailing_on, $arriving_on, $expiry];

        $data = [];
        $items = [];

        foreach ($quotations as $quotation) {
            $product_code = $quotation->product_code;
            $product_name = $quotation->product_name;
            $quantity = $quotation->quantity;
            $ctn = $quotation->ctn;
            $quantity = $quotation->quantity;
            $unit_price = $quotation->unit_price;
            $amount = $quotation->amount;
            $data = [$product_code, $product_name, $quantity, $ctn, $unit_price, $amount];
            array_push($items, $data);
        }

        $quantity_total = $quotations[0]->quantity_total;
        $ctn_total = $quotations[0]->ctn_total;
        $amount_total = $quotations[0]->amount_total;
        $total = [$quantity_total, $ctn_total, $amount_total];
        /*
        $pdf = \PDF::loadView('quotation_print',compact("main","items"));
        $pdf->setPaper('A4');
        return $pdf->download('quotation.pdf');
        */

        $image_path = storage_path('app/public/hamada.png');
        $image_data = base64_encode(file_get_contents($image_path));

        //$pdf = PDF::loadView('(Bladeファイル)', compact('image_data',....))->setPaper('a4', 'landscape');
        //$pdf->download('(ファイル名)');

        $pdf = \PDF::loadView('quotation_print', compact('image_data', 'main', 'items', 'total'))->setPaper('a4')->setWarnings(false);
        return $pdf->download('quotation.pdf');
    }
}
