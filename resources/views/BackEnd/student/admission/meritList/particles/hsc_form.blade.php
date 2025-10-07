<form class='form-horizontal' action="{{route('student.meritlist.hsc.store', ['type'=> 'hsc_probable'])}}" data-form='postForm'>
  <div class="row">
      <div class="col-sm-6">
        <div class="form-group">
          {{ Form::label('name', 'Name', ['class' => 'col-form-label']) }}
            {{ Form::text('name', $name ?? null, ['class' => 'form-control', 'placeholder' => 'Enter Student Name']) }}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('ssc_roll', 'SSC Roll', ['class' => 'col-form-label']) }}
            {{ Form::text('ssc_roll', $ssc_roll ?? null, ['class' => 'form-control', 'placeholder' => 'Enter SSC Roll']) }}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('ssc_group', 'SSC Group', ['class' => 'col-form-label']) }}
            {!! Form::select('ssc_group', filter_empty_array(selective_hsc_groups()) , $ssc_group ?? null, ['class'=>'form-control selectize','placeholder', 'data-plugin'=> 'selectpicker']) !!}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('current_level', 'Current Level', ['class' => 'col-form-label']) }}
          {{ Form::select('current_level',selective_hsc_current_level(),$current_level ?? null, ['class' => 'form-control show-tick']) }}
          <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('ssc_board', 'SSC Board', ['class' => 'col-form-label']) }}
            {!! Form::select('ssc_board', filter_empty_array(selective_boards()) , $ssc_board ?? null, ['class'=>'form-control selectize','placeholder', 'data-plugin'=> 'selectpicker']) !!}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('passing_year', 'Passing Year', ['class' => 'col-form-label']) }}
            {!! Form::select('passing_year', filter_empty_array(selective_multiple_passing_year()) , $passing_year ?? null, ['class'=>'form-control selectize','placeholder', 'data-plugin'=> 'selectpicker']) !!}
            <div class='invalid-feedback'></div>
        </div>
    </div>

    <div class="col-sm-6">

      <div class="form-group">
        {{ Form::label('merit_status', 'Merit Status', ['class' => 'col-form-label']) }}
          {!! Form::text('merit_status', $merit_status ?? null, ['class'=>'form-control selectize','placeholder'=> 'Enter Merit Status']) !!}
          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('rank', 'Merit Position', ['class' => 'col-form-label']) }}
          {!! Form::text('rank', $rank ?? null, ['class'=>'form-control selectize','placeholder'=> 'Enter Merit Position']) !!}
          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('session', 'Session', ['class' => 'col-form-label']) }}
          {!! Form::select('session', selective_multiple_session(), $session ?? null, ['class'=>'form-control selectize', 'data-placeholder'=> '<--Session-->']) !!}
          <div class='invalid-feedback'></div>
      </div>
      
      <div class="form-group">
          {{ Form::label('password', 'Password', ['class' => 'col-form-label']) }}
            {{ Form::text('password', $password ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => 'Enter Password', 'id'=> 'password']) }}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('admission_status', 'Admission Status', ['class' => 'col-form-label']) }}
          {{ Form::select('admission_status',selective_admission_status(),$admission_status ?? null, ['class' => 'form-control show-tick']) }}
          <div class='invalid-feedback'></div>
        </div>
      
    </div>
  </div>

  <div class="form-group">
      {!! Form::submit('Save Data', ['class'=> 'btn btn-primary','data-value'=> 'create', 'data-button'=> 'save']) !!}
  </div>
</form>