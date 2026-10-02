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

    .schedule-card {
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        transition: all 0.2s ease;
    }

    .schedule-card:hover {
        border-color: var(--medium-blue);
        box-shadow: 0 8px 20px rgba(5, 63, 92, 0.08);
        transform: translateY(-2px);
    }

    .date-card {
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        background: var(--card-bg);
        transition: all 0.2s ease;
        padding: 1.5rem;
        cursor: pointer;
    }

    .date-card:hover {
        border-color: var(--medium-blue);
        box-shadow: 0 8px 20px rgba(5, 63, 92, 0.08);
        transform: translateY(-2px);
    }

    .date-card.no-slots {
        border-color: var(--border-color);
        background: var(--bg-light);
    }

    .date-card.no-slots:hover {
        transform: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .kpi-card {
        border-radius: 1rem;
        padding: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(5, 63, 92, 0.08);
    }

    .kpi-card.schedules {
        background: linear-gradient(135deg, rgba(66, 158, 189, 0.1) 0%, rgba(66, 158, 189, 0.05) 100%);
        border-left: 4px solid var(--medium-blue);
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .kpi-icon.schedules { background: rgba(66, 158, 189, 0.2); color: var(--medium-blue); }

    .modal-content {
        border: none;
        border-radius: 0.25rem;
        background: var(--card-bg);
        color: var(--text-primary);
    }

    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100% - 1rem);
    }

    .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }

    .modal-header {
        border: none;
        border-radius: 0.25rem 0.25rem 0 0;
    }

    .form-control, .form-select {
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.625rem 0.875rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--medium-blue);
        box-shadow: 0 0 0 3px rgba(66, 158, 189, 0.3);
    }

    .btn-primary {
        background: var(--action-blue);
        border: none;
        border-radius: 0.5rem;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        color: var(--badge-text-light);
        min-height: 44px;
    }

    .btn-primary:hover {
        background: var(--navy);
    }

    .btn-secondary {
        background: var(--border-color);
        border: none;
        border-radius: 0.5rem;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        color: var(--text-primary);
    }

    .btn-secondary:hover {
        background: var(--border-color-light);
    }

    .btn-disabled {
        background: var(--border-color-light);
        color: var(--text-muted);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
    }

    .form-label {
        color: var(--text-primary);
        font-weight: 500;
        margin-bottom: 0.375rem;
    }

    .readonly-field {
        background: var(--bg-light);
        border-color: var(--border-color);
        color: var(--text-primary);
    }

    .appointment-mode-option {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        min-height: 40px;
        padding: 0.375rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        background: var(--card-bg);
        color: var(--body-text);
        cursor: pointer;
    }

    .appointment-mode-option:focus-within {
        outline: 3px solid rgba(66, 158, 189, 0.35);
        outline-offset: 2px;
    }

    .appointment-mode-option:has(input:checked) {
        border-color: var(--action-blue);
        background: var(--bg-light);
        color: var(--text-primary);
    }

    html[data-theme="dark"] .appointment-mode-option:has(input:checked) {
        border-color: var(--medium-blue);
    }

    /* View Switch Control */
    .view-switch {
        display: inline-flex;
        align-items: center;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 0.375rem;
        box-shadow: 0 1px 3px rgba(5, 63, 92, 0.08);
    }

    .view-switch .view-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: none;
        background: transparent;
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        color: var(--text-muted);
    }

    .view-switch .view-btn:hover {
        color: var(--medium-blue);
    }

    .view-switch .view-btn.active {
        background: var(--action-blue);
        color: #FFFFFF !important;
    }

    .view-switch .view-btn.active i {
        color: #FFFFFF;
    }

    .view-switch .view-btn i {
        color: var(--medium-blue);
        font-size: 1.1rem;
    }

    /* Unified Calendar Component */
    .calendar-component {
        display: flex;
        gap: 1.5rem;
        background: var(--card-bg);
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(5, 63, 92, 0.08), 0 1px 2px rgba(5, 63, 92, 0.05);
        border: 1px solid var(--border-color);
        overflow: hidden;
        height: 440px;
        min-height: 440px;
        min-width: 0;
    }

    /* Left Panel - Date Details */
    .calendar-left-panel {
        background: var(--card-bg);
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        width: 320px;
        border-right: 1px solid var(--border-color);
        height: 440px;
        min-height: 440px;
    }

    .calendar-left-panel .panel-header {
        border-bottom: 1px solid var(--border-color-light);
        padding-bottom: 0.75rem;
        margin-bottom: 1rem;
    }

    .calendar-left-panel #selectedDateInfo {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .panel-header h5 {
        margin: 0;
        color: var(--text-primary);
        font-weight: 600;
    }

     .date-number {
        font-size: 3rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
    }

    .date-day {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--medium-blue);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0.25rem 0;
    }

     .date-full {
        font-size: 0.95rem;
        color: var(--text-muted);
    }

    .detail-section {
        margin-top: 0.75rem;
    }

    .detail-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }

    .detail-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .slots-badge {
        background: var(--light-blue);
        color: var(--text-primary);
        border-radius: 1rem;
        padding: 0.375rem 0.875rem;
        font-size: 0.9rem;
        font-weight: 500;
        display: inline-block;
    }

     .no-appointment-msg {
        text-align: center;
        padding: 1.5rem 1rem;
        color: var(--text-muted);
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .no-appointment-msg i {
        font-size: 2rem;
        opacity: 0.3;
        margin-bottom: 0.75rem;
    }

    .schedule-session-item {
        background: var(--bg-light);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        margin-bottom: 0.375rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .schedule-session-item:hover {
        border-color: var(--medium-blue);
        background: var(--card-bg);
    }

    .schedule-session-item.selected {
        border-color: var(--medium-blue);
        background: var(--card-bg);
        box-shadow: 0 0 0 2px rgba(66, 158, 189, 0.3);
    }

    .session-time {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 1rem;
    }

    .session-associate {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-top: 0.25rem;
    }

    .session-slots {
        margin-top: 0.375rem;
    }

    /* Right Panel - Calendar */
    .calendar-right-panel {
        flex: 1;
        padding: 1.5rem;
        height: 440px;
        min-height: 440px;
        min-width: 0;
        box-sizing: border-box;
    }

    .calendar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color-light);
    }

    .calendar-header .nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }

    .calendar-nav-btn {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        color: var(--text-primary);
        font-size: 1.1rem;
    }

    .calendar-nav-btn:hover {
        background: var(--action-blue);
        color: var(--badge-text-light);
    }

    .calendar-month-year {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        min-width: 180px;
        text-align: center;
        white-space: nowrap;
    }

    .calendar-inline-legend {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem 1rem;
        margin: -0.5rem 0 1rem;
        color: var(--text-muted);
        font-size: 0.75rem;
    }

    .calendar-inline-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        white-space: nowrap;
    }

    .calendar-inline-legend-marker {
        display: inline-block;
        width: 0.65rem;
        height: 0.65rem;
        flex: 0 0 auto;
        border-radius: 50%;
    }

    .calendar-inline-legend-marker.available {
        background: #22C55E;
    }

    .calendar-inline-legend-marker.booked {
        background: #EF4444;
    }

    .calendar-inline-legend-marker.today {
        background: transparent;
        border: 2px solid #FACC15;
    }

    .calendar-inline-legend-marker.selected {
        background: #3B82F6;
        border-radius: 0.15rem;
    }

    .provider-profile-card {
        border-left: 3px solid #429EBD !important;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }

    .provider-profile-card[data-provider-role="admin"] {
        border-left-color: #2148db !important;
        background: linear-gradient(110deg, rgba(33, 72, 219, 0.08), var(--bg-light) 62%) !important;
    }

    .provider-profile-card[data-provider-role="guidance_associate"] {
        border-left-color: #d9a900 !important;
        background: linear-gradient(110deg, rgba(255, 210, 0, 0.14), var(--bg-light) 62%) !important;
    }

    .provider-profile-avatar {
        width: 56px;
        height: 56px;
        flex: 0 0 56px;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(5, 63, 92, 0.16);
    }

    .provider-role-badge {
        display: inline-block;
        margin-top: 0.2rem;
        padding: 0.2rem 0.5rem;
        border-radius: 999px;
        background: rgba(66, 158, 189, 0.14);
        color: var(--text-primary);
        font-size: 0.65rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .provider-profile-card[data-provider-role="admin"] .provider-role-badge {
        background: rgba(33, 72, 219, 0.12);
        color: var(--text-primary);
    }

    .provider-profile-card[data-provider-role="guidance_associate"] .provider-role-badge {
        background: rgba(255, 210, 0, 0.24);
        color: var(--text-primary);
    }

    /* Fixed 7-column calendar grid */
    .calendar-weekday-header {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        background: var(--border-color);
        border-radius: 0.5rem;
        gap: 0;
        width: 100%;
    }

    .calendar-day-header {
        background: #0077b6;
        color: #FFFFFF;
        padding: 0.5rem;
        text-align: center;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .calendar-day {
        background: var(--card-bg);
        height: 50px;
        min-height: 50px;
        padding: 0.4rem;
        position: relative;
        text-align: right;
        border: 1px solid var(--border-color-light);
        box-sizing: border-box;
        min-width: 0;
        display: flex;
        align-items: flex-start;
        justify-content: flex-end;
        overflow: hidden;
    }

    .calendar-day-number {
        position: relative;
        z-index: 1;
        font-weight: 500;
        font-size: 0.9rem;
        color: var(--text-primary);
    }

    .calendar-status-dot {
        position: absolute;
        right: 6px;
        bottom: 6px;
        display: block;
        flex: 0 0 8px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: transparent;
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.85);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .calendar-day.other-month {
        background: var(--bg-light);
        color: var(--text-muted);
        opacity: 0.5;
    }

    .calendar-day.today::after {
        content: none;
    }

    .calendar-day.today {
        box-shadow: inset 0 0 0 2px #FACC15;
    }

    .calendar-day.available .calendar-day-number {
        color: #22C55E;
        font-weight: 600;
    }

    .calendar-day.available .calendar-status-dot {
        opacity: 1;
        visibility: visible;
        background: #22C55E;
    }

    .calendar-day.booked .calendar-day-number {
        color: #EF4444;
        font-weight: 600;
    }

    .calendar-day.booked .calendar-status-dot {
        opacity: 1;
        visibility: visible;
        background: #EF4444;
    }

    .calendar-day.booked {
        pointer-events: none;
    }

    .calendar-day.selected {
        background: #3B82F6;
        color: var(--badge-text-light);
        cursor: default;
    }

    .calendar-day.selected .calendar-day-number {
        color: var(--badge-text-light);
        font-weight: 700;
    }

    .calendar-day.available.selected .calendar-status-dot,
    .calendar-day.booked.selected .calendar-status-dot {
        opacity: 1;
        visibility: visible;
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.9);
    }

    .calendar-day:not(.other-month):hover {
        background: rgba(66, 158, 189, 0.05);
        cursor: pointer;
    }

    .calendar-day.available:hover {
        background: rgba(66, 158, 189, 0.1);
        cursor: pointer;
    }

    .calendar-day:focus-visible {
        z-index: 2;
        outline: 3px solid var(--medium-blue);
        outline-offset: -3px;
    }

    html[data-theme="dark"] .provider-profile-card[data-provider-role="admin"] {
        border-left-color: #8BA8FF !important;
        background: linear-gradient(110deg, rgba(139, 168, 255, 0.16), var(--bg-light) 62%) !important;
    }

    html[data-theme="dark"] .provider-profile-card[data-provider-role="guidance_associate"] {
        border-left-color: #F7D66D !important;
        background: linear-gradient(110deg, rgba(247, 214, 109, 0.16), var(--bg-light) 62%) !important;
    }

    html[data-theme="dark"] .provider-role-badge {
        background: rgba(104, 187, 213, 0.18);
        color: #B9F0FA;
    }

    html[data-theme="dark"] .provider-profile-card[data-provider-role="admin"] .provider-role-badge {
        background: rgba(139, 168, 255, 0.18);
        color: #D2DDFF;
    }

    html[data-theme="dark"] .provider-profile-card[data-provider-role="guidance_associate"] .provider-role-badge {
        background: rgba(247, 214, 109, 0.2);
        color: #FFE89A;
    }

    html[data-theme="dark"] .provider-profile-avatar {
        border-color: var(--border-color);
    }

    html[data-theme="dark"] .calendar-day.available .calendar-day-number {
        color: #4ADE80;
    }

    html[data-theme="dark"] .calendar-day.booked .calendar-day-number {
        color: #F87171;
    }

    html[data-theme="dark"] .calendar-day.selected {
        background: var(--action-blue);
    }

    @media (max-width: 768px) {

        .schedule-page-header {
            flex-wrap: wrap;
            align-items: flex-start !important;
            gap: 0.75rem;
        }

        .schedule-page-header > div:first-child,
        .schedule-page-header > div:last-child {
            min-width: 0;
            width: 100%;
        }

        .view-switch {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .view-switch .view-btn {
            flex: 1 1 0;
            justify-content: center;
            min-width: 0;
            padding: 0.4rem 0.3rem;
            font-size: 0.75rem;
            white-space: nowrap;
        }

        .calendar-component {
            flex-direction: column;
            height: auto;
            min-height: 0;
            max-height: none;
            gap: 0;
        }

        .calendar-left-panel {
            order: 2;
            border-right: none;
            border-bottom: 1px solid var(--border-color);
            width: auto;
            height: auto;
            min-height: 0;
            padding: 1rem;
        }

        .calendar-right-panel {
            order: 1;
            width: 100%;
            height: auto;
            min-height: auto;
            padding: 1rem;
        }

        .calendar-header {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            width: 100%;
            box-sizing: border-box;
        }

        .calendar-header .nav-item {
            width: 100%;
            gap: 0.25rem;
            justify-content: center;
        }

        .calendar-nav-btn {
            flex: 0 0 36px;
        }

        .calendar-month-year {
            flex: 1 1 auto;
            min-width: 0;
            max-width: 100%;
            white-space: normal;
            font-size: clamp(1rem, 4.2vw, 1.1rem);
        }

        .calendar-day-header {
            padding: 0.35rem 0.1rem;
            font-size: 0.65rem;
            letter-spacing: 0.02em;
        }

        .calendar-day {
            min-height: 60px;
            padding: 0.3rem;
        }
    }
</style>
@endsection

@section('content')
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@foreach ($errors->all() as $error)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endforeach

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="row mb-4">
    <div class="col-12">
        <div class="schedule-page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-1" style="color: var(--text-primary); font-weight: 700;">Available Schedules</h1>
                <p class="text-muted mb-0">Browse and book available counseling sessions</p>
            </div>
            <div class="d-flex align-items-center">
                <div class="view-switch">
                    <button type="button" class="view-btn active" data-view="calendar">
                        <i class="bi bi-calendar3"></i> Calendar View
                    </button>
                    <button type="button" class="view-btn" data-view="card">
                        <i class="bi bi-grid-1x2"></i> Card View
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    // Build available dates map for calendar
    $availableDatesMap = [];
    foreach ($availableDates as $date => $availabilities) {
        $dateStr = \Carbon\Carbon::parse($date)->format('Y-m-d');
        $slotCount = 0;
        $bookedSlotCount = 0;
        $availabilityData = [];

        foreach ($availabilities as $availability) {
            $start = \Carbon\Carbon::parse($dateStr . ' ' . $availability->start_time->format('H:i'));
            $end = \Carbon\Carbon::parse($dateStr . ' ' . $availability->end_time->format('H:i'));
            $duration = $availability->slot_duration;

            while ($start->copy()->addMinutes($duration) <= $end) {
                $slotEnd = $start->copy()->addMinutes($duration);

                $isBooked = \App\Models\Appointment::where('guidance_associate_id', $availability->guidance_associate_id)
                    ->where('appointment_date', $date)
                    ->where('start_time', $start->format('H:i'))
                    ->where('end_time', $slotEnd->format('H:i'))
                    ->whereHas('status', function ($q) {
                        $q->whereIn('name', ['pending', 'approved']);
                    })
                    ->exists();

                if (!$isBooked) {
                    $slotCount++;
                    $availabilityData[] = [
                        'availability_id' => $availability->id,
                        'guidance_associate_id' => $availability->guidance_associate_id,
                        'start_time' => $start->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'formatted_time' => $start->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                        'guidance_associate_name' => $availability->guidanceAssociate->full_name,
                        'guidance_associate_id_val' => $availability->guidance_associate_id,
                        'provider_role' => $availability->guidanceAssociate->role->name ?? null,
                        'school' => $availability->guidanceAssociate->school,
                        'profile_photo' => $availability->guidanceAssociate->profile_photo ? asset('storage/' . $availability->guidanceAssociate->profile_photo) : null,
                    ];
                } else {
                    $bookedSlotCount++;
                }

                $start = $slotEnd;
            }
        }

        $firstAvailability = $availabilities->first();
        $availableDatesMap[$dateStr] = [
            'slotCount' => $slotCount,
            'bookedSlotCount' => $bookedSlotCount,
            'availabilityData' => $availabilityData,
            'firstAvailability' => [
                'guidanceAssociate' => [
                    'id' => $firstAvailability->guidanceAssociate->id,
                    'full_name' => $firstAvailability->guidanceAssociate->full_name,
                    'role' => $firstAvailability->guidanceAssociate->role->name ?? null,
                    'school' => $firstAvailability->guidanceAssociate->school,
                    'profile_photo' => $firstAvailability->guidanceAssociate->profile_photo ? asset('storage/' . $firstAvailability->guidanceAssociate->profile_photo) : null,
                ],
            ],
        ];
    }
@endphp

<!-- Calendar View -->
<div id="calendarView" class="view-content">
    <div class="calendar-component">
        <!-- Left Panel - Selected Date Information -->
        <div class="calendar-left-panel">
            <div class="panel-header">
                <h5><i class="bi bi-info-circle me-2"></i>Selected Date</h5>
            </div>
            <div id="selectedDateInfo">
                <div class="no-appointment-msg">
                    <i class="bi bi-calendar-check"></i>
                    <p class="mb-0">Select an available date to view schedule details.</p>
                </div>
            </div>
        </div>

        <!-- Right Panel - Calendar -->
        <div class="calendar-right-panel">
            <div class="calendar-header">
                <div class="nav-item">
                    <button type="button" class="calendar-nav-btn" id="prevMonth" aria-label="Previous month">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <span class="calendar-month-year" id="currentMonthYear"></span>
                    <button type="button" class="calendar-nav-btn" id="nextMonth" aria-label="Next month">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="calendar-inline-legend" aria-label="Calendar legend">
                <span class="calendar-inline-legend-item"><span class="calendar-inline-legend-marker available" aria-hidden="true"></span>Available</span>
                <span class="calendar-inline-legend-item"><span class="calendar-inline-legend-marker booked" aria-hidden="true"></span>Booked</span>
                <span class="calendar-inline-legend-item"><span class="calendar-inline-legend-marker today" aria-hidden="true"></span>Today</span>
                <span class="calendar-inline-legend-item"><span class="calendar-inline-legend-marker selected" aria-hidden="true"></span>Selected Date</span>
            </div>

            <div class="calendar-weekday-header">
                @php
                    $daysOfWeek = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
                @endphp
                @foreach($daysOfWeek as $day)
                    <div class="calendar-day-header">{{ $day }}</div>
                @endforeach
            </div>
            <div class="calendar-grid" id="calendarGrid"></div>
        </div>
    </div>
</div>

<!-- Card View -->
<div id="cardView" class="view-content" style="display: none;">
    @if($availableDates->count() > 0)
        <div class="row g-4">
            @foreach($availableDates as $date => $availabilities)
                @php
                    $dateStr = \Carbon\Carbon::parse($date)->format('Y-m-d');
                    $dateObj = \Carbon\Carbon::parse($dateStr);
                    $firstAvailability = $availabilities->first();
                    $guidanceAssociate = $firstAvailability->guidanceAssociate;
                    $slotCount = 0;
                    $availabilityData = [];

                    foreach ($availabilities as $availability) {
                        $start = \Carbon\Carbon::parse($dateStr . ' ' . $availability->start_time->format('H:i'));
                        $end = \Carbon\Carbon::parse($dateStr . ' ' . $availability->end_time->format('H:i'));
                        $duration = $availability->slot_duration;

                        while ($start->copy()->addMinutes($duration) <= $end) {
                            $slotEnd = $start->copy()->addMinutes($duration);

                            $isBooked = \App\Models\Appointment::where('guidance_associate_id', $availability->guidance_associate_id)
                                ->where('appointment_date', $date)
                                ->where('start_time', $start->format('H:i'))
                                ->where('end_time', $slotEnd->format('H:i'))
                                ->whereHas('status', function ($q) {
                                    $q->whereIn('name', ['pending', 'approved']);
                                })
                                ->exists();

                            if (!$isBooked) {
                                $slotCount++;
                                $availabilityData[] = [
                                    'availability_id' => $availability->id,
                                    'guidance_associate_id' => $availability->guidance_associate_id,
                                    'start_time' => $start->format('H:i'),
                                    'end_time' => $slotEnd->format('H:i'),
                                    'formatted_time' => $start->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                                    'guidance_associate_name' => $availability->guidanceAssociate->full_name,
                                    'provider_role' => $availability->guidanceAssociate->role->name ?? null,
                                    'school' => $availability->guidanceAssociate->school,
                                    'profile_photo' => $availability->guidanceAssociate->profile_photo ? asset('storage/' . $availability->guidanceAssociate->profile_photo) : null,
                                ];
                            }

                            $start = $slotEnd;
                        }
                    }
                @endphp

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card date-card {{ $slotCount > 0 ? '' : 'no-slots' }}">
                        <div class="card-header" style="border-bottom: 1px solid var(--border-color-light);">
                            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                                <i class="bi bi-calendar-date me-2" style="color: var(--medium-blue);"></i>
                                {{ $dateObj->format('l, F d, Y') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-badge me-1" style="color: var(--medium-blue);"></i>
                                    <div>
                                        <p class="text-muted small mb-0">{{ $guidanceAssociate->role->name === 'admin' ? 'School Guidance Counselor' : ($guidanceAssociate->school ? $guidanceAssociate->school . ' Department Guidance Associate' : 'Department Guidance Associate') }}</p>
                                        <p class="fw-medium mb-0" style="color: var(--text-primary);">{{ $guidanceAssociate->full_name }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($slotCount > 0)
                                <div class="mb-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-clock me-1" style="color: var(--medium-blue);"></i>
                                        <p class="mb-0 fw-medium" style="color: var(--text-primary);">{{ $slotCount }} {{ $slotCount === 1 ? 'slot' : 'slots' }} available</p>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#bookingModal"
                                        data-date="{{ $dateStr }}"
                                        data-date-display="{{ $dateObj->format('F d, Y') }}"
                                        data-availability-json="{{ json_encode($availabilityData) }}">
                                    <i class="bi bi-plus-circle me-2"></i>Book Appointment
                                </button>
                            @else
                                <div class="mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-clock-slash me-1" style="color: var(--text-primary);"></i>
                                        <p class="mb-0 text-muted fw-medium">No available slots</p>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-disabled w-100" disabled>
                                    <i class="bi bi-slash-circle me-2"></i>No Slots Available
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x fs-1" style="color: var(--text-muted); opacity: 0.5;"></i>
                <h4 class="mt-3 text-muted">No Available Schedules</h4>
                <p class="text-muted">There are currently no available counseling schedules. Please check back later.</p>
            </div>
        </div>
    @endif
</div>

<div class="modal fade" id="bookingModal" tabindex="-1" aria-label="Book Appointment" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 0.5rem;">
            <form method="POST" action="{{ route('student.appointments.store') }}" id="bookingForm">
                @csrf
                <div class="modal-body" style="padding: 1rem;">
                    <input type="hidden" name="availability_id" id="modal_availability_id">
                    <input type="hidden" name="appointment_date" id="modal_appointment_date">
                    <input type="hidden" name="start_time" id="modal_start_time">
                    <input type="hidden" name="end_time" id="modal_end_time">

                    <div class="mb-1 p-1" id="provider_profile" style="background: var(--bg-light); border-radius: 0.375rem; border: 1px solid var(--border-color-light);">
                        <div class="d-flex align-items-center gap-2 mb-0">
                            <i class="bi bi-calendar-date" style="color: #0077b6; font-size: 1rem;"></i>
                            <span class="form-label small mb-0" style="color: var(--text-muted);">Date</span>
                        </div>
                        <div class="fw-medium small" style="color: var(--text-primary);" id="modal_date_display">September 17, 2026</div>
                    </div>

                    <div class="mb-2 p-2 provider-profile-card" id="matched_provider_card" style="background: var(--bg-light); border-radius: 0.5rem; border: 1px solid var(--border-color-light);">
                        <div class="d-flex align-items-center gap-2 mb-0">
                            <i class="bi bi-person-check-fill" style="color: #0077b6; font-size: 1rem;"></i>
                            <span class="form-label small mb-0 text-uppercase fw-semibold" style="color: var(--text-muted); letter-spacing: .04em;">Your matched provider</span>
                        </div>
                        <div class="d-flex align-items-center gap-3 mt-2">
                            <img id="modal_provider_photo" src="" alt="" class="rounded-circle provider-profile-avatar d-none">
                            <div id="modal_provider_initial" class="rounded-circle provider-profile-avatar d-flex align-items-center justify-content-center text-white d-none" style="background:var(--medium-blue);font-weight:700;font-size:1.15rem;"></div>
                            <div>
                                <div class="fw-semibold" style="color: var(--text-primary);" id="modal_guidance_associate_name">Choose a concern to see your provider</div>
                                <div class="provider-role-badge" id="modal_provider_role"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-1" id="concern_category_wrapper" style="overflow: visible;">
                        <label for="modal_concern_category" class="form-label small">Concern / Category <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="modal_concern_category" name="concern_category" required>
                            <option value="">Select a concern</option>
                            <option value="Academic Concerns">Academic Concerns</option>
                            <option value="Personal Concerns">Personal Concerns</option>
                            <option value="Family Concerns">Family Concerns</option>
                            <option value="Peer / Social Concerns">Peer / Social Concerns</option>
                            <option value="Emotional / Well-being Concerns">Emotional / Well-being Concerns</option>
                            <option value="Career / Educational Planning">Career / Educational Planning</option>
                            <option value="Financial Concerns">Financial Concerns</option>
                            <option value="Adjustment Concerns">Adjustment Concerns</option>
                            <option value="Behavioral Concerns">Behavioral Concerns</option>
                            <option value="Other">Other</option>
                        </select>
                        <div class="form-text small">Your service is assigned automatically based on your concern.</div>
                    </div>

                    <div class="mb-1">
                        <label for="modal_service_type_display" class="form-label small">Type of Service</label>
                        <input type="text" class="form-control form-control-sm readonly-field" id="modal_service_type_display" value="" placeholder="Choose a concern to see your service" aria-live="polite" readonly>
                    </div>

                    <input type="hidden" id="modal_service_type" name="service_type" value="">

                    <fieldset class="mb-2">
                        <legend class="form-label small mb-1">Appointment Mode <span class="text-danger">*</span></legend>
                        <div class="d-flex flex-wrap gap-2">
                            <label class="appointment-mode-option">
                                <input class="form-check-input" type="radio" name="appointment_mode" value="online" required>
                                <span><i class="bi bi-camera-video me-1"></i>Online</span>
                            </label>
                            <label class="appointment-mode-option">
                                <input class="form-check-input" type="radio" name="appointment_mode" value="in_person" required>
                                <span><i class="bi bi-building me-1"></i>In Person</span>
                            </label>
                        </div>
                    </fieldset>

                    <div class="mb-1">
                        <label class="form-label small">Time Slot <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="modal_time_slot" required disabled>
                            <option value="">Select a concern first</option>
                        </select>
                        <div class="invalid-feedback">Choose an available time slot.</div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label small">Purpose <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm" id="purpose" name="purpose" rows="2" required placeholder="Reason for appointment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-between" style="padding: 0.5rem 1rem; border-top: 1px solid var(--border-color-light);">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check-circle me-1"></i>Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let selectedSlot = null;
    let currentDateObj = null;
    let selectedSchedule = null;
    let modalSlots = [];
    const concernServiceMap = {
        'Academic Concerns': 'Coaching',
        'Personal Concerns': 'Counseling',
        'Family Concerns': 'Counseling',
        'Peer / Social Concerns': 'Counseling',
        'Emotional / Well-being Concerns': 'Counseling',
        'Career / Educational Planning': 'Coaching',
        'Financial Concerns': 'Coaching',
        'Adjustment Concerns': 'Counseling',
        'Behavioral Concerns': 'Counseling',
        'Other': 'Counseling'
    };

    function updateServiceTypeDisplay() {
        document.getElementById('modal_service_type_display').value =
            document.getElementById('modal_service_type').value;
    }

    // Available dates data passed from PHP
    const availableDatesMap = @json($availableDatesMap);
    const guidanceProviders = @json($guidanceProviders);
    const studentSchool = @json($studentSchool);

    document.addEventListener('DOMContentLoaded', function() {
        const bookingModal = document.getElementById('bookingModal');
        const bookingForm = document.getElementById('bookingForm');

        // View switching
        const viewButtons = document.querySelectorAll('.view-switch .view-btn');
        const viewContents = document.querySelectorAll('.view-content');

        viewButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetView = this.getAttribute('data-view');

                viewButtons.forEach(b => b.classList.remove('active'));
                viewContents.forEach(c => c.style.display = 'none');

                this.classList.add('active');
                document.getElementById(targetView + 'View').style.display = 'block';

                if (targetView === 'calendar') {
                    renderCalendar();
                }
            });
        });

        // Calendar state
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let selectedDateStr = null;

        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                           'July', 'August', 'September', 'October', 'November', 'December'];

        function renderCalendar() {
            const monthYearDisplay = document.getElementById('currentMonthYear');
            monthYearDisplay.textContent = monthNames[currentMonth] + ' ' + currentYear;

            const calendarDays = document.getElementById('calendarGrid');
            calendarDays.innerHTML = '';

            // First day of the month (0 = Sunday, 1 = Monday, etc.)
            const firstDay = new Date(currentYear, currentMonth, 1);
            const startingDay = firstDay.getDay();

            // Previous month's trailing days to fill first week
            const prevMonthLastDate = new Date(currentYear, currentMonth, 0);
            for (let i = startingDay - 1; i >= 0; i--) {
                const dayEl = document.createElement('div');
                dayEl.className = 'calendar-day other-month';
                const dayNum = prevMonthLastDate.getDate() - i;
                dayEl.innerHTML = '<span class="calendar-day-number">' + dayNum + '</span>';
                calendarDays.appendChild(dayEl);
            }

            // Current month's days
            const today = new Date();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

            for (let d = 1; d <= daysInMonth; d++) {
                const day = new Date(currentYear, currentMonth, d);
                const dayStr = formatDate(day);
                const dateData = availableDatesMap[dayStr];
                const hasAvailableSlots = dateData && dateData.slotCount > 0;
                const hasBookedSlots = dateData && dateData.bookedSlotCount > 0;

                const dayEl = document.createElement(hasAvailableSlots ? 'button' : 'div');
                dayEl.className = 'calendar-day';
                dayEl.setAttribute('data-date', dayStr);
                if (hasAvailableSlots) {
                    dayEl.type = 'button';
                    dayEl.setAttribute('aria-pressed', dayStr === selectedDateStr ? 'true' : 'false');
                    dayEl.setAttribute('aria-label', `${dateData.slotCount} available appointment ${dateData.slotCount === 1 ? 'slot' : 'slots'} on ${day.toLocaleDateString()}`);
                    dayEl.title = `${dateData.slotCount} available ${dateData.slotCount === 1 ? 'slot' : 'slots'}`;
                }

                // Check if today
                if (day.toDateString() === today.toDateString()) {
                    dayEl.classList.add('today');
                }

                // Check if selected
                if (dayStr === selectedDateStr) {
                    dayEl.classList.add('selected');
                }

                // Check if available (has schedules with slots)
                if (hasAvailableSlots) {
                    dayEl.classList.add('available');
                } else if (hasBookedSlots) {
                    dayEl.classList.add('booked');
                    dayEl.setAttribute('aria-label', 'Fully booked on ' + day.toLocaleDateString());
                    dayEl.title = 'Fully booked';
                }

                dayEl.innerHTML = '<span class="calendar-day-number">' + d + '</span><span class="calendar-status-dot"></span>';
                calendarDays.appendChild(dayEl);
            }

            // Next month's leading days to fill last row
            const totalCells = startingDay + daysInMonth;
            const remainingCells = (Math.ceil(totalCells / 7) * 7) - totalCells;
            for (let i = 1; i <= remainingCells; i++) {
                const dayEl = document.createElement('div');
                dayEl.className = 'calendar-day other-month';
                dayEl.innerHTML = '<span class="calendar-day-number">' + i + '</span>';
                calendarDays.appendChild(dayEl);
            }
        }

        document.getElementById('calendarGrid').addEventListener('click', function(event) {
            const dayButton = event.target.closest('button.calendar-day.available[data-date]');
            if (!dayButton) {
                return;
            }

            const [year, month, day] = dayButton.dataset.date.split('-').map(Number);
            selectDate(new Date(year, month - 1, day), dayButton.dataset.date);
            const dateData = availableDatesMap[dayButton.dataset.date];
            openBookingModal({
                date: dayButton.dataset.date,
                dateDisplay: new Date(year, month - 1, day).toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                }),
                availabilityData: dateData.availabilityData
            }, dayButton);
        });

        function formatDate(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + d;
        }

        function selectDate(dateObj, dateStr) {
            selectedDateStr = dateStr;
            currentDateObj = dateObj;
            selectedSchedule = null;

            // Update selected state in calendar
            document.querySelectorAll('.calendar-day.selected').forEach(el => {
                el.classList.remove('selected');
                if (el.matches('button')) {
                    el.setAttribute('aria-pressed', 'false');
                }
            });
            const selectedEl = document.querySelector('.calendar-day[data-date="' + dateStr + '"]');
            if (selectedEl) {
                selectedEl.classList.add('selected');
                if (selectedEl.matches('button')) {
                    selectedEl.setAttribute('aria-pressed', 'true');
                }
            }

            updateSelectedDateInfo(dateObj, dateStr);
        }

        function getDayName(dayIndex) {
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            return days[dayIndex];
        }

        function updateSelectedDateInfo(dateObj, dateStr) {
            const panel = document.getElementById('selectedDateInfo');
            const dateData = availableDatesMap[dateStr];

            // Reset panel structure
            panel.innerHTML = '';

            if (dateData && dateData.slotCount > 0) {
                const firstAvail = dateData.firstAvailability;
                const associateName = firstAvail ? firstAvail.guidanceAssociate.full_name : 'Unknown';

                let contentHtml = `
                    <div class="date-number">${dateObj.getDate()}</div>
                    <div class="date-day">${getDayName(dateObj.getDay())}</div>
                    <div class="date-full">${dateObj.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</div>
                `;

                // Check if multiple schedules exist
                if (dateData.availabilityData && dateData.availabilityData.length > 0) {
                    // Group by guidance associate
                    const sessionsByAssoc = {};
                    dateData.availabilityData.forEach(slot => {
                        const key = slot.guidance_associate_name;
                        if (!sessionsByAssoc[key]) {
                            sessionsByAssoc[key] = [];
                        }
                        sessionsByAssoc[key].push(slot);
                    });

                    contentHtml += '<div class="detail-section"><div class="detail-label">Available Sessions</div></div>';
                    let firstSession = true;
                    for (const [assoc, slots] of Object.entries(sessionsByAssoc)) {
                        const firstSlot = slots[0];
                        const providerRole = firstSlot.provider_role === 'admin'
                            ? 'School Guidance Counselor'
                            : (firstSlot.school
                                ? firstSlot.school + ' Department Guidance Associate'
                                : 'Department Guidance Associate');
                        contentHtml += `<div class="schedule-session-item ${firstSession ? 'selected' : ''}"
                                 data-assoc="${assoc}"
                                 data-assoc-id="${firstSlot.guidance_associate_id}">
                            <div class="session-time">${firstSlot.formatted_time}</div>
                            <div class="session-associate">${assoc}</div>
                            <div class="text-muted small">${providerRole}</div>
                            <div class="session-slots">
                                <span class="slots-badge">${slots.length} ${slots.length === 1 ? 'slot' : 'slots'}</span>
                            </div>
                        </div>`;
                        if (firstSession) firstSession = false;
                    }
                } else {
                    const providerRole = firstAvail && firstAvail.guidanceAssociate.role === 'admin'
                        ? 'School Guidance Counselor'
                        : (firstAvail && firstAvail.guidanceAssociate.school
                            ? firstAvail.guidanceAssociate.school + ' Department Guidance Associate'
                            : 'Department Guidance Associate');
                    contentHtml += '<div class="detail-section"><div class="detail-label">' + providerRole + '</div><div class="detail-value">' + associateName + '</div></div>';
                    contentHtml += '<div class="detail-section"><div class="detail-label">Available Slots</div><div class="detail-value"><span class="slots-badge">' + dateData.slotCount + ' ' + (dateData.slotCount === 1 ? 'slot' : 'slots') + '</span></div></div>';
                }

                // Wrap content in a div that can scroll
                const contentDiv = document.createElement('div');
                contentDiv.innerHTML = contentHtml;
                panel.appendChild(contentDiv);

                panel.querySelectorAll('.schedule-session-item').forEach(function(item) {
                    item.addEventListener('click', function() {
                        panel.querySelectorAll('.schedule-session-item').forEach(i => i.classList.remove('selected'));
                        this.classList.add('selected');
                        selectedSchedule = {
                            id: this.getAttribute('data-assoc-id'),
                            associate: this.getAttribute('data-assoc')
                        };
                    });
                });
            } else {
                panel.innerHTML = `
                    <div class="date-number">${dateObj.getDate()}</div>
                    <div class="date-day">${getDayName(dateObj.getDay())}</div>
                    <div class="date-full">${dateObj.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</div>
                    <div class="no-appointment-msg">
                        <i class="bi bi-calendar-x"></i>
                        <p class="mt-2 mb-0">No available appointments on this date.</p>
                    </div>
                `;
            }
        }

        // Navigation handlers
        document.getElementById('prevMonth').addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });

        let pendingBookingPayload = null;

        function bookingPayloadFromButton(button) {
            return {
                date: button.getAttribute('data-date'),
                dateDisplay: button.getAttribute('data-date-display'),
                availabilityData: button.getAttribute('data-availability-json')
            };
        }

        function initializeBookingModal(payload) {
            const date = payload.date;
            const dateDisplay = payload.dateDisplay;
            const availability = payload.availabilityData;

            document.getElementById('modal_date_display').textContent = dateDisplay;
            document.getElementById('modal_guidance_associate_name').textContent = 'Choose a concern to see your provider';
            document.getElementById('modal_provider_role').textContent = '';
            document.getElementById('matched_provider_card').removeAttribute('data-provider-role');
            document.getElementById('modal_provider_photo').classList.add('d-none');
            document.getElementById('modal_provider_initial').classList.add('d-none');

            const timeSlotSelect = document.getElementById('modal_time_slot');
            timeSlotSelect.innerHTML = '<option value="">Select a time slot</option>';

            try {
                modalSlots = typeof availability === 'string' ? JSON.parse(availability) : availability;
                if (!Array.isArray(modalSlots)) {
                    modalSlots = [];
                }
            } catch (e) {
                modalSlots = [];
                console.error('Error parsing availability data:', e);
            }

            selectedSlot = null;
            document.getElementById('bookingForm').reset();
            timeSlotSelect.value = '';

            document.getElementById('modal_appointment_date').value = date;
            document.getElementById('modal_availability_id').value = '';
            document.getElementById('modal_start_time').value = '';
            document.getElementById('modal_end_time').value = '';
            document.getElementById('modal_service_type').value = '';
            updateServiceTypeDisplay();
            timeSlotSelect.disabled = true;
            const concernSelect = document.getElementById('modal_concern_category');
            concernSelect.value = '';
            concernSelect.disabled = false;
            document.getElementById('modal_provider_role').textContent = '';
            document.getElementById('modal_provider_photo').classList.add('d-none');
            document.getElementById('modal_provider_initial').classList.add('d-none');
        }

        function openBookingModal(payload, trigger) {
            pendingBookingPayload = payload;
            bootstrap.Modal.getOrCreateInstance(bookingModal).show(trigger || undefined);
        }

        bookingModal.addEventListener('show.bs.modal', function(event) {
            const payload = pendingBookingPayload || (event.relatedTarget
                ? bookingPayloadFromButton(event.relatedTarget)
                : null);
            pendingBookingPayload = null;
            if (payload) {
                initializeBookingModal(payload);
            }
        });

        function providerRoleLabel(providerRole, school) {
            if (providerRole === 'admin') {
                return 'School Guidance Counselor';
            }

            const department = school || studentSchool;
            return department
                ? department + ' Department Guidance Associate'
                : 'Department Guidance Associate';
        }

        function missingProviderMessage(providerRole) {
            if (providerRole === 'guidance_associate') {
                return studentSchool
                    ? 'No Guidance Associate assigned for ' + studentSchool
                    : 'Your student profile does not have a school assigned';
            }

            return 'No active School Guidance Counselor is available';
        }

        function updateProviderProfile(provider, providerRole, emptyMessage) {
            const nameElement = document.getElementById('modal_guidance_associate_name');
            const roleElement = document.getElementById('modal_provider_role');
            const photo = document.getElementById('modal_provider_photo');
            const initial = document.getElementById('modal_provider_initial');

            if (!provider) {
                nameElement.textContent = emptyMessage || missingProviderMessage(providerRole);
                roleElement.textContent = providerRoleLabel(providerRole);
                document.getElementById('matched_provider_card').dataset.providerRole = providerRole;
                photo.classList.add('d-none');
                initial.classList.add('d-none');
                return;
            }

            nameElement.textContent = provider.guidance_associate_name || 'Guidance provider';
            roleElement.textContent = providerRoleLabel(provider.provider_role, provider.school);
            document.getElementById('matched_provider_card').dataset.providerRole = provider.provider_role;
            if (provider.profile_photo) {
                photo.src = provider.profile_photo;
                photo.alt = provider.guidance_associate_name || 'Guidance provider';
                photo.classList.remove('d-none');
                initial.classList.add('d-none');
            } else {
                photo.removeAttribute('src');
                photo.classList.add('d-none');
                initial.textContent = (provider.guidance_associate_name || '?').charAt(0).toUpperCase();
                initial.classList.remove('d-none');
            }
        }

        function getEligibleRole(concern) {
            return ['Academic Concerns', 'Financial Concerns', 'Career / Educational Planning'].includes(concern)
                ? 'guidance_associate'
                : concern ? 'admin' : '';
        }

        function getProviderForRole(role, matchingSlots) {
            const isSchoolMatch = provider => role !== 'guidance_associate'
                || (studentSchool && provider.school === studentSchool);
            const slotProvider = matchingSlots.find(provider =>
                provider.provider_role === role && isSchoolMatch(provider));
            if (slotProvider) {
                return slotProvider;
            }

            return guidanceProviders.find(provider =>
                provider.provider_role === role && isSchoolMatch(provider)) || null;
        }

        function renderEligibleSlots() {
            const service = document.getElementById('modal_service_type').value;
            const concern = document.getElementById('modal_concern_category').value;
            const eligibleRole = getEligibleRole(concern);
            const displayRole = eligibleRole;
            const timeSlotSelect = document.getElementById('modal_time_slot');
            timeSlotSelect.innerHTML = '<option value="">' + (service && concern ? 'Select a time slot' : 'Select a concern first') + '</option>';
            const eligibleSlots = service && concern
                ? modalSlots.filter(slot => slot.provider_role === eligibleRole
                    && (eligibleRole !== 'guidance_associate'
                        || (studentSchool && slot.school === studentSchool)))
                : [];
            const displayProvider = displayRole
                ? getProviderForRole(displayRole, concern ? eligibleSlots : modalSlots)
                : null;
            const providers = new Map();
            eligibleSlots.forEach(function(slot) {
                providers.set(String(slot.guidance_associate_id), slot);
            });
            eligibleSlots.forEach(function(slot) {
                const option = document.createElement('option');
                option.value = slot.availability_id + '|' + slot.start_time + '|' + slot.end_time;
                option.textContent = providers.size > 1
                    ? slot.formatted_time + ' · ' + slot.guidance_associate_name
                    : slot.formatted_time;
                option.dataset.name = slot.guidance_associate_name || '';
                option.dataset.role = slot.provider_role || '';
                option.dataset.photo = slot.profile_photo || '';
                timeSlotSelect.appendChild(option);
            });
            timeSlotSelect.disabled = !service || !concern || eligibleSlots.length === 0;
            if (service && concern && eligibleSlots.length === 0) {
                timeSlotSelect.options[0].textContent = 'No matching provider has open slots on this date';
            }
            selectedSlot = null;
            document.getElementById('modal_availability_id').value = '';
            if (!service || !concern) {
                if (displayProvider) {
                    updateProviderProfile(displayProvider, displayRole);
                } else if (service) {
                    updateProviderProfile(null, displayRole, missingProviderMessage(displayRole));
                } else {
                    document.getElementById('modal_guidance_associate_name').textContent = 'Choose a concern to see your provider';
                    document.getElementById('modal_provider_role').textContent = '';
                    document.getElementById('matched_provider_card').removeAttribute('data-provider-role');
                    document.getElementById('modal_provider_photo').classList.add('d-none');
                    document.getElementById('modal_provider_initial').classList.add('d-none');
                }
            } else if (displayProvider) {
                updateProviderProfile(displayProvider, eligibleRole);
            } else if (providers.size > 1) {
                document.getElementById('modal_guidance_associate_name').textContent = 'Select a time slot';
                document.getElementById('modal_provider_role').textContent = providerRoleLabel(eligibleRole);
                document.getElementById('matched_provider_card').dataset.providerRole = eligibleRole;
                document.getElementById('modal_provider_photo').classList.add('d-none');
                document.getElementById('modal_provider_initial').classList.add('d-none');
            } else {
                updateProviderProfile(null, eligibleRole, missingProviderMessage(eligibleRole));
            }
        }

        document.getElementById('modal_concern_category').addEventListener('change', function() {
            this.classList.remove('is-invalid');
            const matchedService = concernServiceMap[this.value] || '';
            document.getElementById('modal_service_type').value = matchedService;
            updateServiceTypeDisplay();
            renderEligibleSlots();
        });

        document.getElementById('modal_time_slot').addEventListener('change', function() {
            const selected = this.value;
            if (selected) {
                const parts = selected.split('|');
                document.getElementById('modal_availability_id').value = parts[0];
                document.getElementById('modal_start_time').value = parts[1];
                document.getElementById('modal_end_time').value = parts[2];
                this.classList.remove('is-invalid');
                selectedSlot = parts;
                const provider = modalSlots.find(s => String(s.availability_id) === parts[0]);
                updateProviderProfile(provider, provider ? provider.provider_role : '');
            } else {
                selectedSlot = null;
                renderEligibleSlots();
            }
        });

        bookingForm.addEventListener('submit', function(e) {
            const concernSelect = document.getElementById('modal_concern_category');
            if (!concernSelect.value) {
                e.preventDefault();
                concernSelect.classList.add('is-invalid');
                return false;
            }
            if (!selectedSlot) {
                e.preventDefault();
                const timeSlotSelect = document.getElementById('modal_time_slot');
                timeSlotSelect.classList.add('is-invalid');
                return false;
            }
        });

        bookingModal.addEventListener('hidden.bs.modal', function() {
            selectedSlot = null;
            modalSlots = [];
            document.getElementById('bookingForm').reset();
            document.getElementById('modal_time_slot').innerHTML = '<option value="">Select a time slot</option>';
            const timeSlotSelect = document.getElementById('modal_time_slot');
            if (timeSlotSelect) {
                timeSlotSelect.disabled = true;
                timeSlotSelect.classList.remove('is-invalid');
            }
            const concernSelect = document.getElementById('modal_concern_category');
            if (concernSelect) {
                concernSelect.value = '';
                concernSelect.disabled = false;
                concernSelect.classList.remove('is-invalid');
            }
            document.getElementById('modal_service_type').value = '';
            updateServiceTypeDisplay();
            document.getElementById('modal_guidance_associate_name').textContent = 'Choose a concern to see your provider';
            document.getElementById('modal_provider_role').textContent = '';
            document.getElementById('matched_provider_card').removeAttribute('data-provider-role');
            document.getElementById('modal_provider_photo').classList.add('d-none');
            document.getElementById('modal_provider_initial').classList.add('d-none');
        });

        // Initialize calendar
        renderCalendar();
    });
</script>
@endsection
