<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Model\User;
use App\Model\Product;
use App\Model\Preference;
use App\Model\Quotation;
use App\Model\Quotation_detail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Model\Userinformation;
use App\Model\Invoice_serial_number;
use App\Model\Invoice_counter;

use App\Model\SailingOn;
use App\Model\Expirie;
use App\Model\Etd;

use App\Mail\QuotationMail;
use App\Mail\InvoiceMail;
use Mail;
use App\Model\Emailtext;
use App\Model\Consignee;
use App\Model\HeadOffice;
use App\Model\Pic;

//use App\Http\Controllers\QuotationController;


class UserinformationController extends Controller
{
    //Consignee(送り先登録)が初めての人の住所登録を行う (普通にPersonInChargeを済ませ、見積もりに移行した場合のコンサイニー住所登録画面)
    public function entry(Request $request)
    {

        //dd("entry");

        $type = $request->type;
        $user_id = $request->user_id;
        $quotation_no = $request->quotation_no;

        $user_information = new Userinformation();

        $user_information->user_id = $request->user_id;
        $user_information->consignee = $request->consignee;
        $user_information->address_line1 = $request->address_line1;
        $user_information->address_line2 = $request->address_line2;
        $user_information->city = $request->city;
        $user_information->state = $request->state;
        $user_information->country_codes = $request->country_codes;
        $user_information->zip = $request->zip;
        $user_information->phone = $request->phone;
        $user_information->person = $request->person;
        $user_information->save();

        //pic番号(Person in charge)を取り出す
        $person_in_charge = Pic::where('user_id', $user_id)->first();
        session(['pic_id' => $person_in_charge->pic_id]);

        //consigneeテーブルにも保存　2024-1-5
        $consignee = new Consignee();

        $consignee->user_id = $request->user_id;
        $consignee->consignee = $request->consignee;
        $consignee->address_line1 = $request->address_line1;
        $consignee->address_line2 = $request->address_line2;
        $consignee->city = $request->city;
        $consignee->state = $request->state;
        $consignee->country_codes = $request->country_codes;
        $consignee->post_code = $request->zip;
        $consignee->phone = $request->phone;
        $consignee->name = $request->person;
        $consignee->default_destination = "1"; //既定に設定
        $consignee->pic_id = $person_in_charge->pic_id;
        $consignee->save();


        $uuid = $quotation_no;
        $main = [];

        $quotations = Quotation::where('quotation_no', $quotation_no)->get();
        $shipper = $quotations[0]->shipper;

        $consignee = $request->consignee;

        $port_of_loading = $quotations[0]->port_of_loading;
        $final_destination = $quotations[0]->final_destination;
        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry;
        $preference_data = Preference::first();

        $main = [$quotation_no, $preference_data, $shipper, $consignee, $port_of_loading, $final_destination, $sailing_on, $arriving_on, $expiry];

        $quotations_sub = Quotation_detail::where('quotation_no', $request->quotation_no)->get();
        $data = [];
        $items = [];

        foreach ($quotations_sub as $quotation) {
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

        //見積もり有効期限
        $expiry_days = Expirie::find(1)->number_of_days; //15daysなどが入る

        //15daysの実際の年月日を出す
        $num = preg_replace('/[^0-9]/', '', $expiry_days);
        $expirytoday = new Carbon('today');
        $expiryaddday = $expirytoday->addDay($num);
        $expiryaddday = $expiryaddday->toDateString();
        $expiryaddday = date('M j Y', strtotime($expiryaddday)); //Apr 26 2021などを作成

        $expiry_days2 = $expiry_days . " (" . $expiryaddday . ")";
        session()->put('expiry_days', $expiry_days2); //15days
        session()->put('expiryaddday', $expiryaddday); //Apr 26 2021

        return view('quotation', compact('quotation_no', 'preference_data', 'items', 'ctn_total', 'quantity_total', 'amount_total', 'sailing_on', 'user_information', 'type', 'shipper', 'consignee', 'port_of_loading', 'arriving_on', 'expiry_days2'));
    }


    //インボイスが初めてか確認
    public function invoice_confirm(Request $request)
    {

       

        $type = $request->type;

        $uuid = $request->quotation_no;
        $user_id = Auth::id();
        $user_information = Userinformation::where('user_id', '=', $user_id)->get();


        //初めての場合
        if ($user_information->first()->bill_company_address_line1 == null) {

            //dd("invoice_confirm_first");

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

            ///////////////////////////////
            //インボイス番号作成
            //Userinformationの１行目からuser_idを取り出しイニシャルを探してインボイスNoを作成し保存

            
            $user_information = Userinformation::where('user_id', $user_id)->first();

            //この時点ではまだイニシャルがない場合がある。ただしマイページに先にアクセスされると、イニシャルは登録済みになっている可能性がある
            $initial = $user_information->initial;
            //国
            $country_code = $user_information->country_codes;
            $user = User::where('id', '=', $user_id)->first();
            //国２文字
            $country_short = strtoupper(substr($user->country, 0, 2));
            //会社名２文字
            $user_information = Userinformation::where('user_id', '=', $user_id)->first();

            if (strtoupper(substr($user_information->initial, 0, 2)) != null) {
                $initial = strtoupper(substr($user_information->initial, 0, 2));
            } else {
                $initial = strtoupper(substr($user_information->company_name, 0, 2));
            }
            if ($initial == "") {
                $initial = "CC";
            }
            //連番
            $latestOrder = Invoice_counter::where('id', 1)->first();
            $today = date('Y-m-d');

            if ($today != $latestOrder->last_update) {
                $latestOrder->count = 1;
                $latestOrder->last_update = date('Y/m/d');
                $latestOrder->save();
            } else {
                $latestOrder->count = $latestOrder->count + 1;
                $latestOrder->save();
            }
            $no = $latestOrder->count;

            $invoice_no =  $country_short . $initial . date('ymd') . '_' . str_pad($no, 2, 0, STR_PAD_LEFT);;
            $output = $invoice_no . '.pdf';
            $print_no = $invoice_no;
            ///////////////////////////////
            //インボイス番号作成END
            ///////////////////////////////



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
            $main = User::find($user_id);
            $company_name = $main->company_name;
            $country = $main->country;

            $address_line1 = $user_information->address_line1;
            $address_line2 = $user_information->address_line2;
            $city = $user_information->city;
            $state = $user_information->state;
            $zip = $user_information->zip;
            $phone = $user_information->phone;
            $person = $user_information->person;
            //イニシャルを２文字で作成
            $initial = substr($company_name, 0, 2);

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
                'company_name' => $company_name,
                'address_line1' => $address_line1,
                'address_line2' => $address_line2,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'zip' => $zip,
                'phone' => $phone,
                'person' => $person,
                'initial' => $initial
            ];

            //商品を配列$itemsにまとめる
            $quotations_sub = \App\Model\Quotation_detail::where('quotation_no', $quotation_no)->get();
            $data = [];
            $items = [];
            foreach ($quotations_sub as $quotation) {
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
                'address_line1' => $user_information->address_line1,
                'address_line2' => $user_information->address_line2,
                'city' => $user_information->city,
                'state' => $user_information->state,
                'country' => User::where('id', $user_id)->first()->country,
                'country_codes' => $user_information->country_codes,
                'zip' => $user_information->zip,
                'phone' => $user_information->phone,
                'fax' => $user_information->fax
            ];


            //インボイステーブルにデータを登録
            $invoice = new \App\Model\Invoice();
            $invoice->quotation_no = $quotation_no;
            $invoice->invoice_no = $invoice_no;
            $invoice->customers_id = $user_id;
            $invoice->date_of_issue = date('Y/m/d H:i:s');
            $invoice->day = $day;
            $invoice->save();


            //見積もり有効期限
            $expiry_days = Expirie::find(1)->number_of_days;
            //session()->put('expiry_days',$expiry_days);
            //15daysの実際の年月日を出す
            $num = preg_replace('/[^0-9]/', '', $expiry_days);
            $expirytoday = new Carbon('today');
            $expiryaddday = $expirytoday->addDay($num);
            $expiryaddday = $expiryaddday->toDateString();
            $expiryaddday = date('M j Y', strtotime($expiryaddday)); //Apr 26 2021などを作成

            $expiry_days2 = $expiry_days . " (" . $expiryaddday . ")";
            session()->put('expiry_days', $expiry_days2); //15days
            session()->put('expiryaddday', $expiryaddday); //Apr 26 2021



            //Invoiceメール送信
            $to = User::find($user_id)->email;
            $bcc = session('adminmail');
            $bcc = "info@lookingfor.jp";
            //dd($to,$bcc);

            $subject = Emailtext::Find(1)->subject_5;
            $content = [
                'contents' => Emailtext::Find(1)->contents_5,
                'shipper' => $shipper,
                'consignee' => $consignee,
                'port_of_loading' => $port_of_loading,
                'final_destination' => $final_destination,
                'sailing_on' => $sailing_on,
                'Arriving on' => '',
                'quotaition_deadline' => $expiry_days,
                'quantity_total' => $quantity_total,
                'ctn_total' => $ctn_total,
                'amount_total' => $amount_total,
            ];

            //インボイスメール
            Mail::to($to)->bcc($bcc)->send(new InvoiceMail($content, $subject, $items));

            return view('invoice_entryform', compact('uuid', 'user_id', 'final_destination', 'main', 'user', 'items', 'total', 'type'));


        } else {


            //dd("invoice_confirm_manytimes");


            $user_id = Auth::id();
            $main = [];
            $type = $request->type;

            $quotation_no = $request->get('quotation_no');
            $final_destination = $request->get('final_destination');
            $preference_data = Preference::first();

            ///////////////////////////////
            //インボイス番号作成
            //Userinformationの１行目からuser_idを取り出しイニシャルを探してインボイスNoを作成し保存
            //イニシャル
            $user_information = Userinformation::where('user_id', $user_id)->first();
            $initial = $user_information->initial;
            //国
            $country_code = $user_information->country_codes;
            $un = User::where('id', '=', $user_id)->first();
            //国２文字
            $ct = strtoupper(substr($un->country, 0, 2));
            //会社名２文字
            $user_information = Userinformation::where('user_id', '=', $user_id)->first();

            if (strtoupper(substr($user_information->initial, 0, 2)) != null) {
                $cp = strtoupper(substr($user_information->initial, 0, 2));
            } else {
                $cp = strtoupper(substr($user_information->company_name, 0, 2));
            }
            if ($cp == "") {
                $cp = "CC";
            }
            //連番
            $latestOrder = Invoice_counter::where('id', 1)->first();
            $today = date('Y-m-d');

            if ($today != $latestOrder->last_update) {
                $latestOrder->count = 1;
                $latestOrder->last_update = date('Y/m/d');
                $latestOrder->save();
            } else {
                $latestOrder->count = $latestOrder->count + 1;
                $latestOrder->save();
            }
            $no = $latestOrder->count;

            $shortYear = date('y');
            $invoice_no =  $ct . $cp .$shortYear .date('md') . '_' . str_pad($no, 2, 0, STR_PAD_LEFT);

            $output = $invoice_no . '.pdf';
            $print_no = $invoice_no;



            ///////////////////////////////

            //uuid
            $uuid = $quotation_no;
            //英語日付
            $day = date("F j Y");

            //Quotationから見積り内容の行を取ってくる※
            $quotations = Quotation::where('quotation_no', $quotation_no)->get();
            //dd($quotations);
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
            $expiry = $quotations[0]->expiry_days2;

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

            $quotations_sub = \App\Model\Quotation_detail::where('quotation_no', $quotation_no)->get();
            //商品を配列$itemsにまとめる
            $data = [];
            $items = [];
            foreach ($quotations_sub as $quotation) {
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
                'address_line1' => $user_information->address_line1,
                'address_line2' => $user_information->address_line2,
                'city' => $user_information->city,
                'state' => $user_information->state,
                'country' => User::where('id', $user_id)->first()->country,
                'country_codes' => $user_information->country_codes,
                'zip' => $user_information->zip,
                'phone' => $user_information->phone,
                'fax' => $user_information->fax
            ];


            //インボイステーブルにデータを登録
            $invoice = new \App\Model\Invoice();
            $invoice->quotation_no = $quotation_no;
            $invoice->invoice_no = $invoice_no;
            $invoice->customers_id = $user_id;
            $invoice->date_of_issue = date('Y/m/d H:i:s');
            $invoice->day = $day;
            $invoice->save();


            //見積もり有効期限
            //$expiry_days = Expirie::find(1)->number_of_days;
            $expiry_days = $quotations[0]->expiry_days2;
            session()->put('expiry_days', $expiry_days);


            //Invoiceメール送信
            $to = User::find($user_id)->email;
            $bcc = "info@lookingfor.jp";
            //$bcc=session('adminmail');
            //dd($to,$bcc);


            $subject = Emailtext::Find(1)->subject_5;
            $content = [
                'contents' => Emailtext::Find(1)->contents_5,
                'shipper' => $shipper,
                'consignee' => $consignee,
                'port_of_loading' => $port_of_loading,
                'final_destination' => $final_destination,
                'sailing_on' => $sailing_on,
                'Arriving on' => $arriving_on,
                'quotaition_deadline' => $expiry_days,
                'quantity_total' => $quantity_total,
                'ctn_total' => $ctn_total,
                'amount_total' => $amount_total,
            ];

            //インボイスメール
            Mail::to($to)->bcc($bcc)->send(new InvoiceMail($content, $subject, $items));
            return view('invoice', compact('main', 'items', 'total', 'user', 'type'));
        }
    }

    //初めての人がHeadOfficeを入力後、Invoiceに移動する
    public function invoice_entry_and_go(Request $request)
    {

        
        $user_id = Auth::id();
        $main = [];
        $type = $request->type;

        $user_information = Userinformation::where('user_id', $user_id)->first();

        $user_information->importer_name = $request->importer_name;
        $user_information->bill_company_address_line1 = $request->bill_company_address_line1;
        $user_information->bill_company_address_line2 = $request->bill_company_address_line2;
        $user_information->bill_company_city = $request->bill_company_city;
        $user_information->bill_company_state = $request->bill_company_state;
        $user_information->bill_company_country = $request->bill_company_country;
        $user_information->bill_company_zip = $request->bill_company_zip;
        $user_information->bill_company_phone = $request->bill_company_phone;
        $user_information->president = $request->president;
        $user_information->initial = $request->initial;
        $user_information->industry = $request->industry;
        $user_information->business_items = $request->business_items;
        $user_information->customer_name = $request->customer_name;
        $user_information->fedex = $request->fedex;
        $user_information->sns = $request->sns;
        $user_information->website = $request->website;
        $user_information->save();

        //2024 1-6 headoffice
        $head_office = new HeadOffice();
        $head_office->company_name = $request->importer_name;
        $head_office->address_line1 = $request->bill_company_address_line1;
        $head_office->address_line2 = $request->bill_company_address_line2;
        $head_office->city = $request->bill_company_city;
        $head_office->state = $request->bill_company_state;
        $head_office->country = $request->bill_company_country;
        $head_office->zip = $request->bill_company_zip;
        $head_office->phone = $request->bill_company_phone;
        $head_office->president = $request->president;
        $head_office->initial = $request->initial;
        $head_office->industry = $request->industry;
        $head_office->business_items = $request->business_items;
        $head_office->customer_name = $request->customer_name;
        $head_office->fedex = $request->fedex;
        $head_office->sns = $request->sns;
        $head_office->website = $request->website;
        $head_office->user_id = $user_id;
        $head_office->save();

        //送信formから
        $quotation_no = $request->get('quotation_no');
        $final_destination = $request->get('final_destination');

        //Preferenceから
        $preference_data = Preference::first();

        ///////////////////////////////
        //インボイス番号作成
        //Userinformationの１行目からuser_idを取り出しイニシャルを探してインボイスNoを作成し保存
        //イニシャル
        //$user_information = Userinformation::where('user_id', $user_id)->first();
        $initial = $user_information->initial;


        //国
        $country_code = $user_information->country_codes;
        
        $un = User::where('id', '=', $user_id)->first();
        //国２文字
        $ct = strtoupper(substr($un->country, 0, 2));
        //会社名２文字
        $user_information = Userinformation::where('user_id', '=', $user_id)->first();

        if (strtoupper(substr($user_information->initial, 0, 2)) != null) {
            $cp = strtoupper(substr($user_information->initial, 0, 2));
        } else {
            $cp = strtoupper(substr($user_information->company_name, 0, 2));
        }
        if ($cp == "") {
            $cp = "CC";
        }
        //連番
        $latestOrder = Invoice_counter::where('id', 1)->first();
        $today = date('Y-m-d');

        if ($today != $latestOrder->last_update) {
            $latestOrder->count = 1;
            $latestOrder->last_update = date('Y/m/d');
            $latestOrder->save();
        } else {
            $latestOrder->count = $latestOrder->count + 1;
            $latestOrder->save();
        }
        $no = $latestOrder->count;

        $shortYear = date('y');
        //$invoice_no =  $ct . $cp . date('md') . '_' . str_pad($no, 2, 0, STR_PAD_LEFT);
        $invoice_no =  $ct . $cp .date('ymd') . '_' . str_pad($no, 2, 0, STR_PAD_LEFT);

        $output = $invoice_no . '.pdf';
        $print_no = $invoice_no;
        ///////////////////////////////


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
        $quotations_sub = \App\Model\Quotation_detail::where('quotation_no', $quotation_no)->get();
        $data = [];
        $items = [];
        foreach ($quotations_sub as $quotation) {
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
            'address_line1' => $user_information->address_line1,
            'address_line2' => $user_information->address_line2,
            'city' => $user_information->city,
            'state' => $user_information->state,
            'country' => User::where('id', $user_id)->first()->country,
            'country_codes' => $user_information->country_codes,
            'zip' => $user_information->zip,
            'phone' => $user_information->phone,
            'fax' => $user_information->fax
        ];


        //インボイステーブルにデータを登録
        $invoice = new \App\Model\Invoice();
        $invoice->quotation_no = $quotation_no;
        $invoice->invoice_no = $invoice_no;
        $invoice->customers_id = $user_id;
        $invoice->date_of_issue = date('Y/m/d H:i:s');
        $invoice->day = $day;
        $invoice->save();

        //見積もり有効期限
        $expiry_days = Expirie::find(1)->number_of_days;
        session()->put('expiry_days', $expiry_days);

        //Invoiceメール送信
        $to = User::find($user_id)->email;
        //$bcc="info@lookingfor.jp";
        $bcc = session('adminmail');
        $subject = Emailtext::Find(1)->subject_5;
        $content = [
            'contents' => Emailtext::Find(1)->contents_5,
            'shipper' => $shipper,
            'consignee' => $consignee,
            'port_of_loading' => $port_of_loading,
            'final_destination' => $final_destination,
            'sailing_on' => $sailing_on,
            'Arriving on' => '',
            'quotaition_deadline' => $expiry_days,
            'quantity_total' => $quantity_total,
            'ctn_total' => $ctn_total,
            'amount_total' => $amount_total,
        ];

        //インボイスメール
        Mail::to($to)->bcc($bcc)->send(new InvoiceMail($content, $subject, $items));
        return view('invoice', compact('main', 'items', 'total', 'user', 'type'));
    }




    //初めてインボイスを行う場合の住所登録
    public function invoice_entry(Request $request)
    {
        //dd("invoice_entry");

        $request->validate(
            ['initial' => 'required|size:2',],
            ['initial.required' => '2 letters',]
        );

        $uuid = $request->uuid;
        $user_id = $request->user_id;
        $type = $request->type;

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
        $user_information = Userinformation::where('user_id', $user_id)->first();
        $initial = $user_information->initial;


        ///////////////////////////////
        //インボイス番号作成
        //Userinformationの１行目からuser_idを取り出しイニシャルを探してインボイスNoを作成し保存
        //イニシャル
        $user_information = Userinformation::where('user_id', $user_id)->first();
        $initial = $user_information->initial;
        //国
        $country_code = $user_information->country_codes;
        $un = User::where('id', '=', $user_id)->first();
        //国２文字
        $ct = strtoupper(substr($un->country, 0, 2));
        //会社名２文字
        $user_information = Userinformation::where('user_id', '=', $user_id)->first();

        if (strtoupper(substr($user_information->initial, 0, 2)) != null) {
            $cp = strtoupper(substr($user_information->initial, 0, 2));
        } else {
            $cp = strtoupper(substr($user_information->company_name, 0, 2));
        }
        if ($cp == "") {
            $cp = "CC";
        }
        //連番
        $latestOrder = Invoice_counter::where('id', 1)->first();
        $today = date('Y-m-d');

        if ($today != $latestOrder->last_update) {
            $latestOrder->count = 1;
            $latestOrder->last_update = date('Y/m/d');
            $latestOrder->save();
        } else {
            $latestOrder->count = $latestOrder->count + 1;
            $latestOrder->save();
        }
        $no = $latestOrder->count;
        $invoice_no =  $ct . $cp . date('md') . '_' . str_pad($no, 2, 0, STR_PAD_LEFT);;
        $output = $invoice_no . '.pdf';
        $print_no = $invoice_no;
        ///////////////////////////////



        //uuid
        $uuid = $quotation_no;
        //英語日付
        $day = date("F j Y");


        $user =
            [
                'user_id' => $user_id,
                'consignee' => $consignee,
                'address_line1' => $user_information->address_line1,
                'address_line2' => $user_information->address_line2,
                'city' => $user_information->city,
                'state' => $user_information->state,
                'country' => User::where('id', $user_id)->first()->country,
                'country_codes' => $user_information->country_codes,
                'zip' => $user_information->zip,
                'phone' => $user_information->phone,
                'fax' => $user_information->fax
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
        $quotations_sub = Quotation_detail::where('quotation_no', $quotation_no)->get();
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


        //見積もり有効期限
        $expiry_days = Expirie::find(1)->number_of_days;
        session()->put('expiry_days', $expiry_days);

        //Invoiceメール送信
        $to = User::find($user_id)->email;
        //$bcc="info@lookingfor.jp";
        $bcc = session('adminmail');
        $subject = Emailtext::Find(1)->subject_5;
        $content = [
            'contents' => Emailtext::Find(1)->contents_5,
            'shipper' => $shipper,
            'consignee' => $consignee,
            'port_of_loading' => $port_of_loading,
            'final_destination' => $final_destination,
            'sailing_on' => $sailing_on,
            'Arriving on' => '',
            'quotaition_deadline' => $expiry_days,
            'quantity_total' => $quantity_total,
            'ctn_total' => $ctn_total,
            'amount_total' => $amount_total,
        ];

        //インボイスメール
        Mail::to($to)->bcc($bcc)->send(new InvoiceMail($content, $subject, $items));

        return view('invoice', compact('uuid', 'user_id', 'main', 'user', 'items', 'total', 'type'));
    }
}
