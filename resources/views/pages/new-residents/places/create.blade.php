@extends('layouts.main')

@section('title', 'Add New Place')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">Add New Place</h2>
    <a href="{{ route('new-residents.places.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Back to Places
    </a>
</div>
@endsection

@section('content')

<form action="{{ route('new-residents.places.store') }}" method="POST" enctype="multipart/form-data" id="place-form">
    @csrf
    <div class="row g-3">
        {{-- Main Content --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-map-pin me-2"></i>
                        Place Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} - {{ $category->title }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    Choose the appropriate category for this place.
                                    @if($categories->isEmpty())
                                    <a href="{{ route('new-residents.categories.index') }}" class="text-primary">Create a category</a> first.
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Place Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required
                                       placeholder="e.g., Masjid Taqha Islamic Center">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <span id="name-count">0</span>/255 characters
                                </small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          name="address" rows="3" required
                                          placeholder="Enter complete address">{{ old('address') }}</textarea>
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
                                       name="tags" value="{{ old('tags') }}"
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
                                       name="map_url" value="{{ old('map_url') }}"
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
                                          placeholder="Enter additional description about this place (optional)">{{ old('description') }}</textarea>
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
            {{-- Place Image --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Place Image
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
                                   {{ old('is_active', '1') ? 'checked' : '' }}>
                            <span class="form-check-label">Active Status</span>
                        </label>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-hint d-block">
                            <i class="ti ti-info-circle me-1"></i>
                            Only active places will be displayed on the website.
                        </small>
                    </div>
                </div>
            </div>

            {{-- Category Info --}}
            <div class="card mt-3" id="category-info" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-square me-2"></i>
                        Category Info
                    </h3>
                </div>
                <div class="card-body">
                    <div id="category-details">
                        <!-- Category details will be populated here -->
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="col-12">
            <div class="card">
                <div class="card-footer bg-transparent text-end">
                    <div class="d-flex">
                        <a href="{{ route('new-residents.places.index') }}" class="btn btn-link">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary ms-auto" id="submit-btn">
                            <i class="ti ti-device-floppy me-1"></i>
                            Create Place
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
        // Category data for info display
        const categories = @json($categories->keyBy('id'));
        
        // Get form elements
        const categorySelect = document.querySelector('select[name="category_id"]');
        const categoryInfo = document.getElementById('category-info');
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
                        <label class="form-label text-secondary">Category Name</label>
                        <div class="fw-bold">${category.name}</div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-secondary">Category Title</label>
                        <div class="text-primary">${category.title}</div>
                    </div>
                    ${category.description ? `
                    <div class="mb-2">
                        <label class="form-label text-secondary">Description</label>
                        <div class="text-secondary small">${category.description}</div>
                    </div>
                    ` : ''}
                    <div class="mb-0">
                        <label class="form-label text-secondary">Status</label>
                        <div>
                            <span class="badge bg-green-lt">
                                <i class="ti ti-check me-1"></i>Active
                            </span>
                        </div>
                    </div>
                `;
                categoryInfo.style.display = 'block';
            } else {
                categoryInfo.style.display = 'none';
            }
        });

        // Trigger category change on page load if there's a selected value
        if (categorySelect.value) {
            categorySelect.dispatchEvent(new Event('change'));
        }

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
            
            // Validate image
            if (!imageInput.files.length) {
                e.preventDefault();
                imageInput.classList.add('is-invalid');
                imageInput.focus();
                showAlert(imageInput, 'danger', 'Please select an image for this place.');
                return false;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
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

        // Clear validation error when image is selected
        imageInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                this.classList.remove('is-invalid');
                // Remove any alert messages
                const alerts = document.querySelectorAll('.alert-custom');
                alerts.forEach(alert => alert.remove());
            }
        });
    });
</script>
@endpush