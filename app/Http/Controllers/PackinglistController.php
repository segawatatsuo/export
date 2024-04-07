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
use App\Model\Order_detail;
use App\Model\Order_number;
use App\Model\Product;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Model\Invoice_serial_number;
use Illuminate\Support\Facades\Storage;
//自作したMailable クラス
use App\Mail\ThanksMail;
use Mail;

class PackinglistController extends Controller
{
    public function index(Request $request)
    {
        $order_number=$request->order_no;

        //consignee
        $hoge=Order::where('order_no', $order_number)->first();
        //dd($order_number);
        $consignee=$hoge->consignee;
        $ui = Order_detail::where('order_no', $order_number)->get();


        $order = Order::where('order_no', $order_number)->first();
        $user_id=$order->user_id;
        $ui = Userinformation::where('user_id', $user_id)->first();


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
        $pdf->Text(77, 10, htmlspecialchars($consignee));
        $pdf->Text(77, 14, htmlspecialchars($ui->address_line1 . ',' . $ui->address_line2 . ',' . $ui->city . ',' . $ui->state . ',' . $ui->country_codes));
        $pdf->Text(77, 18, htmlspecialchars('tel: ' . $ui->phone . ' fax: ' . $ui->fax));
        //日付
        $pdf->Text(150, 18, htmlspecialchars($order->invoice_no));
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

        //$storage_path=(storage_path('app\public\pdf'));
        //サーバーにPDF保存
        //$output="\\PL".$order_number.".pdf";
        //$pdf->Output($storage_path.$output, 'F');

        $output = "PL" . $order_number . ".pdf";
        //Storage::put('app\public\pdf\aaa', $output);
        Storage::disk('public')->put('pdf/' . $output, $pdf->output());

        //PDF名を保存
        $order->packaging_list_pdf = "PL" . $order_number . ".pdf";
        $order->save();
    }
}
