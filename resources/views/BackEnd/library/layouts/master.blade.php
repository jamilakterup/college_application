<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  @include('BackEnd.library.particles.head')
</head>
<body class="animsition ">

  @include('BackEnd.library.particles.navbar')

  @include('BackEnd.library.particles.sidebar')

  


  <!-- Page -->
  <div class="page">

    <div class="page-header">

      @include('BackEnd.library.particles.breadcrumb')

      @include('BackEnd.library.particles.page_header_action')
      
    </div>

    <div class="page-content">
      @yield('content')
      
    </div>
  </div>
  <!-- End Page -->


  <!-- Footer -->
  @include('BackEnd.library.particles.footer')
  @include('common.message')
</body>

</html>