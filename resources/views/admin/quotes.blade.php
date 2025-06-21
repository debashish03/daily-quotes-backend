@extends('layouts.admin')

@section('title', 'Quote Management')

@section('header_title', 'Quote Management')
@section('header_subtitle', 'Manage quotes and categories')
@section('header_actions')
    <button class="btn btn-primary" onclick="openQuoteModal()">
        <i class="bi bi-plus-lg me-2"></i>Add Quote
    </button>
@endsection

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Quote Management</h1>
            <p class="text-muted mb-0">Manage quotes and categories</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="openQuoteModal()">
                <i class="bi bi-plus-lg me-2"></i>Add Quote
            </button>
            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="quote-category-filter" class="form-label">Category</label>
                    <select class="form-select" id="quote-category-filter">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="quote-status-filter" class="form-label">Status</label>
                    <select class="form-select" id="quote-status-filter">
                        <option value="">All Status</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button class="btn btn-outline-primary" onclick="filterQuotes()">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                    <button class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="bi bi-x-circle me-2"></i>Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotes Table -->
    <div class="card">
        <div class="card-header bg-transparent border-0">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bi bi-chat-quote me-2"></i>All Quotes
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Quote</th>
                            <th>Author</th>
                            <th class="d-none d-md-table-cell">Category</th>
                            <th>Status</th>
                            <th class="d-none d-lg-table-cell">Views</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="quotes-table-body">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-hourglass-split fs-1"></i>
                                <p class="mt-2">Loading quotes...</p>
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

    <!-- Quote Modal -->
    <div class="modal fade" id="quoteModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="quoteModalTitle">Add New Quote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="quoteForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="quoteContent" class="form-label">Quote Content</label>
                            <textarea class="form-control" id="quoteContent" rows="4" required placeholder="Enter the quote content..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="quoteAuthor" class="form-label">Author</label>
                            <input type="text" class="form-control" id="quoteAuthor"
                                placeholder="Enter the author name...">
                        </div>
                        <div class="mb-3">
                            <label for="quoteCategory" class="form-label">Category</label>
                            <select class="form-select" id="quoteCategory" required>
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="quotePublished" checked>
                                <label class="form-check-label" for="quotePublished">
                                    Published
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Save Quote
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Define routes for JavaScript
        window.routes = {
            quotes: '{{ route('admin.api.quotes.index') }}',
            categories: '{{ route('admin.api.categories.index') }}'
        };

        // Global variables
        let quotes = [];
        let categories = [];
        let currentQuoteId = null;

        // Load quotes
        async function loadQuotes(url = null) {
            const fetchUrl = url || window.routes.quotes;
            console.log('Loading quotes from URL:', fetchUrl); // Debug log
            try {
                const response = await fetch(fetchUrl);
                const data = await response.json();

                if (data.success) {
                    quotes = data.data.data || [];
                    renderQuotesTable();
                    renderPagination(data.data); // Pass the whole pagination object

                    // Update filter UI if loading from a URL with parameters
                    if (url) {
                        updateFiltersFromURL(url);
                    }

                    // populateCategoryFilter() is now called in loadCategories()
                }
            } catch (error) {
                console.error('Error loading quotes:', error);
            }
        }

        // Load categories
        async function loadCategories() {
            try {
                console.log('Loading categories...'); // Debug log
                const response = await fetch(window.routes.categories);
                const data = await response.json();

                if (data.success) {
                    categories = data.data.data || []; // Fixed: access paginated data
                    console.log('Categories loaded:', categories.length); // Debug log
                    populateQuoteCategorySelect();
                    populateCategoryFilter(); // Also populate the filter
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        // Render quotes table
        function renderQuotesTable() {
            const tbody = document.getElementById('quotes-table-body');
            if (quotes.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1"></i><p class="mt-2">No quotes found</p></td></tr>';
                return;
            }

            tbody.innerHTML = quotes.map(quote => `
                <tr>
                    <td>
                        <div class="fw-medium">"${quote.content.substring(0, 50)}${quote.content.length > 50 ? '...' : ''}"</div>
                        <small class="text-muted d-md-none">${quote.category.name}</small>
                    </td>
                    <td>${quote.author || 'Unknown'}</td>
                    <td class="d-none d-md-table-cell">${quote.category.name}</td>
                    <td>
                        <span class="badge ${quote.is_published ? 'bg-success' : 'bg-warning'}">
                            ${quote.is_published ? 'Published' : 'Draft'}
                        </span>
                    </td>
                    <td class="d-none d-lg-table-cell">${quote.view_count || 0}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="editQuote(${quote.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteQuote(${quote.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Render pagination links
        function renderPagination(paginationData) {
            console.log('Rendering quotes pagination with data:', paginationData); // Debug log
            const paginationContainer = document.getElementById('pagination-links');
            paginationContainer.innerHTML = '';

            if (!paginationData || !paginationData.links) {
                console.log('No pagination data or links found for quotes'); // Debug log
                return;
            }

            // Show pagination if there are multiple pages (more than just current page)
            const hasMultiplePages = paginationData.last_page > 1;
            console.log('Has multiple pages for quotes:', hasMultiplePages, 'Last page:', paginationData
                .last_page); // Debug log

            if (!hasMultiplePages) {
                console.log('Only one page for quotes, not showing pagination'); // Debug log
                return;
            }

            const nav = document.createElement('nav');
            const ul = document.createElement('ul');
            ul.classList.add('pagination', 'mb-0');

            paginationData.links.forEach(link => {
                const li = document.createElement('li');
                li.classList.add('page-item');
                if (link.active) {
                    li.classList.add('active');
                }
                if (!link.url) {
                    li.classList.add('disabled');
                }

                const a = document.createElement('a');
                a.classList.add('page-link');
                a.innerHTML = link.label;

                if (link.url) {
                    a.href = '#'; // Prevent default navigation
                    a.addEventListener('click', (e) => {
                        e.preventDefault();
                        loadQuotes(link.url);
                    });
                }

                li.appendChild(a);
                ul.appendChild(li);
            });

            nav.appendChild(ul);
            paginationContainer.appendChild(nav);
            console.log('Quotes pagination rendered successfully'); // Debug log
        }

        // Populate category filter
        function populateCategoryFilter() {
            const select = document.getElementById('quote-category-filter');
            select.innerHTML = '<option value="">All Categories</option>';
            categories.forEach(category => {
                select.innerHTML += `<option value="${category.id}">${category.name}</option>`;
            });
        }

        // Populate quote category select
        function populateQuoteCategorySelect() {
            console.log('Populating quote category select...'); // Debug log
            const select = document.getElementById('quoteCategory');
            if (!select) {
                console.error('Quote category select element not found!');
                return;
            }
            console.log('Found select element, categories count:', categories.length); // Debug log

            select.innerHTML = '<option value="">Select Category</option>';
            categories.forEach(category => {
                select.innerHTML += `<option value="${category.id}">${category.name}</option>`;
            });
            console.log('Category select populated with options'); // Debug log
        }

        // Filter quotes
        async function filterQuotes() {
            const categoryFilter = document.getElementById('quote-category-filter').value;
            const statusFilter = document.getElementById('quote-status-filter').value;

            // Build query parameters
            const params = new URLSearchParams();
            if (categoryFilter) {
                params.append('category_id', categoryFilter);
            }
            if (statusFilter) {
                params.append('status', statusFilter);
            }

            // Reset to page 1 when filtering
            const url = `${window.routes.quotes}?${params.toString()}`;
            await loadQuotes(url);

            // Show filter status
            showFilterStatus(categoryFilter, statusFilter);
        }

        // Show filter status
        function showFilterStatus(categoryFilter, statusFilter) {
            const filterStatus = document.getElementById('filter-status');
            if (!filterStatus) {
                // Create filter status element if it doesn't exist
                const statusDiv = document.createElement('div');
                statusDiv.id = 'filter-status';
                statusDiv.className = 'alert alert-info mt-3';
                document.querySelector('.card.mb-4').appendChild(statusDiv);
            }

            const statusElement = document.getElementById('filter-status');
            let statusText = '';

            if (categoryFilter || statusFilter) {
                statusText = '<strong>Active Filters:</strong> ';
                const filters = [];

                if (categoryFilter) {
                    const categorySelect = document.getElementById('quote-category-filter');
                    const categoryName = categorySelect.options[categorySelect.selectedIndex].text;
                    filters.push(`Category: ${categoryName}`);
                }

                if (statusFilter) {
                    const statusName = statusFilter === 'published' ? 'Published' : 'Draft';
                    filters.push(`Status: ${statusName}`);
                }

                statusText += filters.join(', ');
                statusElement.innerHTML = statusText;
                statusElement.style.display = 'block';
            } else {
                statusElement.style.display = 'none';
            }
        }

        // Clear filters
        async function clearFilters() {
            document.getElementById('quote-category-filter').value = '';
            document.getElementById('quote-status-filter').value = '';
            await loadQuotes(); // Load without any filters

            // Hide filter status
            const filterStatus = document.getElementById('filter-status');
            if (filterStatus) {
                filterStatus.style.display = 'none';
            }
        }

        // Show current filter state
        function showCurrentFilters() {
            const categoryFilter = document.getElementById('quote-category-filter').value;
            const statusFilter = document.getElementById('quote-status-filter').value;
            console.log('Current filters - Category:', categoryFilter, 'Status:', statusFilter);
        }

        // Parse URL parameters and set filter values
        function setFiltersFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            const categoryId = urlParams.get('category_id');
            const status = urlParams.get('status');

            if (categoryId) {
                document.getElementById('quote-category-filter').value = categoryId;
            }
            if (status) {
                document.getElementById('quote-status-filter').value = status;
            }
        }

        // Quote modal functions
        function openQuoteModal(quoteId = null) {
            currentQuoteId = quoteId;
            const modal = new bootstrap.Modal(document.getElementById('quoteModal'));
            const title = document.getElementById('quoteModalTitle');
            const submitBtn = document.querySelector('#quoteForm button[type="submit"]');

            if (quoteId) {
                const quote = quotes.find(q => q.id === quoteId);
                if (quote) {
                    title.textContent = 'Edit Quote';
                    submitBtn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Update Quote';

                    document.getElementById('quoteContent').value = quote.content;
                    document.getElementById('quoteAuthor').value = quote.author || '';
                    document.getElementById('quoteCategory').value = quote.category_id;
                    document.getElementById('quotePublished').checked = quote.is_published;
                }
            } else {
                title.textContent = 'Add New Quote';
                submitBtn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Save Quote';

                document.getElementById('quoteForm').reset();
            }

            modal.show();
        }

        // Save quote
        async function saveQuote() {
            const formData = {
                content: document.getElementById('quoteContent').value,
                author: document.getElementById('quoteAuthor').value,
                category_id: document.getElementById('quoteCategory').value,
                is_published: document.getElementById('quotePublished').checked
            };

            try {
                const url = currentQuoteId ? `${window.routes.quotes}/${currentQuoteId}` : window.routes.quotes;
                const method = currentQuoteId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('quoteModal')).hide();
                    loadQuotes();
                    showSuccess(currentQuoteId ? 'Quote updated successfully!' : 'Quote added successfully!');
                } else {
                    showError(data.message || 'Failed to save quote');
                }
            } catch (error) {
                console.error('Error saving quote:', error);
                showError('Failed to save quote');
            }
        }

        // Edit quote
        function editQuote(quoteId) {
            openQuoteModal(quoteId);
        }

        // Delete quote
        async function deleteQuote(quoteId) {
            if (!confirm('Are you sure you want to delete this quote?')) {
                return;
            }

            try {
                const response = await fetch(`${window.routes.quotes}/${quoteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    loadQuotes();
                    showSuccess('Quote deleted successfully!');
                } else {
                    showError(data.message || 'Failed to delete quote');
                }
            } catch (error) {
                console.error('Error deleting quote:', error);
                showError('Failed to delete quote');
            }
        }

        // Show success message
        function showSuccess(message) {
            alert('Success: ' + message);
        }

        // Show error message
        function showError(message) {
            alert('Error: ' + message);
        }

        // Update filters from URL
        function updateFiltersFromURL(url) {
            try {
                const urlObj = new URL(url);
                const categoryId = urlObj.searchParams.get('category_id');
                const status = urlObj.searchParams.get('status');

                // Update filter dropdowns
                if (categoryId) {
                    document.getElementById('quote-category-filter').value = categoryId;
                } else {
                    document.getElementById('quote-category-filter').value = '';
                }

                if (status) {
                    document.getElementById('quote-status-filter').value = status;
                } else {
                    document.getElementById('quote-status-filter').value = '';
                }

                // Show filter status
                showFilterStatus(categoryId, status);

                console.log('Updated filters from URL - Category:', categoryId, 'Status:', status);
            } catch (error) {
                console.error('Error updating filters from URL:', error);
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', async function() {
            // Load categories first, then quotes
            await loadCategories();

            // Check if there are URL parameters and load quotes accordingly
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.has('category_id') || urlParams.has('status');

            if (hasFilters) {
                // Load quotes with current URL parameters
                await loadQuotes(window.location.href);
            } else {
                // Load quotes normally
                await loadQuotes();
            }

            // Setup form submission
            document.getElementById('quoteForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveQuote();
            });
        });
    </script>
@endpush
