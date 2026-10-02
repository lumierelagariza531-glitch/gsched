@extends('layouts.app')

@section('styles')
<style>
    body {
        background-color: var(--bg-light);
        font-family: 'Inter', 'Roboto', sans-serif;
    }
    
    .card {
        background: var(--card-bg);
        border: none;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(5, 63, 92, 0.08), 0 1px 2px rgba(5, 63, 92, 0.05);
    }
    
    .card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color-light);
        padding: 1rem 1.5rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .notification-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color-light);
        transition: background 0.2s ease;
    }
    
    .notification-item:last-child {
        border-bottom: none;
    }
    
    .notification-item:hover {
        background: var(--bg-light);
    }
    
    .notification-title {
        color: var(--text-primary);
        font-weight: 500;
        font-size: 0.875rem;
    }
    
    .notification-time {
        color: var(--text-muted);
        font-size: 0.75rem;
    }
    
    .notification-message {
        color: var(--text-muted);
        font-size: 0.8125rem;
        margin-top: 0.25rem;
        line-height: 1.4;
    }
    
    .type-badge {
        height: 24px;
        min-width: 84px;
        padding: 0 10px;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        line-height: 24px;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .type-badge.feedback { background: rgba(247, 173, 25, 0.15); color: var(--text-primary); }
    
    .feedback-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color-light);
        transition: background 0.2s ease;
    }
    
    .feedback-item:last-child {
        border-bottom: none;
    }
    
    .feedback-item:hover {
        background: var(--bg-light);
    }
    
    .rating-stars {
        display: flex;
        gap: 0.25rem;
    }
    
    .rating-stars i {
        font-size: 1rem;
    }
    
    .rating-stars .filled {
        color: var(--yellow);
    }
    
    .rating-stars .empty {
        color: var(--text-muted);
        opacity: 0.4;
    }

    .sqd-tag {
        background: rgba(66, 158, 189, 0.15);
        color: var(--text-primary);
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }
</style>@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h2 mb-1" style="color: var(--text-primary); font-weight: 700;">Feedback</h1>
                    <p class="text-muted mb-0">Share your experience and view submitted feedback</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Awaiting Feedback -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                        <i class="bi bi-chat-text me-2" style="color: var(--yellow);"></i>Appointments Awaiting Feedback
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($appointments->count() > 0)
                        @foreach($appointments as $appointment)
                            <a href="{{ route('student.feedback.create', $appointment) }}" class="notification-item d-flex gap-3 text-decoration-none">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="notification-title mb-1">{{ $appointment->guidanceAssociate->full_name }}</h6>
                                        <small class="notification-time">{{ $appointment->formatted_date }}</small>
                                    </div>
                                    <p class="notification-message mb-0">{{ $appointment->formatted_time }} - {{ Str::limit($appointment->purpose, 80) }}</p>
                                </div>
                                <span class="type-badge feedback">Give Feedback</span>
                            </a>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle fs-1" style="color: var(--medium-blue);"></i>
                            <h5 class="mt-3" style="color: var(--text-primary);">All Caught Up!</h5>
                            <p class="text-muted">No pending feedback at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Submitted Feedback -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                        <i class="bi bi-chat-text me-2" style="color: var(--medium-blue);"></i>Submitted Feedback
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($submittedFeedback->count() > 0)
                        @foreach($submittedFeedback as $feedback)
                            <div class="feedback-item">
                                <div class="d-flex gap-3">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="notification-title mb-1">{{ $feedback->appointment->guidanceAssociate->full_name }}</h6>
                                            <small class="notification-time">{{ $feedback->submitted_at->format('M d, Y') }}</small>
                                        </div>
                                        <div class="sqd-summary mb-2">
                                            @php
                                                $sqdLabels = \App\Models\Feedback::SQD_OPTIONS;
                                                $sqdFields = ['sqd0', 'sqd1', 'sqd2', 'sqd3', 'sqd4', 'sqd5', 'sqd6', 'sqd7', 'sqd8'];
                                            @endphp
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($sqdFields as $field)
                                                    @if(isset($feedback->$field) && $feedback->$field)
                                                        <small class="sqd-tag">{{ strtoupper($field) }}: {{ $sqdLabels[$feedback->$field] ?? $feedback->$field }}</small>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @if($feedback->suggestions)
                                            <p class="notification-message mb-0">{{ Str::limit($feedback->suggestions, 100) }}</p>
                                        @else
                                            <p class="text-muted mb-0 fst-italic">No suggestions provided</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-chat-text fs-1" style="color: var(--text-muted); opacity: 0.5;"></i>
                            <h5 class="mt-3" style="color: var(--text-primary);">No Feedback Submitted</h5>
                            <p class="text-muted">Your submitted feedback will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
    </div>
@endsection
