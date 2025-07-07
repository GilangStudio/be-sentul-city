@extends('layouts.main')

@section('title', 'Service Sections')

@push('styles')
<style>
    .section-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .section-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .section-preview-image {
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
    
    .section-status {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 10;
    }
    
    .section-order {
        position: absolute;
        bottom: 15px;
        left: 15px;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        z-index: 10;
    }
    
    .section-actions {
        position: absolute;
        bottom: 15px;
        right: 15px;
        z-index: 10;
    }
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }
    
    .layout-preview {
        min-height: 200px;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .layout-preview.image-right {
        flex-direction: row-reverse;
    }
    
    .preview-image-container {
        flex: 0 0 300px;
    }
    
    .preview-content-container {
        flex: 1;
        padding: 1rem;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Service Sections Management</h2>
        <div class="page-subtitle">Manage service sections that will be displayed on the services page</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Services
        </a>
        @if($servicesPage)
        <a href="{{ route('services.sections.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Add Section
        </a>
        @else
        <a href="{{ route('services.index') }}" class="btn btn-primary">
            <i class="ti ti-settings me-1"></i> Setup Services Page
        </a>
        @endif
    </div>
</div>
@endsection

@section('content')

@if(!$servicesPage)
{{-- Services Page Not Setup Notice --}}
<div class="col-12">
    <div class="card border-warning">
        <div class="card-body text-center py-5">
            <div class="empty-icon mb-3">
                <i class="ti ti-alert-triangle icon icon-lg text-warning"></i>
            </div>
            <h3 class="empty-title">Services Page Not Set Up</h3>
            <p class="empty-subtitle text-secondary mb-4">
                You need to set up the services page settings first before you can create service sections.<br>
                This includes banner image and basic page configuration.
            </p>
            <div class="empty-action">
                <a href="{{ route('services.index') }}" class="btn btn-warning">
                    <i class="ti ti-settings me-1"></i>
                    Set Up Services Page
                </a>
            </div>
        </div>
    </div>
</div>
@else
{{-- Service Sections Grid --}}
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-layout-grid me-2"></i>
                Service Sections
            </h3>
            <div class="card-actions">
                <div class="btn-list">
                    <span class="badge bg-blue-lt">
                        {{ $sections->count() }} sections
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($sections->count() > 0)
            <div class="row g-3 p-3" id="sortable-container">
                @foreach($sections as $section)
                <div class="col-12" data-id="{{ $section->id }}">
                    <div class="card section-card position-relative">
                        {{-- Drag Handle --}}
                        <div class="sortable-handle" title="Drag to reorder">
                            <i class="ti ti-grip-vertical"></i>
                        </div>
                        
                        {{-- Status Badge --}}
                        <div class="section-status">
                            @if($section->is_active)
                            <span class="badge bg-success text-white">Active</span>
                            @else
                            <span class="badge bg-secondary text-white">Inactive</span>
                            @endif
                        </div>
                        
                        {{-- Order Number --}}
                        <div class="section-order">#{{ $section->order }}</div>
                        
                        {{-- Action Buttons --}}
                        <div class="section-actions">
                            <div class="btn-list">
                                <a href="{{ route('services.sections.edit', $section) }}" 
                                   class="btn btn-primary-lt btn-icon" 
                                   title="Edit Section">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-danger btn-icon delete-btn"
                                        data-id="{{ $section->id }}"
                                        data-name="{{ $section->title }}"
                                        data-url="{{ route('services.sections.destroy', $section) }}"
                                        title="Delete Section">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        {{-- Layout Preview --}}
                        <div class="layout-preview {{ $section->layout === 'image_right' ? 'image-right' : '' }}">
                            {{-- Image Container --}}
                            <div class="preview-image-container">
                                <img src="{{ $section->image_url }}" 
                                     class="section-preview-image w-100 rounded" 
                                     alt="{{ $section->image_alt_text ?: $section->title }}">
                            </div>
                            
                            {{-- Content Container --}}
                            <div class="preview-content-container">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h4 class="card-title mb-2">{{ $section->title }}</h4>
                                        <div class="text-secondary">
                                            {!! Str::limit($section->description, 300) !!}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-auto pt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-secondary small">
                                            <i class="ti ti-calendar me-1"></i>
                                            Created {{ $section->created_at->format('d M Y') }}
                                            @if($section->updated_at != $section->created_at)
                                            • Updated {{ $section->updated_at->format('d M Y') }}
                                            @endif
                                        </div>
                                    </div>
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
                    <i class="ti ti-layout-grid icon icon-lg"></i>
                </div>
                <p class="empty-title h3">No service sections yet</p>
                <p class="empty-subtitle text-secondary">
                    Create service sections to showcase your offerings.<br>
                    Each section can have a custom layout and content.
                </p>
                <div class="empty-action">
                    <a href="{{ route('services.sections.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Create First Section
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

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
        setupSortable();
    });

    function setupSortable() {
        @if($sections->count() > 1)
        const sortableContainer = document.getElementById('sortable-container');
        if (sortableContainer) {
            new Sortable(sortableContainer, {
                handle: '.sortable-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function(evt) {
                    const orders = [];
                    const items = sortableContainer.querySelectorAll('.col-12[data-id]');
                    
                    console.log('Total items found:', items.length);
                    
                    items.forEach((item, index) => {
                        const id = item.getAttribute('data-id');
                        console.log(`Index ${index}: ID = ${id}`);
                        
                        orders.push({
                            id: id,
                            order: index + 1
                        });
                    });
                    
                    console.log('Orders to send:', orders);
                    updateSectionsOrder(orders);
                }
            });
        }
        @endif
    }

    function updateSectionsOrder(orders) {
        // Validasi data sebelum dikirim
        const uniqueIds = new Set();
        const validOrders = [];
        
        orders.forEach(item => {
            if (!uniqueIds.has(item.id)) {
                uniqueIds.add(item.id);
                validOrders.push(item);
            }
        });
        
        console.log('Valid orders to send:', validOrders);
        
        fetch('{{ route('services.sections.update-order') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ orders: validOrders })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Update order numbers in UI immediately
                validOrders.forEach((item, index) => {
                    const row = document.querySelector(`[data-id="${item.id}"]`);
                    if (row) {
                        const orderElement = row.querySelector('.section-order');
                        if (orderElement) {
                            orderElement.textContent = index + 1;
                        }
                    }
                });
            } else {
                showToast(data.message, 'error');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error updating order:', error);
            showToast('Failed to update order', 'error');
            location.reload();
        });
    }
</script>
@endpush