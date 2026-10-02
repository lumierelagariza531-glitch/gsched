@extends('layouts.app')

@section('title', ' - Appointment Report')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Appointment Report</h1>
    <a href="{{ route('admin.reports') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Reports
    </a>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Date From</label>
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Date To</label>
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->name }}" {{ request('status') == $status->name ? 'selected' : '' }}>{{ $status->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Guidance Associate</label>
                <select class="form-select" name="guidance_associate_id">
                    <option value="">All</option>
                    @foreach($guidanceAssociates as $ga)
                        <option value="{{ $ga->id }}" {{ request('guidance_associate_id') == $ga->id ? 'selected' : '' }}>{{ $ga->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Student</label>
                <select class="form-select" name="student_id">
                    <option value="">All</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>{{ $student->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('admin.reports.export', ['type' => 'appointments']) . '?' . request()->getQueryString() }}" class="btn btn-success w-100">
                    <i class="bi bi-download me-2"></i>Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

@if($appointments->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Guidance Associate</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->id }}</td>
                                <td>{{ $appointment->student->full_name }}</td>
                                <td>{{ $appointment->guidanceAssociate->full_name ?? 'N/A' }}</td>
                                <td>{{ $appointment->formatted_date }}</td>
                                <td>{{ $appointment->formatted_time }}</td>
                                <td>{{ Str::limit($appointment->purpose, 50) }}</td>
                                <td><span class="badge" style="background: {{ $appointment->status->color ?? '#64748B' }}; color: var(--badge-text-light);">{{ $appointment->status->label }}</span></td>
                                <td>{{ $appointment->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $appointments->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-calendar-check fs-1 text-muted"></i>
        <h4 class="mt-3 text-muted">No Appointments Found</h4>
    </div>
@endif
@endsection
