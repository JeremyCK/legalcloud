<?php
/**
 * Diagnostic script to check why edit icons aren't showing
 * Run on SERVER: php diagnose_edit_icons.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

echo "=== Edit Icons Diagnostic ===\n\n";

// 1. Check if view file exists
$viewPath = __DIR__.'/resources/views/dashboard/transfer-fee-v3/edit.blade.php';
echo "1. View File Check:\n";
if (file_exists($viewPath)) {
    echo "   ✅ File exists: $viewPath\n";
    echo "   📅 Last modified: " . date('Y-m-d H:i:s', filemtime($viewPath)) . "\n";
    echo "   📏 File size: " . filesize($viewPath) . " bytes\n";
    
    // Check content
    $content = file_get_contents($viewPath);
    $hasEditPfee = strpos($content, 'edit-pfee') !== false;
    $hasEditSst = strpos($content, 'edit-sst') !== false;
    $hasDataBillId = strpos($content, 'data-bill-id') !== false;
    
    echo "   " . ($hasEditPfee ? "✅" : "❌") . " Contains 'edit-pfee'\n";
    echo "   " . ($hasEditSst ? "✅" : "❌") . " Contains 'edit-sst'\n";
    echo "   " . ($hasDataBillId ? "✅" : "❌") . " Contains 'data-bill-id'\n";
    
    // Count occurrences
    $editPfeeCount = substr_count($content, 'edit-pfee');
    echo "   📊 'edit-pfee' appears $editPfeeCount times\n";
    
    // Check if inside @foreach
    if (preg_match('/@foreach\s*\(\s*\$TransferFeeDetails[^}]*edit-pfee/s', $content)) {
        echo "   ✅ 'edit-pfee' is inside @foreach loop\n";
    } else {
        echo "   ⚠️  'edit-pfee' might not be inside @foreach loop\n";
    }
    
} else {
    echo "   ❌ File NOT found: $viewPath\n";
    exit(1);
}

echo "\n2. Route Check:\n";
$routePath = __DIR__.'/routes/web.php';
if (file_exists($routePath)) {
    $routeContent = file_get_contents($routePath);
    $hasRoute = strpos($routeContent, 'updateAmountsV3') !== false;
    echo "   " . ($hasRoute ? "✅" : "❌") . " Route 'updateAmountsV3' found\n";
} else {
    echo "   ❌ Route file not found\n";
}

echo "\n3. Controller Check:\n";
$controllerPath = __DIR__.'/app/Http/Controllers/TransferFeeV3Controller.php';
if (file_exists($controllerPath)) {
    $controllerContent = file_get_contents($controllerPath);
    $hasMethod = strpos($controllerContent, 'function updateAmountsV3') !== false;
    $hasEditMethod = strpos($controllerContent, 'function transferFeeEditV3') !== false;
    echo "   " . ($hasMethod ? "✅" : "❌") . " Method 'updateAmountsV3' exists\n";
    echo "   " . ($hasEditMethod ? "✅" : "❌") . " Method 'transferFeeEditV3' exists\n";
    
    // Check if TransferFeeDetails is passed
    if (preg_match('/TransferFeeDetails.*=>.*\$TransferFeeDetails/', $controllerContent)) {
        echo "   ✅ TransferFeeDetails is passed to view\n";
    } else {
        echo "   ⚠️  TransferFeeDetails might not be passed to view\n";
    }
} else {
    echo "   ❌ Controller file not found\n";
}

echo "\n4. Blade Syntax Check:\n";
// Check for common syntax errors
$content = file_get_contents($viewPath);
$openIf = substr_count($content, '@if');
$closeIf = substr_count($content, '@endif');
$openForeach = substr_count($content, '@foreach');
$closeForeach = substr_count($content, '@endforeach');

echo "   @if: $openIf open, $closeIf close " . ($openIf == $closeIf ? "✅" : "❌ MISMATCH") . "\n";
echo "   @foreach: $openForeach open, $closeForeach close " . ($openForeach == $closeForeach ? "✅" : "❌ MISMATCH") . "\n";

// Check for specific edit icon section
if (preg_match('/@if\(in_array\(auth\(\)->user\(\)->menuroles.*edit-pfee/s', $content)) {
    echo "   ✅ Edit icon is inside permission check\n";
} else {
    echo "   ⚠️  Edit icon might not be inside permission check\n";
}

echo "\n5. File Comparison:\n";
echo "   Compare file modification dates:\n";
echo "   - View file: " . date('Y-m-d H:i:s', filemtime($viewPath)) . "\n";
if (file_exists($controllerPath)) {
    echo "   - Controller: " . date('Y-m-d H:i:s', filemtime($controllerPath)) . "\n";
}

echo "\n=== Recommendations ===\n";
if (!$hasEditPfee) {
    echo "❌ View file does NOT contain 'edit-pfee' - file needs to be updated!\n";
} else {
    echo "✅ View file contains edit icons\n";
    echo "   If icons still don't show:\n";
    echo "   1. Clear view cache: php artisan view:clear\n";
    echo "   2. Clear all caches: php artisan cache:clear\n";
    echo "   3. Check if TransferFeeDetails collection has data\n";
    echo "   4. Verify user permissions in database\n";
    echo "   5. Check browser console for JavaScript errors\n";
}

echo "\n";

