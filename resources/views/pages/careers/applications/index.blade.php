@extends('layouts.main')

@section('title', 'Job Applications')

@push('styles')
<style>
    .application-card {
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    
    .application-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }
    
    /* Search highlighting */
    mark.search-highlight {
        background-color: #fff3cd;
        padding: 0.1rem 0.2rem;
        border-radius: 0.25rem;
        font-weight: 600;
    }
    
    .filter-badge {
        font-size: 0.75rem;
    }

    .bulk-actions-bar {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        display: none;
    }

    .bulk-actions-bar.show {
        display: block;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Job Applications</h2>
        <div class="page-subtitle">
            Applications for: <strong>{{ $position->title }}</strong>
            <span class="badge bg-{{ $position->status_color }}-lt ms-2">{{ $position->status_text }}</span>
        </div>
    </div>
    <div class="btn-list">
        <a href="{{ route('careers.positions.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Positions
        </a>
        <a href="{{ route('careers.positions.edit', $position) }}" class="btn btn-outline-primary">
            <i class="ti ti-edit me-1"></i> Edit Position
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- Position Info Card --}}
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title mb-1">{{ $position->title }}</h4>
                    <div class="text-secondary">
                        <i class="ti ti-map-pin me-1"></i> {{ $position->location }}
                        <span class="mx-2">•</span>
                        <span class="badge bg-primary-lt">{{ $position->type_text }}</span>
                        <span class="mx-2">•</span>
                        Posted {{ $position->days_posted_text }}
                    </div>
                </div>
                <div class="col-auto">
                    <div class="row g-2">
                        <div class="col-auto">
                            <div class="text-center">
                                <div class="h1 text-primary mb-0">{{ $applications->total() }}</div>
                                <div class="text-secondary small">Total Applications</div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="text-center">
                                <div class="h1 text-warning mb-0">{{ $position->pending_applications_count }}</div>
                                <div class="text-secondary small">Pending Review</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Search and Filter Form --}}
<div class="col-12">
    <form method="GET" action="{{ route('careers.positions.applications.index', $position) }}" id="filter-form">
        <div class="d-flex justify-content-start align-items-center gap-2">
            <div class="input-icon" style="max-width: 350px;">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Search applicant name, email, or phone..." 
                       value="{{ request('search') }}" 
                       autocomplete="off" 
                       id="search-input">
            </div>
            <div class="d-flex gap-2 align-items-center">
                <select class="form-select" name="status" id="status-filter" style="min-width: 150px;">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option value="shortlisted" {{ request('status') === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="hired" {{ request('status') === 'hired' ? 'selected' : '' }}>Hired</option>
                </select>
                @if(request('search') || request('status'))
                <a href="{{ route('careers.positions.applications.index', $position) }}" class="btn btn-outline-secondary" title="Clear all filters">
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
                Status: {{ ucfirst(request('status')) }}
            </span>
            @endif
        </div>
        @endif
    </form>
</div>

{{-- Bulk Actions Bar --}}
<div class="col-12">
    <div class="bulk-actions-bar" id="bulk-actions-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong id="selected-count">0</strong> applications selected
            </div>
            <div class="btn-list ms-2">
                <select class="form-select" id="bulk-status-select" style="width: auto;">
                    <option value="">Change Status</option>
                    <option value="reviewed">Mark as Reviewed</option>
                    <option value="shortlisted">Mark as Shortlisted</option>
                    <option value="rejected">Mark as Rejected</option>
                    <option value="hired">Mark as Hired</option>
                </select>
                <button type="button" class="btn btn-primary" id="apply-bulk-action">Apply</button>
                <button type="button" class="btn btn-outline-secondary" id="clear-selection">Clear Selection</button>
            </div>
        </div>
    </div>
</div>

{{-- Applications List --}}
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-users me-2"></i>
                Applications
            </h3>
            <div class="card-actions">
                <div class="btn-list">
                    @if($applications->count() > 0)
                    <label class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="select-all">
                        <span class="form-check-label">Select All</span>
                    </label>
                    @endif
                    <span class="badge bg-blue-lt">
                        {{ $applications->total() }} applications
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($applications->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($applications as $application)
                <div class="list-group-item application-card">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <input class="form-check-input application-checkbox" type="checkbox" 
                                   value="{{ $application->id }}" data-id="{{ $application->id }}">
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-rounded" style="background-color: var(--tblr-primary-lt);">
                                {{ strtoupper(substr($application->name, 0, 2)) }}
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="mb-1" data-searchable="name">{{ $application->name }}</h4>
                                    <div class="text-secondary small mb-1">
                                        <i class="ti ti-mail me-1"></i>
                                        <span data-searchable="email">{{ $application->email }}</span>
                                        <span class="mx-2">•</span>
                                        <i class="ti ti-phone me-1"></i>
                                        <span data-searchable="phone">{{ $application->phone }}</span>
                                    </div>
                                    <div class="text-secondary small">
                                        <i class="ti ti-clock me-1"></i>
                                        Applied {{ $application->applied_ago }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="mb-2">
                                        <span class="badge bg-{{ $application->status_color }}-lt">
                                            <span class="status-dot bg-{{ $application->status_color }}"></span>
                                            {{ $application->status_text }}
                                        </span>
                                    </div>
                                    <div class="btn-list">
                                        @if($application->cv_file_path)
                                        <a href="{{ route('careers.positions.applications.download-cv', [$position, $application]) }}" 
                                           class="btn btn-outline-primary btn-icon" title="Download CV">
                                            <i class="ti ti-download"></i>
                                        </a>
                                        @endif
                                        <a href="{{ route('careers.positions.applications.show', [$position, $application]) }}" 
                                           class="btn btn-primary btn-icon" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-icon dropdown-toggle" 
                                                    data-bs-toggle="dropdown" title="Change Status">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <h6 class="dropdown-header">Change Status</h6>
                                                <form method="POST" action="{{ route('careers.positions.applications.update-status', [$position, $application]) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="reviewed">
                                                    <button type="submit" class="dropdown-item {{ $application->status === 'reviewed' ? 'active' : '' }}">
                                                        <i class="ti ti-eye-check me-2"></i> Reviewed
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('careers.positions.applications.update-status', [$position, $application]) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="shortlisted">
                                                    <button type="submit" class="dropdown-item {{ $application->status === 'shortlisted' ? 'active' : '' }}">
                                                        <i class="ti ti-star me-2"></i> Shortlisted
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('careers.positions.applications.update-status', [$position, $application]) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="dropdown-item {{ $application->status === 'rejected' ? 'active' : '' }}">
                                                        <i class="ti ti-x me-2"></i> Rejected
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('careers.positions.applications.update-status', [$position, $application]) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="hired">
                                                    <button type="submit" class="dropdown-item {{ $application->status === 'hired' ? 'active' : '' }}">
                                                        <i class="ti ti-check me-2"></i> Hired
                                                    </button>
                                                </form>
                                                <div class="dropdown-divider"></div>
                                                <button type="button" 
                                                        class="dropdown-item text-danger delete-btn"
                                                        data-id="{{ $application->id }}"
                                                        data-name="{{ $application->name }}"
                                                        data-url="{{ route('careers.positions.applications.destroy', [$position, $application]) }}">
                                                    <i class="ti ti-trash me-2"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($application->message)
                            <div class="mt-2">
                                <small class="text-secondary">
                                    <strong>Message:</strong> {{ Str::limit($application->message, 150) }}
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty py-5">
                <div class="empty-icon">
                    @if(request('search') || request('status'))
                    <i class="ti ti-search icon icon-lg"></i>
                    @else
                    <i class="ti ti-users icon icon-lg"></i>
                    @endif
                </div>
                <p class="empty-title h3">
                    @if(request('search') || request('status'))
                    No applications found
                    @else
                    No applications yet
                    @endif
                </p>
                <p class="empty-subtitle text-secondary">
                    @if(request('search') || request('status'))
                    Try adjusting your search terms or clear the filters to see all applications.
                    @else
                    Applications for this position will appear here.<br>
                    Share the job posting to start receiving applications.
                    @endif
                </p>
                <div class="empty-action">
                    @if(request('search') || request('status'))
                    <a href="{{ route('careers.positions.applications.index', $position) }}" class="btn btn-outline-secondary">
                        <i class="ti ti-x me-1"></i> Clear Filters
                    </a>
                    @else
                    <a href="{{ route('careers.positions.edit', $position) }}" class="btn btn-primary">
                        <i class="ti ti-edit me-1"></i> Edit Position
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
        
        {{-- Footer with Results Info and Pagination --}}
        @if($applications->total() > 0 || request('search') || request('status'))
        <div class="card-footer d-flex align-items-center">
            <div class="text-secondary">
                @if($applications->total() > 0)
                    Showing <strong>{{ $applications->firstItem() }}</strong> to <strong>{{ $applications->lastItem() }}</strong> 
                    of <strong>{{ $applications->total() }}</strong> results
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
            
            @include('components.pagination', ['paginator' => $applications])
        </div>
        @endif
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
        setupBulkActions();
        
        // Highlight search terms in results
        const searchTerm = '{{ request('search') }}';
        if (searchTerm) {
            highlightSearchResults(searchTerm.toLowerCase());
        }
    });

    function setupSearch() {
        const filterForm = document.getElementById('filter-form');
        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('status-filter');
        
        let searchTimeout;
        
        // Search input with debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                submitFilter();
            }, 600);
        });
        
        // Status filter change
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
        
        function submitFilter() {
            filterForm.submit();
        }
    }

    function setupBulkActions() {
        const selectAllCheckbox = document.getElementById('select-all');
        const applicationCheckboxes = document.querySelectorAll('.application-checkbox');
        const bulkActionsBar = document.getElementById('bulk-actions-bar');
        const selectedCountSpan = document.getElementById('selected-count');
        const bulkStatusSelect = document.getElementById('bulk-status-select');
        const applyBulkActionBtn = document.getElementById('apply-bulk-action');
        const clearSelectionBtn = document.getElementById('clear-selection');

        function updateBulkActionsVisibility() {
            const selectedCheckboxes = document.querySelectorAll('.application-checkbox:checked');
            const count = selectedCheckboxes.length;
            
            if (count > 0) {
                bulkActionsBar.classList.add('show');
                selectedCountSpan.textContent = count;
            } else {
                bulkActionsBar.classList.remove('show');
            }
            
            // Update select all checkbox state
            if (selectAllCheckbox) {
                selectAllCheckbox.indeterminate = count > 0 && count < applicationCheckboxes.length;
                selectAllCheckbox.checked = count === applicationCheckboxes.length && count > 0;
            }
        }

        // Select all functionality
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                applicationCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActionsVisibility();
            });
        }

        // Individual checkbox change
        applicationCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActionsVisibility);
        });

        // Clear selection
        if (clearSelectionBtn) {
            clearSelectionBtn.addEventListener('click', function() {
                applicationCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateBulkActionsVisibility();
            });
        }

        // Apply bulk action
        if (applyBulkActionBtn) {
            applyBulkActionBtn.addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('.application-checkbox:checked');
                const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
                const status = bulkStatusSelect.value;

                if (!status) {
                    showToast('Please select a status to apply', 'warning');
                    return;
                }

                if (selectedIds.length === 0) {
                    showToast('Please select applications to update', 'warning');
                    return;
                }

                // Apply bulk update
                fetch('{{ route('careers.positions.applications.bulk-update-status', $position) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        application_ids: selectedIds,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        location.reload();
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    showToast('Failed to update applications', 'error');
                });
            });
        }
    }

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