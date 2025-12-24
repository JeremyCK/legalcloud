<?php
/**
 * Test script to verify attachment date calculation
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Carbon\Carbon;

echo "========================================\n";
echo "ATTACHMENT DATE VERIFICATION\n";
echo "========================================\n\n";

$now = Carbon::now();
$date7Days = Carbon::now()->subDays(7);
$date4Days = Carbon::now()->subDays(4); // Old value

echo "Current Date/Time: " . $now->format('Y-m-d H:i:s') . "\n";
echo "7 Days Ago: " . $date7Days->format('Y-m-d H:i:s') . "\n";
echo "4 Days Ago (old): " . $date4Days->format('Y-m-d H:i:s') . "\n\n";

echo "Date Range for 7 Days:\n";
echo "  From: " . $date7Days->format('Y-m-d') . " 00:00:00\n";
echo "  To:   " . $now->format('Y-m-d') . " 23:59:59\n\n";

echo "This means attachments should show from:\n";
echo "  " . $date7Days->format('Y-m-d') . " onwards\n";
echo "  (Today is " . $now->format('Y-m-d') . ")\n\n";

// Test database query
use Illuminate\Support\Facades\DB;

$testDate = Carbon::now()->subDays(7);
$count = DB::table('loan_attachment')
    ->where('status', '=', 1)
    ->where('created_at', '>=', $testDate)
    ->count();

echo "Total attachments from last 7 days: " . $count . "\n\n";

// Show sample dates
$sampleDates = DB::table('loan_attachment')
    ->where('status', '=', 1)
    ->where('created_at', '>=', $testDate)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->pluck('created_at')
    ->map(function($date) {
        return Carbon::parse($date)->format('Y-m-d');
    })
    ->unique();

echo "Sample dates found (last 10, unique):\n";
foreach ($sampleDates as $date) {
    echo "  - " . $date . "\n";
}



