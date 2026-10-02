@extends('layouts.app')

@section('title', ' - Notification')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2" style="color: var(--text-primary); font-weight: 700;">Notification Details</h1>
    <a href="{{ auth()->user()->isAdmin() ? route('admin.notifications') : (auth()->user()->isGuidanceAssociate() ? route('guidance.notifications') : route('student.notifications')) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--border-color-light); padding: 1rem 1.5rem;">
                <h5 class="mb-0" style="color: var(--text-primary);">
                    <i class="bi {{ $notification->icon }}" style="color: {{ $notification->typeColor }};"></i>
                    {{ $notification->title }}
                </h5>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                @if(auth()->user()->isStudent())
                    <dl class="row">
                        <dt class="col-sm-3">Title</dt>
                        <dd class="col-sm-9">{{ $notification->title }}</dd>

                        <dt class="col-sm-3">Message</dt>
                        <dd class="col-sm-9">{{ $notification->message }}</dd>

                        @if($notification->type === 'appointment_approved' && $notification->appointment?->appointment_mode === 'online' && $notification->facebook_profile_url)
                            <dt class="col-sm-3">Counselor Facebook</dt>
                            <dd class="col-sm-9">
                                <a href="{{ $notification->facebook_profile_url }}" target="_blank" rel="noopener noreferrer">
                                    Contact your counselor on Facebook
                                </a>
                            </dd>
                        @endif

                        <dt class="col-sm-3">Date/Time</dt>
                        <dd class="col-sm-9">{{ $notification->created_at->format('F d, Y g:i A') }}</dd>

                        @if($notification->related_appointment_id)
                            <dt class="col-sm-3">Related Appointment</dt>
                            <dd class="col-sm-9">
                                <a href="{{ route('student.appointments.show', $notification->appointment) }}">
                                    View appointment details
                                </a>
                                @if($notification->appointment && $notification->appointment->appointment_mode === 'online' && $notification->appointment->safe_online_meeting_url)
                                    <div class="mt-2">
                                        <a href="{{ $notification->appointment->safe_online_meeting_url }}" target="_blank" rel="noopener noreferrer">
                                            Join online appointment
                                        </a>
                                    </div>
                                @endif
                            </dd>
                        @endif
                    </dl>
                @else
                    <dl class="row">
                        <dt class="col-sm-3">Message</dt>
                        <dd class="col-sm-9">{{ $notification->message }}</dd>

                        <dt class="col-sm-3">Type</dt>
                        <dd class="col-sm-9">
                            <span class="badge" style="background: {{ $notification->typeColor }}; color: var(--badge-text-light);">{{ ucfirst($notification->type) }}</span>
                        </dd>

                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">
                            @if($notification->is_read)
                                <span class="badge" style="background: var(--light-blue); color: var(--badge-text-light);"><i class="bi bi-check-circle me-1"></i>Read</span>
                            @else
                                <span class="badge" style="background: var(--yellow); color: var(--badge-text-light);"><i class="bi bi-envelope me-1"></i>Unread</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Created</dt>
                        <dd class="col-sm-9">{{ $notification->created_at->format('F d, Y g:i A') }}</dd>

                        @if($notification->read_at)
                            <dt class="col-sm-3">Read At</dt>
                            <dd class="col-sm-9">{{ $notification->read_at->format('F d, Y g:i A') }}</dd>
                        @endif

                        @if($notification->related_appointment_id)
                            <dt class="col-sm-3">Related Appointment</dt>
                            <dd class="col-sm-9">
                                <a href="{{ auth()->user()->isAdmin() ? route('admin.appointments.show', $notification->appointment) : route('guidance.appointments.show', $notification->appointment) }}">
                                    Appointment #{{ $notification->related_appointment_id }}
                                </a>
                            </dd>
                        @endif

                        @if($notification->user_id && !auth()->user()->isAdmin())
                            <dt class="col-sm-3">Recipient</dt>
                            <dd class="col-sm-9">{{ $notification->user->full_name }} ({{ $notification->user->email }})</dd>
                        @endif
                    </dl>
                @endif

                @if(!$notification->is_read)
                    <form method="POST" action="{{ auth()->user()->isAdmin() ? route('admin.notifications.read', $notification) : (auth()->user()->isGuidanceAssociate() ? route('guidance.notifications.read', $notification) : route('student.notifications.read', $notification)) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Mark as Read
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
