@extends('BackEnd.teacher.layouts.master')
@section('page-title', 'Teacher Management')

@section('content')
    <div class="panel">

        <header class="panel-heading">
            <h3 class="panel-title">Teachers ID Card</h3>
        </header>

        <div class="panel-body">
            {{ Form::open(['route' => 'teacher.generateidcard', 'method' => 'post', 'class' => 'form-inline form-type-a', 'target'=> '_blank']) }}
            <div class="row d-flex justify-content-center">
                <div class="form-group">
                    <input name="teacher_id" class="form-control" placeholder="Teacher ID" autofocus="YES"/>
                </div>

                <div class="form-group">
                    {!! Form::select('category', selective_multiple_subject(), null, ['class'=> 'form-control']) !!}
                </div>

                <div class="form-group">
                    <select id="print_id" name="print_id" class="form-control">
                        <option value="1">Front Side</option>
                        <option value="2">Back Side</option>
                    </select>
                </div>

                <div class="form-group">
                    {{ Form::submit('Generate', ['class' => 'click btn btn-primary generate']) }}
                </div>
            </div>
            {{ Form::close() }}

        </div>
    </div>

@endsection