<?php
/**
 * Lead Controller Namespace Update Script
 * 
 * This script updates all references from the old LeadController namespace
 * to the new separated controller structure.
 * 
 * Usage: php update_lead_controllers.php
 */

class LeadControllerUpdater
{
    private $basePath;
    private $updatedFiles = [];
    private $errors = [];

    public function __construct()
    {
        $this->basePath = __DIR__;
    }

    public function run()
    {
        echo "Starting Lead Controller Namespace Update...\n";
        echo "============================================\n\n";

        $this->updateRoutes();
        $this->updateViewFiles();
        $this->updateControllerFiles();
        $this->updateJavaScriptFiles();
        $this->updateDocumentation();

        $this->printSummary();
    }

    private function updateRoutes()
    {
        echo "Updating route files...\n";
        
        $routeFiles = [
            'routes/web.php',
            'routes/api.php',
            'routes/console.php'
        ];

        foreach ($routeFiles as $file) {
            if (file_exists($this->basePath . '/' . $file)) {
                $this->updateFile($file, [
                    'Admin\\LeadController@assign' => 'Admin\\Leads\\LeadAssignmentController@assign',
                    'Admin\\LeadController@convertoClient' => 'Admin\\Leads\\LeadConversionController@convertToClient',
                    'Admin\\LeadController@convertToClient' => 'Admin\\Leads\\LeadConversionController@convertToClient',
                    'Admin\\LeadController@bulkConvert' => 'Admin\\Leads\\LeadConversionController@bulkConvertToClient',
                    'Admin\\LeadController@getAssignableUsers' => 'Admin\\Leads\\LeadAssignmentController@getAssignableUsers',
                ]);
            }
        }
    }

    private function updateViewFiles()
    {
        echo "Updating view files...\n";
        
        $viewFiles = $this->findFiles('resources/views', ['*.blade.php']);
        
        foreach ($viewFiles as $file) {
            $this->updateFile($file, [
                'route("admin.leads.assign")' => 'route("admin.leads.assign")',
                'route("admin.leads.convert")' => 'route("admin.leads.convert")',
                'action("Admin\\LeadController@assign")' => 'action("Admin\\Leads\\LeadAssignmentController@assign")',
                'action("Admin\\LeadController@convertoClient")' => 'action("Admin\\Leads\\LeadConversionController@convertToClient")',
                'action("Admin\\LeadController@convertToClient")' => 'action("Admin\\Leads\\LeadConversionController@convertToClient")',
            ]);
        }
    }

    private function updateControllerFiles()
    {
        echo "Updating controller files...\n";
        
        $controllerFiles = $this->findFiles('app/Http/Controllers', ['*.php']);
        
        foreach ($controllerFiles as $file) {
            // Skip the new Leads folder files
            if (strpos($file, 'app/Http/Controllers/Admin/Leads/') !== false) {
                continue;
            }
            
            $this->updateFile($file, [
                'use App\\Http\\Controllers\\Admin\\LeadController' => 'use App\\Http\\Controllers\\Admin\\Leads\\LeadController',
                '\\App\\Http\\Controllers\\Admin\\LeadController' => '\\App\\Http\\Controllers\\Admin\\Leads\\LeadController',
                'Admin\\LeadController@' => 'Admin\\Leads\\LeadController@',
            ]);
        }
    }

    private function updateJavaScriptFiles()
    {
        echo "Updating JavaScript files...\n";
        
        $jsFiles = $this->findFiles('public/js', ['*.js']);
        $jsFiles = array_merge($jsFiles, $this->findFiles('resources/js', ['*.js']));
        
        foreach ($jsFiles as $file) {
            $this->updateFile($file, [
                'admin/leads/assign' => 'admin/leads/assign',
                'admin/leads/convert' => 'admin/leads/convert',
                'LeadController' => 'LeadController', // Keep as is, just update URLs if needed
            ]);
        }
    }

    private function updateDocumentation()
    {
        echo "Updating documentation files...\n";
        
        $docFiles = $this->findFiles('.', ['*.md']);
        
        foreach ($docFiles as $file) {
            $this->updateFile($file, [
                'Admin\\LeadController' => 'Admin\\Leads\\LeadController',
                'LeadController@assign' => 'LeadAssignmentController@assign',
                'LeadController@convertoClient' => 'LeadConversionController@convertToClient',
                'LeadController@convertToClient' => 'LeadConversionController@convertToClient',
            ]);
        }
    }

    private function updateFile($filePath, $replacements)
    {
        $fullPath = $this->basePath . '/' . $filePath;
        
        if (!file_exists($fullPath)) {
            return;
        }

        $content = file_get_contents($fullPath);
        $originalContent = $content;
        
        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        
        if ($content !== $originalContent) {
            if (file_put_contents($fullPath, $content)) {
                $this->updatedFiles[] = $filePath;
                echo "  ✓ Updated: $filePath\n";
            } else {
                $this->errors[] = "Failed to write: $filePath";
                echo "  ✗ Failed: $filePath\n";
            }
        }
    }

    private function findFiles($directory, $patterns)
    {
        $files = [];
        
        if (!is_dir($this->basePath . '/' . $directory)) {
            return $files;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->basePath . '/' . $directory)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = str_replace($this->basePath . '/', '', $file->getPathname());
                $relativePath = str_replace('\\', '/', $relativePath);
                
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

    private function printSummary()
    {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "UPDATE SUMMARY\n";
        echo str_repeat("=", 50) . "\n";
        
        echo "Files Updated: " . count($this->updatedFiles) . "\n";
        echo "Errors: " . count($this->errors) . "\n\n";
        
        if (!empty($this->updatedFiles)) {
            echo "UPDATED FILES:\n";
            foreach ($this->updatedFiles as $file) {
                echo "  - $file\n";
            }
            echo "\n";
        }
        
        if (!empty($this->errors)) {
            echo "ERRORS:\n";
            foreach ($this->errors as $error) {
                echo "  - $error\n";
            }
            echo "\n";
        }
        
        echo "Next Steps:\n";
        echo "1. Test the application to ensure all functionality works\n";
        echo "2. Update any remaining hardcoded references manually\n";
        echo "3. Run: php artisan route:clear && php artisan config:clear\n";
        echo "4. Run: composer dump-autoload\n";
        echo "\n";
        
        if (count($this->errors) === 0) {
            echo "✓ Update completed successfully!\n";
        } else {
            echo "⚠ Update completed with errors. Please review the errors above.\n";
        }
    }
}

// Run the updater
$updater = new LeadControllerUpdater();
$updater->run();
