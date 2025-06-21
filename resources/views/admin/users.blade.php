@extends('layouts.admin')

@section('title', 'User Management')

@section('header_title', 'User Management')
@section('header_subtitle', 'Manage users and view analytics')
@section('header_actions')
    <button class="btn btn-outline-primary" onclick="loadUserAnalytics()">
        <i class="bi bi-graph-up me-2"></i>Analytics
    </button>
    <button class="btn btn-primary" onclick="exportUsers()">
        <i class="bi bi-download me-2"></i>Export Users
    </button>
@endsection

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">User Management</h1>
            <p class="text-muted mb-0">Manage users and view analytics</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="loadUserAnalytics()">
                <i class="bi bi-graph-up me-2"></i>Analytics
            </button>
            <button class="btn btn-primary" onclick="exportUsers()">
                <i class="bi bi-download me-2"></i>Export Users
            </button>
            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Users</h6>
                            <h3 class="mb-0 fw-bold" id="users-total">0</h3>
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
                            <i class="bi bi-person-check fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Active Users</h6>
                            <h3 class="mb-0 fw-bold" id="users-active">0</h3>
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
                            <i class="bi bi-gear fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">With Preferences</h6>
                            <h3 class="mb-0 fw-bold" id="users-preferences">0</h3>
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
                            <i class="bi bi-phone fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Mobile Users</h6>
                            <h3 class="mb-0 fw-bold" id="users-mobile">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header bg-transparent border-0">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bi bi-table me-2"></i>All Users
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Preferences</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-hourglass-split fs-1"></i>
                                <p class="mt-2">Loading users...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end" id="pagination-links">
            <!-- Pagination links will be injected here -->
        </div>
    </div>

    <!-- Analytics Modal -->
    <div class="modal fade" id="analyticsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-graph-up me-2"></i>User Analytics
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

    <!-- User Detail Modal -->
    <div class="modal fade" id="userDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person me-2"></i>User Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userDetailModalBody">
                    <!-- User details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="editCurrentUser()">
                        <i class="bi bi-pencil me-2"></i>Edit User
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Edit Modal -->
    <div class="modal fade" id="userEditModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil me-2"></i>Edit User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="userEditForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="userName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="userName" required>
                        </div>
                        <div class="mb-3">
                            <label for="userEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="userEmail" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="userIsAdmin">
                                <label class="form-check-label" for="userIsAdmin">
                                    Admin User
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="userNotificationTime" class="form-label">Notification Time</label>
                            <input type="time" class="form-control" id="userNotificationTime">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="userNotificationsEnabled">
                                <label class="form-check-label" for="userNotificationsEnabled">
                                    Enable Notifications
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preferred Categories</label>
                            <div id="userCategoryPreferences">
                                <!-- Categories will be loaded here -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Routes configuration
        window.routes = {
            users: '{{ route('admin.api.users') }}',
            usersExport: '{{ route('admin.api.users.export') }}',
            userAnalytics: '{{ route('admin.api.user.analytics') }}',
            categories: '{{ route('admin.api.categories.index') }}',
            dashboard: '{{ route('admin.api.dashboard') }}'
        };

        let currentUser = null;
        let allCategories = [];
        let currentPage = 1;

        // Load users with pagination
        async function loadUsers(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${window.routes.users}?page=${page}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.success) {
                    displayUsers(data.data.users.data);
                    updateStats(data.data.stats);
                    displayPagination(data.data.users);
                } else {
                    showError('Failed to load users');
                }
            } catch (error) {
                console.error('Error loading users:', error);
                showError('Failed to load users');
            }
        }

        // Display users in table
        function displayUsers(users) {
            const tbody = document.getElementById('users-table-body');

            if (users.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">No users found</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = users.map(user => {
                const joinedDate = new Date(user.created_at).toLocaleDateString();
                const hasPreferences = hasMeaningfulPreferences(user);
                const status = user.is_admin ? 'Admin' : 'User';
                const statusClass = user.is_admin ? 'badge bg-danger' : 'badge bg-success';

                return `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                ${generateUserAvatar(user)}
                                <div class="ms-3">
                                    <h6 class="mb-0 fw-bold">${user.name}</h6>
                                    <small class="text-muted">ID: ${user.id}</small>
                                </div>
                            </div>
                        </td>
                        <td>${user.email}</td>
                        <td>${joinedDate}</td>
                        <td>
                            <span class="badge ${hasPreferences ? 'bg-success' : 'bg-secondary'}">
                                ${hasPreferences ? 'Yes' : 'No'}
                            </span>
                        </td>
                        <td>
                            <span class="${statusClass}">${status}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="viewUser(${user.id})" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" onclick="editUser(${user.id})" title="Edit User">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Check if user has meaningful preferences
        function hasMeaningfulPreferences(user) {
            if (!user.preferences) {
                return false;
            }

            const prefs = user.preferences;

            // Check if notifications are enabled
            if (prefs.notifications_enabled) {
                return true;
            }

            // Check if user has device token
            if (prefs.device_token) {
                return true;
            }

            // Check if user has preferred categories
            if (prefs.preferred_categories && Array.isArray(prefs.preferred_categories) && prefs.preferred_categories
                .length > 0) {
                return true;
            }

            return false;
        }

        // Generate user avatar
        function generateUserAvatar(user) {
            const initials = user.name.split(' ').map(n => n[0]).join('').toUpperCase();
            const colors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-info'];
            const color = colors[user.id % colors.length];

            return `
                <div class="avatar ${color} text-white rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 40px; height: 40px; font-size: 14px; font-weight: 600;">
                    ${initials}
                </div>
            `;
        }

        // Update statistics
        function updateStats(stats) {
            document.getElementById('users-total').textContent = stats.total_users;
            document.getElementById('users-active').textContent = stats.active_users;
            document.getElementById('users-preferences').textContent = stats.users_with_preferences;
            document.getElementById('users-mobile').textContent = stats.users_with_device_tokens;
        }

        // Display pagination
        function displayPagination(paginator) {
            const container = document.getElementById('pagination-links');

            if (paginator.last_page <= 1) {
                container.innerHTML = '';
                return;
            }

            let links = '';

            // Previous button
            if (paginator.current_page > 1) {
                links +=
                    `<button class="btn btn-outline-primary btn-sm me-2" onclick="loadUsers(${paginator.current_page - 1})">Previous</button>`;
            }

            // Page numbers
            for (let i = 1; i <= paginator.last_page; i++) {
                if (i === paginator.current_page) {
                    links += `<button class="btn btn-primary btn-sm me-2">${i}</button>`;
                } else {
                    links += `<button class="btn btn-outline-primary btn-sm me-2" onclick="loadUsers(${i})">${i}</button>`;
                }
            }

            // Next button
            if (paginator.current_page < paginator.last_page) {
                links +=
                    `<button class="btn btn-outline-primary btn-sm" onclick="loadUsers(${paginator.current_page + 1})">Next</button>`;
            }

            container.innerHTML = links;
        }

        // Load user analytics
        async function loadUserAnalytics() {
            try {
                const response = await fetch(window.routes.userAnalytics, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.success) {
                    showAnalyticsModal(data.data);
                } else {
                    showError('Failed to load analytics');
                }
            } catch (error) {
                console.error('Error loading analytics:', error);
                showError('Failed to load analytics');
            }
        }

        // Show analytics modal
        function showAnalyticsModal(analytics) {
            const modal = new bootstrap.Modal(document.getElementById('analyticsModal'));
            const modalBody = document.getElementById('analyticsModalBody');

            modalBody.innerHTML = `
                <div class="row g-4 mb-4">
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="fw-bold text-primary">${analytics.total_users}</h4>
                            <small class="text-muted">Total Users</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="fw-bold text-success">${analytics.active_users}</h4>
                            <small class="text-muted">Active Users</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="fw-bold text-info">${analytics.users_with_device_tokens}</h4>
                            <small class="text-muted">Mobile Users</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="fw-bold text-warning">${analytics.popular_notification_times.length}</h4>
                            <small class="text-muted">Notification Times</small>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="fw-bold">Popular Notification Times</h6>
                <div class="row g-2">
                    ${analytics.popular_notification_times.map(time => `
                                    <div class="col-6">
                                        <div class="d-flex justify-content-between">
                                            <span>${time.notification_time}</span>
                                            <span class="badge bg-primary">${time.count}</span>
                                        </div>
                                    </div>
                                `).join('')}
                </div>

                <hr>

                <h6 class="fw-bold">Category Preferences</h6>
                <div class="row g-2">
                    ${analytics.category_preferences.map(pref => `
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between">
                                            <span>${pref.category.name}</span>
                                            <span class="badge bg-success">${pref.user_count} users</span>
                                        </div>
                                    </div>
                                `).join('')}
                </div>
            `;

            modal.show();
        }

        // Export users
        function exportUsers() {
            fetch(window.routes.usersExport)
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'users-export.csv';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                })
                .catch(error => {
                    console.error('Error exporting users:', error);
                    showError('Failed to export users');
                });
        }

        // View user details
        async function viewUser(userId) {
            console.log('Attempting to view user with ID:', userId);
            try {
                const response = await fetch(`{{ route('admin.api.users.detail', ':id') }}`.replace(':id', userId), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Response not ok:', response.status, errorText);

                    if (response.status === 401) {
                        showError('Authentication required. Please log in again.');
                        // Redirect to login
                        window.location.href = '{{ route('admin.login') }}';
                        return;
                    } else if (response.status === 403) {
                        showError('Access denied. Admin privileges required.');
                        return;
                    } else if (response.status === 404) {
                        showError('User not found.');
                        return;
                    } else {
                        showError(`Server error: ${response.status}`);
                        return;
                    }
                }

                const data = await response.json();
                console.log('Response data:', data);

                if (data.success) {
                    currentUser = data.data;
                    showUserDetail(currentUser);
                } else {
                    showError(data.message || 'Failed to load user details');
                }
            } catch (error) {
                console.error('Error loading user details:', error);
                showError('Failed to load user details: ' + error.message);
            }
        }

        // Show user detail modal
        function showUserDetail(user) {
            const modal = new bootstrap.Modal(document.getElementById('userDetailModal'));
            const modalBody = document.getElementById('userDetailModalBody');

            const joinedDate = new Date(user.created_at).toLocaleDateString();
            const hasPreferences = hasMeaningfulPreferences(user);
            const status = user.is_admin ? 'Admin' : 'User';
            const statusClass = user.is_admin ? 'badge bg-danger' : 'badge bg-success';

            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-3 text-center">
                        ${generateUserAvatar(user)}
                        <h5 class="mt-3 mb-1">${user.name}</h5>
                        <span class="${statusClass}">${status}</span>
                    </div>
                    <div class="col-md-9">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Email</label>
                                <p class="mb-0">${user.email}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Joined</label>
                                <p class="mb-0">${joinedDate}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Has Preferences</label>
                                <p class="mb-0">
                                    <span class="badge ${hasPreferences ? 'bg-success' : 'bg-secondary'}">
                                        ${hasPreferences ? 'Yes' : 'No'}
                                    </span>
                                </p>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Device Token</label>
                                <p class="mb-0">${user.preferences && user.preferences.device_token ? 'Yes' : 'No'}</p>
                            </div>
                        </div>

                        ${user.preferences ? `
                                <hr>
                                <h6 class="fw-bold">Preferences</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label fw-bold">Notification Time</label>
                                        <p class="mb-0">${user.preferences.notification_time || 'Not set'}</p>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold">Notifications Enabled</label>
                                        <p class="mb-0">
                                            <span class="badge ${user.preferences.notifications_enabled ? 'bg-success' : 'bg-secondary'}">
                                                ${user.preferences.notifications_enabled ? 'Yes' : 'No'}
                                            </span>
                                        </p>
                                    </div>
                                    ${user.preferences.preferred_categories && user.preferences.preferred_categories.length > 0 ? `
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Preferred Categories</label>
                                        <div class="d-flex flex-wrap gap-1">
                                            ${user.preferences.preferred_categories.map(catId => {
                                                const category = allCategories.find(c => c.id == catId);
                                                return category ? `<span class="badge bg-primary">${category.name}</span>` : '';
                                            }).join('')}
                                        </div>
                                    </div>
                                ` : ''}
                                </div>
                            ` : ''}
                    </div>
                </div>
            `;

            modal.show();
        }

        // Edit user
        async function editUser(userId) {
            console.log('Attempting to edit user with ID:', userId);
            try {
                const response = await fetch(`{{ route('admin.api.users.detail', ':id') }}`.replace(':id', userId), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                console.log('Response status:', response.status);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Response not ok:', response.status, errorText);

                    if (response.status === 401) {
                        showError('Authentication required. Please log in again.');
                        // Redirect to login
                        window.location.href = '{{ route('admin.login') }}';
                        return;
                    } else if (response.status === 403) {
                        showError('Access denied. Admin privileges required.');
                        return;
                    } else if (response.status === 404) {
                        showError('User not found.');
                        return;
                    } else {
                        showError(`Server error: ${response.status}`);
                        return;
                    }
                }

                const data = await response.json();
                console.log('Response data:', data);

                if (data.success) {
                    currentUser = data.data;
                    showUserEditForm(currentUser);
                } else {
                    showError(data.message || 'Failed to load user details');
                }
            } catch (error) {
                console.error('Error loading user details:', error);
                showError('Failed to load user details: ' + error.message);
            }
        }

        // Edit current user (from detail modal)
        function editCurrentUser() {
            if (currentUser) {
                bootstrap.Modal.getInstance(document.getElementById('userDetailModal')).hide();
                showUserEditForm(currentUser);
            }
        }

        // Show user edit form
        function showUserEditForm(user) {
            const modal = new bootstrap.Modal(document.getElementById('userEditModal'));

            // Populate form fields
            document.getElementById('userName').value = user.name;
            document.getElementById('userEmail').value = user.email;
            document.getElementById('userIsAdmin').checked = user.is_admin;

            if (user.preferences) {
                document.getElementById('userNotificationTime').value = user.preferences.notification_time || '';
                document.getElementById('userNotificationsEnabled').checked = user.preferences.notifications_enabled;
            } else {
                document.getElementById('userNotificationTime').value = '';
                document.getElementById('userNotificationsEnabled').checked = false;
            }

            // Populate category preferences
            populateCategoryPreferences(user.preferences ? user.preferences.preferred_categories : []);

            modal.show();
        }

        // Populate category preferences checkboxes
        function populateCategoryPreferences(selectedCategories = []) {
            const container = document.getElementById('userCategoryPreferences');

            if (allCategories.length === 0) {
                container.innerHTML = '<p class="text-muted">Loading categories...</p>';
                return;
            }

            container.innerHTML = allCategories.map(category => `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                           id="category_${category.id}"
                           value="${category.id}"
                           ${selectedCategories.includes(category.id) ? 'checked' : ''}>
                    <label class="form-check-label" for="category_${category.id}">
                        ${category.name}
                    </label>
                </div>
            `).join('');
        }

        // Save user changes
        async function saveUserChanges() {
            if (!currentUser) {
                showError('No user selected');
                return;
            }

            const formData = {
                name: document.getElementById('userName').value,
                email: document.getElementById('userEmail').value,
                is_admin: document.getElementById('userIsAdmin').checked,
                notification_time: document.getElementById('userNotificationTime').value,
                notifications_enabled: document.getElementById('userNotificationsEnabled').checked,
                preferred_categories: getSelectedCategories()
            };

            try {
                const response = await fetch(`{{ route('admin.api.users.update', ':id') }}`.replace(':id', currentUser
                    .id), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('userEditModal')).hide();
                    showSuccess('User updated successfully');
                    loadUsers(); // Reload users list
                } else {
                    showError(data.message || 'Failed to update user');
                }
            } catch (error) {
                console.error('Error updating user:', error);
                showError('Failed to update user');
            }
        }

        // Get selected categories from checkboxes
        function getSelectedCategories() {
            const checkboxes = document.querySelectorAll('#userCategoryPreferences input[type="checkbox"]:checked');
            return Array.from(checkboxes).map(cb => parseInt(cb.value));
        }

        // Check authentication status
        async function checkAuthStatus() {
            try {
                const response = await fetch(window.routes.dashboard, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                console.log('Auth check response status:', response.status);

                if (!response.ok) {
                    if (response.status === 401 || response.status === 403) {
                        console.log('Not authenticated, redirecting to login');
                        window.location.href = '{{ route('admin.login') }}';
                        return false;
                    }
                }

                return true;
            } catch (error) {
                console.error('Error checking auth status:', error);
                return false;
            }
        }

        // Show error message
        function showError(message) {
            alert('Error: ' + message);
        }

        // Show success message
        function showSuccess(message) {
            alert('Success: ' + message);
        }

        // Show info message
        function showInfo(message) {
            alert('Info: ' + message);
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', async function() {
            console.log('Page loaded, checking authentication...');

            // Check authentication first
            const isAuthenticated = await checkAuthStatus();
            if (!isAuthenticated) {
                return; // Will redirect to login
            }

            console.log('Authentication confirmed, loading data...');

            // Load categories for edit form
            await loadCategoriesForEdit();

            // Load users
            loadUsers();

            // Setup form submission
            document.getElementById('userEditForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveUserChanges();
            });
        });

        // Load categories for edit form
        async function loadCategoriesForEdit() {
            try {
                const response = await fetch(window.routes.categories);
                const data = await response.json();

                if (data.success) {
                    allCategories = data.data.data || [];
                }
            } catch (error) {
                console.error('Error loading categories for edit form:', error);
            }
        }
    </script>
@endpush
