<!DOCTYPE html>
<html lang="en">

<head>
  @include('layouts.admin.head')
</head>

<body>
  <div class="container-scroller">
    @include('layouts.admin.navbar')

    <div class="container-fluid page-body-wrapper">
      @include('layouts.admin.sidebar')

      <div class="main-panel">
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title"> @yield('title-page', 'Dashboard') </h3>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                @yield('breadcrumb')
              </ol>
            </nav>
          </div>
          @yield('content')
        </div>
        @include('layouts.admin.footer')
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
  {{-- <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
  <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
  @stack('scripts') --}}

  <!-- Utility for Button Loader -->
  <script>
    function setButtonLoading(button, loadingText = 'Memproses...') {
      button.disabled = true;
      button.dataset.originalHtml = button.innerHTML;
      button.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${loadingText}`;
      button.style.opacity = '0.8';
    }

    function resetButtonLoading(button) {
      if (button.dataset.originalHtml) {
        button.innerHTML = button.dataset.originalHtml;
        button.disabled = false;
        button.style.opacity = '1';
      }
    }
  </script>

  @yield('js-page')
</body>

</html>