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
                    <span><a href="{{ route('account.index') }}">Account Services</a> / Order History</span>
                </div>
            </div>
        </div>

        <hr>

        <div class=" continer mt-4 mb-4 ">

            <div class="row ">
                <div class="col-10 offset-1">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">orders list</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>受注日</th>
                                        <th>対応状況</th>
                                        <th>支払方法</th>
                                        <th>総合計金額</th>
                                        <th>配送方法</th>
                                        <th>荷送人</th>
                                        <th>国</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td><a href="{{ route( 'account.order_each',$order->id ) }}">{{ $order->order_no }}</a></td>
                                            <td>{{ $order->order_date }}</td>
                                            <td>{{ $order->status }}</td>
                                            <td>{{ $order->payment_method }}</td>
                                            <td>{{ number_format($order->total_amount,2) }}</td>
                                            <td>{{ $order->delivery_method }}</td>
                                            <td>{{ $order->consignee }}</td>
                                            <td>{{ $order->country }}</td>
                                            

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br>
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    {{ $orders->links() }}
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <!-- /.card -->
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
