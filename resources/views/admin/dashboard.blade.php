@extends('layouts.app')

@section('styles')
<style>
    body {
        background-color: var(--bg-light);
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
    
    .kpi-card {
        border-radius: 0.75rem;
        padding: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    
    .kpi-link {
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        display: block;
    }

    .kpi-card h2 {
        font-size: clamp(1.25rem, 2.5vw, 1.75rem);
        line-height: 1.15;
        overflow-wrap: anywhere;
    }

    .kpi-card .d-flex > div:first-child {
        min-width: 0;
    }
    
    .kpi-link:hover .kpi-card {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    }
    
    .kpi-card.users {
        background: linear-gradient(135deg, rgba(66, 158, 189, 0.1) 0%, rgba(66, 158, 189, 0.05) 100%);
        border-left: 4px solid var(--medium-blue);
    }
    
    .kpi-card.students {
        background: linear-gradient(135deg, rgba(159, 231, 245, 0.15) 0%, rgba(159, 231, 245, 0.05) 100%);
        border-left: 4px solid var(--light-blue);
    }
    
    .kpi-card.guidance {
        background: linear-gradient(135deg, rgba(159, 231, 245, 0.25) 0%, rgba(159, 231, 245, 0.15) 100%);
        border-left: 4px solid var(--light-blue);
    }
    
    .kpi-card.appointments {
        background: linear-gradient(135deg, rgba(247, 173, 25, 0.1) 0%, rgba(247, 173, 25, 0.05) 100%);
        border-left: 4px solid var(--yellow);
    }
    
    .kpi-card.pending {
        background: linear-gradient(135deg, rgba(247, 173, 25, 0.1) 0%, rgba(247, 173, 25, 0.05) 100%);
        border-left: 4px solid var(--yellow);
    }
    
    .kpi-card.approved {
        background: linear-gradient(135deg, rgba(66, 158, 189, 0.1) 0%, rgba(66, 158, 189, 0.05) 100%);
        border-left: 4px solid var(--medium-blue);
    }
    
    .kpi-card.completed {
        background: linear-gradient(135deg, rgba(159, 231, 245, 0.15) 0%, rgba(159, 231, 245, 0.05) 100%);
        border-left: 4px solid var(--light-blue);
    }
    
    .kpi-card.cancelled {
        background: linear-gradient(135deg, rgba(242, 127, 12, 0.1) 0%, rgba(242, 127, 12, 0.05) 100%);
        border-left: 4px solid var(--orange);
    }
    
    .kpi-icon {
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .kpi-icon.users { background: rgba(66, 158, 189, 0.2); color: var(--medium-blue); }
    .kpi-icon.students { background: rgba(159, 231, 245, 0.2); color: var(--medium-blue); }
    .kpi-icon.guidance { background: rgba(159, 231, 245, 0.3); color: var(--medium-blue); }
    .kpi-icon.appointments { background: rgba(247, 173, 25, 0.2); color: var(--yellow); }
    .kpi-icon.pending { background: rgba(247, 173, 25, 0.2); color: var(--yellow); }
    .kpi-icon.approved { background: rgba(66, 158, 189, 0.2); color: var(--medium-blue); }
    .kpi-icon.completed { background: rgba(159, 231, 245, 0.2); color: var(--medium-blue); }
    .kpi-icon.cancelled { background: rgba(242, 127, 12, 0.25); color: var(--orange); }
    
    .chart-card {
        min-height: 280px;
    }

    .admin-chart-container {
        position: relative;
        width: 100%;
        height: 250px;
        min-width: 0;
    }

    .admin-chart-container-large {
        height: 300px;
    }

    @media (max-width: 576px) {
        .chart-card {
            min-height: 250px;
        }

        .admin-chart-container {
            height: 220px;
        }

        .admin-chart-container-large {
            height: 260px;
        }
    }
    
    .action-btn.primary {
        background: var(--action-blue);
        border: none;
        border-radius: 0.75rem;
        color: var(--badge-text-light);
        padding: 1rem 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .kpi-link:focus-visible,
    .action-btn:focus-visible {
        outline: 3px solid var(--medium-blue);
        outline-offset: 3px;
    }

    @media (max-width: 575.98px) {
        .card-header {
            padding: 0.875rem 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .chart-card .card-body {
            padding: 0.75rem;
        }
    }
    .action-btn.primary:hover {
        background: var(--navy);
    }
    .action-btn.primary i {
        color: var(--badge-text-light);
    }

    .action-btn.primary-inverted {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        color: var(--link-color);
        padding: 1rem 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(5, 63, 92, 0.08);
    }
    .action-btn.primary-inverted:hover {
        border-color: var(--action-blue);
        background: rgba(66, 158, 189, 0.05);
        color: var(--link-color);
    }
    .action-btn.primary-inverted i {
        color: var(--link-color);
    }

    .action-btn {
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1rem 0.875rem;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        text-decoration: none;
    }
    
    .action-btn.success:hover {
        border-color: var(--medium-blue);
        background: rgba(66, 158, 189, 0.05);
    }
    
    .action-btn.info:hover {
        border-color: var(--medium-blue);
        background: rgba(66, 158, 189, 0.05);
    }
    
    .action-btn.warning:hover {
        border-color: var(--yellow);
        background: rgba(247, 173, 25, 0.05);
    }
    
    .quick-action-icon {
        font-size: 1.75rem;
        display: block;
        margin-bottom: 0.5rem;
        width: 48px;
        height: 48px;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .action-btn.success .quick-action-icon {
        background: rgba(159, 231, 245, 0.25);
    }
    
    .action-btn.info .quick-action-icon {
        background: rgba(66, 158, 189, 0.15);
    }
    
    .action-btn.warning .quick-action-icon {
        background: rgba(247, 173, 25, 0.2);
    }
</style>
@endsection

@section('content')
<div class="d-none d-md-block mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 mb-1" style="color: var(--text-primary); font-weight: 700;">Admin Dashboard</h1>
            <p class="text-muted mb-0">Overview of users, appointments, and system statistics</p>
        </div>
    </div>
</div>
<div class="d-md-none mb-3">
    <h1 class="h4 mb-1" style="color: var(--text-primary); font-weight: 700;">Admin Dashboard</h1>
    <p class="text-muted mb-0 small">Users and system statistics</p>
</div>

<div class="row g-3">
        <!-- KPI Summary Cards - User Stats -->
        <div class="col-12 col-md-6 col-lg-3">
            <a href="{{ route('admin.users.index') }}" class="kpi-link">
                <div class="card kpi-card users h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.05em;">Total Users</p>
                            <h2 class="mb-0" style="color: var(--text-primary); font-weight: 700;">{{ $totalUsers }}</h2>
                        </div>
                        <div class="kpi-icon users">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-12 col-md-6 col-lg-3">
            <a href="{{ route('admin.users.index') }}" class="kpi-link">
                <div class="card kpi-card students h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.05em;">Students</p>
                            <h2 class="mb-0" style="color: var(--text-primary); font-weight: 700;">{{ $totalStudents }}</h2>
                        </div>
                        <div class="kpi-icon students">
                            <i class="bi bi-mortarboard-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-12 col-md-6 col-lg-3">
            <a href="{{ route('admin.users.index') }}" class="kpi-link">
                <div class="card kpi-card guidance h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.05em;">Guidance Associates</p>
                            <h2 class="mb-0" style="color: var(--text-primary); font-weight: 700;">{{ $totalGuidance }}</h2>
                        </div>
                        <div class="kpi-icon guidance">
                            <i class="bi bi-person-badge-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-12 col-lg-3">
            <a href="{{ route('admin.appointments') }}" class="kpi-link">
                <div class="card kpi-card appointments h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.05em;">Total Appointments</p>
                            <h2 class="mb-0" style="color: var(--text-primary); font-weight: 700;">{{ $totalAppointments }}</h2>
                        </div>
                        <div class="kpi-icon appointments">
                            <i class="bi bi-calendar-check-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- KPI Summary Cards - Appointment Stats -->
        <div class="col-12 col-md-6 col-lg-3">
            <a href="{{ route('admin.appointments') }}" class="kpi-link">
                <div class="card kpi-card pending h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.05em;">Pending</p>
                            <h2 class="mb-0" style="color: var(--text-primary); font-weight: 700;">{{ $pendingAppointments }}</h2>
                        </div>
                        <div class="kpi-icon pending">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-12 col-md-6 col-lg-3">
            <a href="{{ route('admin.appointments') }}" class="kpi-link">
                <div class="card kpi-card approved h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.05em;">Approved</p>
                            <h2 class="mb-0" style="color: var(--text-primary); font-weight: 700;">{{ $approvedAppointments }}</h2>
                        </div>
                        <div class="kpi-icon approved">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-12 col-md-6 col-lg-3">
            <a href="{{ route('admin.appointments') }}" class="kpi-link">
                <div class="card kpi-card completed h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.05em;">Completed</p>
                            <h2 class="mb-0" style="color: var(--text-primary); font-weight: 700;">{{ $completedAppointments }}</h2>
                        </div>
                        <div class="kpi-icon completed">
                            <i class="bi bi-check2-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-12 col-lg-3">
            <a href="{{ route('admin.appointments') }}" class="kpi-link">
                <div class="card kpi-card cancelled h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.05em;">Cancelled</p>
                            <h2 class="mb-0" style="color: var(--text-primary); font-weight: 700;">{{ $cancelledAppointments }}</h2>
                        </div>
                        <div class="kpi-icon cancelled">
                            <i class="bi bi-x-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Charts Row -->
        <div class="col-12 col-lg-6">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                        <i class="bi bi-graph-up me-2" style="color: var(--medium-blue);"></i>Appointments per Month
                    </h5>
                </div>
                <div class="card-body">
                    <div class="admin-chart-container admin-chart-container-large">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-6">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                        <i class="bi bi-pie-chart me-2" style="color: var(--medium-blue);"></i>Appointment Status Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div class="admin-chart-container admin-chart-container-large">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Analytics Charts -->
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header" style="border-bottom: 1px solid var(--border-color-light);">
                    <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                        <i class="bi bi-graph-up-arrow me-2" style="color: var(--medium-blue);"></i>Analytics Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12 col-lg-4">
                            <div class="chart-card">
                                <div class="card h-100" style="border: none; box-shadow: none;">
                                    <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color-light); padding: 1rem 1.25rem;">
                                        <h6 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                                            <i class="bi bi-building me-2" style="color: var(--medium-blue);"></i>Appointment Volume by School
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="admin-chart-container">
                                            <canvas id="schoolChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="chart-card">
                                <div class="card h-100" style="border: none; box-shadow: none;">
                                    <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color-light); padding: 1rem 1.25rem;">
                                        <h6 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                                            <i class="bi bi-people me-2" style="color: var(--medium-blue);"></i>Age Range Distribution
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="admin-chart-container">
                                            <canvas id="ageChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="chart-card">
                                <div class="card h-100" style="border: none; box-shadow: none;">
                                    <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color-light); padding: 1rem 1.25rem;">
                                        <h6 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                                            <i class="bi bi-gender-ambiguous me-2" style="color: var(--medium-blue);"></i>Gender Distribution
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="admin-chart-container">
                                            <canvas id="genderChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                        <i class="bi bi-lightning me-2" style="color: var(--yellow);"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <a href="{{ route('admin.users.create') }}" class="action-btn primary-inverted d-block h-100">
                                <i class="bi bi-person-plus quick-action-icon" style="color: var(--medium-blue);"></i>
                                <span class="fw-medium d-block" style="color: var(--link-color);">Add User</span>
                            </a>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <a href="{{ route('admin.reports') }}" class="action-btn success d-block h-100">
                                <i class="bi bi-graph-up quick-action-icon" style="color: var(--medium-blue);"></i>
                                <span class="fw-medium d-block" style="color: var(--text-primary);">Generate Reports</span>
                            </a>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <a href="{{ route('admin.settings') }}" class="action-btn info d-block h-100">
                                <i class="bi bi-gear quick-action-icon" style="color: var(--medium-blue);"></i>
                                <span class="fw-medium d-block" style="color: var(--text-primary);">System Settings</span>
                            </a>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <a href="{{ route('admin.logs') }}" class="action-btn warning d-block h-100">
                                <i class="bi bi-journal-text quick-action-icon" style="color: var(--yellow);"></i>
                                <span class="fw-medium d-block" style="color: var(--text-primary);">Activity Logs</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    (function() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const tickColor = isDark ? '#CAF0F8' : '#053F5C';
        const gridColor = isDark ? 'rgba(0, 180, 216, 0.18)' : 'rgba(66, 158, 189, 0.3)';
        const donutBorder = isDark ? 'rgba(13, 23, 37, 0.9)' : '#FFFFFF';
        const legendColor = isDark ? '#90E0EF' : '#64748B';
        const blue = isDark ? ['rgba(0, 119, 182, 0.7)', 'rgba(0, 119, 182, 1)'] : ['rgba(66, 158, 189, 0.7)', 'rgba(66, 158, 189, 1)'];
        const cyan = isDark ? ['rgba(0, 180, 216, 0.7)', 'rgba(0, 180, 216, 1)'] : ['rgba(159, 231, 245, 0.7)', 'rgba(159, 231, 245, 1)'];
        const orange = isDark ? ['rgba(247, 173, 25, 0.82)', 'rgba(255, 209, 102, 1)'] : ['rgba(242, 127, 12, 0.7)', 'rgba(242, 127, 12, 1)'];

        // Monthly Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
        type: 'bar',
        data: {
                labels: @json($monthlyLabels),
                datasets: [{
                    label: 'Appointments',
                    data: @json($monthlyData),
                    backgroundColor: blue[0],
                    borderColor: blue[1],
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: tickColor },
                        grid: { color: gridColor }
                    },
                    x: {
                        ticks: { color: tickColor },
                        grid: { display: false }
                    }
                }
            }
        });

        window.addEventListener('g-sched-theme-changed', function(event) {
            const dark = event.detail.theme === 'dark';
            const axisColor = dark ? '#D4DFEA' : '#475569';
            const gridColor = dark ? 'rgba(141, 221, 237, 0.18)' : 'rgba(66, 158, 189, 0.25)';
            const legendColor = dark ? '#D4DFEA' : '#475569';
            const blueFill = dark ? ['rgba(0, 119, 182, 0.7)', 'rgba(0, 119, 182, 1)'] : ['rgba(66, 158, 189, 0.7)', 'rgba(66, 158, 189, 1)'];
            const cyanFill = dark ? ['rgba(0, 180, 216, 0.7)', 'rgba(0, 180, 216, 1)'] : ['rgba(159, 231, 245, 0.7)', 'rgba(159, 231, 245, 1)'];
            const orangeFill = dark ? ['rgba(247, 173, 25, 0.82)', 'rgba(255, 209, 102, 1)'] : ['rgba(242, 127, 12, 0.7)', 'rgba(242, 127, 12, 1)'];

            Object.values(Chart.instances).forEach(function(chart) {
                if (chart.options.scales) {
                    Object.values(chart.options.scales).forEach(function(scale) {
                        if (scale.ticks) {
                            scale.ticks.color = axisColor;
                        }
                        if (scale.grid) {
                            scale.grid.color = gridColor;
                        }
                    });
                }

                if (chart.options.plugins && chart.options.plugins.legend && chart.options.plugins.legend.labels) {
                    chart.options.plugins.legend.labels.color = legendColor;
                }
                if (chart.options.plugins && chart.options.plugins.tooltip) {
                    chart.options.plugins.tooltip.titleColor = dark ? '#F8FAFC' : '#FFFFFF';
                    chart.options.plugins.tooltip.bodyColor = dark ? '#F8FAFC' : '#FFFFFF';
                    chart.options.plugins.tooltip.backgroundColor = dark ? '#25364B' : 'rgba(0, 0, 0, 0.8)';
                    chart.options.plugins.tooltip.borderColor = dark ? '#52647A' : 'rgba(255, 255, 255, 0.15)';
                }

                if (chart.canvas.id === 'statusChart') {
                    chart.data.datasets.forEach(function(dataset) {
                        dataset.borderColor = dark ? 'rgba(13, 23, 37, 0.9)' : '#FFFFFF';
                    });
                } else {
                    const fills = chart.canvas.id === 'schoolChart'
                        ? [orangeFill]
                        : chart.canvas.id === 'genderChart'
                            ? [blueFill, cyanFill, orangeFill]
                            : [blueFill];

                    chart.data.datasets.forEach(function(dataset, index) {
                        if (chart.canvas.id === 'genderChart') {
                            dataset.backgroundColor = fills.map(function(fill) { return fill[0]; });
                            dataset.borderColor = fills.map(function(fill) { return fill[1]; });
                        } else {
                            const fill = fills[index] || blueFill;
                            dataset.backgroundColor = fill[0];
                            dataset.borderColor = fill[1];
                        }
                    });
                }

                chart.update('none');
            });
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
        type: 'doughnut',
        data: {
                labels: @json($statusLabels),
                datasets: [{
                    data: @json($statusData),
                    backgroundColor: @json($statusColors),
                    borderWidth: 2,
                    borderColor: donutBorder
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            color: legendColor,
                            font: { size: 12 }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Appointment Volume by School Chart
        const schoolCtx = document.getElementById('schoolChart').getContext('2d');
        new Chart(schoolCtx, {
        type: 'bar',
        data: {
                labels: @json($schoolLabels),
                datasets: [{
                    label: 'Appointments',
                    data: @json(array_values($schoolData)),
                    backgroundColor: orange[0],
                    borderColor: orange[1],
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' appointment' + (context.raw !== 1 ? 's' : '');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: tickColor },
                        grid: { color: gridColor }
                    },
                    x: {
                        ticks: { color: tickColor },
                        grid: { display: false }
                    }
                }
            }
        });

        // Age Range Distribution Chart
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        new Chart(ageCtx, {
        type: 'bar',
        data: {
                labels: @json($ageRangeLabels),
                datasets: [{
                    label: 'Students',
                    data: @json($ageRangeData),
                    backgroundColor: blue[0],
                    borderColor: blue[1],
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' student' + (context.raw !== 1 ? 's' : '');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: tickColor },
                        grid: { color: gridColor }
                    },
                    x: {
                        ticks: { color: tickColor },
                        grid: { display: false }
                    }
                }
            }
        });

        // Gender Distribution Chart
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        new Chart(genderCtx, {
        type: 'bar',
        data: {
                labels: @json($genderLabels),
                datasets: [{
                    label: 'Students',
                    data: @json($genderData),
                    backgroundColor: [blue[0], cyan[0], orange[0]],
                    borderColor: [blue[1], cyan[1], orange[1]],
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' student' + (context.raw !== 1 ? 's' : '');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: tickColor },
                        grid: { color: gridColor }
                    },
                    x: {
                        ticks: { color: tickColor },
                        grid: { display: false }
                    }
                }
            }
        });
    })();
</script>
@endsection
