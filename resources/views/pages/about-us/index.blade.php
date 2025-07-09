{{-- resources/views/pages/about-us/index.blade.php --}}
@extends('layouts.main')

@section('title', 'About Us Management')

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
    
    .stats-card {
        background: var(--tblr-primary);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
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
    
    .section-preview-card {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .section-preview-card:hover {
        border-color: var(--tblr-primary);
        background: #e3f2fd;
    }
    
    .contact-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">About Us Management</h2>
        <div class="page-subtitle">Manage your company's about us page content and settings</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('about-us.executive-summary.index') }}" class="btn btn-outline-primary">
            <i class="ti ti-chart-line me-1"></i> Executive Summary
        </a>
        <a href="{{ route('about-us.functions.index') }}" class="btn btn-outline-success">
            <i class="ti ti-tools me-1"></i> Functions
        </a>
        <a href="{{ route('about-us.services.index') }}" class="btn btn-outline-info">
            <i class="ti ti-heart-handshake me-1"></i> Services
        </a>
    </div>
</div>
@endsection

@section('content')

<form method="POST" action="{{ route('about-us.updateOrCreate') }}" enctype="multipart/form-data" id="main-form">
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
                    @if($aboutPage)
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
                        @if($aboutPage && $aboutPage->banner_image_url)
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Current Banner</label>
                                <div class="banner-preview">
                                    <img src="{{ $aboutPage->banner_image_url }}" class="banner-image" alt="Current Banner">
                                </div>
                                <small class="text-secondary mt-1 d-block">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Current banner image
                                </small>
                            </div>
                        </div>
                        @endif
                        
                        <div class="{{ $aboutPage && $aboutPage->banner_image_url ? 'col-md-8' : 'col-12' }}">
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ $aboutPage ? 'Update Banner Image' : 'Banner Image' }}
                                    @if(!$aboutPage)<span class="text-danger">*</span>@endif
                                </label>
                                <input type="file" class="form-control @error('banner_image') is-invalid @enderror" 
                                       name="banner_image" accept="image/*" {{ $aboutPage ? '' : 'required' }} id="banner-input">
                                @error('banner_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    @if($aboutPage)Leave empty to keep current image. @endif
                                    Recommended: 1920x800px, Max: 10MB (JPG, PNG, WebP)
                                </small>
                                <div class="mt-3" id="banner-preview"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Banner Alt Text</label>
                                <input type="text" class="form-control @error('banner_alt_text') is-invalid @enderror" 
                                       name="banner_alt_text" value="{{ old('banner_alt_text', $aboutPage->banner_alt_text ?? '') }}"
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

            {{-- Company Information --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-building-bank me-2"></i>
                        Company Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       name="company_name" value="{{ old('company_name', $aboutPage->company_name ?? 'PT SENTUL CITY Tbk.') }}" 
                                       required placeholder="PT SENTUL CITY Tbk.">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', $aboutPage->is_active ?? true) ? 'checked' : '' }}>
                                    <span class="form-check-label">Active Page</span>
                                </label>
                                <small class="form-hint d-block">
                                    Make the about us page publicly accessible.
                                </small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Company Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('company_description') is-invalid @enderror" 
                                          name="company_description" id="company-editor" rows="6" required
                                          placeholder="Enter comprehensive company description...">{{ old('company_description', $aboutPage->company_description ?? '') }}</textarea>
                                @error('company_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="company-description-error" style="display: none;"></div>
                                <small class="form-hint">
                                    Provide a detailed overview of your company, its history, and core values.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Vision <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('vision') is-invalid @enderror" 
                                          name="vision" rows="4" required
                                          placeholder="Enter company vision statement...">{{ old('vision', $aboutPage->vision ?? '') }}</textarea>
                                @error('vision')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    Your company's long-term aspirations and goals.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Mission <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('mission') is-invalid @enderror" 
                                          name="mission" rows="4" required
                                          placeholder="Enter company mission statement...">{{ old('mission', $aboutPage->mission ?? '') }}</textarea>
                                @error('mission')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    Your company's purpose and core objectives.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistics Section --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-chart-bar me-2"></i>
                        Statistics Section
                    </h3>
                    <div class="card-actions">
                        <small class="text-secondary">Live preview below</small>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Live Preview --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <div class="stats-number" id="houses-preview">{{ number_format($aboutPage->total_houses ?? 7800) }}</div>
                                <div id="houses-label-preview">{{ $aboutPage->houses_label ?? 'Houses' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <div class="stats-number" id="visitors-preview">{{ number_format($aboutPage->daily_visitors ?? 40000) }}</div>
                                <div id="visitors-label-preview">{{ $aboutPage->visitors_label ?? 'Daily Visitors' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <div class="stats-number" id="commercial-preview">{{ number_format($aboutPage->commercial_areas ?? 100) }}</div>
                                <div id="commercial-label-preview">{{ $aboutPage->commercial_label ?? 'Commercial Areas' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Form Fields --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Total Houses <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('total_houses') is-invalid @enderror" 
                                       name="total_houses" value="{{ old('total_houses', $aboutPage->total_houses ?? 7800) }}" 
                                       required min="0" id="houses-input">
                                @error('total_houses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Houses Label <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('houses_label') is-invalid @enderror" 
                                       name="houses_label" value="{{ old('houses_label', $aboutPage->houses_label ?? 'Houses') }}" 
                                       required id="houses-label-input">
                                @error('houses_label')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Daily Visitors <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('daily_visitors') is-invalid @enderror" 
                                       name="daily_visitors" value="{{ old('daily_visitors', $aboutPage->daily_visitors ?? 40000) }}" 
                                       required min="0" id="visitors-input">
                                @error('daily_visitors')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Visitors Label <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('visitors_label') is-invalid @enderror" 
                                       name="visitors_label" value="{{ old('visitors_label', $aboutPage->visitors_label ?? 'Daily Visitors') }}" 
                                       required id="visitors-label-input">
                                @error('visitors_label')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Commercial Areas <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('commercial_areas') is-invalid @enderror" 
                                       name="commercial_areas" value="{{ old('commercial_areas', $aboutPage->commercial_areas ?? 100) }}" 
                                       required min="0" id="commercial-input">
                                @error('commercial_areas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Commercial Label <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('commercial_label') is-invalid @enderror" 
                                       name="commercial_label" value="{{ old('commercial_label', $aboutPage->commercial_label ?? 'Commercial Areas') }}" 
                                       required id="commercial-label-input">
                                @error('commercial_label')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO Settings --}}
            @include('components.seo-meta-form', ['data' => $aboutPage, 'type' => $aboutPage ? 'edit' : 'create'])
            
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            
            {{-- Main Section 1 --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-layout-sections me-2"></i>
                        Section 1: Building Comfortable City
                    </h3>
                </div>
                <div class="card-body">
                    @if($aboutPage && $aboutPage->main_section1_image_url)
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div class="image-preview-container">
                            <img src="{{ $aboutPage->main_section1_image_url }}" class="img-fluid rounded" alt="Section 1 Image">
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label">
                            Section Image @if(!$aboutPage)<span class="text-danger">*</span>@endif
                        </label>
                        <input type="file" class="form-control @error('main_section1_image') is-invalid @enderror" 
                               name="main_section1_image" accept="image/*" {{ $aboutPage ? '' : 'required' }} id="section1-input">
                        @error('main_section1_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            @if($aboutPage)Leave empty to keep current image. @endif
                            Recommended: 1200x800px, Max: 5MB
                        </small>
                        <div class="mt-3" id="section1-preview"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image Alt Text</label>
                        <input type="text" class="form-control @error('main_section1_image_alt_text') is-invalid @enderror" 
                               name="main_section1_image_alt_text" value="{{ old('main_section1_image_alt_text', $aboutPage->main_section1_image_alt_text ?? '') }}"
                               placeholder="Enter image description">
                        @error('main_section1_image_alt_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Section Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('main_section1_title') is-invalid @enderror" 
                               name="main_section1_title" value="{{ old('main_section1_title', $aboutPage->main_section1_title ?? 'Building Sentul City as a Comfortable City') }}" 
                               required placeholder="Building Sentul City as a Comfortable City">
                        @error('main_section1_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Section Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('main_section1_description') is-invalid @enderror" 
                                  name="main_section1_description" id="section1-editor" rows="6" required
                                  placeholder="Enter section 1 description...">{{ old('main_section1_description', $aboutPage->main_section1_description ?? '') }}</textarea>
                        @error('main_section1_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="invalid-feedback" id="section1-description-error" style="display: none;"></div>
                    </div>
                </div>
            </div>

            {{-- Main Section 2 --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-heart-handshake me-2"></i>
                        Section 2: More than Just Services
                    </h3>
                </div>
                <div class="card-body">
                    @if($aboutPage && $aboutPage->main_section2_image_url)
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div class="image-preview-container">
                            <img src="{{ $aboutPage->main_section2_image_url }}" class="img-fluid rounded" alt="Section 2 Image">
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label">
                            Section Image @if(!$aboutPage)<span class="text-danger">*</span>@endif
                        </label>
                        <input type="file" class="form-control @error('main_section2_image') is-invalid @enderror" 
                               name="main_section2_image" accept="image/*" {{ $aboutPage ? '' : 'required' }} id="section2-input">
                        @error('main_section2_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            @if($aboutPage)Leave empty to keep current image. @endif
                            Recommended: 1200x800px, Max: 5MB
                        </small>
                        <div class="mt-3" id="section2-preview"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image Alt Text</label>
                        <input type="text" class="form-control @error('main_section2_image_alt_text') is-invalid @enderror" 
                               name="main_section2_image_alt_text" value="{{ old('main_section2_image_alt_text', $aboutPage->main_section2_image_alt_text ?? '') }}"
                               placeholder="Enter image description">
                        @error('main_section2_image_alt_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Section Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('main_section2_title') is-invalid @enderror" 
                               name="main_section2_title" value="{{ old('main_section2_title', $aboutPage->main_section2_title ?? 'More than Just Resident Services') }}" 
                               required placeholder="More than Just Resident Services">
                        @error('main_section2_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Section Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('main_section2_description') is-invalid @enderror" 
                                  name="main_section2_description" id="section2-editor" rows="6" required
                                  placeholder="Enter section 2 description...">{{ old('main_section2_description', $aboutPage->main_section2_description ?? '') }}</textarea>
                        @error('main_section2_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="invalid-feedback" id="section2-description-error" style="display: none;"></div>
                    </div>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-phone me-2"></i>
                        Contact Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="contact-info-grid">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="ti ti-phone me-1"></i>
                                Phone Number
                            </label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   name="phone" value="{{ old('phone', $aboutPage->phone ?? '') }}"
                                   placeholder="+62 821-2340-8551">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="ti ti-mail me-1"></i>
                                Email Address
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email', $aboutPage->email ?? '') }}"
                                   placeholder="sentulcity@gmail.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="ti ti-map-pin me-1"></i>
                            Address
                        </label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" rows="3"
                                  placeholder="Ruko Paragon Blok C5-8, Bogor, Indonesia">{{ old('address', $aboutPage->address ?? '') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="ti ti-world me-1"></i>
                            Website URL
                        </label>
                        <input type="url" class="form-control @error('website_url') is-invalid @enderror" 
                               name="website_url" value="{{ old('website_url', $aboutPage->website_url ?? '') }}"
                               placeholder="https://sentulcity.com">
                        @error('website_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
        </div>

        {{-- Submit Button --}}
        <div class="col-12">
            <div class="card">
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        @if($aboutPage)
                        <div class="d-flex align-items-center text-secondary">
                            <i class="ti ti-clock me-2"></i>
                            <div>
                                <small class="fw-medium">Last saved:</small>
                                <small class="d-block">{{ $aboutPage->updated_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        @else
                        <div class="text-secondary">
                            <i class="ti ti-info-circle me-1"></i>
                            Create your About Us page settings
                        </div>
                        @endif
                        
                        <div class="btn-list">
                            <button type="submit" class="btn btn-primary" id="save-main-btn">
                                <i class="ti ti-device-floppy me-1"></i>
                                {{ $aboutPage ? 'Update' : 'Save' }}  About Us Page
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
        setupStatsPreviews();
        setupFormSubmission();
    });

    function setupImagePreviews() {
        const bannerInput = document.getElementById('banner-input');
        const bannerPreview = document.getElementById('banner-preview');
        const section1Input = document.getElementById('section1-input');
        const section1Preview = document.getElementById('section1-preview');
        const section2Input = document.getElementById('section2-input');
        const section2Preview = document.getElementById('section2-preview');
        
        if (bannerInput) {
            bannerInput.addEventListener('change', function(e) {
                handleImagePreview(e, bannerPreview, bannerInput, 10);
            });
        }
        
        if (section1Input) {
            section1Input.addEventListener('change', function(e) {
                handleImagePreview(e, section1Preview, section1Input, 5);
            });
        }
        
        if (section2Input) {
            section2Input.addEventListener('change', function(e) {
                handleImagePreview(e, section2Preview, section2Input, 5);
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

    function setupStatsPreviews() {
        // Houses
        const housesInput = document.getElementById('houses-input');
        const housesLabelInput = document.getElementById('houses-label-input');
        const housesPreview = document.getElementById('houses-preview');
        const housesLabelPreview = document.getElementById('houses-label-preview');

        if (housesInput && housesPreview) {
            housesInput.addEventListener('input', function() {
                const value = parseInt(this.value) || 0;
                housesPreview.textContent = value.toLocaleString();
            });
        }

        if (housesLabelInput && housesLabelPreview) {
            housesLabelInput.addEventListener('input', function() {
                housesLabelPreview.textContent = this.value || 'Houses';
            });
        }

        // Visitors
        const visitorsInput = document.getElementById('visitors-input');
        const visitorsLabelInput = document.getElementById('visitors-label-input');
        const visitorsPreview = document.getElementById('visitors-preview');
        const visitorsLabelPreview = document.getElementById('visitors-label-preview');

        if (visitorsInput && visitorsPreview) {
            visitorsInput.addEventListener('input', function() {
                const value = parseInt(this.value) || 0;
                visitorsPreview.textContent = value.toLocaleString();
            });
        }

        if (visitorsLabelInput && visitorsLabelPreview) {
            visitorsLabelInput.addEventListener('input', function() {
                visitorsLabelPreview.textContent = this.value || 'Daily Visitors';
            });
        }

        // Commercial
        const commercialInput = document.getElementById('commercial-input');
        const commercialLabelInput = document.getElementById('commercial-label-input');
        const commercialPreview = document.getElementById('commercial-preview');
        const commercialLabelPreview = document.getElementById('commercial-label-preview');

        if (commercialInput && commercialPreview) {
            commercialInput.addEventListener('input', function() {
                const value = parseInt(this.value) || 0;
                commercialPreview.textContent = value.toLocaleString();
            });
        }

        if (commercialLabelInput && commercialLabelPreview) {
            commercialLabelInput.addEventListener('input', function() {
                commercialLabelPreview.textContent = this.value || 'Commercial Areas';
            });
        }
    }

    function setupFormSubmission() {
        const mainForm = document.getElementById('main-form');
        const saveMainBtn = document.getElementById('save-main-btn');
        
        if (mainForm && saveMainBtn) {
            mainForm.addEventListener('submit', function(e) {
                let hasError = false;

                // Validate company description from editor
                try {
                    const companyEditorContent = hugeRTE.get('company-editor').getContent();
                    const companyError = document.getElementById('company-description-error');
                    
                    if (!companyEditorContent.trim() || companyEditorContent.trim() === '<p></p>' || companyEditorContent.trim() === '<p><br></p>') {
                        e.preventDefault();
                        companyError.textContent = 'Company description is required.';
                        companyError.style.display = 'block';
                        document.getElementById('company-editor').scrollIntoView({ behavior: 'smooth', block: 'center' });
                        hasError = true;
                    } else {
                        companyError.style.display = 'none';
                    }
                } catch (err) {
                    console.warn('Company editor validation error:', err);
                }

                // Validate section 1 description
                try {
                    const section1EditorContent = hugeRTE.get('section1-editor').getContent();
                    const section1Error = document.getElementById('section1-description-error');
                    
                    if (!section1EditorContent.trim() || section1EditorContent.trim() === '<p></p>' || section1EditorContent.trim() === '<p><br></p>') {
                        e.preventDefault();
                        section1Error.textContent = 'Section 1 description is required.';
                        section1Error.style.display = 'block';
                        if (!hasError) {
                            document.getElementById('section1-editor').scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        hasError = true;
                    } else {
                        section1Error.style.display = 'none';
                    }
                } catch (err) {
                    console.warn('Section 1 editor validation error:', err);
                }

                // Validate section 2 description
                try {
                    const section2EditorContent = hugeRTE.get('section2-editor').getContent();
                    const section2Error = document.getElementById('section2-description-error');
                    
                    if (!section2EditorContent.trim() || section2EditorContent.trim() === '<p></p>' || section2EditorContent.trim() === '<p><br></p>') {
                        e.preventDefault();
                        section2Error.textContent = 'Section 2 description is required.';
                        section2Error.style.display = 'block';
                        if (!hasError) {
                            document.getElementById('section2-editor').scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        hasError = true;
                    } else {
                        section2Error.style.display = 'none';
                    }
                } catch (err) {
                    console.warn('Section 2 editor validation error:', err);
                }

                // If validation passed, show loading state
                if (!hasError) {
                    saveMainBtn.disabled = true;
                    saveMainBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                    mainForm.classList.add('loading');
                }
            });
        }
    }

    // Clear image preview function
    window.clearImagePreview = function(previewId, inputId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewId).innerHTML = '';
    };

    // Auto-save draft functionality (optional)
    function setupAutoSave() {
        const form = document.getElementById('main-form');
        const inputs = form.querySelectorAll('input, textarea, select');
        
        let autoSaveTimer;
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    // Could implement auto-save to drafts here
                    console.log('Auto-save triggered');
                }, 30000); // 30 seconds
            });
        });
    }

    // Initialize enhanced features
    document.addEventListener("DOMContentLoaded", function () {
        // setupAutoSave(); // Uncomment if auto-save is needed
    });
</script>
@endpush