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
use App\Model\Image;
use App\Model\Order;
use App\Model\Order_confirm;
use App\Model\Order_detail;
use App\Model\Order_detail_confirm;
use App\Model\Order_number;
use App\Model\Product;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Model\Invoice_serial_number;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Model\SailingOn;
use App\Model\Expirie;
use App\Model\Etd;

use App\Model\Emailtext;

//自作したMailable クラス
use App\Mail\ThanksMail;
use App\Mail\PaymentImageMail;
use LDAP\Result;
use Mail;

class OrderController extends Controller
{
    // 受け取る変数
    public $user;
    public $content;

    //注文データをDBに登録
    public function order(Request $request)
    {
        $user_id = Auth::id();
        $main = [];
        $type = $request->type;
        //送信formから
        $quotation_no = $request->get('quotation_no');
        $final_destination = $request->get('final_destination');
        //Preferenceから
        $preference_data = Preference::first();
        //Quotationから見積り内容の行を取ってくる※
        $quotations = Quotation::where('quotation_no', $quotation_no)->get();


        //ここでエラー

        //複数行ある可能性があるので配列の1行目[0]から
        $shipper = $quotations[0]->shipper;
        $consignee_no = $quotations[0]->consignee_no;
        $consignee = Userinformation::where('user_id', $consignee_no)->first()->consignee;
        $port_of_loading = $quotations[0]->port_of_loading;
        $final_destination = $quotations[0]->final_destination;
        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry_days2;
        $delivery_method = $quotations[0]->delivery_method;



        $iv = Invoice::where('quotation_no', $quotation_no)->first();

        $invoice_no = $iv['invoice_no'];

        $uuid = $quotation_no;
        $day = $iv->day;

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
            'delivery_method' => $delivery_method
        ];

        //商品を配列$itemsにまとめる
        $quotations_sub = Quotation_detail::where('quotation_no', $quotation_no)->get();

        $data = [];
        $items = [];
        foreach ($quotations_sub as $quotation) {
            $product_code = $quotation->product_code;
            $product_name = $quotation->product_name;
            $quantity = $quotation->quantity;
            $ctn = $quotation->ctn;
            $quantity = $quotation->quantity;
            $unit = $quotation->unit;
            $unit_price = $quotation->unit_price;
            $amount = $quotation->amount;
            $data = [$product_code, $product_name, $quantity, $ctn, $unit_price, $amount, $unit];
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

        $ui = Userinformation::where('user_id', $user_id)->first();

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

        return view('order', compact('main', 'items', 'total', 'user', 'type'));
    }



    public function order_repeat(Request $request)
    {
        $order_no = $request->order_no;
        $order = Order_confirm::where('order_no', $order_no)->first();
        $quotation_no = $order->quotation_no;
        $quotations = Quotation::where('quotation_no', $quotation_no)->get();
        $quotation_details = Quotation_detail::where('quotation_no', $quotation_no)->get();
        //dd($quotation_details);
        $invoice_no = $order->invoice_no;
        $main['preference_data'] = Preference::first();
        $preference_data = Preference::first();
        $shipper = $quotations[0]->shipper;
        $consignee_no = $quotations[0]->consignee_no;
        $consignee = Userinformation::where('user_id', $consignee_no)->first()->consignee;
        $port_of_loading = $quotations[0]->port_of_loading;
        $final_destination = $quotations[0]->final_destination;
        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry_days2;
        $delivery_method = $quotations[0]->delivery_method;
        $iv = Invoice::where('quotation_no', $quotation_no)->first();

        $invoice_no = $iv['invoice_no'];

        $uuid = $quotation_no;
        $day = $iv->day;

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
            'delivery_method' => $delivery_method
        ];


        $user['address_line1'] = $order->address_line1;
        $user['address_line2'] = $order->address_line2;
        $user['city'] = $order->city;
        $user['state'] = $order->state;
        $user['country'] = $order->country;
        $user['zip'] = $order->zip;
        $user['phone'] = $order->phone;
        $user['fax'] = $order->fax;
        $request->session()->put('type', $order->payment_method);
        $type = $order->payment_method;

        $data = [];
        $items = [];
        foreach ($quotation_details as $quotation) {
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
        return view('order', compact('main', 'items', 'total', 'user', 'type'));
    }



    //注文書のアップロードフォーム画面
    public function order_confirm(Request $request)
    {
        $user_id = Auth::id();
        $main = [];
        $type = $request->type;
        //支払い方法
        $payment_method = $request->payment_method;
        //送信formから
        $quotation_no = $request->get('quotation_no');

        $final_destination = $request->get('final_destination');
        //Preferenceから
        $preference_data = Preference::first();
        //Quotationから見積り内容の行を取ってくる※
        $quotations = \App\Model\Quotation::where('quotation_no', $quotation_no)->get();
        //複数行ある可能性があるので配列の1行目[0]から
        $shipper = $quotations[0]->shipper;
        $consignee_no = $quotations[0]->consignee_no;
        $consignee = Userinformation::where('user_id', $consignee_no)->first()->consignee;
        $port_of_loading = $quotations[0]->port_of_loading;
        $final_destination = $quotations[0]->final_destination;
        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry;
        $delivery_method = $quotations[0]->delivery_method;
        $iv = Invoice::where('quotation_no', $quotation_no)->first();
        $invoice_no = $iv->invoice_no;
        $uuid = $quotation_no;
        $day = $iv->day;
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
            'delivery_method' => $delivery_method
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
            $unit = $quotation->unit;
            $data = [
                'product_code' => $product_code,
                'product_name' => $product_name,
                'quantity' => $quantity,
                'ctn' => $ctn,
                'unit_price' => $unit_price,
                'amount' => $amount,
                'unit' => $unit
            ];
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

        $ui = Userinformation::where('user_id', $user_id)->first();

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

        //order_noを作成
        $order = new Order_confirm();
        $order->user_id = Auth()->id();
        $latestOrder = Order::orderBy('created_at', 'DESC')->first();
        $latestOrder = Order_confirm::orderBy('created_at', 'DESC')->first();
        if ($latestOrder === null) {
            $order_number = '000001';
        } else {
            $order_number = str_pad($latestOrder->id + 1, 6, "0", STR_PAD_LEFT);
        }
        //dd($latestOrder->id);

        //order_confirmテーブルに登録
        $order = new \App\Model\Order_confirm();
        $order->order_no = $order_number;
        $order->quotation_no = $quotation_no;
        $order->invoice_no = $invoice_no;
        $order->user_id = $user_id;
        $order->day = $day;
        $order->order_date = date("Y-m-d H:i:s");
        $order->payment_method = $payment_method;

        $order->total_amount = $amount_total;
        $order->quantity_total = $quantity_total;
        $order->ctn_total = $ctn_total;

        //$order->shipping_method=$shipping_method;
        $order->consignee = $consignee;
        $order->country = User::where('id', $user_id)->first()->country;
        $order->address_line1 = $ui->address_line1;
        $order->address_line2 = $ui->address_line2;
        $order->city = $ui->city;
        $order->state = $ui->state;
        $order->zip = $ui->zip;
        $order->phone = $ui->phone;
        $order->fax = $ui->fax;
        $order->bill_company_address_line1 = $ui->bill_company_address_line1;
        $order->bill_company_address_line2 = $ui->bill_company_address_line2;
        $order->bill_company_city = $ui->bill_company_city;
        $order->bill_company_state = $ui->bill_company_state;
        $order->bill_company_country = $ui->bill_company_country;
        $order->bill_company_zip = $ui->bill_company_zip;
        $order->bill_company_phone = $ui->bill_company_phone;

        $order->president = $ui->president;
        $order->delivery_method = $delivery_method;

        $order->save();

        // 二重送信防止
        $request->session()->regenerateToken();

        if (Order::orderBy('created_at', 'DESC')->first()) {
            $latestOrder = Order::orderBy('created_at', 'DESC')->first();
            $latestOrder_id = $latestOrder->id;
        } else {
            $latestOrder_id = 0;
        }


        foreach ($items as $item) {
            //order_confirm明細テーブルに登録
            $order_detail = new \App\Model\Order_detail_confirm();
            $order_detail->order_id = $latestOrder_id;
            $order_detail->order_no = $order_number;
            $order_detail->product_code = $item['product_code'];
            $order_detail->product_name = $item['product_name'];
            $order_detail->quantity = $item['quantity'];
            $order_detail->ctn = $item['ctn'];
            $order_detail->quantity = $item['quantity'];
            $order_detail->unit_price = $item['unit_price'];
            $order_detail->amount = $item['amount'];
            $order_detail->unit = $item['unit'];
            //dd($item['unit']);


            $pd = Product::where('product_code', $item['product_code'])->first();
            $order_detail->weight_net = $pd->carton_weight_net;
            $order_detail->weight_gross = $pd->carton_weight_gross;

            $order_detail->total_weight_net = $pd->carton_weight_net * $item['ctn'];
            $order_detail->total_weight_gross = $pd->carton_weight_gross * $item['ctn'];

            $order_detail->carton_size_h = $pd->carton_size_h;
            $order_detail->carton_size_w = $pd->carton_size_w;
            $order_detail->carton_size_d = $pd->carton_size_d;

            $order_detail->volume_carton = $pd->carton_size_h * $pd->carton_size_w * $pd->carton_size_d / 1000 / 1000 / 1000;
            $order_detail->volume_total = $pd->carton_size_h * $pd->carton_size_w * $pd->carton_size_d / 1000 / 1000 / 1000 * $item['ctn'];

            $order_detail->product_color = $pd->color;
            $order_detail->category = $pd->category;
            $order_detail->item_group = $pd->group;
            $order_detail->fedex_goods_name = $pd->fedex_goods_name;
            //$order_detail->unit = $pd->unit;
            //$order_detail->unit = $pd->units;
            $order_detail->fedex_commercial_invoice_unit_value = $pd->fedex_commercial_invoice_unit_value;

            //2023 5-26

            //total_weight(箱数＊カートン数)
            $order_detail->total_weight = $pd->carton_weight_gross * $item['ctn'];
            //value_for_customs(1000円*本数)
            $order_detail->value_for_customs = $pd->fedex_commercial_invoice_unit_value * $item['quantity'];

            $order_detail->save();
            // 二重送信防止
            $request->session()->regenerateToken();
        }

        //payment_methodが未選択ならアラートを出してリダイレクト（引数に画面表示に必要な要素をcompactで渡す)
        if ($payment_method == null) {
            return redirect()->action('OrderController@order', compact('main', 'items', 'total', 'user', 'quotation_no', 'type', 'payment_method'))->with('note', 'Please select a payment method');
        }

        //商品番号とカートン数
        $cartons = [];
        $data = Order_detail_confirm::where('order_no', $order_number)->get();
        foreach ($data as $line) {
            array_push($cartons, $line->ctn);
        }

        $n = 1;
        $markes_numbers = [];
        foreach ($cartons as $cc) {
            //1回目
            if ($n == 1) {
                $ca = $cartons[0];
                if ($cartons[0] == 1) {
                    $markes_number = $ca;
                } else {
                    $markes_number = "1 - " . $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //2回目
            if ($n == 2) {
                $ca = $cartons[0] + 1;
                $nx = $cartons[0] + $cartons[1];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //3回目
            if ($n == 3) {
                $ca = $cartons[0] + $cartons[1] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }

            //4回目
            if ($n == 4) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //5回目
            if ($n == 5) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //6回目
            if ($n == 6) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //7回目
            if ($n == 7) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //8回目
            if ($n == 8) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //9回目
            if ($n == 9) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7] + $cartons[8];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //10回目
            if ($n == 10) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7] + $cartons[8] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7] + $cartons[8] + $cartons[9];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }

            //11回目
            if ($n == 11) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7] + $cartons[8] + $cartons[9] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7] + $cartons[8] + $cartons[9] + $cartons[10];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            $n = $n + 1;
        }


        $data = Order_detail_confirm::where('order_no', $order_number)->get();
        $x = 0;
        foreach ($data as $line) {
            if (isset($markes_numbers[$x])) {
                $line->marks_no = $markes_numbers[$x];
                $line->save();
                $x += 1;
            }
        }


        //packing list PDF
        $order_details = Order_detail_confirm::where('order_no', $order_number)->get();

        $pdf = new FPDI();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetCellPadding(0);
        $pdf->AddPage();
        $pdf->setSourceFile(public_path() . '/pdf-template/PackingList.pdf');
        $page = $pdf->importPage(1);
        $pdf->useTemplate($page);
        $pdf->SetFont('helvetica', '', 9);
        //No.
        $number = "×××××××";

        //コンサイニー
        $pdf->Text(77, 10, htmlspecialchars($consignee));
        $pdf->Text(77, 14, htmlspecialchars($ui->address_line1 . ',' . $ui->address_line2 . ',' . $ui->city . ',' . $ui->state . ',' . $ui->country_codes));
        $pdf->Text(77, 18, htmlspecialchars('tel: ' . $ui->phone . ' fax: ' . $ui->fax));
        //日付
        $pdf->Text(150, 18, htmlspecialchars($invoice_no));
        //インボイスNo
        $pdf->Text(150, 32, htmlspecialchars($order->order_date));

        $a = 58;
        $b = 56;
        $c = 48;
        $d = 53;
        $e = 58;
        $f = 62;

        $x = 0;

        $total_weight_net = 0;
        $total_weight_gross = 0;
        $volume_total = 0;
        foreach ($order_details as $order_detail) {
            if (isset($markes_numbers[$x])) {
                //1行目1列(Markes&Numbers) １行19づつ加算して下げる
                $pdf->Text(33, $a, htmlspecialchars("C/No. " . $markes_numbers[$x]));

                //1行目２列(No.of Package)
                $pdf->Text(66, $b, htmlspecialchars($order_detail->ctn . " cartons"));

                //1行目3列(Description )
                $pdf->Text(89, $c, htmlspecialchars($order_detail->category));
                $pdf->Text(89, $d, htmlspecialchars($order_detail->item_group));
                $pdf->Text(89, $e, htmlspecialchars('[' . $order_detail->product_code . ']'));
                $pdf->Text(89, $f, htmlspecialchars($order_detail->product_color));

                //1行目4列(WEIGHT)
                $pdf->Text(117, $c, htmlspecialchars($order_detail->weight_net) . 'kg');
                $pdf->Text(117, $d, htmlspecialchars('(NET)'));
                $pdf->Text(117, $e, htmlspecialchars($order_detail->weight_gross) . 'kg');
                $pdf->Text(117, $f, htmlspecialchars('(GROSS)'));

                //1行目5列(TOTAL WEIGHT)
                $pdf->Text(135, $c, htmlspecialchars($order_detail->total_weight_net) . 'kg');
                $pdf->Text(135, $d, htmlspecialchars('(NET)'));
                $pdf->Text(135, $e, htmlspecialchars($order_detail->total_weight_gross) . 'kg');
                $pdf->Text(135, $f, htmlspecialchars('(GROSS)'));
                $total_weight_net += $order_detail->total_weight_net;
                $total_weight_gross += $order_detail->total_weight_gross;

                //1行目6列(carton size)
                $pdf->Text(153, $c, htmlspecialchars('H' . $order_detail->carton_size_h));
                $pdf->Text(153, $d, htmlspecialchars('W' . $order_detail->carton_size_w));
                $pdf->Text(153, $e, htmlspecialchars('D' . $order_detail->carton_size_d));
                $pdf->Text(153, $f, htmlspecialchars('(mm)'));

                //1行目7列(volume)
                $pdf->Text(171, $c, htmlspecialchars(round($order_detail->volume_carton, 3) . ' M3'));
                $pdf->Text(171, $d, htmlspecialchars('(carton)'));
                $pdf->Text(171, $e, htmlspecialchars(round($order_detail->volume_total, 3) . ' M3'));
                $pdf->Text(171, $f, htmlspecialchars('(total)'));
                $volume_total += $order_detail->volume_total;

                $a = $a + 19;
                $b = $b + 19;
                $c = $c + 19;
                $d = $d + 19;
                $e = $e + 19;
                $f = $f + 19;

                $x += 1;
            }
        }
        //トータル
        $order = Order_confirm::where('order_no', $order_number)->first();

        $pdf->Text(66, 263, htmlspecialchars($order->ctn_total . ' cartons'));
        $pdf->Text(135, 261, htmlspecialchars($total_weight_net . 'kg'));
        $pdf->Text(135, 266, htmlspecialchars($total_weight_gross . 'kg'));
        $pdf->Text(171, 263, htmlspecialchars(round($volume_total, 3) . 'M3'));
        $pdf->Text(35, 273, htmlspecialchars($order->ctn_total . ' cartons'));

        $output = "PL" . $order_number . ".pdf";
        //PDF名を保存
        $order->packaging_list_pdf = "PL" . $order_number . ".pdf";
        $order->save();

        return view('order_confirm', compact('quotation_no', 'payment_method', 'order_number', 'invoice_no'));
    }


    public function order_confirm_repeat(Request $request)
    {
        dd("order_confirm_repeat");
    }

    //注文書画像の確認画面
    public function order_upload(Request $request)
    {
        $payment_method = $request->payment_method;
        $quotation_no = $request->get('quotation_no');
        $order_number = $request->get('order_number');
        $invoice_no = $request->invoice_no;

        //画像がセットされていれば
        $img = $request->file('img_path');

        if (isset($img)) {
            $quotation_no = $request->quotation_no;
            $invoice_no = $request->invoice_no;
            $order_number = $request->order_number;
            $payment_method = $request->payment_method;

            //画像の保存先フォルダ(order)がなければ作成
            $directory_path = storage_path() . '/app/public/order/';

            // ディレクトリ名
            $dir = 'order/';

            // アップロードされたファイル名を取得
            $file_name = $request->file('img_path')->getClientOriginalName();
            $file_name = time() . $file_name;

            // 取得したファイル名で保存
            $request->file('img_path')->storeAs('public/' . $dir, $file_name);

            // ファイル情報をDBに保存
            $image = new Image();
            $image->about = "order";
            $image->invoice_no = $invoice_no;
            $image->order_no = $order_number;
            $image->img_path = 'storage/' . $dir . $file_name;
            $image->save();

            //ccmap
            //$from= '/home/macintosh/www/fedex/public/storage/order/'.$file_name;
            //$to='/home/macintosh/www/ccmapp/public/storage/order/'.$file_name;
            //copy($from, $to);

            //お名前用
            //$from= '/home/r2325683/fedex/public/storage/order/'.$file_name;
            //$to='/home/r2325683/ccmapp/public/storage/order/'.$file_name;
            //copy($from, $to);


            if (strpos(__DIR__, 'MAMP') !== false) {
                $image->img_path = 'storage/' . $dir . $file_name;
                $from = '/Applications/MAMP/htdocs/fedex/storage/app/public/order/' . $file_name;
                $to = '/Applications/MAMP/htdocs/ccmapp/storage/app/public/order/' . $file_name;
                copy($from, $to);
            } elseif (strpos(__DIR__, 'xamp') !== false) {
                //windows local
                $from = 'C:\xampp\htdocs\fedex\storage\app\public\order\\' . $file_name;
                $to = 'C:\xampp\htdocs\ccmapp\storage\app\public\order\\' . $file_name;
                copy($from, $to);
            } elseif (strpos(__DIR__, 'macintosh') !== false) {
                //サクラ
                $from = '/home/macintosh/www/fedex/public/storage/order/' . $file_name;
                $to = '/home/macintosh/www/ccmapp/public/storage/order/' . $file_name;
                copy($from, $to);
            } elseif (strpos(__DIR__, '/Users/segawa/www/html/fedex-ccm/app/Http/Controllers') !== false) {
                //MacBook
                $from = '/Users/segawa/www/html/fedex-ccm/public/storage/order/' . $file_name;
                $to = '/Users/segawa/www/html/ccmapp/public/storage/order/' . $file_name;
                copy($from, $to);
            } else {
                //お名前用
                $from = '/home/r2325683/export/public/storage/order/' . $file_name;
                $to = '/home/r2325683/ccmapp/public/storage/order/' . $file_name;
                copy($from, $to);
            }


            return view('order_upload', compact('order_number', 'payment_method', 'file_name', 'invoice_no', 'directory_path'));
        } else {
            return redirect()->action('OrderController@order_confirm', compact('quotation_no', 'payment_method', 'order_number', 'invoice_no'))->with('error', 'Please select an image to upload');
        }
    }


    //入金書のアップロード画面(ここで注文をOrdersテーブルにインポート)
    public function order_payment(Request $request)
    {
        $order_number = $request->order_number;
        $order_conform = Order_confirm::where('order_no', $order_number)->get();
        $order_detail_confirms = Order_detail_confirm::where('order_no', $order_number)->get();
        //dd($order_detail_confirms);

        //orderテーブルに登録
        $order = new \App\Model\Order();
        $order->order_no = $order_conform[0]['order_no'];
        $order->quotation_no = $order_conform[0]['quotation_no'];
        $order->invoice_no = $order_conform[0]['invoice_no'];
        $invoice_no = $order_conform[0]['invoice_no'];

        $order->user_id = $order_conform[0]['user_id'];
        $order->day = $order_conform[0]['day'];
        $order->order_date = date("Y-m-d H:i:s");
        $order->payment_method = $order_conform[0]['payment_method'];

        //$order->total_amount = $order_conform[0]['amount_total'];
        $order->total_amount = $order_conform[0]['total_amount'];
        $order->quantity_total = $order_conform[0]['quantity_total'];
        $order->ctn_total = $order_conform[0]['ctn_total'];

        //$order->shipping_method=$shipping_method;

        $order->consignee = $order_conform[0]['consignee'];
        $order->country = $order_conform[0]['country'];
        $order->address_line1 = $order_conform[0]['address_line1'];
        $order->address_line2 = $order_conform[0]['address_line2'];
        $order->city = $order_conform[0]['city'];
        $order->state = $order_conform[0]['state'];
        $order->zip = $order_conform[0]['zip'];
        $order->phone = $order_conform[0]['phone'];
        $order->fax = $order_conform[0]['fax'];
        $order->bill_company_address_line1 = $order_conform[0]['bill_company_address_line1'];
        $order->bill_company_address_line2 = $order_conform[0]['bill_company_address_line2'];
        $order->bill_company_city = $order_conform[0]['bill_company_city'];
        $order->bill_company_state = $order_conform[0]['bill_company_state'];
        $order->bill_company_country = $order_conform[0]['bill_company_country'];
        $order->bill_company_zip = $order_conform[0]['bill_company_zip'];
        $order->bill_company_phone = $order_conform[0]['bill_company_phone'];

        $order->president = $order_conform[0]['president'];
        $order->delivery_method = $order_conform[0]['delivery_method'];

        //ステータスに「まだ出荷されていませんnot yet shipped」を入れる
        $order->status = "not yet shipped";

        $order->save();

        if (Order::orderBy('created_at', 'DESC')->first()) {
            $latestOrder = Order::orderBy('created_at', 'DESC')->first();
            $latestOrder_id = $latestOrder->id;
        } else {
            $latestOrder_id = 0;
        }


        $n = 0;
        foreach ($order_detail_confirms as $item) {
            //order明細テーブルに登録
            $order_detail = new \App\Model\Order_detail();
            $order_detail->order_id = $latestOrder_id;
            $order_detail->order_no = $item['order_no'];
            $order_detail->product_code = $item['product_code'];
            $order_detail->product_name = $item['product_name'];
            $order_detail->quantity = $item['quantity'];
            $order_detail->ctn = $item['ctn'];
            $order_detail->quantity = $item['quantity'];
            $order_detail->unit_price = $item['unit_price'];
            $order_detail->amount = $item['amount'];

            $order_detail->unit = $item['unit'];
            //dd($item['unit']);

            $pd = Product::where('product_code', $item['product_code'])->first();
            $order_detail->weight_net = $pd->carton_weight_net;
            $order_detail->weight_gross = $pd->carton_weight_gross;

            $order_detail->total_weight_net = $pd->carton_weight_net * $item['ctn'];
            $order_detail->total_weight_gross = $pd->carton_weight_gross * $item['ctn'];

            $order_detail->carton_size_h = $pd->carton_size_h;
            $order_detail->carton_size_w = $pd->carton_size_w;
            $order_detail->carton_size_d = $pd->carton_size_d;

            $order_detail->volume_carton = $pd->carton_size_h * $pd->carton_size_w * $pd->carton_size_d / 1000 / 1000 / 1000;
            $order_detail->volume_total = $pd->carton_size_h * $pd->carton_size_w * $pd->carton_size_d / 1000 / 1000 / 1000 * $item['ctn'];

            $order_detail->product_color = $pd->color;
            $order_detail->category = $pd->category;
            $order_detail->item_group = $pd->group;
            $order_detail->fedex_goods_name = $pd->fedex_goods_name;
            $order_detail->fedex_commercial_invoice_unit_value = $pd->fedex_commercial_invoice_unit_value;
            //$order_detail->unit = $pd->unit;
            $order_detail->save();
        }



        //商品番号とカートン数
        $cartons = [];
        $data = Order_detail::where('order_no', $order_number)->get();
        foreach ($data as $line) {
            array_push($cartons, $line->ctn);
        }

        $n = 1;
        $markes_numbers = [];
        foreach ($cartons as $cc) {
            //1回目
            if ($n == 1) {
                $ca = $cartons[0];
                if ($cartons[0] == 1) {
                    $markes_number = $ca;
                } else {
                    $markes_number = "1 - " . $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //2回目
            if ($n == 2) {
                $ca = $cartons[0] + 1;
                $nx = $cartons[0] + $cartons[1];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //3回目
            if ($n == 3) {
                $ca = $cartons[0] + $cartons[1] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }

            //4回目
            if ($n == 4) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //5回目
            if ($n == 5) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //6回目
            if ($n == 6) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //7回目
            if ($n == 7) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //8回目
            if ($n == 8) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //9回目
            if ($n == 9) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7] + $cartons[8];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            //10回目
            if ($n == 10) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7] + $cartons[8] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7] + $cartons[8] + $cartons[9];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }

            //11回目
            if ($n == 11) {
                $ca = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7] + $cartons[8] + $cartons[9] + 1;
                $nx = $cartons[0] + $cartons[1] + $cartons[2] + $cartons[3] + $cartons[4] + $cartons[5] + $cartons[6] + $cartons[7] + $cartons[8] + $cartons[9] + $cartons[10];
                if ($ca != $nx) {
                    $markes_number = $ca . " - " . $nx;
                } else {
                    $markes_number = $ca;
                }
                array_push($markes_numbers, $markes_number);
            }
            $n = $n + 1;
        }

        $data = Order_detail::where('order_no', $order_number)->get();
        $x = 0;
        foreach ($data as $line) {
            $line->marks_no = $markes_numbers[$x];
            $line->save();
            $x += 1;
        }


        //packing list PDF
        $order_details = Order_detail::where('order_no', $order_number)->get();

        $pdf = new FPDI();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetCellPadding(0);
        $pdf->AddPage();
        $pdf->setSourceFile(public_path() . '/pdf-template/PackingList.pdf');
        $page = $pdf->importPage(1);
        $pdf->useTemplate($page);
        $pdf->SetFont('helvetica', '', 9);
        //No.
        $number = "×××××××";

        //コンサイニー
        $pdf->Text(77, 10, htmlspecialchars($order_conform[0]['consignee']));
        $pdf->Text(77, 14, htmlspecialchars($order_conform[0]['address_line1'] . ',' . $order_conform[0]['address_line2'] . ',' . $order_conform[0]['city'] . ',' . $order_conform[0]['state'] . ',' . $order_conform[0]['country_codes']));
        $pdf->Text(77, 18, htmlspecialchars('tel: ' . $order_conform[0]['phone'] . ' fax: ' . $order_conform[0]['fax']));
        //日付
        $pdf->Text(150, 18, htmlspecialchars($order_conform[0]['invoice_no']));
        //インボイスNo
        $pdf->Text(150, 32, htmlspecialchars($order->order_date));

        $a = 58;
        $b = 56;
        $c = 48;
        $d = 53;
        $e = 58;
        $f = 62;

        $x = 0;

        $total_weight_net = 0;
        $total_weight_gross = 0;
        $volume_total = 0;
        foreach ($order_details as $order_detail) {
            //1行目1列(Markes&Numbers) １行19づつ加算して下げる
            $pdf->Text(33, $a, htmlspecialchars("C/No. " . $markes_numbers[$x]));

            //1行目２列(No.of Package)
            $pdf->Text(66, $b, htmlspecialchars($order_detail->ctn . " cartons"));

            //1行目3列(Description )
            $pdf->Text(89, $c, htmlspecialchars($order_detail->category));
            $pdf->Text(89, $d, htmlspecialchars($order_detail->item_group));
            $pdf->Text(89, $e, htmlspecialchars('[' . $order_detail->product_code . ']'));
            $pdf->Text(89, $f, htmlspecialchars($order_detail->product_color));

            //1行目4列(WEIGHT)
            $pdf->Text(117, $c, htmlspecialchars($order_detail->weight_net) . 'kg');
            $pdf->Text(117, $d, htmlspecialchars('(NET)'));
            $pdf->Text(117, $e, htmlspecialchars($order_detail->weight_gross) . 'kg');
            $pdf->Text(117, $f, htmlspecialchars('(GROSS)'));

            //1行目5列(TOTAL WEIGHT)
            $pdf->Text(135, $c, htmlspecialchars($order_detail->total_weight_net) . 'kg');
            $pdf->Text(135, $d, htmlspecialchars('(NET)'));
            $pdf->Text(135, $e, htmlspecialchars($order_detail->total_weight_gross) . 'kg');
            $pdf->Text(135, $f, htmlspecialchars('(GROSS)'));
            $total_weight_net += $order_detail->total_weight_net;
            $total_weight_gross += $order_detail->total_weight_gross;

            //1行目6列(carton size)
            $pdf->Text(153, $c, htmlspecialchars('H' . $order_detail->carton_size_h));
            $pdf->Text(153, $d, htmlspecialchars('W' . $order_detail->carton_size_w));
            $pdf->Text(153, $e, htmlspecialchars('D' . $order_detail->carton_size_d));
            $pdf->Text(153, $f, htmlspecialchars('(mm)'));

            //1行目7列(volume)
            $pdf->Text(171, $c, htmlspecialchars(round($order_detail->volume_carton, 3) . ' M3'));
            $pdf->Text(171, $d, htmlspecialchars('(carton)'));
            $pdf->Text(171, $e, htmlspecialchars(round($order_detail->volume_total, 3) . ' M3'));
            $pdf->Text(171, $f, htmlspecialchars('(total)'));
            $volume_total += $order_detail->volume_total;

            $a = $a + 19;
            $b = $b + 19;
            $c = $c + 19;
            $d = $d + 19;
            $e = $e + 19;
            $f = $f + 19;

            $x += 1;
        }
        //トータル
        $order = Order::where('order_no', $order_number)->first();

        $pdf->Text(66, 263, htmlspecialchars($order->ctn_total . ' cartons'));
        $pdf->Text(135, 261, htmlspecialchars($total_weight_net . 'kg'));
        $pdf->Text(135, 266, htmlspecialchars($total_weight_gross . 'kg'));
        $pdf->Text(171, 263, htmlspecialchars(round($volume_total, 3) . 'M3'));
        $pdf->Text(35, 273, htmlspecialchars($order->ctn_total . ' cartons'));


        $output = "PL" . $order_number . ".pdf";
        //PDF名を保存
        $order->packaging_list_pdf = "PL" . $order_number . ".pdf";
        $order->save();

        //メール文章(不要なタブが入らないように端に書く)
        /*
        $email_text = "";
        //メール送信
        $email = User::find($order->user_id)->email;
        $content = $email_text;
        $bcc = "info@lookingfor.jp";
        $content = $content . "\n\n1 Shipping address" ."2 Head office3 Payment method4 Items 5 Order total";
        Mail::to($email)->bcc($bcc)->send(new ThanksMail($content));
        */

        //見積もり有効期限
        $shipper = session()->get('shipper');
        $expiry_days = session()->get('expiry_days');

        //メール送信
        $to = User::find($order->user_id)->email;
        $bcc = "info@lookingfor.jp";
        //$bcc=session('adminmail');
        $subject = Emailtext::Find(1)->subject_1;

        //dd($order_conform[0]['port_of_loading']);
        $items = $order_detail_confirms;
        $content = [
            'contents' => Emailtext::Find(1)->contents_1,
            'shipper' => $shipper,
            'consignee' => $order_conform[0]['consignee'],
            'port_of_loading' => $order_conform[0]['port_of_loading'],
            'final_destination' => $order_conform[0]['final_destination'],
            'sailing_on' => $order_conform[0]['sailing_on'],
            'Arriving on' => '',
            'quotaition_deadline' => $expiry_days,
            'quantity_total' => $order_conform[0]['quantity_total'],
            'ctn_total' => $order_conform[0]['ctn_total'],
            'amount_total' => $order_conform[0]['total_amount'],
            'items' => $items
        ];
        //メール
        Mail::to($to)->bcc($bcc)->send(new ThanksMail($content, $subject, $items));
        return view('order_payment', compact('order_number', 'invoice_no'));
    }



    //入金書画像の確認とアップロード
    public function payment_upload(Request $request)
    {
        $payment_method = $request->payment_method;
        $quotation_no = $request->get('quotation_no');
        $order_number = $request->get('order_number');
        $invoice_no = $request->invoice_no;

        //画像がセットされていれば
        $img = $request->file('img_path');

        if (isset($img)) {
            $quotation_no = $request->quotation_no;
            $invoice_no = $request->invoice_no;
            $order_number = $request->order_number;
            $payment_method = $request->payment_method;

            //保存先フォルダがなければ作成
            $directory_path = storage_path() . '/app/public/order/';

            // ディレクトリ名
            $dir = 'order/';

            // アップロードされたファイル名を取得
            $file_name = $request->file('img_path')->getClientOriginalName();
            $file_name = time() . $file_name;

            // 取得したファイル名で保存
            $request->file('img_path')->storeAs('public/' . $dir, $file_name);

            // ファイル情報をDBに保存
            $image = new Image();
            $image->about = "payment";
            $image->invoice_no = $invoice_no;
            $image->order_no = $order_number;
            $image->img_path = 'storage/' . $dir . $file_name;
            $image->save();

            if (strpos(__DIR__, 'MAMP') !== false) {
                //mac
                $from = '/Applications/MAMP/htdocs/fedex/storage/app/public/order/' . $file_name;
                $to = '/Applications/MAMP/htdocs/ccmapp/storage/app/public/order/' . $file_name;
                copy($from, $to);
            } elseif (strpos(__DIR__, 'xamp') !== false) {
                //windows local
                $from = 'C:\xampp\htdocs\fedex\storage\app\public\order\\' . $file_name;
                $to = 'C:\xampp\htdocs\ccmapp\storage\app\public\order\\' . $file_name;
                copy($from, $to);
            } elseif (strpos(__DIR__, 'macintosh') !== false) {
                //サクラ
                $from = '/home/macintosh/www/fedex/public/storage/order/' . $file_name;
                $to = '/home/macintosh/www/ccmapp/public/storage/order/' . $file_name;
                copy($from, $to);
            } elseif (strpos(__DIR__, '/Users/segawa/www/html/fedex-ccm/app/Http/Controllers') !== false) {
                //MacBook
                $from = '/Users/segawa/www/html/fedex-ccm/public/storage/order/' . $file_name;
                $to = '/Users/segawa/www/html/ccmapp/public/storage/order/' . $file_name;
                copy($from, $to);
            } else {
                //お名前用
                $from = '/home/r2325683/fedex/public/storage/order/' . $file_name;
                $to = '/home/r2325683/ccmapp/public/storage/order/' . $file_name;
                copy($from, $to);
            }

            return view('payment_upload', compact('order_number', 'payment_method', 'file_name', 'invoice_no', 'directory_path'));
        } else {
            return redirect()->action('OrderController@order_confirm', compact('quotation_no', 'payment_method', 'order_number', 'invoice_no'))->with('error', 'Please select an image to upload');
        }
    }


    //注文完了(入金画像をアップロード終了)
    public function order_complete(Request $request)
    {
        $order_no = request('order_number');
        $order = Order_confirm::where('order_no', $order_no)->first();

        //メール送信
        $to = User::find($order->user_id)->email;
        $bcc = "info@lookingfor.jp";
        //$bcc=session('adminmail');
        $subject = Emailtext::Find(1)->subject_2;
        $content = [
            'contents' => Emailtext::Find(1)->contents_2,
        ];
        //メール
        Mail::to($to)->bcc($bcc)->send(new PaymentImageMail($content, $subject));

        return view("order_complete", compact('order_no'));
    }




    //マイページから送金書をアップするための画面へ
    public function payment_uploader(Request $request)
    {
        $order_no = request('order_no');
        return view("account/payment_uploader", compact('order_no'));
    }


    //マイページから送金書をアップ実行
    public function payment_up(Request $request)
    {
        $order_no = $request->order_no;

        //画像がセットされていれば
        $img = $request->file('img_path');


        if (isset($img)) {
            $quotation_no = Order::where('order_no', $order_no)->first()->quotation_no;
            $invoice_no = Order::where('order_no', $order_no)->first()->invoice_no;
            $payment_method = Order::where('order_no', $order_no)->first()->payment_method;

            //保存先フォルダがなければ作成
            $directory_path = storage_path() . '/app/public/order/';

            // ディレクトリ名
            $dir = 'order/';

            // アップロードされたファイル名を取得
            $file_name = $request->file('img_path')->getClientOriginalName();
            $file_name = time() . $file_name;

            // 取得したファイル名で保存
            $request->file('img_path')->storeAs('public/' . $dir, $file_name);

            // ファイル情報をDBに保存
            $image = new Image();
            $image->about = "payment";
            $image->invoice_no = $invoice_no;
            $image->order_no = $order_no;
            $image->img_path = 'storage/' . $dir . $file_name;
            $image->save();

            if (strpos(__DIR__, 'MAMP') !== false) {
                //mac
                $from = '/Applications/MAMP/htdocs/fedex/storage/app/public/order/' . $file_name;
                $to = '/Applications/MAMP/htdocs/ccmapp/storage/app/public/order/' . $file_name;
                copy($from, $to);
            } elseif (strpos(__DIR__, 'xamp') !== false) {
                //Windows
                $from = 'C:\xampp\htdocs\fedex\storage\app\public\order\\' . $file_name;
                $to = 'C:\xampp\htdocs\ccmapp\storage\app\public\order\\' . $file_name;
                copy($from, $to);
            } elseif (strpos(__DIR__, 'macintosh') !== false) {
                //サクラ
                $from = '/home/macintosh/www/fedex/public/storage/order/' . $file_name;
                $to = '/home/macintosh/www/ccmapp/public/storage/order/' . $file_name;
                copy($from, $to);
            } elseif (strpos(__DIR__, '/Users/segawa/www/html/fedex-ccm/app/Http/Controllers') !== false) {
                //MacBook
                $from = '/Users/segawa/www/html/fedex-ccm/public/storage/order/' . $file_name;
                $to = '/Users/segawa/www/html/ccmapp/public/storage/order/' . $file_name;
                copy($from, $to);
            } else {
                //お名前用
                $from = '/home/r2325683/fedex/public/storage/order/' . $file_name;
                $to = '/home/r2325683/ccmapp/public/storage/order/' . $file_name;
                copy($from, $to);
            }
        }
    }


    public function ThanksMail(Request $request)
    {
        $id = $request->id;
        $users = User::find($id);
        $to = $users->email;
        $content = "ここに本文";
        //メール送信処理
        Mail::to($to)->send(new ThanksMail($content));
    }

    public function packing_list()
    {
        //packing list
        $pdf = new FPDI();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->setSourceFile(public_path() . '/pdf-template/PackingList.pdf');
        $page = $pdf->importPage(1);
        $pdf->useTemplate($page);
        $pdf->SetFont('helvetica', '', 9);
        //No.
        $number = "×××××××";


        //コンサイニー
        $pdf->Text(77, 10, htmlspecialchars($consignee));
        $pdf->Text(77, 14, htmlspecialchars($ui->address_line1));
        $pdf->Text(77, 18, htmlspecialchars($ui->phone));
        //日付
        $pdf->Text(150, 18, htmlspecialchars($invoice_no));
        //インボイスNo
        $pdf->Text(150, 32, htmlspecialchars($order->order_date));


        //1行目1列(Markes&Numbers)
        $pdf->Text(33, 58, htmlspecialchars($number));
        //1行目２列(No.of Package)
        $pdf->Text(66, 56, htmlspecialchars($number));
        //1行目3列(Description )
        $pdf->Text(88, 48, htmlspecialchars($number));
        $pdf->Text(88, 53, htmlspecialchars($number));
        $pdf->Text(88, 58, htmlspecialchars($number));
        //1行目4列(WEIGHT)
        $pdf->Text(115, 48, htmlspecialchars($number));
        $pdf->Text(115, 53, htmlspecialchars($number));
        $pdf->Text(115, 58, htmlspecialchars($number));
        $pdf->Text(115, 63, htmlspecialchars($number));
        //1行目5列(TOTAL WEIGHT)
        $pdf->Text(134, 48, htmlspecialchars($number));
        $pdf->Text(134, 53, htmlspecialchars($number));
        $pdf->Text(134, 58, htmlspecialchars($number));
        $pdf->Text(134, 63, htmlspecialchars($number));
        //1行目6列(carton size)
        $pdf->Text(151, 48, htmlspecialchars($number));
        $pdf->Text(151, 53, htmlspecialchars($number));
        $pdf->Text(151, 58, htmlspecialchars($number));
        $pdf->Text(151, 63, htmlspecialchars($number));
        //1行目7列(volume)
        $pdf->Text(169, 48, htmlspecialchars($number));
        $pdf->Text(169, 53, htmlspecialchars($number));
        $pdf->Text(169, 58, htmlspecialchars($number));
        $pdf->Text(169, 63, htmlspecialchars($number));


        $pdf->Output("output.pdf", "I");
    }

    //注文書PDFを表示
    public function ShowOrderSheet(Request $request)
    {
        $order_no = $request->order_no;
        //降順にソートしてその先頭（Last）を取り出す
        //$imagecollection = Image::orderBy('created_at', 'desc')->where('order_no', '=', $order_no)->first();
        $imagecollection = Image::where('order_no', '=', $order_no)->where('about', '=', 'order')->orderBy('created_at', 'desc')->first();
        $pdf_name = $imagecollection->img_path;
        return redirect(asset($pdf_name));
    }

    //入金書PDFを表示
    public function ShowPaymentSheet(Request $request)
    {
        $order_no = $request->order_no;
        //降順にソートしてその先頭（Last）を取り出す
        $imagecollection = Image::where('order_no', '=', $order_no)->where('about', '=', 'payment')->orderBy('created_at', 'desc')->first();
        $pdf_name = $imagecollection->img_path;
        return redirect(asset($pdf_name));
    }

    public function ShowPackinglist(Request $request)
    {
    }

}
