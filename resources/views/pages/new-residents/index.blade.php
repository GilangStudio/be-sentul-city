@extends('layouts.main')

@section('title', 'New Residents Management')

@push('styles')
<style>
    .info-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">New Residents Management</h2>
</div>
@endsection

@section('content')

{{-- Banner Settings Form --}}
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-photo me-2"></i>
                Banner & Page Settings
            </h3>
            @if($newResidentsPage)
            <div class="card-actions">
                <span class="badge bg-green-lt">
                    <i class="ti ti-check me-1"></i>
                    Configured
                </span>
            </div>
            @endif
        </div>
        
        <form method="POST" action="{{ route('new-residents.updateOrCreate') }}" enctype="multipart/form-data" id="banner-form">
            @csrf
            <div class="card-body">
                <div class="row">
                    {{-- Current Banner Preview --}}
                    @if($newResidentsPage)
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Current Banner</label>
                            <div class="banner-preview">
                                <img src="{{ $newResidentsPage->banner_image_url }}" class="banner-image" alt="Current Banner">
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Banner Form Fields --}}
                    <div class="{{ $newResidentsPage ? 'col-md-8' : 'col-12' }}">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ $newResidentsPage ? 'Update Banner Image' : 'Banner Image' }}
                                @if(!$newResidentsPage)<span class="text-danger">*</span>@endif
                            </label>
                            <input type="file" class="form-control @error('banner_image') is-invalid @enderror" 
                                   name="banner_image" accept="image/*" {{ $newResidentsPage ? '' : 'required' }} id="banner-input">
                            @error('banner_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                <i class="ti ti-info-circle me-1"></i>
                                @if($newResidentsPage)Leave empty to keep current image. @endif
                                Recommended: 1920x800px, Max: 10MB (JPG, PNG, WebP)
                            </small>
                            <div class="mt-3" id="banner-preview"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Banner Alt Text</label>
                            <input type="text" class="form-control @error('banner_alt_text') is-invalid @enderror" 
                                   name="banner_alt_text" value="{{ old('banner_alt_text', $newResidentsPage->banner_alt_text ?? '') }}"
                                   placeholder="Enter image description for accessibility">
                            @error('banner_alt_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                       {{ old('is_active', $newResidentsPage->is_active ?? true) ? 'checked' : '' }}>
                                <span class="form-check-label">Active Status</span>
                            </label>
                            <small class="form-hint d-block">
                                <i class="ti ti-info-circle me-1"></i>
                                Make the new residents page publicly accessible.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Neighborhood Guide Settings --}}
            <div class="card-body border-top">
                <h4 class="card-title mb-3">
                    <i class="ti ti-map me-2"></i>
                    Neighborhood Guide Section
                </h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Section Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('neighborhood_title') is-invalid @enderror" 
                                   name="neighborhood_title" value="{{ old('neighborhood_title', $newResidentsPage->neighborhood_title ?? 'Neighborhood Guide') }}" 
                                   required placeholder="e.g., Neighborhood Guide">
                            @error('neighborhood_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Section Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('neighborhood_image') is-invalid @enderror" 
                                   name="neighborhood_image" accept="image/*" {{ $newResidentsPage ? '' : 'required' }} id="neighborhood-input">
                            @error('neighborhood_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                @if($newResidentsPage)Leave empty to keep current image. @endif
                                Max: 5MB (JPG, PNG, WebP)
                            </small>
                            <div class="mt-3" id="neighborhood-preview"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Section Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('neighborhood_description') is-invalid @enderror" 
                                      name="neighborhood_description" id="editor" rows="8"
                                      placeholder="Enter neighborhood guide description...">{{ old('neighborhood_description', $newResidentsPage->neighborhood_description ?? '') }}</textarea>
                            @error('neighborhood_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback" id="neighborhood-error" style="display: none;"></div>
                            <small class="form-hint">Describe the neighborhood features and benefits for new residents.</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Image Alt Text</label>
                            <input type="text" class="form-control @error('neighborhood_image_alt_text') is-invalid @enderror" 
                                   name="neighborhood_image_alt_text" value="{{ old('neighborhood_image_alt_text', $newResidentsPage->neighborhood_image_alt_text ?? '') }}"
                                   placeholder="Enter image description for accessibility">
                            @error('neighborhood_image_alt_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                Describe the image for screen readers and SEO.
                            </small>
                        </div>
                    </div>
                    @if($newResidentsPage && $newResidentsPage->neighborhood_image_url)
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div class="banner-preview">
                                <img src="{{ $newResidentsPage->neighborhood_image_url }}" class="banner-image" alt="Current Neighborhood Image">
                            </div>
                        </div>
                    </div>
                    @endif
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
                                   name="meta_title" value="{{ old('meta_title', $newResidentsPage->meta_title ?? '') }}" 
                                   placeholder="Enter title that will appear in search results"
                                   maxlength="255" id="meta-title-input">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                <span id="meta-title-count">{{ strlen($newResidentsPage->meta_title ?? '') }}</span>/255 characters. 
                                Leave empty to use default "New Residents".
                            </small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                      name="meta_description" rows="3" 
                                      placeholder="Enter description that will appear in search results"
                                      maxlength="500" id="meta-desc-input">{{ old('meta_description', $newResidentsPage->meta_description ?? '') }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                <span id="meta-desc-count">{{ strlen($newResidentsPage->meta_description ?? '') }}</span>/500 characters.
                            </small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                   name="meta_keywords" value="{{ old('meta_keywords', $newResidentsPage->meta_keywords ?? '') }}" 
                                   placeholder="keywords separated by commas. e.g: new residents, information, practical guide"
                                   maxlength="255" id="meta-keywords-input">
                            @error('meta_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                <span id="meta-keywords-count">{{ strlen($newResidentsPage->meta_keywords ?? '') }}</span>/255 characters.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer text-end">
                <div class="btn-list">
                    {{-- @if($newResidentsPage)
                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                        <i class="ti ti-trash me-1"></i>
                        Delete Settings
                    </button>
                    @endif --}}
                    <button type="submit" class="btn btn-primary" id="save-banner-btn">
                        <i class="ti ti-device-floppy me-1"></i>
                        {{ $newResidentsPage ? 'Update' : 'Create' }} Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Content Management Cards --}}
@if($newResidentsPage)
<div class="col-12">
    <div class="row w-100">
        {{-- Practical Info Categories --}}
        <div class="col-md-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="bg-primary-lt p-3 rounded">
                                <i class="ti ti-map-pins icon icon-lg text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="card-title mb-1">Practical Info Categories</h3>
                            <p class="text-secondary mb-0">Manage place categories</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h2 mb-1">{{ $categories->count() }}</div>
                                <small class="text-secondary">Total Categories</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h2 mb-1 text-primary">{{ $categories->where('is_active', true)->count() }}</div>
                                <small class="text-secondary">Active</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('new-residents.categories.index') }}" class="btn btn-primary w-100">
                            <i class="ti ti-settings me-1"></i>
                            Manage Categories
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Places Management --}}
        <div class="col-md-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="bg-success-lt p-3 rounded">
                                <i class="ti ti-building-store icon icon-lg text-success"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="card-title mb-1">Places</h3>
                            <p class="text-secondary mb-0">Manage places information</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h2 mb-1">{{ $categories->sum('places_count') }}</div>
                                <small class="text-secondary">Total Places</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h2 mb-1 text-success">{{ $categories->sum(function($cat) { return $cat->places()->active()->count(); }) }}</div>
                                <small class="text-secondary">Active</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        @if($categories->where('is_active', true)->count() > 0)
                        <a href="{{ route('new-residents.places.index') }}" class="btn btn-success w-100">
                            <i class="ti ti-map-pin me-1"></i>
                            Manage Places
                        </a>
                        @else
                        <a href="{{ route('new-residents.categories.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="ti ti-alert-triangle me-1"></i>
                            Create Categories First
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Transportation Items --}}
        <div class="col-md-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="bg-warning-lt p-3 rounded">
                                <i class="ti ti-car icon icon-lg text-warning"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="card-title mb-1">Transportation</h3>
                            <p class="text-secondary mb-0">Manage transportation items</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h2 mb-1">{{ $transportationItems->count() }}</div>
                                <small class="text-secondary">Total Items</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h2 mb-1 text-warning">{{ $transportationItems->where('is_active', true)->count() }}</div>
                                <small class="text-secondary">Active</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('new-residents.transportation.index') }}" class="btn btn-warning w-100">
                            <i class="ti ti-bus me-1"></i>
                            Manage Transportation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Delete Confirmation Modal --}}
<div class="modal modal-blur fade" id="delete-confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <form class="modal-content" id="delete-form" method="POST" action="{{ route('new-residents.destroy') }}">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <i class="ti ti-alert-triangle icon mb-2 text-danger icon-lg"></i>
                <h3>Are you sure?</h3>
                <div class="text-secondary">
                    This will delete all new residents page settings including the banner and neighborhood images. This process cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">Cancel</button>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-danger w-100">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
@include('components.scripts.wysiwyg')
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
        setupFormSubmission();
    });

    function confirmDelete() {
        const modal = new bootstrap.Modal(document.getElementById('delete-confirmation-modal'));
        modal.show();
    }

    function setupImagePreviews() {
        const bannerInput = document.getElementById('banner-input');
        const bannerPreview = document.getElementById('banner-preview');
        const neighborhoodInput = document.getElementById('neighborhood-input');
        const neighborhoodPreview = document.getElementById('neighborhood-preview');
        
        if (bannerInput) {
            bannerInput.addEventListener('change', function(e) {
                handleImagePreview(e, bannerPreview, bannerInput, 10);
            });
        }
        
        if (neighborhoodInput) {
            neighborhoodInput.addEventListener('change', function(e) {
                handleImagePreview(e, neighborhoodPreview, neighborhoodInput, 5);
            });
        }
    }

    function handleImagePreview(event, previewContainer, inputElement, maxSizeMB) {
        const file = event.target.files[0];
        if (file) {
            // Validate file size
            const maxSize = maxSizeMB * 1024 * 1024;
            if (file.size > maxSize) {
                showAlert(inputElement, 'danger', `File size too large. Maximum ${maxSizeMB}MB allowed.`);
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
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearImagePreview('${previewContainer.id}', '${inputElement.id}')">
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

    function setupCharacterCounters() {
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

    function setupFormSubmission() {
        const bannerForm = document.getElementById('banner-form');
        const saveBannerBtn = document.getElementById('save-banner-btn');
        
        if (bannerForm && saveBannerBtn) {
            bannerForm.addEventListener('submit', function(e) {
                // Validate neighborhood description from editor
                const editorContent = hugeRTE.get('editor').getContent();
                const neighborhoodError = document.getElementById('neighborhood-error');
                
                // Clear previous errors
                neighborhoodError.style.display = 'none';
                
                // Check if content is empty
                if (!editorContent.trim() || editorContent.trim() === '<p></p>' || editorContent.trim() === '<p><br></p>') {
                    e.preventDefault();
                    neighborhoodError.textContent = 'Neighborhood description is required.';
                    neighborhoodError.style.display = 'block';
                    
                    // Scroll to editor
                    document.getElementById('editor').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    
                    return false;
                }
                
                saveBannerBtn.disabled = true;
                saveBannerBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                bannerForm.classList.add('loading');
            });
        }
    }

    // Clear image preview function
    window.clearImagePreview = function(previewId, inputId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewId).innerHTML = '';
    };
</script>
@endpush