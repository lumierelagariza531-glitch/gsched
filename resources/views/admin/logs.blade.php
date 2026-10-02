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
    
    .form-control, .form-select {
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.625rem 0.875rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--medium-blue);
        box-shadow: 0 0 0 3px rgba(66, 158, 189, 0.3);
    }
    
    .btn-primary {
        background: var(--medium-blue);
        border: none;
        border-radius: 0.5rem;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        color: #FFFFFF;
        min-height: 44px;
    }
    
    .btn-primary:hover {
        background: var(--navy);
    }
    
    .btn-secondary {
        background: var(--border-color);
        border: none;
        border-radius: 0.5rem;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        color: var(--text-primary);
        min-height: 44px;
    }
    
    .btn-secondary:hover {
        background: var(--border-color-light);
    }
    
    .action-badge {
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: #FFFFFF;
        background: #64748B;
    }
    
    .log-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color-light);
        transition: background 0.2s ease;
    }
    
    .log-item:last-child {
        border-bottom: none;
    }
    
    .log-item:hover {
        background: var(--bg-light);
    }
    
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .kpi-icon.logs { background: rgba(159, 231, 245, 0.25); color: var(--medium-blue); }
    
    .kpi-icon.filters { background: rgba(247, 173, 25, 0.2); color: var(--yellow); }

    /* Custom pagination */
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
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h2 mb-1" style="color: var(--text-primary); font-weight: 700;">Activity Logs</h1>
                <p class="text-muted mb-0">System-wide activity tracking and audit trail</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Filters Card (Sidebar) -->
    <div class="col-12 col-lg-6">
        <div class="card mb-3">
            <div class="card-header" style="padding: 0.75rem 1rem;">
                <div class="d-flex align-items-center gap-2">
                    <div class="kpi-icon filters" style="width: 32px; height: 32px;">
                        <i class="bi bi-funnel fs-6"></i>
                    </div>
                    <h6 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Filters</h6>
                </div>
            </div>
            <div class="card-body" style="padding: 1rem;">
                <form method="GET" class="row g-2">
                    <div class="col-12 col-sm-6">
                        <label class="form-label small">Date From</label>
                        <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label small">Date To</label>
                        <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small">User</label>
                        <select class="form-select form-select-sm" name="user_id">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label small">Action</label>
                        <input type="text" class="form-control form-control-sm" name="action" placeholder="Search action..." value="{{ request('action') }}">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label small">Module</label>
                        <input type="text" class="form-control form-control-sm" name="module" placeholder="Search module..." value="{{ request('module') }}">
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.logs') }}" class="btn btn-secondary btn-sm">Clear</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Activity Logs Card -->
    <div class="col-12 col-lg-6">
        @if($logs->count() > 0)
            <div class="card h-100 d-flex flex-column">
                <div class="card-header" style="padding: 0.75rem 1rem;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="kpi-icon logs" style="width: 32px; height: 32px;">
                            <i class="bi bi-journal-text fs-6"></i>
                        </div>
                        <h6 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Activity Logs</h6>
                    </div>
                </div>
                <div class="card-body p-0 flex-grow-1 overflow-auto" style="max-height: 400px;">
                    @foreach($logs as $log)
                        <a href="#" class="log-item d-flex gap-2 text-decoration-none" style="padding: 0.75rem 1rem;">
                            <div class="flex-shrink-0">
                                <div class="kpi-icon logs" style="width: 36px; height: 36px;">
                                    <i class="bi bi-activity fs-6"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <h6 class="notification-title mb-0 small">{{ $log->user->full_name ?? 'System' }}</h6>
                                        <small class="notification-time">{{ $log->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        <span class="action-badge" style="font-size: 0.65rem; padding: 0.15rem 0.5rem;">{{ $log->action }}</span>
                                        <span class="text-muted small">{{ $log->module }}</span>
                                        <span class="text-muted small">{{ $log->ip_address ?? '-' }}</span>
                                    </div>
                                </div>
                                <p class="notification-message mb-0 small">{{ $log->description }}</p>
                            </div>
                        </a>
                    @endforeach
                    <div class="card-footer bg-transparent border-top" style="padding: 0.5rem; border-color: var(--border-color-light);">
                        <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap">
                            {{ $logs->appends(request()->query())->onEachSide(3)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-journal-text fs-2" style="color: var(--text-muted); opacity: 0.5;"></i>
                    <h5 class="mt-2 text-muted">No Activity Logs</h5>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection