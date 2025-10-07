<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
  <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="description" content="bootstrap admin template">
	<meta name="author" content="">

	<title>Easy Collegemate</title>

	<link rel="apple-touch-icon" href="../assets/images/apple-touch-icon.png">
	<link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">

	<!-- Stylesheets -->
	<link rel="stylesheet" href="{{ asset('backend/css/app.css') }}">
	<link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
	<link rel='stylesheet' href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">
	<style type="text/css">
		.h1, h1 {
		    font-size: 10.572rem;
		}
		.vertical-align-bottom, .vertical-align-middle {
		    font-size: 1.5rem;
		}
	</style>

	@stack('styles')

	<script src="{{asset('assets/js/Plugin/skintools.min.js?v4.0.2')}}"></script>
	<script src="{{asset('global/vendor/breakpoints/breakpoints.min.js?v4.0.2')}}"></script>
	<script>
	  Breakpoints();
	</script>
</head>
<body class="animsition page-error page-error-403 layout-full">


  <!-- Page -->
  <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">
    <div class="page-content vertical-align-middle">
      <header>
        <h1 class="animation-slide-top">403</h1>
        <p>User does not have the right permissions !</p>
      </header>
      <p class="error-advise">YOU SEEM TO BE TRYING TO FIND HIS WAY HOME</p>
      <a class="btn btn-primary btn-round" href="{{ url()->previous() }}">GO BACK</a>

      <footer class="page-copyright">
        <p>WEBSITE BY Rajit Solution Ltd.</p>
        <p>© {{date('Y')}}. All RIGHT RESERVED.</p>
        </div>
      </footer>
    </div>
  </div>

<script src="{{ asset('backend/js/app.js') }}"></script>
<script src="{{ asset('backend/js/script.js') }}"></script>
</html>