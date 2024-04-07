@extends('layouts.base')

@section('content')
<div class="container-fluid" style="padding:0;">
    <div class="d-flex align-items-center justify-content-center h3" style="height:50px;background: #131921;color: azure;">
        ORDER PLAN
    </div>
</div>
<div class="container-fluid" style="background-color: rgb(54, 54, 54);">
    <div class="container">
        <div class="progressbar">
            <div class="item"><a href="{{ route('plan') }}">PLAN</a></div>
            <div class="item">Quotation</div>
            <div class="item  active">INVOICE</div>
            <div class="item">ORDER</div>
            <div class="item">FACTORY</div>
            <div class="item">SHIP</div>
            <div class="item">ARRIVAL</div>
        </div>
    </div>
</div>






<div class="container mt-4">
    <form action="{{ 'generate_invoice_pdf' }}" method="post">
    @csrf
        <div class="container">

        <table class="table"> 
            <tbody>
                <tr>
                    <td colspan="3" class="text-center"><h4>C.C.Medico Co.,Ltd.</h4></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-center">
                        {{ $main['preference_data']->shipper_address1 }},
                        {{ $main['preference_data']->shipper_address2 }},
                        {{ $main['preference_data']->shipper_address3 }},
                        {{ $main['preference_data']->shipper_address4}},
                        {{ $main['preference_data']->shipper_address5}}
                        {{ $main['preference_data']->shipper_address6 }}
                        <br>
                        
                        Tel:{{ $main['preference_data']->tel }} FAX:{{ $main['preference_data']->fax }}

                    </td>
                </tr>
                <tr>
                    <td class="text-right font-weight-bold"><h5>Consignee:</h5></td>

                    <!--顧客住所 -->
                    <td class="text-left">
                        <div class="font-weight-bold"><h5>{{ $main['consignee'] }}</h5></div>

                        {{ $user['address_line1'] }},
                        {{ $user['address_line2'] }},
                        {{ $user['city'] }},
                        {{ $user['state']}},
                        {{ $user['country']}}
                        {{ $user['zip'] }}
                        <br>
                        
                        Tel:{{ $user['phone']}} FAX:{{ $user['fax'] }}

                    </td>
                    
                    
                    
                    <td rowspan="2" class="text-right">
                        {{ $main['day'] }}<br>
                        Invoice No.<br>
                        {{ $main['invoice_no'] }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-center"><h2>1.PROFORMA INVOICE</h2></td>
                </tr>
                <!--
                <tr>
                    <td colspan="3" class="text-right"><button type="submit" class="btn btn-warning btn-lg">PRINTOUT</button></td>
                </tr>

                <tr>
                    <td colspan="3" class="text-right"><button type="submit" class="btn btn-warning btn-lg">ORDER</button></td>
                </tr>
            -->

            <div class="text-right pb-2">
                <button formaction="{{ 'generate_invoice_pdf' }}" type="submit" class="btn btn-warning btn-lg" style="width:200px">PRINTOUT</button>
            </div>

            <div class="text-right pb-2">
                <button formaction="{{ 'order' }}" type="submit" class="btn btn-warning btn-lg" style="width:200px">ORDER</button>
            </div>


            </tbody>
        </table>





            <table class="table table-bordered" style="font-size: 16px;">
                <tr>
                    <th style="width:20%" class="table-gray">Shipper</th>
                    <td style="width:80%;">{{ $main['shipper'] }}</td>
                </tr>
                <tr>
                    <th class="table-gray">Consignee</th>
                    <td>{{ $main['consignee'] }}</td>
                </tr>
                <tr>
                    <th class="table-gray">Port ofr Loading</th>
                    <td>
                    {{ $main['port_of_loading'] }}
                    </td>
                </tr>
                <tr>
                    <th class="table-gray">Final Destination</th>
                    <td>{{ $main['final_destination'] }}</td>
                </tr>
                <tr>
                    <th class="table-gray">Sailing on (ETD)</th>
                    <td>{{ $main['sailing_on'] }}</td>
                </tr>
                <tr>
                    <th class="table-gray">Arriving on (ETA)</th>
                    <td></td>
                </tr>
                <tr>
                    <th class="table-gray">EXPIRY</th>
                    <td>{{  $main['expiry'] }}</td>
                </tr>
            </table>

            <table class="table table-bordered mt-4" style="font-size: 16px;">
                <tr>
                    <th style="width:5%" class="table-gray"></th>
                    <th style="width:43%" class="table-gray">Description of goods</th>
                    <th style="width:10%;text-align:center;" class="table-gray">Quantity</th>
                    <th style="width:10%;text-align:center;" class="table-gray">Ctn</th>
                    <th colspan="2" style="width:22%;text-align:center;" class="table-gray">Unit Price (Ex-Work) </th>
                    <th colspan="2" style="width:10%;text-align:center;" class="table-gray">Amount</th>
                </tr>

                <?php $no = 1; ?>

                @foreach($items as $union)
                <tr>
                    <td>{{ $no }}</td>
                    <td>{{ '['. $union[0].']'.$union[1] }} </td>
                    
                    <td style="text-align:right;">{{ $union[2] }}</td>
                    <td style="text-align:right;">{{ $union[3] }}</td>
                    <td>USD</td>
                    <td style="text-align:right;">{{ number_format($union[4]) }}</td>
                    <td>USD</td>
                    <!--
                    <td style="text-align:right;">{{ number_format($union[2]*$union[3] * 24) }}</td>
                    -->
                    <td style="text-align:right;">{{ number_format($union[5]) }}</td>
                    
                </tr>              
                <?php $no = $no + 1; ?>
                @endforeach

                <tr>
                <td></td>
                <th>TOTAL</th>
                <th style="text-align:right;">{{ number_format( $total['quantity_total'] ) }}</th>
                <th style="text-align:right;">{{ number_format( $total['ctn_total'] ) }}</th>
                <th colspan="2"></th>
                <th>USD</th>
                <th style="text-align:right;">{{ number_format( $total['amount_total'] ) }}</th>
            </tr>
            </table>

            <div>After the payment at USD {{ number_format( $total['amount_total'] ) }} was confirmed, an order becomes effective.</div>

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

            <div class="mt-4">Shipment : By {{ $main['preference_data']->shipment_by }}</div>
            <div>Made in Japan</div>
            <div>C.C.Medico Co.,Ltd.</div>

            <div>
                <img src="{{ asset('storage/premium-silk/hamada.png') }}" class="img-fluid sign-border">
            </div>
            <div>
                Yoshiumi Hamada. President
            </div>
            <input type="hidden" name="quotation_no" value="{{ $main['uuid'] }}" >
    </form>
</div>
<!--end of container-->
@stop