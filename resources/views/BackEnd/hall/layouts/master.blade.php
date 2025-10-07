<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  @include('BackEnd.hall.particles.head')
</head>
<body class="animsition ">

  @include('BackEnd.hall.particles.navbar')

  @include('BackEnd.hall.particles.sidebar')

  


  <!-- Page -->
  <div class="page">

    <div class="page-header">

      @include('BackEnd.hall.particles.breadcrumb')

      @include('BackEnd.hall.particles.page_header_action')
      
    </div>

    <div class="page-content">
      @yield('content')
      
    </div>
  </div>
  <!-- End Page -->


  <!-- Footer -->
  @include('BackEnd.hall.particles.footer')
  @include('common.message')
</body>

</html>