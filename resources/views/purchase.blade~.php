<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <style>
        /*書体（ノーマルとボールドの指定*/

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

        /*
        @font-face {
            font-family: ipaexg;
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/ipaexg.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: ipaexg;
            font-style: bold;
            font-weight: bold;
            src: url('{{ storage_path('fonts/ipaexg.ttf') }}') format('truetype');
        }
        body {
            font-family: ipaexg;
        }
        */
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

        .box{
            /*overflow: hidden;*/
        }

        .box1 {
            width: 45%;
            padding-top: 0.2em;
            padding-bottom: 15px;
            margin: 2em 0;
            border-top: solid 2px;
            font-size:12px;
        }

        .box2 {
            width: 45%;
            padding-top: 0.2em;
            padding-bottom: 15px;
            float: left;
            border-top: solid 2px;
            margin-right: 10%;
            font-size:12px;
        }

        .box3 {
            width: 45%;
            padding-top: 0.2em;
            padding-bottom: 15px;
            float: left;
            border-top: solid 2px;
            font-size:12px;
        }


    </style>
    <title>C.C. Medico Co.,Ltd.</title>
</head>

<body>
    <main>
        <div>
            <h1 style="text-align: center">Purchase Order</h1>
            <p style="text-align: center">INVOICE No.{{ $main['invoice_no'] }}</p>
            <p style="text-align: right">{{ date('M j , Y') }}</p>
        </div>


        <table class="sushiTable">
            <tr>
                <th style="text-align:left; width: 30%;">Shipper</th>
                <td>{{ $main['shipper'] }}<< /td>
            </tr>
            <tr>
                <th style="text-align:left;">Consignee</th>
                <td>{{ $consignee_name }}<< /td>
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
                <th style="text-align:left;">Sailing on (ETD)</th>
                <td>{{ $main['sailing_on'] }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Arriving on (ETA)</th>
                <td>{{ $main['arriving_on'] }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Invoice Deadline</th>
                <td>{{ $main['expiry'] }}</td>
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
                    <td style="text-align:right;">{{ number_format($val[4], 2) }}</td>
                    <td>USD</td>
                    <td style="text-align:right;">{{ number_format($val[5], 2) }}</td>

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
                <td style="text-align:right;">{{ number_format($total['amount_total'], 2) }}</td>

            </tr>
        </table>


        <br><br>
        <div>After the payment at USD {{ number_format($total['amount_total'], 2) }} was confirmed, an order becomes
            effective.</div>

        <br>


        <div><b>Importer</b></div>
        <table>
            <tr>
                <th style="text-align:left;">president</th>
                <td> </td>
                <td>{{ $main['president'] }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">address</th>
                <td> </td>
                <td>{{ $main['bill_company_address_line1'] }},{{ $main['bill_company_address_line2'] }},{{ $main['bill_company_city'] }},{{ $main['bill_company_state'] }},{{ $main['bill_company_zip'] }},{{ $main['bill_company_country'] }}
                </td>
            </tr>
            <tr>
                <th style="text-align:left;">phone</th>
                <td> </td>
                <td>{{ $main['bill_company_phone'] }}</td>
            </tr>

        </table>
        <br>

        <table>
            <tr>
                <td style="font-size:12px;">I DECLARE ALL THE INFORMATION CONTAINED IN THE ORDER SHEET TO BE TRUE AND
                    CORRECT.</td>
            </tr>
        </table>




        <br>
        
        <div class="box1">
            DATE
        </div>

        <div class="box1">
            SIGNATURE OF INPORTER/ORDERER
        </div>


        <div class="box">
            <div class="box2">
                NAME(PLEASE PRINT)
            </div>

            <div class="box3">
                TITLE(PLEASE PRINT)
            </div>
        </div>


        <div>
            <!--Shipment : By NIPPON EXPRESS-->
        </div><br>

        <div>
            <!--Made in Japan-->
        </div><br>

        <div>
            <!--C.C.Medico Co.,Ltd.-->
        </div>
        <br>
        <div>
            <!--<img src="{{ public_path('storage/img/premium-silk/hamada.png') }}" class="img-fluid sign-border">-->
            <!--<img src="data:image/png;base64,{{ $image_data }}">-->
        </div>
        <div>
            <!--Yoshiumi Hamada. President-->
        </div>
    </main>

    <!--footer-->
    <hr>
    <div class="col text-center " style="font-size: 11px; ">
        Copyright c 2022 C.C. Medico Co.,Ltd. All Rights Reserved.
    </div>
    <!--footer-->
</body>

</html>
