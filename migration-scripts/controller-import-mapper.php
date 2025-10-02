<?php
/**
 * Controller Import Mapping Script for Laravel 12 Migration
 * This script generates all required use statements for controllers
 * Based on the controller audit findings
 */

class ControllerImportMapper
{
    /**
     * Controller mapping from old string syntax to new class syntax
     * Based on audit: 39 unique controllers with 503 total references
     */
    private $controllerMapping = [
        // HIGH PRIORITY (50+ references)
        'Admin\ClientsController' => 'App\Http\Controllers\Admin\ClientsController',
        'Admin\AdminController' => 'App\Http\Controllers\Admin\AdminController',
        
        // MEDIUM PRIORITY (10-49 references)
        'Admin\ApplicationsController' => 'App\Http\Controllers\Admin\ApplicationsController',
        'Admin\DocumentController' => 'App\Http\Controllers\Admin\DocumentController',
        'Admin\AssigneeController' => 'App\Http\Controllers\Admin\AssigneeController',
        'Admin\UserController' => 'App\Http\Controllers\Admin\UserController',
        'HomeController' => 'App\Http\Controllers\HomeController',
        'Admin\OfficeVisitController' => 'App\Http\Controllers\Admin\OfficeVisitController',
        'Admin\LeadController' => 'App\Http\Controllers\Admin\LeadController',
        'Admin\AppointmentsController' => 'App\Http\Controllers\Admin\AppointmentsController',
        
        // STANDARD PRIORITY (5-9 references)
        'Admin\WorkflowController' => 'App\Http\Controllers\Admin\WorkflowController',
        'Admin\BranchesController' => 'App\Http\Controllers\Admin\BranchesController',
        'Admin\DashboardController' => 'App\Http\Controllers\Admin\DashboardController',
        'Admin\VisaDocumentTypeController' => 'App\Http\Controllers\Admin\VisaDocumentTypeController',
        'Admin\MatterOtherEmailTemplateController' => 'App\Http\Controllers\Admin\MatterOtherEmailTemplateController',
        'Admin\PromoCodeController' => 'App\Http\Controllers\Admin\PromoCodeController',
        'Admin\PersonalDocumentTypeController' => 'App\Http\Controllers\Admin\PersonalDocumentTypeController',
        
        // LOW PRIORITY (1-4 references)
        'Admin\DocToPdfController' => 'App\Http\Controllers\Admin\DocToPdfController',
        'Auth\AdminLoginController' => 'App\Http\Controllers\Auth\AdminLoginController',
        'Admin\ChecklistController' => 'App\Http\Controllers\Admin\ChecklistController',
        'Admin\UsertypeController' => 'App\Http\Controllers\Admin\UsertypeController',
        'Admin\UserroleController' => 'App\Http\Controllers\Admin\UserroleController',
        'Admin\UploadChecklistController' => 'App\Http\Controllers\Admin\UploadChecklistController',
        'Admin\TagController' => 'App\Http\Controllers\Admin\TagController',
        'Admin\StaffController' => 'App\Http\Controllers\Admin\StaffController',
        'Admin\ProfileController' => 'App\Http\Controllers\Admin\ProfileController',
        'Admin\MatterEmailTemplateController' => 'App\Http\Controllers\Admin\MatterEmailTemplateController',
        'Admin\CrmEmailTemplateController' => 'App\Http\Controllers\Admin\CrmEmailTemplateController',
        'Admin\EmailTemplateController' => 'App\Http\Controllers\Admin\EmailTemplateController',
        'Admin\EmailController' => 'App\Http\Controllers\Admin\EmailController',
        'Admin\AppointmentDisableDateController' => 'App\Http\Controllers\Admin\AppointmentDisableDateController',
        'Admin\DocumentChecklistController' => 'App\Http\Controllers\Admin\DocumentChecklistController',
        'Admin\MatterController' => 'App\Http\Controllers\Admin\MatterController',
        'Admin\TeamController' => 'App\Http\Controllers\Admin\TeamController',
        'Admin\MediaController' => 'App\Http\Controllers\Admin\MediaController',
        'ExceptionController' => 'App\Http\Controllers\ExceptionController',
        'AppointmentBookController' => 'App\Http\Controllers\AppointmentBookController',
        'Admin\ApiController' => 'App\Http\Controllers\Admin\ApiController',
        'Admin\InvoiceController' => 'App\Http\Controllers\Admin\InvoiceController',
        'Admin\AuditLogController' => 'App\Http\Controllers\Admin\AuditLogController',
    ];

    /**
     * Generate use statements for all controllers
     */
    public function generateUseStatements(): array
    {
        $imports = [];
        
        foreach ($this->controllerMapping as $oldController => $newController) {
            $imports[] = "use {$newController};";
        }
        
        // Sort imports alphabetically for better organization
        sort($imports);
        
        return $imports;
    }

    /**
     * Generate use statements grouped by priority
     */
    public function generateGroupedUseStatements(): array
    {
        $grouped = [
            'high_priority' => [],
            'medium_priority' => [],
            'standard_priority' => [],
            'low_priority' => []
        ];

        // High Priority (50+ references)
        $highPriority = ['Admin\ClientsController', 'Admin\AdminController'];
        foreach ($highPriority as $controller) {
            if (isset($this->controllerMapping[$controller])) {
                $grouped['high_priority'][] = "use {$this->controllerMapping[$controller]};";
            }
        }

        // Medium Priority (10-49 references)
        $mediumPriority = [
            'Admin\ApplicationsController', 'Admin\DocumentController', 'Admin\AssigneeController',
            'Admin\UserController', 'HomeController', 'Admin\OfficeVisitController',
            'Admin\LeadController', 'Admin\AppointmentsController'
        ];
        foreach ($mediumPriority as $controller) {
            if (isset($this->controllerMapping[$controller])) {
                $grouped['medium_priority'][] = "use {$this->controllerMapping[$controller]};";
            }
        }

        // Standard Priority (5-9 references)
        $standardPriority = [
            'Admin\WorkflowController', 'Admin\BranchesController', 'Admin\DashboardController',
            'Admin\VisaDocumentTypeController', 'Admin\MatterOtherEmailTemplateController',
            'Admin\PromoCodeController', 'Admin\PersonalDocumentTypeController'
        ];
        foreach ($standardPriority as $controller) {
            if (isset($this->controllerMapping[$controller])) {
                $grouped['standard_priority'][] = "use {$this->controllerMapping[$controller]};";
            }
        }

        // Low Priority (1-4 references)
        $lowPriority = [
            'Admin\DocToPdfController', 'Auth\AdminLoginController', 'Admin\ChecklistController',
            'Admin\UsertypeController', 'Admin\UserroleController', 'Admin\UploadChecklistController',
            'Admin\TagController', 'Admin\StaffController', 'Admin\ProfileController',
            'Admin\MatterEmailTemplateController', 'Admin\CrmEmailTemplateController',
            'Admin\EmailTemplateController', 'Admin\EmailController', 'Admin\AppointmentDisableDateController',
            'Admin\DocumentChecklistController', 'Admin\MatterController', 'Admin\TeamController',
            'Admin\MediaController', 'ExceptionController', 'AppointmentBookController',
            'Admin\ApiController', 'Admin\InvoiceController', 'Admin\AuditLogController'
        ];
        foreach ($lowPriority as $controller) {
            if (isset($this->controllerMapping[$controller])) {
                $grouped['low_priority'][] = "use {$this->controllerMapping[$controller]};";
            }
        }

        return $grouped;
    }

    /**
     * Get controller mapping for route conversion
     */
    public function getControllerMapping(): array
    {
        return $this->controllerMapping;
    }

    /**
     * Validate that all controllers exist
     */
    public function validateControllers(): array
    {
        $validation = [
            'valid' => [],
            'invalid' => [],
            'missing' => []
        ];

        foreach ($this->controllerMapping as $oldController => $newController) {
            $controllerPath = str_replace('App\\', 'app/', $newController);
            $controllerPath = str_replace('\\', '/', $controllerPath) . '.php';
            
            if (file_exists($controllerPath)) {
                $validation['valid'][] = $newController;
            } else {
                $validation['missing'][] = $newController;
            }
        }

        return $validation;
    }

    /**
     * Generate import file content
     */
    public function generateImportFile(): string
    {
        $imports = $this->generateUseStatements();
        
        $content = "<?php\n\n";
        $content .= "/**\n";
        $content .= " * Auto-generated controller imports for Laravel 12 migration\n";
        $content .= " * Generated on: " . date('Y-m-d H:i:s') . "\n";
        $content .= " * Total controllers: " . count($imports) . "\n";
        $content .= " */\n\n";
        
        foreach ($imports as $import) {
            $content .= $import . "\n";
        }
        
        return $content;
    }
}

// Usage example (uncomment to test):
/*
$mapper = new ControllerImportMapper();

// Generate all imports
$imports = $mapper->generateUseStatements();
echo "Generated " . count($imports) . " use statements\n";

// Validate controllers
$validation = $mapper->validateControllers();
echo "Valid controllers: " . count($validation['valid']) . "\n";
echo "Missing controllers: " . count($validation['missing']) . "\n";

// Generate import file
$importFile = $mapper->generateImportFile();
file_put_contents('generated-imports.php', $importFile);
echo "Import file generated: generated-imports.php\n";
*/
