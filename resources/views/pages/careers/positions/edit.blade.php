@extends('layouts.main')

@section('title', 'Edit Career Position')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Edit Career Position</h2>
        <div class="page-subtitle">Update position: {{ $position->title }}</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('careers.positions.applications.index', $position) }}" class="btn btn-outline-success">
            <i class="ti ti-users me-1"></i> 
            Applications ({{ $position->applications_count }})
        </a>
        <a href="{{ route('careers.positions.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Positions
        </a>
    </div>
</div>
@endsection

@section('content')

<form action="{{ route('careers.positions.update', $position) }}" method="POST" id="position-form">
    @csrf
    @method('PUT')
    <div class="row g-3">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Basic Information --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-briefcase me-2"></i>
                        Position Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Position Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title', $position->title) }}" required
                                       placeholder="e.g., Marketing Executive, Software Developer">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <span id="title-count">{{ strlen($position->title) }}</span>/255 characters
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                    <option value="">Select Employment Type</option>
                                    <option value="full-time" {{ old('type', $position->type) === 'full-time' ? 'selected' : '' }}>Full-Time</option>
                                    <option value="part-time" {{ old('type', $position->type) === 'part-time' ? 'selected' : '' }}>Part-Time</option>
                                    <option value="contract" {{ old('type', $position->type) === 'contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="internship" {{ old('type', $position->type) === 'internship' ? 'selected' : '' }}>Internship</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Work Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       name="location" value="{{ old('location', $position->location) }}" required
                                       placeholder="e.g., Jakarta, Remote, Hybrid">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Posted Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('posted_at') is-invalid @enderror" 
                                       name="posted_at" value="{{ old('posted_at', $position->posted_at->format('Y-m-d')) }}" required>
                                @error('posted_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Application Deadline</label>
                                <input type="date" class="form-control @error('closing_date') is-invalid @enderror" 
                                       name="closing_date" value="{{ old('closing_date', $position->closing_date?->format('Y-m-d')) }}">
                                @error('closing_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Leave empty for no deadline</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Job Responsibilities --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-list-check me-2"></i>
                        Job Responsibilities <span class="text-danger">*</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <textarea class="form-control @error('responsibilities') is-invalid @enderror" 
                                  name="responsibilities" id="responsibilities-editor" rows="10" required
                                  placeholder="Enter detailed job responsibilities...">{{ old('responsibilities', $position->responsibilities) }}</textarea>
                        @error('responsibilities')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Use the rich text editor to format responsibilities clearly</small>
                    </div>
                </div>
            </div>

            {{-- Requirements --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-checklist me-2"></i>
                        Requirements <span class="text-danger">*</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                  name="requirements" id="requirements-editor" rows="10" required
                                  placeholder="Enter job requirements and qualifications...">{{ old('requirements', $position->requirements) }}</textarea>
                        @error('requirements')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Include education, experience, and skill requirements</small>
                    </div>
                </div>
            </div>

            {{-- Benefits (Optional) --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-gift me-2"></i>
                        Benefits & Perks
                        <span class="badge bg-blue-lt ms-2">Optional</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <textarea class="form-control @error('benefits') is-invalid @enderror" 
                                  name="benefits" id="benefits-editor" rows="8"
                                  placeholder="Enter benefits and perks (optional)...">{{ old('benefits', $position->benefits) }}</textarea>
                        @error('benefits')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">List salary, benefits, and company perks</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Position Settings --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-settings me-2"></i>
                        Position Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', $position->is_active) ? 'checked' : '' }}>
                            <span class="form-check-label">Active Status</span>
                        </label>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-hint d-block">
                            <i class="ti ti-info-circle me-1"></i>
                            Only active positions will be visible to job seekers.
                        </small>
                        @if($position->is_active)
                        <small class="text-success d-block mt-1">
                            <i class="ti ti-check me-1"></i>
                            This position is currently active and accepting applications.
                        </small>
                        @else
                        <small class="text-warning d-block mt-1">
                            <i class="ti ti-alert-triangle me-1"></i>
                            This position is currently inactive and hidden from job seekers.
                        </small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Applications Summary --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-users me-2"></i>
                        Applications Summary
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h1 text-primary">{{ $position->applications_count }}</div>
                            <div class="text-secondary">Total Applications</div>
                        </div>
                        <div class="col-6">
                            <div class="h1 text-warning">{{ $position->pending_applications_count }}</div>
                            <div class="text-secondary">Pending Review</div>
                        </div>
                    </div>
                    @if($position->applications_count > 0)
                    <div class="mt-3">
                        <a href="{{ route('careers.positions.applications.index', $position) }}" class="btn btn-success w-100">
                            <i class="ti ti-users me-1"></i>
                            View All Applications
                        </a>
                    </div>
                    @endif
                    @if(!$position->canDelete())
                    <div class="mt-3">
                        <div class="alert alert-info mb-0">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-info-circle me-2"></i>
                                </div>
                                <div>
                                    <strong>Note:</strong> This position cannot be deleted because it has applications.
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Position Info --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-circle me-2"></i>
                        Position Details
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Order Position</span>
                            <strong>#{{ $position->order }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Created</span>
                            <strong>{{ $position->created_at->format('M j, Y') }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Last Updated</span>
                            <strong>{{ $position->updated_at->format('M j, Y') }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Status</span>
                            <span class="badge bg-{{ $position->status_color }}-lt">{{ $position->status_text }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Preview Info --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-eye me-2"></i>
                        Position Preview
                    </h3>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="avatar avatar-xl mb-3" style="background-color: var(--tblr-primary-lt);">
                            <i class="ti ti-briefcase text-primary"></i>
                        </div>
                        <h5 id="preview-title">{{ $position->title }}</h5>
                        <div class="text-secondary mb-2">
                            <span id="preview-location">{{ $position->location }}</span> • <span id="preview-type">{{ $position->type_text }}</span>
                        </div>
                        <div class="small text-secondary">
                            Posted on <span id="preview-date">{{ $position->posted_at->format('M j, Y') }}</span>
                            <div id="preview-deadline" style="{{ $position->closing_date ? '' : 'display: none;' }}">
                                Deadline: <span>{{ $position->closing_date?->format('M j, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="col-12">
            <div class="card">
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center text-secondary">
                            <i class="ti ti-clock me-2"></i>
                            <div>
                                <small class="fw-medium">Last updated:</small>
                                <small class="d-block">{{ $position->updated_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        
                        <div class="btn-list">
                            <a href="{{ route('careers.positions.index') }}" class="btn btn-link">
                                Cancel
                            </a>
                            @if($position->canDelete())
                            <button type="button" class="btn btn-outline-danger delete-btn"
                                    data-id="{{ $position->id }}"
                                    data-name="{{ $position->title }}"
                                    data-url="{{ route('careers.positions.destroy', $position) }}">
                                <i class="ti ti-trash me-1"></i>
                                Delete Position
                            </button>
                            @endif
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="ti ti-device-floppy me-1"></i>
                                Update Position
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

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
    document.addEventListener('DOMContentLoaded', function() {
        setupWysiwygEditors();
        setupCharacterCounter();
        setupPreviewUpdates();
        setupFormSubmission();
    });

    function setupWysiwygEditors() {
        // Initialize WYSIWYG editors for each textarea
        const editorOptions = {
            height: 300,
            menubar: false,
            statusbar: false,
            license_key: "gpl",
            toolbar: "undo redo | styles | bold italic | bullist numlist | removeformat",
            content_style: "body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }"
        };

        if (localStorage.getItem("tablerTheme") === "dark") {
            editorOptions.skin = "oxide-dark";
            editorOptions.content_css = "dark";
        }

        // Initialize editors
        hugeRTE.init({
            ...editorOptions,
            selector: "#responsibilities-editor"
        });

        hugeRTE.init({
            ...editorOptions,
            selector: "#requirements-editor"
        });

        hugeRTE.init({
            ...editorOptions,
            selector: "#benefits-editor"
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

    function setupPreviewUpdates() {
        const titleInput = document.querySelector('input[name="title"]');
        const locationInput = document.querySelector('input[name="location"]');
        const typeSelect = document.querySelector('select[name="type"]');
        const postedInput = document.querySelector('input[name="posted_at"]');
        const deadlineInput = document.querySelector('input[name="closing_date"]');

        const previewTitle = document.getElementById('preview-title');
        const previewLocation = document.getElementById('preview-location');
        const previewType = document.getElementById('preview-type');
        const previewDate = document.getElementById('preview-date');
        const previewDeadline = document.getElementById('preview-deadline');

        function updatePreview() {
            previewTitle.textContent = titleInput.value || '{{ $position->title }}';
            previewLocation.textContent = locationInput.value || '{{ $position->location }}';
            
            const typeText = typeSelect.options[typeSelect.selectedIndex]?.text || '{{ $position->type_text }}';
            previewType.textContent = typeText;
            
            if (postedInput.value) {
                const date = new Date(postedInput.value);
                previewDate.textContent = date.toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                });
            }

            if (deadlineInput.value) {
                const deadline = new Date(deadlineInput.value);
                previewDeadline.querySelector('span').textContent = deadline.toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                });
                previewDeadline.style.display = 'block';
            } else {
                previewDeadline.style.display = 'none';
            }
        }

        titleInput.addEventListener('input', updatePreview);
        locationInput.addEventListener('input', updatePreview);
        typeSelect.addEventListener('change', updatePreview);
        postedInput.addEventListener('change', updatePreview);
        deadlineInput.addEventListener('change', updatePreview);
    }

    function setupFormSubmission() {
        const form = document.getElementById('position-form');
        const submitBtn = document.getElementById('submit-btn');
        
        form.addEventListener('submit', function(e) {
            // Add loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating Position...';
            form.classList.add('loading');
        });
    }
</script>
@endpush