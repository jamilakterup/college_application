@extends('fees-payment.layouts.masters')

@section('page-title', 'Select Fees Payment Configuration')
@section('section-title', 'Select Fees Payment Configuration')

@section('content')
<div class="container py-5">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="text-center mb-5">
                <h2 class="mb-3"><i class="fas fa-money-check-alt mr-2"></i> Fees Payment</h2>
                <p class="text-muted">Select the applicable fees payment configuration to proceed</p>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Configuration Cards -->
            @if($configurations->count() > 0)
                <div class="row">
                    @foreach($configurations as $config)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm hover-shadow">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-graduation-cap mr-2"></i>
                                        {{ $config->title ?? $config->getCourseName() . ' - ' . $config->level }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <small class="text-muted">Course:</small>
                                            </div>
                                            <div class="col-6">
                                                <strong>{{ $config->getCourseName() }}</strong>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <small class="text-muted">Level:</small>
                                            </div>
                                            <div class="col-6">
                                                <strong>{{ $config->level }}</strong>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <small class="text-muted">Session:</small>
                                            </div>
                                            <div class="col-6">
                                                <strong>{{ $config->academic_session }}</strong>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Valid Till:</small>
                                            </div>
                                            <div class="col-6">
                                                <strong class="text-danger">
                                                    {{ $config->clossing_date->format('d M Y') }}
                                                </strong>
                                            </div>
                                        </div>
                                    </div>

                                    @if($config->isWithinDateRange())
                                        <div class="alert alert-success py-2 mb-3">
                                            <i class="fas fa-check-circle mr-1"></i> Currently Open
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer bg-light">
                                    <form action="{{ route('fees-payment.select-configuration') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="configuration_id" value="{{ $config->id }}">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-arrow-right mr-2"></i> Proceed to Payment
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <h4>No Active Configurations Available</h4>
                        <p class="text-muted">There are currently no open fees payment configurations. Please check back later.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.card-header {
    border-bottom: 2px solid rgba(0,0,0,.125);
}

.card {
    border-radius: 10px;
    overflow: hidden;
}
</style>
@endsection
