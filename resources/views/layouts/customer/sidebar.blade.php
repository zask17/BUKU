<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ Auth::user()->nama_user }}</span>
                    <span class="text-secondary text-small">Vendor: {{ Auth::user()->vendor->nama_vendor }}</span>
                </div>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('vendor.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('vendor.dashboard') }}">
                <span class="menu-title">Dashboard & Pesanan</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('vendor/menu*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('vendor.menu.index') }}">
                <span class="menu-title">Master Menu</span>
                <i class="mdi mdi-food menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('vendor.pesanan') }}">
                <span class="menu-title">Pesanan</span>
                <i class="mdi mdi-cart-outline menu-icon"></i>
            </a>
        </li>
        {{--
        <li class="nav-item">
            <a class="nav-link" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="menu-title">Keluar</span>
                <i class="mdi mdi-logout menu-icon"></i>
            </a>
        </li> --}}
    </ul>
</nav>