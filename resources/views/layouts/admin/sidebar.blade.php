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
                    <span class="text-secondary text-small">Administrator</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('admin.pengguna.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.pengguna') }}">
                <span class="menu-title">Pengguna</span>
                <i class="mdi mdi-account menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('admin.kategori.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kategori.index') }}">
                <span class="menu-title">Kategori Buku</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('admin.buku.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.buku.index') }}">
                <span class="menu-title">Manajemen Buku</span>
                <i class="mdi mdi-book-open-variant menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('pdf.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('pdf.index') }}">
                <span class="menu-title">Generate PDF</span>
                <i class="mdi mdi-file menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::routeIs('barang.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('barang.index') }}">
                <span class="menu-title">Harga Barang (Database)</span>
                <i class="mdi mdi-tag-multiple menu-icon"></i>
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

        <li class="nav-item {{ Request::routeIs('admin.kota.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kota.index') }}">
                <span class="menu-title">Manajemen Kota</span>
                <i class="mdi mdi-city menu-icon"></i>
            </a>
        </li>

    </ul>
</nav>