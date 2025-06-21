@extends('layouts.admin')

@section('title', 'Analytics')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 fw-bold">Analytics Dashboard</h1>
            <p class="text-muted mb-0">Comprehensive insights and performance metrics</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshAnalytics()">
                <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" onclick="exportAnalytics()">
                <i class="bi bi-download me-2"></i>Export Report
            </button>
            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Analytics Dashboard</h1>
            <p class="text-muted mb-0">Comprehensive insights and performance metrics</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshAnalytics()">
                <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" onclick="exportAnalytics()">
                <i class="bi bi-download me-2"></i>Export Report
            </button>
            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="date-range" class="form-label">Date Range</label>
                    <select class="form-select" id="date-range" onchange="updateDateRange()">
                        <option value="7">Last 7 Days</option>
                        <option value="30" selected>Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                        <option value="365">Last Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3" id="custom-date-start" style="display: none;">
                    <label for="start-date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start-date">
                </div>
                <div class="col-md-3" id="custom-date-end" style="display: none;">
                    <label for="end-date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end-date">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary" onclick="applyDateFilter()">
                        <i class="bi bi-funnel me-2"></i>Apply Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
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
                            <small class="opacity-75" id="quotes-growth">+0% from last period</small>
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
                            <h6 class="card-title mb-1">Active Users</h6>
                            <h3 class="mb-0 fw-bold" id="active-users">0</h3>
                            <small class="opacity-75" id="users-growth">+0% from last period</small>
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
                            <i class="bi bi-eye fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Views</h6>
                            <h3 class="mb-0 fw-bold" id="total-views">0</h3>
                            <small class="opacity-75" id="views-growth">+0% from last period</small>
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
                            <h6 class="card-title mb-1">Total Shares</h6>
                            <h3 class="mb-0 fw-bold" id="total-shares">0</h3>
                            <small class="opacity-75" id="shares-growth">+0% from last period</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- User Growth Chart -->
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-graph-up me-2"></i>User Growth Trend
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="userGrowthChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Quote Categories Distribution -->
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-pie-chart me-2"></i>Quote Categories
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics -->
    <div class="row g-4 mb-4">
        <!-- Popular Quotes -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-star me-2"></i>Top Performing Quotes
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="top-quotes">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-hourglass-split fs-1"></i>
                            <p class="mt-2">Loading top quotes...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Engagement -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-activity me-2"></i>User Engagement
                    </h5>
                </div>
                <div class="card-body">
                    <div id="engagement-stats">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-hourglass-split fs-1"></i>
                            <p class="mt-2">Loading engagement data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Platform Analytics -->
    <div class="row g-4 mb-4">
        <!-- Share Platform Stats -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-share me-2"></i>Share Platform Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div id="platform-stats">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-hourglass-split fs-1"></i>
                            <p class="mt-2">Loading platform data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Analytics -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-bell me-2"></i>Notification Preferences
                    </h5>
                </div>
                <div class="card-body">
                    <div id="notification-stats">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-hourglass-split fs-1"></i>
                            <p class="mt-2">Loading notification data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-clock-history me-2"></i>Recent Activity
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>User</th>
                                    <th>Details</th>
                                    <th>Time</th>
                                    <th>Impact</th>
                                </tr>
                            </thead>
                            <tbody id="recent-activity">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-hourglass-split fs-1"></i>
                                        <p class="mt-2">Loading recent activity...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Define routes for JavaScript
        window.routes = {
            analytics: '{{ route('admin.api.analytics') }}',
            userAnalytics: '{{ route('admin.api.user.analytics') }}',
            quoteAnalytics: '{{ route('admin.api.quote.analytics') }}'
        };

        // Global variables
        let userGrowthChart = null;
        let categoryChart = null;
        let currentDateRange = 30;

        // Initialize analytics
        async function initializeAnalytics() {
            await loadAnalytics();
            await loadUserAnalytics();
            await loadQuoteAnalytics();
            await loadTopQuotes();
            await loadEngagementStats();
            await loadPlatformStats();
            await loadNotificationStats();
            await loadRecentActivity();
        }

        // Load main analytics
        async function loadAnalytics() {
            try {
                const response = await fetch(window.routes.analytics);
                const data = await response.json();

                if (data.success) {
                    updateKeyMetrics(data.data);
                    updateUserGrowthChart(data.data.user_growth || []);
                    updateCategoryChart(data.data.popular_categories || []);
                }
            } catch (error) {
                console.error('Error loading analytics:', error);
            }
        }

        // Load user analytics
        async function loadUserAnalytics() {
            try {
                const response = await fetch(window.routes.userAnalytics);
                const data = await response.json();

                if (data.success) {
                    updateEngagementStats(data.data);
                }
            } catch (error) {
                console.error('Error loading user analytics:', error);
            }
        }

        // Load quote analytics
        async function loadQuoteAnalytics() {
            try {
                const response = await fetch(window.routes.quoteAnalytics);
                const data = await response.json();

                if (data.success) {
                    updateTopQuotes(data.data.top_performing_quotes || []);
                }
            } catch (error) {
                console.error('Error loading quote analytics:', error);
            }
        }

        // Update key metrics
        function updateKeyMetrics(data) {
            document.getElementById('total-quotes').textContent = data.total_quotes || 0;
            document.getElementById('active-users').textContent = data.total_users || 0;
            document.getElementById('total-views').textContent = data.total_views || 0;
            document.getElementById('total-shares').textContent = data.total_shares || 0;

            // Update growth indicators
            document.getElementById('quotes-growth').textContent = `+${data.quotes_growth || 0}% from last period`;
            document.getElementById('users-growth').textContent = `+${data.users_growth || 0}% from last period`;
            document.getElementById('views-growth').textContent = `+${data.views_growth || 0}% from last period`;
            document.getElementById('shares-growth').textContent = `+${data.shares_growth || 0}% from last period`;
        }

        // Update user growth chart
        function updateUserGrowthChart(data) {
            const ctx = document.getElementById('userGrowthChart').getContext('2d');

            if (userGrowthChart) {
                userGrowthChart.destroy();
            }

            userGrowthChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.date),
                    datasets: [{
                        label: 'New Users',
                        data: data.map(item => item.count),
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Update category chart
        function updateCategoryChart(data) {
            const ctx = document.getElementById('categoryChart').getContext('2d');

            if (categoryChart) {
                categoryChart.destroy();
            }

            // Use category colors if available, otherwise fallback to default colors
            const defaultColors = [
                '#667eea', '#764ba2', '#f093fb', '#f5576c',
                '#4facfe', '#00f2fe', '#43e97b', '#38f9d7'
            ];

            const colors = data.map((item, index) => item.color || defaultColors[index % defaultColors.length]);

            categoryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(item => item.name),
                    datasets: [{
                        data: data.map(item => item.quotes_count),
                        backgroundColor: colors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Load top quotes
        async function loadTopQuotes() {
            try {
                const response = await fetch('{{ route('admin.api.quotes.index') }}');
                const data = await response.json();

                if (data.success) {
                    const quotes = data.data.data || [];
                    const topQuotes = quotes
                        .sort((a, b) => (b.view_count || 0) - (a.view_count || 0))
                        .slice(0, 5);

                    updateTopQuotes(topQuotes);
                }
            } catch (error) {
                console.error('Error loading top quotes:', error);
            }
        }

        // Update top quotes
        function updateTopQuotes(quotes) {
            const container = document.getElementById('top-quotes');

            if (!quotes || quotes.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2">No quotes found</p>
                    </div>
                `;
                return;
            }

            let html = '';
            quotes.forEach((quote, index) => {
                const views = quote.view_count || 0;
                const shares = quote.share_count || 0;

                html += `
                    <div class="border-bottom p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-primary me-2">#${index + 1}</span>
                                    <div class="d-flex align-items-center">
                                        ${quote.category.image ?
                                            `<img src="/storage/${quote.category.image}" alt="${quote.category.name}" class="analytics-category-image me-2">` :
                                            `<div class="analytics-category-no-image me-2">
                                                                            <i class="bi bi-tag"></i>
                                                                        </div>`
                                        }
                                        <small class="text-muted">${quote.category.name}</small>
                                    </div>
                                </div>
                                <p class="mb-1 fw-medium">"${quote.content.substring(0, 60)}${quote.content.length > 60 ? '...' : ''}"</p>
                                <small class="text-muted">${quote.author || 'Unknown'}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary">${views}</div>
                                <small class="text-muted">views</small>
                                <br>
                                <div class="fw-bold text-success">${shares}</div>
                                <small class="text-muted">shares</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Load engagement stats
        async function loadEngagementStats() {
            const stats = {
                avg_session_duration: '2m 34s',
                bounce_rate: '23%',
                pages_per_session: '3.2',
                return_visitors: '67%'
            };
            updateEngagementStats(stats);
        }

        // Update engagement stats
        function updateEngagementStats(stats) {
            const container = document.getElementById('engagement-stats');

            container.innerHTML = `
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="fw-bold text-primary mb-1">${stats.avg_session_duration || '2m 34s'}</h4>
                            <small class="text-muted">Avg. Session Duration</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="fw-bold text-success mb-1">${stats.bounce_rate || '23%'}</h4>
                            <small class="text-muted">Bounce Rate</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="fw-bold text-info mb-1">${stats.pages_per_session || '3.2'}</h4>
                            <small class="text-muted">Pages per Session</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="fw-bold text-warning mb-1">${stats.return_visitors || '67%'}</h4>
                            <small class="text-muted">Return Visitors</small>
                        </div>
                    </div>
                </div>
            `;
        }

        // Load platform stats
        async function loadPlatformStats() {
            const platforms = [{
                    name: 'Facebook',
                    count: 45,
                    percentage: 45
                },
                {
                    name: 'Twitter',
                    count: 32,
                    percentage: 32
                },
                {
                    name: 'WhatsApp',
                    count: 15,
                    percentage: 15
                },
                {
                    name: 'Email',
                    count: 8,
                    percentage: 8
                }
            ];
            updatePlatformStats(platforms);
        }

        // Update platform stats
        function updatePlatformStats(platforms) {
            const container = document.getElementById('platform-stats');

            let html = '';
            platforms.forEach(platform => {
                html += `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="category-icon-bg rounded-circle p-2 me-3">
                                <i class="bi bi-share"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">${platform.name}</h6>
                                <small class="text-muted">${platform.count} shares</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">${platform.percentage}%</div>
                            <div class="progress" style="width: 60px; height: 4px;">
                                <div class="progress-bar" style="width: ${platform.percentage}%"></div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Load notification stats
        async function loadNotificationStats() {
            const stats = [{
                    time: '9:00 AM',
                    count: 45,
                    percentage: 45
                },
                {
                    time: '12:00 PM',
                    count: 32,
                    percentage: 32
                },
                {
                    time: '6:00 PM',
                    count: 15,
                    percentage: 15
                },
                {
                    time: '9:00 PM',
                    count: 8,
                    percentage: 8
                }
            ];
            updateNotificationStats(stats);
        }

        // Update notification stats
        function updateNotificationStats(stats) {
            const container = document.getElementById('notification-stats');

            let html = '';
            stats.forEach(stat => {
                html += `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="category-icon-bg rounded-circle p-2 me-3">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">${stat.time}</h6>
                                <small class="text-muted">${stat.count} users</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">${stat.percentage}%</div>
                            <div class="progress" style="width: 60px; height: 4px;">
                                <div class="progress-bar bg-success" style="width: ${stat.percentage}%"></div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Load recent activity
        async function loadRecentActivity() {
            const activities = [{
                    activity: 'New Quote Added',
                    user: 'John Doe',
                    details: 'Added motivational quote',
                    time: '2 hours ago',
                    impact: 'High'
                },
                {
                    activity: 'User Registered',
                    user: 'Jane Smith',
                    details: 'New user joined',
                    time: '3 hours ago',
                    impact: 'Medium'
                },
                {
                    activity: 'Quote Shared',
                    user: 'Mike Johnson',
                    details: 'Shared on Facebook',
                    time: '4 hours ago',
                    impact: 'High'
                },
                {
                    activity: 'Category Created',
                    user: 'Admin',
                    details: 'New category: Technology',
                    time: '5 hours ago',
                    impact: 'Medium'
                }
            ];
            updateRecentActivity(activities);
        }

        // Update recent activity
        function updateRecentActivity(activities) {
            const tbody = document.getElementById('recent-activity');

            let html = '';
            activities.forEach(activity => {
                const impactClass = activity.impact === 'High' ? 'badge bg-success' :
                    activity.impact === 'Medium' ? 'badge bg-warning' : 'badge bg-info';

                html += `
                    <tr>
                        <td>
                            <div class="fw-medium">${activity.activity}</div>
                        </td>
                        <td>${activity.user}</td>
                        <td>${activity.details}</td>
                        <td>${activity.time}</td>
                        <td><span class="${impactClass}">${activity.impact}</span></td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        // Date range functions
        function updateDateRange() {
            const range = document.getElementById('date-range').value;
            const startDiv = document.getElementById('custom-date-start');
            const endDiv = document.getElementById('custom-date-end');

            if (range === 'custom') {
                startDiv.style.display = 'block';
                endDiv.style.display = 'block';
            } else {
                startDiv.style.display = 'none';
                endDiv.style.display = 'none';
                currentDateRange = parseInt(range);
                applyDateFilter();
            }
        }

        function applyDateFilter() {
            const range = document.getElementById('date-range').value;

            if (range === 'custom') {
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;

                if (startDate && endDate) {
                    // Apply custom date filter
                    console.log('Applying custom date filter:', startDate, 'to', endDate);
                    refreshAnalytics();
                }
            } else {
                currentDateRange = parseInt(range);
                refreshAnalytics();
            }
        }

        // Refresh analytics
        function refreshAnalytics() {
            initializeAnalytics();
        }

        // Export analytics
        function exportAnalytics() {
            // Create a CSV export of analytics data
            const csvContent = "data:text/csv;charset=utf-8," +
                "Metric,Value,Growth\n" +
                "Total Quotes," + (document.getElementById('total-quotes').textContent) + ",0%\n" +
                "Active Users," + (document.getElementById('active-users').textContent) + ",0%\n" +
                "Total Views," + (document.getElementById('total-views').textContent) + ",0%\n" +
                "Total Shares," + (document.getElementById('total-shares').textContent) + ",0%";

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "analytics-report.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            initializeAnalytics();
        });
    </script>
@endpush
