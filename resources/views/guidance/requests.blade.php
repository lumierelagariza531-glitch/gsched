@extends('layouts.app')

@section('title', ' - Appointment Requests')

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
    .status-badge.reschedule_requested { background: #429EBD; color: #032536; }

    .badge-high-severity {
        background: var(--orange);
        color: #032536;
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-weight: 600;
        margin-left: 0.375rem;
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

    .btn-icon.success:hover {
        border-color: #21db3d;
        color: var(--text-primary);
        background: rgba(33, 219, 61, 0.1);
    }

    .btn-icon.danger:hover {
        border-color: var(--orange);
        color: var(--text-primary);
        background: rgba(242, 127, 12, 0.1);
    }

    .btn-icon.warning:hover {
        border-color: var(--yellow);
        color: var(--text-primary);
        background: rgba(247, 173, 25, 0.1);
    }

    .btn-icon.secondary:hover {
        border-color: var(--text-muted);
        color: var(--text-primary);
        background: var(--bg-light);
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
        .mobile-request-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .mobile-request-card {
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1rem;
            background: var(--card-bg);
        }
        .mobile-request-card .request-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }
        .mobile-request-card .request-title {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1rem;
        }
        .mobile-request-card .request-email {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .mobile-request-card .request-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            margin-top: 0.5rem;
        }
        .mobile-request-card .request-detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
        }
        .mobile-request-card .request-detail-label {
            color: var(--text-muted);
        }
        .mobile-request-card .request-detail-value {
            color: var(--body-text);
            font-weight: 500;
        }
        .mobile-request-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.75rem;
            flex-wrap: wrap;
        }
        .mobile-request-actions .btn {
            flex: 1;
            min-width: 80px;
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
                <h1 class="h2 mb-1" style="color: var(--text-primary); font-weight: 700;">Appointment Requests</h1>
                <p class="text-muted mb-0">Manage and respond to student appointment requests</p>
            </div>
        </div>
    </div>
</div>

@if($requests->count() > 0)
    <!-- Mobile Card View (hidden on desktop) -->
    <div class="d-lg-none mobile-request-list">
        @foreach($requests as $request)
            @php
                $statusClass = strtolower($request->status->name ?? 'pending');
            @endphp
            <div class="card mobile-request-card">
                <div class="request-header">
                    <div>
                        @if($request->isHighSeverity() && !auth()->user()->isAdmin())
                            <div class="request-title" style="color: var(--orange);">Confidential (High Severity)</div>
                            <div class="request-email text-muted">Student identity hidden</div>
                        @else
                            <div class="request-title">{{ $request->student->full_name }}</div>
                            <div class="request-email">{{ $request->student->email }}</div>
                            @if($request->student->student_id)
                                <div class="request-email">ID: {{ $request->student->student_id }}</div>
                            @endif
                        @endif
                    </div>
                    <span class="status-badge {{ $statusClass }}">{{ $request->status->label }}</span>
                </div>
                <div class="request-details">
                    <div class="request-detail-row">
                        <span class="request-detail-label">Date</span>
                        <span class="request-detail-value">{{ $request->formatted_date }}</span>
                    </div>
                    <div class="request-detail-row">
                        <span class="request-detail-label">Time</span>
                        <span class="request-detail-value">{{ $request->formatted_time }}</span>
                    </div>
                    <div class="request-detail-row">
                        <span class="request-detail-label">Mode</span>
                        <span class="request-detail-value">{{ $request->appointment_mode === 'online' ? 'Online' : 'In Person' }}</span>
                    </div>
                    <div class="request-detail-row">
                        <span class="request-detail-label">Purpose</span>
                        <span class="request-detail-value text-muted">{{ Str::limit($request->purpose, 60) }}</span>
                    </div>
                    <div class="request-detail-row">
                        <span class="request-detail-label">Requested</span>
                        <span class="request-detail-value">{{ $request->created_at->format('M d, Y g:i A') }}</span>
                    </div>
                </div>
                <div class="mobile-request-actions">
                    <a href="{{ route('guidance.requests.show', $request) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-eye me-1"></i>{{ $request->appointment_mode === 'online' ? 'Add Link' : 'View' }}
                    </a>
                    @if($request->appointment_mode !== 'online')
                        <form method="POST" action="{{ route('guidance.requests.approve', $request) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-success btn-sm" onclick="return confirm('Approve this appointment?')">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('guidance.requests.reject', $request) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Reject this appointment?')">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </form>
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
                                <th>Requested</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                @php
                                    $statusClass = strtolower($request->status->name ?? 'pending');
                                @endphp
                                <tr>
                                    <td>
                                        @if($request->isHighSeverity() && !auth()->user()->isAdmin())
                                            <strong style="color: var(--orange);">Confidential (High Severity)</strong>
                                            <br><small class="text-muted">Student identity hidden</small>
                                        @else
                                            <strong>{{ $request->student->full_name }}</strong>
                                            <br><small class="text-muted">{{ $request->student->email }}</small>
                                            @if($request->student->student_id)
                                                <br><small class="text-muted">ID: {{ $request->student->student_id }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $request->formatted_date }}</td>
                                    <td>{{ $request->formatted_time }}</td>
                                    <td>{{ $request->appointment_mode === 'online' ? 'Online' : 'In Person' }}</td>
                                    <td>
                                        <div class="text-muted" style="max-width: 300px;">{{ Str::limit($request->purpose, 60) }}</div>
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $statusClass }}">{{ $request->status->label }}</span>
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y g:i A') }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('guidance.requests.show', $request) }}" class="btn-icon" title="View">
                                                <i class="bi bi-eye fs-5"></i>
                                            </a>
                                            @if($request->appointment_mode !== 'online')
                                                <form method="POST" action="{{ route('guidance.requests.approve', $request) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn-icon success" onclick="return confirm('Approve this appointment?')" title="Approve">
                                                        <i class="bi bi-check-circle fs-5"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('guidance.requests.reject', $request) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-icon danger" onclick="return confirm('Reject this appointment?')" title="Reject">
                                                    <i class="bi bi-x-circle fs-5"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('guidance.requests.reschedule', $request) }}" class="btn-icon warning" title="Reschedule">
                                                <i class="bi bi-calendar-event fs-5"></i>
                                            </a>
                                            <form method="POST" action="{{ route('guidance.requests.cancel', $request) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-icon secondary" onclick="return confirm('Cancel this appointment?')" title="Cancel">
                                                    <i class="bi bi-slash-circle fs-5"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent border-top p-3" style="border-color: var(--border-color-light);">
                    <div class="d-flex justify-content-center">
                        {{ $requests->appends(request()->query())->onEachSide(3)->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-lg-none d-flex justify-content-center mt-3">
        {{ $requests->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox fs-1" style="color: var(--text-muted); opacity: 0.5;"></i>
            <h4 class="mt-3 text-muted">No Pending Requests</h4>
            <p class="text-muted">All caught up! No new appointment requests at this time.</p>
        </div>
    </div>
@endif
@endsection
