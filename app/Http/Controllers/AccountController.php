<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Model\User;
use App\Model\Product;
use App\Model\Preference;
use App\Model\Quotation;
use App\Model\Userinformation;
use App\Model\Order;
use App\Model\Order_detail;
use App\Model\Order_confirm;
use App\Model\Invoice;
use Carbon\Carbon;
use App\Model\Quitation_serial_number;
use App\Model\Invoice_serial_number;
use App\Model\Image;
use App\Model\Expirie;
use App\Model\Consignee;
use App\Model\Pic;

class AccountController extends Controller
{
    public function consignee()
    {
        $id = Auth::id();
        //$users = User::with('Userinformations')->where('id', $id)->first();
        $pic = Pic::where('user_id', $id)->where('default_destination', 1)->first();
        $con = Consignee::where('user_id', $id)->where('default_destination', 1)->first();
        //dd($con);
        return view('account.consignee', compact('con', 'pic'));
    }

    public function importer()
    {
        $id = Auth::id();
        $main = User::with('Userinformations')->where('id', $id)->first();
        $main = $main->Userinformations;
        return view('account.importer', compact('main'));
    }


    public function index()
    {
        $data = Quotation::with(['invoices', 'invoices.order_confirms'])->where('consignee_no', Auth::id())->orderBy('created_at', 'desc')->paginate(10);
        $consignee = Userinformation::where('user_id', Auth::id())->first();


        $id = Auth::id();
        $users = User::with('Userinformations')->where('id', $id)->first();

        return view('account/index', compact('data', 'consignee', 'users'));
    }

    public function order()
    {
        $id = Auth::id();

        $od = Order::query();
        $orders = $od->where('user_id', $id)->orderByDesc('created_at')->paginate(10);
        return view('account/order', compact('orders'));
    }

    public function order_each($id)
    {
        //$img = Image::where('id',$id)->get();
        $order = Order::with('order_details')->where('id', $id)->first();
        $img = Image::where('order_no', $order->order_no)->get();
        //$imgはcollection
        if ($img->contains('about', 'payment')) {
            $about = "送金画像あり";
        } else {
            $about = "送金画像なし";
        }
        return view('account/order_each', compact('order', 'about'));
    }


    public function address()
    {
        $id = Auth::id();
        $users = User::with('Userinformations')->where('id', $id)->first();
        /*
        if ($user->address_line1=="") {
            return redirect()->route('account.index');
         }
         */
        return view('account/show', compact('users'));
    }

    public function edit()
    {

        $id = Auth::id();
        $user = User::with('Userinformations')->where('id', $id)->first();
        //$con = Consignee::where('user_id', $id)->first();
        //$pic = Pic::where('pic_id', $id)->first();
        
        $pic = Pic::where('user_id', $id)->where('default_destination', 1)->first();
        $con = Consignee::where('user_id', $id)->where('default_destination', 1)->first();

        


        return view('account/edit', compact('user', 'con', 'pic'));
    }

    public function quotation()
    {
        $id = Auth::id();
        /*
        $orders = Quitation_serial_number::query()
        ->where('user_id', '=', $id)
        ->orderByDesc('id')
        ->paginate(10);
       */
        //最初はPDFを出した分だけを表示していたが全部出すように変更
        //$orders =Quotation::where('consignee_no', '=', $id)->where('create_PDF', '>=', '2022-01-01 00:00:01')->orderByDesc('id')->paginate(10);
        $orders = Quotation::where('consignee_no', '=', $id)->orderByDesc('id')->paginate(10);

        return view('account/quotation', compact('orders'));
    }

    public function invoice()
    {
        //$id=Auth::id();
        //$iv=Invoice::query();
        //$invoices=$iv->orderByDesc('created_at')->paginate(10);
        //return view('account/invoice',compact('invoices'));

        $id = Auth::id();
        /*
        $orders = Invoice_serial_number::query()
        ->where('user_id', '=', $id)
        ->orderByDesc('id')
        ->paginate(10);
        */
        $orders = Invoice::where('customers_id', '=', $id)->where('create_PDF', '>=', '2022-01-01 00:00:01')->orderByDesc('id')->paginate(10);;

        return view('account/invoice', compact('orders'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'consignee_name' => 'required',
            'name' => 'required',
            'address_line1' => 'required',
            'address_line2' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zip' => 'required',
            'phone' => 'required',
        ]);

        $id = $request->id;
        $user = User::find($id);

        $user_id = $user->id;

        $consignee = Consignee::where('user_id', $user_id)->where('default_destination', '1')->first();

        //dd($user_id); 

        if (!$consignee) {
            $consignee = new Consignee();
            $consignee->user_id = $user_id;
            $consignee->default_destination = '1'; // デフォルトとして設定する場合
        }
        $consignee->consignee = $request->input('consignee_name');
        $consignee->name = $request->input('name'); // ←ここでエラーになる可能性あり
        $consignee->address_line1 = $request->input('address_line1');
        $consignee->address_line2 = $request->input('address_line2');
        $consignee->city = $request->input('city');
        $consignee->state = $request->input('state');
        $consignee->country_codes = $request->input('country');
        $consignee->post_code = $request->input('zip');
        $consignee->phone = $request->input('phone');
        $consignee->save();

        $pic = Pic::where('user_id', $user_id)->where('default_destination', '1')->first();

        if (!$pic) {
            $pic = new Pic();
            $pic->user_id = $user_id;
            $pic->default_destination = '1'; // これも必要に応じて設定
            $pic->pic_id = $user_id;
        }
        $pic->name = $request->input('person_name');
        $pic->email = $request->input('email');
        $pic->company_name = $request->input('company_name');
        $pic->country = $request->input('person_in_charge_country');
        $pic->save();


        return redirect(route('account.consignee'))->with('flash_message', 'Has been updated');
    }

    public function img_store(Request $request)
    {
        $order_no = $request->order_no;
        $user_id = $request->user_id;
        // 画像フォームでリクエストした画像を取得
        $img = $request->file('img_path');
        // 画像情報がセットされていれば、保存処理を実行
        if (isset($img)) {
            // storage > public > img配下に画像が保存される
            $path = $img->store('img', 'public');
            // store処理が実行できたらDBに保存処理を実行
            if ($path) {
                // DBに登録する処理
                Image::create([
                    'img_path' => $path,
                    'order_no' => $order_no,
                    'user_id'  => $user_id,
                    'about' => 'payment'
                ]);
            }
        }

        //　リダイレクト
        return redirect()->route('account.index');
    }

    public function add(Request $request)
    {
        $id = Auth::id();
        $user = User::with('Userinformations')->where('id', $id)->first();
        return view('account/add', compact('user'));
    }

    public function add_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address_line1' => 'required',
            'address_line2' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zip' => 'required',
            'phone' => 'required',
        ]);

        $id = $request->id;
        $user = User::find($id);
        $user_id = $user->id;
        $check = $request->default; //Set as default registration destination onかnull

        $count = Consignee::where('user_id', $user_id)->count();
        $count = $count + 1; //１を追加

        //既存の1を消去
        if ($check) {
            $con = Consignee::where('user_id', $user_id)->where('default_destination', '1')->first();
            if ($con) {
                $con->default_destination = "";
                $con->save();
            }

            $pc = Pic::where('user_id', $user_id)->where('default_destination', '1')->first();
            if ($pc) {
                $pc->default_destination = "";
                $pc->save();
            }
        }



        $consignee = new Consignee();
        $pic = new Pic();



        $consignee->consignee = $request->input('name');
        $consignee->address_line1 = $request->input('address_line1');
        $consignee->address_line2 = $request->input('address_line2');
        $consignee->city = $request->input('city');
        $consignee->state = $request->input('state');
        $consignee->country_codes = $request->input('country');
        $consignee->post_code = $request->input('zip');
        $consignee->phone = $request->input('phone');
        $consignee->user_id = $user_id;
        $consignee->name = $request->input('person_name');


        $consignee->pic_id = $user_id . "_" . $count;

        if ($check) {
            $consignee->default_destination = "1";
        }
        $consignee->save();

        $pic->name = $request->input('person_name');
        $pic->email = $request->input('email');
        $pic->company_name = $request->input('company_name');
        $pic->country = $request->input('person_in_charge_country');
        $pic->user_id = $user_id;
        $pic->pic_id = $user_id . "_" . $count;
        if ($check) {
            $pic->default_destination = "1";
        }
        $pic->save();

        return redirect()->route('account.index');
    }

    public function change(Request $request)
    {
        $id = Auth::id();
        $consignees = Consignee::where('user_id', $id)->get();
        return view('account/change', compact('consignees'));
    }
    public function change_update(Request $request)
    {
        $user_id = Auth::id();

        // 既存のデフォルトConsigneeをリセット
        $con = Consignee::where('user_id', $user_id)->where('default_destination', '1')->first();

        if ($con) {
            $con->default_destination = "";
            $con->save();
        }

        // 既存のデフォルトPicをリセット
        $pc = Pic::where('user_id', $user_id)->where('default_destination', '1')->first();
        if ($pc) {
            $pc->default_destination = "";
            $pc->save();
        }

        // 新しくチェックされたconsigneeのidを取得
        $id = $request->consignee;

        $con = Consignee::where('id', $id)->first();

        if (!$con) {
            return redirect()->back()->with('error', '宛先が見つかりませんでした。');
        }

        //$pic_id = $con->pic_id;
        $pic_id = $con->id;

        // 同じ担当者のConsigneeを default に
        $consignee = Consignee::where('id', $pic_id)->first();

        if ($consignee) {
            $consignee->default_destination = "1";
            $consignee->save();
        }

        // Pic を default に
        $pc = Pic::where('id', $pic_id)->first();
        //dd($pc);

        if ($pc) {
            $pc->default_destination = "1";
            $pc->save();
        }

        return redirect()->route('account.index');
    }



    public function importer_update(Request $request)
    {

        $id = Auth::id();
        $main = User::with('Userinformations')->where('id', $id)->first();
        $main = $main->Userinformations;

        $main->importer_name = $request->importer_name;
        $main->bill_company_address_line1 = $request->bill_company_address_line1;
        $main->bill_company_address_line2 = $request->bill_company_address_line2;
        $main->bill_company_city = $request->bill_company_city;
        $main->bill_company_state = $request->bill_company_state;
        $main->bill_company_country = $request->bill_company_country;
        $main->bill_company_zip = $request->bill_company_zip;
        $main->bill_company_phone = $request->bill_company_phone;
        $main->president = $request->president;
        $main->initial = $request->initial;
        $main->industry = $request->industry;
        $main->business_items = $request->business_items;
        $main->customer_name = $request->customer_name;
        $main->website = $request->website;
        $main->fedex = $request->fedex;
        $main->sns = $request->sns;

        $main->save();

        return view('account.importer', compact('main'));

    }
}
