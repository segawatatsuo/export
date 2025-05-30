﻿{{-- @extends('../layouts.app') --}}
@extends('../layouts.acount2')

@section('content')



    <div class="container">

        <form method="post" action="{{ route("account.update") }}">
            @csrf
            <div class="card-deck mx-auto" style="width:40rem">

                <div class="card" style="width: 18rem;">
                    <div class="card-header">
                        Consignee
                    </div>


                    <div class="card-body">
                        <div>


                            <label for="consignee_name" class="mt">consignee:</label>
                            <input id="consignee_name" type="text"
                                class="form-control @error('consignee_name') is-invalid @enderror" 
                                name="consignee_name"
                                @if ($con) value="{{ $con->consignee }}" @endif
                                required placeholder="">
                            @error('consignee_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror


                            <label for="name" class="mt">person:</label>

                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name" @if ($con) value="{{ $con->name }}" @endif required placeholder="">
                            @error('importer_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="address_line1" class="mt">address_line1:</label>
                            <input id="address_line1" type="text"
                                class="form-control @error('address_line1') is-invalid @enderror" name="address_line1"
                                @if ($con) value="{{ $con->address_line1 }}" @endif
                                required placeholder="">
                            @error('address_line1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="address_line2" class="mt">address_line2:</label>
                            <input id="address_line2" type="text"
                                class="form-control @error('address_line2') is-invalid @enderror" name="address_line2"
                                @if ($con) value="{{ $con->address_line2 }}" @endif
                                required placeholder="">
                            @error('address_line2')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="city" class="mt">city:</label>
                            <input id="city" type="text" class="form-control @error('city') is-invalid @enderror"
                                name="city" 
                                @if ($con) value="{{ $con->city }}" @endif
                                required placeholder="">
                            @error('city')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="state" class="mt">state:</label>
                            <input id="state" type="text" class="form-control @error('state') is-invalid @enderror"
                                name="state" 
                                @if ($con) value="{{ $con->state }}" @endif
                                required placeholder="">
                            @error('state')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="country" class="mt">country:</label>
                            <input id="country" type="text" class="form-control @error('country') is-invalid @enderror"
                                name="country" 
                                @if ($con) value="{{ $con->country_codes }}" @endif
                                required placeholder="">
                            @error('country')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="zip" class="mt">zip:</label>
                            <input id="zip" type="text" class="form-control @error('zip') is-invalid @enderror"
                                name="zip" 
                                @if ($con) value="{{ $con->post_code }}" @endif
                                required placeholder="">
                            @error('zip')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="phone" class="mt">phone:</label>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror"
                                name="phone" 
                                @if ($con) value="{{ $con->phone }}" @endif
                                required placeholder="">
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror


                        </div>

                    </div>
                </div>



                <div class="card" style="width: 18rem;">
                    <div class="card-header">
                        Person in charge
                    </div>
                    <div class="card-body">
                        <div>


                            <label for="person_name" class="mt">person:</label>
                            <input id="person_name" type="text"
                                class="form-control @error('person_name') is-invalid @enderror" 
                                name="person_name"
                                @if ($pic) value="{{ $pic->name }}" @endif
                                required placeholder="">
                            @error('person_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror


                            <label for="email" class="mt">email:</label>
                            <input id="email" type="text"
                                class="form-control @error('email') is-invalid @enderror" 
                                name="email"
                                @if ($pic) value="{{ $pic->email }}" @endif
                                required placeholder="">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror


                            <label for="person_in_charge_country" class="mt">country:</label>
                            <input id="person_in_charge_country" type="text"
                                class="form-control @error('person_in_charge_country') is-invalid @enderror"
                                name="person_in_charge_country"
                                @if ($pic) value="{{ $pic->country }}" @endif
                                required placeholder="">
                            @error('person_in_charge_country')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="company_name" class="mt">company_name:</label>
                            <input id="company_name" type="text"
                                class="form-control @error('company_name') is-invalid @enderror"
                                name="company_name"
                                @if ($pic) value="{{ $pic->company_name }}" @endif
                                required placeholder="">
                            @error('company_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-5 mt-5">
                    <div class="text-center">
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <button formaction="{{ 'update' }}" type="submit" class="btn btn-lg a-button-input btn150">update</button>
                        <button type="button" class="btn btn-lg a-button-input btn150" onClick="history.back();">Back</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop
