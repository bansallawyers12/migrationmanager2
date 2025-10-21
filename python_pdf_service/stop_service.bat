@echo off
echo ============================================================
echo   Stopping PDF Processing Microservice
echo ============================================================
echo.

REM Find and kill Python processes running the PDF service
for /f "tokens=2" %%a in ('tasklist ^| findstr /i "python.*pdf_processor"') do (
    echo Stopping process %%a...
    taskkill /F /PID %%a >nul 2>&1
)

REM Also try to kill by port 5000
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":5000" ^| findstr "LISTENING"') do (
    echo Stopping process on port 5000 (PID: %%a)...
    taskkill /F /PID %%a >nul 2>&1
)

echo.
echo [INFO] Service stopped
echo.
pause

