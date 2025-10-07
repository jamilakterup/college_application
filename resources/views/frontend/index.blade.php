@extends('frontend.layouts.masters')

@section('content')
  
<section class="section hero-section" id="hero">
    <div class="bg-overlay bg-overlay-pattern"></div>
    <div class="container">
        <div class="row justify-content-center p-10">
            <div class="col-sm-6 col-xl-3">
                <!-- Simple card -->
                <div class="card">
                    <img class="card-img-top img-fluid" src="{{ asset('img/admission.png') }}" alt="Card image cap">
                    <div class="card-body text-center">
                        <h4 class="card-title mb-2">Online Admission/Formfillup</h4>
                    </div>
                </div><!-- end card -->
            </div>

            <div class="col-sm-6 col-xl-3">
                <!-- Simple card -->
                <div class="card">
                    <img class="card-img-top img-fluid" src="{{ asset('img/result.jpg') }}" alt="Card image cap">
                    <div class="card-body text-center">
                        <h4 class="card-title mb-2">HSC Result</h4>
                    </div>
                </div><!-- end card -->
            </div>
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</section>
    
@stop