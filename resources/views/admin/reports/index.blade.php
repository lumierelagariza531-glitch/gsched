@extends('layouts.app')

@section('title', ' - Reports')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Reports</h1>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('admin.reports.appointments') }}" class="card shadow-sm text-decoration-none h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(66, 158, 189, 0.15); color: var(--medium-blue);">
                    <i class="bi bi-calendar-check fs-1"></i>
                </div>
                <h5>Appointment Report</h5>
                <p class="text-muted small">View all appointments with filters</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('admin.reports.students') }}" class="card shadow-sm text-decoration-none h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(159, 231, 245, 0.15); color: var(--medium-blue);">
                    <i class="bi bi-mortarboard fs-1"></i>
                </div>
                <h5>Student Report</h5>
                <p class="text-muted small">Student appointment statistics</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('admin.reports.guidance-associates') }}" class="card shadow-sm text-decoration-none h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(159, 231, 245, 0.15); color: var(--medium-blue);">
                    <i class="bi bi-person-badge fs-1"></i>
                </div>
                <h5>Guidance Associate Report</h5>
                <p class="text-muted small">Counselor workload statistics</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('admin.reports.status') }}" class="card shadow-sm text-decoration-none h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(247, 173, 25, 0.15); color: var(--text-primary);">
                    <i class="bi bi-pie-chart fs-1"></i>
                </div>
                <h5>Status Report</h5>
                <p class="text-muted small">Appointment status distribution</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('admin.reports.monthly') }}" class="card shadow-sm text-decoration-none h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(5, 63, 92, 0.15); color: var(--text-primary);">
                    <i class="bi bi-graph-up fs-1"></i>
                </div>
                <h5>Monthly Report</h5>
                <p class="text-muted small">Monthly appointment trends</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('admin.reports.cancellations') }}" class="card shadow-sm text-decoration-none h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(242, 127, 12, 0.15); color: var(--text-primary);">
                    <i class="bi bi-x-circle fs-1"></i>
                </div>
                <h5>Cancellation Report</h5>
                <p class="text-muted small">Cancelled appointments analysis</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('admin.reports.feedback') }}" class="card shadow-sm text-decoration-none h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(159, 231, 245, 0.15); color: var(--medium-blue);">
                    <i class="bi bi-star fs-1"></i>
                </div>
                <h5>Feedback Report</h5>
                <p class="text-muted small">Student feedback analysis</p>
            </div>
        </a>
    </div>
</div>
@endsection
