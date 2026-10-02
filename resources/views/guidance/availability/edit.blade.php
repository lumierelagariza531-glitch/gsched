@extends('layouts.app')

@section('title', ' - Edit Availability')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Availability</h1>
    <a href="{{ route('guidance.availability') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Availability</h5>
            </div>
            <div class="card-body">
                @if($availability->isBooked())
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>This slot is booked and cannot be modified.
                    </div>
                @endif

                <form method="POST" action="{{ route('guidance.availability.update', $availability) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="available_date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="available_date" name="available_date" value="{{ $availability->available_date->format('Y-m-d') }}" @if($availability->isBooked()) readonly @endif required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="start_time" name="start_time" value="{{ \Carbon\Carbon::parse($availability->start_time)->format('H:i') }}" @if($availability->isBooked()) readonly @endif required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="end_time" name="end_time" value="{{ \Carbon\Carbon::parse($availability->end_time)->format('H:i') }}" @if($availability->isBooked()) readonly @endif required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="slot_duration" class="form-label">Slot Duration (minutes) <span class="text-danger">*</span></label>
                        <select class="form-select" id="slot_duration" name="slot_duration" @if($availability->isBooked()) disabled @endif required>
                            <option value="15" {{ $availability->slot_duration == 15 ? 'selected' : '' }}>15 minutes</option>
                            <option value="30" {{ $availability->slot_duration == 30 ? 'selected' : '' }}>30 minutes</option>
                            <option value="45" {{ $availability->slot_duration == 45 ? 'selected' : '' }}>45 minutes</option>
                            <option value="60" {{ $availability->slot_duration == 60 ? 'selected' : '' }}>60 minutes</option>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2">
                        @if(!$availability->isBooked())
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Save Changes
                            </button>
                        @endif
                        <a href="{{ route('guidance.availability') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
