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
                <div class="item active">PLAN</div>
                <div class="item">Quotation</div>
                <div class="item">INVOICE</div>
                <div class="item">ORDER</div>
                <div class="item">FACTORY</div>
                <div class="item">SHIP</div>
                <div class="item">ARRIVAL</div>
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
        <h4>This order is FedEx.</h4>
        <h5>
        ■ FedEx wholesale price<br>
        &nbsp;&nbsp;&nbsp;&nbsp;<span style="color:red;">MOQ {{ $Minimum_orders }}  cartons/color, total {{ $lower_limit }}-{{ $upper_limit }} cartons</span><br>
        </h5>
    
    </div>

    {{ session('user[consignee]') }}

    <div class="container mt-4" id="ItemList">

        <form action="{{ route('quotation', ['type' => 'fedex']) }}" method="post">
            @csrf
            <!-- 計算表示 -->
            <div class="row mb-2 d-flex align-items-center">
                <div class="col-8">
                    <span class="h5">CARTON TOTAL : <span id="total_sum_value">Total here</span></span> |
                    <span class="h5">PCS TOTAL : <span id="total_sum_amount">Total here</span></span>
                </div>
                <div class="col-4 text-right">
                    <button type="submit" class="btn btn-warning btn-lg a-button-input">Quotation</button>
                </div>
            </div>
            <hr>

            <?php $x = 0; ?>
            @while (count($items) > $x)
                <!--グループ名-->
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <h3>{{ $groups[$x]['group'] }}</h3>
                    </div>
                </div>
                <hr class="top">

                <div class="row">
                    <div class="col-lg-12 mb-5">
                        <h4>{{ $items[$x][0]['category'] . ' ' . $items[$x][0]['group'] }}</h4>
                    </div>
                </div>

                <!-- 商品-->
                <div class="row">
                    @foreach ($items[$x] as $item)
                        <div class="col-sm-15 col-6">
                            <div>
                                <a href="{{ route('item', ['id' => $item['id']]) }}" target="_blank">
                                <img src="{{ asset('storage/img/' . $item['img1']) }}" class="img-fluid" alt="">
                                </a>
                            </div>

                            <hr>
                            <div class="line_2">[{{ $item['product_code'] }}]{{ $item['kind'] }}</div>

                            <div>＄{{ number_format($item['price'], 2) }} </div>

                            <div class="row">
                                <div class="caption1 col-6">CARTON</div>
                                <div class="col-6 text-danger" style="text-align: right">[ {{ $item['stock'] }} ]</div>
                            </div>

                            @if ($item['stock'] == '0')
                                <input type="text" id="{{ $item['product_code'] }}" class="txtCal outofstock"
                                    name="{{ $item['product_code'] }}"
                                    value="" placeholder="Out of stock" readonly>
                            @else
                                <input type="text" id="{{ $item['product_code'] }}" class="txtCal"
                                    name="{{ $item['product_code'] }}"
                                    value="{{ old($item['product_code']) }}"  placeholder="Enter the number">
                            @endif

                            <div class="caption1">PCS</div>
                            <input type="text" readonly style="border-width:1px" type="text"
                                class="border-top-0 border-right-0 border-left-0" name="{{ $groups[$x]['group'].'[]' }}" id="{{ $item['product_code'].'-PCS' }}"
                                value="" placeholder="">
                        </div>



                        <script>
                            $(document).ready(function() {
                                $("#ItemList").on('input', <?php echo "'#" . $item['product_code'] . "'"; ?>, function() {
                                    var get_textbox_value = $(this).val();
                                    if ($.isNumeric(get_textbox_value)) {
                                        $(<?php echo "'#" . $item['product_code'] . "-PCS'"; ?>).val(get_textbox_value * {{ $item['units'] }});
                                    }else {
                                        $(<?php echo "'#" . $item['product_code'] . "-PCS'"; ?>).val("");
                                    }
                                });
                            });
                        </script>

                    @endforeach
                </div>
                <!--カウンター-->
                <?php $x = $x + 1; ?>
                <br>
            @endwhile
            
        </form>
    </div>

@stop
