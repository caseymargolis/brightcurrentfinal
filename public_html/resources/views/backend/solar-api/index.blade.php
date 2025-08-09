@extends('backend.layouts.master')
@section('title') {{'Solar API Management'}} @endsection

@section('breadcrumb') Pages / Solar API @endsection
@section('page-title') Solar API Management @endsection

@section('content')
    <!-- API Status Overview -->
    <div class="row gy-4 mb-5">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-secondary-light mb-1">Total Systems</h6>
                            <h2 class="mb-0">{{ $totalSystems }}</h2>
                        </div>
                        <div class="w-50-px h-50-px bg-primary rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:solar-panel-outline" class="h5 mb-0 text-white"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-secondary-light mb-1">API Enabled</h6>
                            <h2 class="mb-0 text-success">{{ $apiEnabledSystems }}</h2>
                        </div>
                        <div class="w-50-px h-50-px bg-success rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:wifi-router-round-outline" class="h5 mb-0 text-white"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-secondary-light mb-1">API Disabled</h6>
                            <h2 class="mb-0 text-warning">{{ $totalSystems - $apiEnabledSystems }}</h2>
                        </div>
                        <div class="w-50-px h-50-px bg-warning rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:wifi-router-round-outline" class="h5 mb-0 text-white"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- API Connection Tests -->
    <div class="row gy-4 mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">API Connection Status</h6>
                        <button class="btn btn-primary d-flex align-items-center gap-2" onclick="testAllConnections()">
                            <iconify-icon icon="solar:refresh-outline"></iconify-icon>
                            Test All Connections
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-lg-4">
                            <div class="d-flex align-items-center justify-content-between p-3 border radius-8" id="enphase-status">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="w-40-px h-40-px bg-primary-light d-flex justify-content-center align-items-center radius-8">
                                        <iconify-icon icon="solar:energy-outline" class="text-primary"></iconify-icon>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Enphase Energy</h6>
                                        <small class="text-muted">System monitoring API</small>
                                    </div>
                                </div>
                                <span class="badge bg-secondary">Testing...</span>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="d-flex align-items-center justify-content-between p-3 border radius-8" id="solaredge-status">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="w-40-px h-40-px bg-success-light d-flex justify-content-center align-items-center radius-8">
                                        <iconify-icon icon="solar:panel-outline" class="text-success"></iconify-icon>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">SolarEdge</h6>
                                        <small class="text-muted">Monitoring platform API</small>
                                    </div>
                                </div>
                                <span class="badge bg-secondary">Testing...</span>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="d-flex align-items-center justify-content-between p-3 border radius-8" id="tesla-status">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="w-40-px h-40-px bg-info-light d-flex justify-content-center align-items-center radius-8">
                                        <iconify-icon icon="solar:battery-charge-outline" class="text-info"></iconify-icon>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Tesla Energy</h6>
                                        <small class="text-muted">Powerwall & Solar API</small>
                                    </div>
                                </div>
                                <span class="badge bg-secondary">Testing...</span>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="d-flex align-items-center justify-content-between p-3 border radius-8" id="weather-status">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="w-40-px h-40-px bg-warning-light d-flex justify-content-center align-items-center radius-8">
                                        <iconify-icon icon="solar:cloud-sun-outline" class="text-warning"></iconify-icon>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">WeatherAPI.com</h6>
                                        <small class="text-muted">Weather monitoring API</small>
                                    </div>
                                </div>
                                <span class="badge bg-secondary">Testing...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="row gy-4 mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="fw-bold mb-0">Bulk Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 flex-wrap">
                        <button class="btn btn-success d-flex align-items-center gap-2" onclick="syncAllSystems()">
                            <iconify-icon icon="solar:refresh-outline"></iconify-icon>
                            Sync All Systems
                        </button>
                        <button class="btn btn-info d-flex align-items-center gap-2" onclick="showSystemMapping()">
                            <iconify-icon icon="solar:settings-outline"></iconify-icon>
                            Configure System Mapping
                        </button>
                        <button class="btn btn-warning d-flex align-items-center gap-2" onclick="exportSystemData()">
                            <iconify-icon icon="solar:download-outline"></iconify-icon>
                            Export System Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Systems Configuration -->
    <div class="row gy-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">System API Configuration</h6>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="manufacturerFilter" onchange="filterByManufacturer()">
                                <option value="">All Manufacturers</option>
                                <option value="enphase">Enphase</option>
                                <option value="solaredge">SolarEdge</option>
                                <option value="tesla">Tesla</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding: 0 !important;">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="systemsTable">
                            <thead>
                            <tr>
                                <th style="padding: 16px 16px 16px 34px !important;">System</th>
                                <th>Manufacturer</th>
                                <th>External ID</th>
                                <th>API Status</th>
                                <th>Last Sync</th>
                                <th>Data Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($systems as $system)
                                <tr data-manufacturer="{{ strtolower($system->manufacturer) }}">
                                    <td style="padding: 16px 16px 16px 27px !important;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="{{ $system->customer_type === 'residential' ? 'ri-home-3-line' : 'ri-building-line' }} text-xl me-8 d-flex w-32-px h-32-px bg-primary-light rounded-circle d-inline-flex align-items-center justify-content-center"></i>
                                            <div>
                                                <h6 class="text-md mb-0 strong">{{ $system->customer_name }}</h6>
                                                <span class="text-sm text-primary-semi-light fw-medium">{{ $system->system_id }}</span>
                                                @if($system->location)
                                                    <br><small class="text-muted">{{ Str::limit($system->location, 40) }}</small>
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
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-sm">{{ $system->external_system_id ?: 'Not set' }}</span>
                                            <button class="btn p-1" onclick="editExternalId({{ $system->id }}, '{{ $system->external_system_id }}')" title="Edit External ID" style="background: none; border: none; color: #007bff; font-size: 16px;">
                                                <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        @if($system->api_enabled)
                                            <span class="badge bg-success text-white px-12 py-4 radius-8 fw-bold text-xs d-flex align-items-center">
                                                <iconify-icon icon="solar:check-circle-outline" class="me-1"></iconify-icon>
                                                ENABLED
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark px-12 py-4 radius-8 fw-bold text-xs d-flex align-items-center">
                                                <iconify-icon icon="solar:close-circle-outline" class="me-1"></iconify-icon>
                                                DISABLED
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($system->last_seen)
                                            <span class="text-sm">{{ $system->last_seen->diffForHumans() }}</span>
                                            @if($system->last_seen->lt(now()->subHours(2)))
                                                <br><small class="text-warning">
                                                    <iconify-icon icon="solar:danger-triangle-outline"></iconify-icon>
                                                    Stale
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $hasData = $system->productionData()->exists();
                                            $hasRecentData = $system->productionData()->whereDate('date', today())->exists();
                                        @endphp
                                        
                                        @if($hasRecentData)
                                            <span class="badge bg-success text-white d-flex align-items-center">
                                                <iconify-icon icon="solar:check-circle-outline" class="me-1"></iconify-icon>
                                                Current
                                            </span>
                                        @elseif($hasData)
                                            <span class="badge bg-warning text-dark d-flex align-items-center">
                                                <iconify-icon icon="solar:history-outline" class="me-1"></iconify-icon>
                                                Historical
                                            </span>
                                        @else
                                            <span class="badge bg-danger text-white d-flex align-items-center">
                                                <iconify-icon icon="solar:close-circle-outline" class="me-1"></iconify-icon>
                                                No Data
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn p-1" onclick="syncSystem({{ $system->id }})" title="Sync Now" style="background: none; border: none; color: #007bff; font-size: 16px;">
                                                <iconify-icon icon="solar:refresh-outline"></iconify-icon>
                                            </button>
                                            <button class="btn p-1" onclick="toggleApiStatus({{ $system->id }}, {{ $system->api_enabled ? 'false' : 'true' }})" title="{{ $system->api_enabled ? 'Disable' : 'Enable' }} API" style="background: none; border: none; color: #17a2b8; font-size: 16px;">
                                                <iconify-icon icon="solar:{{ $system->api_enabled ? 'eye-closed-outline' : 'eye-outline' }}"></iconify-icon>
                                            </button>
                                            <a href="{{ route('system.show', $system->id) }}" class="btn p-1" title="View Details" style="background: none; border: none; color: #28a745; font-size: 16px;">
                                                <iconify-icon icon="solar:chart-outline"></iconify-icon>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Edit External ID Modal -->
    <div class="modal fade" id="editExternalIdModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit External System ID</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editExternalIdForm">
                        <input type="hidden" id="editSystemId">
                        <div class="mb-3">
                            <label for="externalSystemId" class="form-label">External System ID</label>
                            <input type="text" class="form-control" id="externalSystemId" placeholder="Enter system ID from API provider">
                            <small class="form-text text-muted">This ID links your system to the external API (Enphase, SolarEdge, or Tesla)</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveExternalId()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    // Test all API connections
    function testAllConnections() {
        const statusElements = {
            enphase: document.getElementById('enphase-status'),
            solaredge: document.getElementById('solaredge-status'),
            tesla: document.getElementById('tesla-status'),
            weather: document.getElementById('weather-status')
        };

        // Reset all to testing state
        Object.keys(statusElements).forEach(api => {
            const badge = statusElements[api].querySelector('.badge');
            badge.className = 'badge bg-secondary';
            badge.innerHTML = '<iconify-icon icon="solar:loading-outline" class="animate-spin me-1"></iconify-icon>Testing...';
        });

        fetch('/dashboard/solar-api/test-connections', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            Object.keys(data).forEach(api => {
                const element = statusElements[api];
                const badge = element.querySelector('.badge');
                const result = data[api];

                if (result.success === true) {
                    badge.className = 'badge bg-success d-flex align-items-center';
                    badge.innerHTML = '<iconify-icon icon="solar:check-circle-outline" class="me-1"></iconify-icon>Connected';
                } else {
                    badge.className = 'badge bg-danger d-flex align-items-center';
                    badge.innerHTML = '<iconify-icon icon="solar:close-circle-outline" class="me-1"></iconify-icon>Failed';
                }
                
                // Add tooltip with details
                badge.title = result.message;
            });
        })
        .catch(error => {
            console.error('Error testing connections:', error);
            Object.keys(statusElements).forEach(api => {
                const badge = statusElements[api].querySelector('.badge');
                badge.className = 'badge bg-danger';
                badge.innerHTML = '<iconify-icon icon="solar:close-circle-outline" class="me-1"></iconify-icon>Error';
            });
        });
    }

    // Sync all systems
    function syncAllSystems() {
        if (!confirm('This will sync data for all API-enabled systems. Continue?')) return;

        const button = event.target;
        const originalContent = button.innerHTML;
        button.innerHTML = '<iconify-icon icon="solar:loading-outline" class="animate-spin me-1"></iconify-icon>Syncing...';
        button.disabled = true;

        fetch('/dashboard/solar-api/sync-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Bulk sync initiated successfully', 'success');
            } else {
                showNotification(data.message || 'Failed to start bulk sync', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred during bulk sync', 'error');
        })
        .finally(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }

    // Sync individual system
    function syncSystem(systemId) {
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
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
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message || 'Failed to sync system', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while syncing', 'error');
        })
        .finally(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }

    // Toggle API status
    function toggleApiStatus(systemId, enable) {
        fetch(`/dashboard/solar-api/update-system/${systemId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                api_enabled: enable
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`API ${enable ? 'enabled' : 'disabled'} successfully`, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message || 'Failed to update API status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while updating API status', 'error');
        });
    }

    // Edit external ID
    function editExternalId(systemId, currentId) {
        document.getElementById('editSystemId').value = systemId;
        document.getElementById('externalSystemId').value = currentId || '';
        new bootstrap.Modal(document.getElementById('editExternalIdModal')).show();
    }

    // Save external ID
    function saveExternalId() {
        const systemId = document.getElementById('editSystemId').value;
        const externalId = document.getElementById('externalSystemId').value;

        fetch(`/dashboard/solar-api/update-system/${systemId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                external_system_id: externalId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('External system ID updated successfully', 'success');
                bootstrap.Modal.getInstance(document.getElementById('editExternalIdModal')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message || 'Failed to update external system ID', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while updating external system ID', 'error');
        });
    }

    // Filter by manufacturer
    function filterByManufacturer() {
        const filter = document.getElementById('manufacturerFilter').value.toLowerCase();
        const rows = document.querySelectorAll('#systemsTable tbody tr');

        rows.forEach(row => {
            const manufacturer = row.getAttribute('data-manufacturer');
            if (!filter || manufacturer === filter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Notification function
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        let container = document.querySelector('.notification-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'notification-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        
        container.innerHTML = alertHtml;
        
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    // Placeholder functions
    function showSystemMapping() {
        showNotification('System mapping feature coming soon', 'info');
    }

    function exportSystemData() {
        showNotification('Export feature coming soon', 'info');
    }

    // Test connections on page load
    document.addEventListener('DOMContentLoaded', function() {
        testAllConnections();
    });

    // Add CSS for animations
    const style = document.createElement('style');
    style.textContent = `
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
</script>
@endpush
