<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  @include('BackEnd.student.particles.head')
</head>
<body class="animsition ">

  @include('BackEnd.student.particles.navbar')

  @include('BackEnd.student.particles.sidebar')

  


  <!-- Page -->
  <div class="page">

    <div class="page-header">

      @include('BackEnd.student.particles.breadcrumb')

      @include('BackEnd.student.particles.page_header_action')
      
    </div>

    <div class="page-content">
      @yield('content')
      
    </div>
  </div>
  <!-- End Page -->


  <!-- Footer -->
  @include('BackEnd.student.particles.footer')
  @include('common.message')
  @include('common.scripts')
</body>

</html>