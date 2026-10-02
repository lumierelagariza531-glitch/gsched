@extends('layouts.app')

@section('title', ' - Manage Availability')

@section('styles')
<style>
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

    .kpi-card {
        border-radius: 1rem;
        padding: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(5, 63, 92, 0.08);
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        border: none;
        border-radius: 1rem;
    }

    .modal-header {
        border: none;
        border-radius: 1rem 1rem 0 0;
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

    /* Left Panel - Selected Date Information */
    .calendar-left-panel {
        background: var(--card-bg);
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        width: 320px;
        border-right: 1px solid var(--border-color);
        height: 440px;
        min-height: 440px;
        overflow-y: auto;
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

    .schedule-session-item {
        background: var(--bg-light);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.6rem 0.85rem;
        margin-bottom: 0.45rem;
        cursor: default;
        transition: all 0.2s ease;
    }

    .schedule-session-item .session-time {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 1rem;
    }

    .schedule-session-item .session-associate {
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    .schedule-session-item .session-slots {
        margin-top: 0.25rem;
    }

    .slots-badge {
        background: var(--light-blue);
        color: var(--text-primary);
        border-radius: 1rem;
        padding: 0.25rem 0.625rem;
        font-size: 0.8rem;
        font-weight: 500;
        display: inline-block;
    }

    .slot-badge-available { background: rgba(66, 158, 189, 0.15); color: var(--medium-blue); }
    .slot-badge-booked { background: rgba(242, 127, 12, 0.15); color: var(--orange); }

    .action-btn {
        background: var(--medium-blue);
        color: var(--badge-text-light);
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem 0.85rem;
        font-weight: 500;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        cursor: pointer;
        transition: all 0.2s ease;
        min-height: 32px;
    }

    .action-btn:hover {
        background: var(--navy);
    }

    .action-btn-secondary {
        background: var(--border-color);
        color: var(--text-primary);
    }

    .action-btn-secondary:hover {
        background: var(--text-muted);
        color: var(--badge-text-light);
    }

    .action-btn-sm {
        padding: 0.35rem 0.6rem;
        font-size: 0.75rem;
        min-height: 28px;
    }

    .no-appointment-msg {
        text-align: center;
        padding: 1.5rem 1rem;
        color: var(--text-muted);
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

    /* Right Panel - Calendar */
    .calendar-right-panel {
        flex: 1;
        padding: 1.5rem;
        height: 440px;
        min-height: 440px;
        overflow-y: auto;
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
        background: var(--medium-blue);
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
        width: 100%;
        padding: 0.4rem;
        position: relative;
        text-align: right;
        border: 1px solid var(--border-color-light);
        box-sizing: border-box;
        min-width: 0;
        font: inherit;
        line-height: inherit;
        appearance: none;
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
        cursor: default;
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

    @media (max-width: 768px) {

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
            overflow: visible;
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
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-1" style="color: var(--text-primary); font-weight: 700;">Manage Availability</h1>
        <p class="text-muted mb-0">Manage your availability schedule</p>
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

<div class="calendar-component">
    <!-- Left Panel - Selected Date Management -->
    <div class="calendar-left-panel">
        <div class="panel-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Selected Date</h5>
        </div>
        <div id="selectedDateInfo">
            <div class="no-appointment-msg">
                <i class="bi bi-calendar-check"></i>
                <p class="mb-0">Select a date to manage availability.</p>
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

<!-- Add Availability Modal (keeping admin routes) -->
<div class="modal fade" id="addAvailabilityModal" tabindex="-1" aria-labelledby="addAvailabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--medium-blue); color: var(--badge-text-light);">
                <h5 class="modal-title" id="addAvailabilityModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add Availability
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.availability.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="available_date" id="add_available_date">
                    <div class="mb-3">
                        <label for="add_guidance_associate" class="form-label">Provider <span class="text-danger">*</span></label>
                        <select class="form-select" id="add_guidance_associate" name="guidance_associate_id" required>
                            <option value="">Select a counselor or associate</option>
                            @foreach($guidanceAssociates as $associate)
                                <option value="{{ $associate->id }}">{{ $associate->full_name }} ({{ $associate->role->name === 'admin' ? 'School Guidance Counselor' : 'Department Guidance Associate' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="text" class="form-control" id="add_date_display" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="add_start_time" name="start_time" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="add_end_time" name="end_time" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add_slot_duration" class="form-label">Slot Duration (minutes) <span class="text-danger">*</span></label>
                        <select class="form-select" id="add_slot_duration" name="slot_duration" required>
                            <option value="15">15 minutes</option>
                            <option value="30" selected>30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">60 minutes</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Availability</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Availability data passed from PHP
    const availabilityMap = @json($availabilityMap ?? []);
    const appointmentsMap = @json($appointmentsMap ?? []);

    document.addEventListener('DOMContentLoaded', function() {
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let selectedDateStr = null;

        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                           'July', 'August', 'September', 'October', 'November', 'December'];

        const addModalEl = document.getElementById('addAvailabilityModal');
        const addModal = addModalEl ? new bootstrap.Modal(addModalEl) : null;
        function renderCalendar() {
            const monthYearDisplay = document.getElementById('currentMonthYear');
            monthYearDisplay.textContent = monthNames[currentMonth] + ' ' + currentYear;

            const calendarDays = document.getElementById('calendarGrid');
            calendarDays.innerHTML = '';

            const firstDay = new Date(currentYear, currentMonth, 1);
            const startingDay = firstDay.getDay();

            const prevMonthLastDate = new Date(currentYear, currentMonth, 0);
            for (let i = startingDay - 1; i >= 0; i--) {
                const dayEl = document.createElement('div');
                dayEl.className = 'calendar-day other-month';
                const dayNum = prevMonthLastDate.getDate() - i;
                dayEl.innerHTML = '<span class="calendar-day-number">' + dayNum + '</span>';
                calendarDays.appendChild(dayEl);
            }

            const today = new Date();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

            for (let d = 1; d <= daysInMonth; d++) {
                const day = new Date(currentYear, currentMonth, d);
                const dayStr = formatDate(day);

                const dayEl = document.createElement('div');
                dayEl.className = 'calendar-day';
                dayEl.setAttribute('data-date', dayStr);

                if (day.toDateString() === today.toDateString()) {
                    dayEl.classList.add('today');
                }

                if (dayStr === selectedDateStr) {
                    dayEl.classList.add('selected');
                }

                const dateData = availabilityMap[dayStr];
                const apptData = appointmentsMap[dayStr];
                if (dateData && dateData.length > 0) {
                    const hasBooked = dateData.some(function(s) { return s.is_booked; });
                    if (hasBooked) {
                        dayEl.classList.add('booked');
                    } else {
                        dayEl.classList.add('available');
                    }
                }
                if (apptData && apptData.length > 0 && !(dateData && dateData.length > 0)) {
                    dayEl.classList.add('booked');
                }

                dayEl.addEventListener('click', function() {
                    selectDate(day, dayStr);
                });

                dayEl.innerHTML = '<span class="calendar-day-number">' + d + '</span><span class="calendar-status-dot"></span>';
                calendarDays.appendChild(dayEl);
            }

            const totalCells = startingDay + daysInMonth;
            const remainingCells = (Math.ceil(totalCells / 7) * 7) - totalCells;
            for (let i = 1; i <= remainingCells; i++) {
                const dayEl = document.createElement('div');
                dayEl.className = 'calendar-day other-month';
                dayEl.innerHTML = '<span class="calendar-day-number">' + i + '</span>';
                calendarDays.appendChild(dayEl);
            }
        }

        function formatDate(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + d;
        }

        function selectDate(dateObj, dateStr) {
            selectedDateStr = dateStr;

            document.querySelectorAll('.calendar-day.selected').forEach(el => {
                el.classList.remove('selected');
            });
            const selectedEl = document.querySelector('.calendar-day[data-date="' + dateStr + '"]');
            if (selectedEl) {
                selectedEl.classList.add('selected');
            }

            updateSelectedDateInfo(dateObj, dateStr);
        }

        function getDayName(dayIndex) {
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            return days[dayIndex];
        }

        function updateSelectedDateInfo(dateObj, dateStr) {
            const panel = document.getElementById('selectedDateInfo');
            const dateData = availabilityMap[dateStr] || [];
            const apptData = appointmentsMap[dateStr] || [];

            let contentHtml = `
                <div class="date-number">${dateObj.getDate()}</div>
                <div class="date-day">${getDayName(dateObj.getDay())}</div>
                <div class="date-full">${dateObj.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</div>
            `;

            if (dateData.length > 0) {
                contentHtml += '<div class="detail-section"><div class="detail-label">Availability Sessions</div></div>';

                dateData.forEach(function(session) {
                    const editUrl = '{{ url('/') }}/admin/availability/' + session.availability_id + '/edit';
                    let actionButtons = `<a href="${editUrl}" class="action-btn action-btn-sm action-btn-secondary" title="Edit"><i class="bi bi-pencil"></i></a>`;
                    if (!session.is_booked) {
                        const deleteUrl = '{{ url('/') }}/admin/availability/' + session.availability_id;
                        actionButtons += `<button type="button" class="action-btn action-btn-sm action-btn-secondary" title="Delete" onclick="deleteAvailability('${deleteUrl}')"><i class="bi bi-trash"></i></button>`;
                    }

                    const associateName = session.guidance_associate_name || '';
                    contentHtml += `
                        <div class="schedule-session-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="session-time">${session.start_time} - ${session.end_time}</div>
                                    <div class="session-associate">${associateName} · Duration: ${session.slot_duration} min</div>
                                    <div class="session-slots">
                                        <span class="slots-badge slot-badge-available">${session.available_slots} available</span>
                                        <span class="slots-badge slot-badge-booked">${session.booked_slots} booked</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    ${actionButtons}
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            if (apptData.length > 0) {
                contentHtml += '<div class="detail-section"><div class="detail-label">Appointments</div></div>';

                apptData.forEach(function(appt) {
                    const statusColors = {
                        'pending': '#F7AD19',
                        'approved': '#429EBD',
                        'completed': '#21db3d',
                        'cancelled': '#db213a',
                        'rejected': '#db213a',
                        'reschedule_requested': '#F27F0C',
                        'rescheduled': '#053F5C'
                    };
                    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                    const statusColorMap = isDark ? {
                        'pending': '#00B4D8',
                        'approved': '#0077B6',
                        'completed': '#21db3d',
                        'cancelled': '#db213a',
                        'rejected': '#db213a',
                        'reschedule_requested': '#90E0EF',
                        'rescheduled': '#03045E'
                    } : statusColors;
                    const statusColor = statusColorMap[appt.status.toLowerCase()] || (isDark ? '#03045E' : '#053F5C');

                    contentHtml += `
                        <div class="schedule-session-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="session-time">${appt.student_name}</div>
                                    <div class="session-associate">${appt.time}</div>
                                </div>
                                <span class="slots-badge" style="background: ${statusColor}; color: var(--badge-text-light);">${appt.status_label}</span>
                            </div>
                        </div>
                    `;
                });
            }

            if (dateData.length === 0 && apptData.length === 0) {
                contentHtml += `
                    <div class="no-appointment-msg">
                        <i class="bi bi-calendar-x"></i>
                        <p class="mt-2 mb-0">No availability scheduled for this date.</p>
                    </div>
                `;
            }

            if (dateData.length === 0) {
                contentHtml += `
                    <div class="detail-section" style="margin-top: auto;">
                        <button type="button" class="action-btn w-100" onclick="openAddModal('${dateStr}')" style="margin-top: 0.5rem;">
                            <i class="bi bi-plus-circle"></i>Add Availability
                        </button>
                    </div>
                `;
            }

            panel.innerHTML = contentHtml;
        }

        window.openAddModal = function(dateStr) {
            if (!dateStr) {
                if (selectedDateStr) {
                    dateStr = selectedDateStr;
                } else {
                    alert('Please select a date first.');
                    return;
                }
            }
            document.getElementById('add_available_date').value = dateStr;
            const displayDate = new Date(dateStr + 'T00:00:00');
            document.getElementById('add_date_display').value = displayDate.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            addModal.show();
        };

        window.deleteAvailability = function(deleteUrl) {
            if (!confirm('Delete this availability?')) return;

            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to delete availability.');
                }
            })
            .catch(error => {
                alert('Failed to delete availability.');
            });
        };

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

        // Initialize calendar
        renderCalendar();
    });
</script>
@endsection