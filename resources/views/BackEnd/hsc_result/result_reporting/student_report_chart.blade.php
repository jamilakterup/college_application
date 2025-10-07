{{-- View: BackEnd/hsc_result/result_reporting/index.blade.php --}}
@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Hsc Result Reporting Management')
@section('content')

@push('styles')
<style>
    .chart-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 30px;
    }
    .stats-box {
        padding: 20px;
        border-radius: 10px;
        background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
        color: white;
    }
    .student-card {
        background: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    .student-card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush

<div class="panel">
    <header class="panel-heading">
        <h3 class="panel-title">Hsc Result Reporting</h3>
    </header>
    <div class="panel-body">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="mb-3">{{ $exam->name }} - {{ $exam_year }}</h2>
                <h4 class="text-muted">Session: {{ $session }}</h4>
            </div>
        </div>

        @foreach($data as $groupName => $students)
        <div class="mb-5">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="stats-box">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h4 class="text-white">{{ $groupName }}</h4>
                                <p>Total Students: {{ count($students) }}</p>
                            </div>
                            <div class="col-md-4">
                                <h4 class="text-white">{{ $summary[$groupName]['pass'] }}</h4>
                                <p>Passed Students</p>
                            </div>
                            <div class="col-md-4">
                                <h4 class="text-white">{{ number_format($summary[$groupName]['gpa_total'] / max($summary[$groupName]['pass'], 1), 2) }}</h4>
                                <p>Average GPA</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GPA Distribution Chart -->
                <div class="col-md-6 mb-4">
                    <div class="chart-card">
                        <div id="gpaChart{{ \Illuminate\Support\Str::slug($groupName) }}"></div>
                    </div>
                </div>

                <!-- Grade Distribution Chart -->
                <div class="col-md-6 mb-4">
                    <div class="chart-card">
                        <div id="gradeChart{{ \Illuminate\Support\Str::slug($groupName) }}"></div>
                    </div>
                </div>

            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    @foreach($data as $groupName => $students)
    // GPA Distribution Chart
    new ApexCharts(document.querySelector("#gpaChart{{ \Illuminate\Support\Str::slug($groupName) }}"), {
        series: [
            @foreach(range(5, 1, -0.5) as $gpa)
                {{ collect($students)->filter(fn($s) => $s['grade'] != 'F' && $s['cgpa'] >= $gpa && $s['cgpa'] < $gpa + 0.5)->count() }},
            @endforeach
        ],
        chart: {
            type: 'pie',
            height: 350,
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
            }
        },
        title: {
            text: 'GPA Distribution',
            align: 'center',
            style: {
                fontSize: '18px',
                fontWeight: 'bold'
            }
        },
        labels: [
            @foreach(range(5, 1, -0.5) as $gpa)
                'GPA {{ number_format($gpa, 1) }}',
            @endforeach
        ],
        colors: [
            '#2E93fA', '#66DA26', '#546E7A', '#E91E63', '#FF9800',
            '#1B998B', '#2451B7', '#7D3C98', '#E74C3C', '#27AE60'
        ],
        legend: {
            position: 'bottom',
            horizontalAlign: 'center'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 300
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
        tooltip: {
            y: {
                formatter: function(value) {
                    return value + ' Students';
                }
            }
        }
    }).render();

    // Grade Distribution Chart
    new ApexCharts(document.querySelector("#gradeChart{{ \Illuminate\Support\Str::slug($groupName) }}"), {
        series: [
            @foreach($scales as $grade)
                {{ collect($students)->filter(fn($s) => $s['grade'] == $grade)->count() }},
            @endforeach
        ],
        chart: {
            type: 'pie',
            height: 350,
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
            }
        },
        title: {
            text: 'Grade Distribution',
            align: 'center',
            style: {
                fontSize: '18px',
                fontWeight: 'bold'
            }
        },
        labels: [@foreach($scales as $grade) '{{ $grade }}', @endforeach],
        colors: ['#4CAF50', '#2196F3', '#FFC107', '#FF5722', '#9C27B0', '#F44336'],
        legend: {
            position: 'bottom',
            horizontalAlign: 'center'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 300
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
        tooltip: {
            y: {
                formatter: function(value) {
                    return value + ' Students';
                }
            }
        }
    }).render();
    @endforeach
</script>
@endpush