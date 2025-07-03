@extends('layouts.main')

@section('title', 'Promo Management')

@push('styles')
<style>
    .promo-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .promo-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .promo-image {
        height: 200px;
        object-fit: cover;
        background-color: #f8f9fa;
    }
    
    .sortable-handle {
        cursor: grab;
        color: #6c757d;
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255,255,255,0.9);
        border-radius: 4px;
        padding: 4px;
        z-index: 10;
    }
    
    .sortable-handle:hover {
        color: var(--bs-primary);
        background: rgba(255,255,255,1);
    }
    
    .sortable-ghost {
        opacity: 0.5;
    }
    
    .sortable-chosen {
        transform: rotate(5deg);
    }
    
    .promo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.7) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .promo-card:hover .promo-overlay {
        opacity: 1;
    }
    
    .promo-actions {
        position: absolute;
        bottom: 10px;
        right: 10px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .promo-card:hover .promo-actions {
        opacity: 1;
    }
    
    .promo-status {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 10;
    }
    
    .promo-order {
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 10;
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
    
    .image-preview-container {
        max-height: 300px;
        overflow: hidden;
        border-radius: 8px;
    }
    
    /* Loading state */
    .loading-overlay {
        opacity: 0.6;
        pointer-events: none;
    }
    
    /* Filter indicators */
    .filter-badge {
        font-size: 0.75rem;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">Promo Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-promo-modal">
        <i class="ti ti-plus me-1"></i> Add Promo
    </button>
</div>
@endsection

@section('content')

{{-- Search and Filter Form --}}
<div class="col-12">
    <form method="GET" action="{{ route('promos.index') }}" id="filter-form">
        <div class="d-flex justify-content-between align-items-center gap-2">
            <div class="input-icon" style="max-width: 350px;">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Search promo title or description..." 
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
                <a href="{{ route('promos.index') }}" class="btn btn-outline-secondary" title="Clear all filters">
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
            <span class="badge bg-blue-lt filter-badge">
                <i class="ti ti-search me-1"></i>
                Search: "{{ request('search') }}"
            </span>
            @endif
            @if(request('status'))
            <span class="badge bg-green-lt filter-badge">
                <i class="ti ti-filter me-1"></i>
                Status: {{ request('status') === 'active' ? 'Active' : 'Inactive' }}
            </span>
            @endif
        </div>
        @endif
    </form>
</div>

{{-- Promos Grid --}}
<div class="col-12">
    <div class="card">
        <div class="card-body p-0">
            @if($promos->count() > 0)
            <div class="row g-3 p-3" id="sortable-container">
                @foreach($promos as $promo)
                <div class="col-sm-6 col-md-4 col-lg-3" data-id="{{ $promo->id }}">
                    <div class="card promo-card h-100 position-relative">
                        <div class="position-relative">
                            <img src="{{ $promo->image_url }}" class="card-img-top promo-image" alt="{{ $promo->title_display }}">
                            
                            {{-- Order Number --}}
                            <div class="promo-order">{{ $promo->order }}</div>
                            
                            {{-- Status Badge --}}
                            <div class="promo-status">
                                @if($promo->is_active)
                                <span class="badge bg-success text-white">Active</span>
                                @else
                                <span class="badge bg-secondary text-white">Inactive</span>
                                @endif
                            </div>
                            
                            {{-- Drag Handle --}}
                            @if(!request('search') && !request('status'))
                            <div class="sortable-handle" title="Drag to reorder">
                                <i class="ti ti-grip-vertical"></i>
                            </div>
                            @endif
                            
                            {{-- Overlay --}}
                            <div class="promo-overlay"></div>
                            
                            {{-- Actions --}}
                            <div class="promo-actions">
                                <div class="btn-list">
                                    <a href="{{ $promo->image_url }}" target="_blank" class="btn btn-sm btn-warning" title="View Promo">
                                        <i class="ti ti-external-link"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-primary edit-btn" 
                                            data-id="{{ $promo->id }}"
                                            data-title="{{ $promo->title }}"
                                            data-description="{{ $promo->description }}"
                                            data-image-url="{{ $promo->image_url }}"
                                            data-is-active="{{ $promo->is_active ? '1' : '0' }}"
                                            title="Edit Promo">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger delete-btn"
                                            data-id="{{ $promo->id }}"
                                            data-name="{{ $promo->title_display }}"
                                            data-url="{{ route('promos.destroy', $promo) }}"
                                            title="Delete Promo">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        @if($promo->title || $promo->description)
                        <div class="card-body">
                            @if($promo->title)
                            <h5 class="card-title mb-2" data-searchable="title">{{ $promo->title }}</h5>
                            @endif
                            @if($promo->description)
                            <p class="card-text text-secondary small" data-searchable="description">
                                {{ Str::limit($promo->description, 80) }}
                            </p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty py-5">
                <div class="empty-icon">
                    @if(request('search') || request('status') || request('page'))
                    <i class="ti ti-search icon icon-lg"></i>
                    @else
                    <i class="ti ti-photo icon icon-lg"></i>
                    @endif
                </div>
                <p class="empty-title h3">
                    @if(request('search') || request('status') || request('page'))
                    No promos found
                    @else
                    No promos yet
                    @endif
                </p>
                <p class="empty-subtitle text-secondary">
                    @if(request('search') || request('status') || request('page'))
                    Try adjusting your search terms or clear the filters to see all promos.
                    @else
                    Get started by creating your first promo.<br>
                    Showcase your offers, events, and special promotions.
                    @endif
                </p>
                <div class="empty-action">
                    @if(request('search') || request('status') || request('page'))
                    <a href="{{ route('promos.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-x me-1"></i> Clear Filters
                    </a>
                    @else
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-promo-modal">
                        <i class="ti ti-plus me-1"></i> Create First Promo
                    </button>
                    @endif
                </div>
            </div>
            @endif
        </div>
        
        {{-- Footer with Results Info and Pagination --}}
        @if($promos->total() > 0 || request('search') || request('status'))
        <div class="card-footer d-flex align-items-center">
            <div class="text-secondary">
                @if($promos->total() > 0)
                    Showing <strong>{{ $promos->firstItem() }}</strong> to <strong>{{ $promos->lastItem() }}</strong> 
                    of <strong>{{ $promos->total() }}</strong> results
                    @if(request('search'))
                        for "<strong>{{ request('search') }}</strong>"
                    @endif
                    @if($promos->hasPages() && !request('search') && !request('status'))
                    <br><small class="text-warning">
                        <i class="ti ti-info-circle me-1"></i>
                        Drag & drop reordering works within current page. Use pagination to reorder across pages.
                    </small>
                    @endif
                @else
                    No results found
                    @if(request('search') || request('status'))
                        with current filters
                    @endif
                @endif
            </div>
            
            @include('components.pagination', ['paginator' => $promos])
        </div>
        @endif
    </div>
</div>

{{-- Create Promo Modal --}}
<div class="modal modal-blur fade" id="create-promo-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" id="create-promo-form" method="POST" action="{{ route('promos.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-photo-plus me-2"></i>
                    Create New Promo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Promo Image <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="image" accept="image/*" required id="create-image-input">
                    <small class="form-hint">
                        <i class="ti ti-info-circle me-1"></i>
                        Recommended: 1200x800px, Max: 5MB (JPG, PNG, WebP)
                    </small>
                    <div class="mt-3" id="create-image-preview"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Title (Optional)</label>
                    <input type="text" class="form-control" name="title" 
                           placeholder="Enter promo title" id="create-title-input">
                    <small class="form-hint">
                        <span id="create-title-count">0</span>/255 characters
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description (Optional)</label>
                    <textarea class="form-control" name="description" rows="4" 
                              placeholder="Enter promo description" id="create-description-input"></textarea>
                    <small class="form-hint">
                        <span id="create-description-count">0</span>/1000 characters
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <span class="form-check-label">Active Status</span>
                    </label>
                    <small class="form-hint d-block">
                        <i class="ti ti-info-circle me-1"></i>
                        Only active promos will be displayed publicly.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="create-submit-btn">
                    <i class="ti ti-device-floppy me-1"></i>
                    Create Promo
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Promo Modal --}}
<div class="modal modal-blur fade" id="edit-promo-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" id="edit-promo-form" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>
                    Edit Promo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3" id="edit-current-image-section">
                    <label class="form-label">Current Image</label>
                    <div class="image-preview-container">
                        <img id="edit-current-image" src="" class="img-fluid rounded">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Update Image (Optional)</label>
                    <input type="file" class="form-control" name="image" accept="image/*" id="edit-image-input">
                    <small class="form-hint">
                        <i class="ti ti-info-circle me-1"></i>
                        Leave empty to keep current image. Max: 5MB (JPG, PNG, WebP)
                    </small>
                    <div class="mt-3" id="edit-image-preview"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Title (Optional)</label>
                    <input type="text" class="form-control" name="title" 
                           placeholder="Enter promo title" id="edit-title-input">
                    <small class="form-hint">
                        <span id="edit-title-count">0</span>/255 characters
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description (Optional)</label>
                    <textarea class="form-control" name="description" rows="4" 
                              placeholder="Enter promo description" id="edit-description-input"></textarea>
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
                        Only active promos will be displayed publicly.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="edit-submit-btn">
                    <i class="ti ti-device-floppy me-1"></i>
                    Update Promo
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
@include('components.alert')

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
        // Initialize all functionality
        setupSearch();
        setupSortable();
        setupCreateModal();
        setupEditModal();
        
        // Highlight search terms in results
        const searchTerm = '{{ request('search') }}';
        if (searchTerm) {
            highlightSearchResults(searchTerm.toLowerCase());
        }
    });

    function setupSearch() {
        // Get form and input elements
        const filterForm = document.getElementById('filter-form');
        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('status-filter');
        
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
        
        // Submit filter function
        function submitFilter() {
            filterForm.submit();
        }
        
        // Focus search input on Ctrl+K or Cmd+K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });
    }

    function setupSortable() {
        // Initialize Sortable for drag and drop ordering
        @if(!request('search') && !request('status') && $promos->count() > 1)
        const sortableContainer = document.getElementById('sortable-container');
        if (sortableContainer) {
            new Sortable(sortableContainer, {
                handle: '.sortable-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function(evt) {
                    const orders = [];
                    const items = sortableContainer.querySelectorAll('[data-id]');
                    
                    // Get current page info
                    const urlParams = new URLSearchParams(window.location.search);
                    const currentPage = parseInt(urlParams.get('page') || 1);
                    const perPage = 12;
                    const offset = (currentPage - 1) * perPage;
                    
                    items.forEach((item, index) => {
                        orders.push({
                            id: item.dataset.id,
                            order: offset + index + 1 // Calculate global order position
                        });
                    });
                    
                    console.log('Reordering items:', orders);
                    console.log('Current page:', currentPage, 'Offset:', offset);
                    
                    // Update order via AJAX
                    updateOrder(orders);
                }
            });
        }
        @endif
    }

    function updateOrder(orders) {
        // Get current page from URL or pagination
        const urlParams = new URLSearchParams(window.location.search);
        const currentPage = urlParams.get('page') || 1;
        
        fetch('{{ route('promos.update-order') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                orders: orders,
                current_page: currentPage
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Reload page to show updated ordering across all pages
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
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
        const createModal = document.getElementById('create-promo-modal');
        const createForm = document.getElementById('create-promo-form');
        const createImageInput = document.getElementById('create-image-input');
        const createImagePreview = document.getElementById('create-image-preview');
        const createTitleInput = document.getElementById('create-title-input');
        const createDescriptionInput = document.getElementById('create-description-input');
        const createTitleCount = document.getElementById('create-title-count');
        const createDescriptionCount = document.getElementById('create-description-count');
        const createSubmitBtn = document.getElementById('create-submit-btn');

        // Character counters
        createTitleInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            createTitleCount.textContent = currentLength;
            
            const percentage = (currentLength / 255) * 100;
            if (percentage > 90) {
                createTitleCount.parentElement.classList.add('text-danger');
                createTitleCount.parentElement.classList.remove('text-warning');
            } else if (percentage > 80) {
                createTitleCount.parentElement.classList.add('text-warning');
                createTitleCount.parentElement.classList.remove('text-danger');
            } else {
                createTitleCount.parentElement.classList.remove('text-warning', 'text-danger');
            }
        });

        createDescriptionInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            createDescriptionCount.textContent = currentLength;
            
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

        // Image preview
        createImageInput.addEventListener('change', function(e) {
            handleImagePreview(e, createImagePreview, createImageInput);
        });

        // Form submission
        createForm.addEventListener('submit', function(e) {
            // Validate image
            if (!createImageInput.files.length) {
                e.preventDefault();
                showAlert(createImageInput, 'danger', 'Please select an image for the promo.');
                return false;
            }

            createSubmitBtn.disabled = true;
            createSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
            createForm.classList.add('loading');
        });

        // Reset form when modal is closed
        createModal.addEventListener('hidden.bs.modal', function() {
            createForm.reset();
            createForm.classList.remove('loading');
            createSubmitBtn.disabled = false;
            createSubmitBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Create Promo';
            createImagePreview.innerHTML = '';
            createTitleCount.textContent = '0';
            createDescriptionCount.textContent = '0';
            // Remove validation classes
            createTitleCount.parentElement.classList.remove('text-warning', 'text-danger');
            createDescriptionCount.parentElement.classList.remove('text-warning', 'text-danger');
            // Remove alerts
            const alerts = document.querySelectorAll('.alert-custom');
            alerts.forEach(alert => alert.remove());
        });
    }

    function setupEditModal() {
        const editModal = document.getElementById('edit-promo-modal');
        const editForm = document.getElementById('edit-promo-form');
        const editImageInput = document.getElementById('edit-image-input');
        const editImagePreview = document.getElementById('edit-image-preview');
        const editCurrentImage = document.getElementById('edit-current-image');
        const editTitleInput = document.getElementById('edit-title-input');
        const editDescriptionInput = document.getElementById('edit-description-input');
        const editIsActive = document.getElementById('edit-is-active');
        const editTitleCount = document.getElementById('edit-title-count');
        const editDescriptionCount = document.getElementById('edit-description-count');
        const editLastUpdated = document.getElementById('edit-last-updated');
        const editSubmitBtn = document.getElementById('edit-submit-btn');

        // Handle edit button clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
                const button = e.target.classList.contains('edit-btn') ? e.target : e.target.closest('.edit-btn');
                const promoId = button.getAttribute('data-id');
                const promoTitle = button.getAttribute('data-title') || '';
                const promoDescription = button.getAttribute('data-description') || '';
                const promoImageUrl = button.getAttribute('data-image-url');
                const isActive = button.getAttribute('data-is-active') === '1';
                
                // Get additional data from card
                const card = button.closest('[data-id]');
                
                // Populate edit form
                editForm.action = `{{ url('promos') }}/${promoId}`;
                editTitleInput.value = promoTitle;
                editDescriptionInput.value = promoDescription;
                editIsActive.checked = isActive;
                editCurrentImage.src = promoImageUrl;
                
                // Update counters
                editTitleCount.textContent = promoTitle.length;
                editDescriptionCount.textContent = promoDescription.length;
                
                // Show modal
                const modal = new bootstrap.Modal(editModal);
                modal.show();
            }
        });

        // Character counters
        editTitleInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            editTitleCount.textContent = currentLength;
            
            const percentage = (currentLength / 255) * 100;
            if (percentage > 90) {
                editTitleCount.parentElement.classList.add('text-danger');
                editTitleCount.parentElement.classList.remove('text-warning');
            } else if (percentage > 80) {
                editTitleCount.parentElement.classList.add('text-warning');
                editTitleCount.parentElement.classList.remove('text-danger');
            } else {
                editTitleCount.parentElement.classList.remove('text-warning', 'text-danger');
            }
        });

        editDescriptionInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            editDescriptionCount.textContent = currentLength;
            
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

        // Image preview
        editImageInput.addEventListener('change', function(e) {
            handleImagePreview(e, editImagePreview, editImageInput);
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
            editSubmitBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Update Promo';
            editImagePreview.innerHTML = '';
            // Remove validation classes
            editTitleCount.parentElement.classList.remove('text-warning', 'text-danger');
            editDescriptionCount.parentElement.classList.remove('text-warning', 'text-danger');
        });
    }

    function handleImagePreview(event, previewContainer, inputElement) {
        const file = event.target.files[0];
        if (file) {
            // Validate file size
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                showAlert(inputElement, 'danger', 'File size too large. Maximum 5MB allowed.');
                inputElement.value = '';
                return;
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                showAlert(inputElement, 'danger', 'Please select a valid image file.');
                inputElement.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title mb-1">${file.name}</h6>
                                    <small class="text-secondary">
                                        ${(file.size / 1024 / 1024).toFixed(2)} MB
                                    </small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearImagePreview('${previewContainer.id}', '${inputElement.id}')">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <small class="text-success">
                                    <i class="ti ti-check me-1"></i>
                                    Ready to upload
                                </small>
                            </div>
                        </div>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.innerHTML = '';
        }
    }

    // Clear image preview function
    window.clearImagePreview = function(previewId, inputId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewId).innerHTML = '';
    };

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

    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
</script>
@endpush