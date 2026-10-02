@extends('layouts.app')

@section('title', ' - System Settings')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">System Settings</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>General Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="system_name" class="form-label">System Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="system_name" name="system_name" value="{{ $settings['system_name']->setting_value ?? 'G-SCHED' }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="institution_name" class="form-label">Institution Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="institution_name" name="institution_name" value="{{ $settings['institution_name']->setting_value ?? 'College/University' }}" required>
                    </div>
                    
                    <hr>
                    
                    <h6>Appointment Settings</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="appointment_duration" class="form-label">Default Appointment Duration (minutes) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="appointment_duration" name="appointment_duration" value="{{ $settings['appointment_duration']->setting_value ?? 30 }}" min="15" max="120" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="max_appointments_per_day" class="form-label">Max Appointments Per Day <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="max_appointments_per_day" name="max_appointments_per_day" value="{{ $settings['max_appointments_per_day']->setting_value ?? 10 }}" min="1" max="50" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="working_hours_start" class="form-label">Working Hours Start <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="working_hours_start" name="working_hours_start" value="{{ $settings['working_hours_start']->setting_value ?? '08:00' }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="working_hours_end" class="form-label">Working Hours End <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="working_hours_end" name="working_hours_end" value="{{ $settings['working_hours_end']->setting_value ?? '17:00' }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="booking_advance_days" class="form-label">Booking Advance Days <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="booking_advance_days" name="booking_advance_days" value="{{ $settings['booking_advance_days']->setting_value ?? 30 }}" min="1" max="365" required>
                        <div class="form-text">How many days in advance students can book appointments.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cancellation_cutoff_hours" class="form-label">Cancellation Cutoff Hours <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="cancellation_cutoff_hours" name="cancellation_cutoff_hours" value="{{ $settings['cancellation_cutoff_hours']->setting_value ?? 2 }}" min="0" max="168" required>
                        <div class="form-text">Minimum hours before appointment to allow cancellation.</div>
                    </div>
                    
                    <hr>
                    
                    <h6>Notification Settings</h6>
                    
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="reminder_24h_enabled" name="reminder_24h_enabled" {{ ($settings['reminder_24h_enabled']->setting_value ?? 'true') == 'true' ? 'checked' : '' }}>
                        <label class="form-check-label" for="reminder_24h_enabled">24-hour Reminder</label>
                    </div>
                    
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="reminder_1h_enabled" name="reminder_1h_enabled" {{ ($settings['reminder_1h_enabled']->setting_value ?? 'true') == 'true' ? 'checked' : '' }}>
                        <label class="form-check-label" for="reminder_1h_enabled">1-hour Reminder</label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Google Calendar Integration</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted">Configure Google Calendar API credentials in your <code>.env</code> file:</p>
                <ul class="small text-muted">
                    <li><code>GOOGLE_CLIENT_ID</code></li>
                    <li><code>GOOGLE_CLIENT_SECRET</code></li>
                    <li><code>GOOGLE_REDIRECT_URI</code></li>
                </ul>
                <hr>
                <p class="small">When configured, approved appointments will automatically create Google Calendar events.</p>
            </div>
        </div>
        
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-shield me-2"></i>Security</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted">Password requirements: Minimum 8 characters</p>
                <p class="small text-muted">Session lifetime: {{ config('session.lifetime') }} minutes</p>
                <p class="small text-muted">CSRF protection: Enabled</p>
            </div>
        </div>
    </div>
</div>
@endsection
