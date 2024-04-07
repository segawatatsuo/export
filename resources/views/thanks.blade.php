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
            <div class="col-md-8" style="padding-top:10px">

                <br><br><br><br>


                <div class="card">
                    <div class="card-header">Payment method of your choice</div>
                    <div class="card-body text-center">
                        <select name="payment_method" class="form-control">
                            @if($payment_method=="bank")
                            <option value="bank" selected >bank</option>
                            <option value="Payoneer">Payoneer</option>
                            @elseif($payment_method=="Payoneer")
                            <option value="bank">bank</option>
                            <option value="Payoneer"  selected >Payoneer</option>
                            @endif
                        </select>

                    </div>
                </div>
                <br>




                <div class="card">
                    <div class="card-header">Download the purchase order</div>

                    <div class="card-body">
                        Download the purchase order and upload the signed purchase order
                    </div>

                    <div class="card-body text-center">
                        <a href="{{ route('purchase.index', ['quotation_no' => $quotation_no]) }}"><h4>Download the purchase order PDF here</h4></a>
                    </div>

                </div>
                <br>




                <div class="card">
                    <div class="card-header">upload the signed purchase order</div>
                    <div class="card-body text-center">

                        <form method="POST" action="order.order_upload" enctype="multipart/form-data">
                            @csrf
                            <table>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input type="file" name="img_path" style="display:inline-block">
                                        <button type="submit">upload</button>
                                    </td>
                                </tr>
                            </table>
                        </form>

                    </div>
                </div>



                <br><br><br><br><br><br><br><br><br><br><br><br>
            </div>
        </div>
    </div>

    <!--end of container-->
@stop
