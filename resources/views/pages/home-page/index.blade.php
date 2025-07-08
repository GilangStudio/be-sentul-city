@extends('layouts.main')

@section('title', 'Home Page Management')

@push('styles')
<style>
    .banner-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .banner-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .banner-image {
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
    
    .banner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.7) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .banner-card:hover .banner-overlay {
        opacity: 1;
    }
    
    .banner-actions {
        position: absolute;
        bottom: 10px;
        right: 10px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .banner-card:hover .banner-actions {
        opacity: 1;
    }
    
    .banner-status {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 10;
    }
    
    .banner-order {
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
    
    .banner-content-preview {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        color: white;
        padding: 1rem;
        text-align: center;
    }
    
    .banner-preview-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .banner-preview-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }
    
    .banner-preview-button {
        display: inline-block;
        background: var(--bs-primary);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        text-decoration: none;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Home Page Management</h2>
        <div class="page-subtitle">Manage homepage SEO settings and hero banners</div>
    </div>
</div>
@endsection

@section('content')

{{-- SEO Settings Form --}}
<form method="POST" action="{{ route('home-page.seo.update') }}" id="seo-form">
    @csrf
    <div class="col-12">
        @include('components.seo-meta-form', [
            'type' => $homePageSettings ? 'edit' : 'create',
            'data' => $homePageSettings
        ])
        
        <div class="card mt-3">
            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    @if($homePageSettings)
                    <div class="d-flex align-items-center text-secondary">
                        <i class="ti ti-clock me-2"></i>
                        <div>
                            <small class="fw-medium">Last updated:</small>
                            <small class="d-block">{{ $homePageSettings->updated_at->format('d M Y, H:i') }}</small>
                        </div>
                    </div>
                    @else
                    <div class="text-secondary">
                        <i class="ti ti-info-circle me-1"></i>
                        Configure your homepage SEO settings
                    </div>
                    @endif
                    
                    <button type="submit" class="btn btn-primary" id="save-seo-btn">
                        <i class="ti ti-device-floppy me-1"></i>
                        {{ $homePageSettings ? 'Update' : 'Save' }} Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Banners Section --}}
<div class="col-12 mt-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-photo me-2"></i>
                Homepage Banners
            </h3>
            <div class="card-actions">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-banner-modal">
                        <i class="ti ti-plus me-1"></i> Add Banner
                    </button>
                {{-- @if($banners->count() > 0)
                <span class="badge bg-blue-lt">
                    <i class="ti ti-photo me-1"></i>
                    {{ $banners->count() }} {{ $banners->count() === 1 ? 'Banner' : 'Banners' }}
                </span>
                @endif --}}
            </div>
        </div>
        <div class="card-body p-0">
            @if($banners->count() > 0)
            <div class="row g-3 p-3" id="sortable-container">
                @foreach($banners as $banner)
                <div class="col-sm-6 col-md-4 col-lg-3" data-id="{{ $banner->id }}">
                    <div class="card banner-card h-100 position-relative">
                        <div class="position-relative">
                            <img src="{{ $banner->image_url }}" class="card-img-top banner-image" alt="{{ $banner->title_display }}">
                            
                            {{-- Order Number --}}
                            <div class="banner-order">{{ $banner->order }}</div>
                            
                            {{-- Status Badge --}}
                            <div class="banner-status">
                                @if($banner->is_active)
                                <span class="badge bg-success text-white">Active</span>
                                @else
                                <span class="badge bg-secondary text-white">Inactive</span>
                                @endif
                            </div>
                            
                            {{-- Drag Handle --}}
                            @if($banners->count() > 1)
                            <div class="sortable-handle" title="Drag to reorder">
                                <i class="ti ti-grip-vertical"></i>
                            </div>
                            @endif
                            
                            {{-- Overlay --}}
                            <div class="banner-overlay"></div>
                            
                            {{-- Content Preview --}}
                            @if($banner->title || $banner->subtitle || $banner->has_button)
                            <div class="banner-content-preview">
                                @if($banner->title)
                                <div class="banner-preview-title">{{ $banner->title }}</div>
                                @endif
                                @if($banner->subtitle)
                                <div class="banner-preview-subtitle">{{ Str::limit($banner->subtitle, 50) }}</div>
                                @endif
                                @if($banner->has_button)
                                <span class="banner-preview-button">{{ $banner->button_text }}</span>
                                @endif
                            </div>
                            @endif
                            
                            {{-- Actions --}}
                            <div class="banner-actions">
                                <div class="btn-list">
                                    <a href="{{ $banner->image_url }}" target="_blank" class="btn btn-sm btn-warning" title="View Image">
                                        <i class="ti ti-external-link"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-primary edit-banner-btn" 
                                            data-id="{{ $banner->id }}"
                                            data-title="{{ $banner->title }}"
                                            data-subtitle="{{ $banner->subtitle }}"
                                            data-button-text="{{ $banner->button_text }}"
                                            data-button-url="{{ $banner->button_url }}"
                                            data-image-url="{{ $banner->image_url }}"
                                            data-image-alt="{{ $banner->image_alt_text }}"
                                            data-is-active="{{ $banner->is_active ? '1' : '0' }}"
                                            title="Edit Banner">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger delete-btn"
                                            data-id="{{ $banner->id }}"
                                            data-name="{{ $banner->title_display }}"
                                            data-url="{{ route('home-page.banners.destroy', $banner) }}"
                                            title="Delete Banner">
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
                    <i class="ti ti-photo icon icon-lg"></i>
                </div>
                <p class="empty-title h3">No banners yet</p>
                <p class="empty-subtitle text-secondary">
                    Get started by creating your first homepage banner.<br>
                    Showcase your best content to welcome visitors.
                </p>
                <div class="empty-action">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-banner-modal">
                        <i class="ti ti-plus me-1"></i> Create First Banner
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Create Banner Modal --}}
<div class="modal modal-blur fade" id="create-banner-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" action="{{ route('home-page.banners.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-plus me-2"></i>
                    Create Homepage Banner
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Banner Image <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="image" accept="image/*" required id="create-image-input">
                    <small class="form-hint">
                        <i class="ti ti-info-circle me-1"></i>
                        Recommended: 1920x800px, Max: 10MB (JPG, PNG, WebP)
                    </small>
                    <div class="mt-3" id="create-image-preview"></div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Title (Optional)</label>
                            <input type="text" class="form-control" name="title" 
                                   placeholder="e.g., Welcome to Sentul City" id="create-title-input">
                            <small class="form-hint">
                                <span id="create-title-count">0</span>/255 characters
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Button Text (Optional)</label>
                            <input type="text" class="form-control" name="button_text" 
                                   placeholder="e.g., Learn More" id="create-button-text-input">
                            <small class="form-hint">
                                <span id="create-button-text-count">0</span>/100 characters
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Subtitle (Optional)</label>
                    <textarea class="form-control" name="subtitle" rows="3"
                              placeholder="Enter banner subtitle or description..."></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Button URL (Optional)</label>
                    <input type="url" class="form-control" name="button_url" 
                           placeholder="https://example.com">
                    <small class="form-hint">Required if button text is provided</small>
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
                        Only active banners will be displayed on the homepage.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="create-banner-submit-btn">
                    <i class="ti ti-device-floppy me-1"></i>Create Banner
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Banner Modal --}}
<div class="modal modal-blur fade" id="edit-banner-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" enctype="multipart/form-data" id="edit-banner-form">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>
                    Edit Homepage Banner
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
                        Leave empty to keep current image. Max: 10MB (JPG, PNG, WebP)
                    </small>
                    <div class="mt-3" id="edit-image-preview"></div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Title (Optional)</label>
                            <input type="text" class="form-control" name="title" 
                                   placeholder="e.g., Welcome to Sentul City" id="edit-title-input">
                            <small class="form-hint">
                                <span id="edit-title-count">0</span>/255 characters
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Button Text (Optional)</label>
                            <input type="text" class="form-control" name="button_text" 
                                   placeholder="e.g., Learn More" id="edit-button-text-input">
                            <small class="form-hint">
                                <span id="edit-button-text-count">0</span>/100 characters
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Subtitle (Optional)</label>
                    <textarea class="form-control" name="subtitle" rows="3"
                              placeholder="Enter banner subtitle or description..." id="edit-subtitle-input"></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Button URL (Optional)</label>
                    <input type="url" class="form-control" name="button_url" 
                           placeholder="https://example.com" id="edit-button-url-input">
                    <small class="form-hint">Required if button text is provided</small>
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
                <button type="submit" class="btn btn-primary" id="edit-banner-submit-btn">
                    <i class="ti ti-device-floppy me-1"></i>Update Banner
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
        setupSortable();
        setupModals();
        setupCharacterCounters();
        setupImagePreviews();
        setupFormSubmissions();
    });

    function setupSortable() {
        @if($banners->count() > 1)
        const sortableContainer = document.getElementById('sortable-container');
        if (sortableContainer) {
            new Sortable(sortableContainer, {
                handle: '.sortable-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onStart: function(evt) {
                    // Add visual feedback when dragging starts
                    evt.item.style.opacity = '0.7';
                },
                onEnd: function(evt) {
                    // Reset visual feedback
                    evt.item.style.opacity = '1';
                    
                    const orders = [];
                    const items = sortableContainer.children;
                    
                    Array.from(items).forEach((item, index) => {
                        const itemId = item.getAttribute('data-id');
                        if (itemId) {
                            orders.push({
                                id: itemId,
                                order: index + 1
                            });
                        }
                    });
                    
                    // Update visual order numbers immediately
                    updateVisualOrderNumbers();
                    
                    // Send to server
                    updateBannerOrder(orders);
                }
            });
        }
        @endif
    }

    function updateBannerOrder(orders) {
        fetch('{{ route('home-page.banners.update-order') }}', {
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
                // Update visual order numbers immediately
                updateVisualOrderNumbers();
                showToast(data.message, 'success');
            } else {
                showToast(data.message, 'error');
                location.reload();
            }
        })
        .catch(error => {
            showToast('Failed to update banner order', 'error');
            location.reload();
        });
    }

    function updateVisualOrderNumbers() {
        const sortableContainer = document.getElementById('sortable-container');
        if (sortableContainer) {
            const items = sortableContainer.children;
            Array.from(items).forEach((item, index) => {
                const orderElement = item.querySelector('.banner-order');
                if (orderElement) {
                    orderElement.textContent = index + 1;
                }
            });
        }
    }

    function setupModals() {
        // Edit banner button handler
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-banner-btn') || e.target.closest('.edit-banner-btn')) {
                const button = e.target.classList.contains('edit-banner-btn') ? e.target : e.target.closest('.edit-banner-btn');
                const bannerId = button.getAttribute('data-id');
                
                document.getElementById('edit-banner-form').action = `{{ url('home-page/banners') }}/${bannerId}`;
                document.getElementById('edit-title-input').value = button.getAttribute('data-title') || '';
                document.getElementById('edit-subtitle-input').value = button.getAttribute('data-subtitle') || '';
                document.getElementById('edit-button-text-input').value = button.getAttribute('data-button-text') || '';
                document.getElementById('edit-button-url-input').value = button.getAttribute('data-button-url') || '';
                document.getElementById('edit-image-alt-input').value = button.getAttribute('data-image-alt') || '';
                document.getElementById('edit-is-active').checked = button.getAttribute('data-is-active') === '1';
                document.getElementById('edit-current-image').src = button.getAttribute('data-image-url');
                
                // Update character counters
                updateCharacterCounter('edit-title-input', 'edit-title-count');
                updateCharacterCounter('edit-button-text-input', 'edit-button-text-count');
                
                const modal = new bootstrap.Modal(document.getElementById('edit-banner-modal'));
                modal.show();
            }
        });

        // Reset modals when closed
        document.getElementById('create-banner-modal').addEventListener('hidden.bs.modal', function() {
            const form = this.querySelector('form');
            form.reset();
            document.getElementById('create-image-preview').innerHTML = '';
            document.getElementById('create-title-count').textContent = '0';
            document.getElementById('create-button-text-count').textContent = '0';
            resetSubmitButton('create-banner-submit-btn', 'Create Banner');
        });

        document.getElementById('edit-banner-modal').addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('edit-banner-form');
            form.reset();
            document.getElementById('edit-image-preview').innerHTML = '';
            resetSubmitButton('edit-banner-submit-btn', 'Update Banner');
        });
    }

    function setupCharacterCounters() {
        setupCharacterCounter('create-title-input', 'create-title-count', 255);
        setupCharacterCounter('create-button-text-input', 'create-button-text-count', 100);
        setupCharacterCounter('edit-title-input', 'edit-title-count', 255);
        setupCharacterCounter('edit-button-text-input', 'edit-button-text-count', 100);
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
            
            const maxLength = inputId.includes('title') ? 255 : 100;
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
    }

    function setupImagePreviews() {
        const createImageInput = document.getElementById('create-image-input');
        const editImageInput = document.getElementById('edit-image-input');
        
        if (createImageInput) {
            createImageInput.addEventListener('change', function(e) {
                handleImagePreview(e, 'create-image-preview', createImageInput, 10);
            });
        }
        
        if (editImageInput) {
            editImageInput.addEventListener('change', function(e) {
                handleImagePreview(e, 'edit-image-preview', editImageInput, 10);
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

    function setupFormSubmissions() {
        // SEO Form
        const seoForm = document.getElementById('seo-form');
        const saveSeoBtn = document.getElementById('save-seo-btn');
        
        if (seoForm && saveSeoBtn) {
            seoForm.addEventListener('submit', function(e) {
                saveSeoBtn.disabled = true;
                saveSeoBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            });
        }

        // Create Banner Form
        const createBannerForm = document.querySelector('#create-banner-modal form');
        const createBannerSubmitBtn = document.getElementById('create-banner-submit-btn');

        if (createBannerForm && createBannerSubmitBtn) {
            createBannerForm.addEventListener('submit', function(e) {
                const imageInput = document.getElementById('create-image-input');
                if (!imageInput.files.length) {
                    e.preventDefault();
                    showAlert(imageInput, 'danger', 'Please select an image for the banner.');
                    return false;
                }

                createBannerSubmitBtn.disabled = true;
                createBannerSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
            });
        }

        // Edit Banner Form
        const editBannerForm = document.getElementById('edit-banner-form');
        const editBannerSubmitBtn = document.getElementById('edit-banner-submit-btn');

        if (editBannerForm && editBannerSubmitBtn) {
            editBannerForm.addEventListener('submit', function(e) {
                editBannerSubmitBtn.disabled = true;
                editBannerSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            });
        }
    }

    function resetSubmitButton(buttonId, originalText) {
        const button = document.getElementById(buttonId);
        if (button) {
            button.disabled = false;
            button.innerHTML = `<i class="ti ti-device-floppy me-1"></i>${originalText}`;
        }
    }

    // Clear image preview function
    window.clearImagePreview = function(previewId, inputId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewId).innerHTML = '';
    };
</script>
@endpush