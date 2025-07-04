@extends('layouts.main')

@section('title', 'Practical Info Categories')

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
    
    .form-control:focus {
        border-color: #0054a6;
        box-shadow: 0 0 0 0.2rem rgba(0, 84, 166, 0.25);
    }
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Practical Info Categories</h2>
        <div class="page-subtitle">Manage categories for practical information places</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('new-residents.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to New Residents
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-category-modal">
            <i class="ti ti-plus me-1"></i> Add Category
        </button>
    </div>
</div>
@endsection

@section('content')

{{-- Search and Filter Form --}}
<div class="col-12">
    <form method="GET" action="{{ route('new-residents.categories.index') }}" id="filter-form">
        <div class="d-flex justify-content-between align-items-center gap-2">
            <div class="input-icon" style="max-width: 350px;">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Search category name, title or description..." 
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
                <a href="{{ route('new-residents.categories.index') }}" class="btn btn-outline-secondary" title="Clear all filters">
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
            <div class="table-responsive">
                <table class="table" id="sortable-table">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">Order</th>
                            <th>Category Information</th>
                            <th width="150" class="text-center">Places Count</th>
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
                                    <div class="fw-bold text-dark mb-1">
                                        {{ $category->name }}
                                        {{-- <small class="text-secondary ms-2">({{ $category->slug }})</small> --}}
                                    </div>
                                    <div class="text-primary fw-medium mb-1">
                                        {{ $category->title }}
                                    </div>
                                    @if($category->description)
                                    <div class="text-secondary small mb-1" style="line-height: 1.4;">
                                        {{ Str::limit($category->description, 100) }}
                                    </div>
                                    @endif
                                    <small class="text-secondary">
                                        <i class="ti ti-calendar me-1"></i>{{ $category->created_at->format('d M Y') }}
                                    </small>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="text-dark fw-bold">{{ $category->places_count }}</div>
                                <small class="text-secondary">total places</small>
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
                                            data-title="{{ $category->title }}"
                                            data-description="{{ $category->description }}"
                                            data-is-active="{{ $category->is_active ? '1' : '0' }}"
                                            title="Edit Category">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn bg-danger-lt text-danger btn-icon delete-btn"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-url="{{ route('new-residents.categories.destroy', $category) }}"
                                            title="Delete Category"
                                            {{ $category->places_count > 0 ? 'disabled' : '' }}>
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
                                        @if(request('search') || request('status'))
                                        <i class="ti ti-search icon icon-lg"></i>
                                        @else
                                        <i class="ti ti-map-pins icon icon-lg"></i>
                                        @endif
                                    </div>
                                    <p class="empty-title h3">
                                        @if(request('search') || request('status'))
                                        No categories found
                                        @else
                                        No practical info categories yet
                                        @endif
                                    </p>
                                    <p class="empty-subtitle text-secondary">
                                        @if(request('search') || request('status'))
                                        Try adjusting your search terms or clear the filters to see all categories.
                                        @else
                                        Get started by creating your first practical info category.<br>
                                        Categories help organize places by type (e.g., Worship Places, Hotels, Restaurants).
                                        @endif
                                    </p>
                                    <div class="empty-action">
                                        @if(request('search') || request('status'))
                                        <a href="{{ route('new-residents.categories.index') }}" class="btn btn-outline-secondary">
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
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" id="create-category-form" method="POST" action="{{ route('new-residents.categories.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-map-pin-plus me-2"></i>
                    Create New Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required
                                   placeholder="e.g., Worship Places" id="create-name-input">
                            <small class="form-hint">
                                <span id="create-name-count">0</span>/255 characters. This will be shown in navigation.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Category Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required
                                   placeholder="e.g., Worship Places in Sentul City Area" id="create-title-input">
                            <small class="form-hint">
                                <span id="create-title-count">0</span>/255 characters. This will be the section title.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4" 
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
                        Only active categories will be available for places.
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
                                <small class="text-secondary">Navigation:</small>
                                <div id="create-preview-name" class="fw-bold">-</div>
                            </div>
                            <div class="col-6">
                                <small class="text-secondary">Section Title:</small>
                                <div id="create-preview-title" class="text-primary">-</div>
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
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required
                                   placeholder="e.g., Worship Places" id="edit-name-input">
                            <small class="form-hint">
                                <span id="edit-name-count">0</span>/255 characters
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Category Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required
                                   placeholder="e.g., Worship Places in Sentul City Area" id="edit-title-input">
                            <small class="form-hint">
                                <span id="edit-title-count">0</span>/255 characters
                            </small>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4" 
                              placeholder="Enter category description (optional)" id="edit-description-input"></textarea>
                    <small class="form-hint">
                        <span id="edit-description-count">0</span>/1000 characters
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit-is-active">
                        <span class="form-check-label">Active Status</span>
                    </label>
                    <small class="form-hint d-block">
                        <i class="ti ti-info-circle me-1"></i>
                        Only active categories will be available for places.
                    </small>
                    <div id="edit-places-warning" class="mt-2" style="display: none;">
                        <small class="text-warning">
                            <i class="ti ti-alert-triangle me-1"></i>
                            This category has <span id="edit-places-count">0</span> places.
                        </small>
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
        setupSearch();
        setupSortable();
        setupCreateModal();
        setupEditModal();
    });

    function setupSearch() {
        const filterForm = document.getElementById('filter-form');
        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('status-filter');
        
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterForm.submit();
            }, 600);
        });
        
        statusFilter.addEventListener('change', function() {
            filterForm.submit();
        });
        
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                filterForm.submit();
            }
        });
    }

    function setupSortable() {
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
                    
                    updateOrder(orders);
                }
            });
        }
        @endif
    }

    function updateOrder(orders) {
        fetch('{{ route('new-residents.categories.update-order') }}', {
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
                location.reload();
            }
        })
        .catch(error => {
            showToast('Failed to update order', 'error');
            location.reload();
        });
    }

    function setupCreateModal() {
        const createModal = document.getElementById('create-category-modal');
        const createForm = document.getElementById('create-category-form');
        const createNameInput = document.getElementById('create-name-input');
        const createTitleInput = document.getElementById('create-title-input');
        const createDescriptionInput = document.getElementById('create-description-input');
        const createSubmitBtn = document.getElementById('create-submit-btn');

        // Character counters and preview
        createNameInput.addEventListener('input', function() {
            updateCharacterCount(this, 'create-name-count', 255);
            document.getElementById('create-preview-name').textContent = this.value || '-';
        });

        createTitleInput.addEventListener('input', function() {
            updateCharacterCount(this, 'create-title-count', 255);
            document.getElementById('create-preview-title').textContent = this.value || '-';
        });

        createDescriptionInput.addEventListener('input', function() {
            updateCharacterCount(this, 'create-description-count', 1000);
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
            document.getElementById('create-name-count').textContent = '0';
            document.getElementById('create-title-count').textContent = '0';
            document.getElementById('create-description-count').textContent = '0';
            document.getElementById('create-preview-name').textContent = '-';
            document.getElementById('create-preview-title').textContent = '-';
        });
    }

    function setupEditModal() {
        const editModal = document.getElementById('edit-category-modal');
        const editForm = document.getElementById('edit-category-form');
        const editNameInput = document.getElementById('edit-name-input');
        const editTitleInput = document.getElementById('edit-title-input');
        const editDescriptionInput = document.getElementById('edit-description-input');
        const editIsActive = document.getElementById('edit-is-active');
        const editSubmitBtn = document.getElementById('edit-submit-btn');

        // Handle edit button clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
                const button = e.target.classList.contains('edit-btn') ? e.target : e.target.closest('.edit-btn');
                const categoryId = button.getAttribute('data-id');
                const categoryName = button.getAttribute('data-name');
                const categoryTitle = button.getAttribute('data-title');
                const categoryDescription = button.getAttribute('data-description') || '';
                const isActive = button.getAttribute('data-is-active') === '1';
                
                // Populate edit form
                editForm.action = `{{ route('new-residents.categories.index') }}/${categoryId}`;
                editNameInput.value = categoryName;
                editTitleInput.value = categoryTitle;
                editDescriptionInput.value = categoryDescription;
                editIsActive.checked = isActive;
                
                // Update counters
                updateCharacterCount(editNameInput, 'edit-name-count', 255);
                updateCharacterCount(editTitleInput, 'edit-title-count', 255);
                updateCharacterCount(editDescriptionInput, 'edit-description-count', 1000);
                
                // Show modal
                const modal = new bootstrap.Modal(editModal);
                modal.show();
            }
        });

        // Character counters
        editNameInput.addEventListener('input', function() {
            updateCharacterCount(this, 'edit-name-count', 255);
        });

        editTitleInput.addEventListener('input', function() {
            updateCharacterCount(this, 'edit-title-count', 255);
        });

        editDescriptionInput.addEventListener('input', function() {
            updateCharacterCount(this, 'edit-description-count', 1000);
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
        });
    }

    function updateCharacterCount(input, countId, maxLength) {
        const currentLength = input.value.length;
        const counter = document.getElementById(countId);
        counter.textContent = currentLength;
        
        const percentage = (currentLength / maxLength) * 100;
        const parent = counter.parentElement;
        
        if (percentage > 90) {
            parent.classList.add('text-danger');
            parent.classList.remove('text-warning');
        } else if (percentage > 80) {
            parent.classList.add('text-warning');
            parent.classList.remove('text-danger');
        } else {
            parent.classList.remove('text-warning', 'text-danger');
        }
    }
</script>
@endpush