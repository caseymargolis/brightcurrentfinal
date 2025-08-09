@extends('backend.layouts.master')
@section('title') {{'Add Alert'}} @endsection

@section('breadcrumb') Pages / Alerts @endsection
@section('page-title') Add Alert @endsection

@section('content')
    @include('backend.partials.alert')
    <div class="page-header d-flex flex-wrap align-items-center justify-content-end gap-3 mb-24">
        <a href="{{ route('alert.index') }}" class="btn bg-dark text-light text-sm btn-sm px-8 py-8 radius-4 d-flex align-items-center">
            <iconify-icon icon="ri:arrow-left-line" class="icon text-xl line-height-1"></iconify-icon>
            Back to Alert List
        </a>
    </div>

    <div class="row gy-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <form action="{{ route('alert.store') }}" method="POST">
                            @csrf
                            <div class="mb-20">
                                <label for="system_id" class="form-label">System <span class="text-danger">*</span></label>
                                <select class="form-control radius-8 form-select @error('system_id') is-invalid @enderror" id="system_id" name="system_id">
                                    <option value="">Select System</option>
                                    @foreach($systems as $system)
                                        <option value="{{ $system->id }}" {{ old('system_id') == $system->id ? 'selected' : '' }}>
                                            {{ $system->customer_name }} (ID: {{ $system->system_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('system_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-20">
                                <label for="severity" class="form-label">Severity <span class="text-danger">*</span></label>
                                <select class="form-control radius-8 form-select @error('severity') is-invalid @enderror" id="severity" name="severity">
                                    <option value="">Select Severity</option>
                                    <option value="critical" {{ old('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                                    <option value="warning" {{ old('severity') == 'warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="info" {{ old('severity') == 'info' ? 'selected' : '' }}>Info</option>
                                </select>
                                @error('severity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-20">
                                <label for="alert_type" class="form-label">Alert Type <span class="text-danger">*</span></label>
                                <input type="text" name="alert_type" id="alert_type" class="form-control @error('alert_type') is-invalid @enderror" value="{{ old('alert_type') }}" placeholder="e.g., Inverter Offline, Low Production">
                                @error('alert_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-20">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea name="message" id="message" class="form-control @error('message') is-invalid @enderror" rows="4" placeholder="Detailed description of the alert...">{{ old('message') }}</textarea>
                                @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-20">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control radius-8 form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="">Select Status</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ old('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-20">
                                <label for="resolved_at" class="form-label">Resolved At (Optional)</label>
                                <input type="datetime-local" name="resolved_at" id="resolved_at" class="form-control @error('resolved_at') is-invalid @enderror" value="{{ old('resolved_at') }}">
                                @error('resolved_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn bg-dark text-light text-lg px-56 py-8 radius-8 mt-24">
                                Save Alert
                            </button>
                        </form>
                    </div>
                </div>
            </div></div>
    </div>
@endsection
