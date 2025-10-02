# Fix Admin namespace references across the codebase
# Replaces App\Admin with App\Models\Admin in PHP and Blade files

param(
    [string]$Root = (Resolve-Path ".").Path
)

Write-Host "Scanning and fixing Admin namespace under: $Root" -ForegroundColor Cyan

$excludeDirs = @(
    "vendor",
    "public",
    "node_modules",
    "storage",
    "bootstrap\cache",
    "namespace_fix_backup_"
)

function Should-ExcludePath {
    param([string]$Path)
    foreach ($ex in $excludeDirs) {
        if ($Path -like "*${ex}*") { return $true }
    }
    return $false
}

$files = Get-ChildItem -Path $Root -Recurse -File -Include *.php,*.blade.php |
    Where-Object { -not (Should-ExcludePath $_.FullName) }

if (-not $files) {
    Write-Host "No candidate files found." -ForegroundColor Yellow
    exit 0
}

$totalChanged = 0
$changedFiles = @()

foreach ($file in $files) {
    $orig = Get-Content -Raw -LiteralPath $file.FullName
    $updated = $orig

    # Replace occurrences without leading backslash (e.g., 'App\\Admin' or "App\\Admin")
    $updated = $updated -replace 'App\\Admin', 'App\\Models\\Admin'
    # Replace FQCN usages with leading backslash (e.g., \\App\\Admin)
    $updated = $updated -replace '\\App\\Admin', '\\App\\Models\\Admin'

    if ($updated -ne $orig) {
        Set-Content -LiteralPath $file.FullName -Value $updated -NoNewline
        $totalChanged++
        $changedFiles += $file.FullName
    }
}

Write-Host ("Updated {0} file(s)." -f $totalChanged) -ForegroundColor Green
if ($changedFiles.Count -gt 0) {
    Write-Host "Sample changes:" -ForegroundColor DarkGray
    $changedFiles | Select-Object -First 10 | ForEach-Object { Write-Host " - $_" }
}

exit 0


