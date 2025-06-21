<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Admin Panel') - Daily Quotes</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @stack('head')
</head>

<body class="noto-sans-font">
    @include('partials.sidebar')
    <div class="main-content" id="mainContent">
        @yield('content')
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Security Scripts -->
    <script>
        // Prevent back button access after logout
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Page was loaded from cache (back button)
                window.location.reload();
            }
        });

        // Prevent form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Check if user is still authenticated
        function checkAuth() {
            fetch('{{ route('admin.api.dashboard') }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.status === 401 || response.status === 403) {
                        // User is not authenticated, redirect to login
                        window.location.href = '{{ route('admin.login') }}';
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && !data.success && data.redirect) {
                        // API returned a redirect URL
                        window.location.href = data.redirect;
                    }
                })
                .catch(error => {
                    console.error('Auth check failed:', error);
                });
        }

        // Check auth every 5 minutes
        setInterval(checkAuth, 300000);

        // Check auth when page becomes visible
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                checkAuth();
            }
        });

        // Add global error handler for 401/403 responses
        window.addEventListener('error', function(e) {
            if (e.target.tagName === 'SCRIPT' || e.target.tagName === 'LINK') {
                // Resource loading error, might be due to authentication
                checkAuth();
            }
        });

        // Intercept fetch requests to handle auth errors globally
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            return originalFetch.apply(this, args).then(response => {
                if (response.status === 401 || response.status === 403) {
                    // Redirect to login on auth errors
                    window.location.href = '{{ route('admin.login') }}';
                }
                return response;
            });
        };
    </script>

    <!-- Custom JS -->
    @stack('scripts')
</body>

</html>
