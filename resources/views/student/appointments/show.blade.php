@extends('layouts.app')

@section('title', ' - Appointment Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Appointment Details</h1>
    <a href="{{ route('student.appointments.index') }}" class="btn btn-secondary" style="min-height: 44px; padding: 0.5rem 1.25rem;">
        <i class="bi bi-arrow-left me-2"></i>Back to Appointments
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Appointment Information</h5>
                    <span class="badge fs-6" style="background: {{ $appointment->status->color ?? '#64748B' }}; color: var(--badge-text-light);">{{ $appointment->status->label }}</span>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Date</dt>
                    <dd class="col-sm-9">{{ $appointment->formatted_date }}</dd>

                    <dt class="col-sm-3">Time</dt>
                    <dd class="col-sm-9">{{ $appointment->formatted_time }}</dd>

                    <dt class="col-sm-3">Guidance Provider</dt>
                    <dd class="col-sm-9">{{ $appointment->guidanceAssociate->full_name }}</dd>

                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9">{{ $appointment->guidanceAssociate->email }}</dd>

                    <dt class="col-sm-3">Phone</dt>
                    <dd class="col-sm-9">{{ $appointment->guidanceAssociate->phone ?: 'Not provided' }}</dd>

                    <dt class="col-sm-3">Appointment Mode</dt>
                    <dd class="col-sm-9">{{ $appointment->appointment_mode === 'online' ? 'Online' : 'In Person' }}</dd>

                    @if($appointment->appointment_mode === 'online')
                        <dt class="col-sm-3">Meeting Link</dt>
                        <dd class="col-sm-9">
                            @if($appointment->safe_online_meeting_url)
                                <a href="{{ $appointment->safe_online_meeting_url }}" target="_blank" rel="noopener noreferrer">Join online appointment</a>
                            @elseif($appointment->isPending())
                                Your provider will add the meeting link when approving your appointment.
                            @else
                                Meeting link not provided yet.
                            @endif
                        </dd>
                    @else
                        <dt class="col-sm-3">Office Location</dt>
                        <dd class="col-sm-9">{{ $appointment->guidanceAssociate?->office_location ?: 'Not provided' }}</dd>
                    @endif

                    <dt class="col-sm-3">Type of Service</dt>
                    <dd class="col-sm-9">{{ $appointment->service_type ?? '—' }}</dd>

                    <dt class="col-sm-3">Concern / Category</dt>
                    <dd class="col-sm-9">{{ $appointment->concern_category ?? '—' }}</dd>

                    <dt class="col-sm-3">Purpose</dt>
                    <dd class="col-sm-9">{{ $appointment->purpose }}</dd>

                    @if($appointment->notes)
                        <dt class="col-sm-3">Notes</dt>
                        <dd class="col-sm-9">{{ $appointment->notes }}</dd>
                    @endif

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                <span class="badge fs-6" style="background: {{ $appointment->status->color ?? '#64748B' }}; color: var(--badge-text-light);">{{ $appointment->status->label }}</span>
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

                    @if($appointment->reschedule_reason)
                        <dt class="col-sm-3">Reschedule Reason</dt>
                        <dd class="col-sm-9">{{ $appointment->reschedule_reason }}</dd>
                    @endif

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $appointment->created_at->format('F d, Y g:i A') }}</dd>
                </dl>

                <div class="mt-3">
                    @if($appointment->isPending() || $appointment->isApproved())
                        @if($appointment->appointment_date >= now()->toDateString())
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <button type="button" class="btn btn-outline-warning w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#rescheduleModal" style="min-height: 44px;">
                                    <i class="bi bi-calendar-event me-2"></i>Request Reschedule
                                </button>
                                <button type="button" class="btn btn-outline-danger w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#cancelModal" style="min-height: 44px;">
                                    <i class="bi bi-x-circle me-2"></i>Cancel Appointment
                                </button>
                            </div>
                        @endif
                    @endif

                    @if($appointment->isCompleted() && !$appointment->feedback)
                        <a href="{{ route('student.feedback.create', $appointment) }}" class="btn btn-success ms-2">
                            <i class="bi bi-chat-text me-2"></i>Submit Feedback
                        </a>
                    @endif
                </div>
            </div>
        </div>

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
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Guidance Associate</h5>
            </div>
            <div class="card-body text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; background: rgba(66, 158, 189, 0.15); color: var(--medium-blue);">
                    <i class="bi bi-person fs-1"></i>
                </div>
                <h5>{{ $appointment->guidanceAssociate->full_name }}</h5>
                <p class="text-muted">{{ $appointment->guidanceAssociate->email }}</p>
                @if($appointment->guidanceAssociate->phone)
                    <p><i class="bi bi-telephone me-2"></i>{{ $appointment->guidanceAssociate->phone }}</p>
                @endif
            </div>
        </div>

        @if($appointment->feedback)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-star me-2"></i>Your Feedback</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $appointment->feedback->rating ? '-fill' : ''}}" style="{{ $i <= $appointment->feedback->rating ? 'color: var(--yellow);' : 'color: var(--text-primary); opacity: 0.4;' }}" fs-4"></i>
                        @endfor
                    </div>
                    <p>{{ $appointment->feedback->comments }}</p>
                    <small class="text-muted">Submitted {{ $appointment->feedback->submitted_at->diffForHumans() }}</small>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--orange); color: var(--badge-text-light);">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Cancel Appointment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('student.appointments.cancel', $appointment) }}">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to cancel this appointment?</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Reason for Cancellation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Appointment</button>
                    <button type="submit" class="btn btn-danger">Cancel Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--yellow); color: var(--badge-text-light);">
                <h5 class="modal-title"><i class="bi bi-calendar-event me-2"></i>Request Reschedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('student.appointments.reschedule', $appointment) }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="availability_id" id="reschedule_availability_id">
                    <input type="hidden" name="requested_date" id="reschedule_requested_date">
                    <input type="hidden" name="requested_start_time" id="reschedule_requested_start_time">
                    <input type="hidden" name="requested_end_time" id="reschedule_requested_end_time">
                    
                    <div class="mb-3">
                        <label class="form-label">Current Appointment</label>
                        <input type="text" class="form-control" value="{{ $appointment->formatted_date }} at {{ $appointment->formatted_time }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">New Date</label>
                        <select class="form-select" id="reschedule_date_select" onchange="loadRescheduleSlots()">
                            <option value="">Select a date</option>
                            @foreach($availableDates as $date)
                                <option value="{{ $date->available_date->format('Y-m-d') }}">{{ $date->available_date->format('l, F d, Y') }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3" id="reschedule_slots_container">
                        <p class="text-muted">Select a date to see available time slots</p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Reschedule <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" disabled id="reschedule_submit">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    async function loadRescheduleSlots() {
        const appointmentId = {{ $appointment->id }};
        const dateSelect = document.getElementById('reschedule_date_select');
        const container = document.getElementById('reschedule_slots_container');
        const submitBtn = document.getElementById('reschedule_submit');
        
        const date = dateSelect.value;
        if (!date) {
            container.innerHTML = '<p class="text-muted">Select a date to see available time slots</p>';
            submitBtn.disabled = true;
            return;
        }
        
        container.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading...</p></div>';
        
        try {
            const response = await fetch(`{{ route('student.schedules.slots', ':date') }}`.replace(':date', date));
            const slots = await response.json();
            
            if (slots.length === 0) {
                container.innerHTML = '<p class="text-center text-muted py-3">No available time slots for this date</p>';
                submitBtn.disabled = true;
                return;
            }
            
            // Group by guidance associate
            const grouped = {};
            slots.forEach(slot => {
                if (!grouped[slot.guidance_associate_id]) {
                    grouped[slot.guidance_associate_id] = {
                        name: slot.guidance_associate_name,
                        slots: []
                    };
                }
                grouped[slot.guidance_associate_id].slots.push(slot);
            });
            
            let html = '';
            let selectedSlot = null;
            
            for (const [gaId, data] of Object.entries(grouped)) {
                html += `<div class="mb-3">
                    <h6 class="text-primary mb-2"><i class="bi bi-person-badge me-1"></i>${data.name}</h6>
                    <div class="d-flex flex-wrap gap-2">`;
                
                data.slots.forEach(slot => {
                    let classes = 'time-slot btn btn-sm px-3 py-2';
                    if (slot.is_booked) {
                        classes += ' booked';
                    } else if (slot.student_conflict) {
                        classes += ' conflict';
                    } else {
                        classes += ' btn-outline-primary';
                    }
                    
                    html += `<button type="button" 
                        class="${classes}" 
                        data-availability-id="${slot.availability_id}"
                        data-start="${slot.start_time}"
                        data-end="${slot.end_time}"
                        data-ga-id="${slot.guidance_associate_id}"
                        data-ga-name="${slot.guidance_associate_name}"
                        ${slot.is_booked || slot.student_conflict ? 'disabled' : ''}
                        onclick="selectRescheduleSlot(this)">
                        ${slot.formatted_time}
                        ${slot.is_booked ? ' <i class="bi bi-lock"></i>' : ''}
                        ${slot.student_conflict ? ' <i class="bi bi-exclamation-triangle"></i>' : ''}
                    </button>`;
                });
                
                html += `</div></div>`;
            }
            
            container.innerHTML = html;
            submitBtn.disabled = true;
        } catch (error) {
            container.innerHTML = '<div class="text-center py-3 text-danger">Error loading slots</div>';
            submitBtn.disabled = true;
        }
    }
    
    function selectRescheduleSlot(button) {
        document.querySelectorAll('#reschedule_slots_container .time-slot.selected').forEach(el => {
            el.classList.remove('selected', 'btn-primary');
            el.classList.add('btn-outline-primary');
        });
        
        button.classList.remove('btn-outline-primary');
        button.classList.add('selected', 'btn-primary');
        
        document.getElementById('reschedule_availability_id').value = button.getAttribute('data-availability-id');
        document.getElementById('reschedule_requested_date').value = document.getElementById('reschedule_date_select').value;
        document.getElementById('reschedule_requested_start_time').value = button.getAttribute('data-start');
        document.getElementById('reschedule_requested_end_time').value = button.getAttribute('data-end');
        
        document.getElementById('reschedule_submit').disabled = false;
    }
</script>
@endsection
