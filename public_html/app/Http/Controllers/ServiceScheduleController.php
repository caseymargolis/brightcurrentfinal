<?php

    namespace App\Http\Controllers;

    use App\Models\ServiceSchedule;
    use App\Models\System;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Carbon\Carbon;

    class ServiceScheduleController extends Controller
    {
        public function index()
        {
            $serviceSchedules = ServiceSchedule::with('system')->orderBy('scheduled_date', 'desc')->get();

            // Fetch upcoming service schedules for the "Upcoming Jobs" section
            $upcomingSchedules = ServiceSchedule::with('system')
                ->whereDate('scheduled_date', '>=', Carbon::today()) // Scheduled for today or future
                ->whereIn('status', ['scheduled', 'in_progress']) // Only show active upcoming ones
                ->orderBy('scheduled_date', 'asc') // Order by earliest first
                ->limit(5) // Limit to display a few upcoming jobs, adjust as needed
                ->get();

            return view('backend.schedule.index', compact('serviceSchedules', 'upcomingSchedules'));
        }

        public function create()
        {
            $systems = System::all();
            $technicians = User::role('technician')->get();

            return view('backend.schedule.create', compact('systems', 'technicians'));
        }

        public function store(Request $request)
        {
            $request->validate([
                'system_id' => 'required|exists:systems,id',
                'scheduled_date' => 'required|date',
                'scheduled_time' => 'required|string|max:255',
                'service_type' => 'required|string|max:255',
                'notes' => 'nullable|string',
                'status' => 'required|in:scheduled,in_progress,completed,cancelled',
                'assigned_technician' => 'nullable|string|max:255', // Validation remains string as per your migration
            ]);

            ServiceSchedule::create($request->all());

            return redirect()->route('service-schedules.index')->with(['msg' => 'Service Schedule Created Successfully', 'type' => 'success']);
        }

        // ... (show method remains the same or removed if not needed)

        public function edit(ServiceSchedule $serviceSchedule)
        {
            $systems = System::all();
            // Fetch all users who have the 'technician' role
            $technicians = User::role('technician')->get();
            return view('backend.schedule.edit', compact('serviceSchedule', 'systems', 'technicians'));
        }

        public function update(Request $request, ServiceSchedule $serviceSchedule)
        {
            $request->validate([
                'system_id' => 'required|exists:systems,id',
                'scheduled_date' => 'required|date',
                'scheduled_time' => 'required|string|max:255',
                'service_type' => 'required|string|max:255',
                'notes' => 'nullable|string',
                'status' => 'required|in:scheduled,in_progress,completed,cancelled',
                'assigned_technician' => 'nullable|string|max:255', // Validation remains string as per your migration
            ]);

            $serviceSchedule->update($request->all());

            return redirect()->route('service-schedules.index')->with(['msg' => 'Service Schedule Updated Successfully', 'type' => 'success']);
        }

        public function destroy(ServiceSchedule $serviceSchedule)
        {
            $serviceSchedule->delete();
            return redirect()->route('service-schedules.index')->with(['msg' => 'Service Schedule Deleted Successfully', 'type' => 'success']);
        }
    }
