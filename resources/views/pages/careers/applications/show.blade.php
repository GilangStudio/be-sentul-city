@extends('layouts.main')

@section('title', 'Application Details')

@push('styles')
<style>
    .cv-preview {
        height: 600px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .status-timeline {
        position: relative;
        padding-left: 2rem;
    }
    
    .status-timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    
    .status-timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .status-timeline-item::before {
        content: '';
        position: absolute;
        left: -14px;
        top: 6px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #6c757d;
    }
    
    .status-timeline-item.active::before {
        background: #0054a6;
        box-shadow: 0 0 0 3px rgba(0, 84, 166, 0.2);
    }
    
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }

    .applicant-avatar {
        width: 80px;
        height: 80px;
        background: var(--tblr-primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 600;
        border-radius: 12px;
        margin-right: 1rem;
    }

    .contact-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--tblr-border-color-translucent);
    }

    .contact-item:last-child {
        border-bottom: none;
    }

    .contact-icon {
        width: 36px;
        height: 36px;
        background: var(--tblr-bg-surface-secondary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
    }

    .message-content {
        background: var(--tblr-bg-surface);
        border-radius: 8px;
        padding: 1rem;
        border-left: 4px solid var(--tblr-primary);
        margin-top: 0.75rem;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Application Details</h2>
        <div class="page-subtitle">
            <strong>{{ $application->name }}</strong> applied for <strong>{{ $position->title }}</strong>
        </div>
    </div>
    <div class="btn-list">
        <a href="{{ route('careers.positions.applications.index', $position) }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Applications
        </a>
        <a href="{{ route('careers.positions.edit', $position) }}" class="btn btn-outline-primary">
            <i class="ti ti-edit me-1"></i> Edit Position
        </a>
    </div>
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-8">
        {{-- Applicant Information Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-user me-2"></i>
                    Applicant Information
                </h3>
                <div class="card-actions">
                    <span class="badge bg-{{ $application->status_color }}-lt fs-6">
                        <span class="status-dot bg-{{ $application->status_color }}"></span>
                        {{ $application->status_text }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        {{-- Basic Info with Avatar --}}
                        <div class="d-flex align-items-start mb-4">
                            <div class="applicant-avatar">
                                {{ strtoupper(substr($application->name, 0, 2)) }}
                            </div>
                            <div class="flex-fill">
                                <h3 class="mb-1">{{ $application->name }}</h3>
                                <div class="text-secondary mb-2">
                                    Applied {{ $application->days_since_applied }} ago • 
                                    Application ID: #{{ $application->id }}
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="contact-item">
                                            <div class="contact-icon">
                                                <i class="ti ti-mail text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium">Email Address</div>
                                                <div class="text-secondary">
                                                    <a href="mailto:{{ $application->email }}" class="text-decoration-none">
                                                        {{ $application->email }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="contact-item">
                                            <div class="contact-icon">
                                                <i class="ti ti-phone text-success"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium">Phone Number</div>
                                                <div class="text-secondary">
                                                    <a href="tel:{{ $application->phone }}" class="text-decoration-none">
                                                        {{ $application->phone }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Application Details --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="ti ti-briefcase text-info"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">Position Applied</div>
                                        <div class="text-secondary">{{ $position->title }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="ti ti-calendar text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">Application Date</div>
                                        <div class="text-secondary">{{ $application->applied_at->format('M j, Y') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="ti ti-clock text-orange"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">Time Since Applied</div>
                                        <div class="text-secondary">{{ $application->days_since_applied }} ago</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Cover Letter/Message --}}
                        @if($application->message)
                        <div class="mt-4">
                            <h5 class="mb-2">
                                <i class="ti ti-message-circle me-2"></i>
                                Cover Letter / Message
                            </h5>
                            <div class="message-content">
                                <div class="text-body">{{ $application->message }}</div>
                            </div>
                        </div>
                        @else
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div>
                                        <i class="ti ti-info-circle me-2"></i>
                                    </div>
                                    <div>
                                        <strong>No cover letter provided</strong><br>
                                        The applicant did not include a cover letter or message with their application.
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- CV Preview --}}
        @if($application->cv_file_path)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-file-text me-2"></i>
                    Curriculum Vitae
                </h3>
                <div class="card-actions">
                    <a href="{{ route('careers.positions.applications.download-cv', [$position, $application]) }}" 
                       class="btn btn-sm btn-primary">
                        <i class="ti ti-download me-1"></i> Download CV
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="cv-preview">
                    <iframe src="{{ $application->cv_file_url }}" 
                            width="100%" 
                            height="100%" 
                            frameborder="0">
                        <p>Your browser does not support PDFs. 
                           <a href="{{ $application->cv_file_url }}">Download the PDF</a>.</p>
                    </iframe>
                </div>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ti ti-file-x icon mb-3 text-secondary" style="font-size: 3rem;"></i>
                <h3>No CV Attached</h3>
                <p class="text-secondary">This application does not include a CV file.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Right Column - Actions & Status --}}
    <div class="col-lg-4">
        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-settings me-2"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($application->cv_file_path)
                    <a href="{{ route('careers.positions.applications.download-cv', [$position, $application]) }}" 
                       class="btn btn-success">
                        <i class="ti ti-download me-1"></i> Download CV
                    </a>
                    @endif
                    <a href="mailto:{{ $application->email }}" class="btn btn-outline-primary">
                        <i class="ti ti-mail me-1"></i> Send Email
                    </a>
                    <a href="tel:{{ $application->phone }}" class="btn btn-outline-primary">
                        <i class="ti ti-phone me-1"></i> Call Applicant
                    </a>
                    <button type="button" class="btn btn-outline-danger delete-btn"
                            data-id="{{ $application->id }}"
                            data-name="{{ $application->name }}"
                            data-url="{{ route('careers.positions.applications.destroy', [$position, $application]) }}">
                        <i class="ti ti-trash me-1"></i> Delete Application
                    </button>
                </div>
            </div>
        </div>

        {{-- Status Management --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-flag me-2"></i>
                    Status Management
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Current Status</label>
                    <div>
                        <span class="badge bg-{{ $application->status_color }}-lt fs-6">
                            <span class="status-dot bg-{{ $application->status_color }}"></span>
                            {{ $application->status_text }}
                        </span>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('careers.positions.applications.update-status', [$position, $application]) }}" id="status-form">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Change Status</label>
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="">Select new status...</option>
                            <option value="pending" {{ $application->status === 'pending' ? 'disabled' : '' }}>
                                Pending Review
                            </option>
                            <option value="reviewed" {{ $application->status === 'reviewed' ? 'disabled' : '' }}>
                                Reviewed
                            </option>
                            <option value="shortlisted" {{ $application->status === 'shortlisted' ? 'disabled' : '' }}>
                                Shortlisted
                            </option>
                            <option value="rejected" {{ $application->status === 'rejected' ? 'disabled' : '' }}>
                                Rejected
                            </option>
                            <option value="hired" {{ $application->status === 'hired' ? 'disabled' : '' }}>
                                Hired
                            </option>
                        </select>
                    </div>
                </form>

                {{-- Status Timeline --}}
                <div class="mb-3">
                    <label class="form-label">Application Process</label>
                    <div class="status-timeline">
                        <div class="status-timeline-item {{ in_array($application->status, ['pending', 'reviewed', 'shortlisted', 'rejected', 'hired']) ? 'active' : '' }}">
                            <div class="fw-medium">Application Received</div>
                            <small class="text-secondary">{{ $application->applied_at->format('M j, Y') }}</small>
                        </div>
                        <div class="status-timeline-item {{ in_array($application->status, ['reviewed', 'shortlisted', 'rejected', 'hired']) ? 'active' : '' }}">
                            <div class="fw-medium">Under Review</div>
                            <small class="text-secondary">
                                {{ in_array($application->status, ['reviewed', 'shortlisted', 'rejected', 'hired']) ? 'Completed' : 'Pending' }}
                            </small>
                        </div>
                        <div class="status-timeline-item {{ in_array($application->status, ['shortlisted', 'hired']) ? 'active' : '' }}">
                            <div class="fw-medium">Shortlisted</div>
                            <small class="text-secondary">
                                {{ in_array($application->status, ['shortlisted', 'hired']) ? 'Completed' : 'Pending' }}
                            </small>
                        </div>
                        <div class="status-timeline-item {{ $application->status === 'hired' ? 'active' : '' }}">
                            <div class="fw-medium">Final Decision</div>
                            <small class="text-secondary">
                                @if($application->status === 'hired')
                                    <span class="text-success fw-medium">Hired</span>
                                @elseif($application->status === 'rejected')
                                    <span class="text-danger fw-medium">Rejected</span>
                                @else
                                    Pending
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Application Summary --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-info-circle me-2"></i>
                    Application Summary
                </h3>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span>Application ID</span>
                        <strong>#{{ $application->id }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span>Applied Date</span>
                        <strong>{{ $application->applied_at->format('M j, Y') }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span>Time Since Applied</span>
                        <strong>{{ $application->days_since_applied }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span>CV Attached</span>
                        <strong>
                            @if($application->cv_file_path)
                                <span class="text-success">
                                    <i class="ti ti-check me-1"></i>Yes
                                </span>
                            @else
                                <span class="text-danger">
                                    <i class="ti ti-x me-1"></i>No
                                </span>
                            @endif
                        </strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span>Cover Letter</span>
                        <strong>
                            @if($application->message)
                                <span class="text-success">
                                    <i class="ti ti-check me-1"></i>Yes
                                </span>
                            @else
                                <span class="text-secondary">
                                    <i class="ti ti-minus me-1"></i>No
                                </span>
                            @endif
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Position Details --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-briefcase me-2"></i>
                    Position Details
                </h3>
                <div class="card-actions">
                    <a href="{{ route('careers.positions.edit', $position) }}" class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-edit"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h5 class="mb-1">{{ $position->title }}</h5>
                    <div class="text-secondary">
                        <i class="ti ti-map-pin me-1"></i>
                        {{ $position->location }} • 
                        <span class="badge bg-primary-lt">{{ $position->type_text }}</span>
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span>Position Status</span>
                        <span class="badge bg-{{ $position->status_color }}-lt">{{ $position->status_text }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span>Posted Date</span>
                        <strong>{{ $position->posted_at->format('M j, Y') }}</strong>
                    </div>
                    @if($position->closing_date)
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span>Closing Date</span>
                        <strong>{{ $position->closing_date->format('M j, Y') }}</strong>
                    </div>
                    @endif
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span>Total Applications</span>
                        <strong>{{ $position->applications_count }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span>Pending Review</span>
                        <strong class="text-warning">{{ $position->pending_applications_count }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Other Applications for this Position --}}
        @php
            $otherApplications = $position->applications()
                ->where('id', '!=', $application->id)
                ->orderBy('applied_at', 'desc')
                ->limit(5)
                ->get();
        @endphp
        
        @if($otherApplications->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-users me-2"></i>
                    Other Applications
                </h3>
                <div class="card-actions">
                    <a href="{{ route('careers.positions.applications.index', $position) }}" class="btn btn-sm btn-outline-primary">
                        View All ({{ $position->applications_count - 1 }})
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($otherApplications as $otherApp)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3" style="background-color: var(--tblr-primary-lt);">
                                    {{ strtoupper(substr($otherApp->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $otherApp->name }}</div>
                                    <small class="text-secondary">Applied {{ $otherApp->days_since_applied }} ago</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $otherApp->status_color }}-lt">{{ $otherApp->status_text }}</span>
                                <div class="mt-1">
                                    <a href="{{ route('careers.positions.applications.show', [$position, $otherApp]) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

@push('scripts')
@include('components.toast')

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
        setupStatusForm();
    });

    function setupStatusForm() {
        const statusForm = document.getElementById('status-form');
        const statusSelect = statusForm.querySelector('select[name="status"]');
        
        statusSelect.addEventListener('change', function() {
            if (this.value) {
                // Add loading state to the form
                statusForm.style.opacity = '0.6';
                statusForm.style.pointerEvents = 'none';
                
                // Add loading text
                const loadingOption = document.createElement('option');
                loadingOption.value = '';
                loadingOption.textContent = 'Updating status...';
                loadingOption.selected = true;
                this.appendChild(loadingOption);
            }
        });
    }
</script>
@endpush