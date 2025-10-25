# Python Service Integration Guide

## ✅ Migration Complete!

**Status**: All controllers have been migrated to use the unified `PythonService` (Oct 25, 2025)

The Laravel integration is **complete and ready to use**. All references to old services have been updated.

---

## 🎯 Overview

This guide shows how to integrate the unified Python services with your Laravel Migration Manager application.

## 📋 Prerequisites

1. **Python Service Running**: Ensure the unified Python service is running on `http://localhost:5000`
2. **Dependencies Installed**: All Python dependencies are installed
3. **Laravel Service**: The `PythonService.php` is added to your Laravel app

## 🚀 Quick Start

### 1. Start Python Service

```bash
# Option 1: Using Python directly
cd C:\xampp\htdocs\migrationmanager\python_services
py main.py

# Option 2: Using startup script
py start_services.py

# Option 3: Using Windows batch file
start_services.bat
```

### 2. Test Service Health

```bash
curl http://localhost:5000/health
```

Expected response:
```json
{
  "status": "healthy",
  "services": {
    "pdf_service": "ready",
    "email_parser": "ready",
    "email_analyzer": "ready",
    "email_renderer": "ready"
  }
}
```

## 💻 Laravel Integration

### 1. Environment Configuration

Add to your `.env` file:

```env
# Python Service Configuration
PYTHON_SERVICE_URL=http://localhost:5000
PYTHON_SERVICE_TIMEOUT=120
PYTHON_SERVICE_MAX_RETRIES=3
```

### 2. Using the PythonService

```php
use App\Services\PythonService;

class YourController extends Controller
{
    protected $pythonService;

    public function __construct(PythonService $pythonService)
    {
        $this->pythonService = $pythonService;
    }

    // Your methods here
}
```

## 📧 Email Processing Examples

### 1. Parse .msg File

```php
public function uploadEmail(Request $request)
{
    $request->validate([
        'email_file' => 'required|file|mimes:msg|max:20480'
    ]);

    try {
        // Parse the .msg file
        $result = $this->pythonService->parseEmail($request->file('email_file'));
        
        if ($result['success']) {
            // Save to database
            $email = Email::create([
                'subject' => $result['subject'],
                'sender_name' => $result['sender_name'],
                'sender_email' => $result['sender_email'],
                'html_content' => $result['html_content'],
                'text_content' => $result['text_content'],
                'sent_date' => $result['sent_date'],
                'recipients' => json_encode($result['recipients']),
                'attachments' => json_encode($result['attachments']),
                'status' => 'parsed'
            ]);

            return response()->json([
                'success' => true,
                'email_id' => $email->id,
                'message' => 'Email parsed successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ], 500);
        }
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Email parsing failed: ' . $e->getMessage()
        ], 500);
    }
}
```

### 2. Analyze Email Content

```php
public function analyzeEmail($emailId)
{
    $email = Email::findOrFail($emailId);

    try {
        // Prepare email data for analysis
        $emailData = [
            'subject' => $email->subject,
            'text_content' => $email->text_content,
            'html_content' => $email->html_content,
            'sender_email' => $email->sender_email,
            'sender_name' => $email->sender_name
        ];

        // Analyze the email
        $analysis = $this->pythonService->analyzeEmail($emailData);

        // Update email with analysis results
        $email->update([
            'category' => $analysis['category'],
            'priority' => $analysis['priority'],
            'sentiment' => $analysis['sentiment'],
            'language' => $analysis['language'],
            'security_issues' => json_encode($analysis['security_issues']),
            'thread_info' => json_encode($analysis['thread_info']),
            'analysis_data' => json_encode($analysis),
            'analyzed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'analysis' => $analysis
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Analysis failed: ' . $e->getMessage()
        ], 500);
    }
}
```

### 3. Complete Email Processing Pipeline

```php
public function processEmail(Request $request)
{
    $request->validate([
        'email_file' => 'required|file|mimes:msg|max:20480'
    ]);

    try {
        // Complete pipeline: parse + analyze + render
        $result = $this->pythonService->processEmail($request->file('email_file'));

        if ($result['processing_status'] === 'success') {
            // Save complete email data
            $email = Email::create([
                'subject' => $result['subject'],
                'sender_name' => $result['sender_name'],
                'sender_email' => $result['sender_email'],
                'html_content' => $result['html_content'],
                'text_content' => $result['text_content'],
                'sent_date' => $result['sent_date'],
                'recipients' => json_encode($result['recipients']),
                'attachments' => json_encode($result['attachments']),
                
                // Analysis results
                'category' => $result['analysis']['category'],
                'priority' => $result['analysis']['priority'],
                'sentiment' => $result['analysis']['sentiment'],
                'language' => $result['analysis']['language'],
                'security_issues' => json_encode($result['analysis']['security_issues']),
                'thread_info' => json_encode($result['analysis']['thread_info']),
                'analysis_data' => json_encode($result['analysis']),
                
                // Rendering results
                'enhanced_html' => $result['rendering']['enhanced_html'],
                'rendered_html' => $result['rendering']['rendered_html'],
                'text_preview' => $result['rendering']['text_preview'],
                'rendering_data' => json_encode($result['rendering']),
                
                'status' => 'processed',
                'processed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'email_id' => $email->id,
                'message' => 'Email processed successfully',
                'category' => $result['analysis']['category'],
                'priority' => $result['analysis']['priority']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Email processing failed'
            ], 500);
        }
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Email processing failed: ' . $e->getMessage()
        ], 500);
    }
}
```

## 📄 PDF Processing Examples

### 1. Convert PDF to Images

```php
public function convertPdfToImages(Request $request)
{
    $request->validate([
        'pdf_file' => 'required|file|mimes:pdf|max:51200'
    ]);

    try {
        $result = $this->pythonService->convertPdfToImages(
            $request->file('pdf_file'),
            $request->input('dpi', 150)
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'total_pages' => $result['total_pages'],
                'images' => $result['images'],
                'message' => "Converted {$result['total_pages']} pages to images"
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ], 500);
        }
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'PDF conversion failed: ' . $e->getMessage()
        ], 500);
    }
}
```

### 2. Merge PDF Files

```php
public function mergePdfs(Request $request)
{
    $request->validate([
        'pdf_files' => 'required|array|min:2',
        'pdf_files.*' => 'file|mimes:pdf|max:51200'
    ]);

    try {
        $files = $request->file('pdf_files');
        $result = $this->pythonService->mergePdfs($files);

        if ($result['success']) {
            // Save merged PDF
            $filename = 'merged_' . time() . '.pdf';
            $path = storage_path('app/public/merged/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            // Decode and save PDF
            file_put_contents($path, base64_decode($result['data']));

            return response()->json([
                'success' => true,
                'filename' => $filename,
                'url' => asset('storage/merged/' . $filename),
                'total_pages' => $result['total_pages'],
                'message' => "Merged {$result['total_files']} PDF files"
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ], 500);
        }
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'PDF merge failed: ' . $e->getMessage()
        ], 500);
    }
}
```

## 🔧 Service Health Monitoring

### 1. Check Service Health

```php
public function checkPythonService()
{
    $health = $this->pythonService->isHealthy();
    $status = $this->pythonService->getStatus();
    $config = $this->pythonService->getConfig();

    return response()->json([
        'healthy' => $health,
        'status' => $status,
        'config' => $config
    ]);
}
```

### 2. Test Service Connection

```php
public function testPythonService()
{
    $test = $this->pythonService->testConnection();

    return response()->json($test);
}
```

## 🎨 Frontend Integration

### 1. JavaScript Service Call

```javascript
// Upload and process email
async function processEmail(file) {
    const formData = new FormData();
    formData.append('email_file', file);

    try {
        const response = await fetch('/api/emails/process', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();
        
        if (result.success) {
            console.log('Email processed:', result);
            // Update UI with results
            updateEmailList(result);
        } else {
            console.error('Processing failed:', result.error);
            showError(result.error);
        }
    } catch (error) {
        console.error('Request failed:', error);
        showError('Network error occurred');
    }
}

// Convert PDF to images
async function convertPdfToImages(file, dpi = 150) {
    const formData = new FormData();
    formData.append('pdf_file', file);
    formData.append('dpi', dpi);

    try {
        const response = await fetch('/api/pdf/convert-to-images', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();
        
        if (result.success) {
            console.log('PDF converted:', result);
            displayImages(result.images);
        } else {
            console.error('Conversion failed:', result.error);
            showError(result.error);
        }
    } catch (error) {
        console.error('Request failed:', error);
        showError('Network error occurred');
    }
}
```

## 📊 Database Schema Updates

### 1. Add Python Analysis Fields to Emails Table

```php
// Migration: add_python_analysis_to_emails_table.php
Schema::table('emails', function (Blueprint $table) {
    $table->string('category')->nullable()->after('status');
    $table->string('priority')->default('medium')->after('category');
    $table->string('sentiment')->default('neutral')->after('priority');
    $table->string('language')->nullable()->after('sentiment');
    $table->json('security_issues')->nullable()->after('language');
    $table->json('thread_info')->nullable()->after('security_issues');
    $table->json('analysis_data')->nullable()->after('thread_info');
    $table->longText('enhanced_html')->nullable()->after('html_content');
    $table->longText('rendered_html')->nullable()->after('enhanced_html');
    $table->text('text_preview')->nullable()->after('rendered_html');
    $table->json('rendering_data')->nullable()->after('text_preview');
    $table->timestamp('analyzed_at')->nullable()->after('processed_at');
    $table->timestamp('rendered_at')->nullable()->after('analyzed_at');
});
```

### 2. Update Email Model

```php
// app/Models/Email.php
class Email extends Model
{
    protected $fillable = [
        'subject', 'sender_name', 'sender_email', 'html_content', 'text_content',
        'sent_date', 'recipients', 'attachments', 'status',
        'category', 'priority', 'sentiment', 'language',
        'security_issues', 'thread_info', 'analysis_data',
        'enhanced_html', 'rendered_html', 'text_preview', 'rendering_data',
        'analyzed_at', 'rendered_at'
    ];

    protected $casts = [
        'recipients' => 'array',
        'attachments' => 'array',
        'security_issues' => 'array',
        'thread_info' => 'array',
        'analysis_data' => 'array',
        'rendering_data' => 'array',
        'sent_date' => 'datetime',
        'analyzed_at' => 'datetime',
        'rendered_at' => 'datetime',
    ];

    // Scopes for filtering by analysis results
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeBySentiment($query, $sentiment)
    {
        return $query->where('sentiment', $sentiment);
    }

    public function scopeWithSecurityIssues($query)
    {
        return $query->whereNotNull('security_issues')
                    ->where('security_issues', '!=', '[]');
    }
}
```

## 🚀 Deployment

### 1. Production Environment Variables

```env
# Production Python Service
PYTHON_SERVICE_URL=http://your-server:5000
PYTHON_SERVICE_TIMEOUT=180
PYTHON_SERVICE_MAX_RETRIES=5
```

### 2. Windows Service Setup

```bash
# Install as Windows Service using NSSM
nssm install PythonServices "C:\Python39\python.exe" "C:\xampp\htdocs\migrationmanager\python_services\main.py"
nssm set PythonServices AppDirectory "C:\xampp\htdocs\migrationmanager\python_services"
nssm set PythonServices DisplayName "Migration Manager Python Services"
nssm set PythonServices Description "Unified Python services for PDF and email processing"
nssm start PythonServices
```

### 3. Linux Service Setup

```bash
# Create systemd service
sudo nano /etc/systemd/system/python-services.service

[Unit]
Description=Migration Manager Python Services
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/migrationmanager/python_services
ExecStart=/usr/bin/python3 main.py --host 0.0.0.0 --port 5000
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target

# Enable and start service
sudo systemctl enable python-services
sudo systemctl start python-services
```

## 🔍 Troubleshooting

### 1. Common Issues

**Service not responding:**
```bash
# Check if service is running
curl http://localhost:5000/health

# Check logs
tail -f python_services/logs/combined-*.log
```

**Dependencies missing:**
```bash
cd python_services
py -m pip install -r requirements.txt
```

**Permission errors:**
```bash
# Windows
icacls python_services /grant Everyone:F /T

# Linux
chmod -R 755 python_services/
chown -R www-data:www-data python_services/
```

### 2. Debug Mode

```php
// Enable debug logging
Log::debug('Python service request', [
    'url' => $this->baseUrl . '/email/parse',
    'file' => $file->getClientOriginalName(),
    'size' => $file->getSize()
]);
```

## 📈 Performance Tips

1. **Use background jobs** for heavy processing
2. **Cache analysis results** to avoid re-processing
3. **Set appropriate timeouts** based on file sizes
4. **Monitor memory usage** for large files
5. **Use retry logic** for transient failures

## 🎯 Next Steps

1. **Test the integration** with sample files
2. **Update your controllers** to use the new service
3. **Add error handling** and user feedback
4. **Implement caching** for better performance
5. **Add monitoring** and health checks

---

**The unified Python service is now ready to use!** 🚀

For more details, see:
- `python_services/README.md` - Technical documentation
- `python_services/QUICK_START.md` - Quick reference
- `PYTHON_SERVICES_DECISION_GUIDE.md` - Architecture decisions
