<nav class="navbar navbar-expand-lg navbar-landing fixed-top" id="navbar">
    <div class="container">
        <a class="navbar-brand" href="index.html">
            <img src="{{asset('upload/sites/'.config('settings.site_logo'))}}" class="card-logo card-logo-dark" alt="logo dark" height="50">
            <img src="{{asset('upload/sites/'.config('settings.site_logo'))}}" class="card-logo card-logo-light" alt="logo light" height="50">
        </a>
        <button class="navbar-toggler py-0 fs-20 text-body" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="mdi mdi-menu"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto mt-2 mt-lg-0" id="navbar-example">
                <li class="nav-item">
                    <a class="nav-link fs-15 active" href="{{ route('index') }}">EasyCollegeMate</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link fs-15" href="">Admission Link</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-15" href="">Form Fillup Link</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-15" href="">Contact</a>
                </li>
            </ul>

            <div class="panel">
                @guest('member')
                    <a href="{{ route('member.login') }}" class="btn btn-link fw-medium text-decoration-none text-dark">Login</a>
                    <a href="{{ route('member.register') }}" class="btn btn-primary">Register</a>
                @else
                    <!-- Example single danger button -->
                    <div class="btn-group">
                      <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-user-line" aria-hidden="true"></i> {{ Auth::user()->name }}
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('member.dashboard') }}">Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('member.logout') }}" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('member.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>

                        </li>
                      </ul>
                    </div>
                @endguest
            </div>
        </div>

    </div>
</nav>