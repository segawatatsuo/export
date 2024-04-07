@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-8">
                    <h4 class="text-center">Please enter your company information</h4>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ 'Register' }}</div>
                    <div class="card-body">
                        
                        <form method="POST" action="entry">
                            @csrf
                            <div class="form-group row">
                                <label for="consignee"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Consignee' }}</label>

                                <div class="col-md-6">
                                    <input id="consignee" type="text"
                                        class="form-control @error('consignee') is-invalid @enderror" name="consignee"
                                        value="{{ old('consignee') }}" required autocomplete=""
                                        placeholder="">

                                    @error('consignee')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="address_line1"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Address Line1' }}</label>

                                <div class="col-md-6">
                                    <input id="address_line1" type="text"
                                        class="form-control @error('address_line1') is-invalid @enderror"
                                        name="address_line1" value="{{ old('address_line1') }}" required
                                        autocomplete="" placeholder="">

                                    @error('address_line1')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="address_line2"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Address Line2' }}</label>

                                <div class="col-md-6">
                                    <input id="address_line2" type="text"
                                        class="form-control @error('address_line2') is-invalid @enderror"
                                        name="address_line2" value="{{ old('address_line2') }}" required
                                        autocomplete="" placeholder="">

                                    @error('address_line2')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="city" class="col-md-4 col-form-label text-md-right">{{ 'City' }}</label>

                                <div class="col-md-6">
                                    <input id="city" type="text" class="form-control @error('city') is-invalid @enderror"
                                        name="city" value="{{ old('city') }}" required autocomplete=""
                                        placeholder="">

                                    @error('city')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="state"
                                    class="col-md-4 col-form-label text-md-right">{{ 'State' }}</label>

                                <div class="col-md-6">
                                    <input id="state" type="text" class="form-control @error('state') is-invalid @enderror"
                                        name="state" value="{{ old('state') }}" required autocomplete=""
                                        placeholder="">

                                    @error('state')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>



                            <div class="form-group row">
                                <label for="country_codes"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Country codes' }}</label>

                                <div class="col-md-6">
                                    <input id="country_codes" type="text"
                                        class="form-control @error('country_codes') is-invalid @enderror"
                                        name="country_codes" value="{{ old('country_codes') }}" required
                                        autocomplete="" placeholder="(ex.) US, JP, CN・・">

                                    @error('country_codes')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>



                            <div class="form-group row">
                                <label for="zip" class="col-md-4 col-form-label text-md-right">{{ 'Zip Code' }}</label>
                                <div class="col-md-6">
                                    <input id="zip" type="text" class="form-control @error('zip') is-invalid @enderror"
                                        name="zip" value="{{ old('zip') }}" required autocomplete=""
                                        placeholder="">

                                    @error('zip')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>



                            <div class="form-group row">
                                <label for="phone"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Phone Number' }}</label>

                                <div class="col-md-6">
                                    <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" value="{{ old('phone') }}" required autocomplete=""
                                        placeholder="">

                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!--

                            <hr>

                            <div class="form-group row">
                                <label for="person"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Person' }}</label>

                                <div class="col-md-6">
                                    <input id="person" type="text"
                                        class="form-control @error('person') is-invalid @enderror" name="person"
                                        value="{{ old('person') }}" required autocomplete="person" placeholder="Person">

                                    @error('person')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="person_gender"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Person Gender' }}</label>

                                <div class="col-md-6">
                                    <input id="person_gender" type="text"
                                        class="form-control @error('person_gender') is-invalid @enderror"
                                        name="person_gender" value="{{ old('person_gender') }}"
                                        autocomplete="person_gender" placeholder="Person Gender">

                                    @error('person_gender')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="bill_company_address_line1"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Bill Company Address Line1' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_address_line1" type="text"
                                        class="form-control @error('bill_company_address_line1') is-invalid @enderror"
                                        name="bill_company_address_line1" value="{{ old('bill_company_address_line1') }}"
                                        autocomplete="bill_company_address_line1" placeholder="Bill Company Address Line1">

                                    @error('bill_company_address_line1')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="bill_company_address_line2"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Bill Company Address Line2' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_address_line2" type="text"
                                        class="form-control @error('bill_company_address_line2') is-invalid @enderror"
                                        name="bill_company_address_line2" value="{{ old('bill_company_address_line2') }}"
                                        autocomplete="bill_company_address_line2" placeholder="Bill Company Address Line2">

                                    @error('bill_company_address_line2')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="bill_company_city"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Bill Company City' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_city" type="text"
                                        class="form-control @error('bill_company_city') is-invalid @enderror"
                                        name="bill_company_city" value="{{ old('bill_company_city') }}"
                                        autocomplete="bill_company_city" placeholder="Bill Company City">

                                    @error('bill_company_city')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="bill_company_state"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Bill Company State' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_state" type="text"
                                        class="form-control @error('bill_company_state') is-invalid @enderror"
                                        name="bill_company_state" value="{{ old('bill_company_state') }}"
                                        autocomplete="bill_company_state" placeholder="Bill Company State">

                                    @error('bill_company_state')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="bill_company_country"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Bill Company Country' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_country" type="text"
                                        class="form-control @error('bill_company_country') is-invalid @enderror"
                                        name="bill_company_country" value="{{ old('bill_company_country') }}"
                                        autocomplete="bill_company_country" placeholder="Bill Company Country">

                                    @error('bill_company_country')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="bill_company_zip"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Bill Company Zip' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_zip" type="text"
                                        class="form-control @error('bill_company_zip') is-invalid @enderror"
                                        name="bill_company_zip" value="{{ old('bill_company_zip') }}"
                                        autocomplete="bill_company_zip" placeholder="Bill Company Zip">

                                    @error('bill_company_zip')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="bill_company_phone"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Bill Company Phone' }}</label>

                                <div class="col-md-6">
                                    <input id="bill_company_phone" type="text"
                                        class="form-control @error('bill_company_phone') is-invalid @enderror"
                                        name="bill_company_phone" value="{{ old('bill_company_phone') }}"
                                        autocomplete="bill_company_phone" placeholder="Bill Company Phone">

                                    @error('bill_company_phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="president"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Company President' }}</label>

                                <div class="col-md-6">
                                    <input id="president" type="text"
                                        class="form-control @error('president') is-invalid @enderror" name="president"
                                        value="{{ old('president') }}" required autocomplete="president"
                                        placeholder="Company President">

                                    @error('president')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>






                            <div class="form-group row">
                                <label for="president_gender"
                                    class="col-md-4 col-form-label text-md-right">{{ 'President Gender' }}</label>

                                <div class="col-md-6">
                                    <input id="president_gender" type="text"
                                        class="form-control @error('president_gender') is-invalid @enderror"
                                        name="president_gender" value="{{ old('president_gender') }}"
                                        autocomplete="president_gender" placeholder="President Gender">

                                    @error('president_gender')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>



                            <div class="form-group row">
                                <label for="president_gender"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Company Initials' }}</label>

                                <div class="col-md-6">
                                    <input id="president_gender" type="text"
                                        class="form-control @error('initial') is-invalid @enderror"
                                        name="president_gender" value="{{ old('initial') }}"
                                        autocomplete="initial" placeholder="initial">

                                    @error('initial')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <hr>

                            <div class="form-group row">
                                <label for="industry"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Industry' }}</label>

                                <div class="col-md-6">
                                    <input id="industry" type="text"
                                        class="form-control @error('industry') is-invalid @enderror" name="industry"
                                        value="{{ old('industry') }}" autocomplete="industry" placeholder="Industry">

                                    @error('industry')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="business_items"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Business Items' }}</label>

                                <div class="col-md-6">
                                    <input id="business_items" type="text"
                                        class="form-control @error('business_items') is-invalid @enderror"
                                        name="business_items" value="{{ old('business_items') }}"
                                        autocomplete="business_items" placeholder="Business Items">

                                    @error('business_items')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="customer_name"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Customer Name' }}</label>

                                <div class="col-md-6">
                                    <input id="customer_name" type="text"
                                        class="form-control @error('customer_name') is-invalid @enderror"
                                        name="customer_name" value="{{ old('customer_name') }}"
                                        autocomplete="customer_name" placeholder="Customer Name">

                                    @error('customer_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>



                            <div class="form-group row">
                                <label for="fax"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Fax Number' }}</label>

                                <div class="col-md-6">
                                    <input id="fax" type="text" class="form-control @error('fax') is-invalid @enderror"
                                        name="fax" value="{{ old('fax') }}" autocomplete="fax" placeholder="Fax Number">

                                    @error('fax')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="fedex"
                                    class="col-md-4 col-form-label text-md-right">{{ 'FedEX Account' }}</label>

                                <div class="col-md-6">
                                    <input id="fedex" type="text"
                                        class="form-control @error('fedex') is-invalid @enderror" name="fedex"
                                        value="{{ old('fedex') }}" autocomplete="fedex" placeholder="FedEX Account">

                                    @error('fedex')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="sns" class="col-md-4 col-form-label text-md-right">{{ 'SNS' }}</label>

                                <div class="col-md-6">
                                    <input id="sns" type="text" class="form-control @error('sns') is-invalid @enderror"
                                        name="sns" value="{{ old('sns') }}" autocomplete="sns" placeholder="SNS">

                                    @error('sns')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        -->

                            <!--登録ボタン -->
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ 'Register' }}
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="quotation_no" value="{{ $uuid }}">        
                            <input type="hidden" name="user_id" value="{{ $user_id }}">    

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
