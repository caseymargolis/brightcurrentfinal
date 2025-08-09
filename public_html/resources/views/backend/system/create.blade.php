@extends('backend.layouts.master')
@section('title') {{'Add System'}} @endsection

@section('breadcrumb') Pages / System @endsection
@section('page-title') Add System @endsection

@section('content')
    @include('backend.partials.alert')
    <div class="row gy-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <form action="{{ route('system.store') }}" method="POST">
                            @csrf
                            <div class="mb-20">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}">
                                @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">Customer Type</label>
                                <select class="form-control radius-8 form-select @error('customer_type') is-invalid @enderror" name="customer_type">
                                    <option value="Residential" {{ old('customer_type') == 'Residential' ? 'selected' : '' }}>Residential</option>
                                    <option value="Commercial" {{ old('customer_type') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                                </select>
                                @error('customer_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">Manufacturer</label>
                                <input type="text" name="manufacturer" class="form-control flex-grow-1 @error('manufacturer') is-invalid @enderror" value="{{ old('manufacturer') }}">
                                @error('manufacturer')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">System ID</label>
                                <input type="text" name="system_id" class="form-control flex-grow-1 @error('system_id') is-invalid @enderror" placeholder="Optional" value="{{ old('system_id') }}">
                                @error('system_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control flex-grow-1 @error('location') is-invalid @enderror" value="{{ old('location') }}">
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">Capacity (kW)</label>
                                <input type="text" name="capacity" class="form-control flex-grow-1 @error('capacity') is-invalid @enderror" placeholder="e.g., 5.5" value="{{ old('capacity') }}">
                                @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">Installation Date</label>
                                <input type="date" name="install_date" class="form-control flex-grow-1 @error('install_date') is-invalid @enderror" value="{{ old('install_date') }}">
                                @error('install_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label class="form-label">Status</label>
                                <select class="form-control radius-8 form-select @error('status') is-invalid @enderror" name="status">
                                    <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Warning" {{ old('status') == 'Warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="Critical" {{ old('status') == 'Critical' ? 'selected' : '' }}>Critical</option>
                                    <option value="Offline" {{ old('status') == 'Offline' ? 'selected' : '' }}>Offline</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Removed kwh_today, kwh_yesterday, last_seen from create form --}}

                            <button type="submit" class="btn bg-dark text-light text-lg px-56 py-8 radius-8 mt-24">
                                Save
                            </button>
                        </form>
                    </div>
                </div>
            </div></div>
    </div>
@endsection

@push('script')
    <script>
        // DataTables initialization is not needed on create page
    </script>
@endpush
