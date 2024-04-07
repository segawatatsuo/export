{{--@extends('layouts.app')--}}
@extends('layouts.login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header">Thank you for registering.</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif                  
                    CCM has sent you a confirmation email.<br>
                    Click on the URL in the email to complete<br>
                    your registration.<br>
                    Thank you very much.
                </div>
            </div>



        </div>
    </div>
</div>
@endsection
