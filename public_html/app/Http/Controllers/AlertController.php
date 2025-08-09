<?php

    namespace App\Http\Controllers;

    use App\Models\Alert;
    use App\Models\System;
    use Illuminate\Http\Request;
    use Carbon\Carbon;

    class AlertController extends Controller
    {
        public function index(Request $request)
        {
            $query = Alert::with('system');

            // Apply filters for the main alert list
            if ($request->filled('system_keyword')) {
                $keyword = $request->input('system_keyword');
                $query->whereHas('system', function ($q) use ($keyword) {
                    $q->where('system_id', 'like', '%' . $keyword . '%')
                        ->orWhere('customer_name', 'like', '%' . $keyword . '%');
                });
            }

            if ($request->filled('severity')) {
                $query->where('severity', $request->input('severity'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->input('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->input('date_to'));
            }

            $alerts = $query->orderBy('created_at', 'desc')->get();

            // Calculate summary counts for the cards
            $totalAlerts = Alert::count();
            $criticalAlerts = Alert::where('severity', 'critical')->count();
            $warningAlerts = Alert::where('severity', 'warning')->count();
            $infoAlerts = Alert::where('severity', 'info')->count();
            $resolvedAlerts = Alert::where('status', 'resolved')->count();
            $pendingAlerts = Alert::where('status', 'pending')->count();

            // Data for "Alert Frequency Chart" (Last 30 Days for multiple series)
            $alertFrequencyLabels = [];
            $criticalAlertData = [];
            $warningAlertData = [];
            $infoAlertData = [];
            $resolvedAlertData = []; // New series data for resolved alerts

            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays(29); // Last 30 days including today

            $alertsForChart = Alert::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->selectRaw('DATE(created_at) as date, severity, status, count(*) as count') // Select status as well
                ->groupBy('date', 'severity', 'status') // Group by status too
                ->orderBy('date', 'asc')
                ->get();

            $dateRange = new \DatePeriod(
                $startDate->copy()->startOfDay(),
                new \DateInterval('P1D'),
                $endDate->copy()->endOfDay()
            );

            // Prepare a map for easy lookup: ['date' => ['severity' => count], 'date' => ['resolved' => count]]
            $dailyCounts = [];
            foreach ($alertsForChart as $item) {
                // Aggregate counts by severity
                $dailyCounts[$item->date][$item->severity] = ($dailyCounts[$item->date][$item->severity] ?? 0) + $item->count;
                // Aggregate counts for resolved status
                if ($item->status === 'resolved') {
                    $dailyCounts[$item->date]['resolved'] = ($dailyCounts[$item->date]['resolved'] ?? 0) + $item->count;
                }
            }

            foreach ($dateRange as $date) {
                $dateString = $date->format('Y-m-d');
                $alertFrequencyLabels[] = $date->format('M d'); // Format for display on X-axis (e.g., Jul 04)

                $criticalAlertData[] = $dailyCounts[$dateString]['critical'] ?? 0;
                $warningAlertData[] = $dailyCounts[$dateString]['warning'] ?? 0;
                $infoAlertData[] = $dailyCounts[$dateString]['info'] ?? 0;
                $resolvedAlertData[] = $dailyCounts[$dateString]['resolved'] ?? 0; // Populate resolved data
            }

            // Get all systems for the system filter dropdown (if needed, or just allow keyword)
            $systems = System::select('id', 'system_id', 'customer_name')->get();


            return view('backend.alert.index', compact(
                'alerts',
                'totalAlerts',
                'criticalAlerts',
                'warningAlerts',
                'infoAlerts',
                'resolvedAlerts',
                'pendingAlerts',
                'systems',
                'criticalAlertData',      // Data for the 'Critical Alerts' series
                'warningAlertData',       // Data for the 'Warning Alerts' series
                'infoAlertData',          // Data for the 'Info Alerts' series
                'resolvedAlertData',      // Data for the 'Resolved Alerts' series
                'alertFrequencyLabels'    // Labels for the chart x-axis
            ));
        }

        public function show(Alert $alert)
        {
            return view('backend.alert.show', compact('alert'));
        }

        public function create()
        {
            $systems = System::all();
            return view('backend.alert.create', compact('systems'));
        }

        public function store(Request $request)
        {
            $request->validate([
                'system_id' => 'required|exists:systems,id',
                'severity' => 'required|in:critical,warning,info',
                'alert_type' => 'required|string|max:255',
                'message' => 'required|string',
                'status' => 'required|in:pending,scheduled,in_progress,resolved',
                'resolved_at' => 'nullable|date',
            ]);

            Alert::create($request->all());

            return redirect()->route('alert.index')->with('success', 'Alert created successfully.');
        }

        public function edit(Alert $alert)
        {
            $systems = System::all();
            return view('backend.alert.edit', compact('alert', 'systems'));
        }

        public function update(Request $request, Alert $alert)
        {
            $request->validate([
                'system_id' => 'required|exists:systems,id',
                'severity' => 'required|in:critical,warning,info',
                'alert_type' => 'required|string|max:255',
                'message' => 'required|string',
                'status' => 'required|in:pending,scheduled,in_progress,resolved',
                'resolved_at' => 'nullable|date',
            ]);

            $alert->update($request->all());

            return redirect()->route('alert.index')->with('success', 'Alert updated successfully.');
        }

        public function destroy(Alert $alert)
        {
            $alert->delete();
            return redirect()->route('alert.index')->with('success', 'Alert deleted successfully.');
        }
    }
