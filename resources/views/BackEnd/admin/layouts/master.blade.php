<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  @include('BackEnd.admin.particles.head')
</head>
<body class="animsition ">

  @include('BackEnd.admin.particles.navbar')

  @include('BackEnd.admin.particles.sidebar')

  


  <!-- Page -->
  <div class="page">

    <div class="page-header">

      @include('BackEnd.admin.particles.breadcrumb')

      @include('BackEnd.admin.particles.page_header_action')
      
    </div>

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