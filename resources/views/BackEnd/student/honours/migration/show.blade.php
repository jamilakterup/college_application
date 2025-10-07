@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Honours Student Migration Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
  <header class="panel-heading">
    <h3 class="panel-title">Honours Student Migration</h3>
  </header>
  <div class="panel-body">

  	<div class="header-menu d-flex justify-content-between mb-1">
      <div>
        
			 @include('BackEnd.student.honours.migration.particles.subMenuMigration')
      </div>
      <a href="{{ route('students.migration.exe') }}" class='btn btn-primary'><i class=''></i>Click Here For Migration </a>
		</div>

    <div class="col-md-12 d-flex justify-content-center">
      {!! Form::open(['route'=> 'students.migration.table.search', 'method'=> 'post', 'class' => 'form-inline']) !!}

        <div class="form-group">
          {!! Form::text('adm_roll', $adm_roll, ['class' => 'form-control', 'placeholder' => 'Admission Roll']) !!}
        </div>
        <button type="submit" class="btn btn-info">Search</button>
      {!! Form::close() !!}
    </div>

    <table class="table table-hover defDTable w-full cell-border">
      <thead>
        <th>Admission Roll</th> 
        <th>Admission Session</th>
        <th>Admitted Subject</th>
        <th>Changed Faculty</th>
        <th>Changed Subject</th>        
        <th>Edit</th>       
        <th>Delete</th>
      </thead>

      <tbody>
        @foreach($hons_migration_list as $college)

          <tr class="text-center {{ Study::updatedRow('id', $college->id) }}">
            <td>{{ $college->admission_roll }}</td>
            <td>{{ $college->admission_session }}</td>

            <td>{{ $college->admitted_subject }}</td>
            <td>{{ $college->faculty }}</td>          
            <td>{{ $college->changed_subject }}</td>  
            <td>
            <a href="{{ URL::route('students.migration.list.edit', $college->auto_id) }}" class='edt'><i class='fa fa-pencil'></i></a>          
            
            </td>

            <td>

              {{ Form::open(['route' => 'students.migration.list.single.delete', 'method' => 'post', 'class' => 'delete'] ) }}
                {{ Form::hidden('id', $college->auto_id) }}
                <button type='submit' class='del'><i class='fa fa-trash'></i></button>
              {{ Form::close() }}
            </td> 


            </tr> 

        @endforeach
      </tbody>
    </table>

    {{ $hons_migration_list->appends(Request::except('page'))->links() }}


  </div>
</div>

@endsection

@push('scripts')
@endpush