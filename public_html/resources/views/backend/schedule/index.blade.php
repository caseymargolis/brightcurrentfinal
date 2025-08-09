@extends('backend.layouts.master')
@section('title') {{'Service Schedules'}} @endsection

@section('breadcrumb') Pages / Scheduling @endsection
@section('page-title') Service Schedules @endsection

@section('content')
    @include('backend.partials.alert')
    <div class="row gy-4">
        <div class="col-xl-8">
            <div class="card basic-data-table">
                <div class="card-body" style="padding: 0 !important;">
                    <a href="{{ route('service-schedules.create') }}" class="text-primary-light text-xxl fw-bold" style="padding: 22px 28px;">
                        Schedule New Service
                    </a>
                    <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                        <thead>
                        <tr>
                            <th scope="col">System ID</th>
                            <th scope="col" style="width: 200px">Scheduled Date</th>
                            <th scope="col" style="width: 200px">Scheduled Time</th>
                            <th scope="col">Service Type</th>
                            <th scope="col">Status</th>
                            <th scope="col" style="width: 220px">Assigned Technician</th>
                            <th scope="col" style="width: 150px">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($serviceSchedules as $schedule)
                            <tr>
                                <td>
                                    {{-- Link to system show page --}}
                                    <a href="{{ route('system.show', $schedule->system->id) }}">
                                        {{ $schedule->system->system_id }}
                                    </a>
                                </td>
                                <td>{{ $schedule->scheduled_date->format('M d, Y') }}</td>
                                <td>{{ $schedule->scheduled_time }}</td>
                                <td>{{ $schedule->service_type }}</td>
                                <td>
                            <span class="text-white px-16 py-4 radius-12 fw-bold text-sm @if($schedule->status == 'completed') bg-success @elseif($schedule->status == 'cancelled') bg-danger bg-success @elseif($schedule->status == 'scheduled') bg-info @else bg-warning @endif">
                                {{ ucfirst($schedule->status) }}
                            </span>
                                </td>
                                <td>{{ $schedule->assigned_technician ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <a href="{{ route('service-schedules.edit', $schedule->id) }}" class="strong text-xxl text-primary-light">
                                            <iconify-icon icon="lucide:edit"></iconify-icon>
                                        </a>
                                        <a href="javascript:void(0)" class="strong text-xxl text-primary-light" data-bs-toggle="modal" data-bs-target="#deleteScheduleModal{{ $schedule->id }}">
                                            <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="deleteScheduleModal{{ $schedule->id }}" tabindex="-1" aria-labelledby="deleteScheduleModalLabel{{ $schedule->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteScheduleModalLabel{{ $schedule->id }}">{{ __('Delete Service Schedule') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <h5>Are you sure you want to delete this schedule?</h5>
                                        </div>
                                        <div class="modal-footer">
                                            <form class="d-inline-block" action="{{ route('service-schedules.destroy', $schedule->id) }}" method="POST">
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
            </div>
        </div>
        {{-- Right Column: Upcoming Jobs --}}
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header" style="border-bottom: 0; padding-bottom: 40px;">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="fw-bold text-lg mb-0">Upcoming Jobs</h6>
                    </div>
                </div>
                <div class="card-body p-8">
                    <div class="upcoming-jobs-list">
                        @php
                            $groupedSchedules = $upcomingSchedules->groupBy(function($schedule) {
                                return $schedule->scheduled_date->format('Y-m-d');
                            });
                        @endphp
                        
                        @forelse ($groupedSchedules as $date => $schedules)
                            <div class="date-group mb-4">
                                <div class="date-header">
                                    <h6 class="date-title">
                                        @php
                                            $dateCarbon = \Carbon\Carbon::parse($date);
                                            $now = \Carbon\Carbon::now();
                                            
                                            if ($dateCarbon->isToday()) {
                                                $dayLabel = 'Today';
                                            } elseif ($dateCarbon->isTomorrow()) {
                                                $dayLabel = 'Tomorrow';
                                            } elseif ($dateCarbon->isCurrentWeek()) {
                                                $dayLabel = $dateCarbon->format('l'); // Day name like Monday, Tuesday
                                            } elseif ($dateCarbon->isNextWeek()) {
                                                $dayLabel = 'Next ' . $dateCarbon->format('l');
                                            } elseif ($dateCarbon->isCurrentMonth()) {
                                                $dayLabel = $dateCarbon->format('l');
                                            } elseif ($dateCarbon->isNextMonth()) {
                                                $dayLabel = 'Next Month';
                                            } else {
                                                $dayLabel = $dateCarbon->format('l');
                                            }
                                        @endphp
                                        {{ $dayLabel }} - {{ $dateCarbon->format('F j, Y') }}
                                    </h6>
                                </div>
                                
                                @foreach ($schedules as $upcoming)
                                <div class="job-card {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="job-info">
                                        <div class="job-header">
                                            <h6 class="customer-name">{{ $upcoming->system->customer_name }}</h6>
                                            <span class="job-time">{{ \Carbon\Carbon::parse($upcoming->scheduled_time)->format('g:i A') }}</span>
                                        </div>
                                        <div class="job-details">
                                            <p class="system-id">{{ $upcoming->system->system_id }}</p>
                                            <p class="service-type">{{ $upcoming->service_type }}</p>
                                            @if($upcoming->assigned_technician)
                                                <p class="technician">{{ $upcoming->assigned_technician }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="empty-state">
                                <span class="strong text-xxl text-primary-light">
                                    <iconify-icon icon="ph:calendar-x"></iconify-icon>
                                </span>
                                <p class="empty-text">No upcoming jobs scheduled</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // Initialize DataTable
        let table = new DataTable('#dataTable', {
            scrollX: true
        });
    </script>
@endpush
