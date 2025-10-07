<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  @include('BackEnd.hsc_result.particles.head')
</head>
<body class="animsition ">

  @include('BackEnd.hsc_result.particles.navbar')

  @include('BackEnd.hsc_result.particles.sidebar')

  


  <!-- Page -->
  <div class="page">

    <div class="page-header">

      @include('BackEnd.hsc_result.particles.breadcrumb')

      @include('BackEnd.hsc_result.particles.page_header_action')
      
    </div>

    <div class="page-content">
      @yield('content')
      
    </div>
  </div>
  <!-- End Page -->


  <!-- Footer -->
  @include('BackEnd.hsc_result.particles.footer')
  @include('common.message')
</body>

</html>