<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.guest.head')
    <style>
        .container-scroller, .page-body-wrapper {
            min-height: 100vh !important;
        }
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

    <script>
        function setButtonLoading(button, loadingText = 'Memproses...') {
            button.disabled = true;
            button.dataset.originalHtml = button.innerHTML;
            button.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${loadingText}`;
            button.style.opacity = '0.8';
        }

        // Fungsi otomatis pasang loader ke form
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        setButtonLoading(submitBtn);
                    }
                });
            });
        });
    </script>
</body>
</html>