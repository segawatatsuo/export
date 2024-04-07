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
    <form action="{{ 'quotation' }}" method="post">
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
        <div class="row mt-3">
            <div class="col-lg-12">
                <!--group -->
                <h3>PREMIUM-SILK</h3>
            </div>
        </div>
        <hr class="top">
        <div class="row">
            <div class="col-lg-12 mb-5">
                <!--<h4>AirStocking® Premier Silk 120g</h4>-->
                <h4>{{ $premium_silks[0]->title_header }}</h4>
            </div>
        </div>

        <div class="row">
            @foreach($premium_silks as $ps)
            <div class="col-sm-15 col-6">
                <div><img src="{{ asset('storage/img/'.$ps->img1) }}" class="img-fluid" alt=""></div>
                <hr>
                <div>[{{ $ps->product_code }}]{{ $ps->color }}</div>
                <div>＄{{ $ps->price }} </div>
                <div class="caption1">CARTON</div>
                <input type="text" id="{{ $ps->product_code }}" class="txtCal" name="item[{{ $ps->product_code.' | '.$ps->title_header.' | '. $ps->price}}]" value="" placeholder="Enter the number" >
                <div class="caption1">PCS</div>
                <input type="text" class="" id="{{ $ps->product_code.'-PCS' }}" value="" placeholder="">
            </div>
            @endforeach
        </div>
        <!--end of row-->


        <div class="row mt-5">
            <div class="col-lg-12">
                <h3>DIAMOND LEGS</h3>
            </div>
        </div>
        <hr class="top">


        <div class="row">
            <div class="col-lg-12 mb-5">
                <!--<h4>AirStocking® Diamond Legs 120g</h4>-->
                <h4>{{ $diamond_legs[0]->title_header }}</h4>
            </div>
        </div>

        <div class="row">

            @foreach($diamond_legs as $dl)

            <div class="col-sm-15 col-6">
                <div><img src="{{ asset( 'storage/img/'. $dl->img1 ) }}" class="img-fluid" alt=""></div>
                <hr>
                <div>[{{ $dl->product_code }}]{{ $dl->color }}</div>
                <div>＄{{ $dl->price }} </div>

                <div class="caption1">CARTON</div>
                <input type="text" id="{{ $dl->product_code }}" class="txtCal" name="item[{{ $dl->product_code.' | '.$dl->title_header.' | '. $dl->price}}]" value="" placeholder="Enter the number">
                <div class="caption1">PCS</div>
                <input type="text" class="" id="{{ $dl->product_code.'-PCS' }}" value="" placeholder="">

            </div>

            @endforeach

        </div>
        <!--end of row-->

    </form>

</div>
<!--end of container-->


@stop