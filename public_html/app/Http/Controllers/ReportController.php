<?php

namespace App\Http\Controllers;

use App\Models\ProductionData;
use App\Models\ServiceSchedule;
use App\Models\System;
use App\Models\Alert; // Import the Alert model
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display the reports index page.
     */
    public function index()
    {
        $systems = System::all(); // Fetch all systems for the dropdown
        return view('backend.reports.index', compact('systems'));
    }

    /**
     * Get production intensity data for heatmap chart.
     * Fetches hourly power_current for each system for a given date.
     * You can expand this to include date range selection via request.
     */
    public function getProductionIntensityData(Request $request)
    {
        // Define the date for which to fetch data (default to today if not provided)
        $date = $request->input('date', Carbon::today()->toDateString());
        $targetDate = Carbon::parse($date);

        $systems = System::all(); // Get all systems
        $chartSeries = [];
        $timeLabels = [];

        // Generate hourly labels for the X-axis (00:00 to 23:00)
        for ($hour = 0; $hour < 24; $hour++) {
            $timeLabels[] = sprintf('%02d:00', $hour); // Format as "00:00", "01:00", etc.
        }

        foreach ($systems as $system) {
            $systemData = [
                'name' => $system->system_id, // Or $system->name if you have a name field
                'data' => []
            ];

            // Fetch production data for the current system for the target date
            // Group by hour and get the average power_current for that hour
            $hourlyData = ProductionData::where('system_id', $system->id)
                ->whereDate('date', $targetDate)
                ->selectRaw('HOUR(date) as hour, AVG(power_current) as avg_power')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get()
                ->keyBy('hour'); // Key by hour for easy lookup

            // Populate data for each hour for the current system
            for ($hour = 0; $hour < 24; $hour++) {
                $powerValue = 0; // Default to 0 if no data for that hour
                if ($hourlyData->has($hour)) {
                    $powerValue = round($hourlyData[$hour]->avg_power, 2); // Round to 2 decimal places
                }
                $systemData['data'][] = [
                    'x' => sprintf('%02d:00', $hour),
                    'y' => $powerValue
                ];
            }
            $chartSeries[] = $systemData;
        }

        return response()->json($chartSeries);
    }


    /**
     * Handle the report export request.
     */
    public function exportReport(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,pdf,xlsx',
        ]);

        $systemId = $request->input('system_id');
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        $format = $request->input('format');

        $system = System::findOrFail($systemId);

        // Fetch production data for the selected system and date range
        $productionData = ProductionData::all();

        $filename = 'production_report';

        switch ($format) {
            case 'csv':
                return $this->exportCsv($productionData, $filename);
            case 'pdf':
                return $this->exportPdf($productionData, $filename); // Requires barryvdh/laravel-dompdf
            case 'xlsx':
                return $this->exportXlsx($productionData, $filename); // Requires maatwebsite/excel
            default:
                return redirect()->back()->with('error', 'Invalid export format.');
        }
    }

    /**
     * Export data as CSV.
     * @param \Illuminate\Support\Collection $data
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function exportCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Date',
                'System ID',
                'Energy Today (kWh)',
                'Energy Yesterday (kWh)',
                'Current Power (kW)',
                'Efficiency (%)'
            ]);

            // Add data rows
            foreach ($data as $row) {
                fputcsv($file, [
                    Carbon::parse($row->date)->format('Y-m-d H:i:s'),
                    $row->system->system_id, // Assuming 'system' relationship is defined in ProductionData model
                    $row->energy_today,
                    $row->energy_yesterday,
                    $row->power_current,
                    $row->efficiency
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Export data as PDF. (Requires barryvdh/laravel-dompdf)
     * @param \Illuminate\Support\Collection $data
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    protected function exportPdf($data, $filename)
    {
        // Make sure you have installed: composer require barryvdh/laravel-dompdf
        // And configured as per their documentation.
        // For a basic PDF, you might create a Blade view to structure the report.

        Log::info("Attempting to export PDF, data count: " . $data->count());
        try {
            // Example of rendering a simple view to PDF
            $pdf = PDF::loadView('reports.pdf_template', compact('data', 'filename'));
            return $pdf->download($filename . '.pdf');
        } catch (\Exception $e) {
            Log::error("PDF generation failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'PDF export failed: ' . $e->getMessage());
        }
        // return redirect()->back()->with('info', 'PDF export is not fully implemented yet. Please install barryvdh/laravel-dompdf and add your PDF template.');
    }

    /**
     * Export data as XLSX. (Requires maatwebsite/excel)
     * @param \Illuminate\Support\Collection $data
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    protected function exportXlsx($data, $filename)
    {
        // Make sure you have installed: composer require maatwebsite/excel
        // And created an export class like: php artisan make:export ProductionReportExport --model=ProductionData
        // Then modify that export class to format your data.

        Log::info("Attempting to export XLSX, data count: " . $data->count());
        try {
            // Example:
            // return Excel::download(new ProductionReportExport($data), $filename . '.xlsx');
            return redirect()->back()->with('info', 'XLSX export is not fully implemented yet. Please install maatwebsite/excel and create an export class.');
        } catch (\Exception $e) {
            Log::error("XLSX generation failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'XLSX export failed: ' . $e->getMessage());
        }
        // return redirect()->back()->with('info', 'XLSX export is not fully implemented yet. Please install maatwebsite/excel and add your export logic.');
    }

    // ... (Your existing downloadServiceReport and getAlertFrequencyData methods) ...

    /**
     * Download service reports for a selected system as a CSV.
     */
    public function downloadServiceReport(Request $request)
    {
        // ... (Your existing downloadServiceReport method) ...
        $request->validate([
            'system_id' => 'required|exists:systems,id',
        ]);

        $systemId = $request->input('system_id');
        $system = System::find($systemId);
        $serviceSchedules = ServiceSchedule::where('system_id', $systemId)
            ->orderBy('scheduled_date', 'asc')
            ->get();

        $filename = 'service_report_system_' . $system->system_id . '_' . Carbon::now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '\"',
        ];

        $callback = function() use ($serviceSchedules) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['Scheduled Date', 'Scheduled Time', 'Service Type', 'Notes', 'Status', 'Assigned Technician']); // CSV Header
            foreach ($serviceSchedules as $schedule) {
                fputcsv($f, [
                    Carbon::parse($schedule->scheduled_date)->format('Y-m-d'),
                    $schedule->scheduled_time,
                    $schedule->service_type,
                    $schedule->notes,
                    $schedule->status,
                    $schedule->assigned_technician
                ]);
            }
            fclose($f);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

}
