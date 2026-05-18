<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    {{-- <span class="font-weight-bold mb-2">{{ Auth::user()->name ?? 'Admin User' }}</span> --}}
                    <span class="text-secondary text-small">Guest</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('/') || Request::is('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('antrian/guest') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('antrian.guest') }}">
                <span class="menu-title">Antrian</span>
                <i class="mdi mdi-book-multiple menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('kategori*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/kategori') }}">
                <span class="menu-title">Kategori</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('buku*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/buku') }}">
                <span class="menu-title">Buku</span>
                <i class="mdi mdi-book-open-variant menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('kantin') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kantin.index') }}">
                <span class="menu-title">Kantin</span>
                <i class="mdi mdi-food-fork-drink menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('wilayah/axios') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('wilayah.index_axios') }}">
                <span class="menu-title">Wilayah (Axios)</span>
                <i class="mdi mdi-map-marker-radius menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('wilayah/ajax') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('wilayah.index_ajax') }}">
                <span class="menu-title">Wilayah (Ajax)</span>
                <i class="mdi mdi-map-marker-radius menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::routeIs('pdf.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('pdf.index') }}">
                <span class="menu-title">Generate PDF</span>
                <i class="mdi mdi-file-pdf-box menu-icon"></i>
            </a>
        </li>
    </ul>
</nav>