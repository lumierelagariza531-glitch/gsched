@extends('layouts.app')

@section('styles')
    <style>
    body {
        background-color: var(--bg-light);
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
    
    .btn-book-appointment {
        background: var(--action-blue);
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
    
    .btn-book-appointment i {
        color: #FFFFFF;
        font-size: 1.1rem;
    }
    
    .btn-book-appointment:hover {
        background: var(--navy);
    }
    
    .btn-book-appointment:focus {
        background: var(--navy);
        box-shadow: 0 0 0 3px rgba(66, 158, 189, 0.3);
    }
    
    .kpi-card {
        border-radius: 0.75rem;
        padding: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(5, 63, 92, 0.08);
    }
    
    .kpi-link {
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    .kpi-link:hover .kpi-card {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(5, 63, 92, 0.12);
    }
    
    .kpi-card .kpi-count {
        font-size: clamp(1.25rem, 3.5vw, 1.75rem) !important;
        font-weight: 700;
        line-height: 1.15;
        overflow-wrap: anywhere;
    }

    .kpi-card .d-flex > div:first-child {
        min-width: 0;
    }

    .kpi-icon {
        flex: 0 0 40px;
    }
    
    .kpi-card.pending,
    .kpi-card.approved,
    .kpi-card.finished {
        min-height: 100px;
    }
    
    .kpi-card.pending {
        background: linear-gradient(135deg, rgba(247, 173, 25, 0.1) 0%, rgba(247, 173, 25, 0.05) 100%);
        border-left: 4px solid var(--yellow);
    }
    
    .kpi-card.approved {
        background: linear-gradient(135deg, rgba(66, 158, 189, 0.1) 0%, rgba(66, 158, 189, 0.05) 100%);
        border-left: 4px solid var(--medium-blue);
    }
    
    .kpi-card.finished {
        background: linear-gradient(135deg, rgba(159, 231, 245, 0.15) 0%, rgba(159, 231, 245, 0.05) 100%);
        border-left: 4px solid var(--light-blue);
    }
    
    .kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .kpi-icon.pending { background: rgba(247, 173, 25, 0.2); color: var(--yellow); }
    .kpi-icon.approved { background: rgba(66, 158, 189, 0.2); color: var(--medium-blue); }
    .kpi-icon.finished { background: rgba(159, 231, 245, 0.2); color: var(--status-available-text); }
    
    .status-badge {
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .status-badge.approved {
        background: var(--action-blue);
        color: #FFFFFF;
    }
    
    .btn-outline-custom {
        border: 2px solid var(--action-blue);
        color: var(--action-blue);
        border-radius: 0.5rem;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }
    
    .btn-outline-custom:hover {
        background: var(--action-blue);
        color: #FFFFFF;
    }
    
     .action-btn {
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
    }
    
    .action-btn:hover {
        border-color: var(--medium-blue);
        background: rgba(66, 158, 189, 0.05);
        transform: translateY(-2px);
        text-decoration: none;
    }

    .action-btn:focus-visible,
    .kpi-link:focus-visible {
        outline: 3px solid var(--medium-blue);
        outline-offset: 3px;
    }
    
     .quick-action-icon {
        font-size: 1.5rem;
        display: block;
        margin-bottom: 0.25rem;
        width: 40px;
        height: 40px;
        border-radius: 0.375rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .action-btn.info .quick-action-icon {
        background: rgba(66, 158, 189, 0.15);
    }
    
    .action-btn.warning .quick-action-icon {
        background: rgba(247, 173, 25, 0.2);
    }
    
    .action-btn.success .quick-action-icon {
        background: rgba(159, 231, 245, 0.25);
    }
    
    .action-btn.secondary .quick-action-icon {
        background: rgba(5, 63, 92, 0.1);
    }
    
    .action-btn.warning:hover {
        border-color: var(--yellow);
        background: rgba(247, 173, 25, 0.05);
    }
    
    .action-btn.info:hover {
        border-color: var(--medium-blue);
        background: rgba(66, 158, 189, 0.05);
    }
    
    .action-btn.secondary:hover {
        border-color: var(--navy);
        background: rgba(5, 63, 92, 0.05);
    }
    
    .type-badge {
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .type-badge.appointment_request { background: #274be8; color: #FFFFFF; }
    .type-badge.appointment_approved { background: #21db3d; color: #053F5C; }
    .type-badge.appointment_rejected { background: #db213a; color: #FFFFFF; }
    .type-badge.appointment_cancelled { background: #db213a; color: #FFFFFF; }
    .type-badge.feedback { background: #ffbf00; color: #053F5C; }

    .type-badge.appointment_rescheduled {
        background: rgba(5, 63, 92, 0.15);
        color: var(--text-primary);
    }

    html[data-theme="dark"] .type-badge.appointment_rescheduled {
        background: rgba(141, 221, 237, 0.16);
    }

    /* Dashboard Grid Layout */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }
    
    .dashboard-lower {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1rem;
        align-items: start;
    }

    @media (max-width: 991.98px) {
        .dashboard-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .dashboard-lower {
            grid-template-columns: 1fr;
        }

    }
    
    @media (max-width: 767.98px) {
        .mobile-greeting {
            margin-bottom: 0.5rem;
        }
        .mobile-greeting h4 {
            font-size: 1.25rem;
            font-weight: 700;
        }
        .mobile-greeting p {
            font-size: 0.875rem;
        }
        .btn-book-mobile {
            height: 48px;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: 0.75rem;
        }
        .mobile-stat-card {
            padding: 0.875rem 1rem;
            min-height: 70px;
        }
        .mobile-stat-card h3 {
            font-size: 1.5rem;
        }
        .mobile-stat-card p {
            font-size: 0.7rem;
        }
        .next-appt-mobile .card-body {
            padding: 1rem;
        }
        .appt-date-mobile {
            font-size: 1.1rem;
            font-weight: 600;
        }
        .appt-time-mobile {
            font-size: 0.9rem;
        }
        .mobile-table-card .card-body {
            padding: 0.75rem;
        }
        .mobile-full-width {
            width: 100% !important;
        }
        
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        .dashboard-lower {
            grid-template-columns: 1fr;
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Mobile Greeting -->
<div class="d-none d-md-block mb-4">
    <div>
        <div>
            <h1 class="h2 mb-1" style="color: var(--text-primary); font-weight: 700;">Student Dashboard</h1>
            <p class="text-muted mb-0">Manage your guidance appointments and requests</p>
        </div>
    </div>
</div>
<div class="d-md-none mb-3 mobile-greeting">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 style="color: var(--text-primary);" class="mb-1">Good morning, {{ auth()->user()->first_name }}</h4>
            <p class="text-muted mb-0">Manage your appointments and requests</p>
        </div>
    </div>
</div>

<!-- Dashboard Layout -->
<div class="dashboard-grid mb-4">
    <!-- Pending Requests -->
    <a href="{{ route('student.appointments.index') }}" class="kpi-link d-block">
        <div class="card kpi-card pending kpi-count-card h-100">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <p class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.05em;">Pending Requests</p>
                    <h2 class="kpi-count mb-0" style="color: var(--text-primary);">{{ $pendingCount }}</h2>
                </div>
                <div class="kpi-icon pending">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
        </div>
    </a>

    <!-- Approved Appointments -->
    <a href="{{ route('student.appointments.index') }}" class="kpi-link d-block">
        <div class="card kpi-card approved kpi-count-card h-100">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <p class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.05em;">Approved Appointments</p>
                    <h2 class="kpi-count mb-0" style="color: var(--text-primary);">{{ $approvedCount }}</h2>
                </div>
                <div class="kpi-icon approved">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </a>

    <!-- Finished Feedback -->
    <a href="{{ route('student.feedback.index') }}" class="kpi-link d-block">
        <div class="card kpi-card finished kpi-count-card h-100">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <p class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.05em;">Finished Feedback</p>
                    <h2 class="kpi-count mb-0" style="color: var(--text-primary);">{{ $finishedFeedbackCount }}</h2>
                </div>
                <div class="kpi-icon finished">
                    <i class="bi bi-star-fill fs-4"></i>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Lower Section: Next Appointment + Quick Actions -->
<div class="dashboard-lower">
    <!-- Next Appointment Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                <i class="bi bi-calendar-event me-2" style="color: var(--medium-blue);"></i>Next Appointment
            </h5>
        </div>
        <div class="card-body">
            @if($nextAppointment)
                 <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                      <div class="d-flex align-items-center gap-3">
                           <div class="bg-light bg-opacity-10 p-3 rounded-xl" style="background: rgba(159, 231, 245, 0.15) !important;">
                              <i class="bi bi-calendar-check fs-1" style="color: var(--medium-blue);"></i>
                           </div>
                           <div>
                               <h5 class="mb-1" style="color: var(--text-primary); font-weight: 600;">{{ $nextAppointment->formatted_date }}</h5>
                               <p class="mb-1 text-muted">{{ $nextAppointment->formatted_time }}</p>
                               <p class="mb-1 small text-muted"><i class="bi {{ $nextAppointment->appointment_mode === 'online' ? 'bi-camera-video' : 'bi-building' }} me-1"></i>{{ $nextAppointment->appointment_mode === 'online' ? 'Online' : 'In Person' }}</p>
                               <p class="mb-1"><strong style="color: var(--text-primary);">{{ $nextAppointment->guidanceAssociate->full_name }}</strong></p>
                               @if($nextAppointment->purpose)
                                   <p class="mb-1 small text-muted"><i class="bi bi-chat-text me-1"></i>{{ Str::limit($nextAppointment->purpose, 100) }}</p>
                               @endif
                           </div>
                      </div>
                      <div class="d-flex flex-column align-items-center gap-2" style="min-width: 140px;">
                          <span class="status-badge approved">{{ $nextAppointment->status->label }}</span>
                          <a href="{{ route('student.appointments.show', $nextAppointment) }}" class="btn-outline-custom">
                              <i class="bi bi-eye me-1"></i>View Details
                          </a>
                      </div>
                  </div>
              @else
                  <div class="text-center py-5">
                     <i class="bi bi-calendar-x fs-1" style="color: var(--text-muted); opacity: 0.5;"></i>
                     <p class="text-muted mt-3" style="color: var(--text-muted);">No upcoming appointments</p>
                      <a href="{{ route('student.schedules') }}" class="btn-book-appointment">
                          <i class="bi bi-plus-circle"></i>Book an Appointment
                      </a>
                  </div>
              @endif
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="card h-100">
        <div class="card-header bg-white p-2">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                <i class="bi bi-lightning me-2" style="color: var(--yellow);"></i>Quick Actions
            </h5>
        </div>
        <div class="card-body p-2">
            <div class="row g-2 h-100">
                <div class="col-12 col-sm-6">
                    <a href="{{ route('student.schedules') }}" class="action-btn info d-block h-100">
                        <i class="bi bi-calendar-week quick-action-icon" style="color: var(--medium-blue);"></i>
                        <span class="fw-medium d-block" style="color: var(--text-primary);">Available Schedules</span>
                    </a>
                </div>
                <div class="col-12 col-sm-6">
                    <a href="{{ route('student.appointments.index') }}" class="action-btn info d-block h-100">
                        <i class="bi bi-calendar-check quick-action-icon" style="color: var(--medium-blue);"></i>
                        <span class="fw-medium d-block" style="color: var(--text-primary);">My Appointments</span>
                    </a>
                </div>
                <div class="col-12 col-sm-6">
                    <a href="{{ route('student.schedules') }}" class="action-btn info d-block h-100">
                        <i class="bi bi-plus-circle quick-action-icon" style="color: var(--medium-blue);"></i>
                        <span class="fw-medium d-block" style="color: var(--text-primary);">Book Appointment</span>
                    </a>
                </div>
                <div class="col-12 col-sm-6">
                    <a href="{{ route('student.notifications') }}" class="action-btn warning d-block h-100">
                        <i class="bi bi-bell quick-action-icon" style="color: #F7AD19;"></i>
                        <span class="fw-medium d-block" style="color: var(--text-primary);">Notifications</span>
                    </a>
                </div>
                <div class="col-12 col-sm-6">
                    <a href="{{ route('student.feedback.index') }}" class="action-btn info d-block h-100">
                        <i class="bi bi-chat-text quick-action-icon" style="color: var(--medium-blue);"></i>
                        <span class="fw-medium d-block" style="color: var(--text-primary);">Feedback</span>
                    </a>
                </div>
                <div class="col-12 col-sm-6">
                    <a href="{{ route('profile') }}" class="action-btn secondary d-block h-100">
                        <i class="bi bi-person quick-action-icon" style="color: var(--text-primary);"></i>
                        <span class="fw-medium d-block" style="color: var(--text-primary);">Profile</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
