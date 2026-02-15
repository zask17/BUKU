<!DOCTYPE html>
<html lang="en">
  <head>
    @include('layouts.guest.head')
  </head>
  
  <body>
    <div class="container-scroller">
      @include('layouts.guest.navbar')
      
      <div class="container-fluid page-body-wrapper">
        @include('layouts.guest.sidebar')
        
        <div class="main-panel">
          <div class="content-wrapper">
            @yield('content')
          </div>
          
          @include('layouts.guest.footer')
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