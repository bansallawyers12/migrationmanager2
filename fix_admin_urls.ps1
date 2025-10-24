# Script: fix_admin_urls.ps1
# Purpose: Remove /admin prefix from hardcoded URLs in views after Admin to CRM refactoring
# Date: October 24, 2025

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Admin URL Fix Script" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

$filesFixed = 0
$totalReplacements = 0

# Define the files to process
$files = @(
    "resources\views\layouts\emailmanager.blade.php",
    "resources\views\layouts\crm_client_detail_appointment.blade.php",
    "resources\views\layouts\crm_client_detail_dashboard.blade.php",
    "resources\views\layouts\crm_client_detail.blade.php",
    "resources\views\Elements\Emailuser\header.blade.php",
    "resources\views\AdminConsole\system\users\view.blade.php",
    "resources\views\crm\signatures\show.blade.php",
    "resources\views\crm\clients\addclientmodal.blade.php",
    "resources\views\crm\booking\appointments\calendar-v6.blade.php",
    "resources\views\crm\email_upload_test.blade.php",
    "resources\views\crm\documents\index.blade.php",
    "resources\views\AdminConsole\database\anzsco\index.blade.php"
)

foreach ($file in $files) {
    if (Test-Path $file) {
        Write-Host "Processing: $file" -ForegroundColor Yellow
        $content = Get-Content $file -Raw
        $originalContent = $content
        $fileReplacements = 0
        
        # Replace all /admin/ patterns (except for login/logout which we want to keep)
        # We'll do simple string replacements to avoid regex escaping issues
        
        # Pattern 1: site_url+'/admin/
        $pattern1 = "site_url+'/admin/"
        $replace1 = "site_url+'/"
        if ($content.Contains($pattern1)) {
            $count = ([regex]::Matches($content, [regex]::Escape($pattern1))).Count
            $content = $content.Replace($pattern1, $replace1)
            $fileReplacements += $count
            Write-Host "  Fixed $count site_url+'/admin/' references" -ForegroundColor Green
        }
        
        # Pattern 2: URL::to('/admin/
        $pattern2 = "URL::to('/admin/"
        $replace2 = "URL::to('/"
        if ($content.Contains($pattern2)) {
            $count = ([regex]::Matches($content, [regex]::Escape($pattern2))).Count
            $content = $content.Replace($pattern2, $replace2)
            $fileReplacements += $count
            Write-Host "  Fixed $count URL::to('/admin/') references" -ForegroundColor Green
        }
        
        # Pattern 3: {{URL::to('/admin/
        $pattern3 = "{{URL::to('/admin/"
        $replace3 = "{{URL::to('/"
        if ($content.Contains($pattern3)) {
            $count = ([regex]::Matches($content, [regex]::Escape($pattern3))).Count
            $content = $content.Replace($pattern3, $replace3)
            $fileReplacements += $count
            Write-Host "  Fixed $count {{{{URL::to('/admin/')}}}} references" -ForegroundColor Green
        }
        
        # Pattern 4: window.location = "/admin/
        $pattern4 = 'window.location = "/admin/'
        $replace4 = 'window.location = "/'
        if ($content.Contains($pattern4)) {
            $count = ([regex]::Matches($content, [regex]::Escape($pattern4))).Count
            $content = $content.Replace($pattern4, $replace4)
            $fileReplacements += $count
            Write-Host "  Fixed $count window.location='/admin/' references" -ForegroundColor Green
        }
        
        # Pattern 5: fetch(`/admin/
        $pattern5 = 'fetch(`/admin/'
        $replace5 = 'fetch(`/'
        if ($content.Contains($pattern5)) {
            $count = ([regex]::Matches($content, [regex]::Escape($pattern5))).Count
            $content = $content.Replace($pattern5, $replace5)
            $fileReplacements += $count
            Write-Host "  Fixed $count fetch(`/admin/`) references" -ForegroundColor Green
        }
        
        # Pattern 6: form.action = `/admin/
        $pattern6 = 'form.action = `/admin/'
        $replace6 = 'form.action = `/'
        if ($content.Contains($pattern6)) {
            $count = ([regex]::Matches($content, [regex]::Escape($pattern6))).Count
            $content = $content.Replace($pattern6, $replace6)
            $fileReplacements += $count
            Write-Host "  Fixed $count form.action=`/admin/` references" -ForegroundColor Green
        }
        
        # Pattern 7: url: '/admin/ (for JS objects)
        # Special case for ANZSCO - should go to /adminconsole
        if ($file -like "*anzsco*") {
            $pattern7 = "url: '/admin/anzsco/"
            $replace7 = "url: '/adminconsole/anzsco/"
            if ($content.Contains($pattern7)) {
                $count = ([regex]::Matches($content, [regex]::Escape($pattern7))).Count
                $content = $content.Replace($pattern7, $replace7)
                $fileReplacements += $count
                Write-Host "  Fixed $count url:'/admin/anzsco/' to '/adminconsole/anzsco/' (AdminConsole)" -ForegroundColor Green
            }
        } else {
            $pattern7 = "url: '/admin/"
            $replace7 = "url: '/"
            if ($content.Contains($pattern7)) {
                $count = ([regex]::Matches($content, [regex]::Escape($pattern7))).Count
                $content = $content.Replace($pattern7, $replace7)
                $fileReplacements += $count
                Write-Host "  Fixed $count url:'/admin/' references" -ForegroundColor Green
            }
        }
        
        if ($content -ne $originalContent) {
            Set-Content $file $content -NoNewline
            Write-Host "  File updated: $fileReplacements replacements made`n" -ForegroundColor Green
            $filesFixed++
            $totalReplacements += $fileReplacements
        } else {
            Write-Host "  No changes needed (already fixed or no /admin URLs)`n" -ForegroundColor Cyan
        }
    } else {
        Write-Host "  File not found: $file`n" -ForegroundColor Red
    }
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Summary" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Files processed: $($files.Count)" -ForegroundColor White
Write-Host "Files fixed: $filesFixed" -ForegroundColor Green
Write-Host "Total replacements: $totalReplacements" -ForegroundColor Green
Write-Host "`n✅ URL fix script completed!`n" -ForegroundColor Green

Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Clear Laravel caches: php artisan optimize:clear" -ForegroundColor White
Write-Host "2. Test the application thoroughly" -ForegroundColor White
Write-Host "3. Check browser console for any remaining 404 errors`n" -ForegroundColor White
