# Update Admin Routes to AdminConsole Script
# This script updates all admin console-related routes to use AdminConsole controllers

Write-Host "Starting admin route updates to AdminConsole..." -ForegroundColor Green

# Read the web.php file
$webRoutesFile = "routes\web.php"
$content = Get-Content $webRoutesFile -Raw

Write-Host "Updating controller references in routes..." -ForegroundColor Yellow

# Replace old Admin controller references with AdminConsole for moved controllers
# Note: Using single backslash for route string format
$replacements = @{
    "'Admin\\UserController" = "'AdminConsole\\UserController"
    "'Admin\\UserroleController" = "'AdminConsole\\UserroleController"
    "'Admin\\TeamController" = "'AdminConsole\\TeamController"
    "'Admin\\BranchesController" = "'AdminConsole\\BranchesController"
    "'Admin\\MatterController" = "'AdminConsole\\MatterController"
    "'Admin\\TagController" = "'AdminConsole\\TagController"
    "'Admin\\WorkflowController" = "'AdminConsole\\WorkflowController"
    "'Admin\\EmailController" = "'AdminConsole\\EmailController"
    "'Admin\\CrmEmailTemplateController" = "'AdminConsole\\CrmEmailTemplateController"
    "'Admin\\MatterEmailTemplateController" = "'AdminConsole\\MatterEmailTemplateController"
    "'Admin\\MatterOtherEmailTemplateController" = "'AdminConsole\\MatterOtherEmailTemplateController"
    "'Admin\\PersonalDocumentTypeController" = "'AdminConsole\\PersonalDocumentTypeController"
    "'Admin\\VisaDocumentTypeController" = "'AdminConsole\\VisaDocumentTypeController"
    "'Admin\\DocumentChecklistController" = "'AdminConsole\\DocumentChecklistController"
    "'Admin\\AppointmentDisableDateController" = "'AdminConsole\\AppointmentDisableDateController"
    "'Admin\\PromoCodeController" = "'AdminConsole\\PromoCodeController"
    "'Admin\\ProfileController" = "'AdminConsole\\ProfileController"
    "'Admin\\AnzscoOccupationController" = "'AdminConsole\\AnzscoOccupationController"
}

$originalContent = $content
foreach ($old in $replacements.Keys) {
    $new = $replacements[$old]
    $content = $content -replace [regex]::Escape($old), $new
    $count = ([regex]::Matches($originalContent, [regex]::Escape($old))).Count
    if ($count -gt 0) {
        Write-Host "  Replaced $count occurrences of $old" -ForegroundColor Cyan
    }
}

# Write back to file
Set-Content $webRoutesFile $content -NoNewline

Write-Host "Routes file updated successfully!" -ForegroundColor Green

Write-Host "Testing route compilation..." -ForegroundColor Yellow

# Clear caches
Write-Host "Clearing Laravel caches..." -ForegroundColor Yellow
php artisan route:clear 2>&1 | Out-Null
php artisan config:clear 2>&1 | Out-Null
php artisan cache:clear 2>&1 | Out-Null

# Test route compilation
$routeTest = php artisan route:list 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "Routes compiled successfully!" -ForegroundColor Green
    
    # Count adminconsole routes
    $adminconsoleCount = ($routeTest | Select-String "adminconsole").Count
    Write-Host "Found $adminconsoleCount adminconsole routes" -ForegroundColor Cyan
} else {
    Write-Host "Route compilation failed:" -ForegroundColor Red
    Write-Host $routeTest -ForegroundColor Red
}

Write-Host "Admin route update completed!" -ForegroundColor Green
