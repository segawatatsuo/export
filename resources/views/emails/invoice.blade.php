<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
    <meta name="viewport" content="target-densitydpi=device-dpi,width=device-width,maximum-scale=1.0,user-scalable=yes">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-2022-jp" />
    <meta http-equiv="Content-Language" content="en">
    <title>C.C.Medico Thank you for your order.</title>
    <style type="text/css">
        html {
            width: 100%;
            height: 100%;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        th,
        td .line {
            border: solid 1px #333;
        }
        .table-gray{
                background-color: #EEEEEE;
            }

            table .box {
    border-collapse: collapse; /* 枠線(ボーダー)を重ねて表示 */
  }
  td .box{
    border: 1px solid #000;
  }
    </style>
</head>

<body style="width:100%;height:100%;margin:0;padding:0;">


    <!-- 全体の背景色を指定するためのtable -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#ededed"
        style="width:100%;height:100%;background-color:#ededed;">
        <td valign="top">
            <!-- 横幅を指定するためのtable -->
            <table width="600" align="center" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                style="width:600px;background-color:#ffffff;">
                <!-- コンテンツを記述するためのtable -->
                <table width="100%" border="0" cellpadding="20" cellspacing="0">
                    <tr>
                        <td valign="top" align="left">

                            <!-- ヘッダーエリア -->
                            <table width="100%" border="0" cellpadding="20" cellspacing="0">
                                <tr>
                                    <td class="responsive-td" valign="top" align="left"
                                        style="background-color:#000000;">
                                        <img src="https://www.ccmedico.com/export/storage/ccm.jpg" alt="ccmedico logo"
                                            width="120" height="38" style="max-width:100%;color:#ffffff;">
                                    </td>
                                </tr>
                            </table>
                            <!-- コンテンツエリア -->






                            <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
                                <tr>
                                    <td align="center" valign="top">
                                        <table border="0" cellpadding="20" cellspacing="0" width="600" id="emailContainer">
                                            <tr>
                                                <td align="center" valign="top">
                                                    {!! nl2br($content['contents']) !!}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                    
                            <table class="box" cellpadding="5" cellspacing="0" height="100%" width="100%" id="bodyTable">
                                <tr>
                                    <th style="width:20%" class="table-gray">Shipper</th>
                                    <td style="width:80%;" class="line">{{ $content['shipper'] }}</td>
                                </tr>
                                <tr>
                                    <th class="table-gray">Consignee</th>
                                    <td class="line">{{ $content['consignee'] }}</td>
                                </tr>
                                <tr>
                                    <th class="table-gray">Port of Loading</th>
                                    <td class="line">
                                        {{ $content['port_of_loading'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-gray">Final Destination</th>
                                    <td class="line">{{-- $content['final_destination'] --}}</td>
                                </tr>
                                <tr>
                                    <th class="table-gray">Shipping on (ETD)</th>
                                    <td class="line">{{ $content['sailing_on'] }}</td>
                                </tr>
                                <tr>
                                    <th class="table-gray">Arriving on (ETA)</th>
                                    <td class="line">{{ $content['Arriving on'] }}</td>
                                </tr>
                                
                                <tr>
                                    <th class="table-gray">Quotaition Deadline</th>
                                    <td class="line">{{ session('expiry_days') }}</td>
                                </tr>
                            
                            </table>
                            <br>
                    
                            <table class="box" cellpadding="5" cellspacing="0" height="100%" width="100%" id="bodyTable">
                                <!--見出し-->
                                <tr>
                                    <th style="width:5%" class="table-gray"></th>
                                    <th style="width:43%" class="table-gray">Description of goods</th>
                                    <th style="width:10%;text-align:center;" class="table-gray">Ctn</th>
                                    <th style="width:10%;text-align:center;" class="table-gray">Quantity</th>
                                    <th colspan="2" style="width:22%;text-align:center;" class="table-gray">Unit Price (Ex-Work)</th>
                                    <th colspan="2" style="width:10%;text-align:center;" class="table-gray">Amount</th>
                                </tr>
                    
                    
                    
                    
                                <!--内容-->
                                @php
                                    $no = 1;
                                @endphp
                    
                                @foreach ( $items as $item )
                                <tr class="line">
                                    <td>{{ $no }}</td>
                                    <td class="line">{{ $item[1] }}</td>
                                    <td class="line" style="text-align:right;">{{ number_format($item[3]) }}</td>
                                    <td class="line" style="text-align:right;">{{ number_format($item[2]) }}</td>
                                    <td class="line">USD</td>
                                    <td class="line" style="text-align:right;">{{ ($item[4]) }}</td>
                                    <td class="line">USD</td>
                                    <td class="line" style="text-align:right;">{{ number_format($item[5]) }}</td>
                                </tr>
                                @php
                                    $no += 1;
                                @endphp
                                @endforeach
                    
                    
                            
                    
                                <!-- TOTAL -->
                                <tr>
                                    <td></td>
                                    <th>TOTAL</th>
                                    <th style="text-align:right;">{{ number_format($content['ctn_total']) }}</th>
                                    <th style="text-align:right;">{{ number_format($content['quantity_total']) }}</th>
                                    <th colspan="2"></th>
                                    <th>USD</th>
                                    <th style="text-align:right;">{{ number_format($content['amount_total']) }}</th>
                                </tr>
                            </table>
                    
                    
                    <br>
                    
                            <div>After the payment at USD {{ $content['amount_total'] }} was confirmed, an order becomes effective.</div>
                            
                            <div>Bank information:</div>

                            <table class="box" cellpadding="5">
                                <tr>
                                    <th align="left">Bank</th>
                                    
                                    <td class="line" colspan="2">{{ session('bank') }}</td>
                                </tr>
                                <tr>
                                    <th  align="left">Branch</th>
                                   
                                    <td class="line" colspan="2">{{ session('branch') }}</td>
                                </tr>
                                <tr>
                                    <th  align="left">SWIFT Code</th>
                                    
                                    <td class="line" colspan="2">{{ session('swift_code') }}</td>
                                </tr>
                                <tr>
                                    <th  align="left">Account #</th>
                                    
                                    <td class="line" colspan="2">{{ session('account') }}</td>
                                </tr>
                                <tr>
                                    <th  align="left">Name</th>
                                    
                                    <td class="line" colspan="2">{{ session('name') }}</td>
                                </tr>
                            </table>
                            <br>
                    
                            <div>Shipment : By {{ session('type') }}</div>
                            <div>Made in Japan</div>
                            <div>C.C.Medico Co.,Ltd.</div>

                            <div>
                                Yoshiumi Hamada. President
                            </div>
                            <br>









                            <!-- フッターエリア -->
                            <table width="100%" border="0" cellpadding="20" cellspacing="0">
                                <tr>
                                    <td class="responsive-td" valign="top" align="left"
                                        style="background-color:#000000;">
                                        <font size="2" color="#ffffff" style="font-size:14px;color:#ffffff;">
                                            C.C.Medico Co.,Ltd.<br>
                                            1-12-1 sibuy markcity W22 JP, Dogenzaka,, Shibuya Ku,, Tokyo,, 150-0043
                                            Japan<br>
                                            phone：<a href="tel:+81-3-6897-4086"
                                                style="color:#ffffff;">+81-3-6897-4086</a><br>
                                            <br>
                                            Copyright c 2022 C.C. Medico Co.,Ltd. All Rights Reserved.
                                        </font>
                                    </td>
                                </tr>
                            </table>


                        </td>
                    </tr>
                </table>
            </table>
        </td>
    </table>

</body>

</html>
