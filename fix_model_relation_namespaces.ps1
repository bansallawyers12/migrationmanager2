# Normalize relationship model namespaces in app\Models
# Converts 'App\\ModelName' -> 'App\\Models\\ModelName' when used inside quotes

param(
    [string]$Root = (Resolve-Path ".").Path
)

$modelsDir = Join-Path $Root "app\Models"
if (-not (Test-Path $modelsDir)) { exit 0 }

Write-Host "Fixing quoted model references under: $modelsDir" -ForegroundColor Cyan

$files = Get-ChildItem -Path $modelsDir -Recurse -File -Include *.php

$totalChanged = 0
foreach ($file in $files) {
    $orig = Get-Content -Raw -LiteralPath $file.FullName
    $updated = $orig

    # Simple quoted prefix replacements
    $updated = $updated -replace "'App\\", "'App\\Models\\"
    $updated = $updated -replace '"App\\', '"App\\Models\\'
    # Collapse accidental double Models from prior runs
    $updated = $updated -replace "App\\Models\\Models\\", "App\\Models\\"

    if ($updated -ne $orig) {
        Set-Content -LiteralPath $file.FullName -Value $updated -NoNewline
        $totalChanged++
    }
}

Write-Host ("Updated {0} file(s)." -f $totalChanged) -ForegroundColor Green
exit 0


