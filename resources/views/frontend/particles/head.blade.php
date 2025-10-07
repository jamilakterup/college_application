<meta charset="utf-8" />
<title>ECM | @yield('title', config('settings.college_name'))</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="{{config('settings.college_name')}} Easy Collegemate" name="description" />
<meta content="ECM" name="author" />
<!-- App favicon -->
<link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">

<link rel="stylesheet" type="text/css" href="{{ asset('frontend/app.bundle.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}" />