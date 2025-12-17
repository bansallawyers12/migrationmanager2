<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Document;
use App\Models\MailReport;
use App\Models\ActivitiesLog;
use App\Models\ClientMatter;
use App\Models\Admin;
use App\Traits\LogsClientActivity;

/**
 * Modern Email Upload Controller
 * 
 * Uses Python microservice for email parsing instead of legacy PEAR libraries.
 * This provides better performance, modern code, and PHP 8.2+ compatibility.
 */
class EmailUploadController extends Controller
{
    use LogsClientActivity;

    /**
     * Python service configuration
     */
    protected $pythonServiceUrl;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->pythonServiceUrl = env('PYTHON_SERVICE_URL', 'http://127.0.0.1:5000');
    }

    /**
     * Upload and process inbox emails using Python microservice
     * 
     * Modern replacement for uploadfetchmail method
     */
    public function uploadInboxEmails(Request $request)
    {
        try {
            // Validate file input
            $validator = Validator::make($request->all(), [
                'email_files' => 'required',
                'email_files.*' => 'mimes:msg|max:30720', // 30MB max
                'client_id' => 'required',
                'type' => 'required|in:client,lead'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $clientId = $request->client_id;
            $clientInfo = Admin::select('client_id')->where('id', $clientId)->first();
            $clientUniqueId = !empty($clientInfo) ? $clientInfo->client_id : "";

            if (!$request->hasfile('email_files')) {
                return response()->json([
                    'status' => false,
                    'message' => 'No files uploaded',
                ], 400);
            }

            $uploadedCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($request->file('email_files') as $file) {
                try {
                    $result = $this->processEmailFile($file, $clientId, $clientUniqueId, 'inbox', $request);
                    
                    if ($result['success']) {
                        $uploadedCount++;
                    } else {
                        $failedCount++;
                        $errors[] = [
                            'filename' => $file->getClientOriginalName(),
                            'error' => $result['error']
                        ];
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = [
                        'filename' => $file->getClientOriginalName(),
                        'error' => $e->getMessage()
                    ];
                    Log::error('Email upload error', [
                        'file' => $file->getClientOriginalName(),
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Return response
            return response()->json([
                'status' => true,
                'message' => "Successfully uploaded {$uploadedCount} email(s)" . ($failedCount > 0 ? ", {$failedCount} failed" : ""),
                'uploaded' => $uploadedCount,
                'failed' => $failedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Email upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload and process sent emails using Python microservice
     */
    public function uploadSentEmails(Request $request)
    {
        try {
            // Validate file input
            $validator = Validator::make($request->all(), [
                'email_files' => 'required',
                'email_files.*' => 'mimes:msg|max:30720', // 30MB max
                'client_id' => 'required',
                'type' => 'required|in:client,lead'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $clientId = $request->client_id;
            $clientInfo = Admin::select('client_id')->where('id', $clientId)->first();
            $clientUniqueId = !empty($clientInfo) ? $clientInfo->client_id : "";

            if (!$request->hasfile('email_files')) {
                return response()->json([
                    'status' => false,
                    'message' => 'No files uploaded',
                ], 400);
            }

            $uploadedCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($request->file('email_files') as $file) {
                try {
                    $result = $this->processEmailFile($file, $clientId, $clientUniqueId, 'sent', $request);
                    
                    if ($result['success']) {
                        $uploadedCount++;
                    } else {
                        $failedCount++;
                        $errors[] = [
                            'filename' => $file->getClientOriginalName(),
                            'error' => $result['error']
                        ];
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = [
                        'filename' => $file->getClientOriginalName(),
                        'error' => $e->getMessage()
                    ];
                    Log::error('Email upload error', [
                        'file' => $file->getClientOriginalName(),
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => "Successfully uploaded {$uploadedCount} email(s)" . ($failedCount > 0 ? ", {$failedCount} failed" : ""),
                'uploaded' => $uploadedCount,
                'failed' => $failedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Sent email upload error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process individual email file using Python microservice
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $clientId
     * @param string $clientUniqueId
     * @param string $mailType (inbox|sent)
     * @param Request $request
     * @return array
     */
    protected function processEmailFile($file, $clientId, $clientUniqueId, $mailType, $request)
    {
        try {
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $uniqueFileName = time() . '-' . $fileName;
            $docType = 'conversion_email_fetch';
            
            // 1. Upload file to S3
            $filePath = $clientUniqueId . '/' . $docType . '/' . $mailType . '/' . $uniqueFileName;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $fileUrl = Storage::disk('s3')->url($filePath);

            // 2. Parse email using Python microservice
            $parsedData = $this->parseEmailWithPython($file);

            if (!$parsedData || !$parsedData['success']) {
                throw new \Exception($parsedData['error'] ?? 'Failed to parse email');
            }

            // 3. Save document record
            $document = new Document();
            $document->file_name = pathinfo($fileName, PATHINFO_FILENAME);
            $document->filetype = pathinfo($fileName, PATHINFO_EXTENSION);
            $document->user_id = Auth::user()->id;
            $document->myfile = $fileUrl;
            $document->myfile_key = $uniqueFileName;
            $document->client_id = $clientId;
            $document->type = $request->type;
            $document->mail_type = $mailType;
            $document->file_size = $fileSize;
            $document->doc_type = $docType;
            $document->client_matter_id = $mailType === 'sent' 
                ? $request->upload_sent_mail_client_matter_id 
                : $request->upload_inbox_mail_client_matter_id;
            $document->save();

            // 4. Save to MailReport
            $mailReport = new MailReport();
            $mailReport->user_id = Auth::user()->id;
            $mailReport->from_mail = $parsedData['sender_email'] ?? '';
            $mailReport->to_mail = isset($parsedData['recipients']) && is_array($parsedData['recipients']) 
                ? implode(',', $parsedData['recipients']) 
                : '';
            $mailReport->subject = $parsedData['subject'] ?? '';
            $mailReport->message = $parsedData['html_content'] ?? $parsedData['text_content'] ?? '';
            $mailReport->mail_type = 1;
            $mailReport->client_id = $clientId;
            $mailReport->conversion_type = $docType;
            $mailReport->mail_body_type = $mailType;
            $mailReport->uploaded_doc_id = $document->id;
            $mailReport->client_matter_id = $document->client_matter_id;
            
            // Format sent time from Python response
            if (!empty($parsedData['sent_date'])) {
                try {
                    $sentDate = new \DateTime($parsedData['sent_date']);
                    $mailReport->fetch_mail_sent_time = $sentDate->format('d/m/Y h:i a');
                } catch (\Exception $e) {
                    $mailReport->fetch_mail_sent_time = $parsedData['sent_date'];
                }
            }
            
            // NEW: Add Python AI analysis
            $analysisData = $this->analyzeEmailWithPython($parsedData);
            if ($analysisData && isset($analysisData['success']) && $analysisData['success']) {
                $mailReport->python_analysis = $analysisData;
                $mailReport->category = $analysisData['category'] ?? 'Uncategorized';
                $mailReport->priority = $analysisData['priority'] ?? 'low';
                $mailReport->sentiment = $analysisData['sentiment'] ?? 'neutral';
                $mailReport->language = $analysisData['language'] ?? null;
                $mailReport->security_issues = $analysisData['security_issues'] ?? null;
                $mailReport->thread_info = $analysisData['thread_info'] ?? null;
                $mailReport->processed_at = now();
            }
            
            // NEW: Add metadata
            $mailReport->message_id = $parsedData['message_id'] ?? null;
            $mailReport->thread_id = $parsedData['thread_id'] ?? null;
            $mailReport->received_date = $parsedData['received_date'] ?? now();
            $mailReport->file_hash = md5_file($file->getRealPath());
            
            $mailReport->save();

            // NEW: Save attachments
            if (isset($parsedData['attachments']) && is_array($parsedData['attachments'])) {
                Log::info('Processing attachments', [
                    'count' => count($parsedData['attachments']),
                    'mail_report_id' => $mailReport->id
                ]);
                
                foreach ($parsedData['attachments'] as $attachmentData) {
                    try {
                        $this->saveAttachment($mailReport->id, $attachmentData, $clientUniqueId);
                    } catch (\Exception $e) {
                        Log::error('Error in saveAttachment loop', [
                            'error' => $e->getMessage(),
                            'attachment' => $attachmentData['filename'] ?? 'unknown'
                        ]);
                        // Continue processing other attachments
                    }
                }
            } else {
                Log::info('No attachments found in parsed data', [
                    'has_attachments_key' => isset($parsedData['attachments']),
                    'mail_report_id' => $mailReport->id
                ]);
            }

            // NEW: Auto-assign labels
            $this->autoAssignLabels($mailReport, $mailType);

            // 5. Update client matter timestamp
            $matterId = $document->client_matter_id;
            if (!empty($matterId)) {
                $matter = ClientMatter::find($matterId);
                if ($matter) {
                    $matter->updated_at = now();
                    $matter->save();
                }
            }

            // 6. Create activity log
            if ($request->type == 'client') {
                // Get matter reference
                $matterReference = '';
                if ($matterId) {
                    $matter = ClientMatter::find($matterId);
                    if ($matter && $matter->client_unique_matter_no) {
                        $matterReference = $matter->client_unique_matter_no;
                    }
                }
                
                // Fall back to latest active matter if none found
                if (empty($matterReference)) {
                    $latestMatter = ClientMatter::where('client_id', $clientId)
                        ->where('matter_status', 1)
                        ->orderBy('id', 'desc')
                        ->first();
                    if ($latestMatter && $latestMatter->client_unique_matter_no) {
                        $matterReference = $latestMatter->client_unique_matter_no;
                    }
                }
                
                // Format subject with matter reference
                $emailSubject = $parsedData['subject'] ?? 'Email';
                $subject = !empty($matterReference)
                    ? "uploaded Email: {$emailSubject} - {$matterReference}"
                    : "uploaded Email: {$emailSubject}";
                
                // Truncate long subjects
                if (strlen($subject) > 100) {
                    $subject = substr($subject, 0, 97) . '...';
                }
                
                $from = $parsedData['from'] ?? 'Unknown';
                $description = "<p>From: {$from}</p>";
                
                $this->logClientActivity(
                    $clientId,
                    $subject,
                    $description,
                    'email'
                );
            }

            return [
                'success' => true,
                'document_id' => $document->id,
                'mail_report_id' => $mailReport->id
            ];

        } catch (\Exception $e) {
            Log::error('Process email file error', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Parse email file using Python microservice
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return array|null
     */
    protected function parseEmailWithPython($file)
    {
        try {
            // Call Python microservice
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($file), $file->getClientOriginalName())
                ->post($this->pythonServiceUrl . '/email/parse');

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Python service error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => 'Python service returned status: ' . $response->status()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Python service connection error', [
                'error' => $e->getMessage(),
                'url' => $this->pythonServiceUrl
            ]);

            return [
                'success' => false,
                'error' => 'Failed to connect to Python service: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if Python service is available
     * 
     * @return array
     */
    public function checkPythonService()
    {
        try {
            $response = Http::timeout(5)->get($this->pythonServiceUrl . '/health');

            return [
                'status' => $response->successful(),
                'url' => $this->pythonServiceUrl,
                'response' => $response->successful() ? $response->json() : null
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'url' => $this->pythonServiceUrl,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Analyze email content with Python AI service
     * 
     * @param array $parsedData
     * @return array|null
     */
    protected function analyzeEmailWithPython($parsedData)
    {
        try {
            $response = Http::timeout(30)->post($this->pythonServiceUrl . '/email/analyze', [
                'subject' => $parsedData['subject'] ?? '',
                'text_content' => $parsedData['text_content'] ?? '',
                'html_content' => $parsedData['html_content'] ?? '',
                'sender_email' => $parsedData['sender_email'] ?? '',
                'recipients' => $parsedData['recipients'] ?? [],
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            
            Log::warning('Python analyzer service unavailable', ['status' => $response->status()]);
            return null;
        } catch (\Exception $e) {
            Log::warning('Python analyzer service error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Save attachment to database and S3
     * 
     * @param int $mailReportId
     * @param array $attachmentData
     * @param string $clientUniqueId
     */
    protected function saveAttachment($mailReportId, $attachmentData, $clientUniqueId)
    {
        $s3Path = null;
        $s3Key = null;
        $fileSize = $attachmentData['file_size'] ?? $attachmentData['size'] ?? 0;
        
        try {
            // Check for both 'content' and 'data' keys (Python service uses 'data')
            $attachmentContent = $attachmentData['content'] ?? $attachmentData['data'] ?? null;
            
            Log::info('Processing attachment data', [
                'filename' => $attachmentData['filename'] ?? 'unknown',
                'has_content' => !empty($attachmentContent),
                'content_length' => !empty($attachmentContent) ? strlen($attachmentContent) : 0,
                'expected_size' => $fileSize
            ]);
            
            if (!empty($attachmentContent)) {
                // Decode base64-encoded attachment data
                $decodedData = base64_decode($attachmentContent, true);
                
                // Validate base64 decode succeeded
                if ($decodedData === false) {
                    Log::warning('Failed to decode base64 attachment data', [
                        'filename' => $attachmentData['filename'] ?? 'unknown',
                        'content_length' => strlen($attachmentContent)
                    ]);
                    // Continue to create attachment record without file
                } else {
                    // Validate decoded data size matches expected size (with some tolerance for base64 padding)
                    $expectedSize = $fileSize;
                    $actualSize = strlen($decodedData);
                    
                    // Allow up to 3 bytes difference (base64 padding can cause small differences)
                    if ($expectedSize > 0) {
                        $sizeDifference = abs($actualSize - $expectedSize);
                        if ($sizeDifference > 3) {
                            Log::warning('Attachment size mismatch', [
                                'filename' => $attachmentData['filename'] ?? 'unknown',
                                'expected' => $expectedSize,
                                'actual' => $actualSize,
                                'difference' => $sizeDifference
                            ]);
                            // Continue anyway, but log the warning
                        }
                    }
                    
                    // Validate minimum size (empty files are suspicious)
                    if ($actualSize === 0) {
                        Log::warning('Decoded attachment data is empty', [
                            'filename' => $attachmentData['filename'] ?? 'unknown'
                        ]);
                        // Continue to create attachment record without file
                    } else {
                        // Generate unique S3 key
                        $s3Key = $clientUniqueId . '/attachments/' . time() . '_' . ($attachmentData['filename'] ?? 'attachment');
                        
                        try {
                            // Upload to S3
                            $uploadSuccess = Storage::disk('s3')->put($s3Key, $decodedData);
                            
                            if (!$uploadSuccess) {
                                throw new \Exception('S3 upload returned false');
                            }
                            
                            // Verify file exists in S3
                            if (!Storage::disk('s3')->exists($s3Key)) {
                                throw new \Exception('File not found in S3 after upload');
                            }
                            
                            $s3Path = Storage::disk('s3')->url($s3Key);
                            
                            // Update file size to actual decoded size
                            $fileSize = $actualSize;
                            
                            Log::info('Attachment saved successfully to S3', [
                                'filename' => $attachmentData['filename'] ?? 'unknown',
                                'size' => $actualSize,
                                's3_key' => $s3Key,
                                's3_path' => $s3Path
                            ]);
                        } catch (\Exception $s3Exception) {
                            Log::error('S3 upload failed for attachment', [
                                'filename' => $attachmentData['filename'] ?? 'unknown',
                                's3_key' => $s3Key,
                                'error' => $s3Exception->getMessage(),
                                'trace' => $s3Exception->getTraceAsString()
                            ]);
                            // Reset s3_key and s3Path so we don't save invalid references
                            $s3Key = null;
                            $s3Path = null;
                        }
                    }
                }
            } else {
                Log::info('Attachment has no content data, creating record without file', [
                    'filename' => $attachmentData['filename'] ?? 'unknown'
                ]);
            }

            // Always create attachment record (even if file upload failed)
            \App\Models\MailReportAttachment::create([
                'mail_report_id' => $mailReportId,
                'filename' => $attachmentData['filename'] ?? 'unknown',
                'display_name' => $attachmentData['display_name'] ?? ($attachmentData['filename'] ?? 'unknown'),
                'content_type' => $attachmentData['content_type'] ?? 'application/octet-stream',
                'file_path' => $s3Path,
                's3_key' => $s3Key,
                'file_size' => $fileSize,
                'content_id' => $attachmentData['content_id'] ?? null,
                'is_inline' => $attachmentData['is_inline'] ?? false,
                'extension' => pathinfo($attachmentData['filename'] ?? 'unknown', PATHINFO_EXTENSION),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to save attachment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attachment' => $attachmentData['filename'] ?? 'unknown'
            ]);
            // Don't re-throw - allow email upload to continue even if attachment fails
            // Attachment record will still be created (if we got that far) but without file
        }
    }

    /**
     * Auto-assign labels based on sender domain
     * 
     * @param \App\Models\MailReport $mailReport
     * @param string $mailType
     */
    protected function autoAssignLabels($mailReport, $mailType)
    {
        try {
            // Company domains that indicate emails WE sent
            $companyDomains = [
                '@bansalimmigration.com.au',
                '@bansaleducation.com.au',
                '@bansallawyers.com.au'
            ];
            
            // Check if email is from our company domains
            $isFromCompany = false;
            $senderEmail = strtolower($mailReport->from_mail);
            
            foreach ($companyDomains as $domain) {
                if (str_contains($senderEmail, $domain)) {
                    $isFromCompany = true;
                    break;
                }
            }
            
            // Assign "Sent" label if from company domain, otherwise "Inbox" label
            $labelName = $isFromCompany ? 'Sent' : 'Inbox';
            
            $label = \App\Models\EmailLabel::where('name', $labelName)
                ->where('type', 'system')
                ->first();
            
            if ($label) {
                $mailReport->labels()->attach($label->id);
                
                Log::info('Auto-assigned label', [
                    'email_id' => $mailReport->id,
                    'sender' => $mailReport->from_mail,
                    'label' => $labelName,
                    'is_from_company' => $isFromCompany
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to auto-assign label', ['error' => $e->getMessage()]);
        }
    }
}

