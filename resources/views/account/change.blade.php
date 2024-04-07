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




    <form action="{{ route("account.change_update") }}" method="post" style="display: inline;">
        @csrf

        <div class="container">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">

                        <div class="card card-primary  mx-auto" style="width: 50%">
                            <div class="card-header">
                                <h4 style="margin-bottom: 0">select consignee(Warehouse)</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    @foreach ( $consignees as $consignee )
                                        
                                        <div>
                                            <input type="radio" id="{{ $consignee->id }}" name="consignee" value="{{ $consignee->id }}" {{ $consignee->default_destination=="1" ? "checked" : "" }} />
                                            <label for="{{ $consignee->consignee }}">{{ $consignee->consignee }}</label>
                                          </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                   
                        <div class="col-12 mb-5 mt-5">
                            <div class="text-center">
                                <button type="submit" class="btn btn-lg a-button-input btn150">update</button>
                                <button type="button" class="btn btn-lg a-button-input btn150" onClick="history.back();">Back</button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
@stop
