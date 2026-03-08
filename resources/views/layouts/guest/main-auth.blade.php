<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.guest.head')
    <style>
        /* Memastikan container-scroller mengambil seluruh tinggi layar */
        .container-scroller, .page-body-wrapper {
            min-height: 100vh !important;
        }
        /* Menghilangkan padding default yang sering ada di template dashboard */
        .content-wrapper {
            background: #f2edf3;
            width: 100%;
            padding: 0 !important;
            margin: 0 !important;
        }
    </style>
    @yield('style-page')
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
</body>
</html>