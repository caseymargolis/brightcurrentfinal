@extends('backend.layouts.master')
@section('title') {{'System List'}} @endsection

@section('breadcrumb') Pages / System @endsection
@section('page-title') System List @endsection

@section('content')
    <div class="page-header d-flex flex-wrap align-items-center justify-content-end gap-3 mb-24">
        <a href="{{ route('system.create') }}" class="btn bg-dark text-light text-sm btn-sm px-8 py-8 radius-4 d-flex align-items-center">
            <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
            Add New System
        </a>
    </div>

    <div class="row gy-4">
        <div class="col-12">
            <div class="card basic-data-table">
                <div class="card-body" style="padding: 0 !important;">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10' style="border: 1px solid #707EAE; min-width: 1200px;">
                            <thead>
                            <tr>
                                <th scope="col" style="padding: 16px 16px 16px 34px !important; min-width: 330px; width: 330px;">Customer</th>
                                <th scope="col" style="min-width: 120px; width: 120px;">Manufacturer</th>
                                <th scope="col" style="min-width: 160px; width: 160px;">System ID</th>
                                <th scope="col" style="min-width: 100px; width: 100px;">Status</th>
                                <th scope="col" style="min-width: 150px; width: 150px;">API Status</th>
                                <th scope="col" style="min-width: 200px; width: 200px;">Current Power</th>
                                <th scope="col" style="min-width: 240px; width: 240px;">Today's Energy</th>
                                <th scope="col" style="min-width: 250px; width: 250px;">Yesterday's Energy</th>
                                <th scope="col" style="min-width: 200px; width: 200px;">Last Seen</th>
                                <th scope="col" style="min-width: 200px; width: 200px;">Action</th>
                            </tr>
                            </thead>
                        <tbody>
                        @foreach ($systems as $system)
                            <tr>
                                <td style="padding: 16px 16px 16px 27px !important;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="{{ $system->customer_type === 'residential' ? 'ri-home-3-line' : 'ri-building-line' }} text-xl me-8 d-flex w-32-px h-32-px bg-primary-light rounded-circle d-inline-flex align-items-center justify-content-center"></i>
                                        <div class="flex-grow-1">
                                            <h6 class="text-md mb-0 strong">{{ $system->customer_name }}</h6>
                                            <span class="text-sm text-primary-semi-light fw-medium">{{ ucfirst($system->customer_type) }}</span>
                                            @if($system->location)
                                                <br><small class="text-muted">{{ strlen($system->location) > 30 ? substr($system->location, 0, 27) . '...' : $system->location }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $manufacturerColors = [
                                            'enphase' => 'primary',
                                            'solaredge' => 'success', 
                                            'tesla' => 'info'
                                        ];
                                        $color = $manufacturerColors[strtolower($system->manufacturer)] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }} text-white px-12 py-4 radius-8 fw-bold text-sm">
                                        {{ strtoupper($system->manufacturer) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $system->system_id }}</span>
                                    @if($system->external_system_id)
                                        <br><small class="text-muted">Ext: {{ $system->external_system_id }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'active' => 'success',
                                            'warning' => 'warning',
                                            'critical' => 'danger',
                                            'offline' => 'secondary',
                                            'maintenance' => 'info'
                                        ];
                                        $statusColor = $statusColors[strtolower($system->status)] ?? 'secondary';
                                    @endphp
                                    <span class="bg-{{ $statusColor }} text-white px-16 py-4 radius-12 fw-bold text-sm">
                                        {{ strtoupper($system->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($system->api_enabled)
                                        <div class="d-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:check-circle-outline" class="text-success text-lg"></iconify-icon>
                                            <span class="text-success fw-semibold text-xs">ENABLED</span>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:close-circle-outline" class="text-warning text-lg"></iconify-icon>
                                            <span class="text-warning fw-semibold text-xs">DISABLED</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($system->latestProductionData)
                                        <span class="fw-semibold text-primary">
                                            {{ number_format($system->latestProductionData->power_current ?? 0, 1) }} kW
                                        </span>
                                    @else
                                        <span class="text-muted">No data</span>
                                    @endif
                                </td>
                                <td>
                                    @if($system->todayProductionData)
                                        <span class="fw-semibold text-success">
                                            {{ number_format($system->todayProductionData->energy_today ?? 0, 1) }} kWh
                                        </span>
                                    @else
                                        <span class="text-muted">No data</span>
                                    @endif
                                </td>
                                <td>
                                    @if($system->latestProductionData)
                                        <span class="fw-semibold text-info">
                                            {{ number_format($system->latestProductionData->energy_yesterday ?? 0, 1) }} kWh
                                        </span>
                                    @else
                                        <span class="text-muted">No data</span>
                                    @endif
                                </td>
                                <td>
                                    @if($system->last_seen)
                                        <span class="text-sm">{{ $system->last_seen->diffForHumans() }}</span>
                                        @if($system->last_seen->lt(now()->subHours(2)))
                                            <br><small class="text-warning">
                                                <iconify-icon icon="solar:danger-triangle-outline"></iconify-icon>
                                                Stale data
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1 align-items-center">
                                        @if($system->api_enabled)
                                            <button onclick="syncSystem({{ $system->id }})" 
                                                    class="btn p-1" 
                                                    title="Sync Data"
                                                    style="background: none; border: none; color: #007bff; font-size: 16px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                                <iconify-icon icon="solar:refresh-outline"></iconify-icon>
                                            </button>
                                        @endif
                                        <a href="{{ route('system.show', $system->id) }}" 
                                           class="btn p-1" 
                                           title="View Details"
                                           style="background: none; border: none; color: #17a2b8; font-size: 16px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                        </a>
                                        <a href="{{ route('system.edit', $system->id) }}" 
                                           class="btn p-1" 
                                           title="Edit System"
                                           style="background: none; border: none; color: #ffc107; font-size: 16px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                            <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                        </a>
                                        <button class="btn p-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{$system->id}}"
                                                title="Delete System"
                                                style="background: none; border: none; color: #dc3545; font-size: 16px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                            <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <div class="modal fade" id="deleteModal{{ $system->id }}" tabindex="-1" role="dialog" aria-hidden="false">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('Delete System') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <h5>Are you sure you want to delete this system?</h5>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form class="d-inline-block" action="{{ route('system.destroy', $system->id) }}" method="POST">
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
@endsection

@push('script')
    <script>
        let table = new DataTable('#dataTable', {
            scrollX: true,
            responsive: false,
            autoWidth: false,
            ordering: true,
            searching: true,
            paging: true,
            info: true,
            columnDefs: [
                { width: "330px", targets: 0 }, // Customer
                { width: "120px", targets: 1 }, // Manufacturer
                { width: "160px", targets: 2 }, // System ID
                { width: "100px", targets: 3 }, // Status
                { width: "150px", targets: 4 }, // API Status
                { width: "200px", targets: 5 }, // Current Power
                { width: "240px", targets: 6 }, // Today's Energy
                { width: "200px", targets: 7 }, // Yesterday's Energy
                { width: "200px", targets: 8 }, // Last Seen
                { width: "200px", targets: 9, orderable: false }  // Action - disable sorting for action column
            ]
        });

        // Function to sync individual system data
        function syncSystem(systemId) {
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            
            // Show loading state
            button.innerHTML = '<iconify-icon icon="solar:loading-outline" class="animate-spin"></iconify-icon>';
            button.disabled = true;

            fetch(`/dashboard/solar-api/sync-system/${systemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('System sync initiated successfully', 'success');
                    // Refresh the page after a delay to show updated data
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showNotification(data.message || 'Failed to sync system', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while syncing', 'error');
            })
            .finally(() => {
                // Restore button state
                button.innerHTML = originalContent;
                button.disabled = false;
            });
        }

        // Simple notification function
        function showNotification(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Find or create notification container
            let container = document.querySelector('.notification-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'notification-container position-fixed top-0 end-0 p-3';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
            }
            
            container.innerHTML = alertHtml;
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }

        // Add CSS for spinning animation and table scrolling
        const style = document.createElement('style');
        style.textContent = `
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            
            /* Custom scrollbar for table */
            .table-responsive {
                scrollbar-width: thin;
                scrollbar-color: #007bff #f8f9fa;
            }
            
            .table-responsive::-webkit-scrollbar {
                height: 12px;
            }
            
            .table-responsive::-webkit-scrollbar-track {
                background: #f8f9fa;
                border-radius: 6px;
            }
            
            .table-responsive::-webkit-scrollbar-thumb {
                background: #007bff;
                border-radius: 6px;
                border: 2px solid #f8f9fa;
            }
            
            .table-responsive::-webkit-scrollbar-thumb:hover {
                background: #0056b3;
            }
            
            /* Hide DataTables generated duplicate header row */
            #dataTable thead tr:not(:first-child) {
                display: none !important;
            }
            
            /* Hide DataTables extra wrapper elements in original headers */
            #dataTable thead th .dt-scroll-sizing {
                display: none !important;
            }
            
            /* Keep original header styling */
            #dataTable thead th {
                background: none !important;
                border: none !important;
                padding: 16px !important;
            }
            
            #dataTable thead th:first-child {
                padding: 16px 16px 16px 34px !important;
            }
            
            /* Ensure table cells don't wrap */
            #dataTable td, #dataTable th {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            /* Allow customer column to wrap for long names */
            #dataTable td:first-child, #dataTable th:first-child {
                white-space: normal;
            }
        `;
        document.head.appendChild(style);
    </script>
@endpush
