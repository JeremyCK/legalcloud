<?php
/**
 * Quick verification script to check if edit icons are properly configured
 * Run: php verify_edit_icons.php
 */

$viewPath = __DIR__.'/resources/views/dashboard/transfer-fee-v3/edit.blade.php';

if (!file_exists($viewPath)) {
    echo "❌ ERROR: View file not found at: $viewPath\n";
    exit(1);
}

echo "Checking view file: $viewPath\n\n";

$content = file_get_contents($viewPath);

$checks = [
    'Has edit-pfee icon' => [
        'pattern' => 'edit-pfee',
        'required' => true,
        'check' => strpos($content, 'edit-pfee') !== false
    ],
    'Has edit-sst icon' => [
        'pattern' => 'edit-sst',
        'required' => true,
        'check' => strpos($content, 'edit-sst') !== false
    ],
    'Has edit-reimb icon' => [
        'pattern' => 'edit-reimb',
        'required' => true,
        'check' => strpos($content, 'edit-reimb') !== false && strpos($content, 'edit-reimb-sst') === false ? false : strpos($content, 'class="fa fa-pencil edit-reimb') !== false
    ],
    'Has edit-reimb-sst icon' => [
        'pattern' => 'edit-reimb-sst',
        'required' => true,
        'check' => strpos($content, 'edit-reimb-sst') !== false
    ],
    'Has data-bill-id on pfee' => [
        'pattern' => 'edit-pfee.*data-bill-id',
        'required' => true,
        'check' => preg_match('/edit-pfee[^>]*data-bill-id/', $content) === 1
    ],
    'Has data-bill-id on sst' => [
        'pattern' => 'edit-sst.*data-bill-id',
        'required' => true,
        'check' => preg_match('/edit-sst[^>]*data-bill-id/', $content) === 1
    ],
    'Has data-bill-id on reimb' => [
        'pattern' => 'edit-reimb.*data-bill-id',
        'required' => true,
        'check' => preg_match('/class="fa fa-pencil edit-reimb[^>]*data-bill-id/', $content) === 1
    ],
    'Has data-bill-id on reimb-sst' => [
        'pattern' => 'edit-reimb-sst.*data-bill-id',
        'required' => true,
        'check' => preg_match('/edit-reimb-sst[^>]*data-bill-id/', $content) === 1
    ],
    'Has permission check' => [
        'pattern' => 'menuroles.*admin.*maker.*account.*is_recon',
        'required' => true,
        'check' => strpos($content, "in_array(auth()->user()->menuroles, ['admin', 'maker', 'account'])") !== false
    ],
    'Has JavaScript handler for edit-pfee' => [
        'pattern' => '\\.edit-pfee.*click',
        'required' => true,
        'check' => preg_match('/\$\(document\)\.on\([\'"]click[\'"],\s*[\'"]\.edit-pfee/', $content) === 1
    ],
    'Has JavaScript handler for edit-sst' => [
        'pattern' => '\\.edit-sst.*click',
        'required' => true,
        'check' => preg_match('/\$\(document\)\.on\([\'"]click[\'"],\s*[\'"]\.edit-sst/', $content) === 1
    ],
    'Has billId in editAmountInline function' => [
        'pattern' => 'function editAmountInline.*billId',
        'required' => true,
        'check' => preg_match('/function\s+editAmountInline\s*\([^)]*billId/', $content) === 1
    ],
    'Has bill_id in AJAX call' => [
        'pattern' => 'bill_id.*billId',
        'required' => true,
        'check' => strpos($content, "bill_id: billId") !== false || strpos($content, "'bill_id': billId") !== false
    ],
];

$allPassed = true;
foreach ($checks as $name => $check) {
    $status = $check['check'] ? '✅' : '❌';
    echo "$status $name\n";
    if (!$check['check'] && $check['required']) {
        $allPassed = false;
        echo "   Pattern: {$check['pattern']}\n";
    }
}

echo "\n";

// Check route file
$routePath = __DIR__.'/routes/web.php';
if (file_exists($routePath)) {
    $routeContent = file_get_contents($routePath);
    $hasRoute = strpos($routeContent, 'updateAmountsV3') !== false;
    echo ($hasRoute ? '✅' : '❌') . " Route 'transferfee.updateAmounts' registered\n";
    if (!$hasRoute) {
        $allPassed = false;
    }
} else {
    echo "❌ Route file not found\n";
    $allPassed = false;
}

// Check controller
$controllerPath = __DIR__.'/app/Http/Controllers/TransferFeeV3Controller.php';
if (file_exists($controllerPath)) {
    $controllerContent = file_get_contents($controllerPath);
    $hasMethod = strpos($controllerContent, 'function updateAmountsV3') !== false;
    echo ($hasMethod ? '✅' : '❌') . " Controller method 'updateAmountsV3' exists\n";
    if (!$hasMethod) {
        $allPassed = false;
    }
} else {
    echo "❌ Controller file not found\n";
    $allPassed = false;
}

echo "\n";
if ($allPassed) {
    echo "✅ All checks passed! Files appear to be correctly configured.\n";
    echo "\nIf icons still don't show on server:\n";
    echo "1. Clear view cache: php artisan view:clear\n";
    echo "2. Clear all caches: php artisan cache:clear\n";
    echo "3. Hard refresh browser: Ctrl+F5\n";
    echo "4. Verify file is uploaded to server\n";
} else {
    echo "❌ Some checks failed. Please review the issues above.\n";
    exit(1);
}

