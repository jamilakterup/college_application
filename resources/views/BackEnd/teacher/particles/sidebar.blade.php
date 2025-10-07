<div class="site-menubar">
  <div class="site-menubar-body">
    <div>
      <div>
        <ul class="site-menu" data-plugin="menu">
          <li class="site-menu-category">Teacher</li>

          <li class="site-menu-item {{active('teachers')}}">
            <a href="{{ route('teacher.index') }}">
                <i class="site-menu-icon fa fa-chalkboard-teacher" aria-hidden="true"></i>
                <span class="site-menu-title">Teachers</span>
            </a>
          </li>

          <li class="site-menu-item {{active('teachers/idcard*')}}">
            <a href="{{ route('teacher.idcard') }}">
                <i class="site-menu-icon fa fa-id-badge" aria-hidden="true"></i>
                <span class="site-menu-title">Teacher ID Card</span>
            </a>
          </li>

          <li class="site-menu-item has-sub {{has_sub_open(['teachers/university-list*', 'teachers/designation*','teachers/subject-list*'])}}">
            <a href="javascript:void(0)">
              <i class="site-menu-icon fa fa-cogs" aria-hidden="true"></i>
              <span class="site-menu-title">Settings</span>
              <span class="site-menu-arrow"></span>
            </a>

            <ul class="site-menu-sub" style="">
              <li class="site-menu-item {{active('teachers/designation*')}}">
                <a href="{{ route('teacher.designation.index') }}">
                  <span class="site-menu-title">Designation</span>
                </a>
              </li>

              <li class="site-menu-item {{active('teachers/university-list*')}}">
                <a href="{{ route('teacher.university-list.index') }}">
                  <span class="site-menu-title">University List</span>
                </a>
              </li>

              <li class="site-menu-item {{active('teachers/subject-list*')}}">
                <a href="{{ route('teacher.subject-list.index') }}">
                  <span class="site-menu-title">Subject List</span>
                </a>
              </li>
            </ul>
          </li>
        </ul>

      </div>
    </div>
  </div>
</div>