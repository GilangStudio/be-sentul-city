@extends('layouts.main')

@section('title', 'Services Management')

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
    
    .service-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    
    .form-control:focus {
        border-color: #0054a6;
        box-shadow: 0 0 0 0.2rem rgba(0, 84, 166, 0.25);
    }
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }
    
    .icon-preview {
        padding: 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        min-height: 40px;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Services Management</h2>
        <div class="page-subtitle">Manage complete service items for About Us page</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('about-us.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to About Us
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-service-modal">
            <i class="ti ti-plus me-1"></i> Add Service
        </button>
    </div>
</div>
@endsection

@section('content')

{{-- Search and Filter Form --}}
<div class="col-12">
    <form method="GET" action="{{ route('about-us.services.index') }}" id="filter-form">
        <div class="d-flex justify-content-between align-items-center gap-2">
            <div class="input-icon" style="max-width: 350px;">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Search service title or description..." 
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
                <a href="{{ route('about-us.services.index') }}" class="btn btn-outline-secondary" title="Clear all filters">
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

{{-- Services Table --}}
<div class="col-12">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">Order</th>
                            <th width="80" class="text-center">Icon</th>
                            <th>Service Information</th>
                            <th width="120" class="text-center">Status</th>
                            <th width="120" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-tbody">
                        @forelse($items as $item)
                        <tr data-id="{{ $item->id }}">
                            <td class="text-center">
                                <div class="d-flex align-items-center h-100">
                                    <span class="sortable-handle">
                                        <i class="ti ti-grip-vertical"></i>
                                    </span>
                                    <small class="text-secondary d-block">{{ $item->order }}</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="service-icon bg-{{ $item->icon_color }}-lt">
                                    <i class="{{ $item->icon_class }} icon text-{{ $item->icon_color }}"></i>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold text-dark mb-1" data-searchable="title">
                                        {{ $item->title }}
                                    </div>
                                    @if($item->description)
                                    <div class="text-secondary mb-1" style="line-height: 1.4;" data-searchable="description">
                                        {{ Str::limit($item->description, 120) }}
                                    </div>
                                    @endif
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
                                            data-icon-class="{{ $item->icon_class }}"
                                            data-icon-color="{{ $item->icon_color }}"
                                            data-is-active="{{ $item->is_active ? '1' : '0' }}"
                                            title="Edit Service">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn bg-danger-lt text-danger btn-icon delete-btn"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->title }}"
                                            data-url="{{ route('about-us.services.destroy', $item) }}"
                                            title="Delete Service">
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
                                        <i class="ti ti-heart-handshake icon icon-lg"></i>
                                        @endif
                                    </div>
                                    <p class="empty-title h3">
                                        @if(request('search') || request('status') || request('page'))
                                        No services found
                                        @else
                                        No service items yet
                                        @endif
                                    </p>
                                    <p class="empty-subtitle text-secondary">
                                        @if(request('search') || request('status') || request('page'))
                                        Try adjusting your search terms or clear the filters to see all services.
                                        @else
                                        Get started by creating your first service item.<br>
                                        These items highlight the complete services offered by Sentul City.
                                        @endif
                                    </p>
                                    <div class="empty-action">
                                        @if(request('search') || request('status') || request('page'))
                                        <a href="{{ route('about-us.services.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-x me-1"></i> Clear Filters
                                        </a>
                                        @else
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-service-modal">
                                            <i class="ti ti-plus me-1"></i> Create First Service
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
        @if($items->total() > 0 || request('search') || request('status'))
        <div class="card-footer d-flex align-items-center">
            <div class="text-secondary">
                @if($items->total() > 0)
                    Showing <strong>{{ $items->firstItem() }}</strong> to <strong>{{ $items->lastItem() }}</strong> 
                    of <strong>{{ $items->total() }}</strong> results
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
            
            @include('components.pagination', ['paginator' => $items])
        </div>
        @endif
    </div>
</div>

{{-- Create Service Modal --}}
<div class="modal modal-blur fade" id="create-service-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" action="{{ route('about-us.services.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-heart-handshake me-2"></i>
                    Create Service Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" required
                           placeholder="e.g., Home Care Unit" id="create-title-input">
                    <small class="form-hint">
                        <span id="create-title-count">0</span>/255 characters
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"
                              placeholder="Enter service description (optional)..." id="create-description-input"></textarea>
                    <small class="form-hint">
                        Describe what this service provides.
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Icon Class <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="icon_class" required
                           placeholder="ti ti-home" id="create-icon-input">
                    <small class="form-hint">
                        Use Tabler Icons class. Examples: ti ti-home, ti ti-phone, ti ti-shield, ti ti-building
                        <a href="https://tabler.io/icons" target="_blank" class="text-primary">Browse icons</a>
                    </small>
                    <div class="mt-2" id="create-icon-preview"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Icon Color <span class="text-danger">*</span></label>
                    <select class="form-select" name="icon_color" required id="create-color-select">
                        <option value="primary" selected>Primary (Blue)</option>
                        <option value="secondary">Secondary (Gray)</option>
                        <option value="success">Success (Green)</option>
                        <option value="danger">Danger (Red)</option>
                        <option value="warning">Warning (Yellow)</option>
                        <option value="info">Info (Cyan)</option>
                        <option value="dark">Dark (Black)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <span class="form-check-label">Active Status</span>
                    </label>
                    <small class="form-hint d-block">
                        <i class="ti ti-info-circle me-1"></i>
                        Only active services will be displayed on the About Us page.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="create-submit-btn">
                    <i class="ti ti-device-floppy me-1"></i>Create Service
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Service Modal --}}
<div class="modal modal-blur fade" id="edit-service-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" id="edit-form">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>
                    Edit Service Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" required id="edit-title-input">
                    <small class="form-hint">
                        <span id="edit-title-count">0</span>/255 characters
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3" id="edit-description-input"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Icon Class <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="icon_class" required id="edit-icon-input">
                    <small class="form-hint">
                        Use Tabler Icons class. 
                        <a href="https://tabler.io/icons" target="_blank" class="text-primary">Browse icons</a>
                    </small>
                    <div class="mt-2" id="edit-icon-preview"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Icon Color <span class="text-danger">*</span></label>
                    <select class="form-select" name="icon_color" required id="edit-color-select">
                        <option value="primary">Primary (Blue)</option>
                        <option value="secondary">Secondary (Gray)</option>
                        <option value="success">Success (Green)</option>
                        <option value="danger">Danger (Red)</option>
                        <option value="warning">Warning (Yellow)</option>
                        <option value="info">Info (Cyan)</option>
                        <option value="dark">Dark (Black)</option>
                    </select>
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
                    <i class="ti ti-device-floppy me-1"></i>Update Service
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
        setupIconPreviews();
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
        fetch('{{ route('about-us.services.update-order') }}', {
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

    function setupModals() {
        // Edit button handler
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
                const button = e.target.classList.contains('edit-btn') ? e.target : e.target.closest('.edit-btn');
                const itemId = button.getAttribute('data-id');
                
                document.getElementById('edit-form').action = `{{ url('about-us/services') }}/${itemId}`;
                document.getElementById('edit-title-input').value = button.getAttribute('data-title');
                document.getElementById('edit-description-input').value = button.getAttribute('data-description');
                document.getElementById('edit-icon-input').value = button.getAttribute('data-icon-class');
                document.getElementById('edit-color-select').value = button.getAttribute('data-icon-color');
                document.getElementById('edit-is-active').checked = button.getAttribute('data-is-active') === '1';
                
                // Update character counters and icon preview
                updateCharacterCounter('edit-title-input', 'edit-title-count');
                updateIconPreview('edit-icon-input', 'edit-icon-preview', 'edit-color-select');
                
                const modal = new bootstrap.Modal(document.getElementById('edit-service-modal'));
                modal.show();
            }
        });

        // Form submission handlers
        const createForm = document.querySelector('#create-service-modal form');
        const editForm = document.getElementById('edit-form');
        const createSubmitBtn = document.getElementById('create-submit-btn');
        const editSubmitBtn = document.getElementById('edit-submit-btn');

        createForm.addEventListener('submit', function(e) {
            createSubmitBtn.disabled = true;
            createSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
        });

        editForm.addEventListener('submit', function(e) {
            editSubmitBtn.disabled = true;
            editSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
        });

        // Reset modals when closed
        document.getElementById('create-service-modal').addEventListener('hidden.bs.modal', function() {
            createForm.reset();
            createSubmitBtn.disabled = false;
            createSubmitBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Create Service';
            document.getElementById('create-icon-preview').innerHTML = '';
            document.getElementById('create-title-count').textContent = '0';
        });

        document.getElementById('edit-service-modal').addEventListener('hidden.bs.modal', function() {
            editForm.reset();
            editSubmitBtn.disabled = false;
            editSubmitBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Update Service';
            document.getElementById('edit-icon-preview').innerHTML = '';
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

    function setupIconPreviews() {
        // Create service icon preview
        const createIconInput = document.getElementById('create-icon-input');
        const createColorSelect = document.getElementById('create-color-select');
        
        if (createIconInput) {
            createIconInput.addEventListener('input', function() {
                updateIconPreview('create-icon-input', 'create-icon-preview', 'create-color-select');
            });
        }
        
        if (createColorSelect) {
            createColorSelect.addEventListener('change', function() {
                updateIconPreview('create-icon-input', 'create-icon-preview', 'create-color-select');
            });
        }

        // Edit service icon preview
        const editIconInput = document.getElementById('edit-icon-input');
        const editColorSelect = document.getElementById('edit-color-select');
        
        if (editIconInput) {
            editIconInput.addEventListener('input', function() {
                updateIconPreview('edit-icon-input', 'edit-icon-preview', 'edit-color-select');
            });
        }
        
        if (editColorSelect) {
            editColorSelect.addEventListener('change', function() {
                updateIconPreview('edit-icon-input', 'edit-icon-preview', 'edit-color-select');
            });
        }
    }

    function updateIconPreview(inputId, previewId, colorSelectId) {
        const iconInput = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const colorSelect = document.getElementById(colorSelectId);
        
        if (iconInput && preview && colorSelect) {
            const iconClass = iconInput.value.trim();
            const iconColor = colorSelect.value;
            
            if (iconClass) {
                preview.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="icon-preview bg-${iconColor}-lt me-2">
                            <i class="${iconClass} icon text-${iconColor}"></i>
                        </div>
                        <small class="text-secondary">Preview</small>
                    </div>
                `;
            } else {
                preview.innerHTML = '';
            }
        }
    }
</script>
@endpush