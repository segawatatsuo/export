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
use App\Model\Consignee;
use Mail;
use App\Model\Emailtext;
use App\Model\Order;

class QuotationController extends Controller
{
    public function quotation(Request $request)
    {
        $user_id = Auth::id();

        // コンサイニー情報の取得と検証
        $selected_consignee = Consignee::where('user_id', $user_id)
            ->where('default_destination', '1')
            ->first();

        // コンサイニー情報が未登録の場合の処理
        if (!$selected_consignee || 
            empty($selected_consignee->consignee) || 
            empty($selected_consignee->address_line1)) {
            // ここで適切な処理を行う（例：登録フォームへリダイレクト）

            return redirect()->route('consignee.create')
                ->with('flash_message', 'Please register your consignee information first.');

        }

        if (session('article') == "") {
            session()->put(['article' => 'Air Stocking']);
        }

        $categorys = Product::where('hidden_item', '!=', '1')
            ->where('category', session('article'))
            ->groupBy('category')
            ->orderBy('sort_order', 'asc')
            ->get(['category']);
            
        $groups = Product::where('hidden_item', '!=', '1')
            ->where('category', session('article'))
            ->groupBy('group')
            ->orderBy('sort_order', 'asc')
            ->get(['group']);
            
        $items = [];
        foreach ($groups as $g) {
            $b = Product::where('hidden_item', '!=', '1')
                ->where('group', $g->group)
                ->orderBy('sort_order', 'asc')
                ->get();
            array_push($items, $b);
        }

        $groups = [];
        foreach ($items as $item) {
            foreach ($item as $val) {
                array_push($groups, $val->group);
                $groups = array_unique($groups);
            }
        }

        $codes = [];
        foreach ($items as $item) {
            foreach ($item as $val) {
                $hoge = [$val->product_code => $val->group];
                $codes = array_merge($codes, $hoge);
            }
        }

        $type = session()->get('type');

        // 数量の制限値
        $fedex = Limit::where('Delivery_type', '=', 'fedex')->first();
        $air1 = Limit::where('Delivery_type', '=', 'air1')->first();
        $air2 = Limit::where('Delivery_type', '=', 'air2')->first();
        $ship = Limit::where('Delivery_type', '=', 'ship')->first();

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
            $GOODS[$key] = mb_convert_kana($request->get($key), "n");
        }

        $array = [];
        foreach ($GOODS as $key => $val) {
            $ps = $codes[$key];
            $array[] = [$ps, $val];
        }

        foreach ($groups as $value) {
            $num = 0;
            foreach ($array as $row) {
                if ($row[0] == $value) {
                    $num = $num + intval($row[1]);
                }
            }
            $group_total[$value] = $num;
        }

        $err = array();
        $err1 = array();
        $err2 = array();

        if ($type == "fedex") {
            foreach ($GOODS as $key => $val) {
                $zaiko = Product::whereproduct_code($key)->first()->stock;
                if ($val > $zaiko) {
                    $err = array($key . ' should be less than ' . $zaiko);
                    $err1 = (array_merge($err1, $err));
                }
            }
            
            foreach ($group_total as $val) {
                if ($val >= 1 and $val < $fedex_low or $val > $fedex_up) {
                    $err2 = array('Make sure the total cartons of items is between ' . $fedex_low . ' and ' . $fedex_up);
                }
            }
            $err = (array_merge($err1, $err2));
            $route_name = "fedex";
            if (!empty($err)) {
                return redirect()->route($route_name)
                    ->with('flash_message', implode('<br>', $err))
                    ->withInput();
            }

            foreach ($group_total as $key => $val) {
                $tanka  = Product::where('group', '=', $key)->first()->price_fedex;
                $fedex_tanka[] = array($key => $tanka);
            }
        }

        if ($type == "air") {
            foreach ($GOODS as $key => $val) {
                $zaiko = Product::whereproduct_code($key)->first()->stock;
                if ($val > $zaiko and $val < $air1_min) {
                    $err = array($key . ' should be less than ' . $zaiko);
                    $err1 = (array_merge($err1, $err));
                }
            }

            $array_sum = array_sum($group_total);
            if ($array_sum >= 1 and $array_sum < $air1_low or $array_sum > $air2_up) {
                $err2 = array('Make sure the total cartons of items is ' . $air1_low . ' or more');
            }

            $err = (array_merge($err1, $err2));
            $route_name = "air";
            if (!empty($err)) {
                return redirect()->route($route_name)
                    ->with('flash_message', implode('<br>', $err))
                    ->withInput();
            }

            foreach ($group_total as $key => $val) {
                if ($val < 100) {
                    $tanka = Product::where('group', '=', $key)->first()->price_fedex;
                    $air_tanka[] = array($key => $tanka);
                } elseif ($val < 200) {
                    $tanka = Product::where('group', '=', $key)->first()->price_air_1;
                    $air_tanka[] = array($key => $tanka);
                } else {
                    $tanka = Product::where('group', '=', $key)->first()->price_air_2;
                    $air_tanka[] = array($key => $tanka);
                }
            }
        }

        $array_sum = 0;

        if ($type == "ship") {
            foreach ($GOODS as $key => $val) {
                $zaiko = Product::whereproduct_code($key)->first()->stock;
                if ($val > $zaiko and $val < $ship_min) {
                    $err = array($key . ' should be less than ' . $zaiko);
                    $err1 = (array_merge($err1, $err));
                }
            }

            foreach ($GOODS as $key => $val) {
                if ($val >= 1 and $val < $ship_min) {
                    $name = Product::where('product_code', '=', $key)->first();
                    $err = array('[' . $key . '] ' . $name->kind . ' should be more than ' . $ship_min);
                    $err1 = (array_merge($err1, $err));
                }
            }

            $array_sum = array_sum($group_total);
            if ($array_sum >= 1 and $array_sum < $ship_low or $array_sum > $ship_up) {
                $err2 = array('Make sure the total cartons of items is ' . $ship_low . ' or more');
            }

            $err = (array_merge($err1, $err2));
            $route_name = "ship";
            $err = implode("<br>", $err);
            if (!empty($err)) {
                return redirect()->route($route_name)
                    ->with('flash_message', $err)
                    ->withInput();
            }

            foreach ($group_total as $key => $val) {
                if ($val < 100) {
                    $tanka = Product::where('group', '=', $key)->first()->price_fedex;
                    $ship_tanka[] = array($key => $tanka);
                } elseif ($val < 200) {
                    $tanka = Product::where('group', '=', $key)->first()->price_air_1;
                    $ship_tanka[] = array($key => $tanka);
                } elseif ($val < 500) {
                    $tanka = Product::where('group', '=', $key)->first()->price_air_2;
                    $ship_tanka[] = array($key => $tanka);
                } else {
                    $tanka = Product::where('group', '=', $key)->first()->price_ship;
                    $ship_tanka[] = array($key => $tanka);
                }
            }
        }

        function which_tanka2($type, $group, $total, $fedex_low, $fedex_up, $air1_low, $air1_up, $air2_low, $air2_up, $ship_low, $ship_up, $array_sum)
        {
            $tanka = null;
            
            if ($type == "fedex" && $total >= $fedex_low && $total <= $fedex_up) {
                $product = Product::where('group', '=', $group)->first();
                $tanka = $product ? $product->price_fedex : null;
                
            } elseif ($type == "air") {
                $product = Product::where('group', '=', $group)->first();
                
                if ($product) {
                    if ($total >= $air2_low && $total <= $air2_up) {
                        $tanka = $product->price_air_2;
                    } elseif ($total >= $air1_low && $total <= $air1_up) {
                        $tanka = $product->price_air_1;
                    } elseif ($total >= 1 && $total < $air1_low) {
                        $tanka = $product->price_air_1;
                    }
                }
                
            } elseif ($type == "ship" && $total >= $ship_low && $total <= $ship_up) {
                $product = Product::whereGroup($group)->first();
                $tanka = $product ? $product->price_ship : null;
                
            } elseif ($type == "ship" && $array_sum >= $ship_low && $array_sum <= $ship_up) {
                $product = Product::whereGroup($group)->first();
                $tanka = $product ? $product->price_ship : null;
            }
            
            if ($tanka === null || $tanka === 0) {
                \Log::warning("which_tanka2: 値が取得できませんでした", [
                    'type' => $type,
                    'group' => $group,
                    'total' => $total,
                    'tanka' => $tanka,
                ]);
            }
            
            return $tanka;
        }

        function set_item($hinban, $ctn, $tanka, $hinmei, $unit)
        {
            $quantity_total = 0;
            $amount_total = 0;
            $product = Product::whereProduct_code($hinban)->first();
            $units = $product ? $product->units : 0;
            $quantity = $ctn * $units;
            $amount = $quantity * $tanka;
            $quantity_total += $quantity;
            $amount_total += $amount;
            $unit = $unit;
            $data = [$hinban, $hinmei, $tanka, $ctn, $quantity, $amount, $unit];
            return $data;
        }

        $items = [];

        foreach ($GOODS as $key => $val) {
            if ($val != "") {
                $hinban = $key;
                $product = Product::whereProduct_code($hinban)->first();
                
                if (!$product) {
                    continue;
                }
                
                $group = $product->group;
                $total = $group_total[$group];
                $ctn = $val;
                $hinmei = $product->category . ' ' . $product->group . ' ' . $product->kind;
                $tanka = which_tanka2($type, $group, $total, $fedex_low, $fedex_up, $air1_low, $air1_up, $air2_low, $air2_up, $ship_low, $ship_up, $array_sum);
                $unit = $product->units;
                $data = set_item($hinban, $ctn, $tanka, $hinmei, $unit);
                array_push($items, $data);
            }
        }

        $ctn_total = 0;
        foreach ($group_total as $key => $val) {
            $ctn_total += $val;
        }
        
        $quantity_total = 0;
        foreach ($items as $item) {
            $quantity_total += $item[4];
        }
        
        $amount_total = 0;
        foreach ($items as $val) {
            $amount_total += $val[5];
        }

        session()->put('items', $items);
        session()->put('ctn_total', $ctn_total);
        session()->put('quantity_total', $quantity_total);
        session()->put('amount_total', $amount_total);

        $etd = Etd::all();
        $air_etd = $etd[0]->air ?? 0;
        $ship_etd = $etd[0]->ship ?? 0;

        if ($type == "fedex") {
            $addday = Etd::find(1)->fedex ?? 0;
            $sailing_on = "1-2 days after order confirmed";
            $arriving_on = "Typical 7-10 days once order in confirmed";
        } elseif ($type == "air") {
            $addday = Etd::find(1)->air ?? 0;
            $sailing_on = "After order confirmation " . $air_etd . " days";
            $arriving_on = "Typical 7-14 days by Air Cargo";
        } elseif ($type == "ship") {
            $addday = Etd::find(1)->ship ?? 0;
            $sailing_on = "After order confirmation " . $ship_etd . " days";
            $arriving_on = "Typical 1-2 months for Ship";
        }

        $date = new Carbon('today');
        $date = $date->addDay($addday);

        $year = $date->format('Y');
        $month = $date->format('M');

        $date = new Carbon('today');
        $date = $date->addDay(7);

        $year = $date->format('Y');
        $month = $date->format('M');
        $day = $date->format('d');
        $quotation_valid = $month . ' ' . $day . ' ,' . $year;
        session()->put('quotation_valid', $quotation_valid);

        $uuid = strtoupper(uniqid());

        $preference_data = Preference::first();

        $expiry_days = Expirie::find(1)->number_of_days ?? '15days';

        $num = preg_replace('/[^0-9]/', '', $expiry_days);
        $expirytoday = new Carbon('today');
        $expiryaddday = $expirytoday->addDay($num);
        $expiryaddday = $expiryaddday->toDateString();
        $expiryaddday = date('M j Y', strtotime($expiryaddday));

        $expiry_days2 = $expiry_days . " (" . $expiryaddday . ")";
        session()->put('expiry_days', $expiry_days2);
        session()->put('expiryaddday', $expiryaddday);

        // Quotationテーブルにデータを作成
        $quotation = new Quotation();

        $serial_number = new Quitation_serial_number();

        $latestOrder = Quitation_serial_number::orderBy('created_at', 'DESC')->first();
        if ($latestOrder === null) {
            $qt_number = '0001';
        } else {
            $qt_number = str_pad($latestOrder->id + 1, 4, "0", STR_PAD_LEFT);
        }

        $quotation_no = 'quitation_' . $qt_number;
        $quotation->quotation_no = $quotation_no;

        $serial_number->pdf_file_name = $quotation_no . '.pdf';
        $serial_number->user_id = $user_id;
        $serial_number->save();

        $shipper = $preference_data->shipper ?? '';

        $quotation->date_of_issue = Carbon::now();
        $quotation->shipper = $shipper;
        $quotation->consignee_no = $user_id;

        // $selected_consigneeは既に存在確認済み
        $pic_id = $selected_consignee->pic_id;

        $quotation->consignees_name = $selected_consignee->consignee;
        $quotation->consignees_address_line1 = $selected_consignee->address_line1;
        $quotation->consignees_address_line2 = $selected_consignee->address_line2;
        $quotation->consignees_city = $selected_consignee->city;
        $quotation->consignees_state = $selected_consignee->state;
        $quotation->consignees_country_codes = $selected_consignee->country_codes;
        $quotation->consignees_postal_code = $selected_consignee->post_code;
        $quotation->consignees_phone = $selected_consignee->phone;

        $consignee = $selected_consignee->consignee;
        $quotation->consignee = $consignee;

        $quotation->port_of_loading = $preference_data->port_of_loading ?? '';
        $quotation->sailing_on = $sailing_on;
        $quotation->expiry = $expiry_days;
        $quotation->expiryaddday = $expiryaddday;
        $quotation->shipping = $expiryaddday;
        $quotation->arriving_on = $arriving_on;
        $quotation->expiry_days2 = $expiry_days2;
        $quotation->type = $type;

        $quotation->quantity_total = $quantity_total;
        $quotation->ctn_total = $ctn_total;
        $quotation->amount_total = $amount_total;

        $state = $selected_consignee->state ?? '';
        $country = $selected_consignee->country ?? '';
        
        if ($quotation->final_destination) {
            $quotation->final_destination = $state . ',' . $country;
        }

        $quotation->delivery_method = $type;
        $quotation->pic_id = $pic_id;

        $quotation->save();

        foreach ($items as $item) {
            $sub = new Quotation_detail();
            $sub->quotation_id = $user_id;

            $sub->product_code = $item[0];
            $sub->product_name = $item[1];
            $sub->unit_price = $item[2];
            $sub->ctn = $item[3];
            $sub->quantity = $item[4];
            $sub->amount = $item[5];
            $sub->unit = $item[6];
            $sub->quotation_no = $quotation_no;
            $sub->quotation_id = $quotation->id;
            $sub->save();
        }

        // Userinformationsの確認（後方互換性のため）
        $Userinformations = User::find($user_id)->Userinformations;
        if ($Userinformations == null && !$selected_consignee) {
            return view('entryform', compact('uuid', 'user_id', 'quotation_no'));
        }

        $consignee = $selected_consignee->consignee;
        $address_line1 = $selected_consignee->address_line1;
        $address_line2 = $selected_consignee->address_line2;
        $city = $selected_consignee->city;
        $state = $selected_consignee->state;
        $country = $selected_consignee->country;
        $country_codes = $selected_consignee->country_codes;
        $phone = $selected_consignee->phone;
        $fax = $selected_consignee->fax ?? '';

        $user = array(
            'user_id' => $user_id, 
            'consignee' => $consignee, 
            'address_line1' => $address_line1,
            'address_line2' => $address_line2, 
            'city' => $city, 
            'state' => $state, 
            'country' => $country,
            'country_codes' => $country_codes, 
            'phone' => $phone, 
            'fax' => $fax, 
            'delivery_method' => $type,
            'quotation_no' => $quotation_no
        );

        $collection = collect($items);
        session()->put('items', $collection);

        $shipper = $preference_data->shipper ?? '';
        $port_of_loading = $preference_data->port_of_loading ?? '';
        $final_destination = $state . ',' . $country;

        $to = User::find($user_id)->email;
        $bcc = session('adminmail', 'info@lookingfor.jp');

        $subject = Emailtext::Find(1)->subject_4 ?? 'Quotation';
        $content = [
            'contents' => Emailtext::Find(1)->contents_4 ?? '',
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
        if(Session::has('impersonating') != null) {
            
        }else{
            Mail::to($to)->bcc($bcc)->send(new QuotationMail($content, $subject, $items));
        }

        return view('quotation', compact(
            'uuid', 'preference_data', 'items', 'ctn_total', 'quantity_total', 
            'amount_total', 'sailing_on', 'user', 'quotation_no', 'type', 
            'expiry_days2', 'shipper', 'consignee', 'port_of_loading', 
            'arriving_on', 'pic_id'
        ));
    }

    public function quotation_repeat(Request $request)
    {
        $quotation_no = $request->quotation_no;
        $data = Quotation::where('quotation_no', $quotation_no)->first();
        
        if (!$data) {
            return redirect()->back()->with('flash_message', 'Quotation not found');
        }
        
        $preference_data = Preference::first();
        $shipper = $data->shipper;
        $consignee_no = $data->consignee_no;

        $selected_consignee = Consignee::where('user_id', $data->consignee_no)
            ->where('default_destination', '1')
            ->first();
            
        if (!$selected_consignee) {
            return redirect()->back()->with('flash_message', 'Consignee information not found');
        }
        
        $consignee = $selected_consignee->consignee;
        $pic_id = $selected_consignee->pic_id;

        $port_of_loading = $data->port_of_loading;
        $final_destination = $data->final_destination;
        $sailing_on = $data->sailing_on;
        $arriving_on = $data->arriving_on;
        $expires = $data->expires;

        $details = Quotation_detail::where('quotation_no', $quotation_no)->get();
        $items = [];
        
        foreach ($details as $detail) {
            $hinban = $detail->product_code;
            $hinmei = $detail->product_name;
            $tanka = (float)$detail->unit_price;
            $ctn = (float)$detail->ctn;
            $unit = (int)$detail->unit;
            $amaunt = $detail->amount;
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

        return view('quotation', compact(
            'uuid', 'preference_data', 'items', 'ctn_total', 'quantity_total', 
            'amount_total', 'sailing_on', 'user', 'quotation_no', 'type', 
            'expiry_days2', 'shipper', 'consignee', 'port_of_loading', 
            'arriving_on', 'pic_id'
        ));
    }

    public function generate_quotation_pdf(Request $request)
    {
        $type = session()->get('type');
        if ($type == "air") {
            $type = "aircargo";
        } elseif ($type == "ship") {
            $type = "shipcontainer";
        }

        $main = [];
        $quotation_no = $request->get('quotation_no');

        $quotations = Quotation::where('quotation_no', $quotation_no)->get();
        $quotations_sub = Quotation_detail::where('quotation_no', $quotation_no)->get();

        $final_destination = $request->final_destination;
        \DB::table('quotations')
            ->where('quotation_no', $quotation_no)
            ->update(['final_destination' => $final_destination]);

        $date = Carbon::now();
        \DB::table('quotations')
            ->where('quotation_no', $quotation_no)
            ->update(['create_PDF' => $date]);

        $shipper = $quotations[0]->shipper ?? '';

        $user_id = Auth::id();
        $Userinformations = User::find($user_id)->Userinformations;
        $consignee = $Userinformations ? $Userinformations->consignee : '';
        
        $port_of_loading = $quotations[0]->port_of_loading ?? '';
        $final_destination = $final_destination;
        $sailing_on = $quotations[0]->sailing_on ?? '';
        $arriving_on = $quotations[0]->arriving_on ?? '';
        $expiry = $quotations[0]->expiry_days2 ?? '';

        $preference_data = "";

        $main = [
            $quotation_no, $preference_data, $shipper, $consignee, 
            $port_of_loading, $final_destination, $sailing_on, 
            $arriving_on, $expiry
        ];

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

        $quantity_total = $quotations[0]->quantity_total ?? 0;
        $ctn_total = $quotations[0]->ctn_total ?? 0;
        $amount_total = $quotations[0]->amount_total ?? 0;
        $total = [$quantity_total, $ctn_total, $amount_total];
        
        $image_path = storage_path('app/public/hamada.png');
        $image_data = base64_encode(file_get_contents($image_path));

        $image_path2 = storage_path('app/public/head.png');
        $image_data2 = base64_encode(file_get_contents($image_path2));

        $output = $quotation_no . '.pdf';
        $pdf = \PDF::loadView('quotation_print', compact(
            'image_data', 'main', 'items', 'total', 'quotation_no', 
            'image_data2', 'type'
        ))->setPaper('a4')->setWarnings(false);

        Storage::disk('public')->put('pdf/' . $output, $pdf->output());

        return $pdf->download($output);
    }

    public function generate_quotation_pdf2(Request $request)
    {
        $type = session()->get('type');

        if ($type == "air") {
            $type = "aircargo";
        } elseif ($type == "ship") {
            $type = "shipcontainer";
        }

        $payee = Payment_method::where('selection', '選択')->get();
        if ($payee->count() > 0) {
            session(['bank' => $payee[0]['bank']]);
            session(['branch' => $payee[0]['branch']]);
            session(['swift_code' => $payee[0]['swift_code']]);
            session(['account' => $payee[0]['account']]);
            session(['name' => $payee[0]['name']]);
        }

        $main = [];
        $quotation_no = $request->get('quotation_no');
        $quotations = Quotation::where('quotation_no', $quotation_no)->get();
        
        if ($quotations->isEmpty()) {
            return redirect()->back()->with('flash_message', 'Quotation not found');
        }
        
        $quotations_sub = Quotation_detail::where('quotation_no', $quotation_no)->get();
        
        $day = Carbon::createFromFormat('Y-m-d H:i:s', $quotations[0]->created_at)
            ->format('Y-m-d');
        $shipper = $quotations[0]->shipper ?? '';
        
        $user_id = Auth::id();
        $Userinformations = Userinformation::where('user_id', $user_id)->get();
        $consignee = $Userinformations->isNotEmpty() ? $Userinformations[0]['consignee'] : '';
        
        $port_of_loading = $quotations[0]->port_of_loading ?? '';
        $final_destination = $quotations[0]->final_destination ?? '';

        $sailing_on = $quotations[0]->sailing_on ?? '';
        $arriving_on = $quotations[0]->arriving_on ?? '';
        $expiry = $quotations[0]->expiry ?? '';
        $preference_data = "";
        
        $main = [
            $quotation_no, $preference_data, $shipper, $consignee, 
            $port_of_loading, $final_destination, $sailing_on, 
            $arriving_on, $expiry
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
        
        $quantity_total = $quotations[0]->quantity_total ?? 0;
        $ctn_total = $quotations[0]->ctn_total ?? 0;
        $amount_total = $quotations[0]->amount_total ?? 0;
        $total = [$quantity_total, $ctn_total, $amount_total];

        $image_path = storage_path('app/public/hamada.png');
        $image_data = base64_encode(file_get_contents($image_path));

        $image_path2 = storage_path('app/public/head.png');
        $image_data2 = base64_encode(file_get_contents($image_path2));

        $output = $quotation_no . '.pdf';
        $pdf = \PDF::loadView('quotation_print', compact(
            'image_data', 'main', 'items', 'total', 'quotation_no', 
            'image_data2', 'day', 'type'
        ))->setPaper('a4')->setWarnings(false);

        return $pdf->stream($output);
    }
}