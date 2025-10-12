<?php
/**
 * Lead Controller Update Verification Script
 * 
 * This script verifies that all LeadController references have been updated
 * to use the new namespace structure.
 */

class LeadUpdateVerifier
{
    private $basePath;
    private $issues = [];
    private $verifiedFiles = [];

    public function __construct()
    {
        $this->basePath = __DIR__;
    }

    public function run()
    {
        echo "Verifying Lead Controller Updates...\n";
        echo "====================================\n\n";

        $this->checkOldReferences();
        $this->checkNewStructure();
        $this->checkRoutes();
        
        $this->printResults();
    }

    private function checkOldReferences()
    {
        echo "Checking for old LeadController references...\n";
        
        $patterns = [
            'Admin\\\\LeadController@assign',
            'Admin\\\\LeadController@convertoClient',
            'Admin\\\\LeadController@convertToClient',
            'use App\\\\Http\\\\Controllers\\\\Admin\\\\LeadController',
            'App\\\\Http\\\\Controllers\\\\Admin\\\\LeadController'
        ];

        $files = $this->findFiles(['*.php', '*.blade.php', '*.js', '*.md']);
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            foreach ($patterns as $pattern) {
                if (preg_match('/' . $pattern . '/', $content)) {
                    $this->issues[] = [
                        'file' => $file,
                        'type' => 'old_reference',
                        'pattern' => $pattern,
                        'message' => "Found old LeadController reference"
                    ];
                }
            }
            
            $this->verifiedFiles[] = $file;
        }
    }

    private function checkNewStructure()
    {
        echo "Checking new controller structure...\n";
        
        $requiredFiles = [
            'app/Http/Controllers/Admin/Leads/LeadController.php',
            'app/Http/Controllers/Admin/Leads/LeadAssignmentController.php',
            'app/Http/Controllers/Admin/Leads/LeadConversionController.php'
        ];

        foreach ($requiredFiles as $file) {
            $fullPath = $this->basePath . '/' . $file;
            if (!file_exists($fullPath)) {
                $this->issues[] = [
                    'file' => $file,
                    'type' => 'missing_file',
                    'pattern' => '',
                    'message' => "Required file not found"
                ];
            } else {
                echo "  ✓ Found: $file\n";
            }
        }
    }

    private function checkRoutes()
    {
        echo "Checking route definitions...\n";
        
        $routeFile = $this->basePath . '/routes/web.php';
        if (file_exists($routeFile)) {
            $content = file_get_contents($routeFile);
            
            $expectedRoutes = [
                'Admin\\Leads\\LeadController@index',
                'Admin\\Leads\\LeadAssignmentController@assign',
                'Admin\\Leads\\LeadConversionController@convertToClient'
            ];

            foreach ($expectedRoutes as $route) {
                if (strpos($content, $route) === false) {
                    $this->issues[] = [
                        'file' => 'routes/web.php',
                        'type' => 'missing_route',
                        'pattern' => $route,
                        'message' => "Expected route not found"
                    ];
                } else {
                    echo "  ✓ Found route: $route\n";
                }
            }
        }
    }

    private function findFiles($patterns)
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->basePath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = str_replace($this->basePath . '/', '', $file->getPathname());
                $relativePath = str_replace('\\', '/', $relativePath);
                
                // Skip vendor, node_modules, and backup files
                if (strpos($relativePath, 'vendor/') === 0 || 
                    strpos($relativePath, 'node_modules/') === 0 ||
                    strpos($relativePath, '.backup') !== false) {
                    continue;
                }
                
                foreach ($patterns as $pattern) {
                    if (fnmatch($pattern, basename($file->getPathname()))) {
                        $files[] = $relativePath;
                        break;
                    }
                }
            }
        }
        
        return $files;
    }

    private function printResults()
    {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "VERIFICATION RESULTS\n";
        echo str_repeat("=", 50) . "\n";
        
        echo "Files Checked: " . count($this->verifiedFiles) . "\n";
        echo "Issues Found: " . count($this->issues) . "\n\n";
        
        if (empty($this->issues)) {
            echo "✓ All verifications passed! The update was successful.\n\n";
            
            echo "Current Controller Structure:\n";
            echo "  ✓ app/Http/Controllers/Admin/Leads/LeadController.php\n";
            echo "  ✓ app/Http/Controllers/Admin/Leads/LeadAssignmentController.php\n";
            echo "  ✓ app/Http/Controllers/Admin/Leads/LeadConversionController.php\n";
            
        } else {
            echo "⚠ Issues found that need attention:\n\n";
            
            foreach ($this->issues as $issue) {
                echo "File: {$issue['file']}\n";
                echo "Type: {$issue['type']}\n";
                echo "Message: {$issue['message']}\n";
                if (!empty($issue['pattern'])) {
                    echo "Pattern: {$issue['pattern']}\n";
                }
                echo str_repeat("-", 30) . "\n";
            }
        }
        
        echo "\nRecommended next steps:\n";
        echo "1. Test the application functionality\n";
        echo "2. Run: php artisan route:list --name=admin.leads\n";
        echo "3. Verify lead management features work correctly\n";
    }
}

// Run the verifier
$verifier = new LeadUpdateVerifier();
$verifier->run();
