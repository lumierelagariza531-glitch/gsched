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
        box-shadow: var(--shadow-sm);
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
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .table {
        margin-bottom: 0;
        background: transparent;
        color: var(--body-text);
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
        background: var(--action-blue);
    }
    
    .table thead th {
        background: var(--action-blue);
        color: var(--badge-text-light);
        border-bottom: none;
    }
    
    .table tbody {
        background: var(--card-bg);
    }
    
    .table td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color-light);
        color: var(--text-primary);
        background: var(--card-bg);
    }
    
    .table tbody tr {
        transition: background 0.2s ease;
    }
    
    .table tbody tr:hover {
        background: var(--bg-light);
    }

    .table tbody tr:hover > td {
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
        display: inline-block;
        line-height: 1.25;
    }
    
    .status-badge.pending {
        background: #F7AD19;
        color: #332400;
    }
    .status-badge.approved {
        background: var(--action-blue);
        color: var(--badge-text-light);
    }
    .status-badge.rejected {
        background: #C53045;
        color: var(--badge-text-light);
    }
    .status-badge.completed {
        background: #16803C;
        color: var(--badge-text-light);
    }
    .status-badge.cancelled {
        background: #A61B2B;
        color: var(--badge-text-light);
    }
    .status-badge.rescheduled {
        background: #6F42C1;
        color: var(--badge-text-light);
    }
    
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
        background: var(--action-blue);
        border: none;
        border-radius: 0.5rem;
        color: var(--badge-text-light);
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
        color: var(--badge-text-light);
    }
    
    .form-control, .form-select {
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.625rem 0.875rem;
        background-color: var(--card-bg);
        color: var(--body-text);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--medium-blue);
        box-shadow: 0 0 0 3px rgba(66, 158, 189, 0.3);
    }
    
    .pagination {
        margin: 0;
        gap: 2px;
        flex-wrap: wrap;
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
        color: var(--action-blue);
    }

    html[data-theme="dark"] .page-link:hover {
        color: var(--medium-blue);
    }
    
    .page-item.active .page-link {
        background: var(--action-blue);
        border-color: var(--action-blue);
        color: var(--badge-text-light);
    }
    
    .page-item.disabled .page-link {
        color: var(--text-muted);
        background: var(--bg-light);
        border-color: var(--border-color-light);
    }

    .page-link:focus-visible,
    .btn-icon:focus-visible,
    .btn-primary-action:focus-visible,
    .mobile-appt-actions .btn:focus-visible {
        outline: 3px solid var(--medium-blue);
        outline-offset: 2px;
        box-shadow: none;
    }
    
    @media (max-width: 991.98px) {
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
        .mobile-appt-card .appt-status {
            font-size: 0.75rem;
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
                <h1 class="h2 mb-1" style="color: var(--text-primary); font-weight: 700;">All Appointments</h1>
                <p class="text-muted mb-0">View and manage all system appointments</p>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Card -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
            <i class="bi bi-funnel me-2" style="color: var(--yellow);"></i>Filters
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label visually-hidden">Search</label>
                <input type="text" class="form-control" name="search" placeholder="Search student, associate, purpose..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label visually-hidden">Role</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="rescheduled" {{ request('status') == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label visually-hidden">Date From</label>
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label visually-hidden">Date To</label>
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn-primary-action w-100">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
        </form>
    </div>
</div>

@if($appointments->count() > 0)
    <!-- Mobile and tablet card view -->
    <div class="d-lg-none mobile-appointment-list">
        @foreach($appointments as $appointment)
            @php
                $statusClass = strtolower($appointment->status->name ?? 'pending');
                if (!in_array($statusClass, ['pending', 'approved', 'rejected', 'completed', 'cancelled', 'rescheduled'])) {
                    $statusClass = 'pending';
                }
            @endphp
            <div class="card mobile-appt-card">
                <div class="appt-header">
                    <div>
                        <div class="appt-title">{{ $appointment->student->full_name }}</div>
                        <div class="appt-guidance">{{ $appointment->guidanceAssociate->full_name ?? 'N/A' }}</div>
                    </div>
                    <span class="status-badge {{ $statusClass }}">{{ $appointment->status->label }}</span>
                </div>
                <div class="appt-details">
                    <div class="appt-detail-row">
                        <span class="appt-detail-label">Date</span>
                        <span class="appt-detail-value">{{ $appointment->formatted_date }}</span>
                    </div>
                    <div class="appt-detail-row">
                        <span class="appt-detail-label">Time</span>
                        <span class="appt-detail-value">{{ $appointment->formatted_time }}</span>
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
                    <a href="{{ route('admin.appointments.show', $appointment) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-eye me-1"></i>View
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Wide desktop table view -->
    <div class="d-none d-lg-block">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Guidance Associate</th>
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
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-medium" style="color: var(--text-primary);">{{ $appointment->student->full_name }}</div>
                                            <div class="small text-muted">{{ $appointment->student->email }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $appointment->guidanceAssociate->full_name ?? 'N/A' }}</div>
                                    </td>
                                    <td>{{ $appointment->formatted_date }}</td>
                                    <td>{{ $appointment->formatted_time }}</td>
                                    <td>{{ $appointment->appointment_mode === 'online' ? 'Online' : 'In Person' }}</td>
                                    <td>
                                        <div class="text-muted" style="max-width: 300px;">{{ Str::limit($appointment->purpose, 60) }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = strtolower($appointment->status->name ?? 'pending');
                                            if (!in_array($statusClass, ['pending', 'approved', 'rejected', 'completed', 'cancelled', 'rescheduled'])) {
                                                $statusClass = 'pending';
                                            }
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">{{ $appointment->status->label }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('admin.appointments.show', $appointment) }}" class="btn-icon" title="View Details">
                                                <i class="bi bi-eye fs-5"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body py-3">
            <div class="d-flex justify-content-center">
                {{ $appointments->appends(request()->query())->onEachSide(3)->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar-check fs-1" style="color: var(--text-muted); opacity: 0.5;"></i>
            <h4 class="mt-3 text-muted">No Appointments</h4>
            <p class="text-muted">No appointments match your search criteria.</p>
        </div>
    </div>
@endif
@endsection