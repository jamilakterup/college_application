<div class="site-menubar">
  <div class="site-menubar-body">
    <div>
      <div>
        <ul class="site-menu" data-plugin="menu">
          <li class="site-menu-category">Administrator</li>
          <li class="site-menu-item has-sub {{has_sub_open(['admin/role*', 'admin/permission*'])}}">
            <a href="javascript:void(0)">
              <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
              <span class="site-menu-title">Role & Permission Mng</span>
              <span class="site-menu-arrow"></span>
            </a>

            <ul class="site-menu-sub" style="">
              <li class="site-menu-item {{active('admin/role*')}}">
                <a href="{{ route('admin.role.index') }}">
                  <span class="site-menu-title">Role</span>
                </a>
              </li>
            </ul>
            <ul class="site-menu-sub" style="">
              <li class="site-menu-item {{active('admin/permission*')}}">
                <a href="{{ route('admin.permission.index') }}">
                  <span class="site-menu-title">Permission</span>
                </a>
              </li>
            </ul>
          </li>
          
          @can('user.index')
            <li class="site-menu-item {{active('admin/user*')}}">
              <a href="{{ route('admin.user.index') }}">
                  <i class="site-menu-icon fa fa-users" aria-hidden="true"></i>
                  <span class="site-menu-title">User Management</span>
              </a>
            </li>
          @endcan

          @can('college.index')
            <li class="site-menu-item {{active('admin/college*')}}">
              <a href="{{ route('admin.college.index') }}">
                  <i class="site-menu-icon fa fa-bars" aria-hidden="true"></i>
                  <span class="site-menu-title">College Management</span>
              </a>
            </li>
          @endcan

          @can('program.index')
          <li class="site-menu-item {{active('admin/program*')}}">
            <a href="{{ route('admin.program.index') }}">
                <i class="site-menu-icon fa fa-bars" aria-hidden="true"></i>
                <span class="site-menu-title">Program Management</span>
            </a>
          </li>
          @endcan

          @can('faculty.index')
            <li class="site-menu-item {{active('admin/faculty*')}}">
              <a href="{{ route('admin.faculty.index') }}">
                  <i class="site-menu-icon fa fa-bars" aria-hidden="true"></i>
                  <span class="site-menu-title">Faculty Management</span>
              </a>
            </li>
          @endcan

          @can('department.index')
            <li class="site-menu-item {{active('admin/dept*')}}">
              <a href="{{ route('admin.dept.index') }}">
                  <i class="site-menu-icon fa fa-bars" aria-hidden="true"></i>
                  <span class="site-menu-title">Department Management</span>
              </a>
            </li>
          @endcan

          <li class="site-menu-item has-sub {{has_sub_open(['admin/payslip*'])}}">
            <a href="javascript:void(0)">
              <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
              <span class="site-menu-title">Account Management</span>
              <span class="site-menu-arrow"></span>
            </a>

            <ul class="site-menu-sub" style="">
              <li class="site-menu-item {{active('admin/payslip*')}}">
                <a href="{{ route('admin.payslip_header.index') }}">
                  <span class="site-menu-title">Payslip Setup</span>
                </a>
              </li>
            </ul>

          </li>

          @can('admission.manage')
            <li class="site-menu-item {{active('admin/admission*')}}">
              <a href="{{ route('admin.admission.index') }}">
                  <i class="site-menu-icon fa fa-tools" aria-hidden="true"></i>
                  <span class="site-menu-title">Admission Management</span>
              </a>
            </li>
          @endcan

          @can('formfillup_config.manage')
          <li class="site-menu-item {{active('admin/formfillup*')}}">
            <a href="{{ route('admin.formfillup.index') }}">
                <i class="site-menu-icon fa fa-tools" aria-hidden="true"></i>
                <span class="site-menu-title">FormFillup Management</span>
            </a>
          </li>
          @endcan

          @can('add_new_student.manage')
          <li class="site-menu-item {{active('admin/newstudent*')}}">
            <a href="{{ route('admin.newstudent.index') }}">
                <i class="site-menu-icon fa fa-plus" aria-hidden="true"></i>
                <span class="site-menu-title">Add New Student</span>
            </a>
          </li>
          @endcan

          @can('course.index')
          <li class="site-menu-item {{active('admin/course*')}}">
            <a href="{{ route('admin.course.index') }}">
                <i class="site-menu-icon fa fa-bars" aria-hidden="true"></i>
                <span class="site-menu-title">Course Management</span>
            </a>
          </li>
          @endcan

          @can('student.invoice.manage')
          <li class="site-menu-item has-sub {{has_sub_open(['admin/invoice*'])}}">
            <a href="javascript:void(0)">
              <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
              <span class="site-menu-title">Student Management</span>
              <span class="site-menu-arrow"></span>
            </a>

            @can('student.invoice.manage')
            <ul class="site-menu-sub" style="">
              <li class="site-menu-item  {{active('admin/invoice*')}}">
                <a href="{{ url('admin/invoice') }}">
                  <span class="site-menu-title">Invoices</span>
                </a>
              </li>
            </ul>
            @endcan

          </li>
          @endcan

          @can('id_roll.manage')
          <li class="site-menu-item {{active('admin/id_roll*')}}">
            <a href="{{ route('admin.id_roll.create') }}">
                <i class="site-menu-icon fa fa-tools" aria-hidden="true"></i>
                <span class="site-menu-title">ID and Roll Management</span>
            </a>
          </li>
          @endcan
          
        </ul>

      </div>
    </div>
  </div>
</div>