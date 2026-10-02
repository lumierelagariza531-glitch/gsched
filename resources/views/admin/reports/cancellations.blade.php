@extends('layouts.app')

@section('title', ' - Cancellation Report')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Cancellation Report</h1>
    <a href="{{ route('admin.reports') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Reports
    </a>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Date From</label>
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Date To</label>
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <a href="{{ route('admin.reports.export', ['type' => 'appointments']) . '?' . request()->getQueryString() }}" class="btn btn-success w-100">
                    <i class="bi bi-download me-2"></i>Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

@if($cancellations->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Guidance Associate</th>
                            <th>Original Date</th>
                            <th>Original Time</th>
                            <th>Cancelled At</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cancellations as $cancellation)
                            <tr>
                                <td>{{ $cancellation->id }}</td>
                                <td>{{ $cancellation->student->full_name }}</td>
                                <td>{{ $cancellation->guidanceAssociate->full_name ?? 'N/A' }}</td>
                                <td>{{ $cancellation->formatted_date }}</td>
                                <td>{{ $cancellation->formatted_time }}</td>
                                <td>{{ $cancellation->cancelled_at->format('M d, Y g:i A') }}</td>
                                <td>{{ Str::limit($cancellation->cancellation_reason, 80) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $cancellations->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-x-circle fs-1 text-muted"></i>
        <h4 class="mt-3 text-muted">No Cancellations Found</h4>
    </div>
@endif
@endsection
