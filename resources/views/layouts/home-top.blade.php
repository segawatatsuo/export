<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
        integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://kit.fontawesome.com/f57af4dcea.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous">
    </script>

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/progressbar.css') }}" rel="stylesheet">

    <title>C.C. Medico Co.,Ltd.</title>

    <script>
        $(function() {
            $('.js-menu_item_link').each(function() {
                $(this).on('click', function() {
                    $("+.submenu", this).slideToggle();
                    return false;
                });
            });
        });
    </script>

</head>

<body>


    <div class="container-fluid" style="height:60px;background: #131921;color: azure;">
        <div class="row">
            <div class="container d-flex align-items-center">

                <div class="col-md-4" style="padding: 0">
                    <a href="https://www.ccmedico.com/">
                        <img src="{{ asset('ccm.jpg') }}" style="height: 60px;">
                    </a>
                </div>

                <div class="col-md-4 text-center" style="color: #fff; background-color: transparent;font-size:24px">
                    CCMEDICO EXPORT</div>

                <div class="col-md-4 d-flex">
                    <div class="col-md-6">

                        @guest
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            @if (Route::has('register'))
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            @endif
                        @else
                            <a id="navbarDropdown" class="dropdown-toggle user" href="#" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Hello, {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                
                                <a class="dropdown-item droptext" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>

                            @endguest
                        </div>


                        <a class="account" href="{{ route('account.index') }}" target="_blank">Account Page</a>

                    </div>
                    <div class="col-md-6">
                        <div style="color: #fff";>◎Deliver to</div>
                        {{-- <div style="color: #fff";>{{ session('user')['country_codes'] }}</div> --}}
                        <div style="color: #fff";>{{ session()->get('country_codes') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<br>


    <main class="mb-5">
        @yield('content')
    </main>




    <!--footer-->
    <hr>
    <div class="container-fluid ">
        <div class="row mt-5 mb-5 ">
            <div class="col text-center " style="font-size: 11px; ">
                Copyright © 2022 C.C. Medico Co.,Ltd. All Rights Reserved.
            </div>
        </div>
    </div>
    <!--footer-->


    <script>
        $(document).ready(function() {

            $("#ItemList").on('input', '.txtCal', function() {
                var calculated_total_sum = 0;

                $("#ItemList .txtCal").each(function() {
                    var get_textbox_value = $(this).val();
                    if ($.isNumeric(get_textbox_value)) {
                        calculated_total_sum += parseFloat(get_textbox_value);
                    }
                });
                $("#total_sum_value").html(calculated_total_sum);
                $("#total_sum_amount").html(calculated_total_sum * 24);
            });
        });
        //*
    </script>





</body>

</html>
