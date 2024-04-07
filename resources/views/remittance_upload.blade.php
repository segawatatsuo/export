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
                <div class="item">INVOICE</a></div>
                <div class="item active">ORDER</div>
                <div class="item">FACTORY</div>
                <div class="item">SHIP</div>
                <div class="item">ARRIVAL</div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12" style="padding-top:10px">

                <br><br><br><br>


                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-body">
                        <object data="{{ asset('storage/order') . '/' . $file_name }}" width="900" height="900"></object>
                    </div>
                </div>
                <br>

                <form method="post">
                    @csrf
                    <table class="table">
                        <tr>
                            <td>
                                <div class="text-right pb-2">

                                    <button formaction="" type="submit" class="btn btn-danger btn-lg"
                                        style="width:230px">Delete</button>


                                    <button formaction="{{ 'order_confirm_complete' }}" type="submit"
                                        class="btn btn-warning btn-lg" style="width:230px">Order</button>
                                        <input type="hidden" name="order_number" value="{{ $order_number }}">
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>



                <br><br><br><br><br><br><br><br><br><br><br><br>
            </div>
        </div>
    </div>

    <!--end of container-->
@stop
