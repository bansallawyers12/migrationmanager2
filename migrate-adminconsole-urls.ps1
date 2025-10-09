# AdminConsole URL Migration Script
# This script automates the migration from /admin/ URLs to /adminconsole/ URLs

param(
    [switch]$DryRun = $false,
    [switch]$Backup = $true,
    [switch]$Rollback = $false
)

# Color output functions
function Write-Success { param($msg) Write-Host $msg -ForegroundColor Green }
function Write-Info { param($msg) Write-Host $msg -ForegroundColor Cyan }
function Write-Warning { param($msg) Write-Host $msg -ForegroundColor Yellow }
function Write-Error { param($msg) Write-Host $msg -ForegroundColor Red }

# Counters
$script:filesModified = 0
$script:replacementsMade = 0
$script:changeLog = @()

# Route mapping configuration
$routeMappings = @{
    # Features routes
    'admin.matter' = 'adminconsole.features.matter'
    'admin.tags' = 'adminconsole.features.tags'
    'admin.workflow' = 'adminconsole.features.workflow'
    'admin.emails' = 'adminconsole.features.emails'
    'admin.feature.appointmentdisabledate' = 'adminconsole.features.appointmentdisabledate'
    'admin.feature.promocode' = 'adminconsole.features.promocode'
    'admin.feature.crmemailtemplate' = 'adminconsole.features.crmemailtemplate'
    'admin.feature.matteremailtemplate' = 'adminconsole.features.matteremailtemplate'
    'admin.feature.matterotheremailtemplate' = 'adminconsole.features.matterotheremailtemplate'
    'admin.feature.personaldocumenttype' = 'adminconsole.features.personaldocumenttype'
    'admin.feature.visadocumenttype' = 'adminconsole.features.visadocumenttype'
    'admin.feature.documentchecklist' = 'adminconsole.features.documentchecklist'
    'admin.feature.profile' = 'adminconsole.features.profile'
    
    # System routes
    'admin.users' = 'adminconsole.system.users'
    'admin.userrole' = 'adminconsole.system.roles'
    'admin.team' = 'adminconsole.system.teams'
    'admin.branches' = 'adminconsole.system.offices'
    'admin.system.settings' = 'adminconsole.system.settings'
    
    # Database routes
    'admin.anzsco' = 'adminconsole.database.anzsco'
}

# URL path mappings for URL::to() conversions
$urlPathMappings = @{
    '/admin/matter' = 'adminconsole.features.matter.index'
    '/admin/matter/create' = 'adminconsole.features.matter.create'
    '/admin/matter/edit' = 'adminconsole.features.matter.edit'
    '/admin/matter_email_template' = 'adminconsole.features.matteremailtemplate.index'
    '/admin/matter_email_template/create' = 'adminconsole.features.matteremailtemplate.create'
    '/admin/matter_email_template/edit' = 'adminconsole.features.matteremailtemplate.edit'
    
    '/admin/tags' = 'adminconsole.features.tags.index'
    '/admin/tags/create' = 'adminconsole.features.tags.create'
    '/admin/tags/edit' = 'adminconsole.features.tags.edit'
    
    '/admin/workflow' = 'adminconsole.features.workflow.index'
    '/admin/workflow/create' = 'adminconsole.features.workflow.create'
    '/admin/workflow/edit' = 'adminconsole.features.workflow.edit'
    
    '/admin/emails' = 'adminconsole.features.emails.index'
    '/admin/emails/create' = 'adminconsole.features.emails.create'
    '/admin/emails/edit' = 'adminconsole.features.emails.edit'
    
    '/admin/users' = 'adminconsole.system.users.index'
    '/admin/users/active' = 'adminconsole.system.users.active'
    '/admin/users/inactive' = 'adminconsole.system.users.inactive'
    '/admin/users/invited' = 'adminconsole.system.users.invited'
    '/admin/users/create' = 'adminconsole.system.users.create'
    '/admin/users/edit' = 'adminconsole.system.users.edit'
    '/admin/users/clientlist' = 'adminconsole.system.users.clientlist'
    '/admin/users/createclient' = 'adminconsole.system.users.createclient'
    '/admin/users/editclient' = 'adminconsole.system.users.editclient'
    
    '/admin/userrole' = 'adminconsole.system.roles.index'
    '/admin/userrole/create' = 'adminconsole.system.roles.create'
    '/admin/userrole/edit' = 'adminconsole.system.roles.edit'
    
    '/admin/team' = 'adminconsole.system.teams.index'
    '/admin/team/edit' = 'adminconsole.system.teams.edit'
    
    '/admin/branches' = 'adminconsole.system.offices.index'
    '/admin/branches/create' = 'adminconsole.system.offices.create'
    '/admin/branches/edit' = 'adminconsole.system.offices.edit'
}

function Backup-File {
    param([string]$filePath)
    
    if ($Backup -and -not $DryRun) {
        $backupPath = "$filePath.backup"
        Copy-Item $filePath $backupPath -Force
        Write-Info "  Created backup: $backupPath"
    }
}

function Process-BladeFile {
    param([string]$filePath)
    
    $content = Get-Content $filePath -Raw -Encoding UTF8
    $originalContent = $content
    $fileChanged = $false
    $fileReplacements = 0
    
    # Pattern 1: Replace route('admin.*') with route('adminconsole.*')
    foreach ($oldRoute in $routeMappings.Keys) {
        $newRoute = $routeMappings[$oldRoute]
        
        # Match route('admin.*') or route("admin.*")
        $pattern1 = "route\(['\`"]$oldRoute"
        $replacement1 = "route('$newRoute"
        
        if ($content -match [regex]::Escape($pattern1)) {
            $content = $content -replace [regex]::Escape($pattern1), $replacement1
            $fileReplacements++
            $script:changeLog += "  [$filePath] route('$oldRoute') → route('$newRoute')"
        }
    }
    
    # Pattern 2: Replace URL::to('/admin/...') with route('adminconsole.*')
    # Handle URLs with parameters
    $urlPattern = "URL::to\(['\`"]\/admin\/([^'`"]+)['\`"]\)"
    $matches = [regex]::Matches($content, $urlPattern)
    
    foreach ($match in $matches) {
        $fullMatch = $match.Value
        $urlPath = $match.Groups[1].Value
        
        # Try to find exact match first
        $adminPath = "/admin/$urlPath"
        
        # Check for exact match
        $found = $false
        foreach ($key in $urlPathMappings.Keys) {
            if ($adminPath -eq $key) {
                $newRoute = $urlPathMappings[$key]
                $newCode = "route('$newRoute')"
                $content = $content.Replace($fullMatch, $newCode)
                $fileReplacements++
                $script:changeLog += "  [$filePath] URL::to('$adminPath') → route('$newRoute')"
                $found = $true
                break
            }
        }
        
        # Handle paths with parameters (e.g., /admin/users/edit/{id})
        if (-not $found) {
            # Extract base path and parameter
            if ($urlPath -match '^([^/]+(?:/[^/]+)*)/(.+)$') {
                $basePath = "/admin/$($matches.Groups[1].Value)"
                $param = $matches.Groups[2].Value
                
                foreach ($key in $urlPathMappings.Keys) {
                    if ($basePath -like "$key*" -or $key -like "$basePath*") {
                        $newRoute = $urlPathMappings[$key]
                        # Check if parameter looks like a variable or encoded value
                        if ($param -match '[\$\{]|base64|convert_uuencode') {
                            $newCode = "route('$newRoute', $param)"
                        } else {
                            $newCode = "route('$newRoute', '$param')"
                        }
                        $content = $content.Replace($fullMatch, $newCode)
                        $fileReplacements++
                        $script:changeLog += "  [$filePath] URL::to('$adminPath') → route('$newRoute', param)"
                        $found = $true
                        break
                    }
                }
            }
        }
    }
    
    # Pattern 3: Replace action="/admin/..." with action="{{ route('adminconsole.*') }}"
    $actionPattern = 'action=["'']\/admin\/([^"'']+)["'']'
    $actionMatches = [regex]::Matches($content, $actionPattern)
    
    foreach ($match in $actionMatches) {
        $fullMatch = $match.Value
        $urlPath = $match.Groups[1].Value
        $adminPath = "/admin/$urlPath"
        
        foreach ($key in $urlPathMappings.Keys) {
            if ($adminPath -eq $key -or $adminPath -like "$key/*") {
                $newRoute = $urlPathMappings[$key]
                $newCode = "action=`"{{ route('$newRoute') }}`""
                $content = $content.Replace($fullMatch, $newCode)
                $fileReplacements++
                $script:changeLog += "  [$filePath] action='$adminPath' → action=`"{{ route('$newRoute') }}`""
                break
            }
        }
    }
    
    # Pattern 4: Update Route::currentRouteName() checks
    foreach ($oldRoute in $routeMappings.Keys) {
        $newRoute = $routeMappings[$oldRoute]
        
        # Match patterns like: Route::currentRouteName() == 'admin.*'
        $pattern = "== ['\`"]$oldRoute"
        $replacement = "== '$newRoute"
        
        if ($content -match [regex]::Escape($pattern)) {
            $content = $content -replace [regex]::Escape($pattern), $replacement
            $fileReplacements++
            $script:changeLog += "  [$filePath] Route check: '$oldRoute' → '$newRoute'"
        }
    }
    
    # Check if content changed
    if ($content -ne $originalContent) {
        $fileChanged = $true
        $script:filesModified++
        $script:replacementsMade += $fileReplacements
        
        if (-not $DryRun) {
            Backup-File $filePath
            Set-Content $filePath -Value $content -Encoding UTF8 -NoNewline
            Write-Success "[OK] Modified: $filePath ($fileReplacements replacements)"
        } else {
            Write-Info "[OK] Would modify: $filePath ($fileReplacements replacements)"
        }
    }
    
    return $fileChanged
}

function Restore-Backups {
    Write-Info "`nRestoring files from backups..."
    
    $backupFiles = Get-ChildItem -Path "resources\views" -Filter "*.backup" -Recurse
    
    foreach ($backup in $backupFiles) {
        $originalFile = $backup.FullName -replace '\.backup$', ''
        Copy-Item $backup.FullName $originalFile -Force
        Remove-Item $backup.FullName -Force
        Write-Success "  Restored: $originalFile"
    }
    
    Write-Success "`nRollback complete!"
}

# Main execution
Write-Info "==================================================="
Write-Info "  AdminConsole URL Migration Script"
Write-Info "==================================================="

if ($Rollback) {
    Restore-Backups
    exit 0
}

if ($DryRun) {
    Write-Warning "`n*** DRY RUN MODE - No files will be modified ***`n"
}

Write-Info "`nScanning blade files..."

# Process all blade files in resources/views
$bladeFiles = Get-ChildItem -Path "resources\views" -Filter "*.blade.php" -Recurse

Write-Info "Found $($bladeFiles.Count) blade files to process`n"

foreach ($file in $bladeFiles) {
    Process-BladeFile $file.FullName
}

# Generate report
Write-Info "`n==================================================="
Write-Info "  Migration Summary"
Write-Info "==================================================="
Write-Success "Files modified: $script:filesModified"
Write-Success "Total replacements: $script:replacementsMade"

if ($script:changeLog.Count -gt 0) {
    Write-Info "`nDetailed Changes:"
    $script:changeLog | ForEach-Object { Write-Info $_ }
}

if ($DryRun) {
    Write-Warning "`n*** This was a DRY RUN - No files were actually modified ***"
    Write-Info "Run without -DryRun flag to apply changes"
} else {
    Write-Success "`n[OK] Migration complete!"
    if ($Backup) {
        Write-Info "Backup files created with .backup extension"
        Write-Info "To rollback: .\migrate-adminconsole-urls.ps1 -Rollback"
    }
}

Write-Info "`nNext steps:"
Write-Info "1. Review the changes in your git diff"
Write-Info "2. Update routes/web.php with redirects"
Write-Info "3. Clear Laravel caches"
Write-Info "4. Test the application"

