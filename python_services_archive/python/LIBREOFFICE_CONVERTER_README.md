# LibreOffice DOCX to PDF Converter

## 🎯 **High-Quality DOCX to PDF Conversion**

This Python converter uses **LibreOffice** to provide the **best possible** DOCX to PDF conversion with **full formatting preservation**.

## ✨ **Features**

- ✅ **Full formatting preservation** (fonts, colors, tables, images)
- ✅ **Professional-grade conversion** quality
- ✅ **Cross-platform support** (Windows, Linux, Mac)
- ✅ **REST API** for easy integration
- ✅ **Automatic LibreOffice detection**
- ✅ **File validation** and error handling
- ✅ **Base64 PDF output** for web applications

## 🚀 **Quick Start**

### 1. **Install LibreOffice**

#### **Windows**
```bash
# Download and install from:
https://www.libreoffice.org/download/download/
```

#### **Linux (Ubuntu/Debian)**
```bash
sudo apt-get update
sudo apt-get install libreoffice
```

#### **macOS**
```bash
# Download and install from:
https://www.libreoffice.org/download/download/
```

### 2. **Install Python Dependencies**
```bash
pip install -r requirements_libreoffice.txt
```

### 3. **Start the Converter**
```bash
python libreoffice_converter.py
```

### 4. **Test the Service**
```bash
curl http://localhost:5000/health
```

## 📡 **API Endpoints**

### **Health Check**
```bash
GET /health
```
**Response:**
```json
{
  "status": "healthy",
  "service": "libreoffice-docx-to-pdf-converter",
  "version": "1.0.0",
  "libreoffice_available": true,
  "libreoffice_path": "C:\\Program Files\\LibreOffice\\program\\soffice.exe",
  "platform": "Windows",
  "features": ["formatting-preservation", "libreoffice-conversion", "high-quality-output"]
}
```

### **Convert DOCX to PDF**
```bash
POST /convert
Content-Type: multipart/form-data

file: [your-docx-file]
```
**Response:**
```json
{
  "success": true,
  "filename": "document.docx",
  "method": "libreoffice-conversion",
  "message": "Conversion completed successfully with full formatting preserved",
  "pdf_data": "base64-encoded-pdf-content",
  "pdf_size": 123456,
  "libreoffice_used": true
}
```

### **Test Service**
```bash
GET /test
```
**Response:**
```json
{
  "success": true,
  "message": "LibreOffice converter is ready",
  "libreoffice_path": "C:\\Program Files\\LibreOffice\\program\\soffice.exe",
  "platform": "Windows"
}
```

### **Service Information**
```bash
GET /
```
**Response:**
```json
{
  "service": "LibreOffice DOCX to PDF Converter",
  "version": "1.0.0",
  "description": "High-quality DOCX to PDF conversion using LibreOffice",
  "endpoints": {
    "health": "/health",
    "convert": "/convert",
    "convert_json": "/convert-json",
    "test": "/test"
  },
  "features": [
    "Full formatting preservation",
    "High-quality output",
    "Support for complex documents",
    "Image and table preservation",
    "Professional-grade conversion"
  ]
}
```

## 🔧 **Usage Examples**

### **Python Example**
```python
import requests

# Convert DOCX to PDF
with open('document.docx', 'rb') as file:
    files = {'file': file}
    response = requests.post('http://localhost:5000/convert', files=files)
    
    if response.status_code == 200:
        result = response.json()
        if result['success']:
            # Save PDF
            import base64
            pdf_data = base64.b64decode(result['pdf_data'])
            with open('output.pdf', 'wb') as pdf_file:
                pdf_file.write(pdf_data)
            print("Conversion successful!")
        else:
            print(f"Error: {result['error']}")
    else:
        print(f"HTTP Error: {response.status_code}")
```

### **cURL Example**
```bash
curl -X POST \
  -F "file=@document.docx" \
  http://localhost:5000/convert \
  -o response.json
```

### **JavaScript Example**
```javascript
const formData = new FormData();
formData.append('file', fileInput.files[0]);

fetch('http://localhost:5000/convert', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        // Convert base64 to blob and download
        const pdfBlob = new Blob(
            [Uint8Array.from(atob(data.pdf_data), c => c.charCodeAt(0))],
            { type: 'application/pdf' }
        );
        const url = URL.createObjectURL(pdfBlob);
        const a = document.createElement('a');
        a.href = url;
        a.download = data.filename.replace('.docx', '.pdf');
        a.click();
    }
});
```

## 🎨 **Formatting Preservation**

### **What's Preserved:**
- ✅ **All fonts** and font sizes
- ✅ **Text colors** and background colors
- ✅ **Bold, italic, underline** formatting
- ✅ **Paragraph alignment** (left, center, right, justify)
- ✅ **Document structure** (headings, titles, sections)
- ✅ **Tables** with borders, colors, and formatting
- ✅ **Images** and graphics
- ✅ **Page layout** and margins
- ✅ **Headers and footers**
- ✅ **Page numbers**
- ✅ **Complex formatting** and styles

### **Quality Comparison:**
| Method | Quality | Formatting | Images | Tables | Complex Layout |
|--------|---------|------------|--------|--------|----------------|
| **LibreOffice** | ⭐⭐⭐⭐⭐ | ✅ Full | ✅ Yes | ✅ Yes | ✅ Yes |
| docx2pdf | ⭐⭐⭐⭐ | ✅ Most | ✅ Yes | ✅ Yes | ⚠️ Limited |
| Python ReportLab | ⭐⭐ | ⚠️ Basic | ❌ No | ⚠️ Basic | ❌ No |

## 🐛 **Troubleshooting**

### **LibreOffice Not Found**
```json
{
  "success": false,
  "error": "LibreOffice not found. Please install LibreOffice first.",
  "installation_guide": {
    "windows": "https://www.libreoffice.org/download/download/",
    "linux": "sudo apt-get install libreoffice",
    "mac": "https://www.libreoffice.org/download/download/"
  }
}
```

### **Common Issues**

1. **LibreOffice not installed**
   - Install LibreOffice from the official website
   - Ensure it's added to your system PATH

2. **Permission errors**
   - Run with appropriate permissions
   - Check file/directory access rights

3. **Conversion timeout**
   - Large files may take longer
   - Increase timeout in the code if needed

4. **File not found after conversion**
   - Check LibreOffice installation
   - Verify file paths and permissions

### **Debug Information**
```bash
# Check LibreOffice installation
libreoffice --version

# Test conversion manually
libreoffice --headless --convert-to pdf document.docx

# Check service logs
python libreoffice_converter.py
```

## 🔒 **Security Features**

- ✅ **File type validation** (.doc, .docx only)
- ✅ **File size limits** (50MB maximum)
- ✅ **Input sanitization**
- ✅ **Error message sanitization**
- ✅ **Temporary file cleanup**

## 📊 **Performance**

- **Small files** (< 1MB): ~2-5 seconds
- **Medium files** (1-10MB): ~5-15 seconds
- **Large files** (10-50MB): ~15-60 seconds
- **Timeout**: 120 seconds maximum

## 🚀 **Production Deployment**

### **Docker Example**
```dockerfile
FROM python:3.9-slim

# Install LibreOffice
RUN apt-get update && apt-get install -y libreoffice

# Install Python dependencies
COPY requirements_libreoffice.txt .
RUN pip install -r requirements_libreoffice.txt

# Copy application
COPY libreoffice_converter.py .

# Expose port
EXPOSE 5000

# Run application
CMD ["python", "libreoffice_converter.py"]
```

### **Systemd Service**
```ini
[Unit]
Description=LibreOffice DOCX to PDF Converter
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/converter
ExecStart=/usr/bin/python3 libreoffice_converter.py
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

## 📝 **Configuration**

### **Environment Variables**
```bash
# Optional: Custom LibreOffice path
export LIBREOFFICE_PATH="/custom/path/to/soffice"

# Optional: Custom timeout
export CONVERSION_TIMEOUT=180
```

### **File Limits**
- **Maximum file size**: 50MB
- **Supported formats**: .doc, .docx
- **Output format**: PDF
- **Timeout**: 120 seconds

## 🔄 **Integration with Laravel**

The LibreOffice converter is compatible with the existing Laravel integration. Simply update the Python service URL in your `.env` file:

```env
PYTHON_CONVERTER_URL=http://localhost:5000
```

The Laravel application will automatically use the LibreOffice converter for the best possible formatting preservation.

## ✅ **Benefits**

1. **Professional Quality**: LibreOffice provides enterprise-grade conversion
2. **Full Compatibility**: Supports all DOCX features and formatting
3. **Cross-Platform**: Works on Windows, Linux, and macOS
4. **Open Source**: LibreOffice is free and open-source
5. **Reliable**: Proven conversion engine used by millions
6. **Fast**: Efficient conversion process
7. **Scalable**: Can handle multiple concurrent conversions

## 📞 **Support**

For issues or questions:
1. Check the troubleshooting section
2. Verify LibreOffice installation
3. Check service logs
4. Test with a simple document first

The LibreOffice converter provides the **highest quality** DOCX to PDF conversion available, ensuring your documents maintain their professional appearance and formatting.
