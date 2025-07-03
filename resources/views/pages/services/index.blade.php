@extends('layouts.main')

@section('title', 'Our Services')

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
    
    .section-image {
        height: 200px;
        object-fit: cover;
        background-color: #f8f9fa;
    }
    
    .sortable-handle {
        cursor: grab;
        color: #6c757d;
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(255,255,255,0.9);
        border-radius: 4px;
        padding: 4px;
        z-index: 10;
    }
    
    .sortable-handle:hover {
        color: #0054a6;
        background: rgba(255,255,255,1);
    }
    
    .sortable-ghost {
        opacity: 0.5;
    }
    
    .sortable-chosen {
        transform: rotate(2deg);
    }
    
    .layout-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
    }
    
    .section-order {
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
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }
    
    .banner-preview {
        max-height: 300px;
        overflow: hidden;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
    
    .banner-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    /* .banner-form-card {
        border: 2px dashed #ddd;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .banner-form-card:hover {
        border-color: #0054a6;
    }
    
    .banner-form-card.has-image {
        border-style: solid;
        border-color: #dee2e6;
    } */
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">Our Services Management</h2>
</div>
@endsection

@section('content')

{{-- Banner Settings Form --}}
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-photo me-2"></i>
                Banner Settings
            </h3>
            @if($servicesPage)
            <div class="card-actions">
                <span class="badge bg-green-lt">
                    <i class="ti ti-check me-1"></i>
                    Configured
                </span>
            </div>
            @endif
        </div>
        
        {{-- Single Banner Form --}}
        <form method="POST" action="{{ route('services.updateOrCreate') }}" enctype="multipart/form-data" id="banner-form">
            @csrf
            <div class="card-body">
                <div class="row">
                    {{-- Current Banner Preview (only show if exists) --}}
                    @if($servicesPage)
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Current Banner</label>
                            <div class="banner-preview">
                                <img src="{{ $servicesPage->banner_image_url }}" class="banner-image" alt="Current Banner" id="current-banner-image">
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Banner Form Fields --}}
                    <div class="{{ $servicesPage ? 'col-md-8' : 'col-12' }}">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ $servicesPage ? 'Update Banner Image' : 'Banner Image' }}
                                @if(!$servicesPage)<span class="text-danger">*</span>@endif
                            </label>
                            <input type="file" class="form-control @error('banner_image') is-invalid @enderror" 
                                   name="banner_image" accept="image/*" {{ $servicesPage ? '' : 'required' }} id="banner-input">
                            @error('banner_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                <i class="ti ti-info-circle me-1"></i>
                                @if($servicesPage)
                                Leave empty to keep current image. 
                                @endif
                                Recommended: 1920x800px, Max: 10MB (JPG, PNG, WebP)
                            </small>
                            <div class="mt-3" id="banner-preview"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alt Text</label>
                            <input type="text" class="form-control @error('banner_alt_text') is-invalid @enderror" 
                                   name="banner_alt_text" value="{{ old('banner_alt_text', $servicesPage->banner_alt_text ?? '') }}"
                                   placeholder="Enter image description for accessibility">
                            @error('banner_alt_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                Describe the image for screen readers and SEO.
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                       {{ old('is_active', $servicesPage->is_active ?? true) ? 'checked' : '' }}>
                                <span class="form-check-label">Active Status</span>
                            </label>
                            <small class="form-hint d-block">
                                <i class="ti ti-info-circle me-1"></i>
                                Make the services page publicly accessible.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- SEO Settings --}}
            <div class="card-body border-top">
                <h4 class="card-title mb-3">
                    <i class="ti ti-seo me-2"></i>
                    SEO Settings
                </h4>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                   name="meta_title" value="{{ old('meta_title', $servicesPage->meta_title ?? '') }}" 
                                   placeholder="Enter title that will appear in search results"
                                   maxlength="255" id="meta-title-input">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                <span id="meta-title-count">{{ strlen($servicesPage->meta_title ?? '') }}</span>/255 characters. 
                                Leave empty to use default "Our Services".
                            </small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                      name="meta_description" rows="3" 
                                      placeholder="Enter description that will appear in search results"
                                      maxlength="500" id="meta-desc-input">{{ old('meta_description', $servicesPage->meta_description ?? '') }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                <span id="meta-desc-count">{{ strlen($servicesPage->meta_description ?? '') }}</span>/500 characters. 
                            </small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                   name="meta_keywords" value="{{ old('meta_keywords', $servicesPage->meta_keywords ?? '') }}" 
                                   placeholder="keywords separated by commas. e.g: services, building control, security"
                                   maxlength="255" id="meta-keywords-input">
                            @error('meta_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                <span id="meta-keywords-count">{{ strlen($servicesPage->meta_keywords ?? '') }}</span>/255 characters. 
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary" id="save-banner-btn">
                    <i class="ti ti-device-floppy me-1"></i>
                    {{ $servicesPage ? 'Update' : 'Create' }} Banner Settings
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Service Sections --}}
@if($servicesPage)
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
                    <a href="{{ route('services.sections.create') }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-plus me-1"></i> Add Section
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($sections->count() > 0)
            <div class="row g-3 p-3" id="sortable-container">
                @foreach($sections as $section)
                <div class="col-md-4" data-id="{{ $section->id }}">
                    <div class="card section-card h-100 position-relative">
                        {{-- Drag Handle --}}
                        <div class="sortable-handle" title="Drag to reorder">
                            <i class="ti ti-grip-vertical"></i>
                        </div>
                        
                        {{-- Layout Badge --}}
                        <div class="layout-badge">
                            @if($section->layout === 'image_left')
                            <span class="badge bg-maroon text-white">
                                <i class="ti ti-layout-align-left me-1"></i>Left
                            </span>
                            @else
                            <span class="badge bg-primary text-white">
                                <i class="ti ti-layout-align-right me-1"></i>Right
                            </span>
                            @endif
                        </div>
                        
                        {{-- Order Number --}}
                        <div class="section-order">{{ $section->order }}</div>
                        
                        <div class="position-relative">
                            <img src="{{ $section->image_url }}" class="card-img-top section-image" alt="{{ $section->title }}">
                            
                            {{-- Status Badge --}}
                            @if(!$section->is_active)
                            <div style="position: absolute; top: 50px; right: 10px;">
                                <span class="badge bg-secondary text-white">Inactive</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title mb-2">{{ $section->title }}</h5>
                            <p class="card-text text-secondary small mb-3" style="line-height: 1.4;">
                                {{ Str::limit(strip_tags($section->description), 100) }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-secondary">
                                        <i class="ti ti-calendar me-1"></i>{{ $section->created_at->format('d M Y') }}
                                    </small>
                                </div>
                                <div class="btn-list">
                                    <a href="{{ route('services.sections.edit', $section) }}" 
                                       class="btn btn-primary btn-sm" 
                                       title="Edit Section">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger btn-sm delete-btn"
                                            data-id="{{ $section->id }}"
                                            data-name="{{ $section->title }}"
                                            data-url="{{ route('services.sections.destroy', $section) }}"
                                            title="Delete Section">
                                        <i class="ti ti-trash"></i>
                                    </button>
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
        setupImagePreviews();
        setupCharacterCounters();
        setupSortable();
        setupFormSubmission();
    });

    function setupImagePreviews() {
        // Banner image preview
        const bannerInput = document.getElementById('banner-input');
        
        if (bannerInput) {
            bannerInput.addEventListener('change', function(e) {
                handleImagePreview(e, 'banner-preview', bannerInput);
                
                // Update banner form card styling
                const bannerFormCard = document.querySelector('.banner-form-card');
                if (bannerFormCard) {
                    if (e.target.files.length > 0) {
                        bannerFormCard.classList.add('has-image');
                    } else {
                        bannerFormCard.classList.remove('has-image');
                    }
                }
            });
        }
    }

    function setupCharacterCounters() {
        // Meta fields character counters
        setupCharacterCounter('meta-title-input', 'meta-title-count', 255);
        setupCharacterCounter('meta-desc-input', 'meta-desc-count', 500);
        setupCharacterCounter('meta-keywords-input', 'meta-keywords-count', 255);
    }

    function setupCharacterCounter(inputId, countId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(countId);
        
        if (input && counter) {
            input.addEventListener('input', function() {
                const currentLength = this.value.length;
                counter.textContent = currentLength;
                
                // Add warning colors
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
            });
        }
    }

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
                    const items = sortableContainer.querySelectorAll('.col-md-6[data-id]');
                    
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

    function setupFormSubmission() {
        // Banner form submission
        const bannerForm = document.getElementById('banner-form');
        const saveBannerBtn = document.getElementById('save-banner-btn');
        
        if (bannerForm && saveBannerBtn) {
            bannerForm.addEventListener('submit', function(e) {
                saveBannerBtn.disabled = true;
                saveBannerBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                bannerForm.classList.add('loading');
            });
        }
    }

    function handleImagePreview(event, previewId, inputElement) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById(previewId);
        
        if (file) {
            // Validate file size
            const maxSize = inputElement.id.includes('banner') ? 10 * 1024 * 1024 : 5 * 1024 * 1024;
            if (file.size > maxSize) {
                const maxSizeMB = inputElement.id.includes('banner') ? '10MB' : '5MB';
                showAlert(inputElement, 'danger', `File size too large. Maximum ${maxSizeMB} allowed.`);
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

    // Clear image preview function
    window.clearImagePreview = function(previewId, inputId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewId).innerHTML = '';
        
        // Update banner form card styling if it's banner input
        if (inputId === 'banner-input') {
            const bannerFormCard = document.querySelector('.banner-form-card');
            if (bannerFormCard) {
                bannerFormCard.classList.remove('has-image');
            }
        }
    };
</script>
@endpush