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
                    <h1 style="text-align: center">Quotation</h1>
                    <p style="text-align: center">No.{{ $quotation_no }}</p>
                    <p style="text-align: right">by {{ $type }}</p>
                    <p style="text-align: right">{{ date('M j , Y') }}</p>
                </div>

                <table class="sushiTable">
                    <tr>
                        <th style="text-align:left; width: 30%;">Shipper</th>
                        <td>{{ $main[2] }}</td>
                    </tr>
                    <tr>
                        <th style="text-align:left;">Consignee</th>
                        <td>{{ $consignee_name }}</td>
                    </tr>
                    <tr>
                        <th style="text-align:left;">Port of Loading</th>
                        <td>
                            {{ $main[4] }}
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align:left;">Final Destination</th>
                        <td>{{ $main[5] }}</td>
                    </tr>
                    <tr>
                        <th style="text-align:left;">Shipping on (ETD)</th>
                        <td>{{-- session()->get('sailing_on') --}}{{ $main[6] }}</td>
                    </tr>
                    <tr>
                        <th style="text-align:left;">Arriving on (ETA)</th>
                        <td>{{ $main[7] }}</td>
                    </tr>
                    <tr>
                        <th style="text-align:left;">Quotaition Deadline</th>
                        <td>{{-- session()->get('expiry_days') --}}{{ $main[8] }}</td>
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

                        <td style="text-align:right;">{{ number_format($total[1]) }}</td>
                        <td style="text-align:right;">{{ number_format($total[0]) }}</td>
                        <td colspan="2"></td>
                        <td>USD</td>
                        <td style="text-align:right;">{{ number_format($total[2], 2) }}</td>
                    </tr>
                </table>

                <br><br>
                <div>After the payment at USD {{ number_format($total[2], 2) }} was confirmed, an order becomes
                    effective.
                </div><br>

                <!--
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
            -->


                @if (session()->get('type') == 'fedex')
                    <div>Shipment : By Fedex</div><br>
                @elseif(session()->get('type') == 'air')
                    <div>Shipment : By Air</div><br>
                @elseif(session()->get('type') == 'ship')
                    <div>Shipment : By Ship</div><br>
                @endif


                <!--
                <div>Made in Japan</div><br>
                <div>C.C.Medico Co.,Ltd.</div>
                <br>
                <div>
                    <img src="data:image/png;base64,{{ $image_data }}">
                </div>
                <div>
                    Yoshiumi Hamada. President
                </div>
            -->
            <div>This quotation is valid until [ {{ session()->get('expiryaddday') }}  ]</div>

            </main>

            <!--footer-->
            <hr>
            <div class="col text-center " style="font-size: 11px; ">
                Copyright c 2022 C.C. Medico Co.,Ltd. All Rights Reserved.
            </div>
            <!--footer-->
        </div>
    </div>

</body>

</html>
