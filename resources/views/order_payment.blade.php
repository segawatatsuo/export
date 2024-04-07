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

    @if (session( 'error' ))
    <div class="flash_message bg-danger text-center py-3 my-0"><h3>{{ session('error') }}</h3></div>
    @endif


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8" style="padding-top:10px">
                <br><br><br><br>


<h3>Please upload your proof of payment</h3>
<h5>(e.g.) PDF,Jpeg,Screenshot</h5>

                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-body text-center">
                            <form method="POST" action="payment_upload" enctype="multipart/form-data">
                            @csrf
                            <table>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input type="file" name="img_path" style="display:inline-block">
                                        <button type="submit">upload</button>
                                        <input type="hidden" name="order_number" value="{{ $order_number }}">
                                        <input type="hidden" name="invoice_no" value="{{ $invoice_no }}">
                                    </td>
                                </tr>
                            </table>
                        </form>

                    </div>
                </div>
<br>
                <h5>We will inform you of the manufacturing and shipping date
                    once we confirm the remittance.</h5>



                <br><br><br><br><br><br><br><br><br><br><br><br>
            </div>
        </div>
    </div>

    <!--end of container-->
@stop
