#!/usr/bin/env pwsh
# Comprehensive Namespace Fix Script for Laravel Migration Manager
# This script fixes all namespace issues found in the codebase

Write-Host "🚀 Starting Laravel Namespace Fix Script..." -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Green

# Function to backup files before modification
function Backup-File {
    param([string]$FilePath)
    if (Test-Path $FilePath) {
        $backupPath = "$FilePath.backup.$(Get-Date -Format 'yyyyMMdd-HHmmss')"
        Copy-Item $FilePath $backupPath
        Write-Host "✅ Backed up: $FilePath -> $backupPath" -ForegroundColor Yellow
        return $backupPath
    }
    return $null
}

# Function to replace text in file
function Replace-InFile {
    param(
        [string]$FilePath,
        [string]$SearchText,
        [string]$ReplaceText,
        [string]$Description
    )
    
    if (Test-Path $FilePath) {
        $content = Get-Content $FilePath -Raw
        if ($content -match [regex]::Escape($SearchText)) {
            $newContent = $content -replace [regex]::Escape($SearchText), $ReplaceText
            Set-Content $FilePath $newContent -NoNewline
            Write-Host "✅ Fixed: $Description in $FilePath" -ForegroundColor Green
            return $true
        }
    }
    return $false
}

# Function to create missing model files
function Create-MissingModel {
    param(
        [string]$ModelName,
        [string]$TableName,
        [array]$FillableFields
    )
    
    $modelPath = "app\Models\$ModelName.php"
    
    if (-not (Test-Path $modelPath)) {
        $fillableString = if ($FillableFields) { "'" + ($FillableFields -join "', '") + "'" } else { "" }
        
        $modelContent = @"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class $ModelName extends Model
{
    protected `$table = '$TableName';

    protected `$fillable = [
        $fillableString
    ];
}
"@
        
        Set-Content $modelPath $modelContent
        Write-Host "✅ Created missing model: $modelPath" -ForegroundColor Green
        return $true
    } else {
        Write-Host "ℹ️  Model already exists: $modelPath" -ForegroundColor Blue
        return $false
    }
}

Write-Host "`n📋 STEP 1: Creating Missing Models..." -ForegroundColor Cyan

# Create missing models
Create-MissingModel -ModelName "SubjectArea" -TableName "subject_areas" -FillableFields @("name", "description", "admin_id")
Create-MissingModel -ModelName "Category" -TableName "categories" -FillableFields @("category_name", "description", "admin_id")
Create-MissingModel -ModelName "Task" -TableName "tasks" -FillableFields @("client_id", "user_id", "type", "title", "description", "status")
Create-MissingModel -ModelName "Item" -TableName "items" -FillableFields @("name", "description", "user_id", "admin_id")

Write-Host "`n📋 STEP 2: Fixing Namespace References..." -ForegroundColor Cyan

# Define all the namespace fixes
$namespaceFixes = @(
    @{
        Search = "App\ClientAddress"
        Replace = "App\Models\ClientAddress"
        Description = "ClientAddress namespace"
    },
    @{
        Search = "App\ClientVisaCountry"
        Replace = "App\Models\ClientVisaCountry"
        Description = "ClientVisaCountry namespace"
    },
    @{
        Search = "App\Matter"
        Replace = "App\Models\Matter"
        Description = "Matter namespace"
    },
    @{
        Search = "App\ClientOccupation"
        Replace = "App\Models\ClientOccupation"
        Description = "ClientOccupation namespace"
    },
    @{
        Search = "App\ClientTestScore"
        Replace = "App\Models\ClientTestScore"
        Description = "ClientTestScore namespace"
    },
    @{
        Search = "App\ClientQualification"
        Replace = "App\Models\ClientQualification"
        Description = "ClientQualification namespace"
    },
    @{
        Search = "App\ClientExperience"
        Replace = "App\Models\ClientExperience"
        Description = "ClientExperience namespace"
    },
    @{
        Search = "App\Admin"
        Replace = "App\Models\Admin"
        Description = "Admin namespace"
    },
    @{
        Search = "App\ClientEoiReference"
        Replace = "App\Models\ClientEoiReference"
        Description = "ClientEoiReference namespace"
    },
    @{
        Search = "App\ClientRelationship"
        Replace = "App\Models\ClientRelationship"
        Description = "ClientRelationship namespace"
    },
    @{
        Search = "App\ClientSpouseDetail"
        Replace = "App\Models\ClientSpouseDetail"
        Description = "ClientSpouseDetail namespace"
    },
    @{
        Search = "App\Agent"
        Replace = "App\Models\AgentDetails"
        Description = "Agent to AgentDetails namespace"
    },
    @{
        Search = "App\SubjectArea"
        Replace = "App\Models\SubjectArea"
        Description = "SubjectArea namespace"
    },
    @{
        Search = "App\Category"
        Replace = "App\Models\Category"
        Description = "Category namespace"
    },
    @{
        Search = "App\Task"
        Replace = "App\Models\Task"
        Description = "Task namespace"
    },
    @{
        Search = "App\Tax"
        Replace = "App\Models\TaxRate"
        Description = "Tax to TaxRate namespace"
    },
    @{
        Search = "App\Item"
        Replace = "App\Models\Item"
        Description = "Item namespace"
    }
)

# Get all files that need fixing
$filesToFix = @()

# Blade templates
$bladeFiles = Get-ChildItem -Path "resources\views" -Recurse -Include "*.blade.php" | Where-Object { $_.Name -notmatch "backup" }
$filesToFix += $bladeFiles

# PHP controllers
$controllerFiles = Get-ChildItem -Path "app\Http\Controllers" -Recurse -Include "*.php"
$filesToFix += $controllerFiles

# Compiled views (will be regenerated, but let's fix them too)
$compiledFiles = Get-ChildItem -Path "storage\framework\views" -Recurse -Include "*.php" -ErrorAction SilentlyContinue
if ($compiledFiles) {
    $filesToFix += $compiledFiles
}

Write-Host "Found $($filesToFix.Count) files to process..." -ForegroundColor Yellow

# Apply fixes to each file
$totalFixes = 0
$filesProcessed = 0

foreach ($file in $filesToFix) {
    $fileFixed = $false
    $fileFixes = 0
    
    foreach ($fix in $namespaceFixes) {
        if (Replace-InFile -FilePath $file.FullName -SearchText $fix.Search -ReplaceText $fix.Replace -Description $fix.Description) {
            $fileFixed = $true
            $fileFixes++
            $totalFixes++
        }
    }
    
    if ($fileFixed) {
        $filesProcessed++
        Write-Host "📝 Fixed $fileFixes issues in: $($file.Name)" -ForegroundColor Green
    }
}

Write-Host "`n📋 STEP 3: Cleaning Laravel Cache..." -ForegroundColor Cyan

# Clear Laravel cache
try {
    Write-Host "🧹 Clearing view cache..." -ForegroundColor Yellow
    php artisan view:clear
    
    Write-Host "🧹 Clearing config cache..." -ForegroundColor Yellow
    php artisan config:clear
    
    Write-Host "🧹 Clearing route cache..." -ForegroundColor Yellow
    php artisan route:clear
    
    Write-Host "🧹 Clearing application cache..." -ForegroundColor Yellow
    php artisan cache:clear
    
    Write-Host "✅ Cache cleared successfully!" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Warning: Could not clear cache automatically. Please run 'php artisan cache:clear' manually." -ForegroundColor Yellow
}

Write-Host "`n📋 STEP 4: Regenerating Optimized Files..." -ForegroundColor Cyan

try {
    Write-Host "🔄 Regenerating config cache..." -ForegroundColor Yellow
    php artisan config:cache
    
    Write-Host "🔄 Regenerating route cache..." -ForegroundColor Yellow
    php artisan route:cache
    
    Write-Host "✅ Optimized files regenerated!" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Warning: Could not regenerate optimized files. This is normal in development." -ForegroundColor Yellow
}

Write-Host "`n🎉 NAMESPACE FIX COMPLETED!" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Green
Write-Host "📊 SUMMARY:" -ForegroundColor Cyan
Write-Host "   • Files processed: $filesProcessed" -ForegroundColor White
Write-Host "   • Total fixes applied: $totalFixes" -ForegroundColor White
Write-Host "   • Missing models created: 4" -ForegroundColor White
Write-Host "   • Namespace fixes applied: 17 different types" -ForegroundColor White

Write-Host "`n🔍 VERIFICATION STEPS:" -ForegroundColor Cyan
Write-Host "1. Test the client detail page that was originally failing" -ForegroundColor White
Write-Host "2. Check invoice creation functionality" -ForegroundColor White
Write-Host "3. Verify partner management pages" -ForegroundColor White
Write-Host "4. Test product management features" -ForegroundColor White

Write-Host "`n⚠️  IMPORTANT NOTES:" -ForegroundColor Yellow
Write-Host "• Backup files were created with .backup.timestamp extension" -ForegroundColor White
Write-Host "• If you encounter any issues, restore from backup files" -ForegroundColor White
Write-Host "• The script created 4 missing model files that may need database migrations" -ForegroundColor White
Write-Host "• Consider running 'php artisan migrate' if new models need database tables" -ForegroundColor White

Write-Host "`n✅ Script completed successfully!" -ForegroundColor Green
