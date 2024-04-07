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
                    <span><a href=" {{ route('account.index') }} ">Account Services</a> / Address Book</span>
                </div>

            </div>
        </div>


        <div class="container mb-4 mt-4">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3>Address</h3>
                </div>
            </div>
        </div>

        <div class="card-deck mx-auto" style="width:40rem">

            <div class="card" style="width: 18rem;">
                <div class="card-header">
                    Consignee
                </div>
                <div class="card-body">
                    <div>

                        {{ $user->name }} <br>
                        
                        {{ $user->userinformations->address_line1 }},
                        {{ $user->userinformations->address_line2 }},
                        {{ $user->userinformations->city }},
                        {{ $user->userinformations->state }},
                        {{ $user->country }}<br>
                        {{ $user->userinformations->zip }}<br>
                        {{ $user->userinformations->phone }}<br>
                        {{ $user->email }}<br>

                    </div>
                    <div class="mt-5">
                        <!--<a href="">add</a>-->
                    </div>
                </div>
            </div>

            <div class="card" style="width: 18rem;">
                <div class="card-header">
                    Head Office
                </div>
                <div class="card-body">
                    <div>
                        {{ $user->userinformations->president }} <br>
                        {{ $user->company_name }}<br>
                        {{ $user->userinformations->bill_company_address_line1 }},
                        {{ $user->userinformations->bill_company_address_line2 }},
                        {{ $user->userinformations->bill_company_city }},
                        {{ $user->userinformations->bill_company_state }},
                        {{ $user->userinformations->bill_company_country }}<br>
                        {{ $user->userinformations->bill_company_zip }}<br>
                        {{ $user->userinformations->bill_company_phone }}<br>
                        

                    </div>
                    <div class="mt-5">
                        
                    </div>
                </div>
            </div>
            <div class="col-12 mb-5 mt-5">
                <div class="text-center">
                    <form method="post">
                        @csrf
                        <button formaction="{{ route('account.edit') }}" type="submit" class="btn btn-warning btn-lg">modify</button>
                    </form>
                </div>
            </div>

        </div>


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
