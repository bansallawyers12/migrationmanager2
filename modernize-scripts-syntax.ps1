# PowerShell Script to Modernize Laravel Scripts Syntax
# Converts @yield('scripts') to @stack('scripts') in layouts
# Converts @section('scripts') to @push('scripts') in views

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "Laravel Scripts Syntax Modernization Script" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Counter for changes
$layoutChanges = 0
$viewChanges = 0
$errors = 0

# Step 1: Update Layout Files - @yield('scripts') to @stack('scripts')
Write-Host "Step 1: Updating Layout Files..." -ForegroundColor Yellow
Write-Host ""

$layoutFiles = @(
    "resources\views\layouts\admin_client_detail.blade.php",
    "resources\views\layouts\admin_client_detail_appointment.blade.php",
    "resources\views\layouts\emailmanager.blade.php",
    "resources\views\layouts\admin.blade.php"
)

foreach ($file in $layoutFiles) {
    if (Test-Path $file) {
        try {
            $content = Get-Content $file -Raw -Encoding UTF8
            $originalContent = $content
            
            # Replace @yield('scripts') with @stack('scripts')
            $content = $content -replace "@yield\('scripts'\)", "@stack('scripts')"
            
            if ($content -ne $originalContent) {
                Set-Content -Path $file -Value $content -Encoding UTF8 -NoNewline
                Write-Host "  ✓ Updated: $file" -ForegroundColor Green
                $layoutChanges++
            } else {
                Write-Host "  - No changes needed: $file" -ForegroundColor Gray
            }
        } catch {
            Write-Host "  ✗ Error processing: $file" -ForegroundColor Red
            Write-Host "    Error: $_" -ForegroundColor Red
            $errors++
        }
    } else {
        Write-Host "  ✗ File not found: $file" -ForegroundColor Red
        $errors++
    }
}

Write-Host ""
Write-Host "Step 2: Updating View Files..." -ForegroundColor Yellow
Write-Host ""

# Step 2: Update View Files - @section('scripts') to @push('scripts')
$viewFiles = @(
    "resources\views\Admin\clients\detail.blade.php",
    "resources\views\AdminConsole\system\users\view.blade.php",
    "resources\views\AdminConsole\system\roles\edit.blade.php",
    "resources\views\AdminConsole\system\roles\create.blade.php",
    "resources\views\AdminConsole\features\workflow\index.blade.php",
    "resources\views\AdminConsole\features\workflow\edit.blade.php",
    "resources\views\AdminConsole\features\workflow\create.blade.php",
    "resources\views\AdminConsole\features\visadocumenttype\index.blade.php",
    "resources\views\AdminConsole\features\visadocumenttype\edit.blade.php",
    "resources\views\AdminConsole\features\tags\index.blade.php",
    "resources\views\AdminConsole\features\personaldocumenttype\index.blade.php",
    "resources\views\AdminConsole\features\personaldocumenttype\edit.blade.php",
    "resources\views\AdminConsole\features\matter\index.blade.php",
    "resources\views\AdminConsole\features\documentchecklist\index.blade.php",
    "resources\views\AdminConsole\database\anzsco\index.blade.php",
    "resources\views\Admin\uploadchecklist\index.blade.php",
    "resources\views\AdminConsole\database\anzsco\import.blade.php",
    "resources\views\AdminConsole\database\anzsco\form.blade.php",
    "resources\views\Admin\leads\history.blade.php",
    "resources\views\Admin\clients\clientsemaillist.blade.php",
    "resources\views\Admin\assignee\index.blade.php",
    "resources\views\Admin\checklist\index.blade.php",
    "resources\views\Admin\applications\migrationindex.blade.php",
    "resources\views\Admin\applications\index.blade.php",
    "resources\views\Admin\applications\detail.blade.php",
    "resources\views\Admin\appointments_api\edit.blade.php",
    "resources\views\Admin\appointments_api\calender.blade.php",
    "resources\views\Admin\appointments\calender.blade.php",
    "resources\views\Admin\appointments_api\index.blade.php",
    "resources\views\Admin\appointments\index.blade.php",
    "resources\views\Admin\appointments\edit.blade.php",
    "resources\views\Admin\clients\clientreceiptlist.blade.php",
    "resources\views\Admin\leads\index.blade.php",
    "resources\views\Admin\clients\journalreceiptlist.blade.php",
    "resources\views\Admin\archived\index.blade.php",
    "resources\views\Admin\officevisits\waiting.blade.php",
    "resources\views\Admin\officevisits\index.blade.php",
    "resources\views\Admin\officevisits\completed.blade.php",
    "resources\views\Admin\officevisits\attending.blade.php",
    "resources\views\Admin\officevisits\archived.blade.php",
    "resources\views\Admin\my_profile.blade.php",
    "resources\views\Admin\clients\officereceiptlist.blade.php",
    "resources\views\Admin\clients\invoicelist.blade.php",
    "resources\views\Admin\clients\index.blade.php",
    "resources\views\Admin\clients\clientsmatterslist.blade.php",
    "resources\views\Admin\assignee\completed.blade.php",
    "resources\views\Admin\assignee\assign_to_me.blade.php",
    "resources\views\Admin\assignee\assign_by_me.blade.php",
    "resources\views\Admin\assignee\activities_completed.blade.php",
    "resources\views\Admin\assignee\activities.blade.php"
)

foreach ($file in $viewFiles) {
    if (Test-Path $file) {
        try {
            $content = Get-Content $file -Raw -Encoding UTF8
            $originalContent = $content
            
            # Replace @section('scripts') with @push('scripts')
            $content = $content -replace "@section\('scripts'\)", "@push('scripts')"
            
            # Replace @endsection (that comes after scripts section) with @endpush
            # This is a more careful replacement - only replace @endsection that follows @push('scripts')
            $content = $content -replace "(@push\('scripts'\)[\s\S]*?)@endsection", '$1@endpush'
            
            if ($content -ne $originalContent) {
                Set-Content -Path $file -Value $content -Encoding UTF8 -NoNewline
                Write-Host "  ✓ Updated: $file" -ForegroundColor Green
                $viewChanges++
            } else {
                Write-Host "  - No changes needed: $file" -ForegroundColor Gray
            }
        } catch {
            Write-Host "  ✗ Error processing: $file" -ForegroundColor Red
            Write-Host "    Error: $_" -ForegroundColor Red
            $errors++
        }
    } else {
        Write-Host "  ✗ File not found: $file" -ForegroundColor Red
        $errors++
    }
}

# Summary
Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "Migration Summary" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "Layout files updated: $layoutChanges / 4" -ForegroundColor $(if ($layoutChanges -eq 4) { "Green" } else { "Yellow" })
Write-Host "View files updated: $viewChanges / 50" -ForegroundColor $(if ($viewChanges -eq 50) { "Green" } else { "Yellow" })
Write-Host "Errors encountered: $errors" -ForegroundColor $(if ($errors -eq 0) { "Green" } else { "Red" })
Write-Host ""

if ($errors -eq 0 -and $layoutChanges -gt 0 -or $viewChanges -gt 0) {
    Write-Host "✓ Migration completed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Yellow
    Write-Host "  1. Test your application to ensure scripts load correctly" -ForegroundColor White
    Write-Host "  2. Check for any custom @section('scripts') in other views" -ForegroundColor White
    Write-Host "  3. Commit changes if everything works as expected" -ForegroundColor White
} elseif ($errors -eq 0) {
    Write-Host "No changes were needed - files already use modern syntax." -ForegroundColor Green
} else {
    Write-Host "⚠ Migration completed with errors. Please review the error messages above." -ForegroundColor Yellow
}

Write-Host ""

