#!/usr/bin/env pwsh
# Script to fix remaining namespace issues in all files

Write-Host "🔧 Fixing Remaining Namespace Issues..." -ForegroundColor Green

# Define the namespace fixes
$fixes = @(
    @{ Search = "App\ClientAddress"; Replace = "App\Models\ClientAddress" },
    @{ Search = "App\ClientVisaCountry"; Replace = "App\Models\ClientVisaCountry" },
    @{ Search = "App\Matter"; Replace = "App\Models\Matter" },
    @{ Search = "App\ClientOccupation"; Replace = "App\Models\ClientOccupation" },
    @{ Search = "App\ClientTestScore"; Replace = "App\Models\ClientTestScore" },
    @{ Search = "App\ClientQualification"; Replace = "App\Models\ClientQualification" },
    @{ Search = "App\ClientExperience"; Replace = "App\Models\ClientExperience" },
    @{ Search = "App\Admin"; Replace = "App\Models\Admin" },
    @{ Search = "App\ClientEoiReference"; Replace = "App\Models\ClientEoiReference" },
    @{ Search = "App\ClientRelationship"; Replace = "App\Models\ClientRelationship" },
    @{ Search = "App\ClientSpouseDetail"; Replace = "App\Models\ClientSpouseDetail" },
    @{ Search = "App\Agent"; Replace = "App\Models\AgentDetails" },
    @{ Search = "App\SubjectArea"; Replace = "App\Models\SubjectArea" },
    @{ Search = "App\Category"; Replace = "App\Models\Category" },
    @{ Search = "App\Task"; Replace = "App\Models\Task" },
    @{ Search = "App\Tax"; Replace = "App\Models\TaxRate" },
    @{ Search = "App\Item"; Replace = "App\Models\Item" }
)

# Get all files to fix
$files = @()
$files += Get-ChildItem -Path "resources\views" -Recurse -Include "*.blade.php"
$files += Get-ChildItem -Path "app\Http\Controllers" -Recurse -Include "*.php"

$totalFixes = 0
$filesProcessed = 0

foreach ($file in $files) {
    $fileFixed = $false
    $fileFixes = 0
    
    foreach ($fix in $fixes) {
        if (Test-Path $file.FullName) {
            $content = Get-Content $file.FullName -Raw
            if ($content -match [regex]::Escape($fix.Search)) {
                $newContent = $content -replace [regex]::Escape($fix.Search), $fix.Replace
                Set-Content $file.FullName $newContent -NoNewline
                $fileFixed = $true
                $fileFixes++
                $totalFixes++
            }
        }
    }
    
    if ($fileFixed) {
        $filesProcessed++
        Write-Host "Fixed $fileFixes issues in: $($file.Name)" -ForegroundColor Green
    }
}

Write-Host "COMPLETED!" -ForegroundColor Green
Write-Host "Files processed: $filesProcessed" -ForegroundColor Cyan
Write-Host "Total fixes applied: $totalFixes" -ForegroundColor Cyan