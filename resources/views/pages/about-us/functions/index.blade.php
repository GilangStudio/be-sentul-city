@extends('layouts.main')

@section('title', 'Functions Management')

@push('styles')
<style>
    .function-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .function-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .function-image {
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
        transform: rotate(2deg);
    }
    
    .function-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.7) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .function-card:hover .function-overlay {
        opacity: 1;
    }
    
    .function-actions {
        position: absolute;
        bottom: 10px;
        right: 10px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .function-card:hover .function-actions {
        opacity: 1;
    }
    
    .function-status {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 10;
    }
    
    .function-order {
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
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }
    
    .image-preview-container {
        max-height: 300px;
        overflow: hidden;
        border-radius: 8px;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Functions Management</h2>
        <div class="page-subtitle">Manage Sentul City function items for About Us page</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('about-us.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to About Us
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-function-modal">
            <i class="ti ti-plus me-1"></i> Add Function
        </button>
    </div>
</div>
@endsection

@section('content')

{{-- Search and Filter Form --}}
<div class="col-12">
    <form method="GET" action="{{ route('about-us.functions.index') }}" id="filter-form">
        <div class="d-flex justify-content-start align-items-center gap-2">
            <div class="input-icon" style="max-width: 350px;">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Search function title or description..." 
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
                <a href="{{ route('about-us.functions.index') }}" class="btn btn-outline-secondary" title="Clear all filters">
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

{{-- Functions Grid --}}
<div class="col-12">
    <div class="card">
        <div class="card-body p-0">
            @if($items->count() > 0)
            <div class="row g-3 p-3" id="sortable-container">
                @foreach($items as $item)
                <div class="col-sm-6 col-md-4 col-lg-3" data-id="{{ $item->id }}">
                    <div class="card function-card h-100 position-relative">
                        <div class="position-relative">
                            <img src="{{ $item->image_url }}" class="card-img-top function-image" alt="{{ $item->title }}">
                            
                            {{-- Order Number --}}
                            <div class="function-order">{{ $item->order }}</div>
                            
                            {{-- Status Badge --}}
                            <div class="function-status">
                                @if($item->is_active)
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
                            <div class="function-overlay"></div>
                            
                            {{-- Actions --}}
                            <div class="function-actions">
                                <div class="btn-list">
                                    <a href="{{ $item->image_url }}" target="_blank" class="btn btn-sm btn-warning" title="View Image">
                                        <i class="ti ti-external-link"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-primary edit-btn" 
                                            data-id="{{ $item->id }}"
                                            data-title="{{ $item->title }}"
                                            data-description="{{ $item->description }}"
                                            data-image-url="{{ $item->image_url }}"
                                            data-image-alt="{{ $item->image_alt_text }}"
                                            data-is-active="{{ $item->is_active ? '1' : '0' }}"
                                            title="Edit Function">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger delete-btn"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->title }}"
                                            data-url="{{ route('about-us.functions.destroy', $item) }}"
                                            title="Delete Function">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title mb-2" data-searchable="title">{{ $item->title }}</h5>
                            @if($item->description)
                            <p class="card-text text-secondary small" data-searchable="description">
                                {{ Str::limit($item->description, 80) }}
                            </p>
                            @endif
                        </div>
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
                    <i class="ti ti-tools icon icon-lg"></i>
                    @endif
                </div>
                <p class="empty-title h3">
                    @if(request('search') || request('status') || request('page'))
                    No functions found
                    @else
                    No function items yet
                    @endif
                </p>
                <p class="empty-subtitle text-secondary">
                    @if(request('search') || request('status') || request('page'))
                    Try adjusting your search terms or clear the filters to see all functions.
                    @else
                    Get started by creating your first function item.<br>
                    Showcase the various functions and services offered by Sentul City.
                    @endif
                </p>
                <div class="empty-action">
                    @if(request('search') || request('status') || request('page'))
                    <a href="{{ route('about-us.functions.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-x me-1"></i> Clear Filters
                    </a>
                    @else
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-function-modal">
                        <i class="ti ti-plus me-1"></i> Create First Function
                    </button>
                    @endif
                </div>
            </div>
            @endif
        </div>
        
        {{-- Footer with Results Info and Pagination --}}
        @if($items->total() > 0 || request('search') || request('status'))
        <div class="card-footer d-flex align-items-center">
            <div class="text-secondary">
                @if($items->total() > 0)
                    Showing <strong>{{ $items->firstItem() }}</strong> to <strong>{{ $items->lastItem() }}</strong> 
                    of <strong>{{ $items->total() }}</strong> results
                    @if(request('search'))
                        for "<strong>{{ request('search') }}</strong>"
                    @endif
                    @if($items->hasPages() && !request('search') && !request('status'))
                    <br><small class="text-warning">
                        <i class="ti ti-info-circle me-1"></i>
                        Drag & drop reordering works within current page.
                    </small>
                    @endif
                @else
                    No results found
                    @if(request('search') || request('status'))
                        with current filters
                    @endif
                @endif
            </div>
            
            @include('components.pagination', ['paginator' => $items])
        </div>
        @endif
    </div>
</div>

{{-- Create Function Modal --}}
<div class="modal modal-blur fade" id="create-function-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" action="{{ route('about-us.functions.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-tools me-2"></i>
                    Create Function Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Function Image <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="image" accept="image/*" required id="create-image-input">
                    <small class="form-hint">
                        <i class="ti ti-info-circle me-1"></i>
                        Recommended: 600x400px, Max: 5MB (JPG, PNG, WebP)
                    </small>
                    <div class="mt-3" id="create-image-preview"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" required
                           placeholder="e.g., Customer Service" id="create-title-input">
                    <small class="form-hint">
                        <span id="create-title-count">0</span>/255 characters
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4"
                              placeholder="Enter function description (optional)..." id="create-description-input"></textarea>
                    <small class="form-hint">
                        Describe what this function or service provides.
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image Alt Text</label>
                    <input type="text" class="form-control" name="image_alt_text"
                           placeholder="Enter image description for accessibility">
                </div>
                <div class="mb-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <span class="form-check-label">Active Status</span>
                    </label>
                    <small class="form-hint d-block">
                        <i class="ti ti-info-circle me-1"></i>
                        Only active functions will be displayed on the About Us page.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="create-submit-btn">
                    <i class="ti ti-device-floppy me-1"></i>Create Function
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Function Modal --}}
<div class="modal modal-blur fade" id="edit-function-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" enctype="multipart/form-data" id="edit-form">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>
                    Edit Function Item
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
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" required
                           placeholder="Enter function title" id="edit-title-input">
                    <small class="form-hint">
                        <span id="edit-title-count">0</span>/255 characters
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4"
                              placeholder="Enter function description (optional)..." id="edit-description-input"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image Alt Text</label>
                    <input type="text" class="form-control" name="image_alt_text" id="edit-image-alt-input">
                </div>
                <div class="mb-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit-is-active">
                        <span class="form-check-label">Active Status</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="edit-submit-btn">
                    <i class="ti ti-device-floppy me-1"></i>Update Function
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
        setupSearch();
        setupSortable();
        setupModals();
        setupCharacterCounters();
        setupImagePreviews();
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
        @if(!request('search') && !request('status') && $items->count() > 1)
        const sortableContainer = document.getElementById('sortable-container');
        if (sortableContainer) {
            new Sortable(sortableContainer, {
                handle: '.sortable-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function(evt) {
                    const orders = [];
                    // Get only direct children with data-id to avoid duplicates
                    const items = sortableContainer.children;
                    
                    // Get current page parameters to maintain context
                    const currentPage = {{ $items->currentPage() }};
                    const perPage = {{ $items->perPage() }};
                    
                    // Calculate offset for current page
                    const offset = (currentPage - 1) * perPage;
                    
                    Array.from(items).forEach((item, index) => {
                        const itemId = item.getAttribute('data-id');
                        if (itemId) {
                            const newOrder = offset + index + 1;
                            orders.push({
                                id: itemId,
                                order: newOrder
                            });
                        }
                    });
                    
                    updateOrder(orders);
                }
            });
        }
        @endif
    }

    function updateOrder(orders) {
        fetch('{{ route('about-us.functions.update-order') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                orders: orders,
                current_page: {{ $items->currentPage() }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Reorder all items to ensure no gaps in ordering
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

    function setupModals() {
        // Edit button handler
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
                const button = e.target.classList.contains('edit-btn') ? e.target : e.target.closest('.edit-btn');
                const itemId = button.getAttribute('data-id');
                
                document.getElementById('edit-form').action = `{{ url('about-us/functions') }}/${itemId}`;
                document.getElementById('edit-title-input').value = button.getAttribute('data-title');
                document.getElementById('edit-description-input').value = button.getAttribute('data-description');
                document.getElementById('edit-image-alt-input').value = button.getAttribute('data-image-alt');
                document.getElementById('edit-is-active').checked = button.getAttribute('data-is-active') === '1';
                document.getElementById('edit-current-image').src = button.getAttribute('data-image-url');
                
                // Update character counters
                updateCharacterCounter('edit-title-input', 'edit-title-count');
                
                const modal = new bootstrap.Modal(document.getElementById('edit-function-modal'));
                modal.show();
            }
        });

        // Form submission handlers
        const createForm = document.querySelector('#create-function-modal form');
        const editForm = document.getElementById('edit-form');
        const createSubmitBtn = document.getElementById('create-submit-btn');
        const editSubmitBtn = document.getElementById('edit-submit-btn');

        createForm.addEventListener('submit', function(e) {
            const imageInput = document.getElementById('create-image-input');
            if (!imageInput.files.length) {
                e.preventDefault();
                showAlert(imageInput, 'danger', 'Please select an image for the function.');
                return false;
            }

            createSubmitBtn.disabled = true;
            createSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
        });

        editForm.addEventListener('submit', function(e) {
            editSubmitBtn.disabled = true;
            editSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
        });

        // Reset modals when closed
        document.getElementById('create-function-modal').addEventListener('hidden.bs.modal', function() {
            createForm.reset();
            createSubmitBtn.disabled = false;
            createSubmitBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Create Function';
            document.getElementById('create-image-preview').innerHTML = '';
            document.getElementById('create-title-count').textContent = '0';
        });

        document.getElementById('edit-function-modal').addEventListener('hidden.bs.modal', function() {
            editForm.reset();
            editSubmitBtn.disabled = false;
            editSubmitBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Update Function';
            document.getElementById('edit-image-preview').innerHTML = '';
        });
    }

    function setupCharacterCounters() {
        setupCharacterCounter('create-title-input', 'create-title-count', 255);
        setupCharacterCounter('edit-title-input', 'edit-title-count', 255);
    }

    function setupCharacterCounter(inputId, countId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(countId);
        
        if (input && counter) {
            input.addEventListener('input', function() {
                updateCharacterCounter(inputId, countId);
            });
        }
    }

    function updateCharacterCounter(inputId, countId) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(countId);
        
        if (input && counter) {
            const currentLength = input.value.length;
            counter.textContent = currentLength;
            
            const percentage = (currentLength / 255) * 100;
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
    }

    function setupImagePreviews() {
        const createImageInput = document.getElementById('create-image-input');
        const editImageInput = document.getElementById('edit-image-input');
        
        if (createImageInput) {
            createImageInput.addEventListener('change', function(e) {
                handleImagePreview(e, 'create-image-preview', createImageInput, 5);
            });
        }
        
        if (editImageInput) {
            editImageInput.addEventListener('change', function(e) {
                handleImagePreview(e, 'edit-image-preview', editImageInput, 5);
            });
        }
    }

    function handleImagePreview(event, previewId, inputElement, maxSizeMB) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById(previewId);
        
        if (file) {
            const maxSize = maxSizeMB * 1024 * 1024;
            if (file.size > maxSize) {
                showAlert(inputElement, 'danger', `File size too large. Maximum ${maxSizeMB}MB allowed.`);
                inputElement.value = '';
                return;
            }

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
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearImagePreview('${previewId}', '${inputElement.id}')">
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
</script>
@endpush