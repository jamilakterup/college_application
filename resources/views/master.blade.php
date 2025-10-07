<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  @include('layouts.head')
</head>
<body class="animsition ">

  @include('layouts.navbar')

  @include('layouts.sidebar')

  


  <!-- Page -->
  <div class="page">

    <div class="page-header">

      @include('layouts.breadcrumb')

      @include('layouts.page_header_action')
      
    </div>

    <div class="page-content">
      @yield('content')
      
    </div>
  </div>
  <!-- End Page -->


  <!-- Footer -->
</body>
  @include('layouts.footer')

</html>