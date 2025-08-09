@extends('backend.layouts.master')
@section('title') {{'System Details'}} @endsection

@section('breadcrumb') Pages / System @endsection
@section('page-title') System Details @endsection

@section('content')
    @include('backend.partials.alert')

    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h4 class="fw-semibold mb-0">{{ $system->customer_name }} ({{ $system->system_id }})</h4>
        <a href="{{ route('system.edit', $system->id) }}" class="btn bg-dark text-light text-sm btn-sm px-8 py-8 radius-4 d-flex align-items-center">
            <iconify-icon icon="lucide:edit" class="icon text-xl line-height-1 me-2"></iconify-icon>
            Edit System
        </a>
    </div>

    <div class="row gy-4">
        {{-- System Information Card --}}
        <div class="col-xxl-12 col-xl-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">System Information</h6>
                    </div>
                </div>
                <div class="card-body p-24">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <p class="text-secondary-light mb-1">System ID:</p>
                            <h6 class="mb-0 text-primary-light">{{ $system->system_id }}</h6>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-secondary-light mb-1">Customer Name:</p>
                            <h6 class="mb-0 text-primary-light">{{ $system->customer_name }}</h6>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-secondary-light mb-1">Customer Type:</p>
                            <h6 class="mb-0 text-primary-light">{{ ucfirst($system->customer_type) }}</h6>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-secondary-light mb-1">Manufacturer:</p>
                            <h6 class="mb-0 text-primary-light">{{ $system->manufacturer }}</h6>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-secondary-light mb-1">Status:</p>
                            <h6 class="mb-0 text-primary-light">
                            <span class="badge @if($system->status == 'active') bg-success-500 @elseif($system->status == 'warning') bg-warning-500 @elseif($system->status == 'critical') bg-danger-500 @else bg-neutral-500 @endif">
                                {{ ucfirst($system->status) }}
                            </span>
                            </h6>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-secondary-light mb-1">Location:</p>
                            <h6 class="mb-0 text-primary-light">{{ $system->location }}</h6>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-secondary-light mb-1">Capacity:</p>
                            <h6 class="mb-0 text-primary-light">{{ $system->capacity ? $system->capacity . ' kW' : 'N/A' }}</h6>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-secondary-light mb-1">Install Date:</p>
                            <h6 class="mb-0 text-primary-light">{{ $system->install_date ? $system->install_date->format('M d, Y') : 'N/A' }}</h6>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-secondary-light mb-1">Last Seen:</p>
                            <h6 class="mb-0 text-primary-light">{{ $system->last_seen ? $system->last_seen->diffForHumans() : 'N/A' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Production Data Chart --}}
        <div class="col-xxl-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Production History (Last 30 Days)</h6>
                    </div>
                </div>
                <div class="card-body p-24">
                    <div id="productionDataChart"></div> {{-- Changed ID for clarity --}}
                </div>
            </div>
        </div>

        {{-- Alert History --}}
        <div class="col-xxl-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-lg mb-0">Alert History</h6>
                        <a href="{{ route('alert.index', ['system_keyword' => $system->system_id]) }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($alerts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th scope="col" class="px-3 py-3">Date</th>
                                    <th scope="col" class="px-3 py-3">Severity</th>
                                    <th scope="col" class="px-3 py-3">Type</th>
                                    <th scope="col" class="px-3 py-3">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($alerts->take(5) as $alert)
                                    <tr>
                                        <td class="px-3 py-3">{{ $alert->created_at->format('M d, Y H:i A') }}</td>
                                        <td class="px-3 py-3">
                                            <span class="badge @if($alert->severity == 'critical') bg-danger @elseif($alert->severity == 'warning') bg-warning @else bg-info @endif">
                                                {{ ucfirst($alert->severity) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">{{ $alert->alert_type }}</td>
                                        <td class="px-3 py-3">
                                            <span class="badge @if($alert->status == 'resolved') bg-success @else bg-secondary @endif">
                                                {{ ucfirst($alert->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center" style="min-height: 200px;">
                            <div class="text-center">
                                <iconify-icon icon="solar:bell-off-outline" class="text-muted mb-3" style="font-size: 64px; display: block;"></iconify-icon>
                                <p class="text-muted mt-2 mb-0">No alerts found for this system.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Service Schedules --}}
        <div class="col-xxl-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-lg mb-0">Service Schedules</h6>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($serviceSchedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th scope="col" class="px-3 py-3">Scheduled Date</th>
                                    <th scope="col" class="px-3 py-3">Service Type</th>
                                    <th scope="col" class="px-3 py-3">Status</th>
                                    <th scope="col" class="px-3 py-3">Assigned Tech</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($serviceSchedules->take(5) as $schedule)
                                    <tr>
                                        <td class="px-3 py-3">{{ $schedule->scheduled_date->format('M d, Y') }}</td>
                                        <td class="px-3 py-3">{{ $schedule->service_type }}</td>
                                        <td class="px-3 py-3">
                                            <span class="badge @if($schedule->status == 'completed') bg-success @elseif($schedule->status == 'cancelled') bg-danger @else bg-secondary @endif">
                                                {{ ucfirst($schedule->status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">{{ $schedule->assigned_technician ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center" style="min-height: 200px;">
                            <div class="text-center">
                                <iconify-icon icon="solar:calendar-outline" class="text-muted mb-3" style="font-size: 64px; display: block;"></iconify-icon>
                                <p class="text-muted mt-2 mb-0">No service schedules found for this system.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // ApexCharts for Production Data
        var optionsProduction = {
            series: [{
                name: 'Current Power (kW)',
                data: @json($powerCurrentData)
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: @json($productionLabels),
                labels: {
                    style: {
                        colors: '#A0AEC0',
                    },
                },
            },
            yaxis: {
                title: {
                    text: 'Power Output (kW)'
                },
                labels: {
                    formatter: function (value) {
                        return parseFloat(value).toFixed(1) + ' kW'; // Format as kW
                    },
                    style: {
                        colors: '#A0AEC0',
                    },
                },
                min: 0
            },
            tooltip: {
                x: {
                    format: 'dd MMM'
                },
                y: {
                    formatter: function (value) {
                        return parseFloat(value).toFixed(2) + ' kW';
                    }
                }
            },
            grid: {
                borderColor: '#f1f1f1',
            },
            colors: ['#4A2AD0'] // A primary color for production data
        };

        var chartProduction = new ApexCharts(document.querySelector("#productionDataChart"), optionsProduction);
        chartProduction.render();
    </script>
@endpush
