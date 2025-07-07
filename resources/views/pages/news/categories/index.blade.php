@extends('layouts.main')

@section('title', 'News Categories')

@push('styles')
<style>
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .btn-list .btn {
        margin-right: 0.25rem;
    }
    
    .btn-list .btn:last-child {
        margin-right: 0;
    }
    
    .sortable-handle {
        cursor: grab;
        color: #6c757d;
    }
    
    .sortable-handle:hover {
        color: #0054a6;
    }
    
    .sortable-ghost {
        opacity: 0.5;
    }
    
    .sortable-chosen {
        background-color: #f8f9fa;
    }
    
    /* Search highlighting */
    mark.search-highlight {
        background-color: #fff3cd;
        padding: 0.1rem 0.2rem;
        border-radius: 0.25rem;
        font-weight: 600;
    }

    /* Modal specific styles */
    .form-control:focus {
        border-color: #0054a6;
        box-shadow: 0 0 0 0.2rem rgba(0, 84, 166, 0.25);
    }
    
    .form-hint {
        color: #6c757d;
        font-size: 0.75rem;
    }
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">News Categories</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-category-modal">
        <i class="ti ti-plus me-1"></i> Add Category
    </button>
</div>
@endsection

@section('content')

{{-- Search and Filter Form --}}
<div class="col-12">
    <form method="GET" action="{{ route('news.categories.index') }}" id="filter-form">
        <div class="d-flex justify-content-start align-items-center gap-2">
            <div class="input-icon" style="max-width: 350px;">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Search category name or description..." 
                       value="{{ request('search') }}" 
                       autocomplete="off" 
                       id="search-input">
            </div>
            <div class="d-flex gap-2 align-items-center">
                <select class="form-select" name="status" id="status-filter" style="min-width: 130px;">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @if(request('search') || request('status'))
                <a href="{{ route('news.categories.index') }}" class="btn btn-outline-secondary" title="Clear all filters">
                    <i class="ti ti-x"></i>
                </a>
                @endif
            </div>
        </div>
        
        {{-- Active Filters Display --}}
        @if(request('search') || request('status'))
        <div class="mt-2 d-flex gap-2 align-items-center flex-wrap">
            <small class="text-secondary">Active filters:</small>
            @if(request('search'))
            <span class="badge bg-blue-lt">
                <i class="ti ti-search me-1"></i>
                Search: "{{ request('search') }}"
            </span>
            @endif
            @if(request('status'))
            <span class="badge bg-green-lt">
                <i class="ti ti-filter me-1"></i>
                Status: {{ request('status') === 'active' ? 'Active' : 'Inactive' }}
            </span>
            @endif
        </div>
        @endif
    </form>
</div>

{{-- Categories Table --}}
<div class="col-12">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive" id="table-container">
                <table class="table" id="sortable-table">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">Order</th>
                            <th>Category</th>
                            <th width="150" class="text-center">News Count</th>
                            <th width="120" class="text-center">Status</th>
                            <th width="120" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-tbody">
                        @forelse($categories as $category)
                        <tr data-id="{{ $category->id }}">
                            <td class="text-center">
                                <div class="d-flex align-items-center h-100">
                                    <span class="sortable-handle">
                                        <i class="ti ti-grip-vertical"></i>
                                    </span>
                                    <small class="text-secondary d-block">{{ $category->order }}</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold text-dark mb-1" data-searchable="name">
                                        {{ $category->name }}
                                    </div>
                                    @if($category->description)
                                    <div class="text-secondary small" style="line-height: 1.4;" data-searchable="description">
                                        {{ Str::limit($category->description, 80) }}
                                    </div>
                                    @endif
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <small class="text-secondary">
                                            <i class="ti ti-link me-1"></i>{{ $category->slug }}
                                        </small>
                                        <small class="text-secondary">
                                            <i class="ti ti-calendar me-1"></i>{{ $category->created_at->format('d M Y') }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="text-dark fw-bold">{{ $category->news_count }}</div>
                                <small class="text-secondary">total articles</small>
                            </td>
                            <td class="text-center">
                                @if($category->is_active)
                                <span class="badge bg-green-lt">
                                    <i class="ti ti-check me-1"></i>Active
                                </span>
                                @else
                                <span class="badge bg-gray-lt">
                                    <i class="ti ti-x me-1"></i>Inactive
                                </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-list">
                                    <button type="button" 
                                            class="btn btn-primary-lt btn-icon edit-btn" 
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-description="{{ $category->description }}"
                                            data-slug="{{ $category->slug }}"
                                            data-news-count="{{ $category->news_count }}"
                                            data-is-active="{{ $category->is_active ? '1' : '0' }}"
                                            title="Edit Category">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn bg-danger-lt text-danger btn-icon delete-btn"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-url="{{ route('news.categories.destroy', $category) }}"
                                            title="Delete Category"
                                            {{ $category->news_count > 0 ? 'disabled' : '' }}>
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        @if(request('search') || request('status') || request('page'))
                                        <i class="ti ti-search icon icon-lg"></i>
                                        @else
                                        <i class="ti ti-folder icon icon-lg"></i>
                                        @endif
                                    </div>
                                    <p class="empty-title h3">
                                        @if(request('search') || request('status') || request('page'))
                                        No categories found
                                        @else
                                        No news categories yet
                                        @endif
                                    </p>
                                    <p class="empty-subtitle text-secondary">
                                        @if(request('search') || request('status') || request('page'))
                                        Try adjusting your search terms or clear the filters to see all categories.
                                        @else
                                        Get started by creating your first news category.<br>
                                        Categories help organize your news articles by type or topic.
                                        @endif
                                    </p>
                                    <div class="empty-action">
                                        @if(request('search') || request('status') || request('page'))
                                        <a href="{{ route('news.categories.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-x me-1"></i> Clear Filters
                                        </a>
                                        @else
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-category-modal">
                                            <i class="ti ti-plus me-1"></i> Create First Category
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Footer with Results Info and Pagination --}}
        @if($categories->total() > 0 || request('search') || request('status'))
        <div class="card-footer d-flex align-items-center">
            <div class="text-secondary">
                @if($categories->total() > 0)
                    Showing <strong>{{ $categories->firstItem() }}</strong> to <strong>{{ $categories->lastItem() }}</strong> 
                    of <strong>{{ $categories->total() }}</strong> results
                    @if(request('search'))
                        for "<strong>{{ request('search') }}</strong>"
                    @endif
                @else
                    No results found
                    @if(request('search') || request('status'))
                        with current filters
                    @endif
                @endif
            </div>
            
            @include('components.pagination', ['paginator' => $categories])
        </div>
        @endif
    </div>
</div>

{{-- Create Category Modal --}}
<div class="modal modal-blur fade" id="create-category-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" id="create-category-form" method="POST" action="{{ route('news.categories.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-folder-plus me-2"></i>
                    Create New Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required
                           placeholder="Enter category name (e.g., News, Events, City Concierge)" id="create-name-input">
                    <small class="form-hint">
                        <span id="create-name-count">0</span>/255 characters
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3" 
                              placeholder="Enter category description (optional)" id="create-description-input"></textarea>
                    <small class="form-hint">
                        <span id="create-description-count">0</span>/1000 characters. Optional field to describe the category purpose.
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <span class="form-check-label">Active Status</span>
                    </label>
                    <small class="form-hint d-block">
                        <i class="ti ti-info-circle me-1"></i>
                        Only active categories will be available for news articles.
                    </small>
                </div>
                
                {{-- Preview Section --}}
                <div class="card bg-light">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ti ti-eye me-1"></i>
                            Preview
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-secondary">Name:</small>
                                <div id="create-preview-name" class="fw-bold">-</div>
                            </div>
                            <div class="col-6">
                                <small class="text-secondary">Slug:</small>
                                <div id="create-preview-slug" class="text-secondary">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="create-submit-btn">
                    <i class="ti ti-device-floppy me-1"></i>
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Category Modal --}}
<div class="modal modal-blur fade" id="edit-category-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" id="edit-category-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>
                    Edit Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required
                           placeholder="Enter category name" id="edit-name-input">
                    <small class="form-hint">
                        <span id="edit-name-count">0</span>/255 characters
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3" 
                              placeholder="Enter category description (optional)" id="edit-description-input"></textarea>
                    <small class="form-hint">
                        <span id="edit-description-count">0</span>/1000 characters. Optional field to describe the category purpose.
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit-is-active">
                        <span class="form-check-label">Active Status</span>
                    </label>
                    <small class="form-hint d-block">
                        <i class="ti ti-info-circle me-1"></i>
                        Only active categories will be available for news articles.
                    </small>
                    <div id="edit-news-warning" class="mt-2" style="display: none;">
                        <small class="text-warning">
                            <i class="ti ti-alert-triangle me-1"></i>
                            This category has <span id="edit-news-count">0</span> news articles.
                        </small>
                    </div>
                </div>
                
                {{-- Current Info --}}
                <div class="card bg-light">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ti ti-info-square me-1"></i>
                            Current Info
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-secondary">Slug:</small>
                                <div id="edit-current-slug" class="text-secondary">-</div>
                            </div>
                            <div class="col-6">
                                <small class="text-secondary">News Count:</small>
                                <div id="edit-current-news-count" class="fw-bold">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="edit-submit-btn">
                    <i class="ti ti-device-floppy me-1"></i>
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

@push('scripts')
@include('components.toast')
@if(session('success'))
    <script>
        showToast('{{ session('success') }}', 'success');
    </script>
@endif
@if(session('error'))
    <script>
        showToast('{{ session('error') }}','error');
    </script>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Get form and input elements
        const filterForm = document.getElementById('filter-form');
        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('status-filter');
        const tableContainer = document.getElementById('table-container');
        
        // Debounce function for search
        let searchTimeout;
        
        // Search input with debounce (auto-submit after 600ms)
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                submitFilter();
            }, 600);
        });
        
        // Status filter change (immediate submit)
        statusFilter.addEventListener('change', function() {
            submitFilter();
        });
        
        // Handle Enter key in search input
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                submitFilter();
            }
        });
        
        // Submit filter function with loading state
        function submitFilter() {
            // Store current cursor position and search value
            const cursorPosition = searchInput.selectionStart;
            const searchValue = searchInput.value;
            
            // Store in sessionStorage for after page reload
            sessionStorage.setItem('searchInputFocus', 'true');
            sessionStorage.setItem('searchCursorPosition', cursorPosition);
            sessionStorage.setItem('searchValue', searchValue);
            
            // Show loading state
            showLoadingState();
            
            // Submit form
            filterForm.submit();
        }
        
        // Restore focus and cursor position after page load
        function restoreSearchFocus() {
            const shouldFocus = sessionStorage.getItem('searchInputFocus');
            const cursorPosition = sessionStorage.getItem('searchCursorPosition');
            const searchValue = sessionStorage.getItem('searchValue');
            
            if (shouldFocus === 'true' && searchInput.value === searchValue) {
                // Focus input
                searchInput.focus();
                
                // Restore cursor position
                if (cursorPosition !== null) {
                    searchInput.setSelectionRange(parseInt(cursorPosition), parseInt(cursorPosition));
                }
                
                // Clear stored values
                sessionStorage.removeItem('searchInputFocus');
                sessionStorage.removeItem('searchCursorPosition');
                sessionStorage.removeItem('searchValue');
            }
        }
        
        // Restore focus on page load
        restoreSearchFocus();
        
        // Show loading state
        function showLoadingState() {
            tableContainer.classList.add('loading-overlay');
            
            // Add loading spinner to search input
            const searchIcon = searchInput.parentElement.querySelector('i');
            const originalClass = searchIcon.className;
            searchIcon.className = 'ti ti-loader-2 animate-spin';
            
            // Reset after a delay (in case form submission fails)
            setTimeout(() => {
                tableContainer.classList.remove('loading-overlay');
                searchIcon.className = originalClass;
            }, 5000);
        }
        
        // Highlight search terms in results
        const searchTerm = '{{ request('search') }}';
        if (searchTerm) {
            highlightSearchResults(searchTerm.toLowerCase());
        }
        
        // Function to highlight search results
        function highlightSearchResults(term) {
            const searchableElements = document.querySelectorAll('[data-searchable]');
            
            searchableElements.forEach(element => {
                const text = element.textContent;
                const lowerText = text.toLowerCase();
                
                if (lowerText.includes(term)) {
                    const regex = new RegExp(`(${escapeRegExp(term)})`, 'gi');
                    element.innerHTML = text.replace(regex, '<mark class="search-highlight">$1</mark>');
                }
            });
        }
        
        // Escape special characters for regex
        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
        
        // Focus search input on Ctrl+K or Cmd+K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });

        // Initialize Sortable for drag and drop ordering
        @if(!request('search') && !request('status') && $categories->count() > 1)
        const sortableTable = document.getElementById('sortable-tbody');
        if (sortableTable) {
            new Sortable(sortableTable, {
                handle: '.sortable-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function(evt) {
                    const orders = [];
                    const rows = sortableTable.querySelectorAll('tr[data-id]');
                    
                    rows.forEach((row, index) => {
                        orders.push({
                            id: row.dataset.id,
                            order: index + 1
                        });
                    });
                    
                    // Update order via AJAX
                    updateOrder(orders);
                }
            });
        }
        @endif
        
        // Update order function
        function updateOrder(orders) {
            fetch('{{ route('news.categories.update-order') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ orders: orders })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    // Update order numbers in UI
                    orders.forEach((item, index) => {
                        const row = document.querySelector(`tr[data-id="${item.id}"]`);
                        if (row) {
                            const orderElement = row.querySelector('.sortable-handle + small');
                            if (orderElement) {
                                orderElement.textContent = item.order;
                            }
                        }
                    });
                } else {
                    showToast(data.message, 'error');
                    location.reload(); // Reload on error
                }
            })
            .catch(error => {
                showToast('Failed to update order', 'error');
                location.reload(); // Reload on error
            });
        }

        // Create Category Modal Functions
        setupCreateModal();
        setupEditModal();
        
        // Add loading animation CSS
        const style = document.createElement('style');
        style.textContent = `
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            .loading-overlay {
                opacity: 0.6;
                pointer-events: none;
            }
        `;
        document.head.appendChild(style);
    });

    function setupCreateModal() {
        const createModal = document.getElementById('create-category-modal');
        const createForm = document.getElementById('create-category-form');
        const createNameInput = document.getElementById('create-name-input');
        const createDescriptionInput = document.getElementById('create-description-input');
        const createNameCount = document.getElementById('create-name-count');
        const createDescriptionCount = document.getElementById('create-description-count');
        const createPreviewName = document.getElementById('create-preview-name');
        const createPreviewSlug = document.getElementById('create-preview-slug');
        const createSubmitBtn = document.getElementById('create-submit-btn');

        // Character counters and preview
        createNameInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            createNameCount.textContent = currentLength;
            
            // Update preview
            if (this.value.trim()) {
                createPreviewName.textContent = this.value;
                createPreviewSlug.textContent = generateSlug(this.value);
            } else {
                createPreviewName.textContent = '-';
                createPreviewSlug.textContent = '-';
            }
            
            // Add warning colors
            const percentage = (currentLength / 255) * 100;
            if (percentage > 90) {
                createNameCount.parentElement.classList.add('text-danger');
                createNameCount.parentElement.classList.remove('text-warning');
            } else if (percentage > 80) {
                createNameCount.parentElement.classList.add('text-warning');
                createNameCount.parentElement.classList.remove('text-danger');
            } else {
                createNameCount.parentElement.classList.remove('text-warning', 'text-danger');
            }
        });

        createDescriptionInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            createDescriptionCount.textContent = currentLength;
            
            // Add warning colors
            const percentage = (currentLength / 1000) * 100;
            if (percentage > 90) {
                createDescriptionCount.parentElement.classList.add('text-danger');
                createDescriptionCount.parentElement.classList.remove('text-warning');
            } else if (percentage > 80) {
                createDescriptionCount.parentElement.classList.add('text-warning');
                createDescriptionCount.parentElement.classList.remove('text-danger');
            } else {
                createDescriptionCount.parentElement.classList.remove('text-warning', 'text-danger');
            }
        });

        // Form submission
        createForm.addEventListener('submit', function(e) {
            createSubmitBtn.disabled = true;
            createSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
            createForm.classList.add('loading');
        });

        // Reset form when modal is closed
        createModal.addEventListener('hidden.bs.modal', function() {
            createForm.reset();
            createForm.classList.remove('loading');
            createSubmitBtn.disabled = false;
            createSubmitBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Create Category';
            createNameCount.textContent = '0';
            createDescriptionCount.textContent = '0';
            createPreviewName.textContent = '-';
            createPreviewSlug.textContent = '-';
            // Remove validation classes
            createNameCount.parentElement.classList.remove('text-warning', 'text-danger');
            createDescriptionCount.parentElement.classList.remove('text-warning', 'text-danger');
        });
    }

    function setupEditModal() {
        const editModal = document.getElementById('edit-category-modal');
        const editForm = document.getElementById('edit-category-form');
        const editNameInput = document.getElementById('edit-name-input');
        const editDescriptionInput = document.getElementById('edit-description-input');
        const editIsActive = document.getElementById('edit-is-active');
        const editNameCount = document.getElementById('edit-name-count');
        const editDescriptionCount = document.getElementById('edit-description-count');
        const editCurrentSlug = document.getElementById('edit-current-slug');
        const editCurrentNewsCount = document.getElementById('edit-current-news-count');
        const editNewsWarning = document.getElementById('edit-news-warning');
        const editNewsCount = document.getElementById('edit-news-count');
        const editSubmitBtn = document.getElementById('edit-submit-btn');

        // Handle edit button clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
                const button = e.target.classList.contains('edit-btn') ? e.target : e.target.closest('.edit-btn');
                const categoryId = button.getAttribute('data-id');
                const categoryName = button.getAttribute('data-name');
                const categoryDescription = button.getAttribute('data-description') || '';
                const categorySlug = button.getAttribute('data-slug');
                const categoryNewsCount = button.getAttribute('data-news-count');
                const isActive = button.getAttribute('data-is-active') === '1';
                
                // Populate edit form
                editForm.action = `{{ route('news.categories.index') }}/${categoryId}`;
                editNameInput.value = categoryName;
                editDescriptionInput.value = categoryDescription;
                editIsActive.checked = isActive;
                editCurrentSlug.textContent = categorySlug;
                editCurrentNewsCount.textContent = categoryNewsCount;
                editNewsCount.textContent = categoryNewsCount;
                
                // Update counters
                editNameCount.textContent = categoryName.length;
                editDescriptionCount.textContent = categoryDescription.length;
                
                // Show warning if category has news and is being set to inactive
                if (parseInt(categoryNewsCount) > 0 && !isActive) {
                    editNewsWarning.style.display = 'block';
                } else {
                    editNewsWarning.style.display = 'none';
                }
                
                // Show modal
                const modal = new bootstrap.Modal(editModal);
                modal.show();
            }
        });

        // Character counters
        editNameInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            editNameCount.textContent = currentLength;
            
            // Add warning colors
            const percentage = (currentLength / 255) * 100;
            if (percentage > 90) {
                editNameCount.parentElement.classList.add('text-danger');
                editNameCount.parentElement.classList.remove('text-warning');
            } else if (percentage > 80) {
                editNameCount.parentElement.classList.add('text-warning');
                editNameCount.parentElement.classList.remove('text-danger');
            } else {
                editNameCount.parentElement.classList.remove('text-warning', 'text-danger');
            }
        });

        editDescriptionInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            editDescriptionCount.textContent = currentLength;
            
            // Add warning colors
            const percentage = (currentLength / 1000) * 100;
            if (percentage > 90) {
                editDescriptionCount.parentElement.classList.add('text-danger');
                editDescriptionCount.parentElement.classList.remove('text-warning');
            } else if (percentage > 80) {
                editDescriptionCount.parentElement.classList.add('text-warning');
                editDescriptionCount.parentElement.classList.remove('text-danger');
            } else {
                editDescriptionCount.parentElement.classList.remove('text-warning', 'text-danger');
            }
        });

        // Status change warning
        editIsActive.addEventListener('change', function() {
            const newsCount = parseInt(editCurrentNewsCount.textContent);
            if (newsCount > 0 && !this.checked) {
                editNewsWarning.style.display = 'block';
            } else {
                editNewsWarning.style.display = 'none';
            }
        });

        // Form submission
        editForm.addEventListener('submit', function(e) {
            editSubmitBtn.disabled = true;
            editSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            editForm.classList.add('loading');
        });

        // Reset form when modal is closed
        editModal.addEventListener('hidden.bs.modal', function() {
            editForm.reset();
            editForm.classList.remove('loading');
            editSubmitBtn.disabled = false;
            editSubmitBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Update Category';
            editNewsWarning.style.display = 'none';
            // Remove validation classes
            editNameCount.parentElement.classList.remove('text-warning', 'text-danger');
            editDescriptionCount.parentElement.classList.remove('text-warning', 'text-danger');
        });
    }

    function generateSlug(text) {
        return text
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
</script>
@endpush