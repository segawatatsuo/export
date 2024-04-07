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
use App\Model\Order;
use App\Model\Order_confirm;
use Carbon\Carbon;
use App\Model\Invoice_serial_number;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    //見積書PDFの出力(FORMからhidenでuuidを受け取る)
    public function index(Request $request)
    {
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
        //dd($quotations);

        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry_days2;

        //本社
        $bill_company_address_line1=$Userinformations->bill_company_address_line1;
        $bill_company_address_line2=$Userinformations->bill_company_address_line2;
        $bill_company_city=$Userinformations->bill_company_city;
        $bill_company_state=$Userinformations->bill_company_state;
        $bill_company_country=$Userinformations->bill_company_country;
        $bill_company_zip=$Userinformations->bill_company_zip;
        $bill_company_phone=$Userinformations->bill_company_phone;
        $president=$Userinformations->president;

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
            'fax' => $fax,
            'country' => $country,
            'bill_company_address_line1'=>$bill_company_address_line1,
            'bill_company_address_line2'=>$bill_company_address_line2,
            'bill_company_city'=>$bill_company_city,
            'bill_company_state'=>$bill_company_state,
            'bill_company_country'=>$bill_company_country,
            'bill_company_zip'=>$bill_company_zip,
            'bill_company_phone'=>$bill_company_phone,
            'president'=>$president
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

        $image_path = storage_path('app/public/hamada.png');
        $image_data = base64_encode(file_get_contents($image_path));

        $pdf = \PDF::loadView('purchase', compact('image_data', 'main', 'items', 'total'))->setPaper('a4')->setWarnings(false);

        $output = 'PO-' . $invoice_no . '.pdf';
        Storage::disk('public')->put('pdf/' . $output, $pdf->output());

        //PDF名を保存_confirm
        //$order = Order::where('quotation_no', $quotation_no)->first();
        $order = Order_confirm::where('quotation_no', $quotation_no)->first();
        $order->order_sheet_pdf = $output . ".pdf";
        $order->save();

        return $pdf->download('PO-' . $invoice_no . '.pdf');
    }
}
