<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Model\User;
use App\Model\Product;
use App\Model\Preference;
use App\Model\Quotation;

use Carbon\Carbon;

use Illuminate\Support\Str;

use App\Model\Userinformation;

class UserinformationController extends Controller
{
    //初めての人の住所登録
    public function entry(Request $request)
    {
        /*
        $request->validate(
            ['country_codes' => 'required|size:2',],
            ['country_codes.required' => '2 letters',]
        );
        */

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

        $user->save();

        $quotation_no = $request->quotation_no;
        $uuid = $quotation_no;
        $main = [];

        $item = Quotation::where('quotation_no', $request->quotation_no)->get();
        $quotations = Quotation::where('quotation_no', $quotation_no)->get();

        $shipper = $quotations[0]->shipper;
        $consignee = $quotations[0]->consignee;
        $port_of_loading = $quotations[0]->port_of_loading;
        $final_destination = $quotations[0]->final_destination;
        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry;
        $preference_data = Preference::first();

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


    //インボイスが初めてか確認
    public function invoice_confirm(Request $request)
    {
        $uuid = $request->quotation_no;
        $user_id = Auth::id();
        $Userinformations = Userinformation::where('user_id', '=', $user_id)->get();

        //初めての場合
        if ($Userinformations->first()->bill_company_address_line1 == null) {
            //フォームから最終目的地を受け取り登録してから移動
            $final_destination = $request->get('final_destination');
            $quotation_no = $request->quotation_no;
            $quotations = Quotation::where('quotation_no', $quotation_no)->get();
            foreach ($quotations as $quotation) {
                $quotation->final_destination = $final_destination;
                $quotation->save();
            }

            $user_id = Auth::id();
            $main = [];


            $final_destination = $request->get('final_destination');

            //Preferenceから
            $preference_data = Preference::first();

            //Userinformationの１行目からuser_idを取り出しイニシャルを探してインボイスNoを作成し保存
            //イニシャル
            $ui = Userinformation::where('user_id', $user_id)->first();
            $initial = $ui->initial;
            //国
            $country_code = $ui->country_codes;
            //インボイス番号作成
            $invoice_no = strtoupper($country_code . $initial);
            $invoice_no = $invoice_no . date('Ymd');
            //uuid
            $uuid = $quotation_no;
            //英語日付
            $day = date("F j Y");

            //Quotationから見積り内容の行を取ってくる※ $quotation_noがNULL
            $quotations = \App\Model\Quotation::where('quotation_no', $quotation_no)->get();

            //Quotationsにフォームから来たfinal_destinationを上書き保存（これでQuotationsの入力は完了）
            //初めての人は前のコントローラーで保存しているのでフォームからはこない（$final_destinationがnullの場合もある）
            if ($final_destination != null) {
                foreach ($quotations as $quotation) {
                    $quotation->final_destination = $final_destination;
                    $quotation->save();
                }
            }

            //複数行ある可能性があるので配列の1行目[0]から
            //初めての場合エラーUndefined offset: 0



            $shipper = $quotations[0]->shipper;

            $consignee_no = $quotations[0]->consignee_no;
            $consignee = Userinformation::where('user_id', $consignee_no)->first()->consignee;
            $port_of_loading = $quotations[0]->port_of_loading;
            $final_destination = $quotations[0]->final_destination;
            $sailing_on = $quotations[0]->sailing_on;
            $arriving_on = $quotations[0]->arriving_on;
            $expiry = $quotations[0]->expiry;

            //入力フォームの表示用に
            $main=User::find($user_id);
            $company_name=$main->company_name;
            $country=$main->country;

            $address_line1=$ui->address_line1;
            $address_line2=$ui->address_line2;
            $city=$ui->address_line2;
            $state=$ui->state;
            $zip=$ui->zip;
            $phone=$ui->phone;
            $person=$ui->person;
            //イニシャルを２文字で作成
            $initial=substr($company_name, 0, 2);

            //上記項目を配列$mainにまとめる
            $main = [
                'invoice_no' => $invoice_no,
                'uuid' => $uuid,
                'quotation_no' => $quotation_no,
                'preference_data' => $preference_data,
                'shipper' => $shipper,
                'consignee' => $consignee,
                'port_of_loading' => $port_of_loading,
                'final_destination' => $final_destination,
                'sailing_on' => $sailing_on,
                'arriving_on' => $arriving_on,
                'expiry' => $expiry,
                'day' => $day,
                'company_name'=>$company_name,
                'address_line1'=>$address_line1,
                'address_line2'=>$address_line2,
                'city'=>$city,
                'state'=>$state,
                'country'=>$country,
                'zip'=>$zip,
                'phone'=>$phone,
                'person'=>$person,
                'initial'=>$initial
            ];

            //商品を配列$itemsにまとめる
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
            //合計関係を$totalにまとめる
            $total = [
                'quantity_total' => $quantity_total,
                'ctn_total' => $ctn_total,
                'amount_total' => $amount_total
            ];

            //画面上の顧客情報用(base.blade.php)
            $user = [
                'user_id' => $user_id,
                'consignee' => $consignee,
                'address_line1' => $ui->address_line1,
                'address_line2' => $ui->address_line2,
                'city' => $ui->city,
                'state' => $ui->state,
                'country' => User::where('id', $user_id)->first()->country,
                'country_codes' => $ui->country_codes,
                'zip' => $ui->zip,
                'phone' => $ui->phone,
                'fax' => $ui->fax
            ];


            //インボイステーブルにデータを登録
            $invoice = new \App\Model\Invoice();
            $invoice->quotation_no = $quotation_no;
            $invoice->invoice_no = $invoice_no;
            $invoice->customers_id = $user_id;
            $invoice->date_of_issue = date('Y/m/d H:i:s');
            $invoice->day = $day;
            $invoice->save();


            //return view('invoice', compact('uuid', 'user_id', 'final_destination', 'main', 'user', 'items', 'total'));
            //return view('invoice', compact('main', 'items', 'total', 'user'));
            return view('invoice_entryform', compact('uuid', 'user_id', 'final_destination', 'main', 'user', 'items', 'total'));
        } else {
            $user_id = Auth::id();
            $main = [];

            //送信formから
            $quotation_no = $request->get('quotation_no');
            $final_destination = $request->get('final_destination');

            //Preferenceから
            $preference_data = Preference::first();

            //Userinformationの１行目からuser_idを取り出しイニシャルを探してインボイスNoを作成し保存
            //イニシャル
            $ui = Userinformation::where('user_id', $user_id)->first();
            $initial = $ui->initial;
            //国
            $country_code = $ui->country_codes;
            //インボイス番号作成
            $invoice_no = strtoupper($country_code . $initial);
            $invoice_no = $invoice_no . date('Ymd');
            //uuid
            $uuid = $quotation_no;
            //英語日付
            $day = date("F j Y");

            //Quotationから見積り内容の行を取ってくる※
            $quotations = \App\Model\Quotation::where('quotation_no', $quotation_no)->get();

            //Quotationsにフォームから来たfinal_destinationを上書き保存（これでQuotationsの入力は完了）
            //初めての人は前のコントローラーで保存しているのでフォームからはこない（$final_destinationがnullの場合もある）
            if ($final_destination != null) {
                foreach ($quotations as $quotation) {
                    $quotation->final_destination = $final_destination;
                    $quotation->save();
                }
            }

            //複数行ある可能性があるので配列の1行目[0]から
            $shipper = $quotations[0]->shipper;
            $consignee_no = $quotations[0]->consignee_no;
            $consignee = Userinformation::where('user_id', $consignee_no)->first()->consignee;
            $port_of_loading = $quotations[0]->port_of_loading;
            $final_destination = $quotations[0]->final_destination;
            $sailing_on = $quotations[0]->sailing_on;
            $arriving_on = $quotations[0]->arriving_on;
            $expiry = $quotations[0]->expiry;

            //上記項目を配列$mainにまとめる
            $main = [
                'invoice_no' => $invoice_no,
                'uuid' => $uuid,
                'quotation_no' => $quotation_no,
                'preference_data' => $preference_data,
                'shipper' => $shipper,
                'consignee' => $consignee,
                'port_of_loading' => $port_of_loading,
                'final_destination' => $final_destination,
                'sailing_on' => $sailing_on,
                'arriving_on' => $arriving_on,
                'expiry' => $expiry,
                'day' => $day
            ];

            //商品を配列$itemsにまとめる
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
            //合計関係を$totalにまとめる
            $total = [
                'quantity_total' => $quantity_total,
                'ctn_total' => $ctn_total,
                'amount_total' => $amount_total
            ];

            //画面上の顧客情報用(base.blade.php)
            $user = [
                'user_id' => $user_id,
                'consignee' => $consignee,
                'address_line1' => $ui->address_line1,
                'address_line2' => $ui->address_line2,
                'city' => $ui->city,
                'state' => $ui->state,
                'country' => User::where('id', $user_id)->first()->country,
                'country_codes' => $ui->country_codes,
                'zip' => $ui->zip,
                'phone' => $ui->phone,
                'fax' => $ui->fax
            ];


            //インボイステーブルにデータを登録
            $invoice = new \App\Model\Invoice();
            $invoice->quotation_no = $quotation_no;
            $invoice->invoice_no = $invoice_no;
            $invoice->customers_id = $user_id;
            $invoice->date_of_issue = date('Y/m/d H:i:s');
            $invoice->day = $day;
            $invoice->save();


            return view('invoice', compact('main', 'items', 'total', 'user'));
        }
    }


    public function invoice_entry_and_go(Request $request)
    {
        $user_id = Auth::id();
        $main = [];

        $ui = Userinformation::where('user_id', $user_id)->first();

        $ui->importer_name=$request->importer_name;
        $ui->bill_company_address_line1=$request->bill_company_address_line1;
        $ui->bill_company_address_line2=$request->bill_company_address_line2;
        $ui->bill_company_city= $request->bill_company_city;
        $ui->bill_company_state= $request->bill_company_state;
        $ui->bill_company_country= $request->bill_company_country;
        $ui->bill_company_zip=$request->bill_company_zip;
        $ui->bill_company_phone=$request->bill_company_phone;
        $ui->president= $request->president;
        $ui->initial= $request->initial;

        $ui->industry=$request->industry;
        $ui->business_items= $request->business_items;
        $ui->customer_name= $request->customer_name;
        $ui->fedex= $request->fedex;
        $ui->sns= $request->sns;
        $ui->website= $request->website;
        $ui->save();


        //送信formから
        $quotation_no = $request->get('quotation_no');
        $final_destination = $request->get('final_destination');

        //Preferenceから
        $preference_data = Preference::first();

        //Userinformationの１行目からuser_idを取り出しイニシャルを探してインボイスNoを作成し保存
        //イニシャル
        $ui = Userinformation::where('user_id', $user_id)->first();
        $initial = $ui->initial;
        //国
        $country_code = $ui->country_codes;
        //インボイス番号作成
        $invoice_no = strtoupper($country_code . $initial);
        $invoice_no = $invoice_no . date('Ymd');
        //uuid
        $uuid = $quotation_no;
        //英語日付
        $day = date("F j Y");

        //Quotationから見積り内容の行を取ってくる※
        $quotations = \App\Model\Quotation::where('quotation_no', $quotation_no)->get();

        //Quotationsにフォームから来たfinal_destinationを上書き保存（これでQuotationsの入力は完了）
        //初めての人は前のコントローラーで保存しているのでフォームからはこない（$final_destinationがnullの場合もある）
        if ($final_destination != null) {
            foreach ($quotations as $quotation) {
                $quotation->final_destination = $final_destination;
                $quotation->save();
            }
        }

        //複数行ある可能性があるので配列の1行目[0]から
        $shipper = $quotations[0]->shipper;
        $consignee_no = $quotations[0]->consignee_no;
        $consignee = Userinformation::where('user_id', $consignee_no)->first()->consignee;
        $port_of_loading = $quotations[0]->port_of_loading;
        $final_destination = $quotations[0]->final_destination;
        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry;

        //上記項目を配列$mainにまとめる
        $main = [
            'invoice_no' => $invoice_no,
            'uuid' => $uuid,
            'quotation_no' => $quotation_no,
            'preference_data' => $preference_data,
            'shipper' => $shipper,
            'consignee' => $consignee,
            'port_of_loading' => $port_of_loading,
            'final_destination' => $final_destination,
            'sailing_on' => $sailing_on,
            'arriving_on' => $arriving_on,
            'expiry' => $expiry,
            'day' => $day
        ];

        //商品を配列$itemsにまとめる
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
        //合計関係を$totalにまとめる
        $total = [
            'quantity_total' => $quantity_total,
            'ctn_total' => $ctn_total,
            'amount_total' => $amount_total
        ];

        //画面上の顧客情報用(base.blade.php)
        $user = [
            'user_id' => $user_id,
            'consignee' => $consignee,
            'address_line1' => $ui->address_line1,
            'address_line2' => $ui->address_line2,
            'city' => $ui->city,
            'state' => $ui->state,
            'country' => User::where('id', $user_id)->first()->country,
            'country_codes' => $ui->country_codes,
            'zip' => $ui->zip,
            'phone' => $ui->phone,
            'fax' => $ui->fax
        ];


        //インボイステーブルにデータを登録
        $invoice = new \App\Model\Invoice();
        $invoice->quotation_no = $quotation_no;
        $invoice->invoice_no = $invoice_no;
        $invoice->customers_id = $user_id;
        $invoice->date_of_issue = date('Y/m/d H:i:s');
        $invoice->day = $day;
        $invoice->save();


        return view('invoice', compact('main', 'items', 'total', 'user'));
    }




    //初めてインボイスを行う場合の住所登録
    public function invoice_entry(Request $request)
    {
        $request->validate(
            ['initial' => 'required|size:2',],
            ['initial.required' => '2 letters',]
        );

        $uuid = $request->uuid;
        $user_id = $request->user_id;

        $quotation_no = $request->quotation_no;
        $user = Userinformation::where('user_id', $user_id)->first();
        $user->bill_company_address_line1 = $request->bill_company_address_line1;
        $user->bill_company_address_line2 = $request->bill_company_address_line2;
        $user->bill_company_city = $request->bill_company_city;
        $user->bill_company_state = $request->bill_company_state;
        $user->bill_company_country = $request->bill_company_country;
        $user->bill_company_zip = $request->bill_company_zip;
        $user->bill_company_phone = $request->bill_company_phone;
        $user->president = $request->president;
        $user->industry = $request->industry;
        $user->business_items = $request->business_items;
        $user->customer_name = $request->customer_name;
        $user->initial = $request->initial;
        $user->fedex = $request->fedex;
        $user->sns = $request->sns;
        $user->website = $request->website;
        $user->save();



        $quotation_no = $request->quotation_no;

        $uuid = $quotation_no;

        $main = [];

        $item = Quotation::where('quotation_no', $request->quotation_no)->get();

        $quotations = Quotation::where('quotation_no', $quotation_no)->get();


        $consignee_no = $quotations[0]->consignee_no;

        $consignee = Userinformation::where('user_id', $consignee_no)->first()->consignee;
        $shipper = $quotations[0]->shipper;
        $consignee = $quotations[0]->consignee;
        $port_of_loading = $quotations[0]->port_of_loading;
        $final_destination = $quotations[0]->final_destination;
        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry;
        $preference_data = Preference::first();
        $ui = Userinformation::where('user_id', $user_id)->first();
        $initial = $ui->initial;

        //国
        $country_code = $ui->country_codes;
        //インボイス番号作成
        $invoice_no = strtoupper($country_code . $initial);
        $invoice_no = $invoice_no . date('Ymd');
        //uuid
        $uuid = $quotation_no;
        //英語日付
        $day = date("F j Y");


        $user =
            [
                'user_id' => $user_id,
                'consignee' => $consignee,
                'address_line1' => $ui->address_line1,
                'address_line2' => $ui->address_line2,
                'city' => $ui->city,
                'state' => $ui->state,
                'country' => User::where('id', $user_id)->first()->country,
                'country_codes' => $ui->country_codes,
                'zip' => $ui->zip,
                'phone' => $ui->phone,
                'fax' => $ui->fax
            ];
        $main =
            [
                'quotation_no' => $quotation_no,
                'preference_data' => $preference_data,
                'shipper' => $shipper,
                'consignee' => $consignee,
                'port_of_loading' => $port_of_loading,
                'final_destination' => $final_destination,
                'sailing_on' => $sailing_on,
                'arriving_on' => $arriving_on,
                'expiry' => $expiry,
                'day' => $day,
                'invoice_no' => $invoice_no,
                'uuid' => $quotation_no
            ];

        //商品を配列$itemsにまとめる
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
        //合計関係を$totalにまとめる
        $total = [
            'quantity_total' => $quantity_total,
            'ctn_total' => $ctn_total,
            'amount_total' => $amount_total
        ];

        return view('invoice', compact('uuid', 'user_id', 'main', 'user', 'items', 'total'));
    }
}
