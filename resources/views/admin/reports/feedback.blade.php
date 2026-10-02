@extends('layouts.app')

@section('title', ' - Feedback Report')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Feedback Report</h1>
    <a href="{{ route('admin.reports') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Reports
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h2 class="mb-0" style="color: var(--yellow);">{{ number_format($avgRating, 2) }}</h2>
                <p class="text-muted mb-0">Average Rating</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h2 class="text-primary mb-0">{{ $feedback->total() }}</h2>
                <p class="text-muted mb-0">Total Feedback</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <canvas id="ratingChart" height="150"></canvas>
            </div>
        </div>
    </div>
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
                <label class="form-label">Rating</label>
                <select class="form-select" name="rating">
                    <option value="">All Ratings</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <a href="{{ route('admin.reports.export', ['type' => 'feedback']) . '?' . request()->getQueryString() }}" class="btn btn-success w-100">
                    <i class="bi bi-download me-2"></i>Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

@if($feedback->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                            <tr>
                                <th>Student</th>
                                <th>Guidance Associate</th>
                                <th>Appointment Date</th>
                                <th>Rating</th>
                                <th>Comments</th>
                                <th>Suggestions</th>
                                <th>Submitted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feedback as $fb)
                                <tr>
                                    <td>{{ $fb->student->full_name }}</td>
                                    <td>{{ $fb->appointment->guidanceAssociate->full_name ?? 'N/A' }}</td>
                                    <td>{{ $fb->appointment->formatted_date }}</td>
                                    <td>
                                        @if($fb->rating)
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $fb->rating ? '-fill' : ''}}" style="{{ $i <= $fb->rating ? 'color: var(--yellow);' : 'color: var(--text-primary); opacity: 0.4;' }}"></i>
                                            @endfor
                                        @else
                                            <span class="badge" style="background: var(--medium-blue); color: var(--text-primary);">SQD Response</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($fb->comments)
                                            {{ Str::limit($fb->comments, 60) }}
                                        @else
                                            @php
                                                $sqdFields = ['sqd0', 'sqd1', 'sqd2', 'sqd3', 'sqd4', 'sqd5', 'sqd6', 'sqd7', 'sqd8'];
                                                $sqdLabels = \App\Models\Feedback::SQD_OPTIONS;
                                                $responses = [];
                                                foreach ($sqdFields as $field) {
                                                    if (isset($fb->$field) && $fb->$field) {
                                                        $responses[] = strtoupper($field) . ': ' . ($sqdLabels[$fb->$field] ?? $fb->$field);
                                                    }
                                                }
                                                echo \Illuminate\Support\Str::limit(implode(', ', $responses), 80);
                                            @endphp
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($fb->suggestions, 60) ?? 'N/A' }}</td>
                                    <td>{{ $fb->submitted_at->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $feedback->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-star fs-1 text-muted"></i>
        <h4 class="mt-3 text-muted">No Feedback Found</h4>
    </div>
@endif
@endsection

@section('scripts')
<script>
    const ratingLabels = ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'];
    const ratingData = @json($ratingDistribution);

    (function() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const bgColors = isDark ? ['#90E0EF', '#00B4D8', '#0077B6', '#00B4D8', '#03045E'] : ['#F27F0C', '#F7AD19', '#429EBD', '#9FE7F5', '#053F5C'];
        const ctx = document.getElementById('ratingChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ratingLabels,
                datasets: [{
                    label: 'Count',
                    data: ratingData,
                    backgroundColor: bgColors,
                    borderWidth: 0
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1, color: isDark ? '#90E0EF' : '#64748B' } },
                    y: { ticks: { color: isDark ? '#90E0EF' : '#64748B' } }
                }
            }
        });
    })();
</script>
@endsection
