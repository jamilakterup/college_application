<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega navbar-expand-md"
    role="navigation">

  <div class="navbar-header">
    <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided"
      data-toggle="menubar">
      <span class="sr-only">Toggle navigation</span>
      <span class="hamburger-bar"></span>
    </button>
    <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
      data-toggle="collapse">
      <i class="icon wb-more-horizontal" aria-hidden="true"></i>
    </button>
    <div class="navbar-brand navbar-brand-center site-gridmenu-toggle" data-toggle="gridmenu">
      <img class="navbar-brand-logo" src="{{asset('upload/sites/'.config('settings.site_logo'))}}" title="Remark">
      <span class="navbar-brand-text hidden-xs-down"> Easy Collegemate</span>
    </div>
  </div>

  <div class="navbar-container container-fluid">
    <!-- Navbar Collapse -->
    <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
      <!-- Navbar Toolbar -->
      <ul class="nav navbar-toolbar">
        <li class="nav-item hidden-float" id="toggleMenubar">
          <a class="nav-link" data-toggle="menubar" href="#" role="button">
              <i class="icon hamburger hamburger-arrow-left">
                <span class="sr-only">Toggle menubar</span>
                <span class="hamburger-bar"></span>
              </i>
            </a>
        </li>

        
      </ul>

      <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
        
        <li class="nav-item dropdown">
          <a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false"
            data-animation="scale-up" role="button">
            <span class="avatar avatar-online">
              <img src="../../global/portraits/5.jpg" alt="...">
              <i></i>
            </span>
          </a>
          <div class="dropdown-menu" role="menu">
            <a class="dropdown-item" href="javascript:void(0)" role="menuitem"><i class="icon wb-user" aria-hidden="true"></i> Profile</a>
            <a class="dropdown-item" href="javascript:void(0)" role="menuitem"><i class="icon wb-settings" aria-hidden="true"></i> Settings</a>
            <div class="dropdown-divider" role="presentation"></div>
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="
              event.preventDefault();
              document.getElementById('logout-form').submit();" 
            role="menuitem"><i class="icon wb-power" aria-hidden="true"></i> Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
          </div>
        </li>
      </ul>

      <ul class="nav navbar-toolbar navbar-right">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"
            role="button"><i class="fad fa-bars" ></i> Menu</a>
          <div class="dropdown-menu" role="menu">
            <a href="{{ route('admin.college.index') }}" class="dropdown-item {{active('admin*')}}"><i class="fad fa-wrench"></i> Administration</a>
            <a href="{{ route('student') }}" class="dropdown-item {{active('students*')}}"><i class="fad fa-graduation-cap"> </i> Students</a>
            <a href="{{ route('hsc_result.index') }}" class="dropdown-item {{active('hsc_result*')}}"><i class="fad fa-certificate"> </i> HSC Result</a>
            <a href="{{ route('teacher.index') }}" class="dropdown-item {{active('teachers*')}}"><i class="fad fa-graduation-cap"> </i> Teachers</a>
            {{-- <a href="#" class="dropdown-item {{active('employees*')}}"><i class="fad fa-users"> </i> Employees</a> --}}
            <a href="{{ route('library.material.index') }}" class="dropdown-item {{active('library*')}}"><i class="fad fa-university"> </i> Library</a>
            <a href="{{ route('hall.index') }}" class="dropdown-item {{active('hall*')}}"><i class="fad fa-building"> </i> Hall</a>
            <a href="{{ route('admin.settings') }}" class="dropdown-item {{active('settings*')}}"><i class="fad fa-cogs"> </i> Site Settings</a>
          </div>
        </li>
      </ul>
      <!-- End Navbar Toolbar Right -->
    </div>
    <!-- End Navbar Collapse -->

  </div>
</nav>