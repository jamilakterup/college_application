<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  @include('BackEnd.teacher.particles.head')
</head>
<body class="animsition ">

  @include('BackEnd.teacher.particles.navbar')

  @include('BackEnd.teacher.particles.sidebar')

  


  <!-- Page -->
  <div class="page">

    <div class="page-header">

      @include('BackEnd.teacher.particles.breadcrumb')

      @include('BackEnd.teacher.particles.page_header_action')
      
    </div>

    <div class="page-content">
      @yield('content')
      
    </div>
  </div>
  <!-- End Page -->


  <!-- Footer -->
  @include('BackEnd.teacher.particles.footer')
  @include('common.message')
  @include('common.scripts')
</body>

</html>