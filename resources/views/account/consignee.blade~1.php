{{-- @extends('../layouts.app') --}}
@extends('../layouts.acount2')
@section('content')

    <!--バリデーションエラー -->
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
    <!-- フラッシュメッセージ -->
    @if (session('flash_message'))
        <div class="flash_message bg-success text-center py-3 my-0 mb-3">
            {{ session('flash_message') }}
        </div>
    @endif




    <form action="" method="post" style="display: inline;">
        @csrf

        <div class="container">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">

                        <div class="card card-primary  mx-auto" style="width: 50%">
                            <div class="card-header">
                                <h4 style="margin-bottom: 0">default address</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <span class="font-weight-bold">Person in charge</span><br>
                                    @if ($pic)
                                        {{ $pic->email ? $pic->email : '' }}<br>
                                        {{ $pic->country ? $pic->country : '' }}<br>
                                        {{ $pic->company_name ? $pic->company_name : '' }}<br>
                                        {{ $pic->name ? $pic->name : '' }}<br>
                                    @endif
                                    <br>

                                    <span class="font-weight-bold">Consignee(Warehouse)</span><br>
                                    @if ($con)
                                        {{ $con->name ? $con->consignee : '' }}<br>
                                        {{ $con->address_line1 ? $con->address_line1 : '' }}<br>
                                        {{ $con->address_line2 ? $con->address_line2 : '' }}<br>
                                        {{ $con->city ? $con->city : '' }}<br>
                                        {{ $con->state ? $con->state : '' }}<br>
                                        {{ $con->country_codes ? $con->country_codes : '' }}<br>
                                        {{ $con->post_code ? $con->post_code : '' }}<br>
                                        {{ $con->name ? $con->name : '' }}<br>
                                    @endif
                                    <div class="text-right"><a href="{{ route('account.edit') }}">edit</a> <a
                                            href="{{ route('account.add') }}">add</a> <a
                                            href="{{ route('account.change') }}">change</a></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-5 mt-5">
                            <div class="text-center">
                                <button type="button" class="btn btn-lg a-button-input"
                                    onClick="history.back();">Back</button>
                            </div>
                        </div>
    </form>
    </div>
    </div>
    </div>
    </div>
@stop
