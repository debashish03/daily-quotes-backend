<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="bi bi-quote me-2"></i>Daily Quotes
        </a>
    </div>
    <nav class="mt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.view') ? 'active' : '' }}"
                    href="{{ route('admin.users.view') }}">
                    <i class="bi bi-people me-2"></i>Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.quotes.view') ? 'active' : '' }}"
                    href="{{ route('admin.quotes.view') }}">
                    <i class="bi bi-chat-quote me-2"></i>Quotes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.categories.view') ? 'active' : '' }}"
                    href="{{ route('admin.categories.view') }}">
                    <i class="bi bi-tags me-2"></i>Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.analytics.view') ? 'active' : '' }}"
                    href="{{ route('admin.analytics.view') }}">
                    <i class="bi bi-graph-up me-2"></i>Analytics
                </a>
            </li>
        </ul>
    </nav>
</div>
