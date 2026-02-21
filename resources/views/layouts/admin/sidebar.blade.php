<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ Auth::user()->name ?? 'Admin User' }}</span>
                    <span class="text-secondary text-small">Administrator</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        
        <li class="nav-item {{ Request::is('admin/pengguna*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.pengguna') }}">
                <span class="menu-title">Pengguna</span>
                <i class="mdi mdi-account menu-icon"></i>
            </a>
        </li>
        
        <li class="nav-item {{ Request::is('admin/kategori*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kategori') }}">
                <span class="menu-title">Kategori</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('admin/buku*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.buku') }}">
                <span class="menu-title">Buku</span>
                <i class="mdi mdi-book-open-variant menu-icon"></i>
            </a>
        </li>
    </ul>
</nav>