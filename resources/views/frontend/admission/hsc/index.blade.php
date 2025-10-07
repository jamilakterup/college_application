@extends('frontend.layouts.masters')

@section('title', 'HSC Admission')

@section('content')

<section class="section admission-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 pt-4">
                <div class="card">
                    <div class="card-header text-center bg-dark bg-gradient">
                        <h1 class="text-white">HSC Online Admission</h1>
                        <p class="text-danger">Please complete all steps to complete your online admission</p>
                        <a href="{{ route('student.instruction', ['type'=>'admission', 'course' => 'hsc']) }}" class="text-info text-decoration-underline ajax_processing" data-label="গুরুত্বপূর্ণ নির্দেশাবলী">গুরুত্বপূর্ণ নির্দেশাবলী</a>
                    </div><!-- end card header -->
                    <div class="card-body form-steps">
                        <div class="d-flex justify-content-end">
                            <div class="sign-section mb-3">
                                <button class="btn btn-primary"><i class="ri-login-box-line"></i> Student Signin</button>
                                <button class="btn btn-danger"><i class="ri-logout-box-line"></i> Student Signout</button>
                            </div>
                        </div>

                        <div class="step-arrow-nav mb-4">
                            
                            <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link done active" id="steparrow-step1-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-step1-info" type="button" role="tab" aria-controls="steparrow-step1-info" aria-selected="true">Step 1</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="steparrow-step2-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-step2-info" type="button" role="tab" aria-controls="steparrow-step2-info" aria-selected="false">Step 2</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-step3-tab" data-bs-toggle="pill" data-bs-target="#pills-step3" type="button" role="tab" aria-controls="pills-step3" aria-selected="false">Step 3</button>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="tab-content">
                            <div class="row justify-content-center pt-3" id="adm-content">
                                    {!!$initial_step ?? null!!}
                                <!-- end tab pane -->
                            </div>
                        </div>
                        <!-- end tab content -->
                    </div>
                    <!-- end card body -->
                </div>
            </div>
            <!-- end card -->
        </div>
    </div>

</section>
    
@stop


@push('scripts')
@include('frontend.particles.modal')

<script>
    $(document).ready(function() {

        $(document).on('click','.ajax_processing', function() {
            event.preventDefault();
            trigger_ajax_modal($(this));
        });
            
    });
    
</script>
    
@endpush