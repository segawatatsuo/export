{{--@extends('layouts.app')--}}
{{-- @extends('layouts.login') --}}
@extends('layouts.home-top')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <!--You are logged in!-->
                        <h3>Hi! {{ $user_name }}. You're welcome to try. </h3>
                    </div>
                </div>
            </div>


            <div class="col-md-8" style="padding-top:10px">
                <div class="card">
                    <div class="card-header">Product List</div>

                    <div class="card-body">
                        <ul style="padding: 0">
                            <li class="pb-2" style="list-style-type:none;padding:0;" ><a href="{{ route('fedex') }}"><img class="img-thumbnail" src="{{ asset('storage/img/CCM_EXPOT_FedEx2.jpg') }}"></a></li>
                            <li class="pb-2" style="list-style-type:none;padding:0;"><a href="{{ route('air') }}"><img class="img-thumbnail" src="{{ asset('storage/img/CCM_EXPOT_Air.jpg') }}"></a></li>
                            <li class="pb-2" style="list-style-type:none;padding:0;"><a href="{{ route('ship') }}"><img class="img-thumbnail" src="{{ asset('storage/img/CCM_EXPOT_Ship.jpg') }}"></a></li>
                            <li style="list-style-type:none;padding:0;"><a href=" "><img class="img-thumbnail" src="{{ asset('storage/img/CCM_EXPOT_Stock.jpg') }}"></a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
