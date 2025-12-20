<?php
/**
 * Test script to verify date query
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "TEST DATE QUERY FOR ATTACHMENTS\n";
echo "========================================\n\n";

$date = Carbon::now()->subDays(7)->startOfDay();

echo "Date object: " . $date->format('Y-m-d H:i:s') . "\n";
echo "Date string: " . $date->toDateTimeString() . "\n";
echo "Date for query: " . $date->format('Y-m-d H:i:s') . "\n\n";

// Test query
$count = DB::table('loan_attachment as a')
    ->where('a.status', '=', 1)
    ->where('a.created_at', '>=', $date->toDateTimeString())
    ->count();

echo "Total attachments from last 7 days: " . $count . "\n\n";

// Get sample dates
$sampleDates = DB::table('loan_attachment as a')
    ->where('a.status', '=', 1)
    ->where('a.created_at', '>=', $date->toDateTimeString())
    ->orderBy('a.created_at', 'DESC')
    ->limit(20)
    ->pluck('a.created_at')
    ->map(function($date) {
        return Carbon::parse($date)->format('Y-m-d');
    })
    ->unique()
    ->values();

echo "Unique dates found (last 20 records):\n";
foreach ($sampleDates as $dateStr) {
    echo "  - " . $dateStr . "\n";
}

echo "\n";
echo "Expected: Should show dates from " . Carbon::now()->subDays(7)->format('Y-m-d') . " onwards\n";
echo "Today is: " . Carbon::now()->format('Y-m-d') . "\n";

