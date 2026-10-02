@extends('layouts.app')

@section('title', ' - Reschedule Appointment')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Reschedule Appointment</h1>
    <a href="{{ route('student.appointments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Appointments
    </a>
</div>

@if($appointment->isPending() || $appointment->isApproved())
    <form method="POST" action="{{ route('student.appointments.reschedule', $appointment) }}">
        @csrf
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Request Reschedule</h5>
            </div>
            <div class="card-body">
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

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-send me-2"></i>Submit Reschedule Request
                    </button>
                    <a href="{{ route('student.appointments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
@else
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        This appointment cannot be rescheduled in its current status.
    </div>
    <a href="{{ route('student.appointments.index') }}" class="btn btn-outline-secondary">Back to Appointments</a>
@endif

@push('scripts')
<script>
    function loadRescheduleSlots() {
        const date = document.getElementById('reschedule_date_select').value;
        const container = document.getElementById('reschedule_slots_container');
        
        if (!date) {
            container.innerHTML = '<p class="text-muted">Select a date to see available time slots</p>';
            return;
        }

        container.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

        fetch(`/student/schedules/${date}/slots`)
            .then(response => response.json())
            .then(slots => {
                if (slots.length === 0) {
                    container.innerHTML = '<p class="text-muted">No available slots for this date</p>';
                    return;
                }

                let html = '<label class="form-label">Available Time Slots</label><div class="row g-2">';
                slots.forEach(slot => {
                    if (slot.is_booked || slot.student_conflict) {
                        html += `
                            <div class="col-md-4">
                                <div class="card border-secondary bg-light" style="pointer-events: none;">
                                    <div class="card-body text-center py-2">
                                        <small class="text-muted">${slot.formatted_time}</small>
                                        <br><small class="text-danger">${slot.is_booked ? 'Booked' : 'Conflict'}</small>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-primary w-100 py-2 slot-btn" 
                                    data-availability-id="${slot.availability_id}"
                                    data-start-time="${slot.start_time}"
                                    data-end-time="${slot.end_time}"
                                    data-date="${date}">
                                    ${slot.formatted_time} with ${slot.guidance_associate_name}
                                </button>
                            </div>
                        `;
                    }
                });
                html += '</div>';
                container.innerHTML = html;

                document.querySelectorAll('.slot-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('btn-primary', 'btn-outline-primary'));
                        this.classList.remove('btn-outline-primary');
                        this.classList.add('btn-primary');
                        document.getElementById('reschedule_availability_id').value = this.dataset.availabilityId;
                        document.getElementById('reschedule_requested_date').value = this.dataset.date;
                        document.getElementById('reschedule_requested_start_time').value = this.dataset.startTime;
                        document.getElementById('reschedule_requested_end_time').value = this.dataset.endTime;
                    });
                });
            })
            .catch(error => {
                console.error('Error loading slots:', error);
                container.innerHTML = '<p class="text-danger">Error loading time slots. Please try again.</p>';
            });
    }
</script>
@endpush
@endsection
