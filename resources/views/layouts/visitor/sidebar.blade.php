<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ Auth::user()->name ?? 'Visitor User' }}</span>
                    <span class="text-secondary text-small">Visitor</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('visitor/dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('visitor.dashboard') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('visitor/kategori') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('visitor.kategori') }}">
                <span class="menu-title">Kategori</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('visitor/buku') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('visitor.buku') }}">
                <span class="menu-title">Buku</span>
                <i class="mdi mdi-book-open-variant menu-icon"></i>
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

        <li class="nav-item {{ Request::is('barang*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('barang.index') }}">
                <span class="menu-title">Tag Harga UMKM</span>
                <i class="mdi mdi-tag-multiple menu-icon"></i>
            </a>
        </li>
    </ul>
</nav>