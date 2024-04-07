<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\Emailtext;
use App\Mail\SendTestMail;
use App\Model\User;

use Mail;

class MailSendController extends Controller
{
    public function send(Request $request){

        $email = $request->email;
        $name = $request->name;
        $no = $request->no;
        $bcc = "segawa@lookingfor.jp";
        $emaitext = Emailtext::find(1);

        if($no==1){
            $subject=$emaitext->subject_1;
            $content=$emaitext->contents_1;
            
        }
        if($no==2){
            $subject=$emaitext->subject_2;
            $content=$emaitext->contents_2;
        }
        //ユーザー登録メール
        if($no==3){           
            $subject=$emaitext->subject_3;
            $content=$emaitext->contents_3;

            $user = Auth::user();
            $name = $user->name;
            $email = $user->email;
            $country = $user->country;
            $company_name = $user->company_name;

            $data = 
            "Name | ". $name . "\n" .
            "Email | ". $email . "\n" .
            "Country | ". $country . "\n" .
            "Company | ". $company_name ;

            $content = str_replace('※replacement1※', $data, $content);
        }
        if($no==4){
            $subject=$emaitext->subject_4;
            $content=$emaitext->contents_4;
        }
        if($no==5){
            $subject=$emaitext->subject_5;
            $content=$emaitext->contents_5;
        }
        if($no==6){
            $subject=$emaitext->subject_6;
            $content=$emaitext->contents_6;
        }
        //Consigneeメール
        if($no==7){
            $subject=$emaitext->subject_7;
            $content=$emaitext->contents_7;

            $user = Auth::user();
            $id = $user->id;
            $user = User::with('Userinformation')->find($id);

            $consignee = $user->userinformation->consignee;
            $address_line1 = $user->userinformation->address_line1;
            $address_line2 = $user->userinformation->address_line2;
            $city = $user->userinformation->city;
            $state = $user->userinformation->state;
            $country = $user->userinformation->country_codes;
            $zip = $user->userinformation->zip;
            $phone = $user->userinformation->phone;
            $person = $user->userinformation->person;

            $data = 
            "Consignee | ". $consignee . "\n" .
            "Address line1 | ". $address_line1 . "\n" .
            "Address line2 | ". $address_line2 . "\n" .
            "City | ". $city . "\n" .
            "State | ". $state . "\n" .
            "Country | ". $country . "\n" .
            "zip code | ". $zip . "\n" .
            "phone number | ". $phone. "\n" .
            "Contact person | ". $person;

            $content = str_replace('※replacement2※', $data, $content);
        }

        //Importerメール
        if($no==8){
            $subject=$emaitext->subject_8;
            $content=$emaitext->contents_8;

            $user = Auth::user();
            $id = $user->id;
            $user = User::with('Userinformation')->find($id);

            $importer_name = $user->userinformation->importer_name;
            $bill_company_address_line1 = $user->userinformation->bill_company_address_line1;
            $bill_company_address_line2 = $user->userinformation->bill_company_address_line2;
            $bill_company_city = $user->userinformation->bill_company_city;
            $bill_company_state = $user->userinformation->bill_company_state;
            $bill_company_country = $user->userinformation->bill_company_country;
            $bill_company_zip = $user->userinformation->bill_company_zip;
            $bill_company_phone = $user->userinformation->bill_company_phone;
            $president = $user->userinformation->president;
            $initial = $user->userinformation->initial;

            $industry = $user->userinformation->industry;
            $business_items = $user->userinformation->business_items;
            $customer_name = $user->userinformation->customer_name;
            $website = $user->userinformation->website;
            $fedex = $user->userinformation->fedex;
            $sns = $user->userinformation->sns;

            $data = 
            "Importer company | ". $importer_name . "\n" .
            "Address line1 | ". $bill_company_address_line1 . "\n" .
            "Address line2 | ". $bill_company_address_line2 . "\n" .
            "Company City | ". $bill_company_city . "\n" .
            "Company State | ". $bill_company_state . "\n" .
            "Company Country | ". $bill_company_country . "\n" .
            "Company Zip | ". $bill_company_zip . "\n" .
            "Company Phone | ". $bill_company_phone. "\n" .
            "Company President | ". $president . "\n" .
            "Company Initials | ". $initial . "\n" .

            "Your Business Type | ". $industry . "\n" .
            "Your Items of Business	 | ". $business_items . "\n" .
            "Your Customer(s) | ". $customer_name . "\n" .
            "Your company Website/URL | ". $website . "\n" .
            "Your FedEX Account | ". $fedex . "\n" .
            "Your company SNS | ". $sns ;

            $content = str_replace('※replacement3※', $data, $content);

        }



        
        $content =['name'=>$name,'subject'=>$subject,'content'=>$content];

    
        Mail::to($email)->bcc($bcc)->send(new SendTestMail($content));

        $mails = Emailtext::first();
        $user = Auth::user();
        return redirect(route('mail.index'))->with('flash_message', 'テストメールを送信しました');
    }
}