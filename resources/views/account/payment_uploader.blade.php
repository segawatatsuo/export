@extends('../layouts.login')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8" style="padding-top:10px">



                <div class="card">
                    <div class="card-header">upload the signed purchase order</div>
                    <div class="card-body text-center">

                        <form method="POST" action="payment_up" enctype="multipart/form-data">
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
                            <input type="hidden" name="quotation_no" value="{{-- $quotation_no --}}">
                            <input type="hidden" name="invoice_no" value="{{-- $invoice_no --}}">
                            <input type="hidden" name="order_no" value="{{ $order_no }}">
                            <input type="hidden" name="payment_method" value="{{-- $payment_method --}}">
                            
                            
                        </form>

                    </div>
                </div>




                <br><br><br><br><br><br><br><br><br><br><br><br>
            </div>
        </div>
    </div>

    <!--end of container-->
@stop
