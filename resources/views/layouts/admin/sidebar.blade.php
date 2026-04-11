<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ Auth::user()->nama_user ?? 'Admin User' }}</span>
                    <span class="text-secondary text-small">
                        {{ Auth::user()->idrole == 1 ? 'Administrator' : 'Visitor' }}
                    </span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-view-dashboard menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('admin.pengguna.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.pengguna') }}">
                <span class="menu-title">Pengguna</span>
                <i class="mdi mdi-account-group menu-icon"></i>
            </a>
        </li>

        <li class="nav-item-divider"></li>

        <li class="nav-item {{ Request::routeIs('admin.kategori.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kategori.index') }}">
                <span class="menu-title">Kategori Buku</span>
                <i class="mdi mdi-format-list-bulleted-type menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('admin.buku.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.buku.index') }}">
                <span class="menu-title">Manajemen Buku</span>
                <i class="mdi mdi-book-multiple menu-icon"></i>
            </a>
        </li>

        <li class="nav-item-divider"></li>

        <li class="nav-item {{ Request::routeIs('barang.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('barang.index') }}">
                <span class="menu-title">Master Barang</span>
                <i class="mdi mdi-database menu-icon"></i>
            </a>
        </li>

                <li class="nav-item {{ Request::routeIs('admin.barang.baru') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.barang.baru') }}">
                <span class="menu-title">Barang Baru (HTML)</span>
                <i class="mdi mdi-plus-box menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('admin.barang.datatable') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.barang.datatable') }}">
                <span class="menu-title">Barang Baru (DataTable)</span>
                <i class="mdi mdi-table-large menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('admin.pos.index_axios') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.pos.index_axios') }}">
                <span class="menu-title">POS (Axios)</span>
                <i class="mdi mdi-cart-outline menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('admin.pos.index_ajax') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.pos.index_ajax') }}">
                <span class="menu-title">POS (Ajax)</span>
                <i class="mdi mdi-cart-arrow-down menu-icon"></i>
            </a>
        </li>

        <li class="nav-item-divider"></li>

        <li class="nav-item {{ Request::routeIs('admin.kota.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kota.index') }}">
                <span class="menu-title">Manajemen Kota</span>
                <i class="mdi mdi-city-variant menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('wilayah.index_axios') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('wilayah.index_axios') }}">
                <span class="menu-title">Wilayah (Axios)</span>
                <i class="mdi mdi-map-marker-radius menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('wilayah.index_ajax') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('wilayah.index_ajax') }}">
                <span class="menu-title">Wilayah (Ajax)</span>
                <i class="mdi mdi-map-search menu-icon"></i>
            </a>
        </li>

        <li class="nav-item-divider"></li>

        <li class="nav-item {{ Request::routeIs('pdf.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('pdf.index') }}">
                <span class="menu-title">Generate PDF</span>
                <i class="mdi mdi-file-pdf-box menu-icon"></i>
            </a>
        </li>

        {{-- <li class="nav-item {{ Request::routeIs('admin.week4.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.week4.index') }}">
                <span class="menu-title">Week 4 Tasks</span>
                <i class="mdi mdi-calendar-check menu-icon"></i>
            </a>
        </li> --}}
    </ul>
</nav>