@extends('layouts.app')

@section('title', ' - Appointment Request Details')

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

    .status-badge {
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: #FFFFFF;
    }

    .status-badge.pending { background: #ffbf00; color: #032536; }
    .status-badge.approved { background: var(--medium-blue); color: #032536; }
    .status-badge.rejected { background: #db213a; }
    .status-badge.completed { background: #21db3d; color: #032536; }
    .status-badge.cancelled { background: var(--orange); color: #032536; }

    .badge-high-severity {
        background: var(--orange);
        color: #032536;
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-weight: 600;
    }

    .alert {
        border-radius: 0.75rem;
        border: none;
    }

    .activity-timeline {
        position: relative;
    }

    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 19px;
        top: 20px;
        bottom: 20px;
        width: 2px;
        background: var(--border-color-light);
    }

    .activity-item {
        position: relative;
        padding: 0.25rem 0 1.25rem;
    }

    .activity-item:last-child {
        padding-bottom: 0;
    }

    .activity-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(66, 158, 189, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .activity-item::after {
        content: '';
        position: absolute;
        left: 15px;
        top: 15px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--medium-blue);
    }

    .activity-content {
        padding-left: 3.5rem;
    }

    .activity-content h6 {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .activity-content p {
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
    }

    .activity-time {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .content-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }

    .summary-section {
        margin-bottom: 1.25rem;
    }

    .summary-panel {
        background: var(--bg-light);
        border: 1px solid var(--border-color-light);
    }

    .student-identity {
        align-items: center;
        display: inline-flex;
        gap: 0.5rem;
    }

    .student-details {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .student-id {
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 500;
    }

    .student-department {
        background: rgba(66, 158, 189, 0.1);
        border: 1px solid rgba(66, 158, 189, 0.25);
        border-radius: 9999px;
        color: var(--medium-blue);
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        white-space: nowrap;
    }

    .submitted-info {
        align-items: center;
        color: var(--text-muted);
        display: flex;
        font-size: 0.8rem;
        gap: 0.4rem;
        white-space: nowrap;
    }

    .summary-section:last-child {
        margin-bottom: 0;
    }

    .summary-section h6 {
        margin-bottom: 1rem;
    }

    .severity-control {
        align-items: center;
        display: flex;
        gap: 0.5rem;
    }

    .severity-control label {
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0;
        white-space: nowrap;
    }

    .severity-select {
        max-width: 100%;
        width: 150px;
    }

    .remarks-header {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }

    .remarks-note {
        color: var(--text-muted);
        font-size: 0.75rem;
        opacity: 0.6;
        white-space: nowrap;
    }

    .summary-grid dl {
        margin-bottom: 0;
    }

    .read-only-box {
        background: rgba(5, 63, 92, 0.03);
        border: 1px solid var(--border-color-light);
        border-radius: 0.5rem;
        padding: 1rem;
    }

    @media (max-width: 575.98px) {
        .student-department {
            max-width: 100%;
            white-space: normal;
        }

        .submitted-info {
            flex-wrap: wrap;
            min-width: 0;
            white-space: normal;
        }

        .severity-control {
            flex-wrap: wrap;
        }

        .severity-control label {
            white-space: normal;
        }

        .remarks-header {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .remarks-note {
            white-space: normal;
        }
    }

    .action-btn-full {
        width: 100%;
        padding: 0.625rem 1.25rem;
        height: 44px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-primary-action {
        background: var(--medium-blue);
        border: none;
        border-radius: 0.5rem;
        color: #FFFFFF;
        font-weight: 500;
        padding: 0.625rem 1.25rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.2s ease;
        min-height: 44px;
    }

    .btn-primary-action:hover {
        background: var(--navy);
    }
</style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h1 class="h2 mb-0" style="color: var(--text-primary); font-weight: 700;">Appointment Request Details</h1>
                @php
                    $statusClass = strtolower($appointment->status->name ?? 'pending');
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ $appointment->status->label }}</span>
            </div>
            <a href="{{ route('guidance.requests') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Requests
            </a>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="content-wrapper">
    <!-- Student / Request Summary Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                <div class="student-identity">
                    <i class="bi bi-person" style="color: var(--medium-blue);"></i>
                    <div class="student-details">
                        <span>{{ $appointment->isHighSeverity() ? 'Confidential Student' : $appointment->student->full_name }}</span>
                        @unless($appointment->isHighSeverity())
                            <span class="student-id">ID: {{ $appointment->student->student_id ?: 'N/A' }}</span>
                            @if($appointment->guidanceAssociate)
                                <span class="text-muted small">{{ $appointment->guidanceAssociate->display_role_name }}</span>
                            @endif
                        @endunless
                    </div>
                    @unless($appointment->isHighSeverity())
                        <span class="student-department">{{ $appointment->student->school ?: 'N/A' }}</span>
                    @endunless
                </div>
            </h5>
            <div class="submitted-info">
                <i class="bi bi-clock"></i>
                <span>Submitted: {{ $appointment->created_at->format('F d, Y g:i A') }}</span>
            </div>
        </div>
        <div class="card-body">
            @if($appointment->isHighSeverity())
                <div class="text-center py-4">
                    <i class="bi bi-shield-lock fs-1" style="color: var(--yellow);"></i>
                    <h5 class="mt-3" style="color: var(--text-primary);">Confidential Appointment</h5>
                    <p class="text-muted">This is a high severity appointment. Student identity is hidden from guidance counselors.</p>
                    <span class="status-badge" style="background: var(--orange);">HIGH SEVERITY</span>
                </div>
            @else
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 summary-panel">
                            <div class="card-body">
                                <div class="summary-section">
                                    <h6 class="text-uppercase small" style="color: var(--text-muted); letter-spacing: 0.05em; font-weight: 600;">Student Information</h6>
                                    <dl class="row">
                                        <dt class="col-5">Email</dt>
                                        <dd class="col-7">{{ $appointment->student->email }}</dd>

                                        <dt class="col-5">Phone</dt>
                                        <dd class="col-7">{{ $appointment->student->phone ?: 'Not provided' }}</dd>

                                        <dt class="col-5">Past Visits</dt>
                                        <dd class="col-7">{{ $pastVisitCount }} Session(s)</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 summary-panel">
                            <div class="card-body">
                                <div class="summary-section">
                                    <h6 class="text-uppercase small" style="color: var(--text-muted); letter-spacing: 0.05em; font-weight: 600;">Requested Appointment</h6>
                                    <dl class="row">
                                        <dt class="col-5">Date</dt>
                                        <dd class="col-7">{{ $appointment->formatted_date }}</dd>

                                        <dt class="col-5">Time</dt>
                                        <dd class="col-7">{{ $appointment->formatted_time }}</dd>

                                        <dt class="col-5">Assigned To</dt>
                                        <dd class="col-7">{{ $appointment->guidanceAssociate->full_name ?? 'Not yet assigned' }}</dd>

                                        <dt class="col-5">Mode</dt>
                                        <dd class="col-7">{{ $appointment->appointment_mode === 'online' ? 'Online' : 'In Person' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @unless($appointment->isStudentInfoHiddenFromGuidance())
                    @include('shared.student-id-proof', [
                        'studentIdProofUser' => $appointment->student,
                        'studentIdProofRouteName' => 'guidance.appointments.id-proof',
                        'studentIdProofRouteTarget' => $appointment,
                    ])
                @endunless
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                <i class="bi bi-info-circle me-2" style="color: var(--medium-blue);"></i>Appointment Details
            </h5>
        </div>
        <div class="card-body">
            <dl class="row mb-3">
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
            </dl>

            @if($appointment->isApproved() && $appointment->appointment_mode === 'online')
                <form method="POST" action="{{ route('guidance.requests.meeting-link', $appointment) }}" class="border-top pt-3">
                    @csrf
                    <label for="online_meeting_url" class="form-label">Add or update meeting link</label>
                    <div class="input-group">
                        <input type="url" class="form-control" id="online_meeting_url" name="online_meeting_url" value="{{ old('online_meeting_url', $appointment->online_meeting_url) }}" placeholder="https://..." required>
                        <button type="submit" class="btn btn-primary">Save link</button>
                    </div>
                    @error('online_meeting_url')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </form>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('guidance.requests.severity', $appointment) }}">
        @csrf
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                    <i class="bi bi-clipboard-data me-2" style="color: var(--medium-blue);"></i>Case Assessment
                </h5>
                <div class="severity-control ms-auto">
                    <label for="severity" class="form-label">Severity Level</label>
                    <select class="form-select form-select-sm severity-select" id="severity" name="severity" required>
                        <option value="not_assessed" {{ $appointment->severity === 'not_assessed' ? 'selected' : '' }}>Not Yet Assessed</option>
                        <option value="low" {{ $appointment->severity === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="moderate" {{ $appointment->severity === 'moderate' ? 'selected' : '' }}>Moderate</option>
                        <option value="high" {{ $appointment->severity === 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label fw-medium" style="color: var(--text-primary);">Student Concern / Purpose</label>
                    <div class="read-only-box">
                        {{ $appointment->purpose }}
                        <div class="form-text small mt-2" style="color: var(--text-muted);">
                            <i class="bi bi-info-circle me-1"></i>Submitted by student. This field is read-only.
                        </div>
                    </div>
                </div>

                <div class="remarks-header mb-4">
                    <label for="notes" class="form-label fw-medium mb-0" style="color: var(--text-primary);">Internal Guidance Notes &amp; Remarks</label>
                    <span class="remarks-note">Confidential counselor notes are visible to guidance staff.</span>
                </div>
                <textarea class="form-control" id="notes" name="notes" rows="4" maxlength="2000" placeholder="Add a confidential counselor notes, initial evaluation, or pre-session here...">{{ old('notes', $appointment->notes) }}</textarea>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn-primary-action">
                        <i class="bi bi-save"></i>Save Assessment
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Action Center Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                <i class="bi bi-lightning me-2" style="color: var(--yellow);"></i>Action Center
            </h5>
        </div>
        <div class="card-body">
            <div class="d-grid gap-2">
                <a href="{{ route('guidance.appointments.show', $appointment) }}" class="action-btn-full btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-up-right"></i>Appointment Details
                </a>
                @if($appointment->isPending())
                    <form method="POST" action="{{ route('guidance.requests.approve', $appointment) }}">
                        @csrf
                        <button type="submit" class="action-btn-full btn btn-success" onclick="return confirm('Approve this appointment?')">
                            <i class="bi bi-check-circle"></i>Approve Request
                        </button>
                    </form>
                    <form method="POST" action="{{ route('guidance.requests.reject', $appointment) }}">
                        @csrf
                        <button type="submit" class="action-btn-full btn btn-danger" onclick="return confirm('Reject this appointment?')">
                            <i class="bi bi-x-circle"></i>Reject Request
                        </button>
                    </form>
                @endif

                @if($appointment->isPending() || $appointment->isApproved())
                    <a href="{{ route('guidance.requests.reschedule', $appointment) }}" class="action-btn-full btn btn-warning">
                        <i class="bi bi-calendar-event"></i>Reschedule Slot
                    </a>

                    <form method="POST" action="{{ route('guidance.requests.cancel', $appointment) }}">
                        @csrf
                        <button type="submit" class="action-btn-full btn btn-secondary" onclick="return confirm('Cancel this appointment?')">
                            <i class="bi bi-slash-circle"></i>Cancel Appointment
                        </button>
                    </form>
                @endif

                @if($appointment->isApproved())
                    <form method="POST" action="{{ route('guidance.requests.complete', $appointment) }}">
                        @csrf
                        <button type="submit" class="action-btn-full btn btn-primary" onclick="return confirm('Mark this appointment as completed?')">
                            <i class="bi bi-check2-circle"></i>Mark Completed
                        </button>
                    </form>

                    <form method="POST" action="{{ route('guidance.requests.remind', $appointment) }}">
                        @csrf
                        <button type="submit" class="action-btn-full btn btn-info">
                            <i class="bi bi-bell"></i>Send Reminder
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if($appointment->rescheduleRequests->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                    <i class="bi bi-calendar-event me-2" style="color: var(--medium-blue);"></i>Reschedule Requests
                </h5>
            </div>
            <div class="card-body">
                @foreach($appointment->rescheduleRequests as $request)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <h6>Request #{{ $request->id }}</h6>
                            @php
                                $requestStatusClass = strtolower($request->status ?? 'pending');
                            @endphp
                            <span class="status-badge {{ $requestStatusClass }}">{{ ucfirst($request->status) }}</span>
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

    <!-- Activity History Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                <i class="bi bi-clock-history me-2" style="color: var(--medium-blue);"></i>Activity History
            </h5>
        </div>
        <div class="card-body">
            @if($activityLogs && $activityLogs->count() > 0)
                <div class="activity-timeline">
                    @foreach($activityLogs as $log)
                        <div class="activity-item">
                            <div class="activity-content">
                                <h6 style="color: var(--text-primary);">
                                    @php
                                        $actionLabels = [
                                            'APPROVE_APPOINTMENT' => 'Appointment Approved',
                                            'REJECT_APPOINTMENT' => 'Appointment Rejected',
                                            'RESCHEDULE_APPOINTMENT' => 'Appointment Rescheduled',
                                            'CANCEL_APPOINTMENT' => 'Appointment Cancelled',
                                            'COMPLETE_APPOINTMENT' => 'Appointment Completed',
                                            'ASSIGN_SEVERITY' => 'Severity Updated',
                                            'SEND_REMINDER' => 'Reminder Sent',
                                            'REQUEST_SUBMITTED' => 'Request Submitted',
                                        ];
                                    @endphp
                                    {{ $actionLabels[$log->action] ?? $log->action }}
                                </h6>
                                <p style="color: var(--text-secondary);">{{ \Illuminate\Support\Str::limit($log->description, 120) }}</p>
                                <div class="activity-time">
                                    <i class="bi bi-person-circle me-1"></i>
                                    {{ $log->user ? $log->user->full_name : 'System' }}
                                    <span class="mx-2" style="color: var(--border-color);">�</span>
                                    {{ $log->created_at->format('M d, Y g:i A') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-info-circle fs-4" style="color: var(--text-muted);"></i>
                    <p class="text-muted mt-2">No activity history available for this request.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
