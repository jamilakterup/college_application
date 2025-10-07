<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="author" content="">

    <title>Easy Collegemate</title>

    {{-- <link rel="apple-touch-icon" href="../assets/images/apple-touch-icon.png"> --}}
    <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/backend/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backend/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/examples/css/pages/login-v3.min.css?v4.0.2') }}">
    <link rel='stylesheet' href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">

    @stack('style')

    <script src="{{asset('assets/js/Plugin/skintools.min.js?v4.0.2')}}"></script>
    <script src="{{asset('global/vendor/breakpoints/breakpoints.min.js?v4.0.2')}}"></script>
    <script>
      Breakpoints();
    </script>
</head>
<body class="animsition page-login-v3 layout-full">
  <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->


  <!-- Page -->
  <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">>
    <div class="page-content vertical-align-middle animation-slide-top animation-duration-1">
      <div class="panel">
        <div class="panel-body">
            @yield('content')
        </div>
      </div>

      <footer class="page-copyright page-copyright-inverse">
        <p>Crafted with <i class="red-600 wb wb-heart"></i> by <a href="https://rajit.net/" class="text-white">raj IT Solutions Ltd</a></p>
        <p>© {{date('Y')}}. All RIGHT RESERVED.</p>
      </footer>
    </div>
  </div>
  <!-- End Page -->


  <!-- Core  -->
  <script src="{{ asset('js/backend/app.js') }}"></script>
</body>

</html>