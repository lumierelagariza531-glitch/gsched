@extends('layouts.app')

@section('title', ' - Schedule Configuration')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Schedule Configuration</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-calendar-week me-2"></i>Working Hours</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="working_hours_start" class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="working_hours_start" name="working_hours_start" value="{{ $settings['working_hours_start']->setting_value ?? '08:00' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="working_hours_end" class="form-label">End Time</label>
                            <input type="time" class="form-control" id="working_hours_end" name="working_hours_end" value="{{ $settings['working_hours_end']->setting_value ?? '17:00' }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slot_duration" class="form-label">Default Slot Duration (minutes)</label>
                        <select class="form-select" id="slot_duration" name="appointment_duration">
                            <option value="15" {{ ($settings['appointment_duration']->setting_value ?? 30) == 15 ? 'selected' : '' }}>15 minutes</option>
                            <option value="30" {{ ($settings['appointment_duration']->setting_value ?? 30) == 30 ? 'selected' : '' }}>30 minutes</option>
                            <option value="45" {{ ($settings['appointment_duration']->setting_value ?? 30) == 45 ? 'selected' : '' }}>45 minutes</option>
                            <option value="60" {{ ($settings['appointment_duration']->setting_value ?? 30) == 60 ? 'selected' : '' }}>60 minutes</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="max_appointments_per_day" class="form-label">Max Appointments Per Day</label>
                        <input type="number" class="form-control" id="max_appointments_per_day" name="max_appointments_per_day" value="{{ $settings['max_appointments_per_day']->setting_value ?? 10 }}" min="1" max="50">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Schedule Settings</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-calendar-plus me-2"></i>Booking Rules</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="booking_advance_days" class="form-label">Booking Advance Days</label>
                        <input type="number" class="form-control" id="booking_advance_days" name="booking_advance_days" value="{{ $settings['booking_advance_days']->setting_value ?? 30 }}" min="1" max="365">
                        <div class="form-text">How many days in advance students can book appointments.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cancellation_cutoff_hours" class="form-label">Cancellation Cutoff (hours)</label>
                        <input type="number" class="form-control" id="cancellation_cutoff_hours" name="cancellation_cutoff_hours" value="{{ $settings['cancellation_cutoff_hours']->setting_value ?? 2 }}" min="0" max="168">
                        <div class="form-text">Minimum hours before appointment to allow cancellation.</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Booking Rules</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Information</h5>
            </div>
            <div class="card-body">
                <h6>Default Schedule Flow:</h6>
                <ol class="small">
                    <li>Guidance Associates create availability slots</li>
                    <li>Students view available dates and time slots</li>
                    <li>Students book appointments (Pending status)</li>
                    <li>Guidance Associates approve/reject requests</li>
                    <li>Approved appointments become confirmed</li>
                    <li>Students can request reschedule/cancel</li>
                    <li>After session, mark as completed</li>
                    <li>Students submit feedback</li>
                </ol>
                
                <hr>
                
                <h6>Slot Generation:</h6>
                <p class="small">When a Guidance Associate creates availability (e.g., 8:00 AM - 5:00 PM with 30-min slots), the system automatically generates time slots for students to book.</p>
            </div>
        </div>
    </div>
</div>
@endsection
