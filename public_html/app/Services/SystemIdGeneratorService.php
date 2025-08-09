<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SystemIdGeneratorService
{
    /**
     * Generate a new system ID in the format BC-YYYY-NNNN
     *
     * @param string $table The table name to check for existing IDs
     * @param string $prefix The prefix to use (default: 'BC')
     * @return string
     */
    public static function generate(string $table, string $prefix = 'BC'): string
    {
        $currentYear = date('Y');

        // Get the last record for the current year
        $lastRecord = DB::table($table)
            ->where('system_id', 'like', "{$prefix}-{$currentYear}-%")
            ->orderBy('system_id', 'desc')
            ->first();

        // Determine the next number in sequence
        $nextNumber = 1;
        if ($lastRecord) {
            $parts = explode('-', $lastRecord->system_id);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $currentYear, $nextNumber);
    }
}
