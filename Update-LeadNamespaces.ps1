# Lead Controller Namespace Update Script for PowerShell
# This script updates all references from old LeadController to new namespace structure

param(
    [switch]$DryRun = $false,
    [switch]$Verbose = $false
)

Write-Host "Starting Lead Controller Namespace Update..." -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green

if ($DryRun) {
    Write-Host "DRY RUN MODE - No files will be modified" -ForegroundColor Yellow
}

# Function to update files with pattern replacement
function Update-Files {
    param(
        [string]$Pattern,
        [string]$Replacement,
        [string]$Path = ".",
        [string[]]$Include = @("*.php", "*.blade.php", "*.js", "*.md"),
        [string[]]$Exclude = @("vendor", "node_modules", ".git")
    )
    
    Write-Host "Searching for: $Pattern" -ForegroundColor Cyan
    
    $files = Get-ChildItem -Path $Path -Include $Include -Recurse | 
        Where-Object { 
            $exclude = $false
            foreach ($excludePath in $Exclude) {
                if ($_.FullName -like "*$excludePath*") {
                    $exclude = $true
                    break
                }
            }
            return -not $exclude
        }
    
    $updatedCount = 0
    
    foreach ($file in $files) {
        try {
            $content = Get-Content $file.FullName -Raw -ErrorAction Stop
            
            if ($content -match [regex]::Escape($Pattern)) {
                if ($Verbose) {
                    Write-Host "  Found in: $($file.FullName)" -ForegroundColor Gray
                }
                
                if (-not $DryRun) {
                    $newContent = $content -replace [regex]::Escape($Pattern), $Replacement
                    Set-Content -Path $file.FullName -Value $newContent -NoNewline
                }
                
                Write-Host "  ✓ Updated: $($file.Name)" -ForegroundColor Green
                $updatedCount++
            }
        }
        catch {
            Write-Host "  ✗ Error processing: $($file.FullName) - $($_.Exception.Message)" -ForegroundColor Red
        }
    }
    
    Write-Host "  Updated $updatedCount files" -ForegroundColor Yellow
    return $updatedCount
}

# Main update operations
$totalUpdates = 0

Write-Host "`n1. Updating route files..." -ForegroundColor Magenta
$totalUpdates += Update-Files -Pattern "Admin\\LeadController@assign" -Replacement "Admin\\Leads\\LeadAssignmentController@assign" -Path "routes" -Include @("*.php")
$totalUpdates += Update-Files -Pattern "Admin\\LeadController@convertoClient" -Replacement "Admin\\Leads\\LeadConversionController@convertToClient" -Path "routes" -Include @("*.php")
$totalUpdates += Update-Files -Pattern "Admin\\LeadController@convertToClient" -Replacement "Admin\\Leads\\LeadConversionController@convertToClient" -Path "routes" -Include @("*.php")

Write-Host "`n2. Updating PHP files for namespace references..." -ForegroundColor Magenta
$totalUpdates += Update-Files -Pattern "use App\\Http\\Controllers\\Admin\\LeadController" -Replacement "use App\\Http\\Controllers\\Admin\\Leads\\LeadController" -Include @("*.php")
$totalUpdates += Update-Files -Pattern "App\\Http\\Controllers\\Admin\\LeadController" -Replacement "App\\Http\\Controllers\\Admin\\Leads\\LeadController" -Include @("*.php")
$totalUpdates += Update-Files -Pattern "Admin\\LeadController@" -Replacement "Admin\\Leads\\LeadController@" -Include @("*.php")

Write-Host "`n3. Updating Blade template files..." -ForegroundColor Magenta
$totalUpdates += Update-Files -Pattern 'action("Admin\\LeadController@assign")' -Replacement 'action("Admin\\Leads\\LeadAssignmentController@assign")' -Path "resources/views" -Include @("*.blade.php")
$totalUpdates += Update-Files -Pattern 'action("Admin\\LeadController@convertoClient")' -Replacement 'action("Admin\\Leads\\LeadConversionController@convertToClient")' -Path "resources/views" -Include @("*.blade.php")
$totalUpdates += Update-Files -Pattern 'action("Admin\\LeadController@convertToClient")' -Replacement 'action("Admin\\Leads\\LeadConversionController@convertToClient")' -Path "resources/views" -Include @("*.blade.php")

Write-Host "`n4. Updating JavaScript files..." -ForegroundColor Magenta
$totalUpdates += Update-Files -Pattern "LeadController" -Replacement "LeadController" -Path "public/js" -Include @("*.js")
$totalUpdates += Update-Files -Pattern "LeadController" -Replacement "LeadController" -Path "resources/js" -Include @("*.js")

Write-Host "`n5. Updating documentation files..." -ForegroundColor Magenta
$totalUpdates += Update-Files -Pattern "Admin\\LeadController" -Replacement "Admin\\Leads\\LeadController" -Include @("*.md")

Write-Host "`n6. Clearing Laravel caches..." -ForegroundColor Magenta
if (-not $DryRun) {
    try {
        Write-Host "  Clearing route cache..." -ForegroundColor Gray
        php artisan route:clear
        
        Write-Host "  Clearing config cache..." -ForegroundColor Gray
        php artisan config:clear
        
        Write-Host "  Clearing application cache..." -ForegroundColor Gray
        php artisan cache:clear
        
        Write-Host "  Clearing view cache..." -ForegroundColor Gray
        php artisan view:clear
        
        Write-Host "  ✓ Caches cleared successfully" -ForegroundColor Green
    }
    catch {
        Write-Host "  ✗ Error clearing caches: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "`n7. Regenerating autoload files..." -ForegroundColor Magenta
if (-not $DryRun) {
    try {
        Write-Host "  Running composer dump-autoload..." -ForegroundColor Gray
        composer dump-autoload
        Write-Host "  ✓ Autoload files regenerated" -ForegroundColor Green
    }
    catch {
        Write-Host "  ✗ Error regenerating autoload: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "`n==============================================" -ForegroundColor Green
Write-Host "Update completed!" -ForegroundColor Green
Write-Host "Total updates made: $totalUpdates" -ForegroundColor Yellow

if ($DryRun) {
    Write-Host "`nThis was a dry run. To apply changes, run the script without -DryRun parameter." -ForegroundColor Yellow
}

Write-Host "`nNext steps:" -ForegroundColor Cyan
Write-Host "1. Test the application functionality" -ForegroundColor White
Write-Host "2. Check for any remaining references manually" -ForegroundColor White
Write-Host "3. Verify all routes are working correctly" -ForegroundColor White

Write-Host "`nNew controller structure:" -ForegroundColor Cyan
Write-Host "  - app\Http\Controllers\Admin\Leads\LeadController.php" -ForegroundColor White
Write-Host "  - app\Http\Controllers\Admin\Leads\LeadAssignmentController.php" -ForegroundColor White
Write-Host "  - app\Http\Controllers\Admin\Leads\LeadConversionController.php" -ForegroundColor White

Write-Host "`nScript completed successfully!" -ForegroundColor Green
