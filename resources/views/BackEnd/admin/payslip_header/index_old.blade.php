@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'PaySlip Header Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.admission.particles.subMenu')
</div>

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('admin.payslip_header.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add PaySlip Header</a></div>
          <h3 class="panel-title">PaySlip Header Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover w-full cell-border text-center" id="headerTable">
            <thead>
              <tr>
                <th>PaySlip Header Title</th>
								<th>Group</th>
								<th>Department</th>
								<th>Subject</th>
								<th>Type</th>
								<th>Level</th>
								<th>Session</th>
								<th>Exam Year</th>
								<th>Formfillup Type</th>
								<th>Code</th>
								<th>Edit</th>
								<th>Action</th>
              </tr>
            </thead>
            
            <tbody id="tdata">
	            @foreach($payslip_headers as $payslip_header)
					<tr class="text-center {{ Study::updatedRow('id', $payslip_header->id) }}">
						<td>{{ $payslip_header->title }}</td>
						<td>{{ $payslip_header->pro_group }}</td>
						<td>{{ $payslip_header->group_dept }}</td>
						<td>{{ $payslip_header->subject }}</td>
						<td>{{ $payslip_header->type }}</td>
						<td>{{ $payslip_header->level }}</td>
						<td>{{ $payslip_header->session }}</td>
						<td>{{ $payslip_header->exam_year }}</td>
						<td>{{ $payslip_header->formfillup_type }}</td>
						<td>{{ $payslip_header->code }}</td>
						<td><a href="{{ URL::route('admin.payslip_header.edit', $payslip_header->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
						<td>
							@if ($payslip_header->type == '2nd_year_promotion')
								<a href="{{ route('invoice.generate.promotion.hsc', ['examyear'=> $payslip_header->exam_year, 'cur_level'=> $payslip_header->level, 'session'=> $payslip_header->session, 'payslipheader_id'=> $payslip_header->id]) }}" class="btn btn-info">HSC Promotion Invoice Generate </a>
							@endif

								{{ Form::hidden('id', $payslip_header->id) }}
								<!-- <button type='submit' class='del'><i class='fa fa-trash'></i></button> -->
							{{ Form::close() }}

							@if ($payslip_header->type == 'formfillup' && $payslip_header->pro_group == 'degree' && $payslip_header->formfillup_type == 'regular')
								<a href="{{ route('invoice.generate.formfillup.degree', ['payslipheader_id'=> $payslip_header->id]) }}" class="btn btn-info">Degree Form Fillup Invoice Generate </a>
							@endif

							@if ($payslip_header->type == 'formfillup' && $payslip_header->pro_group == 'masters')
								<a href="{{ route('invoice.generate.formfillup.masters', ['payslipheader_id'=> $payslip_header->id]) }}" class="btn btn-info">Masters Form Fillup Invoice Generate </a>
							@endif

							@if ($payslip_header->type == 'formfillup' && $payslip_header->pro_group == 'honours' && $payslip_header->formfillup_type == 'regular')
								<a href="{{ route('invoice.generate.formfillup.honours', ['payslipheader_id'=> $payslip_header->id]) }}" class="btn btn-info">Honours Form Fillup Invoice Generate </a>
							@endif

							@if ($payslip_header->type == 'admission' && $payslip_header->pro_group == 'hsc')
								<a href="{{ route('invoice.generate.admission.hsc', ['payslipheader_id'=> $payslip_header->id]) }}" class="btn btn-info">HSC Admissoin Invoice Generate </a>
							@endif

							@if ($payslip_header->type == 'admission' && $payslip_header->pro_group == 'honours')
								<a href="{{ route('invoice.generate.admission.honours', ['payslipheader_id'=> $payslip_header->id]) }}" class="btn btn-info">Honours Admissoin Invoice Generate </a>
							@endif

							@if ($payslip_header->type == 'admission' && $payslip_header->pro_group == 'masters' && $payslip_header->level == 'Masters 2nd Year')
								<a href="{{ route('invoice.generate.admission.masters', ['payslipheader_id'=> $payslip_header->id]) }}" class="btn btn-info">Masters Admissoin Invoice Generate </a>
							@endif

							@if ($payslip_header->type == 'admission' && $payslip_header->pro_group == 'masters' && $payslip_header->level == 'Masters 1st Year')
								<a href="{{ route('invoice.generate.admission.masters1st', ['payslipheader_id'=> $payslip_header->id]) }}" class="btn btn-info">Masters 1st Admissoin Invoice Generate </a>
							@endif

							@if ($payslip_header->type == 'admission' && $payslip_header->pro_group == 'degree' && $payslip_header->level == 'Degree 1st Year')
								<a href="{{ route('invoice.generate.admission.degree', ['payslipheader_id'=> $payslip_header->id]) }}" class="btn btn-info">Degree Admission Invoice Generate </a>
							@endif

								{{ Form::open(['route' => ['admin.payslip_header.destroy', $payslip_header->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $payslip_header->id) }}
								<button type='submit' class='del btn btn-danger'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>
				@endforeach
            </tbody>
          </table>
          {{ $payslip_headers->links() }}
        </div>
      </div>

@endsection

@push('scripts')

{{ajax_crud_setup()}}


<script>
$(document).ready(function() {
  var table = $('#headerTable').DataTable({
      ajax:{
        url: "{{ route('admin.payslip_header.datasource') }}",
        data: function (d) {
          d._token = "{{ csrf_token() }}",
          d.name = $('#permission_name').val()
          d.group_name = $('#group_name').val(),
          d.parent_group_name = $('#parent_group_name').val()
        },
        dataType: "json",
        type: "POST",
      },
      columns: [
        { data: "id"},
        { data: "name"},
        { data: "group_name"},
        { data: "parent_group_name"},
        { data: "guard_name"},
        { data: "actions", orderable: false, searchable: false }
      ],
      autoWidth : true,
      processing: true,
      serverSide: true,
      searching : false,
      scrollY: '50vh',
      // dom: '<"top"i>rt<"bottom"flp><"clear">',
      sDom: 'Lfrtlip',
      responsive: true,
      aaSorting: [],
      lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
      iDisplayLength: 50,
      // dom: '<"top"i>rt<"bottom"flp><"clear">',
      // bAutoWidth: false,
      columnDefs: [
        // { orderable: false, targets: 4 }
        // { "width": "10%", "targets": [1,4] }
      ],



    });

  table.columns.adjust().draw();

  $(document).on('change','#group_name', function(){
    table.draw();
  });

  $(document).on('change','#parent_group_name', function(){
    table.draw();
  });

  $(document).on('keyup','#permission_name', function(){
    table.draw();
  });

  $("select[name='group_name']").select2({
      placeholder: '--Select Group Name--',
      allowClear: true
  });

  $("select[name='parent_group_name']").select2({
      placeholder: '--Select Parent Group Name--',
      allowClear: true
  });

});
  
</script>
@endpush