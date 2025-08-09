@extends('backend.layouts.master')
@section('title') {{'Edit System'}} @endsection

@section('breadcrumb') Pages / System @endsection
@section('page-title') Edit System @endsection

@section('content')
    @include('backend.partials.alert')
    <div class="page-header d-flex flex-wrap align-items-center justify-content-end gap-3 mb-24">
        <a href="{{ route('system.index') }}" class="btn bg-dark text-light text-sm btn-sm px-8 py-8 radius-4 d-flex align-items-center">
            <iconify-icon icon="ri:arrow-left-line" class="icon text-xl line-height-1"></iconify-icon>
            Back to List
        </a>
    </div>

    <div class="row gy-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <form action="{{ route('system.update', $system->id) }}" method="POST">
                            @csrf
                            @method('PUT') {{-- Use PUT method for update requests --}}

                            <div class="mb-20">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', $system->customer_name) }}">
                                @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">Customer Type</label>
                                <select class="form-control radius-8 form-select @error('customer_type') is-invalid @enderror" name="customer_type">
                                    <option value="Residential" {{ old('customer_type', $system->customer_type) == 'Residential' ? 'selected' : '' }}>Residential</option>
                                    <option value="Commercial" {{ old('customer_type', $system->customer_type) == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                                </select>
                                @error('customer_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">Manufacturer</label>
                                <input type="text" name="manufacturer" class="form-control flex-grow-1 @error('manufacturer') is-invalid @enderror" value="{{ old('manufacturer', $system->manufacturer) }}">
                                @error('manufacturer')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">System ID</label>
                                <input type="text" name="system_id" class="form-control flex-grow-1 @error('system_id') is-invalid @enderror" value="{{ old('system_id', $system->system_id) }}">
                                @error('system_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control flex-grow-1 @error('location') is-invalid @enderror" value="{{ old('location', $system->location) }}">
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">Capacity (kW)</label>
                                <input type="text" name="capacity" class="form-control flex-grow-1 @error('capacity') is-invalid @enderror" placeholder="e.g., 5.5" value="{{ old('capacity', $system->capacity) }}">
                                @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">Installation Date</label>
                                <input type="date" name="install_date" class="form-control flex-grow-1 @error('install_date') is-invalid @enderror" value="{{ old('install_date', $system->install_date ? $system->install_date->format('Y-m-d') : '') }}">
                                @error('install_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">Status</label>
                                <select class="form-control radius-8 form-select @error('status') is-invalid @enderror" name="status">
                                    <option value="Active" {{ old('status', $system->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Warning" {{ old('status', $system->status) == 'Warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="Critical" {{ old('status', $system->status) == 'Critical' ? 'selected' : '' }}>Critical</option>
                                    <option value="Offline" {{ old('status', $system->status) == 'Offline' ? 'selected' : '' }}>Offline</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn bg-dark text-light text-lg px-56 py-8 radius-8 mt-24">
                                Update
                            </button>
                        </form>
                    </div>
                </div>
            </div></div>
    </div>
@endsection

@push('script')
    <script>
        // No specific script for edit page
    </script>
@endpush
