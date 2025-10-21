# Python PDF Service - Setup & Usage Guide

## Overview

This Python-based microservice provides fast and reliable PDF page rendering for the e-signature application. It replaces the need for Ghostscript and provides better performance and reliability.

## Features

- ✅ PDF page to image conversion (high quality)
- ✅ PDF validation and metadata extraction
- ✅ Signature placement on PDFs
- ✅ Batch page conversion
- ✅ RESTful API with health checks
- ✅ CORS enabled for cross-origin requests
- ✅ Comprehensive logging

## Installation

### Step 1: Install Python (if not already installed)

Download and install Python 3.8+ from: https://www.python.org/downloads/

**Important:** During installation, check the box "Add Python to PATH"

### Step 2: Install Dependencies

Open Command Prompt or PowerShell in the `python_pdf_service` folder and run:

```bash
pip install -r requirements.txt
```

This will install:
- PyMuPDF (fitz) - PDF processing
- Pillow - Image handling
- Flask - Web framework
- flask-cors - CORS support

### Step 3: Verify Installation

```bash
python -c "import fitz, PIL, flask, flask_cors; print('All dependencies installed successfully!')"
```

## Quick Start

### Starting the Service

**Windows:**
```bash
# Double-click or run:
start_service.bat
```

**Manual Start:**
```bash
python start_pdf_service.py
```

You should see:
```
============================================================
  PDF Processing Microservice v1.0.0
============================================================
Service URL: http://127.0.0.1:5000
Health Check: http://127.0.0.1:5000/health
============================================================
```

### Verifying the Service

Open your browser and visit: http://127.0.0.1:5000/health

You should see:
```json
{
  "status": "healthy",
  "service": "pdf-processor",
  "version": "1.0.0",
  "timestamp": "2025-10-21T12:00:00"
}
```

## Service Configuration

The service is pre-configured to work with your Laravel application:

- **Host:** 127.0.0.1 (localhost)
- **Port:** 5000
- **Max File Size:** 50MB
- **Default Resolution:** 150 DPI
- **Log Directory:** `python_pdf_service/logs/`

### Environment Variables (Optional)

You can customize the service by setting environment variables:

```bash
# Service settings
FLASK_HOST=127.0.0.1
FLASK_PORT=5000
FLASK_ENV=development

# PDF processing
DEFAULT_RESOLUTION=150
MAX_FILE_SIZE=52428800  # 50MB in bytes

# Logging
LOG_LEVEL=INFO
```

## API Endpoints

### 1. Health Check
**GET** `/health`

Check if the service is running and healthy.

```bash
curl http://127.0.0.1:5000/health
```

### 2. Convert Page to Image
**POST** `/convert_page`

Convert a PDF page to a PNG image.

```bash
curl -X POST http://127.0.0.1:5000/convert_page \
  -H "Content-Type: application/json" \
  -d '{
    "file_path": "C:\\path\\to\\document.pdf",
    "page_number": 1,
    "resolution": 150
  }'
```

**Response:**
```json
{
  "success": true,
  "image_data": "data:image/png;base64,iVBORw0KGgoAAAANSUhEU...",
  "page_number": 1,
  "resolution": 150
}
```

### 3. Get PDF Info
**POST** `/pdf_info`

Get PDF metadata and page information.

```bash
curl -X POST http://127.0.0.1:5000/pdf_info \
  -H "Content-Type: application/json" \
  -d '{
    "file_path": "C:\\path\\to\\document.pdf"
  }'
```

### 4. Validate PDF
**POST** `/validate_pdf`

Validate if a file is a valid PDF.

```bash
curl -X POST http://127.0.0.1:5000/validate_pdf \
  -H "Content-Type: application/json" \
  -d '{
    "file_path": "C:\\path\\to\\document.pdf"
  }'
```

### 5. Add Signatures to PDF
**POST** `/add_signatures`

Add signature images to a PDF at specified positions.

### 6. Batch Convert Pages
**POST** `/batch_convert`

Convert multiple pages at once for better performance.

## PHP Integration

The service is already integrated with your Laravel application through the `PythonPDFService` class.

### Usage in PHP

```php
use App\Services\PythonPDFService;

$pdfService = new PythonPDFService();

// Check if service is available
if ($pdfService->isHealthy()) {
    // Convert a page
    $result = $pdfService->convertPageToImage($pdfPath, $pageNumber, 150);
    
    if ($result && $result['success']) {
        $imageData = $result['image_data'];
        // Use the image data...
    }
}
```

### Automatic Fallback

The `DocumentController` has been updated to:
1. **Try Python PDF Service first** (recommended, faster)
2. **Fall back to Spatie/Ghostscript** if Python service is unavailable

This ensures your application continues working even if the Python service isn't running.

## Troubleshooting

### Service Won't Start

**Problem:** "Python is not installed or not in PATH"

**Solution:** 
1. Install Python from https://www.python.org/downloads/
2. During installation, check "Add Python to PATH"
3. Restart Command Prompt

---

**Problem:** "Missing dependency: No module named 'fitz'"

**Solution:**
```bash
pip install -r requirements.txt
```

---

**Problem:** "Port 5000 is already in use"

**Solution:**
1. Stop the existing service on port 5000, or
2. Change the port in `pdf_service_config.py`:
   ```python
   FLASK_PORT = 5001  # Use a different port
   ```

### Service Not Responding

**Check if service is running:**
```bash
# Visit in browser:
http://127.0.0.1:5000/health
```

**Restart the service:**
```bash
# Windows:
stop_service.bat
start_service.bat
```

### PDF Conversion Fails

**Check logs:**
```
python_pdf_service/logs/pdf_service.log
```

**Common issues:**
- File path doesn't exist
- File is corrupted
- File is not a valid PDF
- File is too large (>50MB by default)

## Stopping the Service

**Windows:**
```bash
# Press Ctrl+C in the service window, or run:
stop_service.bat
```

## Production Deployment

For production environments, consider:

1. **Using a Process Manager:**
   ```bash
   # Install waitress for production
   pip install waitress
   
   # Run with waitress
   waitress-serve --host=127.0.0.1 --port=5000 pdf_processor:app
   ```

2. **Running as a Windows Service:**
   - Use NSSM (Non-Sucking Service Manager)
   - Configure to start automatically

3. **Setting up Monitoring:**
   - Monitor the `/health` endpoint
   - Set up alerts if service goes down

4. **Load Balancing:**
   - Run multiple instances on different ports
   - Use Nginx as a reverse proxy

## Performance Tips

1. **Increase Resolution for Better Quality:**
   ```php
   $result = $pdfService->convertPageToImage($pdfPath, $pageNumber, 300);
   ```

2. **Use Batch Conversion for Multiple Pages:**
   ```php
   $results = $pdfService->batchConvertPages($pdfPath, [1, 2, 3, 4, 5], 150);
   ```

3. **Cache Converted Images:**
   - Store converted pages in cache
   - Serve cached images to reduce processing

## Security Considerations

1. **Service runs on localhost only** (127.0.0.1) - not accessible from internet
2. **CORS is enabled** but restricted to your application
3. **File size limits** prevent DoS attacks
4. **Input validation** on all endpoints
5. **No direct file system access** from web requests

## Support

For issues or questions:
1. Check the logs: `python_pdf_service/logs/pdf_service.log`
2. Verify service health: http://127.0.0.1:5000/health
3. Review Laravel logs: `storage/logs/laravel.log`

## Version History

- **v1.0.0** - Initial release
  - PDF page to image conversion
  - Health check endpoint
  - Batch conversion support
  - Signature placement

---

**Service Status:** ✅ Ready for Production

**Maintained by:** Your Development Team

**Last Updated:** October 2025

