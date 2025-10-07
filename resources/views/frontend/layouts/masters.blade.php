<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none">

<head>

    @include('frontend.particles.head')
    @stack('styles')
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        @include('frontend.particles.navbar')
        <!-- end navbar -->

        <!-- start hero section -->
        @yield('content')
        <!-- end hero section -->

        <!-- Start footer -->
        @include('frontend.particles.footer')
        <!-- end footer -->

    </div>
    <!-- end layout wrapper -->


    <!-- JAVASCRIPT -->

    @include('frontend.particles.footer_script')
</body>

</html>