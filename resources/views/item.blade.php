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
    <form action="{{ route('quotation',['type'=>'fedex']) }}" method="post">
        @csrf

        <div class="row mt-3">
            <div class="col-lg-12">
                <!--group -->
                <h3>{{ $product->group }} {{ $product->product_code }} {{ $product->color }}</h3>
            </div>
        </div>
        <hr class="top">
        <div class="row">
            <div class="col-lg-12 mb-5">
                <!--<h4>AirStocking® Premier Silk 120g</h4>-->
                <h4></h4>
            </div>
        </div>

        <div class="row">

            <div class="col-6"><img src="{{ asset('storage/img/' . $product['img1']) }}" class="img-fluid" alt=""></div>
            <div class="col-6">説明文をここに</div>
        </div>
        <!--end of row-->






    </form>

</div>
<!--end of container-->


@stop