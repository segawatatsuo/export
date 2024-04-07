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
                <div class="item"><a href="{{ route('home') }}">PLAN</a></div>
                <div class="item">Quotation</div>
                <div class="item">INVOICE</div>
                <div class="item active">ORDER</div>
                <div class="item">FACTORY</div>
                <div class="item">SHIP</div>
                <div class="item">ARRIVAL</div>
            </div>
        </div>
    </div>


<!-- フラッシュメッセージ -->
@if (session( 'note' ))
<div class="flash_message bg-danger text-center py-3 my-0"><h3>{{ session('note') }}</h3></div>
@endif


    <div class="container mt-4">
        <form method="post">
            @csrf
            <div class="container">

                <table class="table">
                    <tbody>
                        <tr>
                            <td colspan="3" class="text-center">
                                <h4>C.C.Medico Co.,Ltd.</h4>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-center">
                                {{ $main['preference_data']->shipper_address1 }},
                                {{ $main['preference_data']->shipper_address2 }},
                                {{ $main['preference_data']->shipper_address3 }},
                                {{ $main['preference_data']->shipper_address4 }},
                                {{ $main['preference_data']->shipper_address5 }}
                                {{ $main['preference_data']->shipper_address6 }}
                                <br>

                                Tel:{{ $main['preference_data']->tel }} FAX:{{ $main['preference_data']->fax }}

                            </td>
                        </tr>
                        <tr>
                            <td class="text-right font-weight-bold">
                                <h5>Consignee:</h5>
                            </td>

                            <!--顧客住所 -->
                            <td class="text-left">
                                <div class="font-weight-bold">
                                    <h5>{{ $consignee_name }}</h5>
                                </div>

                                {{ $consignee_address_line1 }},
                                {{ $consignee_address_line2 }},
                                {{ $consignee_city }},
                                {{ $consignee_state }},
                                {{ $consignee_country }}
                                {{ $consignee_zip }}
                                <br>

                                Tel:{{ $consignee_phone }}

                            </td>



                            <td rowspan="2" class="text-right">
                                {{ $main['day'] }}<br>
                                Invoice No.<br>
                                {{ $main['invoice_no'] }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="text-right font-weight-bold">
                                    <h5>Payment Method</h5>
                                </div>
                            </td>
                            <td>
                                <select name="payment_method" class="form-control">
                                    @if(session()->get('type')=="fedex")
                                    <option value="Payoneer">Payoneer</option>
                                    @else
                                    <option value="Payoneer">Payoneer</option>
                                    <option value="bank">bank</option>

                                    @endif
                                    
                                </select>


                            </td>
                        </tr>

                        <!--
                    <tr>
                        <td colspan="3" class="text-right"><button type="submit" class="btn btn-warning btn-lg">PRINTOUT</button></td>
                    </tr>

                    <tr>
                        <td colspan="3" class="text-right"><button type="submit" class="btn btn-warning btn-lg">ORDER</button></td>
                    </tr>
                -->
                        <!--
                <div class="text-right pb-2">
                    <button formaction="{{-- 'generate_invoice_pdf' --}}" type="submit" class="btn btn-warning btn-lg" style="width:200px">PRINTOUT</button>
                </div>

                <div class="text-right pb-2">
                    <button formaction="{{-- 'order' --}}" type="submit" class="btn btn-warning btn-lg" style="width:200px">ORDER</button>
                </div>
            -->

                    </tbody>
                </table>





                <table class="table table-bordered" style="font-size: 16px;">
                    <tr>
                        <th style="width:20%" class="table-gray">Shipper</th>
                        <td style="width:80%;">{{ $main['shipper'] }}</td>
                    </tr>
                    <tr>
                        <th class="table-gray">Consignee</th>
                        <td>{{ $consignee_name }}</td>
                    </tr>
                    <tr>
                        <th class="table-gray">Port of Loading</th>
                        <td>
                            {{ $main['port_of_loading'] }}
                        </td>
                    </tr>
                    <tr>
                        <th class="table-gray">Final Destination</th>
                        <td>{{ $main['final_destination'] }}</td>
                    </tr>
                    <tr>
                        <th class="table-gray">Shipping on (ETD)</th>
                        <td>{{-- session()->get('sailing_on') --}}{{ $main['sailing_on'] }}</td>
                    </tr>
                    <tr>
                        <th class="table-gray">Arriving on (ETA)</th>
                        <td>{{ $main['arriving_on'] }}</td>
                    </tr>
                    <tr>
                        <th class="table-gray">Quotaition expires</th>
                        <td>{{-- session()->get('expiry_days') --}}{{ $main['expiry'] }}</td>
                    </tr>
                </table>

                <table class="table table-bordered mt-4" style="font-size: 16px;">
                    <tr>
                        <th style="width:5%" class="table-gray"></th>
                        <th style="width:43%" class="table-gray">Description of goods</th>
                        
                        <th style="width:10%;text-align:center;" class="table-gray">Ctn</th>
                        <th style="width:10%;text-align:center;" class="table-gray">Quantity</th>
                        <th colspan="2" style="width:22%;text-align:center;" class="table-gray">Unit Price (Ex-Work)
                        </th>
                        <th colspan="2" style="width:10%;text-align:center;" class="table-gray">Amount</th>
                    </tr>

                    <?php $no = 1; ?>

                    @foreach ($items as $union)
                        <tr>
                            <td>{{ $no }}</td>
                            <td>{{ '[' . $union[0] . ']' . $union[1] }} </td>
                            
                            <td style="text-align:right;">{{ number_format($union[3]) }}</td>
                            <td style="text-align:right;">{{ number_format($union[2]) }}</td>
                            <td style="text-align:right;">USD</td>
                            <td style="text-align:right;">{{ number_format($union[4],2) }}</td>
                            <td style="text-align:right;">USD</td>
                            <!--
                        <td style="text-align:right;">{{ number_format($union[2] * $union[3] * 24) }}</td>
                        -->
                            <td style="text-align:right;">{{ number_format($union[5],2) }}</td>

                        </tr>
                        <?php $no = $no + 1; ?>
                    @endforeach

                    <tr>
                        <td></td>
                        <th>TOTAL</th>
                        
                        <th style="text-align:right;">{{ number_format($total['ctn_total']) }}</th>
                        <th style="text-align:right;">{{ number_format($total['quantity_total']) }}</th>
                        <th colspan="2"></th>
                        <th style="text-align:right;">USD</th>
                        <th style="text-align:right;">{{ number_format($total['amount_total'],2) }}</th>
                    </tr>
                </table>

                <div>After successful payment of USD {{ number_format($total['amount_total'],2) }} ,this order will be confirmed.</div>

                <table class="table">
                    <tr>
                        <td>
                            <div class="text-right pb-2">

                                 <button formaction="{{ 'order_confirm' }}" type="submit" class="btn btn-warning btn-lg"
                                    style="width:230px">Order confirm</button>

                            </div>
                        </td>
                    </tr>
                </table>

<!--
                <div class="h5 mt-4">Bank information:</div>
                <table>
                    <tr>
                        <th>Bank</th>
                        <td> </td>
                        <td>{{-- session('bank') --}}</td>
                    </tr>
                    <tr>
                        <th>Branch</th>
                        <td> </td>
                        <td>{{-- session('branch') --}}</td>
                    </tr>
                    <tr>
                        <th>SWIFT Code</th>
                        <td> </td>
                        <td>{{-- session('swift_code') --}}</td>
                    </tr>
                    <tr>
                        <th>Account #</th>
                        <td> </td>
                        <td>{{-- session('account') --}}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td> </td>
                        <td>{{-- session('name') --}}</td>
                    </tr>
                </table>

                @if(session()->get('type')=="fedex")
                <div class="mt-4">Shipment : By Fedex</div>
                @elseif(session()->get('type')=="air")
                <div class="mt-4">Shipment : By Air</div>
                @elseif(session()->get('type')=="ship")
                <div class="mt-4">Shipment : By Ship</div>
                @endif
                <div>Made in Japan</div>
                <div>C.C.Medico Co.,Ltd.</div>

                <div>
                    <img src="{{ asset('storage/premium-silk/hamada.png') }}" class="img-fluid sign-border">
                </div>
                <div>
                    Yoshiumi Hamada. President
                </div>
            -->

                <input type="hidden" name="quotation_no" value="{{ $main['quotation_no'] }}">
                <input type="hidden" name="id" value="{{ $main['uuid'] }}">
        </form>
    </div>
    <!--end of container-->
@stop
