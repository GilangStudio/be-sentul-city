@extends('layouts.main')

@section('title', 'Edit Place')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Edit Place</h2>
        <div class="page-subtitle">{{ $place->name }}</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('new-residents.places.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Places
        </a>
    </div>
</div>
@endsection

@section('content')

<form action="{{ route('new-residents.places.update', $place) }}" method="POST" enctype="multipart/form-data" id="place-form">
    @csrf
    @method('PUT')
    <div class="row g-3">
        {{-- Main Content --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-edit me-2"></i>
                        Place Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required id="category-select">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $place->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} - {{ $category->title }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    Choose the appropriate category for this place.
                                </small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Place Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $place->name) }}" required
                                       placeholder="e.g., Masjid Taqha Islamic Center">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <span id="name-count">{{ strlen($place->name ?? '') }}</span>/255 characters
                                </small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          name="address" rows="3" required
                                          placeholder="Enter complete address">{{ old('address', $place->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Provide the complete address of the place.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tags</label>
                                <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                       name="tags" value="{{ old('tags', $place->tags_display) }}"
                                       placeholder="e.g., mosque, islamic, prayer">
                                @error('tags')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Separate tags with commas. Optional field for categorization.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Map URL</label>
                                <input type="url" class="form-control @error('map_url') is-invalid @enderror" 
                                       name="map_url" value="{{ old('map_url', $place->map_url) }}"
                                       placeholder="https://maps.google.com/...">
                                @error('map_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Google Maps or other map service URL. Optional.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" rows="4"
                                          placeholder="Enter additional description about this place (optional)">{{ old('description', $place->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Optional additional information about the place.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Current Image --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Current Image
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="card border">
                            <img src="{{ $place->image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $place->name }}">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-secondary fw-medium">Current Place Image</small>
                                    <a href="{{ $place->image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-external-link"></i> View
                                    </a>
                                </div>
                                @if($place->image_alt_text)
                                <small class="text-secondary d-block mt-1">Alt: {{ $place->image_alt_text }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Update Image --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-upload me-2"></i>
                        Update Image
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">New Image (Optional)</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               name="image" accept="image/*" id="image-input">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle me-1"></i>
                            Leave empty to keep current image. Max: 5MB (JPG, PNG, WebP)
                        </small>
                        <div class="mt-3" id="image-preview"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Alt Text</label>
                        <input type="text" class="form-control @error('image_alt_text') is-invalid @enderror" 
                               name="image_alt_text" value="{{ old('image_alt_text', $place->image_alt_text) }}"
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

            {{-- Settings --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-settings me-2"></i>
                        Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', $place->is_active) ? 'checked' : '' }}>
                            <span class="form-check-label">Active Status</span>
                        </label>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-hint d-block">
                            <i class="ti ti-info-circle me-1"></i>
                            Only active places will be displayed on the website.
                        </small>
                        @if($place->is_active)
                        <small class="text-success d-block mt-1">
                            <i class="ti ti-check me-1"></i>
                            This place is currently active and visible.
                        </small>
                        @else
                        <small class="text-warning d-block mt-1">
                            <i class="ti ti-alert-triangle me-1"></i>
                            This place is currently inactive and hidden.
                        </small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Category Info --}}
            <div class="card mt-3" id="category-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-square me-2"></i>
                        Category Info
                    </h3>
                </div>
                <div class="card-body">
                    <div id="category-details">
                        @if($place->category)
                        <div class="mb-2">
                            <label class="form-label text-secondary">Current Category</label>
                            <div class="fw-bold">{{ $place->category->name }}</div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-secondary">Category Title</label>
                            <div class="text-primary">{{ $place->category->title }}</div>
                        </div>
                        @if($place->category->description)
                        <div class="mb-0">
                            <label class="form-label text-secondary">Description</label>
                            <div class="text-secondary small">{{ $place->category->description }}</div>
                        </div>
                        @endif
                        @else
                        <div class="text-secondary">No category selected</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Place Meta --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-square me-2"></i>
                        Place Meta
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-2">
                                <small class="text-secondary">Order Position:</small>
                                <div class="fw-bold">#{{ $place->order }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <small class="text-secondary">Created:</small>
                                <div>{{ $place->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <small class="text-secondary">Last Updated:</small>
                                <div>{{ $place->updated_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        @if($place->tags && count($place->tags) > 0)
                        <div class="col-12">
                            <div class="mb-0">
                                <small class="text-secondary">Current Tags:</small>
                                <div class="mt-1">
                                    @foreach($place->tags as $tag)
                                    <span class="badge bg-blue-lt me-1 mb-1">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            @if($place->map_url)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-external-link me-2"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ $place->map_url }}" target="_blank" class="btn btn-info">
                            <i class="ti ti-map-pin me-1"></i>
                            View on Map
                        </a>
                        <a href="{{ $place->image_url }}" target="_blank" class="btn btn-warning">
                            <i class="ti ti-photo me-1"></i>
                            View Full Image
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Submit Buttons --}}
        <div class="col-12">
            <div class="card">
                <div class="card-footer bg-transparent text-end">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-secondary">
                                <i class="ti ti-clock me-1"></i>
                                Last saved: {{ $place->updated_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                        <div class="btn-list">
                            <a href="{{ route('new-residents.places.index') }}" class="btn btn-link">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="ti ti-device-floppy me-1"></i>
                                Update Place
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
    document.addEventListener('DOMContentLoaded', function() {
        // Category data for info display
        const categories = @json($categories->keyBy('id'));
        
        // Get form elements
        const categorySelect = document.getElementById('category-select');
        const categoryDetails = document.getElementById('category-details');
        
        // Name character counter
        const nameInput = document.querySelector('input[name="name"]');
        const nameCount = document.getElementById('name-count');
        
        nameInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            nameCount.textContent = currentLength;
            
            if (currentLength > 200) {
                nameCount.parentElement.classList.add('text-warning');
            } else if (currentLength > 255) {
                nameCount.parentElement.classList.remove('text-warning');
                nameCount.parentElement.classList.add('text-danger');
            } else {
                nameCount.parentElement.classList.remove('text-warning', 'text-danger');
            }
        });

        // Category selection handler
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            
            if (categoryId && categories[categoryId]) {
                const category = categories[categoryId];
                categoryDetails.innerHTML = `
                    <div class="mb-2">
                        <label class="form-label text-secondary">Selected Category</label>
                        <div class="fw-bold">${category.name}</div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-secondary">Category Title</label>
                        <div class="text-primary">${category.title}</div>
                    </div>
                    ${category.description ? `
                    <div class="mb-0">
                        <label class="form-label text-secondary">Description</label>
                        <div class="text-secondary small">${category.description}</div>
                    </div>
                    ` : ''}
                `;
                
                // Clear validation error if any
                categorySelect.classList.remove('is-invalid');
                // Remove any alert messages
                const alerts = document.querySelectorAll('.alert-custom');
                alerts.forEach(alert => alert.remove());
            } else {
                categoryDetails.innerHTML = '<div class="text-secondary">No category selected</div>';
            }
        });

        // Image preview functionality
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
                        <div class="card border-warning">
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
                                    <small class="text-warning">
                                        <i class="ti ti-alert-triangle me-1"></i>
                                        This will replace the current image
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

        // Clear image preview function
        window.clearImagePreview = function() {
            imageInput.value = '';
            imagePreview.innerHTML = '';
        };

        // Form submission loading state
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submit-btn');
        
        form.addEventListener('submit', function(e) {
            // Validate category selection
            if (!categorySelect.value) {
                e.preventDefault();
                categorySelect.classList.add('is-invalid');
                categorySelect.focus();
                showAlert(categorySelect, 'danger', 'Please select a category for this place.');
                return false;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            form.classList.add('loading');
        });

        // Clear validation error when category is selected
        categorySelect.addEventListener('change', function() {
            if (this.value) {
                this.classList.remove('is-invalid');
                // Remove any alert messages
                const alerts = document.querySelectorAll('.alert-custom');
                alerts.forEach(alert => alert.remove());
            }
        });

        // Tags input enhancement
        const tagsInput = document.querySelector('input[name="tags"]');
        if (tagsInput) {
            tagsInput.addEventListener('blur', function() {
                // Clean up tags - remove extra spaces and empty tags
                const tags = this.value.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
                this.value = tags.join(', ');
            });
        }

        // Map URL validation
        const mapUrlInput = document.querySelector('input[name="map_url"]');
        if (mapUrlInput) {
            mapUrlInput.addEventListener('blur', function() {
                if (this.value && !this.value.startsWith('http')) {
                    showAlert(this, 'warning', 'Map URL should start with http:// or https://', 3000);
                }
            });
        }
    });
</script>
@endpush