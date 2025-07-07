@extends('layouts.main')

@section('title', 'Transportation Management')

@push('styles')
<style>
    .table tbody tr:hover {
        background-color: #f8f9fa;
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
        <h2 class="page-title">Transportation Management</h2>
        <div class="page-subtitle">Manage public transportation information</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('new-residents.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to New Residents
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-transportation-modal">
            <i class="ti ti-plus me-1"></i> Add Transportation
        </button>
    </div>
</div>
@endsection

@section('content')

{{-- Search and Filter Form --}}
<div class="col-12">
    <form method="GET" action="{{ route('new-residents.transportation.index') }}" id="filter-form">
        <div class="d-flex justify-content-start align-items-center gap-2">
            <div class="input-icon" style="max-width: 350px;">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Search transportation title or description..." 
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
                <a href="{{ route('new-residents.transportation.index') }}" class="btn btn-outline-secondary" title="Clear all filters">
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

{{-- Transportation Items Table --}}
<div class="col-12">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">Order</th>
                            <th>Transportation Information</th>
                            <th width="120" class="text-center">Status</th>
                            <th width="120" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-tbody">
                        @forelse($transportationItems as $item)
                        <tr data-id="{{ $item->id }}">
                            <td class="text-center">
                                <div class="d-flex align-items-center h-100">
                                    <span class="sortable-handle">
                                        <i class="ti ti-grip-vertical"></i>
                                    </span>
                                    <small class="text-secondary d-block">{{ $item->order }}</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold text-dark mb-1">
                                        {{ $item->title }}
                                    </div>
                                    <div class="text-secondary mb-1" style="line-height: 1.4;">
                                        {{ $item->description }}
                                    </div>
                                    <small class="text-secondary">
                                        <i class="ti ti-calendar me-1"></i>{{ $item->created_at->format('d M Y') }}
                                    </small>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($item->is_active)
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
                                            data-id="{{ $item->id }}"
                                            data-title="{{ $item->title }}"
                                            data-description="{{ $item->description }}"
                                            data-is-active="{{ $item->is_active ? '1' : '0' }}"
                                            title="Edit Transportation">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn bg-danger-lt text-danger btn-icon delete-btn"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->title }}"
                                            data-url="{{ route('new-residents.transportation.destroy', $item) }}"
                                            title="Delete Transportation">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        @if(request('search') || request('status'))
                                        <i class="ti ti-search icon icon-lg"></i>
                                        @else
                                        <i class="ti ti-car icon icon-lg"></i>
                                        @endif
                                    </div>
                                    <p class="empty-title h3">
                                        @if(request('search') || request('status'))
                                        No transportation items found
                                        @else
                                        No transportation items yet
                                        @endif
                                    </p>
                                    <p class="empty-subtitle text-secondary">
                                        @if(request('search') || request('status'))
                                        Try adjusting your search terms or clear the filters to see all items.
                                        @else
                                        Get started by creating your first transportation item.<br>
                                        Add information about public transportation options available for residents.
                                        @endif
                                    </p>
                                    <div class="empty-action">
                                        @if(request('search') || request('status'))
                                        <a href="{{ route('new-residents.transportation.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-x me-1"></i> Clear Filters
                                        </a>
                                        @else
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-transportation-modal">
                                            <i class="ti ti-plus me-1"></i> Create First Item
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
        @if($transportationItems->total() > 0 || request('search') || request('status'))
        <div class="card-footer d-flex align-items-center">
            <div class="text-secondary">
                @if($transportationItems->total() > 0)
                    Showing <strong>{{ $transportationItems->firstItem() }}</strong> to <strong>{{ $transportationItems->lastItem() }}</strong> 
                    of <strong>{{ $transportationItems->total() }}</strong> results
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
            
            @include('components.pagination', ['paginator' => $transportationItems])
        </div>
        @endif
    </div>
</div>

{{-- Create Transportation Modal --}}
<div class="modal modal-blur fade" id="create-transportation-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" id="create-transportation-form" method="POST" action="{{ route('new-residents.transportation.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-car-plus me-2"></i>
                    Add Transportation Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Transportation Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" required
                           placeholder="e.g., Sinar Jaya" id="create-title-input">
                    <small class="form-hint">
                        <span id="create-title-count">0</span>/255 characters. Transportation name or service.
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="description" rows="4" required
                              placeholder="e.g., Arah Rawamangun, Grogol, Blok M" id="create-description-input"></textarea>
                    <small class="form-hint">
                        <span id="create-description-count">0</span>/1000 characters. Route information or service details.
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <span class="form-check-label">Active Status</span>
                    </label>
                    <small class="form-hint d-block">
                        <i class="ti ti-info-circle me-1"></i>
                        Only active transportation items will be displayed publicly.
                    </small>
                </div>
                
                {{-- Preview Section --}}
                {{-- <div class="card bg-light">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ti ti-eye me-1"></i>
                            Preview
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="ti ti-car icon text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-bold" id="create-preview-title">Transportation Title</div>
                                <div class="text-secondary" id="create-preview-description">Transportation description will appear here</div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="create-submit-btn">
                    <i class="ti ti-device-floppy me-1"></i>
                    Create Item
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Transportation Modal --}}
<div class="modal modal-blur fade" id="edit-transportation-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" id="edit-transportation-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>
                    Edit Transportation Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Transportation Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" required
                           placeholder="e.g., Sinar Jaya" id="edit-title-input">
                    <small class="form-hint">
                        <span id="edit-title-count">0</span>/255 characters
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="description" rows="4" required
                              placeholder="e.g., Arah Rawamangun, Grogol, Blok M" id="edit-description-input"></textarea>
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
                        Only active transportation items will be displayed publicly.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="edit-submit-btn">
                    <i class="ti ti-device-floppy me-1"></i>
                    Update Item
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
        @if(!request('search') && !request('status') && $transportationItems->count() > 1)
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
        fetch('{{ route('new-residents.transportation.update-order') }}', {
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
        const createModal = document.getElementById('create-transportation-modal');
        const createForm = document.getElementById('create-transportation-form');
        const createTitleInput = document.getElementById('create-title-input');
        const createDescriptionInput = document.getElementById('create-description-input');
        const createSubmitBtn = document.getElementById('create-submit-btn');

        // Character counters and preview
        createTitleInput.addEventListener('input', function() {
            updateCharacterCount(this, 'create-title-count', 255);
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
            createSubmitBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Create Item';
            document.getElementById('create-title-count').textContent = '0';
            document.getElementById('create-description-count').textContent = '0';
        });
    }

    function setupEditModal() {
        const editModal = document.getElementById('edit-transportation-modal');
        const editForm = document.getElementById('edit-transportation-form');
        const editTitleInput = document.getElementById('edit-title-input');
        const editDescriptionInput = document.getElementById('edit-description-input');
        const editIsActive = document.getElementById('edit-is-active');
        const editSubmitBtn = document.getElementById('edit-submit-btn');

        // Handle edit button clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
                const button = e.target.classList.contains('edit-btn') ? e.target : e.target.closest('.edit-btn');
                const itemId = button.getAttribute('data-id');
                const itemTitle = button.getAttribute('data-title');
                const itemDescription = button.getAttribute('data-description');
                const isActive = button.getAttribute('data-is-active') === '1';
                
                // Populate edit form
                editForm.action = `{{ route('new-residents.transportation.index') }}/${itemId}`;
                editTitleInput.value = itemTitle;
                editDescriptionInput.value = itemDescription;
                editIsActive.checked = isActive;
                
                // Update counters
                updateCharacterCount(editTitleInput, 'edit-title-count', 255);
                updateCharacterCount(editDescriptionInput, 'edit-description-count', 1000);
                
                // Show modal
                const modal = new bootstrap.Modal(editModal);
                modal.show();
            }
        });

        // Character counters
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
            editSubmitBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Update Item';
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