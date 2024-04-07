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
    <div class="container mb-4 mt-4">
        <div class="row">
            <!--カテゴリアイコン-->
            <div class="col-md-2">
                <img src="{{ asset('/img/logo1.png') }}" class="img-fluid" alt="">
            </div>
            <!--荷受人-->
            <div class="col-md-6">
                <span class="font-weight-bold">Consignee</span>: {{ $user['consignee'] }}<br>
                {{ $user['address_line1'] }}, {{ $user['address_line2'] }}, {{ $user['city'] }}
                {{ $user['state'] }}<br> tel: {{ $user['phone'] }} fax: {{ $user['fax'] }}
            </div>
            <!--発送アイコン-->
            <div class="col-md-4 d-flex align-items-center">

                <a href="{{ route('home') }}">
                    <img src="{{ asset('/img/cclogo.png') }}" class="img-fluid" alt="">
                </a>

            </div>
        </div>
    </div>




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


    <script>
        $(document).ready(function() {
            $("#ItemList").on('input', '#PS01', function() {
                var get_textbox_value = $(this).val();
                if ($.isNumeric(get_textbox_value)) {
                    $("#PS01-PCS").val(get_textbox_value * 24);
                }
            });
        });

        $(document).ready(function() {
            $("#ItemList").on('input', '#PS02', function() {
                var get_textbox_value = $(this).val();
                if ($.isNumeric(get_textbox_value)) {
                    $("#PS02-PCS").val(get_textbox_value * 24);
                }
            });
        });

        $(document).ready(function() {
            $("#ItemList").on('input', '#PS03', function() {
                var get_textbox_value = $(this).val();
                if ($.isNumeric(get_textbox_value)) {
                    $("#PS03-PCS").val(get_textbox_value * 24);
                }
            });
        });

        $(document).ready(function() {
            $("#ItemList").on('input', '#PS04', function() {
                var get_textbox_value = $(this).val();
                if ($.isNumeric(get_textbox_value)) {
                    $("#PS04-PCS").val(get_textbox_value * 24);
                }
            });
        });

        $(document).ready(function() {
            $("#ItemList").on('input', '#PS05', function() {
                var get_textbox_value = $(this).val();
                if ($.isNumeric(get_textbox_value)) {
                    $("#PS05-PCS").val(get_textbox_value * 24);
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#ItemList").on('input', '#DL01', function() {
                var get_textbox_value = $(this).val();
                if ($.isNumeric(get_textbox_value)) {
                    $("#DL01-PCS").val(get_textbox_value * 24);
                }
            });
        });

        $(document).ready(function() {
            $("#ItemList").on('input', '#DL02', function() {
                var get_textbox_value = $(this).val();
                if ($.isNumeric(get_textbox_value)) {
                    $("#DL02-PCS").val(get_textbox_value * 24);
                }
            });
        });

        $(document).ready(function() {
            $("#ItemList").on('input', '#DL03', function() {
                var get_textbox_value = $(this).val();
                if ($.isNumeric(get_textbox_value)) {
                    $("#DL03-PCS").val(get_textbox_value * 24);
                }
            });
        });

        $(document).ready(function() {
            $("#ItemList").on('input', '#DL04', function() {
                var get_textbox_value = $(this).val();
                if ($.isNumeric(get_textbox_value)) {
                    $("#DL04-PCS").val(get_textbox_value * 24);
                }
            });
        });

        $(document).ready(function() {
            $("#ItemList").on('input', '#DL05', function() {
                var get_textbox_value = $(this).val();
                if ($.isNumeric(get_textbox_value)) {
                    $("#DL05-PCS").val(get_textbox_value * 24);
                }
            });
        });
    </script>

</body>

</html>
