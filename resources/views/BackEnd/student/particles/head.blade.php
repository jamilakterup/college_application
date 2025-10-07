<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="description" content="bootstrap admin template">
<meta name="author" content="">

<title>Easy Collegemate</title>

<link rel="apple-touch-icon" href="../assets/images/apple-touch-icon.png">
<link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">
<meta name="csrf-token" content="{{ csrf_token() }}" />

<!-- Stylesheets -->
<link rel="stylesheet" href="{{ asset('backend/css/app.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/selectize/selectize.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
<link rel='stylesheet' href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">

@stack('styles')

<script src="{{asset('assets/js/Plugin/skintools.min.js?v4.0.2')}}"></script>
<script src="{{asset('global/vendor/breakpoints/breakpoints.min.js?v4.0.2')}}"></script>
<script>
  Breakpoints();
</script>