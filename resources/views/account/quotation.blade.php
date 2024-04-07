{{--@extends('../layouts.app')--}}
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
                    <span><a href="{{ route('account.index') }}">Account Services</a> / Quatation</span>
                </div>

            </div>
        </div>
        <hr>
        <div class=" continer mt-4 mb-4 ">

            <div class="row ">
                <div class="col-10 offset-1">
                    <div class="card " style="width: 100%; ">

                        <div class="card-header ">
                            <!--上の行-->
                            <div class="row ">
                                <div class="col-3 ">作成日</div>
                                <div class="col-3 ">見積もり書番号</div>
                                <div class="col-2 ">有効期限</div>
                                <div class="col-2 ">続き</div>
                                <div class="col-2 text-right ">再発行</div>
                            </div>
                            <!--上の行-->
                        </div>
                        

                        <div class="card-body ">
                            <div class="row ">
                                <div class="col-12 ">
                                    @foreach($orders as $order)
                                    <div class="row ">
                                        <div class="col-3 ">{{ $order->updated_at }}</div>
                                        <div class="col-3 ">{{ $order->quotation_no  }}</div>
                                        <div class="col-2 ">{{ $order->expiry }}Days</div>
                                        <div class="col-2 "></div>
                                        <div class="col-2 text-right ">
                                            <!--
                                            <a href="{{ asset('storage/pdf/'.$order->quotation_no.'.pdf') }}" download="{{ $order->quotation_no.'.pdf' }}">ダウンロード</a>
                                            -->
                                            <form action="../generate_quotation_pdf" method="post">
                                                @csrf
                                                <input type="hidden" name="quotation_no" value="{{ $order->quotation_no }}">
                                               <input type="submit" value="download">
                                            </form>
                                        </div>
                                    </div>
                                    <hr>
                                    @endforeach
                                </div>
                                
                            </div>
                        </div>


                    </div>
                </div>
            </div><!--end of row -->
            <br>          

        </div><!--end of continer -->


    </div>

<!--
    <hr>
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
