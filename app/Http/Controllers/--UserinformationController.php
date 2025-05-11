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

}
