<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\Product;
use App\Model\Limit;
use App\Model\Preference;
use App\Model\Quotation;
use App\Model\Quotation_detail;
use App\Model\Userinformation;
use App\Model\Payment_method;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Model\Quitation_serial_number;
use Illuminate\Support\Facades\Validator;

use App\Model\SailingOn;
use App\Model\Expirie;
use App\Model\Etd;

use App\Mail\QuotationMail;
use Mail;
use App\Model\Emailtext;
use App\Model\Order;

class QuotationController extends Controller
{
    public function quotation(Request $request)
    {
        if (session('article') == "") {
            session()->put(['article' => 'Air Stocking']);
        }
        //カテゴリーのユニークだけ(ここではAIRSTOCKINGだけだが、今後ネールなどが入ってくる) session('article')は'Air Stocking'など
        $categorys = Product::where('hidden_item', '!=', '1')->where('category', session('article'))->groupBy('category')->orderBy('sort_order', 'asc')->get(['category']);

        //Air Stocking中分類 session('article')は'Air Stocking'など
        $groups = Product::where('hidden_item', '!=', '1')->where('category', session('article'))->groupBy('group')->orderBy('sort_order', 'asc')->get(['group']);

        //グループ別の商品配列
        $items = [];
        foreach ($groups as $g) {
            $b = Product::where('hidden_item', '!=', '1')->where('group', $g->group)->orderBy('sort_order', 'asc')->get();
            array_push($items, $b);
        }
        //dd($items[0][0]['group'])==PREMIUM-SILK; 形式の配列;
        $groups = [];
        foreach ($items as $item) {
            foreach ($item as $val) {
                array_push($groups, $val->group);
                $groups = array_unique($groups);
            }
        }

        //配列に全部の商品コード(PS01,PS02...)を取り出す
        $codes = [];
        foreach ($items as $item) {
            foreach ($item as $val) {
                $hoge = [$val->product_code => $val->group];
                $codes = array_merge($codes, $hoge);
            }
        }

        $type = $request->type;
        //HTMLフォーム送信のnameがitemのものだけ取得
        $type = session()->get('type');

        /*
        if($type=="fedex"){
            $arriving_on = "Typical 7-10 days once order in confirmed";
        }
        elseif($type=="air"){
            $arriving_on = "Typical 7-14 days by Air Cargo";
        }
        elseif($type=="ship"){
            $arriving_on = "Typical 1-2 months for Ship";
        }
        */



        //数量の制限値
        $fedex = Limit::where('Delivery_type', '=', 'fedex')->first(); //10～100
        $air1 = Limit::where('Delivery_type', '=', 'air1')->first(); //101～200
        $air2 = Limit::where('Delivery_type', '=', 'air2')->first(); //201～500
        $ship = Limit::where('Delivery_type', '=', 'ship')->first(); //501～

        $fedex_low = $fedex->lower_limit;
        $fedex_up  = $fedex->upper_limit;
        $fedex_min = $fedex->Minimum_orders;

        $air1_low = $air1->lower_limit;
        $air1_up  = $air1->upper_limit;
        $air1_min = $air1->Minimum_orders;

        $air2_low = $air2->lower_limit;
        $air2_up  = $air2->upper_limit;
        $air2_min = $air2->Minimum_orders;

        $ship_low = $ship->lower_limit;
        $ship_up  = $ship->upper_limit;
        $ship_min = $ship->Minimum_orders;

        $GOODS = [];

        foreach ($codes as $key => $val) {
            //全角数字を半角に
            $GOODS[$key] = mb_convert_kana($request->get($key), "n");
        }

        //アイテムごとを1つづつの配列に変換
        $array = [];
        foreach ($GOODS as $key => $val) {
            $ps = $codes[$key];
            $array[] = [$ps, $val];
        }

        //各グループ別の集計
        foreach ($groups as $value) {
            $num = 0;
            foreach ($array as $row) {
                if ($row[0] == $value) {
                    $num = $num + intval($row[1]);
                }
            }
            $group_total[$value] = $num;
        }


        /*エラーメッセージ作成 */
        $err = array();
        $err1 = array();
        $err2 = array();

        if ($type == "fedex") {
            //fedexは各アイテムの在庫数以上の注文はエラーにする
            foreach ($GOODS as $key => $val) {
                $zaiko = Product::whereproduct_code($key)->first()->stock;
                if ($val > $zaiko) {
                    //$err=array($key.'は'.$zaiko.'より少なくしてください');
                    $err = array($key . ' should be less than ' . $zaiko);
                    $err1 = (array_merge($err1, $err));
                }
            }
            //各行の少計数が最低最大に収まっているか
            foreach ($group_total as $val) {
                if ($val >= 1 and $val < $fedex_low or $val > $fedex_up) {
                    //$err2=array('1アイテムの合計数が'.$err[]=$fedex_low.'以上'.$fedex_up.'以下になるようにしてください');
                    $err2 = array('Make sure the total cartons of items is between ' . $fedex_low . 'and ' . $fedex_up);
                }
            }
            $err = (array_merge($err1, $err2));
            $route_name = "fedex";
            if (!empty($err)) {
                return redirect()->route($route_name)->with('flash_message', implode('<br>', $err))->withInput();
            }
        }

        if ($type == "air") {
            foreach ($GOODS as $key => $val) {
                //空欄は無視して最低数より少ない場合（1アイテムは最低20）
                if ($val >= 1 and $val < $air1_min) {
                    //$err=array($key.'は'.$air1_min.'より多くしてください');
                    $name = Product::where('product_code', '=', $key)->first();
                    $err = array('[' . $key . '] ' . $name->kind . ' should be more than ' . $air1_min);
                    $err1 = (array_merge($err1, $err));
                }
            }
            //各行の少計数が最低最大に収まっているか
            foreach ($group_total as $val) {
                if ($val >= 1 and $val < $air1_low or $val > $air2_up) {
                    //$err2=array('1アイテムの合計数が'.$air1_low.'以上'.$air2_up.'以下になるようにしてください');
                    $err2 = array('Make sure the total cartons of items is between ' . $air1_low . ' and ' . $air2_up);
                }
            }
            /*配列に追加*/
            $err = (array_merge($err1, $err2));
            $route_name = "air";
            if (!empty($err)) {
                return redirect()->route($route_name)->with('flash_message', implode('<br>', $err))->withInput();
            }
        }

        //初期化
        $array_sum=0;

        if ($type == "ship") {
            foreach ($GOODS as $key => $val) {
                //空欄は無視して最低数より少ない場合（1アイテムは最低40）
                if ($val >= 1 and $val < $ship_min) {
                    //$err=array($key.'は'.$ship_min.'より多くしてください');
                    $name = Product::where('product_code', '=', $key)->first();
                    $err = array('[' . $key . '] ' . $name->kind . ' should be more than ' . $ship_min);
                    //$err=array($key.' should be more than '.$ship_min);
                    $err1 = (array_merge($err1, $err));
                }
            }
            //各行の少計数が最低最大に収まっているか

            /* 2023-11-8
            foreach ($group_total as $val) {
                if ($val >= 1 and $val < $ship_low or $val > $ship_up) {
                    //$err2=array('1アイテムの合計数が'.$ship_low.'以上'.$ship_up.'以下になるようにしてください');
                    $err2 = array('Make sure the total cartons of items is ' . $ship_low . ' or more');
                }
            }
            */
            //2023-11-8 各行ではなく全体の数が500以下の場合エラーに変更
            $array_sum = array_sum($group_total);
            if ($array_sum >= 1 and $array_sum < $ship_low or $array_sum > $ship_up) {
                //$err2=array('1アイテムの合計数が'.$ship_low.'以上'.$ship_up.'以下になるようにしてください');
                $err2 = array('Make sure the total cartons of items is ' . $ship_low . ' or more');
            }

            /*配列に追加*/
            $err = (array_merge($err1, $err2));
            $route_name = "ship";
            $err = implode("<br>", $err);
            if (!empty($err)) {
                return redirect()->route($route_name)->with('flash_message', $err)->withInput();
            }
        }

        //単価を出す関数
        //$type(fedex,air,ship) group_total(PREMIUM-SILK=>200,PREMIUM-SILK QT=>0,DIAMOND LEGS=>300,DIAMOND LEGS DQ) $total(groupの各合計)
        function which_tanka2($type, $group, $total, $fedex_low, $fedex_up, $air1_low, $air1_up, $air2_low, $air2_up, $ship_low, $ship_up,$array_sum)
        {
            if ($type == "fedex" and $total >= 1 and $total <= $fedex_up) {
                $tanka = Product::where('group', '=', $group)->first()->price_fedex;
                return $tanka;
            } elseif ($type == "air" and $total >= $air1_low and $total <= $air1_up) {
                $tanka = Product::where('group', '=', $group)->first()->price_air_1;
                return $tanka;
            } elseif ($type == "air" and $total >= $air2_low and $total <= $air2_up) {
                $tanka = Product::where('group', '=', $group)->first()->price_air_2;
                return $tanka;
            //} elseif ($type == "ship" and $total >= $ship_low and $total <= $ship_up) {
            } elseif ($type == "ship" and $array_sum >= $ship_low and $array_sum <= $ship_up) {
                
                $tanka = Product::whereGroup($group)->first()->price_ship;
                return $tanka;
            }
        }

        function set_item($hinban, $ctn, $tanka, $hinmei, $unit)
        {
            $quantity_total = 0;
            $amount_total = 0;
            $units = Product::whereProduct_code($hinban)->first()->units;
            $quantity = $ctn * $units; //本数(カートン数*$units本)
            $amount = $quantity * $tanka; //金額＝数量＊単価
            $quantity_total += $quantity; //本数合計
            $amount_total += $amount; //金額合計
            $unit = $unit; //ユニット数
            $data = [$hinban, $hinmei, $tanka, $ctn, $quantity, $amount, $unit];
            return $data;
        }

        $items = [];

        //$GOODSは　array:20 ["PS01" => "10","PS02" => "",......]　どの商品番号が何カートンを入れた配列

        foreach ($GOODS as $key => $val) {
            if ($val != "") {
                $hinban = $key; //品番PS01
                $group = Product::whereProduct_code($hinban)->first()->group; //PREMIUM-SILK
                $total = $group_total[$group]; //行の合計
                $ctn = $val; //PS01のカートン数
                $temp = Product::whereProduct_code($hinban)->first(); //PS01の商品詳細
                $hinmei = $temp->category . ' ' . $temp->group . ' ' . $temp->kind;
                $tanka = which_tanka2($type, $group, $total, $fedex_low, $fedex_up, $air1_low, $air1_up, $air2_low, $air2_up, $ship_low, $ship_up,$array_sum);
                $unit = $temp->units;
                $data = set_item($hinban, $ctn, $tanka, $hinmei, $unit);
                array_push($items, $data);
            }
        }
        //dd($items);

        //全アイテムのカートン総数
        $ctn_total = 0;
        foreach ($group_total as $key => $val) {
            $ctn_total += $val;
        }
        //数量合計（カートン数*ユニット数）
        $quantity_total = 0;
        foreach ($items as $item) {
            $quantity_total += $item[4];
        }
        //合計金額
        $amount_total = 0;
        foreach ($items as $val) {
            $amount_total += $val[5];
        }

        //アイテムをセッションに入れた
        session()->put('items', $items);
        session()->put('ctn_total', $ctn_total);
        session()->put('quantity_total', $quantity_total);
        session()->put('amount_total', $amount_total);

        $etd = Etd::all();
        $air_etd = $etd[0]->air;
        $ship_etd = $etd[0]->ship;

        if ($type == "fedex") {
            $addday = Etd::find(1)->fedex;
            $sailing_on = "1-2 days after order confirmed";
            $arriving_on = "Typical 7-10 days once order in confirmed";
        } elseif ($type == "air") {
            $addday = Etd::find(1)->air;
            $sailing_on = "After order confirmation " . $air_etd . "days";
            $arriving_on = "Typical 7-14 days by Air Cargo";
        } elseif ($type == "ship") {
            $addday = Etd::find(1)->ship;
            $sailing_on = "After order confirmation " . $ship_etd . "days";
            $arriving_on = "Typical 1-2 months for Ship";
        }


        //Sailing on(出航予定月)
        //$addday=SailingOn::find(1)->number_of_days;
        //現在の日付
        $date = new Carbon('today');
        //40日後
        $date = $date->addDay($addday);

        $year = $date->format('Y');
        $month = $date->format('M');
        //$sailing_on = $month . ',' . $year;
        //session()->put('sailing_on',$sailing_on);



        $date = new Carbon('today');
        //7日後
        $date = $date->addDay(7);

        $year = $date->format('Y');
        $month = $date->format('M');
        $day = $date->format('d');
        $quotation_valid = $month . ' ' . $day . ' ,' . $year;
        session()->put('quotation_valid', $quotation_valid);


        //ユニークキー（見積番号）を作成
        $uuid = strtoupper(uniqid());

        $preference_data = Preference::first();
        $user_id = Auth::id();

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

        //quotations(見積もり)テーブルにデータを作成する
        $db = new Quotation();

        //見積書番号作成
        $serial_number = new Quitation_serial_number();

        $latestOrder = Quitation_serial_number::orderBy('created_at', 'DESC')->first();
        if ($latestOrder === null) {
            $qt_number = '0001';
        } else {
            $qt_number = str_pad($latestOrder->id + 1, 4, "0", STR_PAD_LEFT);
        }

        $quotation_no = 'quitation_' . $qt_number;
        $db->quotation_no = $quotation_no;

        $serial_number->pdf_file_name = $quotation_no . '.pdf';
        $serial_number->user_id = $user_id;
        $serial_number->save();

        $shipper = $preference_data->shipper;

        $db->date_of_issue = Carbon::now();
        $db->shipper = $shipper;
        $db->consignee_no = $user_id;
        //SELECT * FROM `userinformations` WHERE `user_id` = 16
        $Userinformations = User::find($user_id)->Userinformations;
        $consignee = $Userinformations->consignee;
        $db->consignee = $consignee;


        $db->port_of_loading = $preference_data->port_of_loading;
        $db->sailing_on = $sailing_on;
        $db->expiry = $expiry_days;
        $db->expiryaddday = $expiryaddday;
        $db->shipping = $expiryaddday;
        $db->arriving_on = $arriving_on;
        $db->expiry_days2 = $expiry_days2;
        $db->type = $type;

        $db->quantity_total = $quantity_total;
        $db->ctn_total = $ctn_total;
        $db->amount_total = $amount_total;

        //session()->put('shipper',$preference_data->shipper);


        //初回の人はまだこの時点ではconsigneeデータがない
        if ($db->consignee) {
            $db->consignee = $consignee;
        }
        if ($db->final_destination) {
            $db->final_destination = $state . ',' . $country;
        }

        //配送方法
        $db->delivery_method = $type;


        //$serial_number
        $kizon = Quotation::where('quotation_no', $serial_number)->get();
        //quotations(見積もり)テーブルに保存
        if ($kizon == "") {
            //$db->save();
        }
        $db->save();

        foreach ($items as $item) {
            //見積もり明細テーブルに登録
            $sub = new Quotation_detail();
            $sub->quotation_id = $user_id;

            $sub->product_code = $item[0]; //"QT02"
            $sub->product_name = $item[1]; //"Air Stocking PREMIUM-SILK QT Natural"
            $sub->unit_price = $item[2]; //10.0
            $sub->ctn = $item[3]; //10
            $sub->quantity = $item[4]; //480
            $sub->amount = $item[5]; //4800
            $sub->unit = $item[6];
            $sub->quotation_no = $quotation_no;
            $sub->quotation_id = $db->id;
            $sub->save();

            // 二重送信防止
            //$request->session()->regenerateToken();
        }
        //Userinformationsテーブルからマスターのidと同じuser_idを探し住所等を取り出す
        $Userinformations = User::find($user_id)->Userinformations;

        //Userinformationsがnullの場合（住所登録が住んでいない場合）なら、quotation_noを持たせて住所入力フォームへ移動
        if ($Userinformations == null) {
            return view('entryform', compact('uuid', 'user_id', 'quotation_no'));
        }

        //住所登録が済んでいる場合
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
            'address_line2' => $address_line2, 'city' => $city, 'state' => $state, 'country' => $country,
            'country_codes' => $country_codes, 'phone' => $phone, 'fax' => $fax, 'delivery_method' => $type,
            'quotation_no' => $quotation_no
        );
        //セッションにitemsを持たす
        $collection = collect($items);
        session()->put('items', $collection);


        $shipper = $preference_data->shipper;
        $port_of_loading = $preference_data->port_of_loading;
        $final_destination = $state . ',' . $country;

        //quoatationメール送信
        $to = User::find($user_id)->email;
        //2023-5-12 ログイン時にセッション化したメールを使うように変更
        //$to=session()->get('email');
        //$bcc="info@lookingfor.jp";
        $bcc = session('adminmail');
        $bcc = 'info@lookingfor.jp';

        $subject = Emailtext::Find(1)->subject_4;
        $content = [
            'contents' => Emailtext::Find(1)->contents_4,
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

        //dd($to,$bcc,$subject,$content);

        //見積もりメール
        Mail::to($to)->bcc($bcc)->send(new QuotationMail($content, $subject, $items));

        return view('quotation', compact('uuid', 'preference_data', 'items', 'ctn_total', 'quantity_total', 'amount_total', 'sailing_on', 'user', 'quotation_no', 'type', 'expiry_days2', 'shipper', 'consignee', 'port_of_loading', 'arriving_on'));
    }

    //マイページから再度表示へ
    public function quotation_repeat(Request $request)
    {
        $quotation_no = $request->quotation_no;
        $data = Quotation::where('quotation_no', $quotation_no)->first();
        $preference_data = Preference::first();
        $shipper    = $data->shipper;
        $consignee_no = $data->consignee_no; //$user_idのこと
        $consignee = $data->consignee;
        $port_of_loading = $data->port_of_loading;
        $final_destination = $data->final_destination; //null
        $sailing_on = $data->sailing_on;
        $arriving_on = $data->arriving_on; //null
        $expires = $data->expires; //null

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
            $dataset = array($hinban, $hinmei, $tanka, $ctn, $unit, $amaunt);
            array_push($items, $dataset);
        }
        $uuid = "";
        $ctn_total = $data->ctn_total;
        $quantity_total = $data->quantity_total;
        $amount_total = $data->amount_total;
        $user = Auth::id();
        $type = $data->type;
        $expiry_days2 = $data->expiry_days2;
        $user = "";

        return view('quotation', compact('uuid', 'preference_data', 'items', 'ctn_total', 'quantity_total', 'amount_total', 'sailing_on', 'user', 'quotation_no', 'type', 'expiry_days2', 'shipper', 'consignee', 'port_of_loading', 'arriving_on'));
    }

    //見積書PDFの出力(FORMからhidenでuuidを受け取る)
    public function generate_quotation_pdf(Request $request)
    {
        $type = session()->get('type');
        if ($type == "air") {
            $type = "aircargo";
        } elseif ($type == "ship") {
            $type = "shipcontainer";
        }

        $main = [];
        //送信formから
        $quotation_no = $request->get('quotation_no');

        //Quotationから見積り内容をget
        $quotations = Quotation::where('quotation_no', $quotation_no)->get();
        $quotations_sub = Quotation_detail::where('quotation_no', $quotation_no)->get();

        //送信formから
        $final_destination = $request->final_destination;
        //データベースに保存
        \DB::table('quotations')->where('quotation_no', $quotation_no)->update(['final_destination' => $final_destination]);

        //出力したものにチェックをつける
        $date = Carbon::now();
        \DB::table('quotations')->where('quotation_no', $quotation_no)->update(['create_PDF' => $date]);
        //Preferenceから
        $shipper = $quotations[0]->shipper;

        //consignee
        $user_id = Auth::id();
        $Userinformations = User::find($user_id)->Userinformations;
        $consignee = $Userinformations->consignee;
        $port_of_loading = $quotations[0]->port_of_loading;
        $final_destination = $final_destination;
        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        //$expiry = $quotations[0]->expiry;
        $expiry = $quotations[0]->expiry_days2;
        //dd($quotations[0]->expiry_days2);

        $preference_data = "";

        $main = [$quotation_no, $preference_data, $shipper, $consignee, $port_of_loading, $final_destination, $sailing_on, $arriving_on, $expiry];

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
        $image_path = storage_path('app/public/hamada.png');
        $image_data = base64_encode(file_get_contents($image_path));

        //レターヘッド画像 C:\xampp\htdocs\fedex\storage\app\public\head.png
        $image_path2 = storage_path('app/public/head.png');
        $image_data2 = base64_encode(file_get_contents($image_path2));

        $output = $quotation_no . '.pdf';
        //quotation_print.blade.phpを読み込む
        $pdf = \PDF::loadView('quotation_print', compact('image_data', 'main', 'items', 'total', 'quotation_no', 'image_data2', 'type'))->setPaper('a4')->setWarnings(false);

        Storage::disk('public')->put('pdf/' . $output, $pdf->output());

        return $pdf->download($output);
    }


    //見積書PDFの出力(FORMからhidenでuuidを受け取る)
    public function generate_quotation_pdf2(Request $request)
    {
        $type = session()->get('type');

        if ($type == "air") {
            $type = "aircargo";
        } elseif ($type == "ship") {
            $type = "shipcontainer";
        }

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
        $quotations = Quotation::where('quotation_no', $quotation_no)->get();
        $quotations_sub = Quotation_detail::where('quotation_no', $quotation_no)->get();
        //pdf作成日
        $day = Carbon::createFromFormat('Y-m-d H:i:s', $quotations[0]->created_at)->format('Y-m-d');
        $shipper = $quotations[0]->shipper;
        //consignee
        $user_id = Auth::id();
        //$Userinformations = User::find($user_id)->Userinformations;
        $Userinformations = Userinformation::where('user_id', $user_id)->get();
        $consignee = $Userinformations[0]['consignee'];
        $port_of_loading = $quotations[0]->port_of_loading;
        $final_destination = $quotations[0]->final_destination;

        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry;
        $preference_data = "";
        $main = [$quotation_no, $preference_data, $shipper, $consignee, $port_of_loading, $final_destination, $sailing_on, $arriving_on, $expiry];
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

        $image_path = 'https://ccmedico.com/fedex/storage/hamada.png';
        $image_data = base64_encode(file_get_contents($image_path));

        //レターヘッド画像
        $image_path = storage_path('img/head.png');
        $image_data2 = base64_encode(file_get_contents($image_path));


        $output = $quotation_no . '.pdf';

        //pdf_printout.blade.phpを読み込む
        $pdf = \PDF::loadView('quotation_print', compact('image_data', 'main', 'items', 'total', 'quotation_no', 'image_data2', 'day', 'type'))->setPaper('a4')->setWarnings(false);

        //プレビュー
        return $pdf->stream($output);
    }
}
