@extends('../layouts.acount')


@section('content')

    <div class="container-fluid" style="padding:0;">

        <div class="container mt-1 mb-1">
            <div class="row">
            <div class="col-4">
                <span class="font-weight-bold">Consignee(Warehouse): {{ $consignee_name }}</span><br>
                {{ $consignee_address_line1 }},
                {{ $consignee_address_line2 }},
                {{ $consignee_city }},
                {{ $consignee_state }}
                {{ $consignee_country }}<br>
                phone:{{ $consignee->phone }}
                <a href="{{ route('account.consignee') }}">modification</a>
            </div>
            <div class="col-4">
                <span class="font-weight-bold">Importer (Head Office): {{ $consignee->importer_name }}</span><br>
                {{ $consignee->bill_company_address_line1 }},
                {{ $consignee->bill_company_address_line2 }},
                {{ $consignee->bill_company_city }},
                {{ $consignee->bill_company_state }}
                {{ $consignee->bill_company_country }}<br>
                phone:{{ $consignee->bill_company_phone }}
                <a href="{{ route('account.importer') }}">modification</a>
            </div>
        </div>
        </div>


        <div class="d-flex align-items-center justify-content-center h3"
            style="height:50px;background: #131921;color: azure;">
            ORDER PLAN LIST
        </div>
    </div>
    <div class="container-fluid" style="background-color: rgb(54, 54, 54);">
        <div class="container">
            <div class="progressbar">
                <div class="item active">PLAN</div>
                <div class="item active">Quotation</div>
                <div class="item active">INVOICE</div>
                <div class="item active">ORDER</div>
                <div class="item active">FACTORY</div>
                <div class="item active">SHIP</div>
                <div class="item active">ARRIVAL</div>
            </div>
        </div>
    </div>

    <!-- フラッシュメッセージ -->
    @if (session('flash_message'))
        <div class="flash_message bg-danger text-center py-3 my-0">
            <h3>{!! session('flash_message') !!}</h3>
        </div>
    @endif

    <div class="container mt-4">

        <div>
            <table class="table">
                @foreach ($data as $hoge)
                    <tr>

                        <td style="width: 15%">{{ $hoge->created_at }}</td>

                        <td style="width: 15%">

                            <a href="{{ route('quotation_repeat', ['quotation_no' => $hoge->quotation_no]) }}">
                                {{ $hoge->quotation_no }}
                            </a>

                            <a href="{{ route('generate_quotation_pdf2', ['quotation_no' => $hoge->quotation_no]) }}">
                                {{ $hoge->quotation_no }}
                            </a>
                        </td>

                        <td style="width: 15%">
                            @if (isset($hoge->invoices->invoice_no))
                            <a href="{{ route('invoice_repeat', ['invoice_no' => $hoge->invoices->invoice_no]) }}">
                                {{ $hoge->invoices->invoice_no }}
                            </a>

                            <a href="{{ route('generate_invoice_pdf2',['quotation_no' => $hoge->quotation_no]) }}">
                                {{ $hoge->invoices->invoice_no }}
                            </a>

                            @endif
                        </td>
                        <td style="width: 15%">
                            @if (isset($hoge->invoices->order_confirms->order_no))
                            <a href="{{ route('order_repeat', ['order_no' => $hoge->invoices->order_confirms->order_no]) }}">
                                {{ $hoge->invoices->order_confirms->order_no }}
                            </a>
                            @endif

                            <br>

                            @if(isset($hoge->invoices->order_confirms->payment_method))
                            <a href="{{ route('order_confirm', ['quotation_no' => $hoge->invoices->order_confirms->quotation_no,'payment_method' => $hoge->invoices->order_confirms->payment_method]) }}">
                                order
                            </a>
                            @endif
                            <br>
                            
                            @if (isset($hoge->invoices->order_confirms->order_no))
                            <a href="{{ route('order_payment', ['order_number' => $hoge->invoices->order_confirms->order_no]) }}">
                                payment
                            </a>
                            @endif

                        </td>
                        <td style="width: 15%">
                            Balance<br>
                            Progress
                        </td>

                        <td style="width: 15%">
                            @if (isset($hoge->invoices->order_confirms->order_no))
                            <!--
                            <a href="{{-- route('account.packinglist', ['order_no' => $hoge->invoices->order_confirms->order_no]) --}}">
                            -->
                            <a href="{{ asset('storage/account/packinglist.pdf') }}">Packinglist</a>
                            
                            @else
                            <a href="{{ asset('storage/account/packinglist.pdf') }}">Packinglist</a>
                            @endif
                            
                            <br><a href="{{ asset('storage/account/AirWaybill.pdf') }}">Air Waybill</a><br>
                            <a href="{{ asset('storage/account/BillOfLoading.pdf') }}">Bill of Loading</a></td>
                        <td style="width: 10%"><a href="{{ asset('storage/account/Export_Declaration.pdf') }}">Export Declaration</a></td>

                    </tr>
                @endforeach
            </table>


        </div>



    </div>

    {{ session('user[consignee]') }}

    <div class="container mt-4" id="ItemList">
        {{ $data->links() }}
    </div>

@stop
