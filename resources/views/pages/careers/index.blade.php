@extends('layouts.main')

@section('title', 'Career Management')

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
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Career Management</h2>
        <div class="page-subtitle">Manage career page content and job positions</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('careers.positions.index') }}" class="btn btn-outline-primary">
            <i class="ti ti-briefcase me-1"></i> Career Positions
        </a>
        @if($careerPage && $positionsCount > 0)
        <a href="{{ route('careers.positions.index') }}" class="btn btn-success">
            <i class="ti ti-eye me-1"></i> Manage Positions ({{ $positionsCount }})
        </a>
        @endif
    </div>
</div>
@endsection

@section('content')

{{-- Page Settings Form --}}
<form method="POST" action="{{ route('careers.updateOrCreate') }}" enctype="multipart/form-data" id="page-form">
    @csrf
    <div class="row g-3">
        {{-- Left Column --}}
        <div class="col-lg-8">
            {{-- Banner Section --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Banner Settings
                    </h3>
                    @if($careerPage)
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
                        {{-- Current Banner Preview --}}
                        @if($careerPage)
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Current Banner</label>
                                <div class="banner-preview">
                                    <img src="{{ $careerPage->banner_image_url }}" class="banner-image" alt="Current Banner">
                                </div>
                                <small class="text-secondary mt-1 d-block">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Current banner image
                                </small>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Banner Form Fields --}}
                        <div class="{{ $careerPage ? 'col-md-8' : 'col-12' }}">
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ $careerPage ? 'Update Banner Image' : 'Banner Image' }}
                                    @if(!$careerPage)<span class="text-danger">*</span>@endif
                                </label>
                                <input type="file" class="form-control @error('banner_image') is-invalid @enderror" 
                                       name="banner_image" accept="image/*" {{ $careerPage ? '' : 'required' }} id="banner-input">
                                @error('banner_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <i class="ti ti-info-circle me-1"></i>
                                    @if($careerPage)Leave empty to keep current image. @endif
                                    Recommended: 1920x800px, Max: 10MB (JPG, PNG, WebP)
                                </small>
                                <div class="mt-3" id="banner-preview"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Banner Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('banner_title') is-invalid @enderror" 
                                       name="banner_title" value="{{ old('banner_title', $careerPage->banner_title ?? 'Career Opportunities') }}" 
                                       required placeholder="Enter banner title">
                                @error('banner_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <span id="banner-title-count">{{ strlen($careerPage->banner_title ?? 'Career Opportunities') }}</span>/255 characters
                                </small>
                            </div>
                            
                            {{-- <div class="mb-3">
                                <label class="form-label">Banner Subtitle</label>
                                <textarea class="form-control @error('banner_subtitle') is-invalid @enderror" 
                                          name="banner_subtitle" rows="3"
                                          placeholder="Enter banner subtitle (optional)">{{ old('banner_subtitle', $careerPage->banner_subtitle ?? '') }}</textarea>
                                @error('banner_subtitle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Optional subtitle text that appears below the main title.</small>
                            </div> --}}
                            
                            <div class="mb-3">
                                <label class="form-label">Alt Text</label>
                                <input type="text" class="form-control @error('banner_alt_text') is-invalid @enderror" 
                                       name="banner_alt_text" value="{{ old('banner_alt_text', $careerPage->banner_alt_text ?? '') }}"
                                       placeholder="Enter image description for accessibility">
                                @error('banner_alt_text')
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

            {{-- Contact Information --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-phone me-2"></i>
                        HR Contact Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">HR Email</label>
                                <input type="email" class="form-control @error('hr_email') is-invalid @enderror" 
                                       name="hr_email" value="{{ old('hr_email', $careerPage->hr_email ?? '') }}"
                                       placeholder="hr@company.com">
                                @error('hr_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Email address for career-related inquiries</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">HR Phone</label>
                                <input type="text" class="form-control @error('hr_phone') is-invalid @enderror" 
                                       name="hr_phone" value="{{ old('hr_phone', $careerPage->hr_phone ?? '') }}"
                                       placeholder="+62 21 1234 5678">
                                @error('hr_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Phone number for career-related inquiries</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO Settings --}}
            @include('components.seo-meta-form', ['data' => $careerPage, 'type' => $careerPage ? 'edit' : 'create'])
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            {{-- Page Settings --}}
            <div class="card">
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
                                   {{ old('is_active', $careerPage->is_active ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Active Status</span>
                        </label>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-hint d-block">
                            <i class="ti ti-info-circle me-1"></i>
                            Only active pages will be publicly accessible on the website.
                        </small>
                        @if($careerPage && $careerPage->is_active)
                        <small class="text-success d-block mt-1">
                            <i class="ti ti-check me-1"></i>
                            This page is currently active and visible to visitors.
                        </small>
                        @elseif($careerPage)
                        <small class="text-warning d-block mt-1">
                            <i class="ti ti-alert-triangle me-1"></i>
                            This page is currently inactive and hidden from visitors.
                        </small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Statistics Card --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-chart-bar me-2"></i>
                        Career Statistics
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h1 text-primary">{{ $positionsCount }}</div>
                                <div class="text-secondary">Job Positions</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h1 text-success">{{ $applicationsCount }}</div>
                                <div class="text-secondary">Applications</div>
                            </div>
                        </div>
                    </div>
                    @if($pendingApplicationsCount > 0)
                    <div class="mt-3">
                        <div class="alert alert-warning mb-0">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-alert-triangle me-2"></i>
                                </div>
                                <div>
                                    <strong>{{ $pendingApplicationsCount }}</strong> applications pending review
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="mt-3">
                        <div class="btn-list w-100">
                            <a href="{{ route('careers.positions.index') }}" class="btn btn-outline-primary w-100">
                                <i class="ti ti-briefcase me-1"></i>
                                Manage Career Positions
                            </a>
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
                        @if($careerPage)
                        <div class="d-flex align-items-center text-secondary">
                            <i class="ti ti-clock me-2"></i>
                            <div>
                                <small class="fw-medium">Last saved:</small>
                                <small class="d-block">{{ $careerPage->updated_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        @else
                        <div class="text-secondary">
                            <i class="ti ti-info-circle me-1"></i>
                            Create your Career page settings
                        </div>
                        @endif
                        
                        <div class="btn-list">
                            {{-- @if($careerPage)
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#delete-modal">
                                <i class="ti ti-trash me-1"></i>
                                Delete Settings
                            </button>
                            @endif --}}
                            <button type="submit" class="btn btn-primary" id="save-page-btn">
                                <i class="ti ti-device-floppy me-1"></i>
                                {{ $careerPage ? 'Update' : 'Save' }} Page Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Delete Settings Modal --}}
@if($careerPage)
<div class="modal modal-blur fade" id="delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" action="{{ route('careers.destroy') }}">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <i class="ti ti-alert-triangle icon mb-2 text-danger icon-lg"></i>
                <h3>Are you sure?</h3>
                <div class="text-secondary">
                    Do you really want to delete the career page settings? This process cannot be undone.
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
@endif

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
        setupFormSubmission();
    });

    function setupImagePreviews() {
        const bannerInput = document.getElementById('banner-input');
        
        if (bannerInput) {
            bannerInput.addEventListener('change', function(e) {
                handleImagePreview(e, 'banner-preview', bannerInput, 10);
            });
        }
    }

    function setupCharacterCounters() {
        setupCharacterCounter('banner_title', 'banner-title-count', 255);
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

    // Clear image preview function
    window.clearImagePreview = function(previewId, inputId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewId).innerHTML = '';
    };
</script>
@endpush