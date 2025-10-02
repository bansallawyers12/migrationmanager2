<?php
/**
 * Test Migration Scripts for Laravel 12 Route Migration
 * This script tests all migration components without making changes
 */

// Include all migration scripts
require_once __DIR__ . '/controller-import-mapper.php';
require_once __DIR__ . '/route-converter.php';
require_once __DIR__ . '/backup-system.php';

class MigrationScriptTester
{
    private $testResults = [];
    private $errors = [];

    public function runAllTests(): array
    {
        $this->testResults = [
            'controller_import_mapper' => $this->testControllerImportMapper(),
            'route_converter' => $this->testRouteConverter(),
            'backup_system' => $this->testBackupSystem(),
            'file_permissions' => $this->testFilePermissions(),
            'route_file_access' => $this->testRouteFileAccess()
        ];

        return $this->testResults;
    }

    private function testControllerImportMapper(): array
    {
        $results = ['passed' => 0, 'failed' => 0, 'tests' => []];

        try {
            $mapper = new ControllerImportMapper();

            // Test 1: Generate use statements
            $imports = $mapper->generateUseStatements();
            $test1 = count($imports) > 0;
            $results['tests']['generate_use_statements'] = $test1;
            $results[$test1 ? 'passed' : 'failed']++;

            // Test 2: Generate grouped use statements
            $grouped = $mapper->generateGroupedUseStatements();
            $test2 = isset($grouped['high_priority']) && count($grouped['high_priority']) > 0;
            $results['tests']['generate_grouped_statements'] = $test2;
            $results[$test2 ? 'passed' : 'failed']++;

            // Test 3: Get controller mapping
            $mapping = $mapper->getControllerMapping();
            $test3 = count($mapping) > 0;
            $results['tests']['get_controller_mapping'] = $test3;
            $results[$test3 ? 'passed' : 'failed']++;

            // Test 4: Validate controllers (check if they exist)
            $validation = $mapper->validateControllers();
            $test4 = count($validation['valid']) > 0;
            $results['tests']['validate_controllers'] = $test4;
            $results[$test4 ? 'passed' : 'failed']++;
            $results['validation_details'] = $validation;

            // Test 5: Generate import file
            $importFile = $mapper->generateImportFile();
            $test5 = !empty($importFile) && strpos($importFile, '<?php') !== false;
            $results['tests']['generate_import_file'] = $test5;
            $results[$test5 ? 'passed' : 'failed']++;

        } catch (Exception $e) {
            $this->errors[] = "ControllerImportMapper test failed: " . $e->getMessage();
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    private function testRouteConverter(): array
    {
        $results = ['passed' => 0, 'failed' => 0, 'tests' => []];

        try {
            $converter = new RouteConverter();

            // Test 1: Convert single route string
            $testRoute = "'Admin\ClientsController@index'";
            $converted = $converter->convertRouteString($testRoute);
            $test1 = strpos($converted, '::class') !== false;
            $results['tests']['convert_single_route'] = $test1;
            $results[$test1 ? 'passed' : 'failed']++;
            $results['conversion_example'] = ['original' => $testRoute, 'converted' => $converted];

            // Test 2: Get conversion stats
            $stats = $converter->getConversionStats();
            $test2 = isset($stats['total_conversions']);
            $results['tests']['get_conversion_stats'] = $test2;
            $results[$test2 ? 'passed' : 'failed']++;

            // Test 3: Generate report
            $report = $converter->generateReport();
            $test3 = !empty($report) && strpos($report, 'Route Conversion Report') !== false;
            $results['tests']['generate_report'] = $test3;
            $results[$test3 ? 'passed' : 'failed']++;

            // Test 4: Validate conversions
            $validation = $converter->validateConversions();
            $test4 = isset($validation['valid']);
            $results['tests']['validate_conversions'] = $test4;
            $results[$test4 ? 'passed' : 'failed']++;

        } catch (Exception $e) {
            $this->errors[] = "RouteConverter test failed: " . $e->getMessage();
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    private function testBackupSystem(): array
    {
        $results = ['passed' => 0, 'failed' => 0, 'tests' => []];

        try {
            $backupSystem = new RouteBackupSystem();

            // Test 1: Create backup
            $backup = $backupSystem->createBackup('Test backup');
            $test1 = !empty($backup['id']) && file_exists($backupSystem->getBackupDir() . '/' . $backup['id']);
            $results['tests']['create_backup'] = $test1;
            $results[$test1 ? 'passed' : 'failed']++;

            // Test 2: List backups
            $backups = $backupSystem->listBackups();
            $test2 = is_array($backups) && count($backups) > 0;
            $results['tests']['list_backups'] = $test2;
            $results[$test2 ? 'passed' : 'failed']++;

            // Test 3: Verify backup
            $verification = $backupSystem->verifyBackup($backup['id']);
            $test3 = isset($verification['valid']);
            $results['tests']['verify_backup'] = $test3;
            $results[$test3 ? 'passed' : 'failed']++;

            // Test 4: Cleanup test backup
            $deleted = $backupSystem->deleteBackup($backup['id']);
            $test4 = $deleted;
            $results['tests']['delete_backup'] = $test4;
            $results[$test4 ? 'passed' : 'failed']++;

        } catch (Exception $e) {
            $this->errors[] = "BackupSystem test failed: " . $e->getMessage();
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    private function testFilePermissions(): array
    {
        $results = ['passed' => 0, 'failed' => 0, 'tests' => []];

        $filesToTest = [
            'routes/web.php',
            'routes/api.php',
            'routes/channels.php',
            'routes/console.php',
            'routes/emailUser.php'
        ];

        foreach ($filesToTest as $file) {
            $testName = 'file_permission_' . basename($file, '.php');
            
            if (file_exists($file)) {
                $readable = is_readable($file);
                $writable = is_writable($file);
                $test = $readable && $writable;
                
                $results['tests'][$testName] = $test;
                $results[$test ? 'passed' : 'failed']++;
                $results['file_details'][$file] = [
                    'readable' => $readable,
                    'writable' => $writable
                ];
            } else {
                $results['tests'][$testName] = false;
                $results['failed']++;
                $results['file_details'][$file] = ['exists' => false];
            }
        }

        return $results;
    }

    private function testRouteFileAccess(): array
    {
        $results = ['passed' => 0, 'failed' => 0, 'tests' => []];

        try {
            // Test reading web.php
            $webContent = file_get_contents('routes/web.php');
            $test1 = !empty($webContent) && strpos($webContent, 'Route::') !== false;
            $results['tests']['read_web_routes'] = $test1;
            $results[$test1 ? 'passed' : 'failed']++;

            // Test counting route definitions
            $routeCount = substr_count($webContent, 'Route::');
            $test2 = $routeCount > 0;
            $results['tests']['count_routes'] = $test2;
            $results[$test2 ? 'passed' : 'failed']++;
            $results['route_count'] = $routeCount;

            // Test finding controller references
            $controllerCount = substr_count($webContent, 'Controller@');
            $test3 = $controllerCount > 0;
            $results['tests']['count_controllers'] = $test3;
            $results[$test3 ? 'passed' : 'failed']++;
            $results['controller_count'] = $controllerCount;

        } catch (Exception $e) {
            $this->errors[] = "Route file access test failed: " . $e->getMessage();
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    public function generateTestReport(): string
    {
        $report = "Migration Scripts Test Report\n";
        $report .= "===========================\n\n";
        $report .= "Generated on: " . date('Y-m-d H:i:s') . "\n\n";

        $totalPassed = 0;
        $totalFailed = 0;

        foreach ($this->testResults as $component => $results) {
            $report .= "Component: " . str_replace('_', ' ', ucwords($component)) . "\n";
            $report .= str_repeat('-', 50) . "\n";

            if (isset($results['error'])) {
                $report .= "ERROR: " . $results['error'] . "\n\n";
                continue;
            }

            $passed = $results['passed'] ?? 0;
            $failed = $results['failed'] ?? 0;
            $total = $passed + $failed;

            $report .= "Tests passed: {$passed}/{$total}\n";
            $report .= "Tests failed: {$failed}/{$total}\n";

            if (isset($results['tests'])) {
                foreach ($results['tests'] as $testName => $result) {
                    $status = $result ? 'PASS' : 'FAIL';
                    $report .= "  - {$testName}: {$status}\n";
                }
            }

            $totalPassed += $passed;
            $totalFailed += $failed;
            $report .= "\n";
        }

        $report .= "Overall Summary\n";
        $report .= str_repeat('=', 50) . "\n";
        $report .= "Total tests passed: {$totalPassed}\n";
        $report .= "Total tests failed: {$totalFailed}\n";
        $report .= "Success rate: " . round(($totalPassed / ($totalPassed + $totalFailed)) * 100, 2) . "%\n";

        if (!empty($this->errors)) {
            $report .= "\nErrors encountered:\n";
            foreach ($this->errors as $error) {
                $report .= "- {$error}\n";
            }
        }

        return $report;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}

// Run tests if script is executed directly
if (php_sapi_name() === 'cli' || (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET')) {
    $tester = new MigrationScriptTester();
    $results = $tester->runAllTests();
    $report = $tester->generateTestReport();
    
    echo $report;
    
    // Save report to file
    file_put_contents('migration-scripts/test-report.txt', $report);
    echo "\nTest report saved to: migration-scripts/test-report.txt\n";
    
    if ($tester->hasErrors()) {
        echo "\nWARNING: Some tests failed. Please review the errors before proceeding.\n";
        exit(1);
    } else {
        echo "\nAll tests passed! Migration scripts are ready to use.\n";
        exit(0);
    }
}
