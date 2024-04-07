{{--@extends('layouts.app')--}}
@extends('layouts.login')

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

                        <div class="pb-2">
                            <h2 class="text-center">Consignee</h2>
                        </div>

                        <form method="POST" action="entry">
                            @csrf
                            <div class="form-group row">
                                <label for="consignee"
                                    class="col-md-4 col-form-label text-md-right">{{ 'consignee' }}</label>

                                <div class="col-md-6">
                                    <input id="consignee" type="text"
                                        class="form-control @error('consignee') is-invalid @enderror" name="consignee"
                                        value="{{ old('consignee') }}" required autocomplete=""
                                        placeholder="(ex.)C.C.Medico Co.,Ltd.">

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
                                        autocomplete="" placeholder="(ex.)1-12-1 Dogenzaka">

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
                                        autocomplete="" placeholder="(ex.)Shibuya Mark City West 22F">

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
                                        placeholder="(ex.)Shibuya-ku">

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
                                        placeholder="(ex.)Tokyo">

                                    @error('state')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>



                            <div class="form-group row">
                                <label for="country_codes"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Country' }}</label>

                                <div class="col-md-6">
                                    <input id="country_codes" type="text"
                                        class="form-control @error('country_codes') is-invalid @enderror"
                                        name="country_codes" value="{{ old('country_codes') }}" required
                                        autocomplete="" placeholder="(ex.)Japan">

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
                                        placeholder="(ex.)150-0043">

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
                                        placeholder="(ex.)+81-3-5942-5536">

                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="person"
                                    class="col-md-4 col-form-label text-md-right">{{ 'Contact person' }}</label>

                                <div class="col-md-6">
                                    <input id="person" type="text"
                                        class="form-control @error('person') is-invalid @enderror" name="person"
                                        value="{{ old('person') }}" required autocomplete="" placeholder="(ex.)Yoshi.HAMADA">

                                    @error('person')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <!--登録ボタン -->
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ 'Register' }}
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="quotation_no" value="{{ $quotation_no }}">
                            <input type="hidden" name="user_id" value="{{ $user_id }}">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
