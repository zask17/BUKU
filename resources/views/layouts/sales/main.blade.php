<!DOCTYPE html>
<html lang="en">

<head>
  @include('layouts.sales.head')
</head>

<body>
  <div class="container-scroller">
    @include('layouts.sales.navbar')

    <div class="container-fluid page-body-wrapper">
      @include('layouts.sales.sidebar')

      <div class="main-panel">
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title"> @yield('title-page', 'Dashboard') </h3>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('sales.dashboard') }}">Dashboard</a></li>
                @yield('breadcrumb')
              </ol>
            </nav>
          </div>
          @yield('content')
        </div>

        @include('layouts.sales.footer')
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
  <script src="{{ asset('assets/vendors/chart.js/chart.umd.js') }}"></script>
  <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
  <script src="{{ asset('assets/js/misc.js') }}"></script>
  <script src="{{ asset('assets/js/settings.js') }}"></script>
  <script src="{{ asset('assets/js/todolist.js') }}"></script>
</body>

</html>