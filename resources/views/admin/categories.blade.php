@extends('layouts.admin')

@section('title', 'Category Management')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 fw-bold">Category Management</h1>
            <p class="text-muted mb-0">Manage quote categories</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="openCategoryModal()">
                <i class="bi bi-plus-lg me-2"></i>Add Category
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
            <h1 class="h3 mb-1 fw-bold">Category Management</h1>
            <p class="text-muted mb-0">Manage quote categories</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="openCategoryModal()">
                <i class="bi bi-plus-lg me-2"></i>Add Category
            </button>
            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Category Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-tags fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Categories</h6>
                            <h3 class="mb-0 fw-bold" id="categories-total">0</h3>
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
                            <i class="bi bi-chat-quote fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Quotes</h6>
                            <h3 class="mb-0 fw-bold" id="categories-quotes">0</h3>
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
                            <i class="bi bi-people fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">User Preferences</h6>
                            <h3 class="mb-0 fw-bold" id="categories-preferences">0</h3>
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
                            <i class="bi bi-star fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Most Popular</h6>
                            <h3 class="mb-0 fw-bold" id="categories-popular">-</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card">
        <div class="card-header bg-transparent border-0">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bi bi-tags me-2"></i>All Categories
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Color</th>
                            <th>Image</th>
                            <th>Quotes Count</th>
                            <th>User Preferences</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categories-table-body">
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-hourglass-split fs-1"></i>
                                <p class="mt-2">Loading categories...</p>
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

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="categoryModalTitle">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="categoryForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" required
                                placeholder="Enter category name...">
                        </div>
                        <div class="mb-3">
                            <label for="categoryDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="categoryDescription" rows="3" placeholder="Enter category description..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="categoryColor" class="form-label">Category Color</label>
                            <div class="color-picker-container">
                                <input type="color" class="form-control form-control-color" id="categoryColor"
                                    value="#667eea" title="Choose category color">
                                <input type="text" class="form-control category-color-text" id="categoryColorText"
                                    placeholder="#667eea" pattern="^#[0-9A-Fa-f]{6}$">
                            </div>
                            <small class="text-muted">Choose a color to represent this category</small>
                        </div>
                        <div class="mb-3">
                            <label for="categoryImage" class="form-label">Category Image</label>
                            <div class="image-upload-container">
                                <input type="file" class="form-control" id="categoryImage" accept="image/*"
                                    title="Upload category image">
                                <div id="imagePreview" class="mt-2 d-none">
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail"
                                        style="max-width: 200px; max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2"
                                        onclick="removeImage()">
                                        <i class="bi bi-trash me-1"></i>Remove Image
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Upload an image to represent this category (max 2MB)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Save Category
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
            categories: '{{ route('admin.api.categories.index') }}'
        };

        // Global state
        let categories = [];
        let currentCategoryId = null;

        // Load categories
        async function loadCategories(url = null) {
            const fetchUrl = url || window.routes.categories;
            try {
                const response = await fetch(fetchUrl);
                const data = await response.json();

                if (data.success) {
                    categories = data.data.data; // Data is now in data.data
                    renderCategories(categories);
                    renderPagination(data.data); // Pass the whole pagination object
                    await updateCategoryStats(); // Update statistics after loading categories
                }
            } catch (error) {
                console.error('Error loading categories:', error);
                const tbody = document.getElementById('categories-table-body');
                tbody.innerHTML =
                    '<tr><td colspan="7" class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle fs-1"></i><p class="mt-2">Failed to load categories.</p></td></tr>';
            }
        }

        // Render categories table
        function renderCategories(categories) {
            const tbody = document.getElementById('categories-table-body');
            tbody.innerHTML = '';

            if (categories.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1"></i><p class="mt-2">No categories found</p></td></tr>';
                return;
            }

            tbody.innerHTML = categories.map(category => `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="category-icon-bg rounded-circle p-2 me-3">
                                    <i class="bi bi-tag"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">${category.name}</h6>
                                </div>
                            </div>
                        </td>
                        <td>${category.description || 'No description'}</td>
                        <td>
                            <div class="category-color-cell">
                                <div class="color-dot" style="background-color: ${category.color};"></div>
                                <span class="category-color-text">${category.color}</span>
                            </div>
                        </td>
                        <td>
                            <div class="category-image-cell">
                                ${category.image ?
                                    `<img src="/storage/${category.image}" alt="${category.name}" class="category-thumbnail" title="${category.name}">` :
                                    `<div class="category-placeholder">
                                                                                                                                                <i class="bi bi-image text-muted"></i>
                                                                                                                                                <small class="d-block text-muted">No image</small>
                                                                                                                                            </div>`
                                }
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">${category.quotes_count || 0}</span>
                        </td>
                        <td>
                            <span class="badge bg-success">${category.user_preferences_count || 0}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="editCategory(${category.id})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteCategory(${category.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
        }

        // Render pagination links
        function renderPagination(paginationData) {
            console.log('Rendering pagination with data:', paginationData); // Debug log
            const paginationContainer = document.getElementById('pagination-links');
            paginationContainer.innerHTML = '';

            if (!paginationData || !paginationData.links) {
                console.log('No pagination data or links found'); // Debug log
                return;
            }

            // Show pagination if there are multiple pages (more than just current page)
            const hasMultiplePages = paginationData.last_page > 1;
            console.log('Has multiple pages:', hasMultiplePages, 'Last page:', paginationData.last_page); // Debug log

            if (!hasMultiplePages) {
                console.log('Only one page, not showing pagination'); // Debug log
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
                        loadCategories(link.url);
                    });
                }

                li.appendChild(a);
                ul.appendChild(li);
            });

            nav.appendChild(ul);
            paginationContainer.appendChild(nav);
            console.log('Pagination rendered successfully'); // Debug log
        }

        // Update category statistics
        async function updateCategoryStats() {
            try {
                // Fetch all categories for accurate statistics (without pagination)
                const response = await fetch(window.routes.categories + '?per_page=1000');
                const data = await response.json();

                if (data.success) {
                    const allCategories = data.data.data;
                    const totalCategories = allCategories.length;
                    const totalQuotes = allCategories.reduce((sum, cat) => sum + (cat.quotes_count || 0), 0);
                    const totalPreferences = allCategories.reduce((sum, cat) => sum + (cat.user_preferences_count || 0),
                        0);

                    // Find most popular category
                    const mostPopular = allCategories.reduce((max, cat) =>
                        (cat.user_preferences_count || 0) > (max.user_preferences_count || 0) ? cat : max, {
                            name: '-',
                            user_preferences_count: 0
                        }
                    );

                    document.getElementById('categories-total').textContent = totalCategories;
                    document.getElementById('categories-quotes').textContent = totalQuotes;
                    document.getElementById('categories-preferences').textContent = totalPreferences;
                    document.getElementById('categories-popular').textContent = mostPopular.name;
                }
            } catch (error) {
                console.error('Error updating category stats:', error);
                // Fallback to current page data if stats fetch fails
                const totalCategories = categories.length;
                const totalQuotes = categories.reduce((sum, cat) => sum + (cat.quotes_count || 0), 0);
                const totalPreferences = categories.reduce((sum, cat) => sum + (cat.user_preferences_count || 0), 0);

                document.getElementById('categories-total').textContent = totalCategories;
                document.getElementById('categories-quotes').textContent = totalQuotes;
                document.getElementById('categories-preferences').textContent = totalPreferences;
                document.getElementById('categories-popular').textContent = '-';
            }
        }

        // Open category modal
        function openCategoryModal(categoryId = null) {
            console.log('Opening category modal with ID:', categoryId); // Debug log
            currentCategoryId = categoryId;
            const modal = document.getElementById('categoryModal');
            const title = document.getElementById('categoryModalTitle');
            const form = document.getElementById('categoryForm');

            if (!modal) {
                console.error('Modal element not found!');
                return;
            }

            console.log('Modal element found, checking Bootstrap...'); // Debug log

            if (categoryId) {
                const category = categories.find(c => c.id === categoryId);
                if (category) {
                    title.textContent = 'Edit Category';
                    document.getElementById('categoryName').value = category.name;
                    document.getElementById('categoryDescription').value = category.description || '';
                    document.getElementById('categoryColor').value = category.color || '#667eea';
                    document.getElementById('categoryColorText').value = category.color || '#667eea';

                    // Handle image preview
                    const imagePreview = document.getElementById('imagePreview');
                    const previewImg = document.getElementById('previewImg');
                    if (category.image) {
                        previewImg.src = `/storage/${category.image}`;
                        imagePreview.classList.remove('d-none');
                    } else {
                        imagePreview.classList.add('d-none');
                    }
                }
            } else {
                title.textContent = 'Add New Category';
                form.reset();
                document.getElementById('imagePreview').classList.add('d-none');
            }

            try {
                console.log('Creating Bootstrap modal instance...'); // Debug log
                const modalInstance = new bootstrap.Modal(modal);
                console.log('Modal instance created, showing...'); // Debug log
                modalInstance.show();
                console.log('Modal should be visible now'); // Debug log
            } catch (error) {
                console.error('Error showing modal:', error);
                // Fallback: try to show modal manually
                modal.classList.add('show');
                modal.style.display = 'block';
                modal.setAttribute('aria-modal', 'true');
                modal.setAttribute('role', 'dialog');
                document.body.classList.add('modal-open');

                // Add backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        }

        // Save category
        async function saveCategory() {
            const formData = new FormData();
            formData.append('name', document.getElementById('categoryName').value);
            formData.append('description', document.getElementById('categoryDescription').value);
            formData.append('color', document.getElementById('categoryColor').value);

            const imageFile = document.getElementById('categoryImage').files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }

            try {
                const url = currentCategoryId ? `${window.routes.categories}/${currentCategoryId}` : window.routes
                    .categories;
                const method = currentCategoryId ? 'POST' : 'POST'; // Use POST for both with _method for update

                if (currentCategoryId) {
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
                    loadCategories();
                    showSuccess(currentCategoryId ? 'Category updated successfully!' : 'Category added successfully!');
                } else {
                    showError(data.message || 'Failed to save category');
                }
            } catch (error) {
                console.error('Error saving category:', error);
                showError('Failed to save category');
            }
        }

        // Edit category
        function editCategory(categoryId) {
            openCategoryModal(categoryId);
        }

        // Delete category
        async function deleteCategory(categoryId) {
            const category = categories.find(c => c.id === categoryId);
            if (!category) return;

            if (category.quotes_count > 0) {
                showError('Cannot delete category with existing quotes. Please move or delete the quotes first.');
                return;
            }

            if (!confirm('Are you sure you want to delete this category?')) {
                return;
            }

            try {
                const response = await fetch(`${window.routes.categories}/${categoryId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    loadCategories();
                    showSuccess('Category deleted successfully!');
                } else {
                    showError(data.message || 'Failed to delete category');
                }
            } catch (error) {
                console.error('Error deleting category:', error);
                showError('Failed to delete category');
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

        // Image preview functionality
        function setupImagePreview() {
            const imageInput = document.getElementById('categoryImage');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.classList.add('d-none');
                }
            });
        }

        // Remove image function
        function removeImage() {
            document.getElementById('categoryImage').value = '';
            document.getElementById('imagePreview').classList.add('d-none');
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing categories page'); // Debug log
            loadCategories();

            // Setup form submission
            document.getElementById('categoryForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveCategory();
            });

            // Debug: Check if modal exists
            const modal = document.getElementById('categoryModal');
            console.log('Modal element found:', !!modal); // Debug log

            // Debug: Check if Bootstrap is available
            console.log('Bootstrap available:', typeof bootstrap !== 'undefined'); // Debug log

            // Setup color picker synchronization
            const colorPicker = document.getElementById('categoryColor');
            const colorText = document.getElementById('categoryColorText');

            colorPicker.addEventListener('input', function() {
                colorText.value = this.value;
            });

            colorText.addEventListener('input', function() {
                if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                    colorPicker.value = this.value;
                }
            });

            // Setup image preview
            setupImagePreview();
        });
    </script>
@endpush
