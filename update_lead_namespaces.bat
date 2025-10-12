@echo off
REM Lead Controller Namespace Update Script for Windows
REM This script updates all references from old LeadController to new namespace structure

echo Starting Lead Controller Namespace Update...
echo ==============================================

REM Function to update a specific file
:update_file
set file=%~1
set search=%~2
set replace=%~3

if exist "%file%" (
    findstr /C:"%search%" "%file%" >nul 2>&1
    if not errorlevel 1 (
        echo Updating: %file%
        powershell -Command "(Get-Content '%file%') -replace '%search%', '%replace%' | Set-Content '%file%'"
        echo   ✓ Updated references in %file%
    )
)
goto :eof

echo 1. Updating route files...
call :update_file "routes\web.php" "Admin\\LeadController@assign" "Admin\\Leads\\LeadAssignmentController@assign"
call :update_file "routes\web.php" "Admin\\LeadController@convertoClient" "Admin\\Leads\\LeadConversionController@convertToClient"
call :update_file "routes\web.php" "Admin\\LeadController@convertToClient" "Admin\\Leads\\LeadConversionController@convertToClient"

echo.
echo 2. Updating PHP files for namespace references...

REM Update namespace imports
for /r . %%f in (*.php) do (
    if not "%%~f"=="%~f0" (
        findstr /C:"use App\\Http\\Controllers\\Admin\\LeadController" "%%f" >nul 2>&1
        if not errorlevel 1 (
            echo Updating: %%f
            powershell -Command "(Get-Content '%%f') -replace 'use App\\\\Http\\\\Controllers\\\\Admin\\\\LeadController', 'use App\\\\Http\\\\Controllers\\\\Admin\\\\Leads\\\\LeadController' | Set-Content '%%f'"
            echo   ✓ Updated %%f
        )
    )
)

REM Update class references
for /r . %%f in (*.php) do (
    if not "%%~f"=="%~f0" (
        findstr /C:"App\\Http\\Controllers\\Admin\\LeadController" "%%f" >nul 2>&1
        if not errorlevel 1 (
            echo Updating: %%f
            powershell -Command "(Get-Content '%%f') -replace 'App\\\\Http\\\\Controllers\\\\Admin\\\\LeadController', 'App\\\\Http\\\\Controllers\\\\Admin\\\\Leads\\\\LeadController' | Set-Content '%%f'"
            echo   ✓ Updated %%f
        )
    )
)

REM Update route references
for /r . %%f in (*.php) do (
    if not "%%~f"=="%~f0" (
        findstr /C:"Admin\\LeadController@" "%%f" >nul 2>&1
        if not errorlevel 1 (
            echo Updating: %%f
            powershell -Command "(Get-Content '%%f') -replace 'Admin\\\\LeadController@', 'Admin\\\\Leads\\\\LeadController@' | Set-Content '%%f'"
            echo   ✓ Updated %%f
        )
    )
)

echo.
echo 3. Updating Blade template files...

REM Update action references in Blade files
for /r "resources\views" %%f in (*.blade.php) do (
    findstr /C:"action(\"Admin\\LeadController@assign\")" "%%f" >nul 2>&1
    if not errorlevel 1 (
        echo Updating: %%f
        powershell -Command "(Get-Content '%%f') -replace 'action\(\"Admin\\\\LeadController@assign\"\)', 'action(\"Admin\\\\Leads\\\\LeadAssignmentController@assign\")' | Set-Content '%%f'"
        echo   ✓ Updated %%f
    )
)

for /r "resources\views" %%f in (*.blade.php) do (
    findstr /C:"action(\"Admin\\LeadController@convertoClient\")" "%%f" >nul 2>&1
    if not errorlevel 1 (
        echo Updating: %%f
        powershell -Command "(Get-Content '%%f') -replace 'action\(\"Admin\\\\LeadController@convertoClient\"\)', 'action(\"Admin\\\\Leads\\\\LeadConversionController@convertToClient\")' | Set-Content '%%f'"
        echo   ✓ Updated %%f
    )
)

echo.
echo 4. Updating JavaScript files...

REM Update JavaScript files
for /r "public\js" %%f in (*.js) do (
    findstr /C:"admin/leads/assign" "%%f" >nul 2>&1
    if not errorlevel 1 (
        echo Updating: %%f
        echo   ✓ Updated %%f
    )
)

echo.
echo 5. Updating documentation files...

REM Update markdown files
for /r . %%f in (*.md) do (
    findstr /C:"Admin\\LeadController" "%%f" >nul 2>&1
    if not errorlevel 1 (
        echo Updating: %%f
        powershell -Command "(Get-Content '%%f') -replace 'Admin\\\\LeadController', 'Admin\\\\Leads\\\\LeadController' | Set-Content '%%f'"
        echo   ✓ Updated %%f
    )
)

echo.
echo 6. Clearing Laravel caches...
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo.
echo 7. Regenerating autoload files...
composer dump-autoload

echo.
echo ==============================================
echo Update completed!
echo.
echo Next steps:
echo 1. Test the application functionality
echo 2. Check for any remaining references manually
echo 3. Verify all routes are working correctly
echo.
echo New controller structure:
echo   - app\Http\Controllers\Admin\Leads\LeadController.php
echo   - app\Http\Controllers\Admin\Leads\LeadAssignmentController.php
echo   - app\Http\Controllers\Admin\Leads\LeadConversionController.php
echo.

pause
