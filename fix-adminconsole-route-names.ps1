# Fix AdminConsole Route Names Script
# This script updates old admin.* route names to adminconsole.* route names

$routeMappings = @{
    "admin.feature.matter" = "adminconsole.features.matter"
    "admin.matter" = "adminconsole.features.matter"
    "admin.feature.tags" = "adminconsole.features.tags"
    "admin.tags" = "adminconsole.features.tags"
    "admin.feature.workflow" = "adminconsole.features.workflow"
    "admin.workflow" = "adminconsole.features.workflow"
    "admin.feature.emails" = "adminconsole.features.emails"
    "admin.emails" = "adminconsole.features.emails"
    "admin.feature.appointmentdisabledate" = "adminconsole.features.appointmentdisabledate"
    "admin.feature.promocode" = "adminconsole.features.promocode"
    "admin.feature.crmemailtemplate" = "adminconsole.features.crmemailtemplate"
    "admin.crmemailtemplate" = "adminconsole.features.crmemailtemplate"
    "admin.feature.matteremailtemplate" = "adminconsole.features.matteremailtemplate"
    "admin.matteremailtemplate" = "adminconsole.features.matteremailtemplate"
    "admin.feature.matterotheremailtemplate" = "adminconsole.features.matterotheremailtemplate"
    "admin.matterotheremailtemplate" = "adminconsole.features.matterotheremailtemplate"
    "admin.feature.personaldocumenttype" = "adminconsole.features.personaldocumenttype"
    "admin.personaldocumenttype" = "adminconsole.features.personaldocumenttype"
    "admin.feature.visadocumenttype" = "adminconsole.features.visadocumenttype"
    "admin.visadocumenttype" = "adminconsole.features.visadocumenttype"
    "admin.feature.documentchecklist" = "adminconsole.features.documentchecklist"
    "admin.documentchecklist" = "adminconsole.features.documentchecklist"
    "admin.feature.profile" = "adminconsole.features.profile"
    "admin.profile" = "adminconsole.features.profile"
    "admin.users" = "adminconsole.system.users"
    "admin.userrole" = "adminconsole.system.roles"
    "admin.team" = "adminconsole.system.teams"
    "admin.branches" = "adminconsole.system.offices"
    "admin.anzsco" = "adminconsole.database.anzsco"
}

$filesProcessed = 0
$replacementsMade = 0

Write-Host "Fixing route names in AdminConsole views..." -ForegroundColor Cyan

Get-ChildItem -Path "resources\views\AdminConsole" -Filter "*.blade.php" -Recurse | ForEach-Object {
    $file = $_
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    $originalContent = $content
    $fileChanged = $false
    
    foreach ($oldRoute in $routeMappings.Keys) {
        $newRoute = $routeMappings[$oldRoute]
        
        # Pattern 1: route('admin.*')
        $pattern1 = "route\(['\`"]$oldRoute"
        if ($content -match [regex]::Escape($pattern1)) {
            $content = $content -replace [regex]::Escape($pattern1), "route('$newRoute"
            $replacementsMade++
            $fileChanged = $true
            Write-Host "  $($file.Name): $oldRoute -> $newRoute" -ForegroundColor Yellow
        }
        
        # Pattern 2: Route::currentRouteName() == 'admin.*'
        $pattern2 = "== ['\`"]$oldRoute"
        if ($content -match [regex]::Escape($pattern2)) {
            $content = $content -replace [regex]::Escape($pattern2), "== '$newRoute"
            $replacementsMade++
            $fileChanged = $true
            Write-Host "  $($file.Name): Route check: $oldRoute -> $newRoute" -ForegroundColor Yellow
        }
        
        # Pattern 3: != 'admin.*'
        $pattern3 = "!= ['\`"]$oldRoute"
        if ($content -match [regex]::Escape($pattern3)) {
            $content = $content -replace [regex]::Escape($pattern3), "!= '$newRoute"
            $replacementsMade++
            $fileChanged = $true
            Write-Host "  $($file.Name): Route check: $oldRoute -> $newRoute" -ForegroundColor Yellow
        }
    }
    
    if ($fileChanged) {
        Set-Content $file.FullName -Value $content -Encoding UTF8 -NoNewline
        $filesProcessed++
        Write-Host "  [OK] Updated: $($file.FullName)" -ForegroundColor Green
    }
}

Write-Host "`nSummary:" -ForegroundColor Cyan
Write-Host "Files processed: $filesProcessed" -ForegroundColor Green
Write-Host "Replacements made: $replacementsMade" -ForegroundColor Green
Write-Host "`nDone!" -ForegroundColor Green

