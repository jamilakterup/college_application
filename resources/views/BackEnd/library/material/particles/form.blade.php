<div class="form-group row">
    {{ Form::label('accession_no', 'Accession No', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
		@if(isset($maccession))
        <?php $accession_no = $maccession->accession_no; ?>
        {{ Form::text('accession_no', $accession_no, ['class' => 'form-control', 'placeholder' => 'Enter accession no', 'readonly' => true]) }}         
        @else
            {{ Form::text('accession_no', NULL, ['class' => 'form-control', 'placeholder' => 'Enter accession no']) }}          
        @endif	
        {!!invalid_feedback('accession_no')!!}
    </div>
</div>

@if(!isset($material))
<div class="form-group row">
    {{ Form::label('no_of_books', 'No Of Books', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('no_of_books', NULL, ['class' => 'form-control', 'placeholder' => 'Enter no of books']) }}
        {!!invalid_feedback('no_of_books')!!}
    </div>
</div>
@endif

<div class="form-group row">
    {{ Form::label('isbn', 'ISBN/ISSN', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('isbn', NULL, ['class' => 'form-control', 'placeholder' => 'Enter ISBN/ISSN']) }}
        {!!invalid_feedback('isbn')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('call_no', 'Call No', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('call_no', NULL, ['class' => 'form-control', 'placeholder' => 'Enter call no']) }}
        {!!invalid_feedback('call_no')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('physical_form', 'Physical Form', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::select('physical_form', $physical_form_lists, NULL, ['class' => 'form-control']) }}
        {!!invalid_feedback('physical_form')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('title', 'Title', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('title', NULL, ['class' => 'form-control', 'placeholder' => 'Enter book title']) }}
        {!!invalid_feedback('title')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('subtitle', 'Subtitle', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('subtitle', NULL, ['class' => 'form-control', 'placeholder' => 'Enter subtitle']) }}
        {!!invalid_feedback('subtitle')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('author', 'Author', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('author', NULL, ['class' => 'form-control', 'placeholder' => 'Enter author name']) }}
        {!!invalid_feedback('author')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('editor', 'Editor', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('editor', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Editor name']) }}
        {!!invalid_feedback('editor')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('edition', 'Edition', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('edition', NULL, ['class' => 'form-control', 'placeholder' => 'Enter edition']) }}
        {!!invalid_feedback('edition')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('edition_year', 'Edition Year', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('edition_year', NULL, ['class' => 'form-control', 'placeholder' => 'Enter edition year']) }}
        {!!invalid_feedback('edition_year')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('publisher', 'Publisher', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('publisher', NULL, ['class' => 'form-control', 'placeholder' => 'Enter publisher name']) }}
        {!!invalid_feedback('publisher')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('publishing_year', 'Publishing Year', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('publishing_year', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Publishing Year']) }}
        {!!invalid_feedback('publishing_year')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('place_of_publication', 'Publication Place', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('place_of_publication', NULL, ['class' => 'form-control', 'placeholder' => 'Enter publication place']) }}
        {!!invalid_feedback('place_of_publication')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('location', 'Location', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('location', NULL, ['class' => 'form-control', 'placeholder' => 'Enter location']) }}
        {!!invalid_feedback('location')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('price', 'Price', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('price', NULL, ['class' => 'form-control', 'placeholder' => 'Enter price']) }}
        {!!invalid_feedback('price')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('series', 'Series', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('series', NULL, ['class' => 'form-control', 'placeholder' => 'Enter series']) }}
        {!!invalid_feedback('series')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('size', 'Size', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::select('size', $size_lists, NULL, ['class' => 'form-control']) }}
        {!!invalid_feedback('size')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('no_of_pages', 'No Of Pages', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('no_of_pages', NULL, ['class' => 'form-control', 'placeholder' => 'Enter no of pages']) }}
        {!!invalid_feedback('no_of_pages')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('clue_page', 'Clue Page', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('clue_page', NULL, ['class' => 'form-control', 'placeholder' => 'Enter clue page']) }}
        {!!invalid_feedback('clue_page')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('source_details', 'Source Details', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::select('source_details', $source_details_lists, NULL, ['class' => 'form-control']) }}
        {!!invalid_feedback('source_details')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('note', 'Notes', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('note', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Notes']) }}
        {!!invalid_feedback('note')!!}
    </div>
</div>


<div class="form-group row">
    {{ Form::label('subject', 'Subject', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        <div class="border p-2 checkbox-group">
            <label class="checkbox-inline">
                @foreach($subjects as $subject)
                    @if(isset($material))
                        <?php $subject_exist = App\Models\Msubject::whereMaterial_id($material->id)->whereSubject_id($subject->id)->count(); ?>

                        @if($subject_exist > 0)
                            <p>
                                {!! Form::checkbox($subject->id, $subject->id, true) . ' ' . $subject->dept_name !!}
                            </p>
                        @else
                            <p>
                                {!! Form::checkbox($subject->id, $subject->id) . ' ' . $subject->dept_name !!}
                            </p>
                        @endif
                    @else
                        <p>
                            {!! Form::checkbox($subject->id, $subject->id) . ' ' . $subject->dept_name !!}
                        </p>
                    @endif	
                @endforeach
            </label>
        </div>
        {!!invalid_feedback('subject')!!}
    </div>
</div>

@if(isset($maccession->id))
	{{ Form::hidden('id', $maccession->id) }}
@endif

<div class="form-group row">
    <div class="col-md-10 offset-md-2">
      {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
      <a href="{{ route('teacher.index') }}" class="btn btn-warning btn-outline">Back</a>
    </div>
</div>