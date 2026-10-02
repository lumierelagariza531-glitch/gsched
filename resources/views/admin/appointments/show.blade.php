@extends('layouts.app')

@section('title', ' - Appointment Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Appointment Details</h1>
    <a href="{{ route('admin.appointments') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Appointments
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Appointment Information</h5>
                <span class="badge fs-6" style="background: {{ $appointment->isCompleted() ? '#198754' : ($appointment->status->color ?? '#64748B') }}; color: var(--badge-text-light);">{{ $appointment->status->label }}</span>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9">#{{ $appointment->id }}</dd>

                    <dt class="col-sm-3">Severity</dt>
                    <dd class="col-sm-9">
                        @php
                            $severityColor = [
                                'not_assessed' => '#94A3B8',
                                'low' => '#429EBD',
                                'moderate' => '#F7AD19',
                                'high' => '#F27F0C',
                            ][$appointment->severity] ?? '#94A3B8';
                        @endphp
                        <span class="badge fs-6" style="background: {{ $severityColor }}; color: var(--badge-text-light);">
                            {{ $appointment->severityLabel() }}
                        </span>
                        @if($appointment->severity === 'high')
                            <span class="text-danger ms-2"><i class="bi bi-shield-lock me-1"></i>Confidential - Admin only</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Date</dt>
                    <dd class="col-sm-9">{{ $appointment->formatted_date }}</dd>

                    <dt class="col-sm-3">Time</dt>
                    <dd class="col-sm-9">{{ $appointment->formatted_time }}</dd>

                    <dt class="col-sm-3">Student</dt>
                    <dd class="col-sm-9">{{ $appointment->student->full_name }} ({{ $appointment->student->email }})</dd>

                    <dt class="col-sm-3">Counselor</dt>
                    <dd class="col-sm-9">{{ $appointment->guidanceAssociate->full_name ?? 'N/A' }} ({{ $appointment->guidanceAssociate->email ?? 'N/A' }})</dd>

                    <dt class="col-sm-3">Appointment Mode</dt>
                    <dd class="col-sm-9">{{ $appointment->appointment_mode === 'online' ? 'Online' : 'In Person' }}</dd>
                    @if($appointment->appointment_mode === 'online')
                        <dt class="col-sm-3">Online Contact</dt>
                        <dd class="col-sm-9">
                            @if($appointment->safe_online_meeting_url)
                                <a href="{{ $appointment->safe_online_meeting_url }}" target="_blank" rel="noopener noreferrer">{{ $appointment->safe_online_meeting_url }}</a>
                            @else
                                The approver's Facebook profile link is sent with the approval notification.
                            @endif
                        </dd>
                    @else
                        <dt class="col-sm-3">Office Location</dt>
                        <dd class="col-sm-9">{{ $appointment->guidanceAssociate?->office_location ?: 'Not provided' }}</dd>
                    @endif

                    <dt class="col-sm-3">Purpose</dt>
                    <dd class="col-sm-9">{{ $appointment->purpose }}</dd>

                    @if($appointment->notes)
                        <dt class="col-sm-3">Notes</dt>
                        <dd class="col-sm-9">{{ $appointment->notes }}</dd>
                    @endif

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                <span class="badge fs-6" style="background: {{ $appointment->isCompleted() ? '#198754' : ($appointment->status->color ?? '#64748B') }}; color: var(--badge-text-light);">{{ $appointment->status->label }}</span>
                    </dd>

                    @if($appointment->approved_at)
                        <dt class="col-sm-3">Approved At</dt>
                        <dd class="col-sm-9">{{ $appointment->approved_at->format('F d, Y g:i A') }}</dd>
                    @endif

                    @if($appointment->completed_at)
                        <dt class="col-sm-3">Completed At</dt>
                        <dd class="col-sm-9">{{ $appointment->completed_at->format('F d, Y g:i A') }}</dd>
                    @endif

                    @if($appointment->cancelled_at)
                        <dt class="col-sm-3">Cancelled At</dt>
                        <dd class="col-sm-9">{{ $appointment->cancelled_at->format('F d, Y g:i A') }}</dd>
                        <dt class="col-sm-3">Cancellation Reason</dt>
                        <dd class="col-sm-9">{{ $appointment->cancellation_reason }}</dd>
                    @endif

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $appointment->created_at->format('F d, Y g:i A') }}</dd>
                </dl>
                @if($appointment->isPending())
                    <form method="POST" action="{{ route('admin.appointments.approve', $appointment) }}" class="border-top pt-3 mt-3">
                        @csrf
                        <button type="submit" class="btn btn-success">Approve Appointment</button>
                    </form>
                @elseif($appointment->isApproved())
                    <form method="POST" action="{{ route('admin.appointments.complete', $appointment) }}" class="border-top pt-3 mt-3" onsubmit="return confirm('Mark this counseling session as complete? The student will be prompted to provide feedback.');">
                        @csrf
                        <button type="submit" class="btn btn-success">Mark Counseling Complete</button>
                    </form>
                    @if($appointment->appointment_mode === 'online')
                        <form method="POST" action="{{ route('admin.appointments.meeting-link', $appointment) }}" class="border-top pt-3 mt-3">
                            @csrf
                            <label for="online_meeting_url" class="form-label">Add or update meeting link</label>
                            <div class="input-group">
                                <input type="url" class="form-control" id="online_meeting_url" name="online_meeting_url" value="{{ old('online_meeting_url', $appointment->online_meeting_url) }}" placeholder="https://..." required>
                                <button type="submit" class="btn btn-primary">Save link</button>
                            </div>
                        </form>
                    @endif
                @endif
            </div>
        </div>

        @include('shared.student-id-proof', [
            'studentIdProofUser' => $appointment->student,
            'studentIdProofRouteName' => 'student-id-proofs.show',
            'studentIdProofRouteTarget' => $appointment->student,
        ])

        @if($appointment->rescheduleRequests->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Reschedule Requests</h5>
                </div>
                <div class="card-body">
                    @foreach($appointment->rescheduleRequests as $request)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <h6>Request #{{ $request->id }}</h6>
                                <span class="badge bg-{{ $request->isPending() ? 'warning' : ($request->isApproved() ? 'success' : 'danger') }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </div>
                            <dl class="row">
                                <dt class="col-sm-3">Old Schedule</dt>
                                <dd class="col-sm-9">{{ $request->old_date->format('F d, Y') }} at {{ \Carbon\Carbon::parse($request->old_start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($request->old_end_time)->format('g:i A') }}</dd>
                                <dt class="col-sm-3">Requested Schedule</dt>
                                <dd class="col-sm-9">{{ $request->requested_date->format('F d, Y') }} at {{ \Carbon\Carbon::parse($request->requested_start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($request->requested_end_time)->format('g:i A') }}</dd>
                                <dt class="col-sm-3">Reason</dt>
                                <dd class="col-sm-9">{{ $request->reason }}</dd>
                                @if($request->reviewed_at)
                                    <dt class="col-sm-3">Reviewed By</dt>
                                    <dd class="col-sm-9">{{ $request->reviewer->full_name }} at {{ $request->reviewed_at->format('F d, Y g:i A') }}</dd>
                                @endif
                            </dl>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm mb-4 appointment-student-card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Student</h5>
            </div>
            <div class="card-body text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; background: rgba(66, 158, 189, 0.15); color: var(--medium-blue);">
                    <i class="bi bi-person fs-1"></i>
                </div>
                <h5>{{ $appointment->student->full_name }}</h5>
                <p class="text-muted">{{ $appointment->student->email }}</p>
                @if($appointment->student->phone)
                    <p><i class="bi bi-telephone me-2"></i>{{ $appointment->student->phone }}</p>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mb-4 appointment-associate-card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Counselor</h5>
            </div>
            <div class="card-body text-center">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; background: rgba(159, 231, 245, 0.15); color: var(--medium-blue);">
                    <i class="bi bi-person-badge fs-1"></i>
                </div>
                <h5>{{ $appointment->guidanceAssociate->full_name ?? 'N/A' }}</h5>
                <p class="text-muted">{{ $appointment->guidanceAssociate->email ?? 'N/A' }}</p>
                @if($appointment->guidanceAssociate)
                    <small class="text-muted">{{ $appointment->guidanceAssociate->display_role_name }}</small>
                @endif
            </div>
        </div>

        @if($appointment->feedback)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-star me-2"></i>Feedback</h5>
                </div>
                <div class="card-body">
                    @include('shared.feedback-rating', ['feedback' => $appointment->feedback])
                    @if($appointment->feedback->comments)
                        <p class="mt-3">{{ $appointment->feedback->comments }}</p>
                    @endif
                    @if($appointment->feedback->suggestions)
                        <p class="mt-3 mb-0"><strong>Suggestions:</strong> {{ $appointment->feedback->suggestions }}</p>
                    @endif
                    <small class="text-muted">Submitted {{ $appointment->feedback->submitted_at->diffForHumans() }}</small>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
