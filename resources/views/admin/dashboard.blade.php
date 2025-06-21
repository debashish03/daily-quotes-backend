@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header_title', 'Dashboard')
@section('header_subtitle', 'Welcome to Daily Quotes Admin Panel')
@section('header_actions')
    <button class="btn btn-outline-primary" onclick="loadAnalytics()">
        <i class="bi bi-graph-up me-2"></i>Analytics
    </button>
@endsection

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Dashboard</h1>
            <p class="text-muted mb-0">Welcome to Daily Quotes Admin Panel</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="loadAnalytics()">
                <i class="bi bi-graph-up me-2"></i>Analytics
            </button>
            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-chat-quote fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Quotes</h6>
                            <h3 class="mb-0 fw-bold" id="total-quotes">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Users</h6>
                            <h3 class="mb-0 fw-bold" id="total-users">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-tags fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Categories</h6>
                            <h3 class="mb-0 fw-bold" id="total-categories">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-share fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Shares</h6>
                            <h3 class="mb-0 fw-bold" id="total-shares">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row g-4">
        <!-- Recent Quotes -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-chat-quote me-2"></i>Recent Quotes
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="recent-quotes">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-hourglass-split fs-1"></i>
                            <p class="mt-2">Loading recent quotes...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-people me-2"></i>Recent Users
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="recent-users">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-hourglass-split fs-1"></i>
                            <p class="mt-2">Loading recent users...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Modal -->
    <div class="modal fade" id="analyticsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-graph-up me-2"></i>Analytics Dashboard
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="analyticsModalBody">
                    <!-- Analytics content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Define routes for JavaScript
        window.routes = {
            dashboard: '{{ route('admin.api.dashboard') }}',
            analytics: '{{ route('admin.api.analytics') }}'
        };

        // Helper function to generate user avatar HTML
        function generateUserAvatar(user) {
            const name = user.name || 'User';
            const initials = getInitials(name);
            const color = getAvatarColor(name);

            return `
                <div class="user-avatar" style="background: ${color};">
                    <span>${initials}</span>
                </div>
            `;
        }

        // Helper function to get user initials
        function getInitials(name) {
            if (!name) return 'U';

            const words = name.trim().split(' ');
            if (words.length >= 2) {
                return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
            } else {
                return name.substring(0, 2).toUpperCase();
            }
        }

        // Helper function to get avatar color
        function getAvatarColor(name) {
            if (!name) return '#667eea';

            const colors = [
                '#667eea', '#764ba2', '#f093fb', '#f5576c',
                '#4facfe', '#00f2fe', '#43e97b', '#38f9d7',
                '#fa709a', '#fee140', '#a8edea', '#fed6e3',
                '#ffecd2', '#fcb69f', '#ff9a9e', '#fecfef',
                '#fecfef', '#fad0c4', '#ffd1ff', '#a1c4fd'
            ];

            const hash = name.split('').reduce((a, b) => {
                a = ((a << 5) - a) + b.charCodeAt(0);
                return a & a;
            }, 0);

            return colors[Math.abs(hash) % colors.length];
        }

        // Load dashboard data
        async function loadDashboard() {
            try {
                const response = await fetch(window.routes.dashboard);
                const data = await response.json();

                if (data.success) {
                    updateStatistics(data.data.stats);
                    updateRecentQuotes(data.data.recent_quotes);
                }

                // Load recent users separately
                const usersResponse = await fetch('{{ route('admin.api.users') }}');
                const usersData = await usersResponse.json();

                if (usersData.success) {
                    const recentUsers = usersData.data.users.data.slice(0, 5); // Get first 5 users from paginated data
                    updateRecentUsers(recentUsers);
                }
            } catch (error) {
                console.error('Error loading dashboard:', error);
            }
        }

        // Update statistics
        function updateStatistics(stats) {
            document.getElementById('total-quotes').textContent = stats.total_quotes || 0;
            document.getElementById('total-users').textContent = stats.total_users || 0;
            document.getElementById('total-categories').textContent = stats.total_categories || 0;
            document.getElementById('total-shares').textContent = stats.total_shares || 0;
        }

        // Update recent quotes
        function updateRecentQuotes(quotes) {
            const container = document.getElementById('recent-quotes');

            if (!quotes || quotes.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2">No quotes found</p>
                        <small>Quotes will appear here when added</small>
                    </div>
                `;
                return;
            }

            let html = '';
            quotes.forEach(quote => {
                const date = new Date(quote.created_at).toLocaleDateString();
                html += `
                    <div class="border-bottom p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <p class="mb-1 fw-medium">"${quote.content.substring(0, 60)}${quote.content.length > 60 ? '...' : ''}"</p>
                                <small class="text-muted">${quote.author || 'Unknown'} • ${quote.category.name}</small>
                            </div>
                            <small class="text-muted ms-2">${date}</small>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Update recent users
        function updateRecentUsers(users) {
            const container = document.getElementById('recent-users');

            if (!users || users.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-people fs-1"></i>
                        <p class="mt-2">No users found</p>
                        <small>Users will appear here when they register</small>
                    </div>
                `;
                return;
            }

            let html = '';
            users.forEach(user => {
                const date = new Date(user.created_at).toLocaleDateString();
                const status = user.is_admin ? 'Admin' : 'User';
                const statusClass = user.is_admin ? 'badge bg-danger' : 'badge bg-success';

                html += `
                    <div class="border-bottom p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                ${generateUserAvatar(user)}
                                <div class="ms-3">
                                    <h6 class="mb-0">${user.name}</h6>
                                    <small class="text-muted">${user.email}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="${statusClass}">${status}</span>
                                <br>
                                <small class="text-muted">${date}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Load analytics
        async function loadAnalytics() {
            try {
                const response = await fetch(window.routes.analytics);
                const data = await response.json();

                if (data.success) {
                    showAnalytics(data.data);
                }
            } catch (error) {
                console.error('Error loading analytics:', error);
            }
        }

        // Show analytics in modal
        function showAnalytics(analytics) {
            const modal = new bootstrap.Modal(document.getElementById('analyticsModal'));
            const modalBody = document.getElementById('analyticsModalBody');

            modalBody.innerHTML = `
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="fw-bold text-primary">${analytics.total_quotes}</h4>
                            <small class="text-muted">Total Quotes</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="fw-bold text-success">${analytics.total_users}</h4>
                            <small class="text-muted">Total Users</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="fw-bold text-info">${analytics.total_categories}</h4>
                            <small class="text-muted">Categories</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="fw-bold text-warning">${analytics.total_shares}</h4>
                            <small class="text-muted">Total Shares</small>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="fw-bold">Popular Categories</h6>
                <div class="row g-2">
                    ${analytics.popular_categories.map(cat => `
                                                                    <div class="col-12">
                                                                        <div class="d-flex justify-content-between">
                                                                            <span>${cat.name}</span>
                                                                            <span class="badge bg-primary">${cat.quotes_count} quotes</span>
                                                                        </div>
                                                                    </div>
                                                                `).join('')}
                </div>

                <hr>

                <h6 class="fw-bold">Recent Activity</h6>
                <div class="row g-2">
                    ${analytics.recent_activity.map(activity => `
                                                                    <div class="col-12">
                                                                        <div class="d-flex justify-content-between">
                                                                            <span>${activity.description}</span>
                                                                            <small class="text-muted">${activity.date}</small>
                                                                        </div>
                                                                    </div>
                                                                `).join('')}
                </div>
            `;

            modal.show();
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboard();
        });
    </script>
@endpush
