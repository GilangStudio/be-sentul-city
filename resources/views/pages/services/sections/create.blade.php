@extends('layouts.main')

@section('title', 'Create Service Section')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">Create Service Section</h2>
    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Back to Services
    </a>
</div>
@endsection

@section('content')

<form action="{{ route('services.sections.store') }}" method="POST" enctype="multipart/form-data" id="section-form">
    @csrf
    <div class="row g-3">
        {{-- Main Content --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-layout-grid-add me-2"></i>
                        Section Content
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Section Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title') }}" required
                                       placeholder="Enter section title (e.g., Building Control, Security)">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <span id="title-count">0</span>/255 characters
                                </small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Section Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" rows="8" required
                                          placeholder="Enter detailed description of this service...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Describe the service features and benefits in detail.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Section Image --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Section Image
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               name="image" accept="image/*" required id="image-input">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle me-1"></i>
                            Recommended: 1200x800px, Max: 5MB (JPG, PNG, WebP)
                        </small>
                        <div class="mt-3" id="image-preview"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Alt Text</label>
                        <input type="text" class="form-control @error('image_alt_text') is-invalid @enderror" 
                               name="image_alt_text" value="{{ old('image_alt_text') }}"
                               placeholder="Enter image description for accessibility">
                        @error('image_alt_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            Describe the image for screen readers and SEO.
                        </small>
                    </div>
                </div>
            </div>

            {{-- Layout Settings --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-layout me-2"></i>
                        Layout Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Section Layout <span class="text-danger">*</span></label>
                        <div class="form-selectgroup form-selectgroup-boxes">
                            <label class="form-selectgroup-item">
                                <input type="radio" name="layout" value="image_left" class="form-selectgroup-input" 
                                       {{ old('layout', 'image_left') === 'image_left' ? 'checked' : '' }}>
                                <span class="form-selectgroup-label d-flex align-items-center p-3">
                                    <span class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </span>
                                    <span class="form-selectgroup-label-content">
                                        <i class="ti ti-layout-align-left icon mb-2"></i>
                                        <span class="form-selectgroup-title strong mb-1">Image Left</span>
                                        <span class="d-block text-secondary">Image on left, text on right</span>
                                    </span>
                                </span>
                            </label>
                            <label class="form-selectgroup-item mt-0 mt-md-2">
                                <input type="radio" name="layout" value="image_right" class="form-selectgroup-input" 
                                       {{ old('layout') === 'image_right' ? 'checked' : '' }}>
                                <span class="form-selectgroup-label d-flex align-items-center p-3">
                                    <span class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </span>
                                    <span class="form-selectgroup-label-content">
                                        <i class="ti ti-layout-align-right icon mb-2"></i>
                                        <span class="form-selectgroup-title strong mb-1">Image Right</span>
                                        <span class="d-block text-secondary">Text on left, image on right</span>
                                    </span>
                                </span>
                            </label>
                        </div>
                        @error('layout')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', '1') ? 'checked' : '' }}>
                            <span class="form-check-label">Active Status</span>
                        </label>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-hint d-block">
                            <i class="ti ti-info-circle me-1"></i>
                            Only active sections will be displayed on the services page.
                        </small>
                    </div>
                </div>
            </div>

            {{-- Layout Preview --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-eye me-2"></i>
                        Layout Preview
                    </h3>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div id="layout-preview" class="border rounded p-3" style="background-color: #f8f9fa;">
                            <div class="align-items-center" id="preview-image-left">
                                <div class="bg-secondary rounded me-2" style="width: 40px; height: 30px;"></div>
                                <div class="flex-grow-1">
                                    <div class="bg-primary rounded mb-1" style="height: 8px; width: 80%;"></div>
                                    <div class="bg-secondary rounded" style="height: 6px; width: 60%;"></div>
                                </div>
                            </div>
                            <div class="align-items-center" id="preview-image-right" style="display: none;">
                                <div class="flex-grow-1 me-2">
                                    <div class="bg-primary rounded mb-1" style="height: 8px; width: 80%;"></div>
                                    <div class="bg-secondary rounded" style="height: 6px; width: 60%;"></div>
                                </div>
                                <div class="bg-secondary rounded" style="width: 40px; height: 30px;"></div>
                            </div>
                        </div>
                        <small class="text-secondary mt-2 d-block">
                            Preview of how the section will appear on the page
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="col-12">
            <div class="card">
                <div class="card-footer bg-transparent text-end">
                    <div class="d-flex">
                        <a href="{{ route('services.index') }}" class="btn btn-link">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary ms-auto" id="submit-btn">
                            <i class="ti ti-device-floppy me-1"></i>
                            Create Section
                        </button>
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
    document.addEventListener('DOMContentLoaded', function() {
        setupImagePreview();
        setupCharacterCounter();
        setupLayoutPreview();
        setupFormSubmission();
    });

    function setupImagePreview() {
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');
        
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    showAlert(imageInput, 'danger', 'File size too large. Maximum 5MB allowed.');
                    imageInput.value = '';
                    return;
                }

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    showAlert(imageInput, 'danger', 'Please select a valid image file.');
                    imageInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `
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
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearImagePreview()">
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
                imagePreview.innerHTML = '';
            }
        });
    }

    function setupCharacterCounter() {
        const titleInput = document.querySelector('input[name="title"]');
        const titleCount = document.getElementById('title-count');
        
        titleInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            titleCount.textContent = currentLength;
            
            const percentage = (currentLength / 255) * 100;
            const parent = titleCount.parentElement;
            
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

    function setupLayoutPreview() {
        const layoutRadios = document.querySelectorAll('input[name="layout"]');
        const previewImageLeft = document.getElementById('preview-image-left');
        const previewImageRight = document.getElementById('preview-image-right');
        
        layoutRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'image_left') {
                    previewImageLeft.style.display = 'flex';
                    previewImageRight.style.display = 'none';
                } else {
                    previewImageLeft.style.display = 'none';
                    previewImageRight.style.display = 'flex';
                }
            });
        });
        
        // Set initial preview
        const checkedLayout = document.querySelector('input[name="layout"]:checked');
        if (checkedLayout) {
            checkedLayout.dispatchEvent(new Event('change'));
        }
    }

    function setupFormSubmission() {
        const form = document.getElementById('section-form');
        const submitBtn = document.getElementById('submit-btn');
        
        form.addEventListener('submit', function(e) {
            // Validate image
            const imageInput = document.getElementById('image-input');
            if (!imageInput.files.length) {
                e.preventDefault();
                showAlert(imageInput, 'danger', 'Please select an image for the section.');
                imageInput.focus();
                return false;
            }
            
            // Validate title
            const titleInput = document.querySelector('input[name="title"]');
            if (!titleInput.value.trim()) {
                e.preventDefault();
                titleInput.classList.add('is-invalid');
                titleInput.focus();
                showAlert(titleInput, 'danger', 'Section title is required.');
                return false;
            }
            
            // Validate description
            const descriptionInput = document.querySelector('textarea[name="description"]');
            if (!descriptionInput.value.trim()) {
                e.preventDefault();
                descriptionInput.classList.add('is-invalid');
                descriptionInput.focus();
                showAlert(descriptionInput, 'danger', 'Section description is required.');
                return false;
            }
            
            // Add loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating Section...';
            form.classList.add('loading');
        });
        
        // Clear validation errors on input
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                // Remove alert messages
                const alerts = document.querySelectorAll('.alert-custom');
                alerts.forEach(alert => alert.remove());
            });
        });
    }

    // Clear image preview function
    window.clearImagePreview = function() {
        document.getElementById('image-input').value = '';
        document.getElementById('image-preview').innerHTML = '';
    };
</script>
@endpush