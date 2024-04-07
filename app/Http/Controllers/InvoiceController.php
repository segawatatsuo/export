<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\Quotation;
use App\Model\Quotation_detail;
use App\Model\Preference;
use App\Model\Userinformation;
use App\Model\Invoice;
use App\Model\Invoice_counter;
use App\Model\Payment_method;
use Carbon\Carbon;
use App\Model\Invoice_serial_number;
use Illuminate\Support\Facades\Storage;

use App\Model\SailingOn;
use App\Model\Expirie;
use App\Model\Etd;

use App\Mail\InvoiceMail;
use Mail;
use App\Model\Emailtext;

class InvoiceController extends Controller
{
    public function invoice(Request $request)
    {
        $user_id = Auth::id();
        $main = [];

        $quotation_no = $request->get('quotation_no');
        $final_destination = $request->get('final_destination');
        $preference_data = Preference::first();

        ///////////////////////////////
        //インボイス番号作成
        //Userinformationの１行目からuser_idを取り出しイニシャルを探してインボイスNoを作成し保存
        //イニシャル
        $ui = Userinformation::where('user_id', $user_id)->first();
        $initial = $ui->initial;
        //国
        $country_code = $ui->country_codes;
        $un = User::where('id', '=', $user_id)->first();
        //国２文字
        $ct = strtoupper(substr($un->country, 0, 2));
        //会社名２文字
        $ui = Userinformation::where('user_id', '=', $user_id)->first();

        if (strtoupper(substr($ui->initial, 0, 2)) != null) {
            $cp = strtoupper(substr($ui->initial, 0, 2));
        } else {
            $cp = strtoupper(substr($ui->company_name, 0, 2));
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
        $invoice_no =  $ct . $cp . date('md') . '_' . str_pad($no, 2, 0, STR_PAD_LEFT);
        ;
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

        //セッション
        session()->put(['port_of_loading' => $quotations[0]->port_of_loading]);
        session()->put(['final_destination' => $quotations[0]->final_destination]);
        session()->put(['sailing_on' => $quotations[0]->sailing_on]);
        session()->put(['arriving_on' => $quotations[0]->arriving_on]);
        session()->put(['expiry' => $quotations[0]->expiry]);

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

        // 二重送信防止
        $request->session()->regenerateToken();

        //見積もり有効期限
        $expiry_days = Expirie::find(1)->number_of_days;
        session()->put('expiry_days', $expiry_days);
        //15daysの実際の年月日を出す
        $num = preg_replace('/[^0-9]/', '', $expiry_days);
        $expirytoday=new Carbon('today');
        $expiryaddday=$expirytoday->addDay($num);
        $expiryaddday = $expiryaddday->toDateString();
        $expiryaddday = date('M j Y', strtotime($expiryaddday));//Apr 26 2021などを作成

        $expiry_days2 = $expiry_days." (".$expiryaddday.")";
        session()->put('expiry_days', $expiry_days2);//15days
        session()->put('expiryaddday', $expiryaddday);//Apr 26 2021



        //Invoiceメール送信
        $to = User::find($user_id)->email;
        //$bcc="info@lookingfor.jp";
        $bcc = session('adminmail');
        $bcc = 'info@lookingfor.jp';


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

        return view('invoice', compact('main', 'items', 'total', 'user'));
    }


    //マイページから再度表示へ
    public function invoice_repeat(Request $request)
    {
        $invoice_no = $request->invoice_no;
        $inv = Invoice::where('invoice_no', $invoice_no)->first();
        $quotation_no = $inv->quotation_no;
        $qt=Quotation::where('quotation_no', $quotation_no)->first();


        $preference_data = Preference::first();
        $main = [
            'invoice_no' => $invoice_no,
            'uuid' => Auth::id(),
            'quotation_no' => $quotation_no,
            'preference_data' => $preference_data,
            'shipper' => $qt->shipper,
            'consignee' => $qt->consignee,
            'port_of_loading' => $qt->port_of_loading,
            'final_destination' => $qt->final_destination,
            'sailing_on' => $qt->sailing_on,
            'arriving_on' => $qt->arriving_on,
            'expiry' => $qt->expiry,
            'day' => $qt->expiry_days2,
        ];
        $ui = Userinformation::where('user_id', Auth::id())->first();
        //画面上の顧客情報用(base.blade.php)

        $user = [
            'user_id' => Auth::id(),
            'consignee' => $qt->consignee,
            'address_line1' => $ui->address_line1,
            'address_line2' => $ui->address_line2,
            'city' => $ui->city,
            'state' => $ui->state,
            'country' => User::where('id', Auth::id())->first()->country,
            'country_codes' => $ui->country_codes,
            'zip' => $ui->zip,
            'phone' => $ui->phone,
            'fax' => $ui->fax
        ];

        /*
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


        */
        $details = Quotation_detail::where('quotation_no', $quotation_no)->get();
        $items = [];
        foreach ($details as $detail) {
            $hinban = $detail->product_code;
            $hinmei = $detail->product_name;
            $tanka = $detail->unit_price;
            $tanka = (float)$tanka;
            $ctn = $detail->ctn;
            $ctn = (float)$ctn;
            $unit = $detail->unit;
            $unit = (int)$unit;
            $amaunt = $detail->amaunt;
            $dataset = array($hinban, $hinmei,$tanka, $ctn,$unit,$amaunt);
            array_push($items, $dataset);
        }
        $uuid=Auth::id();
        $ctn_total = $qt->ctn_total;
        $quantity_total = $qt->quantity_total;
        $amount_total = $qt->amount_total;
        $type = $qt->type;
        $expiry_days2 = $qt->expiry_days2;
        $total=[
            'ctn_total'=>$ctn_total,
            'quantity_total' => $quantity_total,
            'amount_total' => $amount_total
        ];

        return view('invoice', compact('main', 'items', 'total', 'user'));
    }


    //見積書PDFの出力(FORMからhidenでuuidを受け取る)
    public function generate_invoice_pdf(Request $request)
    {
        $type = session()->get('type');

        $main = [];
        //送信formから
        $quotation_no = $request->get('quotation_no');
        //Quotationから見積り内容をget
        $quotations = Quotation::where('quotation_no', $quotation_no)->get();

        //Preferenceから
        $preference_data = Preference::first();

        //インボイスナンバー
        $invoice = Invoice::where('quotation_no', $quotation_no)->first();
        $invoice_no = $invoice->invoice_no;

        //出力したものにチェックをつける
        $date = Carbon::now();
        \DB::table('invoices')->where('invoice_no', $invoice_no)->update(['create_PDF' => $date]);

        $day = $invoice->day;

        $shipper = $quotations[0]->shipper;

        $user_id = Auth::id();
        $Userinformations = User::find($user_id)->Userinformations;
        $consignee = $Userinformations->consignee;
        $address_line1 = $Userinformations->address_line1;
        $address_line2 = $Userinformations->address_line2;
        $city = $Userinformations->city;
        $state = $Userinformations->state;
        $zip = $Userinformations->zip;
        $phone = $Userinformations->phone;
        $fax = $Userinformations->fax;

        $User = User::find($user_id);
        $country = $User->country;

        $port_of_loading = $quotations[0]->port_of_loading;

        $final_destination = $quotations[0]->final_destination;

        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry_days2;

        $main = [
            'invoice_no' => $invoice_no,
            'quotation_no' => $quotation_no,
            'day' => $day,
            'preference_data' => $preference_data,
            'shipper' => $shipper,
            'consignee' => $consignee,
            'port_of_loading' => $port_of_loading,
            'final_destination' => $final_destination,
            'sailing_on' => $sailing_on,
            'arriving_on' => $arriving_on,
            'expiry' => $expiry,
            'address_line1' => $address_line1,
            'address_line2' => $address_line2,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'phone' => $phone,
            'fax' => $phone,
            'country' => $country

        ];
        $data = [];
        $items = [];

        $quotations_sub = Quotation_detail::where('quotation_no', $quotation_no)->get();

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
        $total = [
            'quantity_total' => $quantity_total,
            'ctn_total' => $ctn_total,
            'amount_total' => $amount_total
        ];

        //濱田氏サイン画像
        $image_path = storage_path('app/public/hamada.png');
        $image_data = base64_encode(file_get_contents($image_path));

        //レターヘッド画像
        $image_path = storage_path('app/public/head.png');
        $image_data2 = base64_encode(file_get_contents($image_path));


        $pdf = \PDF::loadView('invoice_print', compact('image_data', 'main', 'items', 'total', 'image_data2', 'type'))->setPaper('a4')->setWarnings(false);

        $output = $invoice_no . '.pdf';
        Storage::disk('public')->put('pdf/' . $output, $pdf->output());

        return $pdf->download($invoice_no . '.pdf');
    }

    public function generate_invoice_pdf2(Request $request)
    {
        $type = session()->get('type');

        //振込先情報をセッションに入れる
        $payee = Payment_method::where('selection', '選択')->get();

        session(['bank' => $payee[0]['bank']]);
        session(['branch' => $payee[0]['branch']]);
        session(['swift_code' => $payee[0]['swift_code']]);
        session(['account' => $payee[0]['account']]);
        session(['name' => $payee[0]['name']]);

        $main = [];
        //送信formから
        $quotation_no = $request->get('quotation_no');

        //Quotationから見積り内容をget
        $quotations = Invoice::where('quotation_no', $quotation_no)->get();
        $quotations_sub = Quotation_detail::where('quotation_no', $quotation_no)->get();
        $qt = Quotation::where('quotation_no', $quotation_no)->get();
        $shipper = $qt[0]['shipper'];
        $port_of_loading = $qt[0]['port_of_loading'];
        $final_destination = $qt[0]['final_destination'];
        $sailing_on = $qt[0]['sailing_on'];
        $arriving_on = $qt[0]['arriving_on'];
        $expiry = $qt[0]['expiry_days2'];
        //pdf作成日
        $day = Carbon::createFromFormat('Y-m-d H:i:s', $quotations[0]->created_at)->format('Y-m-d');
        $user_id = Auth::id();
        $Userinformations = Userinformation::where('user_id', $user_id)->get();
        $address_line1 = $Userinformations[0]['address_line1'];
        $address_line2 = $Userinformations[0]['address_line2'];
        $city = $Userinformations[0]['city'];
        $state = $Userinformations[0]['state'];
        $phone = $Userinformations[0]['phone'];
        $fax = $Userinformations[0]['fax'];

        $us = User::where('id', $user_id)->get();
        $country = $us[0]['country'];
        $invoice_no = $quotations[0]['invoice_no'];

        $ctn_total = $qt[0]['ctn_total'];

        $quantity_total = $qt[0]['quantity_total'];
        $amount_total = $qt[0]['amount_total'];

        $consignee = $Userinformations[0]['consignee'];
        $preference_data = "";

        $total = ['quantity_total' => $quantity_total, 'ctn_total' => $ctn_total, 'amount_total' => $amount_total, 'ctn_total' => $ctn_total];

        $main = [
            "quotation_no" => $quotation_no,
            "preference_data" => $preference_data,
            "shipper" => $shipper,
            "consignee" => $consignee,
            "port_of_loading" => $port_of_loading,
            "final_destination" => $final_destination,
            "sailing_on" => $sailing_on,
            "arriving_on" => $arriving_on,
            "expiry" => $expiry,
            "address_line1" => $address_line1,
            "address_line2" => $address_line2,
            "city" => $city,
            "state" => $state,
            "country" => $country,
            "phone" => $phone,
            "fax" => $fax,
            "invoice_no" => $invoice_no,
            "ctn_total" => $ctn_total,
            "quantity_total" => $quantity_total,
            "amount_total" => $amount_total
        ];
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
        //$image_path = storage_path('app/public/hamada.png');
        //$image_path = 'https://ccmedico.com/fedex/storage/premium-silk/hamada.png';
        $image_path = 'https://ccmedico.com/fedex/storage/hamada.png';
        $image_path = storage_path('app/public/hamada.png');
        $image_data = base64_encode(file_get_contents($image_path));

        //レターヘッド画像
        $image_path = storage_path('img/head.png');
        $image_path = storage_path('app/public/head.png');
        $image_data2 = base64_encode(file_get_contents($image_path));

        $output = $invoice_no . '.pdf';

        //pdf_printout.blade.phpを読み込む
        $pdf = \PDF::loadView('invoice_print', compact('image_data', 'main', 'items', 'total', 'quotation_no', 'image_data2', 'day', 'type'))->setPaper('a4')->setWarnings(false);

        //Storage::disk('public')->put('pdf/' . $output, $pdf->output());
        //Download
        //return $pdf->download($output);
        //プレビュー
        return $pdf->stream($output);
    }
}
