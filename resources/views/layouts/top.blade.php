<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
        integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://kit.fontawesome.com/f57af4dcea.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/progressbar.css') }}" rel="stylesheet">

    <title>C.C. Medico Co.,Ltd.</title>

    <script>
        $(function () {
            $('.js-menu_item_link').on('click', function () {
                $("+.submenu", this).slideToggle();
                return false;
            });
        });
    </script>
</head>

<body>
    <!-- Header -->
    <div class="container-fluid" style="height:60px;background: #131921;color: azure;">
        <div class="row">
            <div class="container d-flex align-items-center">
                <div class="col-md-4" style="padding: 0">
                    <a href="https://www.ccmedico.com/">
                        <img src="{{ asset('storage/img/ccm.jpg') }}" style="height: 60px;">
                    </a>
                </div>
                <div class="col-md-4 text-center" style="color: #fff; font-size:24px;">
                    CCMEDICO EXPORT
                </div>
                <div class="col-md-4">
                    <div class="col-md-12 text-right">
                        @guest
                            <a class="user" href="{{ route('login') }}">{{ __('Login') }}</a> 
                            @if (Route::has('register'))
                                <span style="margin-left: 20px"><a class="user" href="{{ route('register') }}">{{ __('Register') }}</a></span>
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
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Consignee / Shipment -->
    <div class="container mb-2 mt-2">
        <div class="row">
            <div class="col-md-2">
                <img src="{{ asset('storage/img/logo1.png') }}" class="img-fluid" alt="">
            </div>
            <div class="col-md-6">
                <span class="font-weight-bold">Consignee</span>: Consignee<br>
                address_line1, address_line2, city state<br>
                tel: phone fax: fax
            </div>
            <div class="col-md-4 d-flex align-items-center">
                <a href="{{ route('home') }}">
                    @if(session()->get('type') == 'fedex')
                        <img src="{{ asset('storage/img/cclogo.png') }}" class="img-fluid" alt="">
                    @elseif(session()->get('type') == 'air')
                        <img src="{{ asset('storage/img/AIr_banner.png') }}" class="img-fluid" alt="">
                    @elseif(session()->get('type') == 'ship')
                        <img src="{{ asset('storage/img/Ship_banner.png') }}" class="img-fluid" alt="">
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Main -->
    <main class="mb-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <hr>
    <div class="container-fluid">
        <div class="row mt-5 mb-5">
            <div class="col text-center" style="font-size: 11px;">
                Copyright © 2022 C.C. Medico Co.,Ltd. All Rights Reserved.
            </div>
        </div>
    </div>

    <!-- 共通の入力処理 -->
    <script>
        $(document).ready(function () {
            $("#ItemList").on('input', '.txtCal', function () {
                let totalCarton = 0;

                $(".txtCal").each(function () {
                    const val = parseFloat($(this).val());
                    const pcsId = $(this).data('pcs');
                    const unit = $(this).data('unit');

                    if ($.isNumeric(val)) {
                        $(pcsId).val(val * unit);
                        totalCarton += val;
                    } else {
                        $(pcsId).val('');
                    }
                });

                $("#total_sum_value").html(totalCarton);
                $("#total_sum_amount").html(totalCarton * 24);
            });
        });
    </script>

    <!-- ページ固有のスクリプト -->
    @stack('scripts')
</body>

</html>
