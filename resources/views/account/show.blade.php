{{-- @extends('../layouts.app') --}}
@extends('../layouts.login')
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

                        <div class="container mt-4">
                            <div class="row">
                                <div class="col-md-4">
                                </div>
                                <div class="col-md-8 text-right">
                                    <span><a href="{{ route('account.index') }}">Account Services</a> / Address Book</span>
                                </div>
                            </div>
                        </div>
                
                        <hr>



<form action="{{ route('account.update', ['id' => $users->id]) }}" method="post" style="display: inline;">
    @csrf

    <div class="container">

        <div class="container mb-4 mt-4">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3>ADRESS</h3>
                    <input type="submit" value=" update " class="btn btn-success">
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-6">

                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 style="margin-bottom: 0">Person in charge</h4>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">

                            <div class="form-group">
                                <label for="Customer">名前</label>
                                <input type="text" value="{{ $users->name }}" class="form-control" id="name"
                                    name="name">
                            </div>

                            <div class="form-group">
                                <label for="Customer">メール</label>
                                <input type="text" value="{{ $users->email }}" class="form-control" id="email"
                                    name="email">
                            </div>

                            <div class="form-group">
                                <label for="Customer">国(Country)</label>
                                <input type="text" value="{{ $users->country }}" class="form-control" id="country"
                                    name="country">
                            </div>

                            <div class="form-group">
                                <label for="Customer">会社名</label>
                                <input type="text" value="{{ $users->company_name }}" class="form-control"
                                    id="company_name" name="company_name">
                            </div>

                        </div>
                        <!-- /.card-body -->
                    </div>

                    <br>
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 style="margin-bottom: 0">Consignee(Warehouse)</h4>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">

                            <div class="form-group">
                                <label for="Customer">荷受人(Consignee)</label>
                                <input type="text" value="{{ $users->Userinformations['consignee'] }}"
                                    class="form-control" id="consignee" name="consignee">
                            </div>

                            <div class="form-group">
                                <label for="Customer">住所欄1(Address Line1)</label>
                                <input type="text" value="{{ $users->Userinformations['address_line1'] }}"
                                    class="form-control" id="address_line1" name="address_line1">
                            </div>

                            <div class="form-group">
                                <label for="Customer">住所欄2(Address Line2)</label>
                                <input type="text" value="{{ $users->Userinformations['address_line2'] }}"
                                    class="form-control" id="address_line2" name="address_line2">
                            </div>

                            <div class="form-group">
                                <label for="Customer">市町村(City)</label>
                                <input type="text" value="{{ $users->Userinformations['city'] }}"
                                    class="form-control" id="city" name="city">
                            </div>

                            <div class="form-group">
                                <label for="Customer">都道府県(State)</label>
                                <input type="text" value="{{ $users->Userinformations['state'] }}"
                                    class="form-control" id="state" name="state">
                            </div>

                            <div class="form-group">
                                <label for="Customer">国コード</label>
                                <input type="text" value="{{ $users->Userinformations['country_codes'] }}"
                                    class="form-control" id="country" name="country_codes">
                            </div>

                            <div class="form-group">
                                <label for="Customer">郵便番号(ZIP)</label>
                                <input type="text" value="{{ $users->Userinformations['zip'] }}"
                                    class="form-control" id="zip" name="zip">
                            </div>

                            <div class="form-group">
                                <label for="Customer">電話番号(Phone)</label>
                                <input type="text" value="{{ $users->Userinformations['phone'] }}"
                                    class="form-control" id="phone" name="phone">
                            </div>

                            <div class="form-group">
                                <label for="Customer">担当者名(Person)</label>
                                <input type="text" value="{{ $users->Userinformations['person'] }}"
                                    class="form-control" id="person" name="person">
                            </div>

                        </div>
                        <!-- /.card-body -->

                    </div>

                </div>



                <!-- Right column -->
                <div class="col-md-6">

                    <!-- general form elements -->
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 style="margin-bottom: 0">Importer (Head Office)</h4>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">

                            <div class="form-group">
                                <label for="Customer">国(Country)</label>
                                <input type="text" value="{{ $users->Userinformations['bill_company_country'] }}"
                                    class="form-control" id="bill_company_country" name="bill_company_country">
                            </div>

                            <div class="form-group">
                                <label for="Customer">住所欄1(Address Line1)</label>
                                <input type="text"
                                    value="{{ $users->Userinformations['bill_company_address_line1'] }}"
                                    class="form-control" id="bill_company_address_line1"
                                    name="bill_company_address_line1">
                            </div>

                            <div class="form-group">
                                <label for="Customer">住所欄2(Address Line2)</label>
                                <input type="text"
                                    value="{{ $users->Userinformations['bill_company_address_line2'] }}"
                                    class="form-control" id="bill_company_address_line2"
                                    name="bill_company_address_line2">
                            </div>


                            <div class="form-group">
                                <label for="Customer">市町村(City)</label>
                                <input type="text" value="{{ $users->Userinformations['bill_company_city'] }}"
                                    class="form-control" id="bill_company_city" name="bill_company_city">
                            </div>


                            <div class="form-group">
                                <label for="Customer">都道府県(State)</label>
                                <input type="text" value="{{ $users->Userinformations['bill_company_state'] }}"
                                    class="form-control" id="bill_company_state" name="bill_company_state">
                            </div>



                            <div class="form-group">
                                <label for="Customer">郵便番号(ZIP)</label>
                                <input type="text" value="{{ $users->Userinformations['bill_company_zip'] }}"
                                    class="form-control" id="bill_company_zip" name="bill_company_zip">
                            </div>


                            <div class="form-group">
                                <label for="Customer">電話番号(Phone)</label>
                                <input type="text" value="{{ $users->Userinformations['bill_company_phone'] }}"
                                    class="form-control" id="bill_company_phone" name="bill_company_phone">
                            </div>

                            <div class="form-group">
                                <label for="Customer">代表者名(President)</label>
                                <input type="text" value="{{ $users->Userinformations['president'] }}"
                                    class="form-control" id="president" name="president">
                            </div>

                            <div class="form-group">
                                <label for="Customer">会社略称(initial)</label>
                                <input type="text" value="{{ $users->Userinformations['initial'] }}"
                                    class="form-control" id="initial" name="initial">
                            </div>

                            <div class="form-group">
                                <label for="Customer">WEBサイト(Web Site)</label>
                                <input type="text" value="{{ $users->Userinformations['website'] }}"
                                    class="form-control" id="website" name="website">
                            </div>
                            <div class="form-group">
                                <label for="Customer">業種(Industry)</label>
                                <input type="text" value="{{ $users->Userinformations['industry'] }}"
                                    class="form-control" id="industry" name="industry">
                            </div>

                            <div class="form-group">
                                <label for="Customer">営業品目(BusinessItems)</label>
                                <input type="text" value="{{ $users->Userinformations['business_items'] }}"
                                    class="form-control" id="business_items" name="business_items">
                            </div>

                            <div class="form-group">
                                <label for="Customer">取引先名(CustomerName)</label>
                                <input type="text" value="{{ $users->Userinformations['customer_name'] }}"
                                    class="form-control" id="customer_name" name="customer_name">
                            </div>

                        </div>
                        <!-- /.card-body -->
                    </div>

</form>

</div>
<br><br><br>


@stop
