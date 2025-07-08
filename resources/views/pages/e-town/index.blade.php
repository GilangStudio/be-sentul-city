@extends('layouts.main')

@section('title', 'E-Town App Management')

@push('styles')
<style>
    .mockup-preview {
        max-height: 400px;
        overflow: hidden;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
    
    .mockup-image {
        width: 100%;
        height: 300px;
        object-fit: contain;
        background-color: #fff;
    }
    
    .app-store-preview {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .app-store-badge {
        height: 60px;
        border-radius: 8px;
        transition: transform 0.2s ease;
    }
    
    .app-store-badge:hover {
        transform: scale(1.05);
    }
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }
    
    .content-preview {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">E-Town App Management</h2>
        <div class="page-subtitle">Manage E-Town mobile application page content</div>
    </div>
</div>
@endsection

@section('content')

{{-- Page Settings Form --}}
<form method="POST" action="{{ route('e-town.updateOrCreate') }}" enctype="multipart/form-data" id="page-form">
    @csrf
    <div class="row g-3">
        {{-- Left Column --}}
        <div class="col-lg-8">
            {{-- App Mockup Section --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-device-mobile me-2"></i>
                        App Mockup Settings
                    </h3>
                    @if($etownSection)
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
                        {{-- Current Mockup Preview --}}
                        @if($etownSection)
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Current App Mockup</label>
                                <div class="mockup-preview">
                                    <img src="{{ $etownSection->app_mockup_image_url }}" class="mockup-image" alt="Current App Mockup">
                                </div>
                                <small class="text-secondary mt-1 d-block">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Current mockup image
                                </small>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Mockup Form Fields --}}
                        <div class="{{ $etownSection ? 'col-md-8' : 'col-12' }}">
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ $etownSection ? 'Update App Mockup Image' : 'App Mockup Image' }}
                                    @if(!$etownSection)<span class="text-danger">*</span>@endif
                                </label>
                                <input type="file" class="form-control @error('app_mockup_image') is-invalid @enderror" 
                                       name="app_mockup_image" accept="image/*" {{ $etownSection ? '' : 'required' }} id="mockup-input">
                                @error('app_mockup_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <i class="ti ti-info-circle me-1"></i>
                                    @if($etownSection)Leave empty to keep current image. @endif
                                    Recommended: 400x800px (phone mockup), Max: 10MB (JPG, PNG, WebP)
                                </small>
                                <div class="mt-3" id="mockup-preview"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alt Text</label>
                                <input type="text" class="form-control @error('app_mockup_alt_text') is-invalid @enderror" 
                                       name="app_mockup_alt_text" value="{{ old('app_mockup_alt_text', $etownSection->app_mockup_alt_text ?? '') }}"
                                       placeholder="Enter image description for accessibility">
                                @error('app_mockup_alt_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    Describe the image for screen readers and SEO.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content Section --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-file-text me-2"></i>
                        Content Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('section_title') is-invalid @enderror" 
                               name="section_title" value="{{ old('section_title', $etownSection->section_title ?? 'E-Town, Feel Sentul City in Your Hand!') }}" 
                               required placeholder="Enter section title">
                        @error('section_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <span id="section-title-count">{{ strlen($etownSection->section_title ?? 'E-Town, Feel Sentul City in Your Hand!') }}</span>/255 characters
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="4" required
                                  placeholder="Enter app description and features...">{{ old('description', $etownSection->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Describe the app features and benefits for users.</small>
                    </div>
                </div>
            </div>

            {{-- App Store Links Section --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-download me-2"></i>
                        App Store Links
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ti ti-brand-google-play me-1"></i>
                                    Google Play Store URL
                                </label>
                                <input type="url" class="form-control @error('google_play_url') is-invalid @enderror" 
                                       name="google_play_url" value="{{ old('google_play_url', $etownSection->google_play_url ?? '') }}"
                                       placeholder="https://play.google.com/store/apps/details?id=...">
                                @error('google_play_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Link to Google Play Store</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ti ti-brand-apple me-1"></i>
                                    Apple App Store URL
                                </label>
                                <input type="url" class="form-control @error('app_store_url') is-invalid @enderror" 
                                       name="app_store_url" value="{{ old('app_store_url', $etownSection->app_store_url ?? '') }}"
                                       placeholder="https://apps.apple.com/app/...">
                                @error('app_store_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Link to Apple App Store</small>
                            </div>
                        </div>
                    </div>
                    
                    {{-- App Store Preview --}}
                    @if($etownSection && $etownSection->has_app_store_links)
                    <div class="mt-3">
                        <label class="form-label">Current Download Badges</label>
                        <div class="app-store-preview">
                            @if($etownSection->google_play_url)
                            <a href="{{ $etownSection->google_play_url }}" target="_blank">
                                <img src="https://play.google.com/intl/en_us/badges/static/images/badges/en_badge_web_generic.png" 
                                     alt="Get it on Google Play" class="app-store-badge" style="height: 57px;">
                            </a>
                            @endif
                            @if($etownSection->app_store_url)
                            <a href="{{ $etownSection->app_store_url }}" target="_blank">
                                <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" 
                                     alt="Download on the App Store" class="app-store-badge" style="height: 40px;">
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            {{-- Content Preview --}}
            {{-- <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-eye me-2"></i>
                        Content Preview
                    </h3>
                </div>
                <div class="card-body">
                    <div class="content-preview">
                        <h4 id="preview-title">{{ $etownSection->section_title ?? 'E-Town, Feel Sentul City in Your Hand!' }}</h4>
                        <p id="preview-description" class="mb-3">{{ $etownSection->description ?? 'Enter your app description here...' }}</p>
                        <div class="app-store-preview">
                            <img src="https://play.google.com/intl/en_us/badges/static/images/badges/en_badge_web_generic.png" 
                                 alt="Get it on Google Play" class="app-store-badge" style="height: 58px;">
                            <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" 
                                 alt="Download on the App Store" class="app-store-badge" style="height: 40px;">
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- Quick Tips --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-bulb me-2"></i>
                        Quick Tips
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="ti ti-device-mobile text-primary"></i>
                                </div>
                                <div class="col text-truncate">
                                    <div class="text-body d-block">Use phone mockup</div>
                                    <small class="d-block text-secondary text-truncate mt-n1">Show app in phone frame for better visual</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="ti ti-download text-green"></i>
                                </div>
                                <div class="col text-truncate">
                                    <div class="text-body d-block">Add store links</div>
                                    <small class="d-block text-secondary text-truncate mt-n1">Include both Google Play and App Store</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="ti ti-writing text-orange"></i>
                                </div>
                                <div class="col text-truncate">
                                    <div class="text-body d-block">Clear description</div>
                                    <small class="d-block text-secondary text-truncate mt-n1">Highlight main app features</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="col-12">
            <div class="card">
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        @if($etownSection)
                        <div class="d-flex align-items-center text-secondary">
                            <i class="ti ti-clock me-2"></i>
                            <div>
                                <small class="fw-medium">Last saved:</small>
                                <small class="d-block">{{ $etownSection->updated_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        @else
                        <div class="text-secondary">
                            <i class="ti ti-info-circle me-1"></i>
                            Create your E-Town section settings
                        </div>
                        @endif
                        
                        <div class="btn-list">
                            {{-- @if($etownSection)
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#delete-modal">
                                <i class="ti ti-trash me-1"></i>
                                Delete Settings
                            </button>
                            @endif --}}
                            <button type="submit" class="btn btn-primary" id="save-page-btn">
                                <i class="ti ti-device-floppy me-1"></i>
                                {{ $etownSection ? 'Update' : 'Create' }} Section
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
        setupContentPreview();
        setupFormSubmission();
    });

    function setupImagePreviews() {
        const mockupInput = document.getElementById('mockup-input');
        
        if (mockupInput) {
            mockupInput.addEventListener('change', function(e) {
                handleImagePreview(e, 'mockup-preview', mockupInput, 10);
            });
        }
    }

    function setupCharacterCounters() {
        setupCharacterCounter('section_title', 'section-title-count', 255);
        setupCharacterCounter('meta-title-input', 'meta-title-count', 255);
        setupCharacterCounter('meta-desc-input', 'meta-desc-count', 500);
        setupCharacterCounter('meta-keywords-input', 'meta-keywords-count', 255);
    }

    function setupCharacterCounter(inputName, countId, maxLength) {
        const input = document.querySelector(`[name="${inputName}"]`) || document.getElementById(inputName.replace('_', '-') + '-input');
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

    function setupContentPreview() {
        const titleInput = document.querySelector('[name="section_title"]');
        const descriptionInput = document.querySelector('[name="description"]');
        const previewTitle = document.getElementById('preview-title');
        const previewDescription = document.getElementById('preview-description');
        
        if (titleInput && previewTitle) {
            titleInput.addEventListener('input', function() {
                previewTitle.textContent = this.value || 'E-Town, Feel Sentul City in Your Hand!';
            });
        }
        
        if (descriptionInput && previewDescription) {
            descriptionInput.addEventListener('input', function() {
                previewDescription.textContent = this.value || 'Enter your app description here...';
            });
        }
    }

    function setupFormSubmission() {
        const pageForm = document.getElementById('page-form');
        const savePageBtn = document.getElementById('save-page-btn');
        
        if (pageForm && savePageBtn) {
            pageForm.addEventListener('submit', function(e) {
                savePageBtn.disabled = true;
                savePageBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                pageForm.classList.add('loading');
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
                        <img src="${e.target.result}" class="card-img-top" style="height: 300px; object-fit: contain; background-color: #fff;">
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

    // Clear image preview function
    window.clearImagePreview = function(previewId, inputId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewId).innerHTML = '';
    };
</script>
@endpush