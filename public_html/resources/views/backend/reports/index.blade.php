@extends('backend.layouts.master')
@section('title') {{'Reports'}} @endsection

@section('breadcrumb') Pages / Reports @endsection
@section('page-title') Reports @endsection

@section('content')
    @include('backend.partials.alert')

    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    </div>

    {{-- Report filters --}}
    <div class="row mb-20">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body p-24">
                    <div class="d-flex gap-32 align-items-center">
                        <span class="text-md fw-bold">Report type:</span>
                        <div class="d-flex gap-3 align-items-center">
                            <button type="button" class="btn btn-outline-neutral-900 fw-bold px-24 py-12 radius-8">Daily Production</button>
                            <button type="button" class="btn btn-outline-neutral-900 fw-bold px-24 py-12 radius-8">Weekly/ Monthly Summary</button>
                            <button type="button" class="btn btn-outline-neutral-900 fw-bold px-24 py-12 radius-8">Error Logs</button>
                            <button type="button" class="btn btn-outline-neutral-900 fw-bold px-24 py-12 radius-8">Custom Report</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Production Heatmap --}}
    <div class="row mb-20">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header" style="border-bottom: 0;">
                    <h6 class="mb-2 fw-bold text-lg">Production Heatmap</h6>
                </div>
                <div class="card-body p-24" style="padding-top: 0 !important">
                    <div id="productionIntensityHeatmap"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Exporting Section --}}
    <div class="row mb-20">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header" style="border-bottom: 0; padding-top: 43px;">
                    <h6 class="mb-0 fw-bold text-md">Export Reports</h6>
                </div>
                <div class="card-body" style="padding: 0 24px 21px;">
                    <form action="{{ route('reports.export') }}" method="POST">
                        @csrf
                        <div class="row gy-3">
                            {{-- Export Format (Radio Buttons) --}}
                            <div class="col-md-4">
                                <label class="form-label mb-16 fw-bold text-md">Format</label>
                                <div class="export-format-radio">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="format" id="formatCsv" value="csv" checked>
                                        <label class="form-check-label" for="formatCsv">CSV</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="format" id="formatXlsx" value="xlsx">
                                        <label class="form-check-label" for="formatXlsx">Excel</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="format" id="formatPdf" value="pdf">
                                        <label class="form-check-label" for="formatPdf">PDF</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="format" id="formatJson" value="json">
                                        <label class="form-check-label" for="formatJson">JSON</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Data to Include (Checkboxes) --}}
                            <div class="col-md-4">
                                <label class="form-label mb-16 fw-bold text-md">Data to Include</label>
                                <div class="data-to-include">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="data_types[]" id="dataEnergyProduction" value="energy_production" checked>
                                        <label class="form-check-label" for="dataEnergyProduction">Energy Production</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="data_types[]" id="dataSystemEfficiency" value="system_efficiency" checked>
                                        <label class="form-check-label" for="dataSystemEfficiency">System Efficiency</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="data_types[]" id="dataAlertsErrors" value="alerts_errors">
                                        <label class="form-check-label" for="dataAlertsErrors">Alerts & Errors</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="data_types[]" id="dataWeatherData" value="weather_data" checked>
                                        <label class="form-check-label" for="dataWeatherData">Weather Data</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="data_types[]" id="dataMaintenanceHistory" value="maintenance_history" checked>
                                        <label class="form-check-label" for="dataMaintenanceHistory">Maintenance History</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Scheduling --}}
                            <div class="col-md-4">
                                <label class="form-label mb-16 fw-bold text-md">Scheduling</label>
                                <div class="scheduling">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export-duration" id="one-time-export" value="one-time-export" checked>
                                        <label class="form-check-label" for="one-time-export">One-time Export</label>
                                    </div>
                                </div>
                                <div class="scheduling">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export-duration" id="recurring-export" value="recurring-export">
                                        <label class="form-check-label" for="recurring-export">Recurring Export</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn text-primary-light text-md px-20 py-6 radius-4 d-flex align-items-center fw-bold" style="background-color: #A3AED0; gap: 4px;">
                                    <span class="text-xl">
                                        <iconify-icon icon="lucide:download"></iconify-icon>
                                    </span>
                                    <span>Export Now</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Reports Section --}}
    <div class="row mb-20">
        <div class="col-xl-12">
            <div class="card h-100">
                <div class="card-header" style="border-bottom: 0;">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-0 fw-bold text-md">Recent Reports</h6>
                        <a href="javascript:void(0)" class="text-neutral-900 hover-text-primary fw-medium">
                            View All Reports
                        </a>
                    </div>
                </div>
                <div class="card-body" style="padding: 0 26px 31px !important">
                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table recent-reports-table mb-0">
                            <thead>
                            <tr>
                                <th scope="col" style="border-radius: 0 !important;">REPORT NAME</th>
                                <th scope="col">ISSUES</th>
                                <th scope="col">GENERATED</th>
                                <th scope="col">TYPE</th>
                                <th scope="col">SIZE</th>
                                <th scope="col" style="border-radius: 0 !important;">ACTIONS</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="">Weekly Summary - April 21-27</span>
                                    </td>
                                    <td>
                                        <span class="">Panel Disconnected</span>
                                    </td>
                                    <td>
                                        <span class="">April 28, 2025</span>
                                    </td>
                                    <td>
                                        <span class="">Excel</span>
                                    </td>
                                    <td>
                                        <span class="">1.2 MB</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="javascript:void(0)" class="strong text-xxl text-primary-light">
                                                <iconify-icon icon="lucide:download" class="icon"></iconify-icon>
                                            </a>
                                            <a href="javascript:void(0)" class="strong text-xxl text-primary-light">
                                                <iconify-icon icon="solar:share-outline" class="icon"></iconify-icon>
                                            </a>
                                            <a href="javascript:void(0)" class="strong text-xxl text-primary-light">
                                                <iconify-icon icon="lucide:trash" class="icon"></iconify-icon>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="">Monthly Production - March 2025</span>
                                    </td>
                                    <td>
                                        <span class="">Panel Disconnected</span>
                                    </td>
                                    <td>
                                        <span class="">April 18, 2025</span>
                                    </td>
                                    <td>
                                        <span class="">PDF</span>
                                    </td>
                                    <td>
                                        <span class="">3.2 MB</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="javascript:void(0)" class="strong text-xxl">
                                                <iconify-icon icon="lucide:download" class="icon"></iconify-icon>
                                            </a>
                                            <a href="javascript:void(0)" class="strong text-xxl text-primary-light">
                                                <iconify-icon icon="solar:share-outline" class="icon"></iconify-icon>
                                            </a>
                                            <a href="javascript:void(0)" class="strong text-xxl">
                                                <iconify-icon icon="lucide:trash" class="icon"></iconify-icon>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-radius: 0;">
                                        <span class="">System Efficiency Analysis</span>
                                    </td>
                                    <td>
                                        <span class="">Data Error</span>
                                    </td>
                                    <td>
                                        <span class="">April 10, 2025</span>
                                    </td>
                                    <td>
                                        <span class="">CSV</span>
                                    </td>
                                    <td>
                                        <span class="">876 KB</span>
                                    </td>
                                    <td style="border-radius: 0;">
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="javascript:void(0)" class="strong text-xxl text-primary-light">
                                                <iconify-icon icon="lucide:download" class="icon"></iconify-icon>
                                            </a>
                                            <a href="javascript:void(0)" class="strong text-xxl text-primary-light">
                                                <iconify-icon icon="solar:share-outline" class="icon"></iconify-icon>
                                            </a>
                                            <a href="javascript:void(0)" class="strong text-xxl text-primary-light">
                                                <iconify-icon icon="lucide:trash" class="icon"></iconify-icon>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // ===============================================
    // Production Intensity Heatmap Chart
    // ===============================================
    document.addEventListener('DOMContentLoaded', function() {
        // Function to fetch and render the chart
        function renderProductionIntensityHeatmap(selectedDate = null) {
            let apiUrl = "{{ route('reports.productionIntensityData') }}";
            if (selectedDate) {
                apiUrl += `?date=${selectedDate}`;
            }

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Fetched Production Intensity Data:", data); // Debugging: check fetched data

                    var heatmapOptions = {
                        series: data, // The data comes directly from your API
                        chart: {
                            height: 350, // This is a good starting height for a heatmap
                            type: 'heatmap',
                            toolbar: {
                                show: false // You can set this to true if you want export options
                            },
                        },
                        dataLabels: {
                            enabled: false // Usually off for heatmaps, but can be turned on
                        },
                        colors: ["#008FFB"], // Default color, will be overridden by custom color scales
                        xaxis: {
                            type: 'category', // X-axis categories are the time labels
                            // Assuming your backend returns time labels like "00:00", "01:00"
                            categories: Array.from({length: 24}, (_, i) => `${String(i).padStart(2, '0')}:00`),
                            title: {
                                text: 'Time of Day (Hour)'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'System ID' // Y-axis will be system IDs/names
                            }
                        },
                        plotOptions: {
                            heatmap: {
                                shadeIntensity: 0.5,
                                radius: 0,
                                useFillColorAsStroke: true,
                                colorScale: {
                                    ranges: [{
                                        from: 0,
                                        to: 0,
                                        name: 'No Production',
                                        color: '#e0e0e0' // Light grey for no production
                                    }, {
                                        from: 0.1, // Start just above 0
                                        to: 10, // Example range, adjust based on your power values
                                        name: 'Low Production',
                                        color: '#ffe0b2' // Light orange
                                    }, {
                                        from: 10.1,
                                        to: 50,
                                        name: 'Medium Production',
                                        color: '#ffc107' // Orange
                                    }, {
                                        from: 50.1,
                                        to: 100,
                                        name: 'High Production',
                                        color: '#ff8f00' // Darker orange
                                    }, {
                                        from: 100.1,
                                        to: 500, // Adjust max value as needed
                                        name: 'Very High Production',
                                        color: '#bf360c' // Deep red-orange
                                    }]
                                }
                            }
                        },
                        title: {
                            text: 'Production Intensity by System and Time',
                            align: 'left'
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val + ' kW'; // Customize tooltip value
                                }
                            }
                        }
                    };

                    var heatmapChart = new ApexCharts(document.querySelector("#productionIntensityHeatmap"), heatmapOptions);
                    heatmapChart.render();
                })
                .catch(error => {
                    console.error('Error fetching production intensity data:', error);
                    // Display an error message to the user if the chart fails to load
                    document.querySelector("#productionIntensityHeatmap").innerHTML = "<p class='text-danger'>Failed to load production intensity chart.</p>";
                });
        }

        // Call the function to render the chart when the page loads
        renderProductionIntensityHeatmap();

        // Optional: Add a date picker to allow users to select a specific date
        // This assumes you have a date input field with id="productionDateFilter"
        // You might need to include a date picker library (like jQuery UI Datepicker or Flatpickr)
        /*
        const dateFilterInput = document.getElementById('productionDateFilter');
        if (dateFilterInput) {
            dateFilterInput.addEventListener('change', (event) => {
                renderProductionIntensityHeatmap(event.target.value);
            });
        }
        */
    });
</script>
@endpush
