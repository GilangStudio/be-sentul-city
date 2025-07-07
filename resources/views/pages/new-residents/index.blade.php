@extends('layouts.main')

@section('title', 'New Residents Management')

@push('styles')
<style>
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
    
    .image-preview-container {
        max-height: 300px;
        overflow: hidden;
        border-radius: 8px;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">New Residents Management</h2>
        <div class="page-subtitle">Manage your new residents page content and practical information</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('new-residents.categories.index') }}" class="btn btn-outline-primary">
            <i class="ti ti-folder me-1"></i> Categories
        </a>
        <a href="{{ route('new-residents.places.index') }}" class="btn btn-outline-success">
            <i class="ti ti-map-pin me-1"></i> Places
        </a>
        <a href="{{ route('new-residents.transportation.index') }}" class="btn btn-outline-info">
            <i class="ti ti-car me-1"></i> Transportation
        </a>
    </div>
</div>
@endsection

@section('content')

<form method="POST" action="{{ route('new-residents.updateOrCreate') }}" enctype="multipart/form-data" id="main-form">
    @csrf
    <div class="row g-3">
        
        {{-- Left Column --}}
        <div class="col-lg-8">
            
            {{-- Banner Section --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Banner Section
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
                <div class="card-body">
                    <div class="row">
                        @if($newResidentsPage && $newResidentsPage->banner_image_url)
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Current Banner</label>
                                <div class="banner-preview">
                                    <img src="{{ $newResidentsPage->banner_image_url }}" class="banner-image" alt="Current Banner">
                                </div>
                                <small class="text-secondary mt-1 d-block">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Current banner image
                                </small>
                            </div>
                        </div>
                        @endif
                        
                        <div class="{{ $newResidentsPage && $newResidentsPage->banner_image_url ? 'col-md-8' : 'col-12' }}">
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
                                <small class="form-hint">
                                    Describe the banner image for screen readers and SEO.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Neighborhood Guide Section --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-map me-2"></i>
                        Neighborhood Guide Section
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
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
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Section Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('neighborhood_description') is-invalid @enderror" 
                                          name="neighborhood_description" id="editor" rows="6" required
                                          placeholder="Enter neighborhood guide description...">{{ old('neighborhood_description', $newResidentsPage->neighborhood_description ?? '') }}</textarea>
                                @error('neighborhood_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="neighborhood-description-error" style="display: none;"></div>
                                <small class="form-hint">
                                    Describe the neighborhood features and benefits for new residents.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO Settings --}}
            @include('components.seo-meta-form', ['data' => $newResidentsPage, 'type' => $newResidentsPage ? 'edit' : 'create'])
            
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            
            {{-- Neighborhood Guide Image --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Neighborhood Guide Image
                    </h3>
                </div>
                <div class="card-body">
                    @if($newResidentsPage && $newResidentsPage->neighborhood_image_url)
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div class="image-preview-container">
                            <img src="{{ $newResidentsPage->neighborhood_image_url }}" class="img-fluid rounded" alt="Neighborhood Image">
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label">
                            Neighborhood Image @if(!$newResidentsPage)<span class="text-danger">*</span>@endif
                        </label>
                        <input type="file" class="form-control @error('neighborhood_image') is-invalid @enderror" 
                               name="neighborhood_image" accept="image/*" {{ $newResidentsPage ? '' : 'required' }} id="neighborhood-input">
                        @error('neighborhood_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            @if($newResidentsPage)Leave empty to keep current image. @endif
                            Recommended: 1200x800px, Max: 5MB
                        </small>
                        <div class="mt-3" id="neighborhood-preview"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image Alt Text</label>
                        <input type="text" class="form-control @error('neighborhood_image_alt_text') is-invalid @enderror" 
                               name="neighborhood_image_alt_text" value="{{ old('neighborhood_image_alt_text', $newResidentsPage->neighborhood_image_alt_text ?? '') }}"
                               placeholder="Enter image description">
                        @error('neighborhood_image_alt_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Page Settings --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-settings me-2"></i>
                        Page Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', $newResidentsPage->is_active ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Active Status</span>
                        </label>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-hint d-block">
                            <i class="ti ti-info-circle me-1"></i>
                            Only active pages will be publicly accessible on the website.
                        </small>
                        @if($newResidentsPage && $newResidentsPage->is_active)
                        <small class="text-success d-block mt-1">
                            <i class="ti ti-check me-1"></i>
                            This page is currently active and visible to visitors.
                        </small>
                        @elseif($newResidentsPage)
                        <small class="text-warning d-block mt-1">
                            <i class="ti ti-alert-triangle me-1"></i>
                            This page is currently inactive and hidden from visitors.
                        </small>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>

        {{-- Submit Button --}}
        <div class="col-12">
            <div class="card">
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        @if($newResidentsPage)
                        <div class="d-flex align-items-center text-secondary">
                            <i class="ti ti-clock me-2"></i>
                            <div>
                                <small class="fw-medium">Last saved:</small>
                                <small class="d-block">{{ $newResidentsPage->updated_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        @else
                        <div class="text-secondary">
                            <i class="ti ti-info-circle me-1"></i>
                            Create your New Residents page settings
                        </div>
                        @endif
                        
                        <div class="btn-list">
                            <button type="submit" class="btn btn-primary" id="save-main-btn">
                                <i class="ti ti-device-floppy me-1"></i>
                                {{ $newResidentsPage ? 'Update New Residents Page' : 'Create New Residents Page' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

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
        setupFormSubmission();
    });

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
                    <div class="card border-success">
                        <img src="${e.target.result}" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-fill">
                                    <h6 class="card-title mb-1">${file.name}</h6>
                                    <small class="text-secondary d-block">
                                        ${(file.size / 1024 / 1024).toFixed(2)} MB
                                    </small>
                                    <small class="text-success">
                                        <i class="ti ti-check me-1"></i>
                                        Ready to upload
                                    </small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="clearImagePreview('${previewContainer.id}', '${inputElement.id}')">
                                    <i class="ti ti-x"></i>
                                </button>
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

    function setupFormSubmission() {
        const mainForm = document.getElementById('main-form');
        const saveMainBtn = document.getElementById('save-main-btn');
        
        if (mainForm && saveMainBtn) {
            mainForm.addEventListener('submit', function(e) {
                // Validate neighborhood description from editor
                try {
                    const neighborhoodEditorContent = hugeRTE.get('editor').getContent();
                    const neighborhoodError = document.getElementById('neighborhood-description-error');
                    
                    if (!neighborhoodEditorContent.trim() || neighborhoodEditorContent.trim() === '<p></p>' || neighborhoodEditorContent.trim() === '<p><br></p>') {
                        e.preventDefault();
                        neighborhoodError.textContent = 'Neighborhood description is required.';
                        neighborhoodError.style.display = 'block';
                        document.getElementById('editor').scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    } else {
                        neighborhoodError.style.display = 'none';
                    }
                } catch (err) {
                    console.warn('Neighborhood editor validation error:', err);
                }

                // If validation passed, show loading state
                saveMainBtn.disabled = true;
                saveMainBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                mainForm.classList.add('loading');
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