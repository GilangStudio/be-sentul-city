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
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Application Details</h2>
        <div class="page-subtitle">
            <strong>{{ $application->name }}</strong> applied for <strong>{{ $position->title }}</strong>
        </div>

        {{-- CV Preview --}}
        @if($application->cv_file_path)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-file-text me-2"></i>
                    Curriculum Vitae
                </h3>
                <div class="card-actions">
                    <a href="{{ route('careers.positions.applications.download-cv', [$position, $application]) }}" 
                       class="btn btn-sm btn-primary">
                        <i class="ti ti-download me-1"></i> Download PDF
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
                        <span>Days Since Applied</span>
                        <strong>{{ $application->applied_at->diffInDays(now()) }} days</strong>
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

        {{-- Related Applications --}}
        @php
            $otherApplications = $position->applications()
                ->where('id', '!=', $application->id)
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
                        View All
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($otherApplications as $otherApp)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-medium">{{ $otherApp->name }}</div>
                                <small class="text-secondary">{{ $otherApp->applied_ago }}</small>
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