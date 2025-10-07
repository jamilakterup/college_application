<div class="site-menubar">
  <div class="site-menubar-body">
    <div>
      <div>
        <ul class="site-menu" data-plugin="menu">
          <li class="site-menu-category">Library</li>

          <li class="site-menu-item {{active('library/material*','library/search/material')}}">
            <a href="{{ route('library.material.index') }}">
                <i class="site-menu-icon fa fa-flask" aria-hidden="true"></i>
                <span class="site-menu-title">Library Material</span>
            </a>
          </li>

          <li class="site-menu-item {{active('library/member*','library/search/member')}}">
            <a href="{{ route('library.member.index') }}">
                <i class="site-menu-icon fa fa-plug" aria-hidden="true"></i>
                <span class="site-menu-title">Library Members</span>
            </a>
          </li>

          <li class="site-menu-item {{active('library/circulation*','library/search/circulation','library/check/circulation','library/checkpost/circulation')}}">
            <a href="{{ route('library.circulation.index') }}">
                <i class="site-menu-icon fa fa-plug" aria-hidden="true"></i>
                <span class="site-menu-title">Circulation</span>
            </a>
          </li>

          <li class="site-menu-item {{active('admin/admission*')}}">
            <a href="{{ route('admin.admission.index') }}">
                <i class="site-menu-icon fa fa-plug" aria-hidden="true"></i>
                <span class="site-menu-title">Reports</span>
            </a>
          </li>
        </ul>

      </div>
    </div>
  </div>
</div>