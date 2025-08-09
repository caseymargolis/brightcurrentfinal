@extends('backend.layouts.master')
@section('title') {{'Edit Service Schedule'}} @endsection

@section('breadcrumb') Pages / Scheduling @endsection
@section('page-title') Edit Service Schedule @endsection

@section('content')
    @include('backend.partials.alert')
    <div class="row gy-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <form action="{{ route('service-schedules.update', $serviceSchedule->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-20">
                                <label for="system_id" class="form-label">System</label>
                                <select class="form-control radius-8 form-select" id="system_id" name="system_id" required>
                                    <option value="">Select System</option>
                                    @foreach($systems as $system)
                                        <option value="{{ $system->id }}" {{ old('system_id', $serviceSchedule->system_id) == $system->id ? 'selected' : '' }}>
                                            {{ $system->system_id }} ({{ $system->customer_name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('system_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label for="scheduled_date" class="form-label">Scheduled Date</label>
                                <input type="date" name="scheduled_date" id="scheduled_date" class="form-control" value="{{ old('scheduled_date', $serviceSchedule->scheduled_date->format('Y-m-d')) }}" required>
                                @error('scheduled_date')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label for="scheduled_time" class="form-label">Scheduled Time</label>
                                <input type="time" name="scheduled_time" id="scheduled_time" class="form-control" value="{{ old('scheduled_time', $serviceSchedule->scheduled_time) }}" required>
                                @error('scheduled_time')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label for="service_type" class="form-label">Service Type</label>
                                <input type="text" name="service_type" id="service_type" class="form-control" placeholder="e.g., Routine Maintenance" value="{{ old('service_type', $serviceSchedule->service_type) }}" required>
                                @error('service_type')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label for="assigned_technician" class="form-label">Assigned Technician</label>
                                <select class="form-control radius-8 form-select" id="assigned_technician" name="assigned_technician">
                                    <option value="">Select Technician</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->name }}" {{ old('assigned_technician', $serviceSchedule->assigned_technician) == $technician->name ? 'selected' : '' }}>
                                            {{ $technician->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_technician')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control radius-8 form-select" id="status" name="status" required>
                                    <option value="scheduled" {{ old('status', $serviceSchedule->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="in_progress" {{ old('status', $serviceSchedule->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status', $serviceSchedule->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $serviceSchedule->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="Any additional notes or details">{{ old('notes', $serviceSchedule->notes) }}</textarea>
                                @error('notes')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn bg-dark text-light text-lg px-56 py-8 radius-8 mt-24">
                                Update Schedule
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
