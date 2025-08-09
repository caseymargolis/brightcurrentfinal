@extends('backend.layouts.master')
@section('title') {{'Alert Details'}} @endsection

@section('breadcrumb') Pages / Alerts @endsection
@section('page-title') Alert Details @endsection

@section('content')
    @include('backend.partials.alert')
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h4 class="fw-semibold mb-0">{{ $alert->alert_type }}</h4>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('alert.index') }}" class="btn bg-dark text-light text-sm btn-sm px-8 py-8 radius-4 d-flex align-items-center">
                <iconify-icon icon="ic:baseline-arrow-left" class="icon text-xl line-height-1"></iconify-icon>
                Back to Alert List
            </a>
            <a href="{{ route('alert.edit', $alert->id) }}" class="btn bg-dark text-light text-sm btn-sm px-8 py-8 radius-4 d-flex align-items-center">
                <iconify-icon icon="lucide:edit" class="icon text-xl line-height-1"></iconify-icon>
                Edit Alert
            </a>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6 mb-20">
                            <label class="form-label fw-semibold">Severity:</label>
                            @if ($alert->severity === 'critical')
                                <span class="badge bg-danger text-white px-16 py-4 radius-4 fw-medium text-sm">Critical</span>
                            @elseif ($alert->severity === 'warning')
                                <span class="badge bg-warning text-dark px-16 py-4 radius-4 fw-medium text-sm">Warning</span>
                            @else
                                <span class="badge bg-info text-white px-16 py-4 radius-4 fw-medium text-sm">Info</span>
                            @endif
                        </div>
                        <div class="col-md-6 mb-20">
                            <label class="form-label fw-semibold">Alert Type:</label>
                            <p class="form-control-static">{{ $alert->alert_type }}</p>
                        </div>

                        <div class="col-md-6 mb-20">
                            <label class="form-label fw-semibold">System ID:</label>
                            <p class="form-control-static">{{ $alert->system->system_id ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-20">
                            <label class="form-label fw-semibold">Customer Name:</label>
                            <p class="form-control-static">{{ $alert->system->customer_name ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-12 mb-20">
                            <label class="form-label fw-semibold">Message:</label>
                            <p class="form-control-static">{{ $alert->message }}</p>
                        </div>

                        <div class="col-md-6 mb-20">
                            <label class="form-label fw-semibold">Status:</label>
                            @php
                                $statusClass = '';
                                $statusTextColor = 'text-white';
                                switch($alert->status) {
                                    case 'resolved': $statusClass = 'bg-success'; break;
                                    case 'pending': $statusClass = 'bg-warning text-dark'; break; // System warning style
                                    case 'in_progress': $statusClass = 'bg-info'; break;
                                    case 'scheduled': $statusClass = 'bg-secondary'; break;
                                    default: $statusClass = 'bg-secondary'; break;
                                }
                            @endphp
                            <span class="badge {{ $statusClass }} {{ ($alert->status === 'pending') ? '' : $statusTextColor }} px-16 py-4 radius-4 fw-medium text-sm">{{ ucfirst(str_replace('_', ' ', $alert->status)) }}</span>
                        </div>

                        <div class="col-md-6 mb-20">
                            <label class="form-label fw-semibold">Created At:</label>
                            <p class="form-control-static">{{ $alert->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        <div class="col-md-6 mb-20">
                            <label class="form-label fw-semibold">Last Updated At:</label>
                            <p class="form-control-static">{{ $alert->updated_at->format('M d, Y h:i A') }}</p>
                        </div>

                        <div class="col-md-6 mb-20">
                            <label class="form-label fw-semibold">Resolved At:</label>
                            <p class="form-control-static">{{ $alert->resolved_at ? $alert->resolved_at->format('M d, Y h:i A') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
