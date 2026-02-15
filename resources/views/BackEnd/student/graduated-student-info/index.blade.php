@php
    use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'ID Card Management')

@push('styles')
    <style type="text/css">

    </style>
@endpush

@section('content')

    <div class="container-fluid">

        <!-- Page Header -->
        <div class="row mb-3">
            <div class="col-md-6">
                <h4>Graduated Students</h4>
            </div>

            <!-- Download Button -->
            <div class="col-md-6 text-right">
                <a href="{{ route('students.graduatedStudentInfo.csv') }}" class="btn btn-success">
                    <i class="fa fa-download"></i> Download CSV
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-striped">

                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Class Roll</th>
                                <th>HSC Roll</th>
                                <th>Session</th>
                                <th>Institution</th>
                                <th>Mobile No</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($students as $key => $student)
                                <tr>
                                    <td>{{ $students->firstItem() + $key }}</td>
                                    <td>
                                        <img src="{{ asset($student->photo) }}" alt="Student Photo" width="40"
                                            class="img-thumbnail">
                                    </td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->class_roll }}</td>
                                    <td>{{ $student->hsc_roll }}</td>
                                    <td>{{ $student->session }}</td>
                                    <td>{{ $student->institution_name }}</td>
                                    <td>{{ $student->mobile }}</td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="7" class="text-center">
                                        No graduated students found
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

                <!-- Pagination -->
                <div class="mt-3 text-right d-flex justify-content-end">
                    {{ $students->links() }}
                </div>

            </div>
        </div>

    </div>

@endsection
