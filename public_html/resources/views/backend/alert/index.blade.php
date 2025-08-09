@extends('backend.layouts.master')
@section('title') {{'Alert List'}} @endsection

@section('breadcrumb') Pages / Alerts @endsection
@section('page-title') Alert List @endsection

@section('content')
    @include('backend.partials.alert')

    <div class="page-header d-flex flex-wrap align-items-center justify-content-end gap-3 mb-24">
        <a href="{{ route('alert.create') }}" class="btn bg-dark text-light text-sm btn-sm px-8 py-8 radius-4 d-flex align-items-center">
            <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
            Add New Alert
        </a>
    </div>

    {{-- START: Filtering System --}}
    <div class="card mb-24">
        <div class="card-body">
            <form action="{{ route('alert.index') }}" method="GET">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <!-- Search Input -->
                    <div style="min-width: 200px; flex: 0 0 auto;">
                        <input type="text" class="form-control" id="system_keyword" name="system_keyword" placeholder="Search Alerts" value="{{ request('system_keyword') }}">
                    </div>
                    
                    <!-- Date Range -->
                    <div class="d-flex align-items-center gap-2" style="min-width: 320px; flex: 0 0 auto;">
                        <span class="fw-bold text-nowrap">Date Range:</span>
                        <input type="date" class="form-control" id="date_from" name="date_from" placeholder="Start Date" value="{{ request('date_from') }}">
                        <span class="fw-bold">-</span>
                        <input type="date" class="form-control" id="date_to" name="date_to" placeholder="End Date" value="{{ request('date_to') }}">
                    </div>
                    
                    <!-- Severity Select -->
                    <div style="min-width: 180px; flex: 0 0 auto;">
                        <select class="form-select" id="severity" name="severity">
                            <option value="">All Severities</option>
                            <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                            <option value="warning" {{ request('severity') == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="info" {{ request('severity') == 'info' ? 'selected' : '' }}>Info</option>
                        </select>
                    </div>
                    
                    <!-- Status Select -->
                    <div style="min-width: 160px; flex: 0 0 auto;">
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2" style="flex: 0 0 auto;">
                        <button type="submit" class="btn btn-outline-secondary fw-bold text-primary-light px-16 py-12 radius-8 d-flex gap-2 align-items-center">
                            <iconify-icon icon="meteor-icons:filter" class="text-lg"></iconify-icon>
                            <span>Filter</span>
                        </button>
                        <button type="button" onclick="location.href='{{ route('alert.index') }}'" class="btn btn-outline-secondary fw-bold text-primary-light px-16 py-12 radius-8 d-flex gap-2 align-items-center">
                            <iconify-icon icon="ri:refresh-line" class="text-lg"></iconify-icon>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- END: Filtering System --}}

{{-- START: Alerts Summary Cards --}}
    <div class="row g-4 mb-24">
        {{-- Card 1: Total Alerts --}}
        <div class="col-xxl-3 col-sm-6">
            <div class="card stat-card style-1">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <div class="flex-grow-1">
                            <p class="fw-bold mb-8 text-lg">Total Alerts</p>
                            <h4 class="fw-bold mb-0" style="font-size: 50px">{{ $totalAlerts }}</h4>
                        </div>
                        <div class="text-2xl">
                            <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Card 2: Critical Alerts --}}
        <div class="col-xxl-3 col-sm-6">
            <div class="card stat-card style-1">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="fw-bold mb-8 text-lg">Critical Alerts</p>
                            <h4 class="fw-bold mb-0" style="font-size: 50px">{{ $criticalAlerts }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Card 3: Warning Alerts --}}
        <div class="col-xxl-3 col-sm-6">
            <div class="card stat-card style-1">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="fw-bold mb-8 text-lg">Warning Alerts</p>
                            <h4 class="fw-bold mb-0" style="font-size: 50px">{{ $warningAlerts }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Card 4: Resolved Alerts --}}
        <div class="col-xxl-3 col-sm-6">
            <div class="card stat-card style-1">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="fw-bold mb-8 text-lg">Resolved Alerts</p>
                            <h4 class="fw-bold mb-0" style="font-size: 50px">{{ $resolvedAlerts }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END: Alerts Summary Cards --}}

    <div class="row gy-4">
        <div class="col-12">
            <p class="text-lg fw-bold text-primary-light">Active Alerts</p>
            <div class="card basic-data-table">
                <div class="card-body" style="padding: 0 !important;">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10' style="border: 1px solid #707EAE; width: 100% !important; min-width: auto !important;">
                            <thead>
                            <tr>
                                <th scope="col">Severity</th>
                                <th scope="col">Alert Type</th>
                                <th scope="col">System ID</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Time</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($alerts as $alert)
                                <tr>
                                    <td>
                                        @if ($alert->severity === 'critical')
                                            <span class="bg-red text-white px-16 py-4 radius-12 fw-bold text-sm">Critical</span>
                                        @elseif ($alert->severity === 'warning')
                                            <span class="bg-yellow text-black px-16 py-4 radius-12 fw-bold text-sm">Warning</span>
                                        @else
                                            <span class="bg-info text-white px-16 py-4 radius-12 fw-bold text-sm">Info</span>
                                        @endif
                                    </td>
                                    <td>{{ $alert->alert_type }}</td>
                                    <td>{{ $alert->system->system_id ?? 'N/A' }}</td>
                                    <td>{{ $alert->system->customer_name ?? 'System Deleted' }}</td>
                                    <td>{{ $alert->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        @if ($alert->status === 'resolved')
                                            <span class="bg-success text-white px-16 py-4 radius-12 fw-bold text-sm">{{ ucfirst(str_replace('_', ' ', $alert->status)) }}</span>
                                        @elseif ($alert->status === 'pending')
                                            <span class="bg-red text-white px-16 py-4 radius-12 fw-bold text-sm">{{ ucfirst(str_replace('_', ' ', $alert->status)) }}</span>
                                        @elseif ($alert->status === 'in_progress')
                                            <span class="bg-yellow text-black px-16 py-4 radius-12 fw-bold text-sm">{{ ucfirst(str_replace('_', ' ', $alert->status)) }}</span>
                                        @else {{-- For 'scheduled' and any other default status --}}
                                        <span class="bg-blue text-white px-16 py-4 radius-12 fw-bold text-sm">{{ ucfirst(str_replace('_', ' ', $alert->status)) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <a href="{{ route('alert.show', $alert->id) }}" class="strong text-xxl text-primary-light">
                                                <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                            </a>
                                            <a href="{{ route('alert.edit', $alert->id) }}" class="strong text-xxl text-primary-light">
                                                <iconify-icon icon="lucide:edit"></iconify-icon>
                                            </a>
                                            <a href="javascript:void(0)" class="strong text-xxl text-primary-light" data-bs-toggle="modal" data-bs-target="#deleteAlertModal{{ $alert->id }}">
                                                <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="deleteAlertModal{{ $alert->id }}" tabindex="-1" role="dialog" aria-hidden="false">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('Delete Alert') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <h5>Are you sure you want to delete this alert?</h5>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form class="d-inline-block" action="{{ route('alert.destroy', $alert->id) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger">Yes, delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- START: Alert Frequency Chart --}}
    <div class="row gy-4 mt-4">
        <div class="col-12">
            <p class="text-lg fw-bold text-primary-light">All Trends</p>
            <div class="card">
                <div class="card-header" style="border-bottom: 0;">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="fw-bold text-xxl mb-0">Alert Frequency</h6>
                        <ul class="nav nav-pills-simple nav-pills-simple-sm" id="pills-tab" role="tablist" style="gap: 10px;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold px-24 py-8 radius-8" id="pills-30-days-tab" data-bs-toggle="pill" data-bs-target="#pills-30-days" type="button" role="tab" aria-controls="pills-30-days" aria-selected="true">30 Days</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold px-24 py-8 radius-8" id="pills-90-days-tab" data-bs-toggle="pill" data-bs-target="#pills-90-days" type="button" role="tab" aria-controls="pills-90-days" aria-selected="false">90 Days</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold px-24 py-8 radius-8" id="pills-1-year-tab" data-bs-toggle="pill" data-bs-target="#pills-1-year" type="button" role="tab" aria-controls="pills-1-year" aria-selected="false">1 Year</button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-24" style="padding-top: 0 !important;">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-30-days" role="tabpanel" aria-labelledby="pills-30-days-tab" tabindex="0">
                            <div id="alertFrequencyChart"></div>
                        </div>
                        {{-- Placeholder for 90 days and 1 year tabs --}}
                        <div class="tab-pane fade" id="pills-90-days" role="tabpanel" aria-labelledby="pills-90-days-tab" tabindex="0">
                            <p class="text-secondary-light">Data for 90 days will appear here. (Requires AJAX implementation)</p>
                        </div>
                        <div class="tab-pane fade" id="pills-1-year" role="tabpanel" aria-labelledby="pills-1-year-tab" tabindex="0">
                            <p class="text-secondary-light">Data for 1 year will appear here. (Requires AJAX implementation)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END: Alert Frequency Chart --}}


@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function() {
            // Initialize main DataTable
            $('#dataTable').DataTable({
                //scrollX: true
            });

            // ApexCharts for Alert Frequency (Multiple Lines - Critical, Warning, Info, Resolved)
            var options = {
                series: [{
                    name: 'Critical Alerts',
                    data: @json($criticalAlertData)
                }, {
                    name: 'Warning Alerts',
                    data: @json($warningAlertData)
                }, {
                    name: 'Info Alerts',
                    data: @json($infoAlertData)
                }, {
                    name: 'Resolved Alerts',
                    data: @json($resolvedAlertData)
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
                    categories: @json($alertFrequencyLabels),
                    labels: {
                        style: {
                            colors: '#A0AEC0',
                        },
                    },
                },
                yaxis: {
                    title: {
                        text: 'Number of Alerts'
                    },
                    labels: {
                        formatter: function (value) {
                            return parseInt(value);
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
                },
                grid: {
                    borderColor: '#f1f1f1',
                },
                // Colors: Critical (Danger), Warning, Info, Resolved (Success)
                colors: ['#EE3D5B', '#F7941C', '#17A2B8', '#0D9863']
            };

            var chart = new ApexCharts(document.querySelector("#alertFrequencyChart"), options);
            chart.render();
        });
    </script>
@endpush
