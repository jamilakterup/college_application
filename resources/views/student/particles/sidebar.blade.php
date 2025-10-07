<div class="site-menubar">
  <div class="site-menubar-body">
    <div>
      <div>
        <ul class="site-menu" data-plugin="menu">
            <li class="site-menu-item mt-3 {{active('student/dashboard*')}}">
                <a href="{{ route('student.dashboard') }}">
                    <i class="site-menu-icon fa fa-tachometer-alt-slow" aria-hidden="true"></i>
                    <span class="site-menu-title">Dashboard</span>
                </a>
            </li>

          <li class="site-menu-item {{active('student/document*')}}">
            <a href="{{ route('student.document.index') }}">
                <i class="site-menu-icon fa fa-file-download" aria-hidden="true"></i>
                <span class="site-menu-title">Documents</span>
            </a>
          </li>

          <li class="site-menu-item {{active('student/profile*')}}">
            <a href="{{ route('student.profile') }}">
                <i class="site-menu-icon fa fa-user-circle" aria-hidden="true"></i>
                <span class="site-menu-title">Profile</span>
            </a>
          </li>
          
        </ul>

      </div>
    </div>
  </div>
</div>