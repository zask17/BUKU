<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

    <!-- Logo -->
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <a class="navbar-brand brand-logo" href="{{ url('/') }}">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="logo"/>
        </a>

        <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}">
            <img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo"/>
        </a>
    </div>

    <!-- Menu Navbar -->
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

        <!-- Toggle sidebar -->
        <button class="navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
        </button>

        <!-- Menu kanan -->
        <ul class="navbar-nav navbar-nav-right align-items-center">

            <!-- Menu Home -->
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/') }}">
                    Home
                </a>
            </li>

            {{-- <!-- Menu Buku -->
            <li class="nav-item">
                <a class="nav-link" href="#">
                    Buku
                </a>
            </li> --}}

            <!-- Login -->
            @guest
            <li class="nav-item ms-3">
                <a class="nav-link btn btn-gradient-primary px-4 text-white"
                   href="{{ route('login') }}">
                    <i class="mdi mdi-login me-1"></i> Login
                </a>
            </li>
            @endguest

        </ul>

        <!-- Toggle mobile -->
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center"
            type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>

    </div>
</nav>