@extends('backend.layouts.master')
@section('title') {{'Settings'}} @endsection

@section('breadcrumb') Pages / Settings @endsection
@section('page-title') Settings @endsection

@section('content')
    @include('backend.partials.alert')
    <div class="row gy-4">
        {{-- Users List Card --}}
        <div class="col-xl-12">
            <div class="d-flex align-items-end flex-wrap gap-2 justify-content-between" style="padding-left: 13px;">
                <h6 class="fw-bold text-lg mb-0">Users List</h6>
                <a href="javascript:void(0)" class="btn bg-dark text-light text-sm btn-sm px-28 py-12 radius-8 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="ri-user-add-line icon text-xl line-height-1"></i>
                    Add User
                </a>
            </div>
        </div>
        <div class="col-xl-12" style="margin-top: 4px;">
            <table class="table bordered-table mb-0 user-list" id="usersDataTable">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col">Last Login</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-10">
                                <h6 class="text-md mb-0">{{ $user->name }}</h6>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @forelse ($user->getRoleNames() as $role)
                                <span>{{ $role }}</span>
                            @empty
                                <span>No Role</span>
                            @endforelse
                        </td>
                        <td>{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->diffForHumans() : 'Never' }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="javascript:void(0)" class="strong text-xl text-primary-light" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" data-bs-title="Edit">
                                    <iconify-icon icon="line-md:edit-twotone" class="icon"></iconify-icon>
                                </a>
                                <a href="javascript:void(0)" class="strong text-xl text-primary-light" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                                    <iconify-icon icon="lucide:trash" class="icon"></iconify-icon>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Edit User: {{ $user->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT') {{-- Use PUT method for update requests --}}
                                        <div class="mb-4">
                                            <label class="form-label" for="edit_name_{{ $user->id }}">Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_name_{{ $user->id }}" name="name" value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label" for="edit_email_{{ $user->id }}">Email<span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="edit_email_{{ $user->id }}" name="email" value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label" for="edit_role_{{ $user->id }}">Role<span class="text-danger">*</span></label>
                                            <select class="form-select" id="edit_role_{{ $user->id }}" name="role">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label" for="edit_password_{{ $user->id }}">New Password (optional)</label>
                                            <input type="password" class="form-control" id="edit_password_{{ $user->id }}" name="password">
                                            <small class="form-text text-muted">Leave blank to keep current password.</small>
                                            @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label" for="edit_password_confirmation_{{ $user->id }}">Confirm New Password</label>
                                            <input type="password" class="form-control" id="edit_password_confirmation_{{ $user->id }}" name="password_confirmation">
                                        </div>
                                        <button type="submit" class="btn bg-dark text-light text-md px-24 py-4 radius-4 mt-24">
                                            Save Changes
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('Delete User') }}</h5>
                                </div>
                                <div class="modal-body text-center">
                                    <h5>Are you sure?</h5>
                                </div>
                                <div class="modal-footer">
                                    <form class="d-inline-block" action="{{ route('users.destroy', $user->id) }}" method="POST">
                                        @method('DELETE')
                                        @csrf
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-success">Yes, delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- API Integrations & Notifications --}}
        <div class="col-xl-12">
            <h5 class="fw-bold text-lg mb-3" style="padding-left: 13px;">API Integrations</h5>
            <div class="d-flex justify-content-between align-items-center gap-3">
                {{-- Inverter API Card --}}
                <div class="card mb-4" style="width: 32%; border-radius: 12px;">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center justify-content-between mb-24">
                            <div class="d-flex align-items-center gap-3">
                                <div class="w-40-px h-40-px api-icon-container text-xl d-flex align-items-center justify-content-center text-white" style="border-radius: 8px;">
                                    <iconify-icon icon="bx:plug" class="text-2xl"></iconify-icon>
                                </div>
                                <h6 class="mb-0 fw-bold text-lg">Inverter API</h6>
                            </div>
                            <span class="badge text-primary-light px-16 py-8 text-sm fw-medium radius-8" style="background-color: #A3AED0">Connected</span>
                        </div>
                        <p class="text-primary-semi-light mb-16 text-sm fw-semibold">Connect to SolarEdge inverter data</p>    
                        <div class="d-flex align-items-center justify-content-between gap-2" style="width: 100%">
                            <p class="text-primary-semi-light mb-2 text-sm fw-semibold">Resolved 3 hours ago</p>
                            <button type="button" class="w-36-px h-36-px bg-neutral-50 rounded-circle d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#inverterApiModal">
                                <iconify-icon icon="lucide:settings" class="text-xxl" style="color: #A3AED0"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- HouseCall API Card --}}
                <div class="card mb-4" style="width: 32%; border-radius: 12px;">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center justify-content-between mb-24">
                            <div class="d-flex align-items-center gap-3">
                                <div class="w-40-px h-40-px api-icon-container text-xl d-flex align-items-center justify-content-center text-white" style="border-radius: 8px;">
                                    <iconify-icon icon="material-symbols:call-outline" class="text-2xl"></iconify-icon>
                                </div>
                                <h6 class="mb-0 fw-bold text-lg">HouseCall API</h6>
                            </div>
                            <span class="badge text-primary-light px-16 py-8 text-sm fw-medium radius-8" style="background-color: #A3AED0">Connected</span>
                        </div>
                        <p class="text-primary-semi-light mb-16 text-sm fw-semibold">Service scheduling integration</p>    
                        <div class="d-flex align-items-center justify-content-between gap-2" style="width: 100%">
                            <p class="text-primary-semi-light mb-2 text-sm fw-semibold">Last Sync Today, 11:35 AM</p>
                            <button type="button" class="w-36-px h-36-px bg-neutral-50 rounded-circle d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#housecallApiModal">
                                <iconify-icon icon="lucide:settings" class="text-xxl" style="color: #A3AED0"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Weather API Card --}}
                <div class="card mb-4" style="width: 32%; border-radius: 12px;">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center justify-content-between mb-24">
                            <div class="d-flex align-items-center gap-3">
                                <div class="w-40-px h-40-px api-icon-container text-xl d-flex align-items-center justify-content-center text-white" style="border-radius: 8px;">
                                    <iconify-icon icon="material-symbols:cloud-outline" class="text-2xl"></iconify-icon>
                                </div>
                                <h6 class="mb-0 fw-bold text-lg">Weather API</h6>
                            </div>
                            <span class="badge text-primary-light px-16 py-8 text-sm fw-medium radius-8" style="background-color: #A3AED0">Disconnected</span>
                        </div>
                        <p class="text-primary-semi-light mb-16 text-sm fw-semibold">Local weather data integration</p>    
                        <div class="d-flex align-items-center justify-content-between gap-2" style="width: 100%">
                            <p class="text-primary-semi-light mb-2 text-sm fw-semibold">Last Sync 3 Hours ago</p>
                            <button type="button" class="w-36-px h-36-px bg-neutral-50 rounded-circle d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#weatherApiModal">
                                <iconify-icon icon="lucide:settings" class="text-xxl" style="color: #A3AED0"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- API Configuration Modals --}}
        {{-- Inverter API Modal --}}
        <div class="modal fade" id="inverterApiModal" tabindex="-1" aria-labelledby="inverterApiModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inverterApiModalLabel">Inverter API Configuration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="solarEdgeForm" action="{{ route('settings.updateSolarEdge') }}" method="POST">
                            @csrf
                            <div class="mb-20">
                                <label for="solar_edge_api_key" class="form-label">API Key</label>
                                <input type="text" name="solar_edge_api_key" id="solar_edge_api_key" class="form-control"
                                       placeholder="Enter SolarEdge API Key" value="{{ old('solar_edge_api_key', $solarEdgeApiKey->value ?? '') }}">
                                @error('solar_edge_api_key')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label for="solar_edge_site_id" class="form-label">Site ID</label>
                                <input type="text" name="solar_edge_site_id" id="solar_edge_site_id" class="form-control"
                                       placeholder="Enter SolarEdge Site ID" value="{{ old('solar_edge_site_id', $solarEdgeSiteId->value ?? '') }}">
                                @error('solar_edge_site_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <button type="button" data-api="solaredge" class="btn btn-secondary text-sm px-12 py-8 radius-4 d-flex align-items-center test-connection-btn">
                                    <i class="ri-refresh-line icon text-xl line-height-1 me-2"></i>
                                    Test Connection
                                </button>
                                <button type="submit" class="btn bg-dark text-light text-sm btn-sm px-8 py-8 radius-4 d-flex align-items-center">
                                    Save Changes
                                </button>
                            </div>
                            <div id="solarEdgeResult" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- HouseCall API Modal --}}
        <div class="modal fade" id="housecallApiModal" tabindex="-1" aria-labelledby="housecallApiModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="housecallApiModalLabel">HouseCall API Configuration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="housecallForm" action="{{ route('settings.updateHousecall') }}" method="POST">
                            @csrf
                            <div class="mb-20">
                                <label for="housecall_api_key" class="form-label">API Key</label>
                                <input type="text" name="housecall_api_key" id="housecall_api_key" class="form-control"
                                       placeholder="Enter Housecall API Key" value="{{ old('housecall_api_key', $housecallApiKey->value ?? '') }}">
                                @error('housecall_api_key')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label for="housecall_client_id" class="form-label">Client ID</label>
                                <input type="text" name="housecall_client_id" id="housecall_client_id" class="form-control"
                                       placeholder="Enter Housecall Client ID" value="{{ old('housecall_client_id', $housecallClientId->value ?? '') }}">
                                @error('housecall_client_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <button type="button" data-api="housecall" class="btn btn-secondary text-sm px-12 py-8 radius-4 d-flex align-items-center test-connection-btn">
                                    <i class="ri-refresh-line icon text-xl line-height-1 me-2"></i>
                                    Test Connection
                                </button>
                                <button type="submit" class="btn bg-dark text-light text-sm btn-sm px-8 py-8 radius-4 d-flex align-items-center">
                                    Save Changes
                                </button>
                            </div>
                            <div id="housecallResult" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Weather API Modal --}}
        <div class="modal fade" id="weatherApiModal" tabindex="-1" aria-labelledby="weatherApiModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="weatherApiModalLabel">Weather API Configuration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="weatherForm" action="{{ route('settings.updateWeather') }}" method="POST">
                            @csrf
                            <div class="mb-20">
                                <label for="weather_api_key" class="form-label">API Key</label>
                                <input type="text" name="weather_api_key" id="weather_api_key" class="form-control"
                                       placeholder="Enter Weather API Key" value="{{ old('weather_api_key', $weatherApiKey->value ?? '') }}">
                                @error('weather_api_key')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-20">
                                <label for="weather_api_base_url" class="form-label">Base URL</label>
                                <input type="text" name="weather_api_base_url" id="weather_api_base_url" class="form-control"
                                       placeholder="e.g., https://api.openweathermap.org/data/2.5/" value="{{ old('weather_api_base_url', $weatherApiBaseUrl->value ?? '') }}">
                                @error('weather_api_base_url')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <button type="button" data-api="weather" class="btn btn-secondary text-sm px-12 py-8 radius-4 d-flex align-items-center test-connection-btn">
                                    <i class="ri-refresh-line icon text-xl line-height-1 me-2"></i>
                                    Test Connection
                                </button>
                                <button type="submit" class="btn bg-dark text-light text-sm btn-sm px-8 py-8 radius-4 d-flex align-items-center">
                                    Save Changes
                                </button>
                            </div>
                            <div id="weatherResult" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifications Settings --}}
        <div class="col-xl-12">
            <div class="d-flex justify-content-between align-items-end mb-3">
                <h5 class="fw-bold text-lg mb-0" style="padding-left: 13px">Notification Preferences</h5>
                <button type="submit" class="btn btn-primary-theme text-sm px-24 py-12 radius-8 d-flex align-items-center fw-semibold">
                    Save Changes
                </button>
            </div>
            <div class="card" style="border-radius: 12px;">
                <div class="card-body p-24">
                    <form action="{{ route('settings.updateNotifications') }}" method="POST">
                        @csrf
                        
                        {{-- Email Notifications --}}
                        <div class="mb-32">
                            <h6 class="mb-20 fw-bold text-lg text-primary-light">Email Notifications</h6>
                            
                            {{-- System Alerts --}}
                            <div class="d-flex justify-content-between align-items-center mb-16">
                                <div>
                                    <h6 class="mb-4 fw-semibold text-md text-primary-light">System Alerts</h6>
                                    <p class="mb-0 text-sm text-primary-semi-light">Receive emails for critical system issues</p>
                                </div>
                                <div class="form-switch switch-primary">
                                    <input class="form-check-input" type="checkbox" role="switch" id="emailSystemAlerts" checked>
                                </div>
                            </div>

                            {{-- Daily Reports --}}
                            <div class="d-flex justify-content-between align-items-center mb-16">
                                <div>
                                    <h6 class="mb-4 fw-semibold text-md text-primary-light">Daily Reports</h6>
                                    <p class="mb-0 text-sm text-primary-semi-light">Daily summary of system performance</p>
                                </div>
                                <div class="form-switch switch-primary">
                                    <input class="form-check-input" type="checkbox" role="switch" id="emailDailyReports" checked>
                                </div>
                            </div>

                            {{-- Service Reminders --}}
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-4 fw-semibold text-md text-primary-light">Service Reminders</h6>
                                    <p class="mb-0 text-sm text-primary-semi-light">Notifications about upcoming service appointments</p>
                                </div>
                                <div class="form-switch switch-primary">
                                    <input class="form-check-input" type="checkbox" role="switch" id="emailServiceReminders" checked>
                                </div>
                            </div>
                        </div>

                        {{-- SMS Notifications --}}
                        <div class="mb-32">
                            <h6 class="mb-20 fw-bold text-lg text-primary-light">SMS Notifications</h6>
                            
                            {{-- Critical Alerts --}}
                            <div class="d-flex justify-content-between align-items-center mb-16">
                                <div>
                                    <h6 class="mb-4 fw-semibold text-md text-primary-light">Critical Alerts</h6>
                                    <p class="mb-0 text-sm text-primary-semi-light">Receive text messages for urgent system issues</p>
                                </div>
                                <div class="form-switch switch-primary">
                                    <input class="form-check-input" type="checkbox" role="switch" id="smsCriticalAlerts" checked>
                                </div>
                            </div>

                            {{-- Service Updates --}}
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-4 fw-semibold text-md text-primary-light">Service Updates</h6>
                                    <p class="mb-0 text-sm text-primary-semi-light">Text notifications about service status changes</p>
                                </div>
                                <div class="form-switch switch-primary">
                                    <input class="form-check-input" type="checkbox" role="switch" id="smsServiceUpdates">
                                </div>
                            </div>
                        </div>

                        {{-- In-App Notifications --}}
                        <div class="mb-32">
                            <h6 class="mb-20 fw-bold text-lg text-primary-light">In-App Notifications</h6>
                            
                            {{-- All System Alerts --}}
                            <div class="d-flex justify-content-between align-items-center mb-16">
                                <div>
                                    <h6 class="mb-4 fw-semibold text-md text-primary-light">All System Alerts</h6>
                                    <p class="mb-0 text-sm text-primary-semi-light">Show all system alerts in the app</p>
                                </div>
                                <div class="form-switch switch-primary">
                                    <input class="form-check-input" type="checkbox" role="switch" id="inAppSystemAlerts" checked>
                                </div>
                            </div>

                            {{-- User Activity --}}
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-4 fw-semibold text-md text-primary-light">User Activity</h6>
                                    <p class="mb-0 text-sm text-primary-semi-light">Notifications about team member actions</p>
                                </div>
                                <div class="form-switch switch-primary">
                                    <input class="form-check-input" type="checkbox" role="switch" id="inAppUserActivity" checked>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form action points to UserController@store, which is good practice. --}}
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label" for="name">Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="email">Email<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="password">Password<span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="password_confirmation">Confirm Password<span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="role">Role<span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn bg-dark text-light text-md px-24 py-4 radius-4 mt-24">
                            Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        new DataTable('#usersDataTable', {
            paging: false,
            searching: false,
            info: false,
            //scrollX: true
        });

        // Universal Test Connection Script for all APIs
        document.querySelectorAll('.test-connection-btn').forEach(button => {
            button.addEventListener('click', function() {
                const apiType = this.dataset.api; // 'solaredge', 'housecall', 'weather'
                let apiKey, idField, resultDiv, testRoute;

                switch (apiType) {
                    case 'solaredge':
                        apiKey = document.getElementById('solar_edge_api_key').value;
                        idField = document.getElementById('solar_edge_site_id').value;
                        resultDiv = document.getElementById('solarEdgeResult');
                        testRoute = '{{ route('settings.testSolarEdgeConnection') }}';
                        break;
                    case 'housecall':
                        apiKey = document.getElementById('housecall_api_key').value;
                        idField = document.getElementById('housecall_client_id').value;
                        resultDiv = document.getElementById('housecallResult');
                        testRoute = '{{ route('settings.testHousecallConnection') }}';
                        break;
                    case 'weather':
                        apiKey = document.getElementById('weather_api_key').value;
                        idField = document.getElementById('weather_api_base_url').value; // Base URL acts as the second ID here
                        resultDiv = document.getElementById('weatherResult');
                        testRoute = '{{ route('settings.testWeatherConnection') }}';
                        break;
                    default:
                        return; // Should not happen
                }

                resultDiv.innerHTML = '<div class="alert alert-info">Testing connection...</div>';
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                let requestBody = {};
                if (apiType === 'solaredge') {
                    requestBody.solar_edge_api_key = apiKey;
                    requestBody.solar_edge_site_id = idField;
                } else if (apiType === 'housecall') {
                    requestBody.housecall_api_key = apiKey;
                    requestBody.housecall_client_id = idField;
                } else if (apiType === 'weather') {
                    requestBody.weather_api_key = apiKey;
                    requestBody.weather_api_base_url = idField;
                }


                fetch(testRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(requestBody)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            resultDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                        } else {
                            resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        resultDiv.innerHTML = '<div class="alert alert-danger">An error occurred while testing connection.</div>';
                    });
            });
        });
    </script>
@endpush
