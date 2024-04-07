<?php

namespace App\Http\Controllers;

use setasign\Fpdi;
use TCPDF_FONTS;

class DocumentController extends Controller
{
    public function tcpdf()
    {
        $pdf = new Fpdi\TcpdfFpdi();
        // $pdf = new \TCPDF("L", "mm", "A4", true, "UTF-8" );	// pdf テンプレートを使わない場合はこちら

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        // テンプレートPDFファイル読み込み(resourcesフォルダにtemplate_01.pdfを配置)
        $pdf->setSourceFile(resource_path('pdf-template/template_01.pdf'));
        $page = $pdf->importPage(1);
        $pdf->useTemplate($page);

        // フォント
        //$font = new TCPDF_FONTS();

        // フォント：源真ゴシック
        // $font_1 = $font->addTTFfont( resource_path('fonts/ipag.ttf') );
        //$font_1 = $font->addTTFfont( resource_path('fonts/GenShinGothic-Medium.ttf') );
        //$pdf->SetFont($font_1 , '', 10,'',true);

        //$number = $_POST["number"];
        //$name = $_POST["name"];
        //$price = $_POST["price"];
        //$proviso = $_POST["proviso"];

        $number = "8104 3146 1705";
        $date = "Mar.23.2020";
        $shipper = "CCMEDICO.co.ltd\nHamada yoshimi\nShibuya Mark City West 22F,1-12-1 Dougenzaka\nShibuya-ku,Tokyo,Japan 150-0043 Tel:+82 3-5942-5536";
        $consignee = " segawa tatsuo\n2-1-2 hirao,inagishi\Tokyo 240-0001\nTel +82 090-9149-6802";
        $country_of_export = "japan";
        $reason_of_export = "Product";
        $destination = "U.S.A";
        ///////////////////
        $origin = "Japan";
        $marks = "(C/NO.1-4)";
        $pkgs = "4";
        $typeofpkg = "Carton";
        $goods = "Aersols Foundtion\nAirStocking Premire Silk 120g\n24p/box";
        $qty = "24";
        $measures = "pieces";
        $weight = "5.5";
        $tanka = '¥1,000';
        $totalvalue = '¥24,000';
        $totalpkg = "4";
        $totalweight = "22kg";
        $currency = "JYE";
        $totalinvoicevalue = '¥96,000';
        $date = "Mar 23. 2022";

        $check1="×";
        $check2="×";


        //No.
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Text(45, 19, htmlspecialchars($number));

        //DATE
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Text(10, 30, htmlspecialchars($date));

        //SHIPPER
        //$pdf->SetFont('kozminproregular', '', 20);
        //$price = number_format($shipper) . "-";
        //$pdf->Text(10, 48, htmlspecialchars($shipper));
        $pdf->SetFont('helvetica', '', 7);
        $pdf->MultiCell(100, 100, $shipper, 0, "L", 0, 0, 10, 48);

        //consignee
        //$pdf->Text(110, 48, htmlspecialchars($consignee));
        $pdf->SetFont('helvetica', '', 7);
        $pdf->MultiCell(100, 100, $consignee, 0, "L", 0, 0, 110, 48);

        //country_of_export
        $pdf->Text(10, 72, htmlspecialchars($country_of_export));

        //reason_of_export
        //$pdf->SetFont('kozminproregular', '', 11);
        $pdf->Text(10, 85, htmlspecialchars($reason_of_export));

        //destination
        $pdf->Text(10, 93, htmlspecialchars($destination));

        //$origin
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(8, 126, htmlspecialchars($origin));

        //$marks
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(18, 126, htmlspecialchars($marks));

        //pkgs
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(35, 126, htmlspecialchars($pkgs));

        //typeofpkg
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(45, 126, htmlspecialchars($typeofpkg));

        //$goods
        $pdf->SetFont('helvetica', '', 7);
        //$pdf->Text(60, 126, htmlspecialchars($goods));
        $pdf->MultiCell(100, 100, $goods, 0, "L", 0, 0, 60, 126);

        //qty
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(140, 126, htmlspecialchars($qty));

        //measures
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(150, 126, htmlspecialchars($measures));

        //weight
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(162, 126, htmlspecialchars($weight));

        //tanka
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(175, 126, htmlspecialchars($tanka));

        //totalvalue
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(192, 126, htmlspecialchars($totalvalue));

        //totalpkg
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(35, 233, htmlspecialchars($totalpkg));

        //totalweight
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(162, 233, htmlspecialchars($totalweight));

        //currency
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(175, 233, htmlspecialchars($currency));

        //totalinvoicevalue
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(192, 233, htmlspecialchars($totalinvoicevalue));

        //date
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Text(150, 263, htmlspecialchars($date));


        $pdf->SetFont('helvetica', '', 12);
        $pdf->Text(174, 248, htmlspecialchars($check1));

        $pdf->SetFont('helvetica', '', 12);
        $pdf->Text(192, 243, htmlspecialchars($check2));


        //日付
        //$pdf->SetFont('kozminproregular', '', 11);
        //$today = date("Y年m月d日");
        //$pdf->Text(150, 21, $today);

        //$pdf->Output(出力時のファイル名, 出力モード);
        //$pdf->Output("output.pdf", "I");

        // 画像埋め込み
        //画像名,左からの掲載位置,上からの掲載位置,画像の横幅サイズ
        $pdf->Image(resource_path("pdf-template/hamada.png"), 10, 257, 25);


        // テンプレートhtmlファイル読み込み(resourcesフォルダにtemplate_01.pdf.htmlを配置)
        $html = \File::get(resource_path('pdf-template/template_01.pdf.html'));
        $pdf->writeHTML($html, $ln = false, $fill = 0, $reseth = false, $cell = false, $align = "L");

        $pdf->Output("output.pdf", "I");
    }
}
