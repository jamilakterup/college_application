<ul class="nav nav-tabs nav-tabs-solid" role="tablist">
  <li class="nav-item" role="presentation">
    <a class="nav-link {{active('settings')}}" href="{{ route('admin.settings') }}" aria-controls="exampleIconifiedTabsOne"
      role="tab">
    <i class="fal fa-cog" aria-hidden="true"></i>
    General
  </a>
  </li>
  <li class="nav-item" role="presentation">
    <a class="nav-link {{active('settings/social')}}" href="{{ route('settings.social') }}" aria-controls="exampleIconifiedTabsTwo"
      role="tab">
    <i class="icon wb-plugin" aria-hidden="true"></i>
    Social
  </a>
  </li>

   <li class="nav-item" role="presentation">
    <a class="nav-link {{active('settings/instruction')}}" href="{{ route('settings.instruction') }}" aria-controls="exampleIconifiedTabsTwo"
      role="tab">
    <i class="icon wb-plugin" aria-hidden="true"></i>
    Student Instruction
  </a>
  </li>

  <li class="nav-item" role="presentation">
    <a class="nav-link {{active('settings/configuration/edit')}}" href="{{ route('settings.config.edit') }}" aria-controls="configuration"
      role="tab">
    <i class="icon wb-plugin" aria-hidden="true"></i>
    Configuration
  </a>
  </li>

  <li class="nav-item" role="presentation">
    <a class="nav-link" href="#exampleIconifiedTabsThree" aria-controls="exampleIconifiedTabsThree"
      role="tab">
    <i class="icon wb-settings" aria-hidden="true"></i>
    Settings
  </a>
  </li>
</ul>

{{-- <ul class="nav nav-tabs">
  <li class="nav-item"><a class="nav-link {{active('settings')}}" href="{{ route('admin.settings') }}"> <i class="fal fa-cog" aria-hidden="true"></i> General</a></li>
  <li class="nav-item"><a class="nav-link {{active('settings/social')}}" href="{{ route('settings.social') }}">Social</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Css</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Javascript</a></li>
</ul> --}}