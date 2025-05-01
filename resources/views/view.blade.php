@extends('layouts.top')

@section('content')

<div class="container-fluid" style="padding:0;">
    <div class="d-flex align-items-center justify-content-center h3" style="height:50px;background: #131921;color: azure;">
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
        <h3>{{ session('flash_message') }}</h3>
    </div>
@endif

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

        @php $x = 0; @endphp
        @while (count($items) > $x)
            @php
                $group = $groups[$x]['group'] ?? 'Unnamed Group';
                $firstItem = $items[$x][0] ?? null;
            @endphp

            <!-- グループ名 -->
            <div class="row mt-3">
                <div class="col-lg-12">
                    <h3>{{ $group }}</h3>
                </div>
            </div>
            <hr class="top">

            @if ($firstItem)
                <div class="row">
                    <div class="col-lg-12 mb-5">
                        <h4>{{ $firstItem['category'] ?? '' }} {{ $firstItem['group'] ?? '' }}</h4>
                    </div>
                </div>
            @endif

            <!-- 商品一覧 -->
            <div class="row">
                @foreach ($items[$x] as $item)
                    @php
                        $productCode = $item['product_code'] ?? 'unknown';
                        $itemId = json_encode("#" . $productCode);
                        $pcsId = json_encode("#" . $productCode . '-PCS');
                        $unit = $item['units'] ?? 1;
                    @endphp

                    <div class="col-sm-15 col-6">
                        <div>
                            <a href="{{ route('item', ['id' => $item['id'] ?? 0]) }}" target="_blank">
                                <img src="{{ asset('storage/img/' . ($item['img1'] ?? 'noimage.png')) }}" class="img-fluid" alt="">
                            </a>
                        </div>
                        <hr>
                        <div class="line_2">[{{ $item['product_code'] ?? '' }}]{{ $item['kind'] ?? '' }}</div>
                        <div>＄{{ number_format($item['price'] ?? 0, 2) }}</div>

                        <div class="row">
                            <div class="caption1 col-6">CARTON</div>
                            <div class="col-6 text-danger text-right">[ {{ $item['stock'] ?? 0 }} ]</div>
                        </div>

                        @if (($item['stock'] ?? 0) == 0)
                            <input type="text" id="{{ $productCode }}" class="txtCal outofstock"
                                name="item[{{ $productCode }} | {{ $item['kind'] ?? '' }} | {{ $item['price'] ?? 0 }} | {{ $item['group'] ?? '' }}]"
                                value="" placeholder="Out of stock" readonly>
                        @else
                            <input type="text" id="{{ $productCode }}" class="txtCal"
                                name="item[{{ $productCode }} | {{ $item['kind'] ?? '' }} | {{ $item['price'] ?? 0 }} | {{ $item['group'] ?? '' }}]"
                                value="" placeholder="Enter the number">
                        @endif

                        <div class="caption1">PCS</div>
                        <input type="text" readonly style="border-width:1px"
                            class="border-top-0 border-right-0 border-left-0" id="{{ $productCode }}-PCS"
                            value="" placeholder="">
                    </div>

                    <script>
                        $(document).ready(function() {
                            $("#ItemList").on('input', {!! $itemId !!}, function() {
                                let value = $(this).val();
                                let pcsSelector = {!! $pcsId !!};
                                if ($.isNumeric(value)) {
                                    $(pcsSelector).val(value * {{ $unit }});
                                } else {
                                    $(pcsSelector).val("");
                                }
                            });
                        });
                    </script>
                @endforeach
            </div>

            @php $x++; @endphp
            <br>
        @endwhile
    </form>

</div>
@stop
