@extends('layouts.welcome')


@section('content')

    <div class="container mt-4">
        <div class="row">
            <div class="col">
                <!--画像-->
                <div>
                    <!--<img src="{{-- asset('storage/img/export.jpg') --}}" class="img-fluid" alt="">-->
                </div>

                <div class="mt-3 h5">
                    <span style="color: #ff9900;">C.C.MEDICO EXPORT</span> is a B to B site operated by C.C.Medico co.,ltd.
                        The site allows buyers to check <span style="color: #ff9900;">Quotations, Invoices</span>, and <span
                            style="color: #ff9900;">Order sheets</span> online, and accepts payment by credit card or T/T
                        through <span style="color: #ff9900;">Payoneer</span>. We offer a service to deliver air hazardous
                        materials by FedEx in as little as 7 days. <span style="color: #ff9900;">You can check the quotation
                            immediately from now on.</span> This site is not a retail EC site.
                </div>

                <div class="mt-5 h4 text-center">
                    How to use C.C.MEDICO EXPORT
                </div>
                <div class="h6" style="color: #F20000">
                    To use C.C.MEDICO EXPORT, you need to log in.
                    Contact Person Account Entry.
                    Consignee input is required to issue a quotation.
                    Importer input is required to issue invoices.
                    Emails from Payoneer will be sent within 24 hours.

                </div>

                <div class="mt-5 h4 text-center">
                    Choose from three shipping options:<br>
                    FedEx / Air / Ship
                </div>



                <div class="card mt-4">
                    <img class="card-img-top" src="{{ asset('storage/img/CCM_EXPOT_FedEx2.jpg') }}" alt="">
                    <div class="card-body">
                      <h4 class="card-title">FedEx</h4>
                      <p class="card-text">
                        <!--<b style="color: #F20000">Free shipping campaign until the end of August!</b><br>-->
                        MOQ:10cartons(240pcs)/ SPQ/Color:1carton/ SNP:24p or 48p/ STOCK:IN STOCK/ DEADLINE:7days/ PAYMENT
                        TERMS:Advanced Payment/ INCOTERMS:CIF/ PAYMENT METHOD:Payoneer or Bank/ TYPE OF PAYMENT:Credit Card
                        or T/T /
                      </p>
                      <a href="{{ route('view.index') }}" class="btn btn-primary">view</a>
                    </div>
                  </div>



                  <div class="card mt-4">
                    <img class="card-img-top" src="{{ asset('storage/img/CCM_EXPOT_Air.jpg') }}" alt="">
                    <div class="card-body">
                      <h4 class="card-title">Air Order</h4>
                      <p class="card-text">
                        MOQ:100cartons(2400pcs)/ SPQ/Color:20carton/ SNP:24p or 48p/ STOCK:Build-to-order/ DEDADLINE:90days
                        Approx/ PAYMENT TERMS:50% Advance Payment/ INCOTERMS:EXW/ PAYMENT METHOD:Payoneer or Bank/ TYPE OF
                        PAYMENT:T/T /
                      </p>
                      <a href="{{ route('view.index') }}" class="btn btn-primary">view</a>
                    </div>
                  </div>



                  <div class="card mt-4">
                    <img class="card-img-top" src="{{ asset('storage/img/CCM_EXPOT_Ship.jpg') }}" alt="">
                    <div class="card-body">
                      <h4 class="card-title">Ship Order</h4>
                      <p class="card-text">
                        MOQ:100cartons(2400pcs)/ SPQ/Color:20carton/ SNP:24p or 48p/ STOCK:Build-to-order/ DEDADLINE:90days
                        Approx/ PAYMENT TERMS:50% Advance Payment/ INCOTERMS:EXW/ PAYMENT METHOD:Bank/ TYPE OF PAYMENT:T/T /
                      </p>
                      <a href="{{ route('view.index') }}" class="btn btn-primary">view</a>
                    </div>
                  </div>

            </div>
        </div>
        <!--end of row-->

    </div>
    <!--end of container-->

@stop
