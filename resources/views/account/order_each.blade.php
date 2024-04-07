{{-- @extends('../layouts.app') --}}
@extends('../layouts.login')

@section('content')
<!--
    <div class="container-fluid" style="padding:0;">
        <div class="d-flex align-items-center justify-content-center h3"
            style="height:50px;background: #131921;color: azure;">
            NAV YOURACCOUNT
        </div>
    </div>
-->

    <div class="container">
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-8 text-right">
                    <span><a href=" {{ route('account.index') }} ">Account Services</a> / Order History</span>
                </div>

            </div>
        </div>

        <div class="container mb-4 mt-4">
            <div class="row">
                <div class="col-md-12 text-center">
                    <!--<h3>Order</h3>-->


                    <a href="{{ route('generate_quotation_pdf2', ['quotation_no' => $order->quotation_no]) }}"
                        class="btn btn-secondary" target="_blank" rel="noopener noreferrer">Quotation</a>

                    <a href="{{ route('generate_invoice_pdf2', ['quotation_no' => $order->quotation_no]) }}"
                        class="btn btn-secondary" target="_blank">Invoice</a>

                    <a href="{{ route('ShowOrderSheet', ['order_no' => $order->order_no]) }}"
                        class="btn btn-secondary" target="_blank">Order Sheet</a>

                    @if($about=="送金画像あり")
                    <a href="{{ route('ShowPaymentSheet', ['order_no' => $order->order_no]) }}"
                        class="btn btn-secondary" target="_blank">Payment Sheet</a>
                    @else
                    {{-- 送金書のアップロード --}}
                    <a href="{{ route('payment_uploader', ['order_no' => $order->order_no]) }}"
                        class="btn btn-secondary" target="_blank">Payment Sheet Upload</a>
                    @endif

                    <a href="{{ route('Packinglist.index', ['order_no' => $order->order_no]) }}"
                        class="btn btn-secondary" target="_blank">Packing list</a>


                </div>
            </div>
        </div>

        <br>

        <div class="card-deck mx-auto">

            <div class="col-10 offset-1">
                <div class="card">

                    <div class="card-header">
                        {{ $order->created_at }}
                    </div>
                    <div class="card-body">
                        <div>
                            <table style="width: 100%;">
                                <th>ID</th>
                                <th>code</th>
                                <th>item</th>
                                <th>price</th>
                                <th>quantity</th>
                                <th>amount</th>
                                @foreach ($order->order_details as $item)
                                    <tr>
                                        <td>{{ $item->id }}</a></td>
                                        <td>{{ $item->product_code }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ number_format($item->unit_price,2) }}</td>
                                        <td>{{ number_format($item->quantity) }}</td>
                                        <td>{{ number_format($item->amount,2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6">
                                        <hr>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <td>total</td>
                                    <td class="text-left">{{ number_format($order->quantity_total) }}</td>
                                    <td class="text-left">{{ number_format($order->total_amount,2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="mt-5">
                            <!--<a href="">add</a>-->
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <!--
            <div class="col-10 offset-1">
                <div class="card">
                    <div class="card-header">注文書アップロード</div>
                    <div class="card-body">
                        <form action="{{-- route('account.img_store') --}}" enctype="multipart/form-data" method="post">
                            @csrf
                            <input type="file" name="img_path">
                            <input type="hidden" name="order_no" value="{{-- $order->order_no --}}">
                            <input type="hidden" name="user_id" value="{{-- $order->user_id --}}">
                            <input type="submit" value="アップロードする">
                        </form>
                    </div>
                    <div class="card-body">
                        {{--  @foreach ($img as $item)--}}
                            <img src="{{-- Storage::url($item->img_path) --}}" width="25%">
                        {{-- @endforeach --}}
                    </div>
                </div>
            </div>
        -->


        </div>


        <!--
        <div class="container-fluid ">
            <div class="row mt-5 mb-5 ">
                <div class="col text-center " style="font-size: 11px; ">
                    Copyright c 2022 C.C. Medico Co.,Ltd. All Rights Reserved.
                </div>
            </div>
        </div>
    -->

        <!--end of container-->
    @stop
