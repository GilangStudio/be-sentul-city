@extends('layouts.main')

@section('title', 'Career Positions')

@push('styles')
<style>
    .position-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .position-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .sortable-handle {
        cursor: grab;
        color: #6c757d;
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(255,255,255,0.9);
        border-radius: 6px;
        padding: 8px;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .sortable-handle:hover {
        color: #0054a6;
        background: rgba(255,255,255,1);
    }
    
    .sortable-ghost {
        opacity: 0.5;
    }
    
    .sortable-chosen {
        transform: rotate(1deg);
    }
    
    .position-status {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 10;
    }
    
    .position-order {
        position: absolute;
        top: 15px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        z-index: 10;
    }
    
    .position-actions {
        position: absolute;
        bottom: 15px;
        right: 15px;
        z-index: 10;
    }
    
    .position-card .card-body {
        min-height: 200px;
        position: relative;
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
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Career Positions Management</h2>
        <div class="page-subtitle">Manage job positions and their applications</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('careers.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Page Settings
        </a>
        <a href="{{ route('careers.positions.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Add Position
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- Search and Filter Form --}}
<div class="col-12">
    <form method="GET" action="{{ route('careers.positions.index') }}" id="filter-form">
        <div class="d-flex justify-content-start align-items-center gap-2">
            <div class="input-icon" style="max-width: 350px;">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Search position title or location..." 
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
                <select class="form-select" name="type" id="type-filter" style="min-width: 150px;">
                    <option value="">All Types</option>
                    <option value="full-time" {{ request('type') === 'full-time' ? 'selected' : '' }}>Full-Time</option>
                    <option value="part-time" {{ request('type') === 'part-time' ? 'selected' : '' }}>Part-Time</option>
                    <option value="contract" {{ request('type') === 'contract' ? 'selected' : '' }}>Contract</option>
                    <option value="internship" {{ request('type') === 'internship' ? 'selected' : '' }}>Internship</option>
                </select>
                @if(request('search') || request('status') || request('type'))
                <a href="{{ route('careers.positions.index') }}" class="btn btn-outline-secondary" title="Clear all filters">
                    <i class="ti ti-x"></i>
                </a>
                @endif
            </div>
        </div>
        
        {{-- Active Filters Display --}}
        @if(request('search') || request('status') || request('type'))
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
            @if(request('type'))
            <span class="badge bg-orange-lt filter-badge">
                <i class="ti ti-briefcase me-1"></i>
                Type: {{ ucfirst(str_replace('-', ' ', request('type'))) }}
            </span>
            @endif
        </div>
        @endif
    </form>
</div>

{{-- Career Positions Grid --}}
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-briefcase me-2"></i>
                Career Positions
            </h3>
            <div class="card-actions">
                <div class="btn-list">
                    <span class="badge bg-blue-lt">
                        {{ $positions->total() }} positions
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($positions->count() > 0)
            <div class="row g-3 p-3" id="sortable-container">
                @foreach($positions as $position)
                <div class="col-md-6 col-lg-4" data-id="{{ $position->id }}">
                    <div class="card position-card h-100 position-relative">
                        {{-- Drag Handle --}}
                        @if(!request('search') && !request('status') && !request('type'))
                        <div class="sortable-handle" title="Drag to reorder">
                            <i class="ti ti-grip-vertical"></i>
                        </div>
                        @endif
                        
                        {{-- Status Badge --}}
                        <div class="position-status">
                            <span class="badge bg-{{ $position->status_color }} text-white">{{ $position->status_text }}</span>
                        </div>
                        
                        {{-- Order Number --}}
                        <div class="position-order">{{ $position->order }}</div>
                        
                        {{-- Action Buttons --}}
                        <div class="position-actions">
                            <div class="btn-list mt-auto">
                                @if($position->applications_count > 0)
                                <a href="{{ route('careers.positions.applications.index', $position) }}" 
                                   class="btn btn-primary btn-icon" 
                                   title="View Applications ({{ $position->applications_count }})">
                                    <i class="ti ti-users"></i>
                                </a>
                                @endif
                                <a href="{{ route('careers.positions.edit', $position) }}" 
                                   class="btn btn-primary-lt btn-icon" 
                                   title="Edit Position">
                                    <i class="ti ti-edit"></i>
                                </a>
                                @if($position->canDelete())
                                <button type="button" 
                                        class="btn btn-danger btn-icon delete-btn"
                                        data-id="{{ $position->id }}"
                                        data-name="{{ $position->title }}"
                                        data-url="{{ route('careers.positions.destroy', $position) }}"
                                        title="Delete Position">
                                    <i class="ti ti-trash"></i>
                                </button>
                                @else
                                <button type="button" 
                                        class="btn btn-outline-secondary btn-icon" 
                                        title="Cannot delete - has applications"
                                        disabled>
                                    <i class="ti ti-lock"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Position Content --}}
                        <div class="card-body" style="padding-top: 4rem;">
                            <div class="mb-3">
                                <h4 class="card-title mb-2" data-searchable="title">{{ $position->title }}</h4>
                                <div class="text-secondary small mb-2">
                                    <i class="ti ti-map-pin me-1"></i>
                                    <span data-searchable="location">{{ $position->location }}</span>
                                    <span class="mx-2">•</span>
                                    <span class="badge bg-{{ $position->type === 'full-time' ? 'primary' : ($position->type === 'part-time' ? 'info' : ($position->type === 'contract' ? 'warning' : 'success')) }}-lt">
                                        {{ $position->type_text }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-secondary small" style="line-height: 1.4;">
                                    {!! Str::limit(strip_tags($position->responsibilities), 120) !!}
                                </div>
                            </div>

                            <div class="mb-3">
                                @if($position->applications_count > 0)
                                <div class="d-flex align-items-start gap-1">
                                    <span class="badge bg-blue-lt">
                                        {{ $position->applications_count }} 
                                        {{ Str::plural('application', $position->applications_count) }}
                                    </span>
                                    @if($position->pending_applications_count > 0)
                                    <span class="badge bg-warning-lt">
                                        {{ $position->pending_applications_count }} pending
                                    </span>
                                    @endif
                                </div>
                                @endif
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-end">
                                <div class="text-secondary small">
                                    <div class="mb-1">
                                        <i class="ti ti-calendar me-1"></i>
                                        Posted {{ $position->days_posted_text }}
                                    </div>
                                    @if($position->closing_date)
                                    <div>
                                        <i class="ti ti-calendar-x me-1"></i>
                                        Closes {{ $position->closing_date->format('M j, Y') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty py-5">
                <div class="empty-icon">
                    @if(request('search') || request('status') || request('type'))
                    <i class="ti ti-search icon icon-lg"></i>
                    @else
                    <i class="ti ti-briefcase icon icon-lg"></i>
                    @endif
                </div>
                <p class="empty-title h3">
                    @if(request('search') || request('status') || request('type'))
                    No career positions found
                    @else
                    No career positions yet
                    @endif
                </p>
                <p class="empty-subtitle text-secondary">
                    @if(request('search') || request('status') || request('type'))
                    Try adjusting your search terms or clear the filters to see all positions.
                    @else
                    Create job positions to start recruiting talent.<br>
                    Each position can have detailed responsibilities, requirements, and benefits.
                    @endif
                </p>
                <div class="empty-action">
                    @if(request('search') || request('status') || request('type'))
                    <a href="{{ route('careers.positions.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-x me-1"></i> Clear Filters
                    </a>
                    @else
                    <a href="{{ route('careers.positions.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Create First Position
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
        
        {{-- Footer with Results Info and Pagination --}}
        @if($positions->total() > 0 || request('search') || request('status') || request('type'))
        <div class="card-footer d-flex align-items-center">
            <div class="text-secondary">
                @if($positions->total() > 0)
                    Showing <strong>{{ $positions->firstItem() }}</strong> to <strong>{{ $positions->lastItem() }}</strong> 
                    of <strong>{{ $positions->total() }}</strong> results
                    @if(request('search'))
                        for "<strong>{{ request('search') }}</strong>"
                    @endif
                @else
                    No results found
                    @if(request('search') || request('status') || request('type'))
                        with current filters
                    @endif
                @endif
            </div>
            
            @include('components.pagination', ['paginator' => $positions])
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
        setupSortable();
        
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
        const typeFilter = document.getElementById('type-filter');
        
        let searchTimeout;
        
        // Search input with debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                submitFilter();
            }, 600);
        });
        
        // Filter change
        statusFilter.addEventListener('change', function() {
            submitFilter();
        });
        
        typeFilter.addEventListener('change', function() {
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
            const cursorPosition = searchInput.selectionStart;
            const searchValue = searchInput.value;
            
            sessionStorage.setItem('searchInputFocus', 'true');
            sessionStorage.setItem('searchCursorPosition', cursorPosition);
            sessionStorage.setItem('searchValue', searchValue);
            
            filterForm.submit();
        }
        
        // Restore focus and cursor position after page load
        function restoreSearchFocus() {
            const shouldFocus = sessionStorage.getItem('searchInputFocus');
            const cursorPosition = sessionStorage.getItem('searchCursorPosition');
            const searchValue = sessionStorage.getItem('searchValue');
            
            if (shouldFocus === 'true' && searchInput.value === searchValue) {
                searchInput.focus();
                
                if (cursorPosition !== null) {
                    searchInput.setSelectionRange(parseInt(cursorPosition), parseInt(cursorPosition));
                }
                
                sessionStorage.removeItem('searchInputFocus');
                sessionStorage.removeItem('searchCursorPosition');
                sessionStorage.removeItem('searchValue');
            }
        }
        
        restoreSearchFocus();
        
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
        @if(!request('search') && !request('status') && !request('type') && $positions->count() > 1)
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
                    const currentPage = {{ $positions->currentPage() }};
                    const perPage = {{ $positions->perPage() }};
                    
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
                    
                    updatePositionsOrder(orders);
                }
            });
        }
        @endif
    }

    function updatePositionsOrder(orders) {
        fetch('{{ route('careers.positions.update-order') }}', {
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
                    const row = document.querySelector(`[data-id="${item.id}"]`);
                    if (row) {
                        const orderElement = row.querySelector('.position-order');
                        if (orderElement) {
                            orderElement.textContent = `${item.order}`;
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