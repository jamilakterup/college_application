<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  @include('BackEnd.admin.particles.head')
</head>
<body class="animsition ">

  @include('student.particles.navbar')

  @include('student.particles.sidebar')


  <!-- Page -->
  <div class="page">

    <div class="page-content">
      @yield('content')
      
    </div>
  </div>
  <!-- End Page -->


  <!-- Footer -->
  @include('BackEnd.admin.particles.footer')
  @include('common.message')
  @include('common.scripts')
</body>

</html>