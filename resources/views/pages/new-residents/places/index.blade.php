@extends('layouts.main')

@section('title', 'Places Management')

@push('styles')
<style>
    .place-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .place-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .place-image {
        height: 200px;
        object-fit: cover;
        background-color: #f8f9fa;
    }
    
    .place-actions {
        flex-shrink: 0;
    }
    
    .place-actions .dropdown-toggle::after {
        display: none;
    }
    
    .place-actions .btn {
        border: none;
        background: transparent;
        color: #6c757d;
        padding: 0.25rem;
        line-height: 1;
    }
    
    .place-actions .btn:hover {
        background: rgba(0, 0, 0, 0.05);
        color: #495057;
    }
    
    .place-status {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 10;
    }
    
    .place-category {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
    }
    
    .place-order {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 10;
    }
    
    .tag-badge {
        font-size: 0.75rem;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
    }
    
    /* Search highlighting */
    mark.search-highlight {
        background-color: #fff3cd;
        padding: 0.1rem 0.2rem;
        border-radius: 0.25rem;
        font-weight: 600;
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
    <h2 class="page-title">Places Management</h2>
    <div class="btn-list">
        <a href="{{ route('new-residents.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to New Residents
        </a>
        <a href="{{ route('new-residents.categories.index') }}" class="btn btn-outline-primary">
            <i class="ti ti-folder me-1"></i> Manage Categories
        </a>
        @if($categories->where('is_active', true)->count() > 0)
        <a href="{{ route('new-residents.places.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Add Place
        </a>
        @else
        <a href="{{ route('new-residents.categories.index') }}" class="btn btn-primary">
            <i class="ti ti-folder-plus me-1"></i> Create Category First
        </a>
        @endif
    </div>
</div>
@endsection

@section('content')

{{-- Search and Filter Form --}}
<div class="col-12">
    <form method="GET" action="{{ route('new-residents.places.index') }}" id="filter-form">
        <div class="d-flex justify-content-between align-items-center gap-2">
            <div class="input-icon" style="max-width: 350px;">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Search place name, address, or description..." 
                       value="{{ request('search') }}" 
                       autocomplete="off" 
                       id="search-input">
            </div>
            <div class="d-flex gap-2 align-items-center">
                <select class="form-select" name="category" id="category-filter" style="min-width: 150px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
                <select class="form-select" name="status" id="status-filter" style="min-width: 130px;">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @if(request('search') || request('status') || request('category'))
                <a href="{{ route('new-residents.places.index') }}" class="btn btn-outline-secondary" title="Clear all filters">
                    <i class="ti ti-x"></i>
                </a>
                @endif
            </div>
        </div>
        
        {{-- Active Filters Display --}}
        @if(request('search') || request('status') || request('category'))
        <div class="mt-2 d-flex gap-2 align-items-center flex-wrap">
            <small class="text-secondary">Active filters:</small>
            @if(request('search'))
            <span class="badge bg-blue-lt filter-badge">
                <i class="ti ti-search me-1"></i>
                Search: "{{ request('search') }}"
            </span>
            @endif
            @if(request('category'))
            @php
                $selectedCategory = $categories->firstWhere('id', request('category'));
            @endphp
            <span class="badge bg-primary-lt filter-badge">
                <i class="ti ti-folder me-1"></i>
                Category: {{ $selectedCategory ? $selectedCategory->name : 'Unknown' }}
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

{{-- Places Grid --}}
<div class="col-12">
    <div class="card">
        <div class="card-body p-0">
            @if($places->count() > 0)
            <div class="row g-3 p-3">
                @foreach($places as $place)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card place-card h-100 position-relative">
                        <div class="position-relative">
                            <img src="{{ $place->image_url }}" class="card-img-top place-image" alt="{{ $place->name }}">
                            
                            {{-- Order Number --}}
                            <div class="place-order">{{ $place->order }}</div>
                            
                            {{-- Status Badge --}}
                            <div class="place-status">
                                @if($place->is_active)
                                <span class="badge bg-success text-white">Active</span>
                                @else
                                <span class="badge bg-secondary text-white">Inactive</span>
                                @endif
                            </div>
                            
                            {{-- Category Badge --}}
                            <div class="place-category">
                                @if($place->category)
                                <span class="badge bg-primary text-white">
                                    {{ $place->category->name }}
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0 me-2" data-searchable="name">{{ $place->name }}</h5>
                                {{-- Actions Dropdown --}}
                                <div class="place-actions">
                                    <div class="dropdown">
                                        <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ $place->image_url }}" target="_blank">
                                                    <i class="ti ti-external-link me-2"></i>
                                                    View Image
                                                </a>
                                            </li>
                                            @if($place->map_url)
                                            <li>
                                                <a class="dropdown-item" href="{{ $place->map_url }}" target="_blank">
                                                    <i class="ti ti-map-pin me-2"></i>
                                                    View on Map
                                                </a>
                                            </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('new-residents.places.edit', $place) }}">
                                                    <i class="ti ti-edit me-2"></i>
                                                    Edit Place
                                                </a>
                                            </li>
                                            <li>
                                                <button type="button" 
                                                        class="dropdown-item text-danger delete-btn"
                                                        data-id="{{ $place->id }}"
                                                        data-name="{{ $place->name }}"
                                                        data-url="{{ route('new-residents.places.destroy', $place) }}">
                                                    <i class="ti ti-trash me-2"></i>
                                                    Delete Place
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <p class="card-text text-secondary small mb-2" data-searchable="address">
                                <i class="ti ti-map-pin me-1"></i>
                                {{ Str::limit($place->address, 60) }}
                            </p>
                            
                            @if($place->description)
                            <p class="card-text text-secondary small mb-2" data-searchable="description">
                                {{ Str::limit(strip_tags($place->description), 80) }}
                            </p>
                            @endif
                            
                            @if($place->tags && count($place->tags) > 0)
                            <div class="mb-2">
                                @foreach(array_slice($place->tags, 0, 3) as $tag)
                                <span class="badge bg-blue-lt tag-badge">{{ $tag }}</span>
                                @endforeach
                                @if(count($place->tags) > 3)
                                <span class="badge bg-gray-lt tag-badge">+{{ count($place->tags) - 3 }} more</span>
                                @endif
                            </div>
                            @endif
                            
                            <small class="text-secondary">
                                <i class="ti ti-calendar me-1"></i>{{ $place->created_at->format('d M Y') }}
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty py-5">
                <div class="empty-icon">
                    @if(request('search') || request('status') || request('category') || request('page'))
                    <i class="ti ti-search icon icon-lg"></i>
                    @else
                    <i class="ti ti-map-pin icon icon-lg"></i>
                    @endif
                </div>
                <p class="empty-title h3">
                    @if(request('search') || request('status') || request('category') || request('page'))
                    No places found
                    @else
                    No places yet
                    @endif
                </p>
                <p class="empty-subtitle text-secondary">
                    @if(request('search') || request('status') || request('category') || request('page'))
                    Try adjusting your search terms or clear the filters to see all places.
                    @else
                    Get started by creating your first place.<br>
                    Add information about important locations for new residents.
                    @endif
                </p>
                <div class="empty-action">
                    @if(request('search') || request('status') || request('category') || request('page'))
                    <a href="{{ route('new-residents.places.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-x me-1"></i> Clear Filters
                    </a>
                    @else
                    @if($categories->where('is_active', true)->count() > 0)
                    <a href="{{ route('new-residents.places.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Create First Place
                    </a>
                    @else
                    <a href="{{ route('new-residents.categories.index') }}" class="btn btn-primary">
                        <i class="ti ti-folder-plus me-1"></i> Create Category First
                    </a>
                    @endif
                    @endif
                </div>
            </div>
            @endif
        </div>
        
        {{-- Footer with Results Info and Pagination --}}
        @if($places->total() > 0 || request('search') || request('status') || request('category'))
        <div class="card-footer d-flex align-items-center">
            <div class="text-secondary">
                @if($places->total() > 0)
                    Showing <strong>{{ $places->firstItem() }}</strong> to <strong>{{ $places->lastItem() }}</strong> 
                    of <strong>{{ $places->total() }}</strong> results
                    @if(request('search'))
                        for "<strong>{{ request('search') }}</strong>"
                    @endif
                @else
                    No results found
                    @if(request('search') || request('status') || request('category'))
                        with current filters
                    @endif
                @endif
            </div>
            
            @include('components.pagination', ['paginator' => $places])
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
        
        // Highlight search terms in results
        const searchTerm = '{{ request('search') }}';
        if (searchTerm) {
            highlightSearchResults(searchTerm.toLowerCase());
        }

        // Handle delete buttons in dropdown
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('delete-btn')) {
                e.preventDefault();
                e.stopPropagation();
                
                const button = e.target;
                const itemName = button.getAttribute('data-name');
                const deleteUrl = button.getAttribute('data-url');
                
                // Update modal content
                const deleteForm = document.getElementById('delete-form');
                const deleteMessage = document.getElementById('delete-message');
                
                deleteForm.action = deleteUrl;
                deleteMessage.innerHTML = `Do you really want to delete "<strong>${itemName}</strong>"? This process cannot be undone.`;
                
                // Show modal
                const deleteModal = new bootstrap.Modal(document.getElementById('delete-modal'));
                deleteModal.show();
            }
        });
    });

    function setupSearch() {
        const filterForm = document.getElementById('filter-form');
        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('status-filter');
        const categoryFilter = document.getElementById('category-filter');
        
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

        // Category filter change (immediate submit)
        categoryFilter.addEventListener('change', function() {
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
            // Store current cursor position and search value
            const cursorPosition = searchInput.selectionStart;
            const searchValue = searchInput.value;
            
            // Store in sessionStorage for after page reload
            sessionStorage.setItem('searchInputFocus', 'true');
            sessionStorage.setItem('searchCursorPosition', cursorPosition);
            sessionStorage.setItem('searchValue', searchValue);
            
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
        
        // Focus search input on Ctrl+K or Cmd+K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
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