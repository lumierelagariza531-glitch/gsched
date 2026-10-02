@extends('layouts.app')

@section('title', ' - Appointment Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Appointment Details</h1>
    <a href="{{ route('guidance.appointments') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Appointments
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
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
                    <dt class="col-sm-3">Date</dt>
                    <dd class="col-sm-9">{{ $appointment->formatted_date }}</dd>

                    <dt class="col-sm-3">Time</dt>
                    <dd class="col-sm-9">{{ $appointment->formatted_time }}</dd>

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

                    <dt class="col-sm-3">Student</dt>
                    <dd class="col-sm-9">{{ $appointment->isStudentInfoHiddenFromGuidance() ? 'Confidential Student' : $appointment->student->full_name }}</dd>

                    @unless($appointment->isStudentInfoHiddenFromGuidance())
                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $appointment->student->email }}</dd>

                        <dt class="col-sm-3">Phone</dt>
                        <dd class="col-sm-9">{{ $appointment->student->phone ?: 'Not provided' }}</dd>
                    @endunless

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
                </dl>
            </div>
        </div>

        @unless($appointment->isStudentInfoHiddenFromGuidance())
            @include('shared.student-id-proof', [
                'studentIdProofUser' => $appointment->student,
                'studentIdProofRouteName' => 'guidance.appointments.id-proof',
                'studentIdProofRouteTarget' => $appointment,
            ])
        @endunless

        @if($appointment->isApproved() && $appointment->appointment_mode === 'online')
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">Appointment Details</h5></div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="{{ route('guidance.requests.meeting-link', $appointment) }}">
                        @csrf
                        <label for="online_meeting_url" class="form-label">Add or update meeting link</label>
                        <div class="input-group">
                            <input type="url" class="form-control" id="online_meeting_url" name="online_meeting_url" value="{{ old('online_meeting_url', $appointment->online_meeting_url) }}" placeholder="https://..." required>
                            <button type="submit" class="btn btn-primary">Save link</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if($appointment->feedback)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-star me-2"></i>Student Feedback</h5>
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

    <div class="col-md-4">
        <div class="card shadow-sm mb-4 appointment-student-card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Student</h5>
            </div>
            <div class="card-body text-center">
                @if($appointment->isStudentInfoHiddenFromGuidance())
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; background: rgba(66, 158, 189, 0.15); color: var(--medium-blue);">
                        <i class="bi bi-shield-lock fs-1" aria-hidden="true"></i>
                    </div>
                    <h5>Confidential Student</h5>
                    <p class="text-muted">Identity and ID proof images are restricted for this high-severity case.</p>
                @else
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; background: rgba(66, 158, 189, 0.15); color: var(--medium-blue);">
                        <i class="bi bi-person fs-1"></i>
                    </div>
                    <h5>{{ $appointment->student->full_name }}</h5>
                    <p class="text-muted">{{ $appointment->student->email }}</p>
                    @if($appointment->student->phone)
                        <p><i class="bi bi-telephone me-2"></i>{{ $appointment->student->phone }}</p>
                    @endif
                @endif
            </div>
        </div>

        @if($appointment->guidanceAssociate)
            <div class="card shadow-sm mb-4 appointment-associate-card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Guidance Associate</h5>
                </div>
                <div class="card-body text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; background: rgba(66, 158, 189, 0.15); color: var(--medium-blue);">
                        <i class="bi bi-person-badge fs-1"></i>
                    </div>
                    <h5>{{ $appointment->guidanceAssociate->full_name }}</h5>
                    <p class="text-muted mb-1">{{ $appointment->guidanceAssociate->email }}</p>
                    <small class="text-muted">{{ $appointment->guidanceAssociate->display_role_name }}</small>
                </div>
            </div>
        @endif

        @if($appointment->isPending() || $appointment->isApproved())
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Appointment Actions</h5>
                </div>
                <div class="card-body">
                    @if($appointment->isPending())
                        <form method="POST" action="{{ route('guidance.requests.approve', $appointment) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-2"></i>Approve Appointment
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('guidance.requests.complete', $appointment) }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Mark this appointment as completed? Student will be notified to provide feedback.')">
                                <i class="bi bi-check2-circle me-2"></i>Mark Completed
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
