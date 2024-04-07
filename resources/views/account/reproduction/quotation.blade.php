@extends('layouts.base')

@section('content')
    <div class="container-fluid" style="padding:0;">
        <div class="d-flex align-items-center justify-content-center h3"
            style="height:50px;background: #131921;color: azure;">
            ORDER PLAN
        </div>
    </div>
    <div class="container-fluid" style="background-color: rgb(54, 54, 54);">
        <div class="container">
            <div class="progressbar">
                <div class="item"><a href="{{ route('plan') }}">PLAN</a></div>
                <div class="item active">Quotation</div>
                <div class="item"><a href="{{ route('invoice', ['quotation_no' => $uuid]) }}">INVOICE</a></div>
                <div class="item">ORDER</div>
                <div class="item">FACTORY</div>
                <div class="item">SHIP</div>
                <div class="item">ARRIVAL</div>
            </div>
        </div>
    </div>

    <div class="container mt-4">

        <form method="post">
            @csrf

            <div class="container">
                <div class="continer mt-4">
                    <div class="row">
                        <div class="col-4"></div>
                        <div class="col-4">
                            <h2 class="text-center">Quotation</h2>
                            <h6 class="text-center">{{ $uuid }}</h6>
                        </div>
                        <div class="col-4">

                            <div class="text-right pb-2">
                                <button formaction="{{ 'generate_quotation_pdf' }}" type="submit" class="btn btn-warning btn-lg" style="width:200px">PRINTOUT</button>
                            </div>

                            <div class="text-right pb-2">
                                <button formaction="{{ 'invoice_confirm' }}" type="submit" class="btn btn-warning btn-lg" style="width:200px">INVOICE</button>
                            </div>

                        </div>
                    </div>
                </div>


                <table class="table table-bordered" style="font-size: 16px;">
                    <tr>
                        <th style="width:20%" class="table-gray">Shipper</th>
                        <td style="width:80%;">{{ $preference_data->shipper }}</td>
                    </tr>
                    <tr>
                        <th class="table-gray">Consignee</th>
                        <td>{{ $user['consignee'] }}</td>
                    </tr>
                    <tr>
                        <th class="table-gray">Port ofr Loading</th>
                        <td>
                            {{ $preference_data->port_of_loading }}
                        </td>
                    </tr>
                    <tr>
                        <th class="table-gray">Final Destination</th>
                        <!--<td>{{-- $user['state'].', '.$user['country'] --}}</td>-->
                        <td><input type="text" name="final_destination" class="form-control"
                                placeholder="Please Enter The Final Destination"></td>
                    </tr>
                    <tr>
                        <th class="table-gray">Sailing on (ETD)</th>
                        <td>{{ $sailing_on }}</td>
                    </tr>
                    <tr>
                        <th class="table-gray">Arriving on (ETA)</th>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="table-gray">EXPIRY</th>
                        <td>{{ $preference_data->expiry }}</td>
                    </tr>
                </table>

                <table class="table table-bordered mt-4" style="font-size: 16px;">
                    <tr>
                        <th style="width:5%" class="table-gray"></th>
                        <th style="width:43%" class="table-gray">Description of goods</th>
                        <th style="width:10%;text-align:center;" class="table-gray">Quantity</th>
                        <th style="width:10%;text-align:center;" class="table-gray">Ctn</th>
                        <th colspan="2" style="width:22%;text-align:center;" class="table-gray">Unit Price (Ex-Work)
                        </th>
                        <th colspan="2" style="width:10%;text-align:center;" class="table-gray">Amount</th>
                    </tr>

                    <?php $no = 1; ?>

                    @foreach ($items as $union)
                        <tr>
                            <td>{{ $no }}</td>
                            <td>{{ '[' . $union[0] . ']' . $union[1] }} </td>
                            <!--<td style="text-align:right;">{{ $union[3] * 24 }}</td>-->
                            <td style="text-align:right;">{{ $union[4] }}</td>
                            <td style="text-align:right;">{{ $union[3] }}</td>
                            <td>USD</td>
                            <td style="text-align:right;">{{ number_format($union[2]) }}</td>
                            <td>USD</td>
                            <!--
                        <td style="text-align:right;">{{ number_format($union[2] * $union[3] * 24) }}</td>
                        -->
                            <td style="text-align:right;">{{ number_format($union[5]) }}</td>

                        </tr>
                        <?php $no = $no + 1; ?>
                    @endforeach

                    <tr>
                        <td></td>
                        <th>TOTAL</th>
                        <th style="text-align:right;">{{ number_format($quantity_total) }}</th>
                        <th style="text-align:right;">{{ number_format($ctn_total) }}</th>
                        <th colspan="2"></th>
                        <th>USD</th>
                        <th style="text-align:right;">{{ number_format($amount_total) }}</th>
                    </tr>
                </table>

                <div>After the payment at USD {{ number_format($amount_total) }} was confirmed, an order becomes
                    effective.</div>

                <div class="h5 mt-4">Bank information:</div>
                <table>
                    <tr>
                        <th>Bank</th>
                        <td> </td>
                        <td>{{ session('bank') }}</td>
                    </tr>
                    <tr>
                        <th>Branch</th>
                        <td> </td>
                        <td>{{ session('branch') }}</td>
                    </tr>
                    <tr>
                        <th>SWIFT Code</th>
                        <td> </td>
                        <td>{{ session('swift_code') }}</td>
                    </tr>
                    <tr>
                        <th>Account #</th>
                        <td> </td>
                        <td>{{ session('account') }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td> </td>
                        <td>{{ session('name') }}</td>
                    </tr>
                </table>

                <div class="mt-4">Shipment : By {{ $preference_data->shipment_by }}</div>
                <div>Made in Japan</div>
                <div>C.C.Medico Co.,Ltd.</div>

                <div>
                    <img src="{{ asset('storage/premium-silk/hamada.png') }}" class="img-fluid sign-border">
                </div>
                <div>
                    Yoshiumi Hamada. President
                </div>
                <input type="hidden" name="quotation_no" value="{{ $uuid }}">
                
        </form>
    </div>
    <!--end of container-->
@stop
