<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <style>
        @font-face {
            font-family: migmix;
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/migmix-2p-regular.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: migmix;
            font-style: bold;
            font-weight: bold;
            src: url("{{ storage_path('fonts/migmix-2p-bold.ttf') }}") format('truetype');
        }

        body {
            font-family: migmix;
            line-height: 100%;
        }

        .main_image {
            width: 100%;
            text-align: center;
            margin: 10px 0;
        }

        .main_image img {
            width: 90%;
        }

        .sushiTable {
            border: 1px solid #000;
            border-collapse: collapse;
            width: 100%;
        }

        .sushiTable tr th {
            background: #87cefa;
            padding: 5px;
            border: 1px solid #000;
        }

        .sushiTable tr td {
            padding: 5px;
            border: 1px solid #000;
        }

        /*headerを入れるので余白をゼロに*/
        @page { 
            margin: 0px;
        }
        .header{
            /*width: 100vw;
            height: 100vh;*/
            width: 850px;
            height: 80px;
            background-image:url("data:image/png;base64,{{ $image_data2 }}");
            background-repeat: no-repeat;
            background-size:contain;
        }
        /*余白*/
        .contents{
            padding-top: 90px;
            padding-top: 0px;
            padding-left: 50px;
            padding-right: 50px;
            padding-bottom: 20px;
        }


    </style>
    <title>C.C. Medico Co.,Ltd.</title>
</head>

<body>
<div class="header"></div>

    <div class="contents">
    <main>
        <div>
            <h3 style="text-align: center">C.C. Medico Co.,Ltd.</h3>
            <p style="text-align: center; line-height: 150%;">
                1-12-1 sibuy markcity W22 JP, Dogenzaka,, Shibuya Ku,, Tokyo,, 150-0043 Japan<br>
                Tel:(81)3 5942-5536 FAX:(81)3 5942-5529
            </p>
            <p style="text-align: right">by {{ $type }}</p>
        </div>

        <div>
            <table class="sushiTable">
                <tr>
                    <td><b>Consignee:</b></td>
                    <td style="line-height: 120%;">
                        {{ $main['consignee'] }}<br>
                        {{ $main['address_line1']}},{{ $main['address_line2']}},{{ $main['city']}},{{ $main['state']}},{{ $main['country']}}<br>
                        Tel:&nbsp;{{ $main['phone']}}&nbsp;Fax:&nbsp;{{ $main['fax']}}
                    </td>
                    <td style="line-height: 120%;">
                        {{ date('M j , Y') }}<br>
                        Invoice:No <br>{{ $main['invoice_no'] }}
                    </td>
                </tr>
            </table>
        </div>

<br>
        <table class="sushiTable">
            <tr>
                <td colspan="2" style="text-align:center">1.PROFORMA INVOICE</td>
            </tr>

            <tr>
                <th style="text-align:left; width: 30%;">Shipper</th>
                <td>{{ $main['shipper'] }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Consignee</th>
                <td>{{ $consignee_name }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Port ofr Loading</th>
                <td>
                    {{ $main['port_of_loading'] }}
                </td>
            </tr>
            <tr>
                <th style="text-align:left;">Final Destination</th>
                <td>{{ $main['final_destination'] }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Shipping on (ETD)</th>
                <td>{{-- session()->get('sailing_on') --}}{{ $main['sailing_on'] }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Arriving on (ETA)</th>
                <td>{{ $main['arriving_on'] }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Quotaition Deadline</th>
                <td>{{-- session()->get('expiry_days') --}}{{ $main['expiry'] }}</td>
            </tr>
        </table>
        <br>
        <table class="sushiTable">
            <tr>
                <th></th>
                <th style="text-align:left;">Description of goods</th>

                <th style="text-align:left;">Ctn</th>
                <th style="text-align:left;">Quantity</th>
                <th style="text-align:left;" colspan="2">Unit Price (Ex-Work) </th>
                <th style="text-align:left;" colspan="2">Amount</th>
            </tr>
            <?php $n = 1; ?>
            @foreach ($items as $key => $val)
                <tr>
                    <td>{{ $n }}</td>
                    <td>[{{ $val[0] }}] {{ $val[1] }} </td>
                    
                    <td style="text-align:right;">{{ number_format($val[3]) }}</td>
                    <td style="text-align:right;">{{ number_format($val[2]) }}</td>
                    <td>USD</td>
                    <td style="text-align:right;">{{ number_format($val[4],2) }}</td>
                    <td>USD</td>
                    <td style="text-align:right;">{{ number_format($val[5],2) }}</td>
                </tr>
                <?php $n += 1; ?>
            @endforeach
            <tr>
                <td></td>
                <td>TOTAL</td>
                
                <td style="text-align:right;">{{ number_format($total['ctn_total']) }}</td>
                <td style="text-align:right;">{{ number_format($total['quantity_total']) }}</td>
                <td colspan="2"></td>
                <td>USD</td>
                <td style="text-align:right;">{{ number_format($total['amount_total'],2) }}</td>
            </tr>
        </table>

        <br><br>
        <div>After the payment at USD {{ number_format($total['amount_total'],2) }} was confirmed, an order becomes
            effective.</div><br>
        <div>Bank information:</div>
        <br>
        <table>
            <tr>
                <th style="text-align:left;">Bank</th>
                <td> </td>
                <td>{{ session('bank') }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Branch</th>
                <td> </td>
                <td>{{ session('branch') }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">SWIFT Code</th>
                <td> </td>
                <td>{{ session('swift_code') }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Account #</th>
                <td> </td>
                <td>{{ session('account') }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Name</th>
                <td> </td>
                <td>{{ session('name') }}</td>
            </tr>
        </table>
        <br>
        <div>Shipment : By NIPPON EXPRESS</div><br>

        <div>Made in Japan</div><br>

        <div>C.C.Medico Co.,Ltd.</div>
        <br>
        <div>
            <!--<img src="{{ public_path('storage/img/premium-silk/hamada.png') }}" class="img-fluid sign-border">-->
            <img src="data:image/png;base64,{{ $image_data }}">
        </div>
        <div>
            Yoshiumi Hamada. President
        </div>
    </main>

    <!--footer-->
    <hr>
    <div class="col text-center " style="font-size: 11px; ">
        Copyright c 2022 C.C. Medico Co.,Ltd. All Rights Reserved.
    </div>
    <!--footer-->
    </div>
</body>

</html>
