@extends('layouts.main')

@section('title', 'Partnership Items')

@push('styles')
<style>
    .partnership-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .partnership-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .partnership-image {
        height: 200px;
        object-fit: cover;
        background-color: #f8f9fa;
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
    
    .partnership-status {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 10;
    }
    
    .partnership-order {
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
    
    .partnership-actions {
        position: absolute;
        bottom: 15px;
        right: 15px;
        z-index: 10;
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
        <h2 class="page-title">Partnership Items Management</h2>
        <div class="page-subtitle">Manage partnership items that will be displayed on the partnerships page</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('partnerships.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Page Settings
        </a>
        <a href="{{ route('partnerships.items.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Add Partnership Item
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- Search and Filter Form --}}
<div class="col-12">
    <form method="GET" action="{{ route('partnerships.items.index') }}" id="filter-form">
        <div class="d-flex justify-content-start align-items-center gap-2">
            <div class="input-icon" style="max-width: 350px;">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Search partnership title or description..." 
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
                <a href="{{ route('partnerships.items.index') }}" class="btn btn-outline-secondary" title="Clear all filters">
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

{{-- Partnership Items Grid --}}
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-users-group me-2"></i>
                Partnership Items
            </h3>
            <div class="card-actions">
                <div class="btn-list">
                    <span class="badge bg-blue-lt">
                        {{ $items->total() }} items
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($items->count() > 0)
            <div class="row g-3 p-3" id="sortable-container">
                @foreach($items as $item)
                <div class="col-md-6 col-lg-4" data-id="{{ $item->id }}">
                    <div class="card partnership-card position-relative">
                        {{-- Drag Handle --}}
                        @if(!request('search') && !request('status'))
                        <div class="sortable-handle" title="Drag to reorder">
                            <i class="ti ti-grip-vertical"></i>
                        </div>
                        @endif
                        
                        {{-- Status Badge --}}
                        <div class="partnership-status">
                            @if($item->is_active)
                            <span class="badge bg-success text-white">Active</span>
                            @else
                            <span class="badge bg-secondary text-white">Inactive</span>
                            @endif
                        </div>
                        
                        {{-- Order Number --}}
                        <div class="partnership-order">{{ $item->order }}</div>
                        
                        {{-- Action Buttons --}}
                        <div class="partnership-actions">
                            <div class="btn-list">
                                <a href="{{ route('partnerships.items.edit', $item) }}" 
                                   class="btn btn-primary-lt btn-icon" 
                                   title="Edit Item">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-danger btn-icon delete-btn"
                                        data-id="{{ $item->id }}"
                                        data-name="{{ $item->title }}"
                                        data-url="{{ route('partnerships.items.destroy', $item) }}"
                                        title="Delete Item">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        {{-- Item Content --}}
                        <img src="{{ $item->image_url }}" 
                             class="partnership-image w-100" 
                             alt="{{ $item->image_alt_text ?: $item->title }}">
                        
                        <div class="card-body">
                            <h5 class="card-title" data-searchable="title">{{ $item->title }}</h5>
                            <p class="card-text text-secondary" data-searchable="description">
                                {{ Str::limit($item->description, 100) }}
                            </p>
                            <small class="text-secondary">
                                <i class="ti ti-calendar me-1"></i>
                                Created {{ $item->created_at->format('d M Y') }}
                                @if($item->updated_at != $item->created_at)
                                • Updated {{ $item->updated_at->format('d M Y') }}
                                @endif
                            </small>
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
                    <i class="ti ti-users-group icon icon-lg"></i>
                    @endif
                </div>
                <p class="empty-title h3">
                    @if(request('search') || request('status'))
                    No partnership items found
                    @else
                    No partnership items yet
                    @endif
                </p>
                <p class="empty-subtitle text-secondary">
                    @if(request('search') || request('status'))
                    Try adjusting your search terms or clear the filters to see all items.
                    @else
                    Create partnership items to showcase your collaboration programs.<br>
                    Each item can have an image, title, and description.
                    @endif
                </p>
                <div class="empty-action">
                    @if(request('search') || request('status'))
                    <a href="{{ route('partnerships.items.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-x me-1"></i> Clear Filters
                    </a>
                    @else
                    <a href="{{ route('partnerships.items.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Create First Item
                    </a>
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
                    
                    updateItemsOrder(orders);
                }
            });
        }
        @endif
    }

    function updateItemsOrder(orders) {
        fetch('{{ route('partnerships.items.update-order') }}', {
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