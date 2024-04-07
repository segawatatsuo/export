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
                        <h5>
                        UPLOAD of the remittance form has been completed.
                        Thank you very much.<br>
                        We will confirm your remittance.<br>
                        Our bank confirmation usually takes from 3 days to 1 week for both "Payoneer" and "Bank Wire".<br>
                        After the remittance is confirmed, the production date and the estimated shipping date will be determined.
                        </h5>
                    </div>
                </div>
                <form method="get" enctype="multipart/form-data">
                    @csrf
                    <div style="text-align: center;padding-top:20px">
                        <button formaction="home" type="submit" class="btn btn-danger btn-lg" style="width:230px">Main page</button>
                    </div>
                </form>
                <br>
                <table class="table">
                    <tr>
                        <td>
                            <div class="text-right pb-2">


                            </div>
                        </td>
                    </tr>
                </table>
                <br><br><br><br><br><br><br><br><br><br><br><br>
            </div>
        </div>
    </div>

    <!--end of container-->
@stop
