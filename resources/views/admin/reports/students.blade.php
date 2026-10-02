@extends('layouts.app')

@section('title', ' - Student Report')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Student Report</h1>
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
                <label class="form-label">Search</label>
                <input type="text" class="form-control" name="search" placeholder="Name, email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('admin.reports.export', ['type' => 'students']) . '?' . request()->getQueryString() }}" class="btn btn-success w-100">
                    <i class="bi bi-download me-2"></i>Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

@if($students->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Appointments</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->phone ?: '-' }}</td>
                                <td>{{ $student->student_appointments_count }}</td>
                                <td><span class="badge" style="background: {{ $student->status === 'active' ? '#9FE7F5' : '#64748B' }}; color: var(--badge-text-light);">{{ ucfirst($student->status) }}</span></td>
                                <td>{{ $student->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $students->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-mortarboard fs-1 text-muted"></i>
        <h4 class="mt-3 text-muted">No Students Found</h4>
    </div>
@endif
@endsection
