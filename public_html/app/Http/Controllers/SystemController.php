<?php

    namespace App\Http\Controllers;

    use App\Models\System;
    use App\Services\SystemIdGeneratorService; // Assuming this service exists
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log; // Import Log facade

    class SystemController extends Controller
    {
    public function index()
    {
        $systems = System::with(['latestProductionData', 'todayProductionData'])->get();
        return view('backend.system.index', compact('systems'));
    }

        public function show($id)
        {
            $system = System::findOrFail($id); // Use findOrFail for cleaner error handling

            // 1. Production Data for Chart (e.g., last 30 days)
            $productionLabels = [];
            $powerCurrentData = []; // Data for the chart line

            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays(29); // Last 30 days including today

            $productionRecords = $system->productionData()
                ->whereBetween('date', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->orderBy('date', 'asc')
                ->get();

            $dateRange = new \DatePeriod(
                $startDate->copy()->startOfDay(),
                new \DateInterval('P1D'),
                $endDate->copy()->endOfDay()
            );

            $dailyProduction = [];
            foreach ($productionRecords as $record) {
                // Group by date, taking the last (or average/sum depending on chart intent) power_current for that day
                // For a daily summary, let's take the latest power_current recorded on that day.
                // If you want cumulative daily energy, you'd use 'energy_today' and sum it up.
                // For a line chart showing power output fluctuations, 'power_current' is more suitable.
                $dailyProduction[$record->date->format('Y-m-d')] = $record->power_current;
            }

            foreach ($dateRange as $date) {
                $dateString = $date->format('Y-m-d');
                $productionLabels[] = $date->format('M d'); // Format for display on X-axis (e.g., Jul 04)
                $powerCurrentData[] = $dailyProduction[$dateString] ?? 0; // Default to 0 if no data for the day
            }

            // 2. Alert History for this System
            $alerts = $system->alerts()->orderBy('created_at', 'desc')->limit(5)->get(); // Get latest 5 alerts

            // 3. Service Schedules for this System
            $serviceSchedules = $system->serviceSchedules()->orderBy('scheduled_date', 'desc')->limit(5)->get(); // Get latest 5 schedules

            return view('backend.system.show', compact(
                'system',
                'productionLabels',
                'powerCurrentData',
                'alerts',
                'serviceSchedules'
            ));
        }

        public function create()
        {
            return view('backend.system.create');
        }

        public function store(Request $request)
        {
            $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_type' => 'required|in:Residential,Commercial',
                'manufacturer' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'capacity' => 'nullable|numeric',
                'install_date' => 'nullable|date',
                'status' => 'required|in:Active,Warning,Critical,Offline',
                'system_id' => 'nullable|string|unique:systems,system_id',
            ]);

            try {
                $system = new System();
                $system->customer_name = $request->customer_name;
                $system->customer_type = $request->customer_type;
                $system->manufacturer = $request->manufacturer;
                $system->status = $request->status;
                $system->location = $request->location;
                $system->capacity = $request->capacity;
                $system->install_date = $request->install_date;

                if (request()->filled('system_id')) {
                    $system->system_id = $request->system_id;
                } else {
                    $system->system_id = SystemIdGeneratorService::generate('systems');
                }

                $system->save();

                return redirect()->route('system.index')->with(['msg' => 'System Created Successfully', 'type' => 'success']);

            } catch (\Throwable $e) {
                Log::error('System creation failed: ' . $e->getMessage());
                return redirect()->back()->withInput()->with(['msg' => 'Something went wrong, please check and try again: ' . $e->getMessage(), 'type' => 'error']);
            }
        }

        public function edit(int $id)
        {
            $system = System::find($id);
            if (!$system) {
                return redirect()->route('system.index')->with(['msg' => 'System not found', 'type' => 'error']);
            }
            return view('backend.system.edit', compact('system'));
        }

        public function update(Request $request, int $id)
        {
            $system = System::find($id);
            if (!$system) {
                return redirect()->route('system.index')->with(['msg' => 'System not found', 'type' => 'error']);
            }

            $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_type' => 'required|in:Residential,Commercial',
                'manufacturer' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'capacity' => 'nullable|numeric',
                'install_date' => 'nullable|date',
                'status' => 'required|in:Active,Warning,Critical,Offline',
                'system_id' => 'required|string|unique:systems,system_id,' . $system->id, // system_id must be unique, except for the current system
            ]);

            try {
                $system->customer_name = $request->customer_name;
                $system->customer_type = $request->customer_type;
                $system->manufacturer = $request->manufacturer;
                $system->status = $request->status;
                $system->location = $request->location;
                $system->capacity = $request->capacity;
                $system->install_date = $request->install_date;
                $system->system_id = $request->system_id; // Update system_id if changed

                $system->save();

                return redirect()->route('system.index')->with(['msg' => 'System Updated Successfully', 'type' => 'success']);

            } catch (\Throwable $e) {
                Log::error('System update failed: ' . $e->getMessage());
                return redirect()->back()->withInput()->with(['msg' => 'Something went wrong, please check and try again: ' . $e->getMessage(), 'type' => 'error']);
            }
        }

        public function destroy(int $id)
        {
            $system = System::find($id);
            if (!$system) {
                return redirect()->route('system.index')->with(['msg' => 'System not found', 'type' => 'error']);
            }
            try {
                $system->delete();
                return redirect()->route('system.index')->with(['msg' => 'System Deleted Successfully', 'type' => 'success']);
            } catch (\Throwable $e) {
                Log::error('System deletion failed: ' . $e->getMessage());
                return redirect()->back()->with(['msg' => 'Something went wrong during deletion: ' . $e->getMessage(), 'type' => 'error']);
            }
        }
    }
