@extends('layouts.app')

@section('content')
    
  <div class="brand">
    <img class="brand-img" src="{{asset('upload/sites/'.config('settings.site_logo'))}}" alt="..." width="70px">
    <h2 class="brand-text font-size-18">Easy Collegemate Login</h2>
  </div>
    {{ Form::open(['route' => 'login', 'method' => 'post'])}}
    <div class="form-group form-material floating" data-plugin="formMaterial">
      {!! Form::text('username', null, ['class'=> 'form-control']) !!}
      <label class="floating-label">Email/Username</label>
      @error('username')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
      @enderror
    </div>
    <div class="form-group form-material floating" data-plugin="formMaterial">
      {!! Form::password('password', ['class'=> 'form-control']) !!}
      <label class="floating-label">Password</label>

      @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary btn-block btn-lg mt-40">Sign in</button>
  {!! Form::close() !!}
        
@endsection
