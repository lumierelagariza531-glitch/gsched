@extends('layouts.app')

@section('title', ' - Manage Availability')

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
        color: #032536;
    }

    .status-badge.available { background: #21db3d; }
    .status-badge.booked { background: #429EBD; }

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
        color: var(--navy);
        background: rgba(66, 158, 189, 0.1);
    }

    .btn-icon.danger:hover {
        border-color: var(--orange);
        color: var(--navy);
        background: rgba(242, 127, 12, 0.1);
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

    .form-control, .form-select {
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.625rem 0.875rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--medium-blue);
        box-shadow: 0 0 0 3px rgba(66, 158, 189, 0.3);
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
        color: #FFFFFF;
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
        .mobile-availability-list {
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
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 0.5rem;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }
        .mobile-appt-card .appt-header > div {
            min-width: 0;
        }
        .mobile-appt-card .appt-title {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1rem;
            overflow-wrap: normal;
        }
        .mobile-appt-card .appt-guidance {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .mobile-appt-card .status-badge {
            flex-shrink: 0;
            white-space: nowrap;
        }
        .mobile-appt-card .appt-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            margin-top: 0.5rem;
        }
        .mobile-appt-card .appt-detail-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 0.75rem;
            font-size: 0.875rem;
        }
        .mobile-appt-card .appt-detail-label {
            color: var(--text-muted);
        }
        .mobile-appt-card .appt-detail-value {
            color: var(--text-primary);
            font-weight: 500;
            min-width: 0;
            overflow-wrap: break-word;
            text-align: right;
        }
        .mobile-appt-card .appt-detail-label {
            min-width: 0;
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
                <h1 class="h2 mb-1" style="color: var(--text-primary); font-weight: 700;">Manage Availability</h1>
                <p class="text-muted mb-0">Set your available time slots for student appointments</p>
            </div>
        </div>
    </div>
</div>

@if($availabilities->count() > 0)
    <!-- Mobile Card View (hidden on desktop) -->
    <div class="d-lg-none mobile-availability-list">
        @foreach($availabilities as $availability)
            <div class="card mobile-appt-card">
                <div class="appt-header">
                    <div>
                        <div class="appt-title">{{ $availability->available_date->format('l, F d, Y') }}</div>
                        <div class="appt-guidance">{{ \Carbon\Carbon::parse($availability->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($availability->end_time)->format('g:i A') }}</div>
                    </div>
                    @php
                        $statusClass = strtolower($availability->status);
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ ucfirst($availability->status) }}</span>
                </div>
                <div class="appt-details">
                    <div class="appt-detail-row">
                        <span class="appt-detail-label">Slot Duration</span>
                        <span class="appt-detail-value">{{ $availability->slot_duration }} minutes</span>
                    </div>
                    <div class="appt-detail-row">
                        <span class="appt-detail-label">Created</span>
                        <span class="appt-detail-value">{{ $availability->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
                <div class="mobile-appt-actions">
                    @if($availability->isAvailable())
                        <a href="{{ route('guidance.availability.edit', $availability) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('guidance.availability.destroy', $availability) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this availability?')">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </form>
                    @else
                        <span class="text-muted small">(Booked)</span>
                    @endif
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
                                <th>Date</th>
                                <th>Time</th>
                                <th>Slot Duration</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availabilities as $availability)
                                @php
                                    $statusClass = strtolower($availability->status);
                                @endphp
                                <tr>
                                    <td>{{ $availability->available_date->format('l, F d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($availability->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($availability->end_time)->format('g:i A') }}</td>
                                    <td>{{ $availability->slot_duration }} minutes</td>
                                    <td>
                                        <span class="status-badge {{ $statusClass }}">{{ ucfirst($availability->status) }}</span>
                                    </td>
                                    <td>{{ $availability->created_at->format('M d, Y') }}</td>
                                    <td class="text-end">
                                        @if($availability->isAvailable())
                                            <div class="d-flex gap-2 justify-content-end">
                                                <a href="{{ route('guidance.availability.edit', $availability) }}" class="btn-icon" title="Edit">
                                                    <i class="bi bi-pencil fs-5"></i>
                                                </a>
                                                <form method="POST" action="{{ route('guidance.availability.destroy', $availability) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-icon danger" onclick="return confirm('Delete this availability?')" title="Delete">
                                                        <i class="bi bi-trash fs-5"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted small">Booked</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent border-top p-3" style="border-color: var(--border-color-light);">
                    <div class="d-flex justify-content-center">
                        {{ $availabilities->appends(request()->query())->onEachSide(3)->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-lg-none d-flex justify-content-center mt-3">
        {{ $availabilities->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar-plus fs-1" style="color: var(--text-muted); opacity: 0.5;"></i>
            <h4 class="mt-3 text-muted">No Availability Set</h4>
            <p class="text-muted">Add your available time slots for student appointments.</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAvailabilityModal">
                <i class="bi bi-plus-circle me-2"></i>Add Availability
            </button>
        </div>
    </div>
@endif

<div class="modal fade" id="addAvailabilityModal" tabindex="-1" aria-labelledby="addAvailabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAvailabilityModalLabel">Add Availability</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('guidance.availability.store') }}">
                @csrf
                <div class="modal-body">
                    @if($errors->has('overlap') || $errors->has('available_date') || $errors->has('start_time') || $errors->has('end_time') || $errors->has('slot_duration'))
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="available_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="available_date" name="available_date" value="{{ old('available_date') }}" min="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label for="start_time" class="form-label">Start time</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label for="end_time" class="form-label">End time</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                        </div>
                    </div>
                    <div>
                        <label for="slot_duration" class="form-label">Slot duration</label>
                        <select class="form-select" id="slot_duration" name="slot_duration" required>
                            @foreach([15, 30, 45, 60, 90, 120] as $duration)
                                <option value="{{ $duration }}" @selected(old('slot_duration', 30) == $duration)>{{ $duration }} minutes</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Availability</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @if(request('open') === 'add' || $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('addAvailabilityModal');
                if (modal && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                }
            });
        </script>
    @endif
@endsection
