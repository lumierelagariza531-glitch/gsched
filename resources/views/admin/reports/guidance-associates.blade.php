@extends('layouts.app')

@section('title', ' - Guidance Associate Report')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Guidance Associate Report</h1>
    <a href="{{ route('admin.reports') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Reports
    </a>
</div>

@if($guidanceAssociates->count() > 0)
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
                        @foreach($guidanceAssociates as $ga)
                            <tr>
                                <td>{{ $ga->full_name }}</td>
                                <td>{{ $ga->email }}</td>
                                <td>{{ $ga->phone ?: '-' }}</td>
                                <td>{{ $ga->guidance_appointments_count }}</td>
                                <td><span class="badge" style="background: {{ $ga->status === 'active' ? '#9FE7F5' : '#64748B' }}; color: var(--badge-text-light);">{{ ucfirst($ga->status) }}</span></td>
                                <td>{{ $ga->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-person-badge fs-1 text-muted"></i>
        <h4 class="mt-3 text-muted">No Guidance Associates Found</h4>
    </div>
@endif
@endsection
