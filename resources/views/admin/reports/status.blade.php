@extends('layouts.app')

@section('title', ' - Appointment Status Report')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Appointment Status Report</h1>
    <a href="{{ route('admin.reports') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Reports
    </a>
</div>

<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Status Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-table me-2"></i>Status Details</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statuses as $status)
                                <tr>
                                    <td>
                                        <span class="badge me-2" style="background: {{ $status->color ?? '#64748B' }}; color: var(--badge-text-light);">{{ $status->label }}</span>
                                    </td>
                                    <td>{{ $status->appointments_count }}</td>
                                    <td>
                                        @php
                                            $total = $statuses->sum('appointments_count');
                                            $percentage = $total > 0 ? round(($status->appointments_count / $total) * 100, 1) : 0;
                                        @endphp
                                        {{ $percentage }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const labels = @json($statuses->pluck('label'));
    const data = @json($statuses->pluck('appointments_count'));
    const colors = @json($statuses->pluck('color'));

    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection
