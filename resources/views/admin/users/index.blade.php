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
    
    .table-responsive {
        border-radius: 12px;
        overflow-x: auto;
        overflow-y: hidden;
    }
    
    .table {
        margin-bottom: 0;
        background: transparent;
        min-width: 850px;
        table-layout: fixed;
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
        color: var(--badge-text-light) !important;
        border-bottom: none;
    }
    
    .table tbody {
        background: var(--card-bg);
    }
    
    .table td {
        background: var(--card-bg);
        overflow-wrap: anywhere;
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

    .table tbody tr:hover td {
        background: var(--bg-light);
    }
    
    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .table th:last-child,
    .table td:last-child {
        width: 180px;
    }
    
    .role-badge {
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: var(--badge-text-light);
    }
    
    .role-badge.admin { background: #db213a; }
    .role-badge.guidance_associate { background: var(--action-blue); }
    .role-badge.student { background: #2563EB; }
    
    .status-badge {
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: var(--badge-text-light);
    }
    
    .status-badge.active { background: #15803D; }
    .status-badge.inactive { background: #64748B; }
    
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
        color: var(--badge-text-light);
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

    html[data-theme="dark"] .role-badge.guidance_associate {
        background: var(--action-blue);
    }

    html[data-theme="dark"] .role-badge.student {
        background: #2563EB;
    }

    html[data-theme="dark"] .status-badge.active {
        background: #15803D;
    }

    html[data-theme="dark"] .btn-primary-action {
        background: var(--action-blue);
    }

    html[data-theme="dark"] .btn-primary-action:hover {
        background: var(--medium-blue);
        color: var(--navy);
    }

    html[data-theme="dark"] .mobile-user-actions .btn-outline-secondary {
        color: var(--text-secondary);
        border-color: var(--border-color);
    }

    html[data-theme="dark"] .mobile-user-actions .btn-outline-warning {
        color: #FFD166;
        border-color: #FFD166;
    }

    html[data-theme="dark"] .mobile-user-actions .btn-outline-success {
        color: #83D9A5;
        border-color: #83D9A5;
    }

    html[data-theme="dark"] .mobile-user-actions .btn-outline-danger {
        color: #FF8A8A;
        border-color: #FF8A8A;
    }

    html[data-theme="dark"] .page-item.active .page-link {
        background: var(--action-blue);
        border-color: var(--action-blue);
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
        color: var(--badge-text-light);
    }
    
    .page-item.disabled .page-link {
        color: var(--text-muted);
        background: var(--bg-light);
        border-color: var(--border-color-light);
    }
    
    @media (max-width: 991.98px) {
        .mobile-user-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .mobile-user-card {
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1rem;
            background: var(--card-bg);
        }
        .mobile-user-card .user-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }
        .mobile-user-card .user-header > div:first-child {
            min-width: 0;
            overflow-wrap: anywhere;
        }
        .mobile-user-card .role-badge,
        .mobile-user-card .status-badge {
            flex-shrink: 0;
        }
        .mobile-user-card .user-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1rem;
        }
        .mobile-user-card .user-email {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .mobile-user-card .user-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            margin-top: 0.5rem;
        }
        .mobile-user-card .user-detail-row {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            font-size: 0.875rem;
        }
        .mobile-user-card .user-detail-label {
            color: var(--text-muted);
        }
        .mobile-user-card .user-detail-value {
            color: var(--text-primary);
            font-weight: 500;
            overflow-wrap: anywhere;
            text-align: right;
        }
        .mobile-user-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.75rem;
            flex-wrap: wrap;
        }
        .mobile-user-actions .btn {
            flex: 1;
            min-width: 0;
            height: 44px;
        }
        .mobile-user-actions > .btn:first-child {
            flex: 2 1 90px;
        }
        .mobile-user-actions .d-inline {
            flex: 1 1 44px;
            min-width: 44px;
            display: flex !important;
        }
        .mobile-user-actions .d-inline .btn {
            width: 100%;
            min-width: 0;
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
                <h1 class="h2 mb-1" style="color: var(--text-primary); font-weight: 700;">User Management</h1>
                <p class="text-muted mb-0">Manage system users and their roles</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.create') }}" class="btn-primary-action">
                    <i class="bi bi-person-plus"></i>Add User
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Card -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
            <i class="bi bi-funnel me-2" style="color: var(--yellow);"></i>Filters
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label visually-hidden">Search</label>
                <input type="text" class="form-control" name="search" placeholder="Search name, email, student ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label visually-hidden">Role</label>
                <select class="form-select" name="role">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label visually-hidden">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-primary-action w-100">
                    <i class="bi bi-funnel"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

@if($users->count() > 0)
    <!-- Compact card view for mobile and tablet -->
    <div class="d-lg-none mobile-user-list">
        @foreach($users as $user)
            <div class="card mobile-user-card">
                <div class="user-header">
                    <div>
                        <div class="user-name">{{ $user->full_name }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                        @if($user->student_id)
                            <div class="user-email">ID: {{ $user->student_id }}</div>
                        @endif
                    </div>
                    <div class="d-flex flex-column gap-1 align-items-end">
                        <span class="role-badge {{ $user->role->name }}">{{ ucfirst(str_replace('_', ' ', $user->role->name)) }}</span>
                        <span class="status-badge {{ $user->status }}">{{ ucfirst($user->status) }}</span>
                    </div>
                </div>
                <div class="user-details">
                    <div class="user-detail-row">
                        <span class="user-detail-label">Created</span>
                        <span class="user-detail-value">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
                <div class="mobile-user-actions">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-{{ $user->status === 'active' ? 'warning' : 'success' }} btn-sm" title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                <i class="bi bi-{{ $user->status === 'active' ? 'pause' : 'play' }}"></i>
                            </button>
                        </form>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $user->id }}" title="Reset Password">
                            <i class="bi bi-key"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @else
                        <span class="text-muted small">(You)</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Wide table view for large screens -->
    <div class="d-none d-lg-block">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-medium" style="color: var(--text-primary);">{{ $user->full_name }}</div>
                                            @if($user->student_id)
                                                <div class="small text-muted">ID: {{ $user->student_id }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="role-badge {{ $user->role->name }}">{{ ucfirst(str_replace('_', ' ', $user->role->name)) }}</span>
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $user->status }}">{{ ucfirst($user->status) }}</span>
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn-icon" title="Edit">
                                                <i class="bi bi-pencil fs-5"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn-icon {{ $user->status === 'active' ? 'warning' : 'success' }}" title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                        <i class="bi bi-{{ $user->status === 'active' ? 'pause' : 'play' }} fs-5"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn-icon secondary" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $user->id }}" title="Reset Password">
                                                    <i class="bi bi-key fs-5"></i>
                                                </button>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-icon danger" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" title="Delete">
                                                        <i class="bi bi-trash fs-5"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">(You)</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top p-3" style="border-color: var(--border-color-light);">
                <div class="d-flex justify-content-center">
                    {{ $users->appends(request()->query())->onEachSide(3)->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
    <div class="d-lg-none d-flex justify-content-center mt-3">
        {{ $users->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>

    @foreach($users as $user)
        <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1" aria-labelledby="resetPasswordModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background: var(--yellow); color: var(--navy); border-radius: 1rem 1rem 0 0;">
                        <h5 class="modal-title" id="resetPasswordModalLabel{{ $user->id }}"><i class="bi bi-key me-2"></i>Reset Password for {{ $user->full_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="password{{ $user->id }}" class="form-label fw-medium" style="color: var(--text-primary);">New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password{{ $user->id }}" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation{{ $user->id }}" class="form-label fw-medium" style="color: var(--text-primary);">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation{{ $user->id }}" name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0" style="border-radius: 0 0 1rem 1rem;">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn" style="background: var(--yellow); border: none; border-radius: 0.5rem; color: var(--navy); font-weight: 500;">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-people fs-1" style="color: var(--text-muted); opacity: 0.5;"></i>
            <h4 class="mt-3 text-muted">No Users Found</h4>
            <p class="text-muted">No users match your search criteria.</p>
        </div>
    </div>
@endif
@endsection