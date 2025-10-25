@echo off
echo ============================================================
echo   PDF Processing Microservice for E-Signature App
echo ============================================================
echo.

REM Check if Python is installed
python --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Python is not installed or not in PATH
    echo Please install Python 3.8+ and add it to your PATH
    echo Download from: https://www.python.org/downloads/
    pause
    exit /b 1
)

echo [INFO] Python found:
python --version
echo.

REM Check if required packages are installed
echo [INFO] Checking dependencies...
python -c "import fitz, PIL, flask, flask_cors" >nul 2>&1
if errorlevel 1 (
    echo [WARN] Some dependencies are missing. Installing...
    pip install -r requirements.txt
    if errorlevel 1 (
        echo [ERROR] Failed to install required packages
        echo Please run manually: pip install -r requirements.txt
        pause
        exit /b 1
    )
    echo [INFO] Dependencies installed successfully
) else (
    echo [INFO] All dependencies are installed
)

echo.
echo ============================================================
echo   Starting Service...
echo ============================================================
echo.
echo Service URL: http://127.0.0.1:5000
echo Health Check: http://127.0.0.1:5000/health
echo.
echo Press Ctrl+C to stop the service
echo ============================================================
echo.

REM Start the service
python start_pdf_service.py

pause

