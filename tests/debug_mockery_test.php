<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\PdfGeneratorService;

echo "Test 1: Before any mock\n";
try {
    $result = PdfGeneratorService::generatePdf('member-application', new \App\Models\Membership\Member());
    echo "Result: " . gettype($result) . " (" . substr((string)$result, 0, 30) . "...)\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
}

echo "\n--- Creating alias mock ---\n";
$mock = Mockery::mock('alias:App\Services\PdfGeneratorService');
$mock->shouldReceive('generatePdf')
    ->andThrow(new \Exception('PDF generation failed'));

echo "Test 2: With alias mock\n";
try {
    $result = PdfGeneratorService::generatePdf('member-application', new \App\Models\Membership\Member());
    echo "Result: " . gettype($result) . "\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
}

echo "\n--- Calling Mockery::close() ---\n";
Mockery::close();

echo "Test 3: After Mockery::close()\n";
try {
    $result = PdfGeneratorService::generatePdf('member-application', new \App\Models\Membership\Member());
    echo "Result: " . gettype($result) . " (" . substr((string)$result, 0, 30) . "...)\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
}

echo "\nTest 4: Second alias mock\n";
$mock2 = Mockery::mock('alias:App\Services\PdfGeneratorService');
$mock2->shouldReceive('generatePdf')
    ->andThrow(new \Exception('PDF generation failed 2'));
Mockery::close();

echo "\nTest 5: After second close\n";
try {
    $result = PdfGeneratorService::generatePdf('member-application', new \App\Models\Membership\Member());
    echo "Result: " . gettype($result) . " (" . substr((string)$result, 0, 30) . "...)\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
}
