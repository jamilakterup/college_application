@extends('BackEnd.library.layouts.master')
@section('page-title', 'Material Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions">
            <a href="{{ route('library.material.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Material</a>
            <a href="{{ route('library.material.upload') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Upload Material From CSV</a>
          </div>
          <h3 class="panel-title">Material List</h3>
        </header>
        <div class="panel-body">
          <div class="col-md-12 d-flex justify-content-center">

            {!! Form::open(['route'=> 'library.material.search', 'method'=> 'post', 'class' => 'form-inline']) !!}
              <div class="form-group">
                {{ Form::select('physical_form', $physical_form_lists, $physical_form, ['class' => 'form-control']) }}
              </div>
    
              <div class="form-group">
                {{ Form::text('accession_no', $accession_no, ['class' => 'form-control', 'placeholder' => 'Enter accession no']) }}
              </div>
    
              <div class="form-group">
                {{ Form::text('call_no', $call_no, ['class' => 'form-control', 'placeholder' => 'Enter library call no']) }}
              </div>
    
              <div class="form-group">
                {{ Form::text('title', $book_title, ['class' => 'form-control', 'placeholder'	=> 'Enter book title']) }}
              </div>
    
              <div class="form-group">
                {{ Form::text('author', $author, ['class' => 'form-control', 'placeholder' => 'Enter author name']) }}
              </div>
    
    
              <button type="submit" class="btn btn-info">Search</button>
            {!! Form::close() !!}
    
          </div>

          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              <tr>
                <th>Accession No</th>
                <th>Call No</th>
                <th>ISBN</th>
                <th>Subject</th>
                <th>Physical Form</th>
                <th>Title</th>
                <th>Author</th>
                <th>Editor</th>
                <th style='width: 4%'>Catalog</th>
                <th style='width: 4%'>Details</th>
                <th style='width: 4%'>Edit</th>
                <th style='width: 4%'>Delete</th>
				      </tr>
            </thead>
            
            <tbody>
            
              @foreach($maccessions as $maccession)

                <?php
        
                  $msubjects = App\Models\Msubject::whereMaterial_id($maccession->material->id)->get();
                  $subject_count = App\Models\Msubject::whereMaterial_id($maccession->material->id)->count();
                  $i=1; 
        
                ?>
        
                <tr class="text-center {{ App\Libs\Study::updatedRow('id', $maccession->material->id) }}">
                  <td><a href="{{ URL::route('library.material.show', $maccession->id) }}">{{ $maccession->accession_no }}</a></td>
                  <td>{{ $maccession->material->call_no }}</td>
                  <td>{{ $maccession->material->isbn }}</td>
                  <td>
                    @foreach($msubjects as $msubject)
                      @if($i == $subject_count)
                        {{ ucfirst($msubject->subject->dept_name) }}
                      @else
                        {{ ucfirst($msubject->subject->dept_name) . ',' }}
                      @endif
        
                      <?php $i++; ?>
                    @endforeach
                  </td>
                  <td>{{ ucfirst($maccession->material->physical_form) }}</td>
                  <td>{{ $maccession->material->title }}</td>
                  <td>{{ $maccession->material->author }}</td>
                  <td>{{ $maccession->material->editor }}</td>
                  <td><a class='catalog_print' href='#' id="catalog_{{$maccession->material->id}}"><i class='fa fa-print'></i></a></td>
                  <td><a class='material_details' href='#' id="details_{{$maccession->material->id}}"><i class='fa fa-eye'></i></a></td>
                  <td><a href="{{ URL::route('library.material.edit', $maccession->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>
                  <td>
                    {{ Form::open(['route' => ['library.material.destroy', $maccession->id], 'method' => 'delete', 'class' => 'delete']) }}
                      {{ Form::hidden('id', $maccession->id) }}
                      <button type='submit' class='del'><i class='fa fa-trash'></i></button>
                    {{ Form::close() }}
                  </td>
                </tr>
        
              @endforeach
            </tbody>
          </table>
          {{paginate_info($maccessions)}}
          {{ $maccessions->appends(Request::except('page'))->links() }}
        </div>
      </div>

<!-- Modal -->
<div class="modal fade" id="material_modal" aria-hidden="false" aria-labelledby="material_modal_label"
  role="dialog" tabindex="-1">
  <div class="modal-dialog modal-simple">
    <form class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title" id="material_modal_label"></h4>
      </div>
      <div class="modal-body" id="material_modal_preview">
        
      </div>
    </form>
  </div>
</div>
<!-- End Modal -->
@endsection


@push('scripts')
	<script>
		$(".catalog_print").click(function(){
      event.preventDefault();
      var id=$(this).attr('id');  // this id is the delete div id.ex: Delete_2,Delete_29
      var id_separate=[];  // array variable
        id_separate=id.split("_");  //separate only table_id from main id.after '_' this is the real i
      $.ajax({
          type:'POST',
          url:'{{route('library.material.material_catalog')}}',
          data:{"_token": "{{ csrf_token() }}",id: id_separate[1]},
          success:function(response){
            $("#material_modal_preview").html(response);
            $("#material_modal_label").html('Material Catalog');
            $("#material_modal").modal('show');
          }
      });
   });

    $(".material_details").click(function(){
      event.preventDefault();
      var id=$(this).attr('id');  // this id is the delete div id.ex: Delete_2,Delete_29
      var id_separate=[];  // array variable
        id_separate=id.split("_");  //separate only table_id from main id.after '_' this is the real i
      $.ajax({
          type:'POST',
          url:'{{route('library.material.material_details')}}',
          data:{"_token": "{{ csrf_token() }}",id: id_separate[1]},
          success:function(response){
            $("#material_modal_preview").html(response);
            $("#material_modal_label").html('Material Details');
            $("#material_modal").modal('show');
          }
      });
   });
	</script>
@endpush