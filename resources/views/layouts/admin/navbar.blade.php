<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <a class="navbar-brand brand-logo" href="{{ url('admin/dashboard') }}"><img src="{{ asset('assets/images/logo.svg') }}"
                alt="logo" /></a>
        <a class="navbar-brand brand-logo-mini" href="{{ url('admin/dashboard') }}"><img
                src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" /></a>
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
        </button>

        <div class="search-field d-none d-md-block">
            <form class="d-flex align-items-center h-100" action="#">
                <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                        <i class="input-group-text border-0 mdi mdi-magnify"></i>
                    </div>
                    <input type="text" class="form-control bg-transparent border-0" placeholder="Search projects">
                </div>
            </form>
        </div>

        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown"
                    aria-expanded="false" title="Profil Pengguna">
                    <div class="nav-profile-img">
                        <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="image">
                        <span class="availability-status online"></span>
                    </div>
                    <div class="nav-profile-text">
                        <p class="mb-1 text-black">{{ Auth::user()->name ?? 'David Greymaax' }}</p>
                    </div>
                </a>
                <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="#">
                        <i class="mdi mdi-cached me-2 text-success"></i> Activity Log </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="mdi mdi-logout me-2 text-primary"></i> Signout </a>
                </div>
            </li>

            {{-- <li class="nav-item d-none d-lg-block full-screen-link">
                <a class="nav-link" title="Layar Penuh">
                    <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
                </a>
            </li> --}}

            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#"
                    data-bs-toggle="dropdown" aria-expanded="false" title="Pesan">
                    <i class="mdi mdi-email-outline"></i>
                    <span class="count-symbol bg-warning"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                    aria-labelledby="messageDropdown">
                    <h6 class="p-3 mb-0">Messages</h6>
                    <div class="dropdown-divider"></div>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                    data-bs-toggle="dropdown" title="Notifikasi">
                    <i class="mdi mdi-bell-outline"></i>
                    <span class="count-symbol bg-danger"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                    aria-labelledby="notificationDropdown">
                    <h6 class="p-3 mb-0">Notifications</h6>
                    <div class="dropdown-divider"></div>
                </div>
            </li>

            <li class="nav-item nav-logout d-none d-lg-block">
                <a class="nav-link" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    title="Log Out">
                    <i class="mdi mdi-power"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>

            <li class="nav-item nav-settings d-none d-lg-block">
                <a class="nav-link" href="#" title="Pengaturan">
                    <i class="mdi mdi-format-line-spacing"></i>
                </a>
            </li>
        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>