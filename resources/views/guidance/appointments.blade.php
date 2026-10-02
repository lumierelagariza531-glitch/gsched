@extends('layouts.app')

@section('title', ' - My Appointments')

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

    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
        background: transparent;
    }

    .table th {
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border-color-light);
        padding: 1rem 1.5rem;
    }

    .table thead {
        background: #0077b6 !important;
    }

    .table thead th {
        background: #0077b6 !important;
        color: #FFFFFF !important;
        border-bottom: none;
    }

    .table tbody {
        background: var(--card-bg);
    }

    .table td {
        background: var(--card-bg);
    }

    .table td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color-light);
        color: var(--text-primary);
    }

    .table tbody tr {
        transition: background 0.2s ease;
    }

    .table tbody tr:hover {
        background: var(--bg-light);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
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
    .status-badge.approved { background: #429EBD; color: #032536; }
    .status-badge.rejected { background: #db213a; }
    .status-badge.completed { background: #21db3d; color: #032536; }
    .status-badge.cancelled { background: #F27F0C; color: #032536; }
    .status-badge.rescheduled { background: var(--border-color); color: var(--text-primary); }

    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        color: var(--text-muted);
        transition: all 0.2s ease;
    }

    .btn-icon:hover {
        border-color: var(--medium-blue);
        color: var(--text-primary);
        background: rgba(66, 158, 189, 0.1);
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

    .pagination {
        margin: 0;
        gap: 2px;
    }

    .page-link {
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        background: var(--card-bg);
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.25rem;
        min-width: 32px;
        text-align: center;
    }

    .page-link:hover {
        background: var(--bg-light);
        border-color: var(--medium-blue);
        color: var(--medium-blue);
    }

    .page-item.active .page-link {
        background: var(--medium-blue);
        border-color: var(--medium-blue);
        color: white;
    }

    .page-item.disabled .page-link {
        color: var(--text-muted);
        background: var(--bg-light);
        border-color: var(--border-color-light);
    }

    @media (max-width: 991.98px) {
        .table-responsive {
            overflow-x: visible;
        }
        .mobile-appointment-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .mobile-appt-card {
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1rem;
            background: var(--card-bg);
        }
        .mobile-appt-card .appt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }
        .mobile-appt-card .appt-title {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1rem;
        }
        .mobile-appt-card .appt-guidance {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .mobile-appt-card .appt-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            margin-top: 0.5rem;
        }
        .mobile-appt-card .appt-detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
        }
        .mobile-appt-card .appt-detail-label {
            color: var(--text-muted);
        }
        .mobile-appt-card .appt-detail-value {
            color: var(--body-text);
            font-weight: 500;
        }
        .mobile-appt-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.75rem;
            flex-wrap: wrap;
        }
        .mobile-appt-actions .btn {
            flex: 1;
            min-width: 100px;
            height: 44px;
        }
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h2 mb-1" style="color: var(--text-primary); font-weight: 700;">My Appointments</h1>
                <p class="text-muted mb-0">View and manage your approved appointment sessions</p>
            </div>
        </div>
    </div>
</div>

@if($appointments->count() > 0)
    <!-- Mobile Card View (hidden on desktop) -->
    <div class="d-lg-none mobile-appointment-list">
        @foreach($appointments as $appointment)
            @php
                $statusClass = strtolower($appointment->status->name ?? 'pending');
            @endphp
            <div class="card mobile-appt-card">
                <div class="appt-header">
                    <div>
                        <div class="appt-title">
                            @if($appointment->isHighSeverity() && !auth()->user()->isAdmin())
                                <strong style="color: var(--orange);">Confidential (High Severity)</strong>
                            @else
                                {{ $appointment->student->full_name }}
                            @endif
                        </div>
                        <div class="appt-guidance">{{ $appointment->formatted_time }}</div>
                    </div>
                    <span class="status-badge {{ $statusClass }}">{{ $appointment->status->label }}</span>
                </div>
                <div class="appt-details">
                    <div class="appt-detail-row">
                        <span class="appt-detail-label">Date</span>
                        <span class="appt-detail-value">{{ $appointment->formatted_date }}</span>
                    </div>
                    <div class="appt-detail-row">
                        <span class="appt-detail-label">Purpose</span>
                        <span class="appt-detail-value text-muted">{{ Str::limit($appointment->purpose, 60) }}</span>
                    </div>
                    <div class="appt-detail-row">
                        <span class="appt-detail-label">Service / Concern</span>
                        <span class="appt-detail-value">{{ $appointment->service_type ?? '—' }}{{ $appointment->concern_category ? ' / ' . $appointment->concern_category : '' }}</span>
                    </div>
                    <div class="appt-detail-row">
                        <span class="appt-detail-label">Mode</span>
                        <span class="appt-detail-value">{{ $appointment->appointment_mode === 'online' ? 'Online' : 'In Person' }}</span>
                    </div>
                </div>
                <div class="mobile-appt-actions">
                    <a href="{{ route('guidance.appointments.show', $appointment) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-eye me-1"></i>View
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Desktop Table View (hidden on mobile) -->
    <div class="d-none d-lg-block">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Mode</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                @php
                                    $statusClass = strtolower($appointment->status->name ?? 'pending');
                                @endphp
                                <tr>
                                    <td>
                                        @if($appointment->isHighSeverity() && !auth()->user()->isAdmin())
                                            <strong style="color: var(--orange);">Confidential (High Severity)</strong>
                                        @else
                                            <div>
                                                <div class="fw-medium" style="color: var(--text-primary);">{{ $appointment->student->full_name }}</div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $appointment->formatted_date }}</td>
                                    <td>{{ $appointment->formatted_time }}</td>
                                    <td>{{ $appointment->appointment_mode === 'online' ? 'Online' : 'In Person' }}</td>
                                    <td>
                                        <div class="text-muted" style="max-width: 300px;">{{ Str::limit($appointment->purpose, 60) }}</div>
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $statusClass }}">{{ $appointment->status->label }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('guidance.appointments.show', $appointment) }}" class="btn-icon" title="View Details">
                                            <i class="bi bi-eye fs-5"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent border-top p-3" style="border-color: var(--border-color-light);">
                    <div class="d-flex justify-content-center">
                        {{ $appointments->appends(request()->query())->onEachSide(3)->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-lg-none d-flex justify-content-center mt-3">
        {{ $appointments->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar-check fs-1" style="color: var(--text-muted); opacity: 0.5;"></i>
            <h4 class="mt-3 text-muted">No Appointments</h4>
            <p class="text-muted">You don't have any approved or completed appointments yet.</p>
        </div>
    </div>
@endif
@endsection
