<?php
/**
 * Main Route Migration Script for Laravel 12
 * This script orchestrates the complete route migration process
 * 
 * Usage: php migrate-routes.php [--dry-run] [--backup] [--help]
 */

// Include all migration scripts
require_once __DIR__ . '/controller-import-mapper.php';
require_once __DIR__ . '/route-converter.php';
require_once __DIR__ . '/backup-system.php';
require_once __DIR__ . '/test-migration-scripts.php';

class RouteMigrator
{
    private $dryRun = false;
    private $createBackup = true;
    private $backupSystem;
    private $converter;
    private $mapper;
    private $logger = [];

    public function __construct()
    {
        $this->backupSystem = new RouteBackupSystem();
        $this->converter = new RouteConverter();
        $this->mapper = new ControllerImportMapper();
    }

    public function run(array $options = []): bool
    {
        $this->dryRun = $options['dry-run'] ?? false;
        $this->createBackup = $options['backup'] ?? true;

        $this->log("Starting Laravel 12 Route Migration");
        $this->log("Dry run mode: " . ($this->dryRun ? 'YES' : 'NO'));
        $this->log("Create backup: " . ($this->createBackup ? 'YES' : 'NO'));

        try {
            // Step 1: Run pre-migration tests
            $this->log("Step 1: Running pre-migration tests...");
            if (!$this->runPreMigrationTests()) {
                $this->log("Pre-migration tests failed. Aborting migration.", 'ERROR');
                return false;
            }

            // Step 2: Create backup
            if ($this->createBackup) {
                $this->log("Step 2: Creating backup...");
                $backup = $this->backupSystem->createBackup('Pre-migration backup - ' . date('Y-m-d H:i:s'));
                $this->log("Backup created: {$backup['id']}");
            }

            // Step 3: Generate controller imports
            $this->log("Step 3: Generating controller imports...");
            $imports = $this->mapper->generateUseStatements();
            $this->log("Generated " . count($imports) . " controller imports");

            // Step 4: Convert routes
            $this->log("Step 4: Converting routes...");
            $webContent = file_get_contents('routes/web.php');
            $convertedContent = $this->converter->convertRouteFile($webContent);
            
            // Add imports to the top of the file
            $finalContent = $this->addImportsToFile($convertedContent, $imports);

            // Step 5: Save converted file
            if (!$this->dryRun) {
                $this->log("Step 5: Saving converted routes...");
                $result = file_put_contents('routes/web.php', $finalContent);
                
                if ($result === false) {
                    throw new Exception("Failed to write converted routes to file");
                }
                
                $this->log("Routes successfully converted and saved");
            } else {
                $this->log("Step 5: Dry run - saving to web.converted.php");
                file_put_contents('routes/web.converted.php', $finalContent);
                $this->log("Converted routes saved to web.converted.php");
            }

            // Step 6: Generate migration report
            $this->log("Step 6: Generating migration report...");
            $this->generateMigrationReport();

            $this->log("Migration completed successfully!", 'SUCCESS');
            return true;

        } catch (Exception $e) {
            $this->log("Migration failed: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    private function runPreMigrationTests(): bool
    {
        $tester = new MigrationScriptTester();
        $results = $tester->runAllTests();

        // Check if any critical tests failed
        $criticalComponents = ['controller_import_mapper', 'route_converter', 'backup_system'];
        
        foreach ($criticalComponents as $component) {
            if (isset($results[$component]['error'])) {
                $this->log("Critical component failed: {$component} - {$results[$component]['error']}", 'ERROR');
                return false;
            }

            $failed = $results[$component]['failed'] ?? 0;
            if ($failed > 0) {
                $this->log("Critical component has failed tests: {$component} ({$failed} failures)", 'ERROR');
                return false;
            }
        }

        $this->log("All pre-migration tests passed");
        return true;
    }

    private function addImportsToFile(string $content, array $imports): string
    {
        $lines = explode("\n", $content);
        $newLines = [];
        
        // Find the opening PHP tag
        $phpTagFound = false;
        $importsAdded = false;

        foreach ($lines as $line) {
            if (!$phpTagFound && strpos($line, '<?php') !== false) {
                $phpTagFound = true;
                $newLines[] = $line;
                continue;
            }

            if ($phpTagFound && !$importsAdded) {
                // Add imports after PHP tag
                $newLines[] = '';
                $newLines[] = '// Auto-generated controller imports for Laravel 12 migration';
                $newLines[] = '// Generated on: ' . date('Y-m-d H:i:s');
                $newLines[] = '';
                
                foreach ($imports as $import) {
                    $newLines[] = $import;
                }
                
                $newLines[] = '';
                $importsAdded = true;
            }

            $newLines[] = $line;
        }

        return implode("\n", $newLines);
    }

    private function generateMigrationReport(): void
    {
        $stats = $this->converter->getConversionStats();
        $validation = $this->mapper->validateControllers();

        $report = "Laravel 12 Route Migration Report\n";
        $report .= "==================================\n\n";
        $report .= "Migration completed on: " . date('Y-m-d H:i:s') . "\n";
        $report .= "Dry run mode: " . ($this->dryRun ? 'YES' : 'NO') . "\n\n";

        $report .= "Conversion Statistics:\n";
        $report .= "- Total route conversions: " . $stats['total_conversions'] . "\n";
        $report .= "- Controllers converted: " . count($stats['controllers_converted']) . "\n";
        $report .= "- Methods converted: " . count($stats['methods_converted']) . "\n";
        $report .= "- Conversion errors: " . $stats['errors'] . "\n\n";

        $report .= "Controller Validation:\n";
        $report .= "- Valid controllers: " . count($validation['valid']) . "\n";
        $report .= "- Missing controllers: " . count($validation['missing']) . "\n\n";

        if (!empty($validation['missing'])) {
            $report .= "Missing Controllers (require attention):\n";
            foreach ($validation['missing'] as $missing) {
                $report .= "- {$missing}\n";
            }
            $report .= "\n";
        }

        $report .= "Controllers Converted:\n";
        foreach ($stats['controllers_converted'] as $controller) {
            $report .= "- {$controller}\n";
        }
        $report .= "\n";

        $report .= "Migration Log:\n";
        foreach ($this->logger as $logEntry) {
            $report .= "[{$logEntry['timestamp']}] {$logEntry['level']}: {$logEntry['message']}\n";
        }

        // Save report
        $reportFile = 'migration-scripts/migration-report-' . date('Y-m-d-H-i-s') . '.txt';
        file_put_contents($reportFile, $report);
        $this->log("Migration report saved to: {$reportFile}");
    }

    private function log(string $message, string $level = 'INFO'): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $this->logger[] = [
            'timestamp' => $timestamp,
            'level' => $level,
            'message' => $message
        ];

        $color = match($level) {
            'ERROR' => "\033[31m", // Red
            'SUCCESS' => "\033[32m", // Green
            'WARNING' => "\033[33m", // Yellow
            default => "\033[37m" // White
        };

        $reset = "\033[0m";
        echo "{$color}[{$timestamp}] {$level}: {$message}{$reset}\n";
    }

    public function getLogs(): array
    {
        return $this->logger;
    }

    public function showHelp(): void
    {
        echo "Laravel 12 Route Migration Script\n";
        echo "==================================\n\n";
        echo "Usage: php migrate-routes.php [options]\n\n";
        echo "Options:\n";
        echo "  --dry-run    Run migration without making changes (saves to .converted file)\n";
        echo "  --backup     Create backup before migration (default: true)\n";
        echo "  --no-backup  Skip backup creation\n";
        echo "  --help       Show this help message\n\n";
        echo "Examples:\n";
        echo "  php migrate-routes.php                    # Full migration with backup\n";
        echo "  php migrate-routes.php --dry-run          # Test migration without changes\n";
        echo "  php migrate-routes.php --no-backup        # Migration without backup\n\n";
    }
}

// Handle command line arguments
$options = [];
$args = $argv ?? [];

foreach ($args as $arg) {
    switch ($arg) {
        case '--dry-run':
            $options['dry-run'] = true;
            break;
        case '--backup':
            $options['backup'] = true;
            break;
        case '--no-backup':
            $options['backup'] = false;
            break;
        case '--help':
            $migrator = new RouteMigrator();
            $migrator->showHelp();
            exit(0);
    }
}

// Run migration
$migrator = new RouteMigrator();
$success = $migrator->run($options);

exit($success ? 0 : 1);
