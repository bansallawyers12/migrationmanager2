# PowerShell Script to Fix All Namespace Issues
# This script will update all App\ namespace references to App\Models\

Write-Host "==================================================================" -ForegroundColor Cyan
Write-Host "  Laravel Namespace Fixer - App\ to App\Models\" -ForegroundColor Cyan
Write-Host "==================================================================" -ForegroundColor Cyan
Write-Host ""

# Create backup directory
$backupDir = "namespace_fix_backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
New-Item -ItemType Directory -Path $backupDir -Force | Out-Null
Write-Host "[INFO] Created backup directory: $backupDir" -ForegroundColor Green

# List of all valid models that exist in App\Models
$validModels = @(
    'AcademicRequirement', 'AccountClientReceipt', 'ActivitiesLog', 'Admin', 'AgentDetails',
    'AiChat', 'AiChatMessage', 'Airport', 'Application', 'ApplicationActivitiesLog',
    'ApplicationDocument', 'ApplicationDocumentList', 'ApplicationFeeOption', 'ApplicationFeeOptionType',
    'Appointment', 'AppointmentLog', 'Attachment', 'AuditLog',
    'BookService', 'BookServiceDisableSlot', 'BookServiceSlotPerPerson', 'Branch',
    'Category', 'CheckinHistory', 'CheckinLog', 'Checklist', 'Client',
    'ClientAddress', 'ClientCharacter', 'ClientContact', 'ClientEmail', 'ClientEoiReference',
    'ClientExperience', 'ClientMatter', 'ClientOccupation', 'ClientPassportInformation',
    'ClientPoint', 'ClientQualification', 'ClientRelationship', 'clientServiceTaken',
    'ClientSpouseDetail', 'ClientTestScore', 'ClientTravelInformation', 'ClientVisaCountry',
    'Contact', 'CostAssignmentForm', 'Country', 'Course', 'CrmEmailTemplate', 'Currency',
    'DeviceToken', 'Document', 'DocumentChecklist', 'Email', 'EmailAccount', 'EmailDraft',
    'EmailRecord', 'EmailSignature', 'EmailTemplate', 'Enquiry', 'EnquirySource',
    'FeeOption', 'FeeOptionType', 'FileStatus', 'Followup', 'FollowupType',
    'Form956', 'FreeDownload', 'Group', 'HomeContent',
    'Invoice', 'InvoiceDetail', 'InvoiceFollowup', 'InvoicePayment', 'InvoiceSchedule',
    'Item', 'Label', 'Lead', 'MailReport', 'Matter', 'MatterEmailTemplate',
    'MatterOtherEmailTemplate', 'NatureOfEnquiry', 'Note', 'Notification', 'OfficeVisit',
    'OnlineForm', 'OurService', 'Partner', 'PartnerBranch', 'PartnerType', 'PasswordResetLink',
    'PersonalDocumentType', 'Product', 'ProductAreaLevel', 'ProductType', 'Profile',
    'PromoCode', 'Promotion', 'RefreshToken', 'Relationship',
    'SeoPage', 'Service', 'ServiceFeeOption', 'ServiceFeeOptionType', 'Setting', 'ShareInvoice',
    'SignatureField', 'Signer', 'Slider', 'Source', 'State', 'SubCategory', 'Subject',
    'SubjectArea', 'Tag', 'Task', 'TaxRate', 'Team', 'Testimonial', 'TestScore',
    'UploadChecklist', 'User', 'UserLog', 'UserRole', 'UserType', 'VerifyUser',
    'VisaDocChecklist', 'VisaDocumentType', 'WebsiteSetting', 'Workflow', 'WorkflowStage'
)

# Model name mappings for renamed models
$modelMappings = @{
    'Agent' = 'AgentDetails'
    'Tax' = 'TaxRate'
}

# Statistics
$stats = @{
    FilesProcessed = 0
    FilesModified = 0
    TotalReplacements = 0
    BackedUpFiles = 0
}

# Function to backup a file
function Backup-File {
    param($filePath)
    
    $relativePath = $filePath.Replace((Get-Location).Path, "").TrimStart('\')
    $backupPath = Join-Path $backupDir $relativePath
    $backupParent = Split-Path -Parent $backupPath
    
    if (-not (Test-Path $backupParent)) {
        New-Item -ItemType Directory -Path $backupParent -Force | Out-Null
    }
    
    Copy-Item -Path $filePath -Destination $backupPath -Force
    $stats.BackedUpFiles++
}

# Function to process a file
function Process-File {
    param($filePath)
    
    $stats.FilesProcessed++
    
    # Read file content
    $content = Get-Content -Path $filePath -Raw -Encoding UTF8
    if ($null -eq $content) { return }
    
    $originalContent = $content
    $fileReplacements = 0
    
    # Process valid models
    foreach ($model in $validModels) {
        # Pattern 1: use App\Model;
        $oldPattern = "use App\$model;"
        $newPattern = "use App\Models\$model;"
        if ($content -match [regex]::Escape($oldPattern)) {
            $content = $content.Replace($oldPattern, $newPattern)
            $fileReplacements++
        }
        
        # Pattern 2: \App\Model:: (static calls)
        $oldPattern = "\App\$model::"
        $newPattern = "\App\Models\$model::"
        if ($content.Contains($oldPattern)) {
            $content = $content.Replace($oldPattern, $newPattern)
            $fileReplacements++
        }
        
        # Pattern 3: new \App\Model
        $oldPattern = "new \App\$model"
        $newPattern = "new \App\Models\$model"
        if ($content.Contains($oldPattern)) {
            $content = $content.Replace($oldPattern, $newPattern)
            $fileReplacements++
        }
        
        # Pattern 4: App\Model::class
        $oldPattern = "App\$model::class"
        $newPattern = "App\Models\$model::class"
        if ($content.Contains($oldPattern)) {
            $content = $content.Replace($oldPattern, $newPattern)
            $fileReplacements++
        }
        
        # Pattern 5: = \App\Model
        $oldPattern = "= \App\$model::"
        $newPattern = "= \App\Models\$model::"
        if ($content.Contains($oldPattern)) {
            $content = $content.Replace($oldPattern, $newPattern)
            $fileReplacements++
        }
        
        # Pattern 6: =\App\Model (no space)
        $oldPattern = "=\App\$model::"
        $newPattern = "=\App\Models\$model::"
        if ($content.Contains($oldPattern)) {
            $content = $content.Replace($oldPattern, $newPattern)
            $fileReplacements++
        }
    }
    
    # Process renamed models
    foreach ($oldModel in $modelMappings.Keys) {
        $newModel = $modelMappings[$oldModel]
        
        # Pattern 1: use App\OldModel;
        $oldPattern = "use App\$oldModel;"
        $newPattern = "use App\Models\$newModel;"
        if ($content -match [regex]::Escape($oldPattern)) {
            $content = $content.Replace($oldPattern, $newPattern)
            $fileReplacements++
        }
        
        # Pattern 2: \App\OldModel::
        $oldPattern = "\App\$oldModel::"
        $newPattern = "\App\Models\$newModel::"
        if ($content.Contains($oldPattern)) {
            $content = $content.Replace($oldPattern, $newPattern)
            $fileReplacements++
        }
        
        # Pattern 3: new \App\OldModel
        $oldPattern = "new \App\$oldModel"
        $newPattern = "new \App\Models\$newModel"
        if ($content.Contains($oldPattern)) {
            $content = $content.Replace($oldPattern, $newPattern)
            $fileReplacements++
        }
        
        # Pattern 4: = \App\OldModel
        $oldPattern = "= \App\$oldModel::"
        $newPattern = "= \App\Models\$newModel::"
        if ($content.Contains($oldPattern)) {
            $content = $content.Replace($oldPattern, $newPattern)
            $fileReplacements++
        }
        
        # Pattern 5: =\App\OldModel
        $oldPattern = "=\App\$oldModel::"
        $newPattern = "=\App\Models\$newModel::"
        if ($content.Contains($oldPattern)) {
            $content = $content.Replace($oldPattern, $newPattern)
            $fileReplacements++
        }
    }
    
    # If content changed, backup original and save new content
    if ($content -ne $originalContent) {
        Backup-File -filePath $filePath
        
        # Save with UTF8 encoding without BOM
        $utf8NoBom = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText($filePath, $content, $utf8NoBom)
        
        $stats.FilesModified++
        $stats.TotalReplacements += $fileReplacements
        
        $relPath = $filePath.Replace((Get-Location).Path, "").TrimStart('\')
        Write-Host "[FIXED] $relPath ($fileReplacements replacements)" -ForegroundColor Yellow
    }
}

# Main execution
Write-Host "[INFO] Starting namespace fix process..." -ForegroundColor Cyan
Write-Host ""

# Get all PHP files in app directory
Write-Host "[SCAN] Processing app/ directory..." -ForegroundColor Cyan
$appFiles = Get-ChildItem -Path "app" -Filter "*.php" -Recurse -File
foreach ($file in $appFiles) {
    Process-File -filePath $file.FullName
}

# Get all Blade files in resources/views
Write-Host ""
Write-Host "[SCAN] Processing resources/views/ directory..." -ForegroundColor Cyan
if (Test-Path "resources\views") {
    $viewFiles = Get-ChildItem -Path "resources\views" -Filter "*.blade.php" -Recurse -File
    foreach ($file in $viewFiles) {
        Process-File -filePath $file.FullName
    }
}

# Process config files
Write-Host ""
Write-Host "[SCAN] Processing config/ directory..." -ForegroundColor Cyan
if (Test-Path "config") {
    $configFiles = Get-ChildItem -Path "config" -Filter "*.php" -Recurse -File
    foreach ($file in $configFiles) {
        Process-File -filePath $file.FullName
    }
}

# Process database files
Write-Host ""
Write-Host "[SCAN] Processing database/ directory..." -ForegroundColor Cyan
if (Test-Path "database") {
    $databaseFiles = Get-ChildItem -Path "database" -Filter "*.php" -Recurse -File
    foreach ($file in $databaseFiles) {
        Process-File -filePath $file.FullName
    }
}

# Process routes files
Write-Host ""
Write-Host "[SCAN] Processing routes/ directory..." -ForegroundColor Cyan
if (Test-Path "routes") {
    $routeFiles = Get-ChildItem -Path "routes" -Filter "*.php" -Recurse -File
    foreach ($file in $routeFiles) {
        Process-File -filePath $file.FullName
    }
}

# Final report
Write-Host ""
Write-Host "==================================================================" -ForegroundColor Cyan
Write-Host "  FIX COMPLETED" -ForegroundColor Green
Write-Host "==================================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Statistics:" -ForegroundColor White
Write-Host "  Files Scanned:      $($stats.FilesProcessed)" -ForegroundColor White
Write-Host "  Files Modified:     $($stats.FilesModified)" -ForegroundColor Yellow
Write-Host "  Files Backed Up:    $($stats.BackedUpFiles)" -ForegroundColor Green
Write-Host "  Total Replacements: $($stats.TotalReplacements)" -ForegroundColor Yellow
Write-Host ""
Write-Host "Backup Location: $backupDir" -ForegroundColor Green
Write-Host ""

if ($stats.FilesModified -gt 0) {
    Write-Host "[SUCCESS] Namespace fix completed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "[NEXT STEPS]" -ForegroundColor Cyan
    Write-Host "1. Test your application thoroughly" -ForegroundColor White
    Write-Host "2. If issues occur, restore from: $backupDir" -ForegroundColor White
    Write-Host "3. Check for any non-existent models that need manual review" -ForegroundColor White
} else {
    Write-Host "[INFO] No changes were made. All namespaces are already correct." -ForegroundColor Green
}

Write-Host ""
Write-Host "[NOTE] Some models may not exist and need manual review:" -ForegroundColor Yellow
Write-Host "  - Company, Provider, Package, LoginLog, HolidayTheme" -ForegroundColor Gray
Write-Host "  - MediaImage, Education, Markup" -ForegroundColor Gray
Write-Host "  - TestSeriesTransactionHistory, PurchasedSubject, etc." -ForegroundColor Gray
Write-Host ""
Write-Host "Use grep to find these: " -ForegroundColor White -NoNewline
Write-Host "grep -r 'use App\\Company' app/" -ForegroundColor Gray
Write-Host ""
