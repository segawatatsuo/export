{{--@extends('layouts.app')--}}

@extends('../layouts.acount2')


@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-8">
                <div class="card">

                    <div class="card-header">{{ 'Register' }}</div>
                    <div class="card-body">

                        <div class="pb-2">
                            <h2 class="text-center">Importer (Head Office)</h2>
                        </div>

                        <form method="POST" action="importer_update">
                            @csrf
                            <div class="form-group row">
                                <label for="bill_company_address_line1"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Importer company ※' }}</label>

                                <div class="col-md-6">
                                    <input id="importer_name" type="text"
                                        class="form-control @error('importer_name') is-invalid @enderror"
                                        name="importer_name" value="{{ $main['importer_name'] }}"
                                        required autocomplete="" placeholder="">

                                    @error('importer_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="bill_company_address_line1"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Address Line1 ※' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_address_line1" type="text"
                                        class="form-control @error('bill_company_address_line1') is-invalid @enderror"
                                        name="bill_company_address_line1" value="{{ $main['bill_company_address_line1'] }}"
                                        required autocomplete="" placeholder="">

                                    @error('bill_company_address_line1')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="bill_company_address_line2"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Address Line2 ※' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_address_line2" type="text"
                                        class="form-control @error('bill_company_address_line2') is-invalid @enderror"
                                        name="bill_company_address_line2" value="{{ $main['address_line2'] }}"
                                        required autocomplete="" placeholder="">

                                    @error('bill_company_address_line2')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="bill_company_city"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Company City ※' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_city" type="text"
                                        class="form-control @error('bill_company_city') is-invalid @enderror"
                                        name="bill_company_city" value="{{ $main['city'] }}"
                                        required autocomplete="" placeholder="">

                                    @error('bill_company_city')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="bill_company_state"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Company State ※' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_state" type="text"
                                        class="form-control @error('bill_company_state') is-invalid @enderror"
                                        name="bill_company_state" value="{{ $main['state'] }}"
                                        required autocomplete="" placeholder="">

                                    @error('bill_company_state')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="bill_company_country"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Company Country ※' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_country" type="text"
                                        class="form-control @error('bill_company_country') is-invalid @enderror"
                                        name="bill_company_country" value="{{ $main['bill_company_country'] }}"
                                        required autocomplete="" placeholder="">

                                    @error('bill_company_country')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="bill_company_zip"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Company Zip ※' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_zip" type="text"
                                        class="form-control @error('bill_company_zip') is-invalid @enderror"
                                        name="bill_company_zip" value="{{ $main['zip'] }}"
                                        required autocomplete="" placeholder="">

                                    @error('bill_company_zip')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="bill_company_phone"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Company Phone ※' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_phone" type="text"
                                        class="form-control @error('bill_company_phone') is-invalid @enderror"
                                        name="bill_company_phone" value="{{ $main['phone'] }}"
                                        required autocomplete="" placeholder="">

                                    @error('bill_company_phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="president"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Company President ※' }}</label>

                                <div class="col-md-6">
                                    <input id="president" type="text"
                                        class="form-control @error('president') is-invalid @enderror" name="president"
                                        value="{{ $main['president'] }}" required autocomplete=""
                                        placeholder="">

                                    @error('president')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>



                            <div class="form-group row">
                                <label for="president_gender"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Company Initials ※' }}</label>

                                <div class="col-md-6">
                                    <input id="initial" type="text"
                                        class="form-control @error('initial') is-invalid @enderror" name="initial"
                                        value="{{ $main['initial'] }}" required
                                        placeholder="Please enter in 2 letters">

                                    @error('initial')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-10" style="text-align:right;">※required</div>


                            <hr>

                            <div class="form-group row">
                                <label for="industry"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Your Business Type' }}</label>

                                <div class="col-md-6">
                                    <input id="industry" type="text" class="form-control" name="industry"
                                        value="{{ old('industry') }}" placeholder="(ex.)Wholesaler of beauty">
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="business_items"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Your Items of Business' }}</label>
                                <div class="col-md-6">
                                    <input id="business_items" type="text" class="form-control" name="business_items"
                                        value="{{ old('business_items') }}" placeholder="(ex.)Cosmetic bags">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="customer_name"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Your Customer(s)' }}</label>
                                <div class="col-md-6">
                                    <input id="customer_name" type="text" class="form-control" name="customer_name"
                                        value="{{ old('customer_name') }}" placeholder="(ex.)Cosmetic Shops, wholesalers,">
                                </div>
                            </div>



                            <div class="form-group row">
                                <label for="website"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Your company Website/URL' }}</label>

                                <div class="col-md-6">
                                    <input id="website" type="text" class="form-control" name="website"
                                        value="{{ old('website') }}" placeholder="(ex.)www.ccmedico.com">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="fedex"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Your FedEX Account' }}</label>
                                <div class="col-md-6">
                                    <input id="fedex" type="text" class="form-control" name="fedex"
                                        value="{{ old('fedex') }}" placeholder="(ex.)012345678">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="sns"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Your company SNS' }}</label>
                                <div class="col-md-6">
                                    <input id="sns" type="text" class="form-control @error('sns') is-invalid @enderror"
                                        name="sns" value="{{ old('sns') }}" placeholder="(ex.)www.facebook.com/ccmedico">
                                </div>
                            </div>


                            <!--登録ボタン -->
                            <div class="col-12 mb-5 mt-5">
                                <div class="text-center">

                                    <button formaction="{{ 'update' }}" type="submit" class="btn btn-lg a-button-input ">update</button>
                                    <button type="button" class="btn btn-lg a-button-input btn150" onClick="history.back();">Back</button>
                                </div>
                            </div>




                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
