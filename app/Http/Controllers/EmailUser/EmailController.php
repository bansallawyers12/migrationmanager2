<?php

namespace App\Http\Controllers\EmailUser;

use App\Http\Controllers\Controller;
use App\Models\EmailAccount;
use App\Models\Email;
use App\Models\EmailDraft;
use App\Models\EmailSignature;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Auth;
use Exception;

class EmailController extends Controller
{
    public function compose(Request $request)
    {
        $accountId = (int) $request->query('account_id');
        $to = (string) $request->query('to', '');
        $cc = (string) $request->query('cc', '');
        $bcc = (string) $request->query('bcc', '');
        $subject = (string) $request->query('subject', '');
        $body = (string) $request->query('body', '');

        // Ensure the account belongs to the user if provided
        $account = null;
        if ($accountId) {
            $account = EmailAccount::where('id', $accountId)
                ->where('user_id', $request->user()->id)
                ->first();
        }

        // Get signatures for the selected account or all accounts
        $signatures = EmailSignature::forUser($request->user()->id)
            ->where(function ($query) use ($accountId) {
                $query->where('account_id', $accountId)
                      ->orWhereNull('account_id');
            })
            ->active()
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return view('EmailUser.emails.compose', compact('account', 'accountId', 'to', 'cc', 'bcc', 'subject', 'body', 'signatures'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'account_id' => ['required', 'integer', 'exists:email_accounts,id'],
            'to' => ['required', 'email'],
            'subject' => ['required', 'string'],
            'body' => ['required', 'string'],
            'cc' => ['nullable', 'string'],
            'bcc' => ['nullable', 'string'],
            'attachments.*' => ['nullable', 'file', 'max:10240'], // Max 10MB per file
        ]);

        $account = EmailAccount::where('id', $validated['account_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Validate provider before proceeding
        $provider = $account->provider ? strtolower(trim($account->provider)) : null;
        $allowedProviders = ['zoho'];
        if (empty($provider) || !in_array($provider, $allowedProviders, true)) {
            Log::warning('Provider rejected during send', [
                'account_id' => $account->id,
                'provider' => $account->provider
            ]);
            return response()->json([
                'ok' => false,
                'error' => 'Invalid or missing provider. Only zoho is supported.',
            ], 422);
        }

        // OS-agnostic Python path detection
        $pythonPath = $this->getPythonExecutablePath();
        $script = $this->getSendMailScriptPath();

        // Get the correct authentication token/password
        $authToken = $account->access_token;
        if (!$authToken && $account->password) {
            // Decrypt the password if it's encrypted
            try {
                $authToken = decrypt($account->password);
            } catch (\Exception $e) {
                // If decryption fails, use the password as-is (might be plain text)
                $authToken = $account->password;
            }
        }

        // Handle attachments
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            $attachments = $request->file('attachments');
            foreach ($attachments as $attachment) {
                if ($attachment->isValid()) {
                    $filename = $attachment->getClientOriginalName();
                    $path = $attachment->store('temp/attachments', 'local');
                    $attachmentPaths[] = [
                        'path' => storage_path('app/private/' . $path),
                        'filename' => $filename,
                        'mime_type' => $attachment->getMimeType(),
                    ];
                }
            }
        }

        $args = [
            $pythonPath,
            $script,
            strtolower(trim($account->provider)),
            $account->email,
            $authToken,
            $validated['to'],
            $validated['subject'],
            $validated['body'],
            $validated['cc'] ?? '',
            $validated['bcc'] ?? '',
            json_encode($attachmentPaths),
        ];

        $process = new Process($args, base_path());
        $process->setTimeout(30);
        
        // Set environment variables for better Windows compatibility
        $env = [
            'PATH' => getenv('PATH'),
            'SYSTEMROOT' => getenv('SYSTEMROOT'),
            'WINDIR' => getenv('WINDIR'),
            'TEMP' => getenv('TEMP'),
            'TMP' => getenv('TMP'),
            'PYTHONPATH' => getenv('PYTHONPATH'),
            'PYTHONIOENCODING' => 'utf-8',
            'PYTHONUNBUFFERED' => '1',
        ];
        
        // Add Windows-specific environment variables
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $env['COMSPEC'] = getenv('COMSPEC');
            $env['PATHEXT'] = getenv('PATHEXT');
            $env['PROCESSOR_ARCHITECTURE'] = getenv('PROCESSOR_ARCHITECTURE');
            $env['PROCESSOR_IDENTIFIER'] = getenv('PROCESSOR_IDENTIFIER');
        }
        
        $process->setEnv($env);
        $process->run();

        // Log the process details for debugging
        Log::info('Email sending process completed', [
            'account_id' => $account->id,
            'account_email' => $account->email,
            'provider' => $account->provider,
            'to' => $validated['to'],
            'subject' => $validated['subject'],
            'is_successful' => $process->isSuccessful(),
            'exit_code' => $process->getExitCode(),
            'output' => $process->getOutput(),
            'error_output' => $process->getErrorOutput(),
            'has_attachments' => !empty($attachmentPaths),
            'attachment_count' => count($attachmentPaths)
        ]);

        // Clean up temporary attachment files AFTER processing is complete
        foreach ($attachmentPaths as $attachment) {
            if (file_exists($attachment['path'])) {
                unlink($attachment['path']);
            }
        }

        if (!$process->isSuccessful()) {
            $errorMessage = $process->getErrorOutput() ?: $process->getOutput();
            
            // If no error message, provide a more descriptive one
            if (empty(trim($errorMessage))) {
                $errorMessage = "Email sending failed with exit code {$process->getExitCode()}. No error details available.";
            }
            
            Log::error('Email sending failed', [
                'account_id' => $account->id,
                'account_email' => $account->email,
                'provider' => $account->provider,
                'to' => $validated['to'],
                'subject' => $validated['subject'],
                'exit_code' => $process->getExitCode(),
                'output' => $process->getOutput(),
                'error_output' => $process->getErrorOutput(),
                'error_message' => $errorMessage
            ]);
            
            return response()->json([
                'ok' => false,
                'error' => $errorMessage,
            ], 422);
        }

        // Store sent email in database for tracking
        $this->storeSentEmail($request, Auth::guard('email_users')->id(), $account->id);

        return response()->json([
            'ok' => true,
            'output' => $process->getOutput(),
        ]);
    }

        public function sync($accountId, Request $request)
    {
        // Handle GET requests (load emails) vs POST requests (sync emails)
        if ($request->isMethod('GET')) {
            // Log::info("GET request - loading emails for account: {$accountId}");
            return $this->loadEmails($accountId, $request);
        }

        // Handle POST requests (sync emails)
        if ($request->isMethod('POST')) {
            // Check if this is a paginated sync request
            if ($request->has('paginated') && $request->get('paginated') === 'true') {
                Log::info("POST request - paginated sync", [
                    'account_id' => $accountId,
                    'paginated_param' => $request->get('paginated'),
                    'all_params' => $request->all()
                ]);
                return $this->syncWithPagination($accountId, $request);
            }

            Log::info("POST request - regular sync (redirecting to paginated)", [
                'account_id' => $accountId,
                'all_params' => $request->all()
            ]);

            // Redirect POST requests to paginated sync
            return $this->syncWithPagination($accountId, $request);
        }

        // Increase PHP execution time limit for sync operations
        set_time_limit(config('mail_sync.sync_timeout', 900)); // 15 minutes default
        
        
        // Increase memory limit for large syncs
        ini_set('memory_limit', config('mail_sync.memory_limit', '1G'));
        
        $account = EmailAccount::where('id', $accountId)
            ->where('user_id', Auth::guard('email_users')->id())
            ->firstOrFail();

        // Validate provider before proceeding
        $provider = $account->provider ? strtolower(trim($account->provider)) : null;
        $allowedProviders = ['zoho'];
        if (empty($provider) || !in_array($provider, $allowedProviders, true)) {
            Log::warning('Provider rejected during sync', [
                'account_id' => $account->id,
                'provider' => $account->provider
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing provider. Only zoho is supported.',
            ], 422);
        }

        $folder = $request->get('folder', 'Inbox');
        // Validate limit strictly (1–200). Reject out-of-range instead of silently clamping.
        $rawLimit = $request->get('limit', 50);
        $limit = (int) $rawLimit;
        if ($limit < 1 || $limit > 200) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid limit. Please choose a value between 1 and 200.',
            ], 422);
        }
        
        // Reduce limit for large syncs to prevent timeouts
        $maxLimit = config('mail_sync.max_sync_limit', 5);
        if ($limit > $maxLimit) {
            $limit = $maxLimit;
            Log::info("Reduced sync limit to prevent timeout", [
                'original_limit' => $rawLimit,
                'adjusted_limit' => $limit
            ]);
        }
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        // Enforce maximum 5-day inclusive range
        if (!empty($startDate) && !empty($endDate)) {
            try {
                $sd = new \DateTime($startDate);
                $ed = new \DateTime($endDate);
                if ($sd > $ed) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Start date cannot be after end date.',
                    ], 422);
                }
                $diff = $sd->diff($ed)->days + 1; // inclusive
                $maxSyncDays = config('mail_sync.max_sync_days', 5);
                if ($diff > $maxSyncDays) {
                    return response()->json([
                        'success' => false,
                        'message' => "Selected range is too large. Please choose a range within {$maxSyncDays} days.",
                    ], 422);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date range provided.',
                ], 422);
            }
        }
        $q = trim((string) $request->get('q', ''));
        $searchFields = $request->get('search_fields', 'from,to,subject,body');
        $hasAttachments = $request->get('has_attachments', false);
        $isUnread = $request->get('is_unread', false);
        $isFlagged = $request->get('is_flagged', false);

        try {
            if ($request->isMethod('post')) {
                // Perform actual sync using Python script
                $pythonPath = $this->getPythonExecutablePath();
                $script = $this->getSyncScriptPath();
                
                // Get the correct authentication token/password
                $authToken = $account->access_token;
                if (!$authToken && $account->password) {
                    // Decrypt the password if it's encrypted
                    try {
                        $authToken = decrypt($account->password);
                    } catch (\Exception $e) {
                        // If decryption fails, use the password as-is (might be plain text)
                        $authToken = $account->password;
                    }
                }

                // Determine folders to sync
                $foldersToSync = [];
                if (strtolower($folder) === 'all') {
                    $foldersToSync = ['Inbox', 'Sent', 'Drafts', 'Trash', 'Spam'];
                } else {
                    $foldersToSync = [$folder];
                }

                $allSyncedEmails = [];
                $debugOutputs = [];
                $hadSuccessfulFolder = false;
                $hadFailedFolder = false;

                foreach ($foldersToSync as $folderName) {
                    // Try with original limit first, then retry with smaller limit if needed
                    $currentLimit = $limit;
                    $maxRetries = 2;
                    $retryCount = 0;
                    $success = false;
                    
                    while ($retryCount < $maxRetries && !$success) {
                        $args = [
                            $pythonPath,
                            $script,
                            strtolower(trim($account->provider)),
                            $account->email,
                            $authToken,
                            $folderName,
                            $currentLimit
                        ];

                        // Add date range parameters if provided
                        if ($startDate) {
                            $args[] = $startDate;
                        }
                        if ($endDate) {
                            $args[] = $endDate;
                        }
                        
                        // Add AWS credentials for S3 upload
                        $args[] = env('AWS_ACCESS_KEY_ID');
                        $args[] = env('AWS_SECRET_ACCESS_KEY');
                        $args[] = env('AWS_DEFAULT_REGION');
                        $args[] = env('AWS_BUCKET');

                        // Log sync attempt
                        Log::info("Starting email sync via controller", [
                            'account_id' => $accountId,
                            'email' => $account->email,
                            'provider' => $account->provider,
                            'folder' => $folderName,
                            'limit' => $currentLimit,
                            'retry_count' => $retryCount,
                            'timeout' => config('mail_sync.process_timeout', 600)
                        ]);

                        $process = new Process($args, base_path());
                        $processTimeout = config('mail_sync.process_timeout', 600);
                        $process->setTimeout($processTimeout);
                        
                        // Set working directory to the Python script location
                        $process->setWorkingDirectory($this->getPythonWorkingDirectory());
                        
                        // Set environment variables
                        $process->setEnv($this->getPythonEnvironmentVariables());
                        
                        Log::info("Process timeout set", [
                            'timeout' => $processTimeout,
                            'folder' => $folderName,
                            'retry_count' => $retryCount
                        ]);
                        
                        // Set environment variables to help with DNS resolution
                        $env = [
                            'PATH' => getenv('PATH'),
                            'SYSTEMROOT' => getenv('SYSTEMROOT'),
                            'WINDIR' => getenv('WINDIR'),
                            'TEMP' => getenv('TEMP'),
                            'TMP' => getenv('TMP'),
                            'PYTHONPATH' => getenv('PYTHONPATH'),
                            'PYTHONIOENCODING' => 'utf-8',
                        ];
                        
                        // Add DNS-related environment variables if available
                        if (getenv('DNS_SERVERS')) {
                            $env['DNS_SERVERS'] = getenv('DNS_SERVERS');
                        }
                        
                        $process->setEnv($env);
                        $process->run();

                        // Capture both stdout and stderr with proper encoding
                        $output = trim($process->getOutput());
                        $errorOutput = trim($process->getErrorOutput());
                        
                        // Ensure proper UTF-8 encoding
                        $output = mb_convert_encoding($output, 'UTF-8', 'auto');
                        $errorOutput = mb_convert_encoding($errorOutput, 'UTF-8', 'auto');
                        if (!empty($errorOutput)) {
                            $debugOutputs[] = [
                                'folder' => $folderName,
                                'stderr' => $errorOutput,
                                'stdout' => $output,
                                'exit_code' => $process->getExitCode(),
                                'retry_count' => $retryCount,
                            ];
                        }

                        if (!$process->isSuccessful()) {
                            $errorDetails = [
                                'account_id' => $accountId,
                                'folder' => $folderName,
                                'error_output' => $errorOutput,
                                'stdout' => $output,
                                'exit_code' => $process->getExitCode(),
                                'timeout' => method_exists($process, 'isTimedOut') ? $process->isTimedOut() : false,
                                'retry_count' => $retryCount,
                                'current_limit' => $currentLimit
                            ];
                            
                            // Check if it's a timeout error
                            if (strpos($errorOutput, 'timeout') !== false || strpos($output, 'timeout') !== false) {
                                $errorDetails['error_type'] = 'timeout';
                                Log::error("Email sync timed out", $errorDetails);
                                
                                // If timeout and we can retry, reduce limit and try again
                                if ($retryCount < $maxRetries - 1) {
                                    $currentLimit = max(10, intval($currentLimit / 2)); // Reduce limit by half, minimum 10
                                    $retryCount++;
                                    Log::info("Retrying with reduced limit", [
                                        'folder' => $folderName,
                                        'new_limit' => $currentLimit,
                                        'retry_count' => $retryCount
                                    ]);
                                    continue; // Try again with smaller limit
                                }
                            } else {
                                Log::error("Email sync failed via controller", $errorDetails);
                            }
                            
                            $hadFailedFolder = true;
                            break; // Exit retry loop
                        }

                        // Parse the JSON response from Python script with UTF-8 handling
                        $synced = $this->safeJsonDecode($output, $accountId, $folderName, $retryCount);
                        if ($synced === null) {
                            Log::error("All JSON parsing attempts failed via controller", [
                                'account_id' => $accountId,
                                'folder' => $folderName,
                                'output_preview' => substr($output, 0, 500),
                                'error_output' => substr($errorOutput, 0, 500),
                                'retry_count' => $retryCount
                            ]);
                            $hadFailedFolder = true;
                            $debugOutputs[] = [
                                'folder' => $folderName,
                                'error' => 'All JSON parsing attempts failed',
                                'json_error' => 'UTF-8 encoding and fallback strategies failed',
                                'stdout' => $output,
                                'retry_count' => $retryCount
                            ];
                            break; // Exit retry loop
                        }

                        if (isset($synced['error'])) {
                            Log::error("Email sync error via controller", [
                                'account_id' => $accountId,
                                'folder' => $folderName,
                                'error' => $synced['error'],
                                'debug_info' => $synced['debug_info'] ?? null,
                                'retry_count' => $retryCount
                            ]);
                            $hadFailedFolder = true;
                            $debugOutputs[] = [
                                'folder' => $folderName,
                                'error' => $synced['error'],
                                'debug_info' => $synced['debug_info'] ?? null,
                                'retry_count' => $retryCount
                            ];
                            break; // Exit retry loop
                        }

                        if (is_array($synced)) {
                            $allSyncedEmails = array_merge($allSyncedEmails, $synced);
                            $hadSuccessfulFolder = true;
                            $success = true; // Mark as successful
                            Log::info("Email sync successful", [
                                'folder' => $folderName,
                                'emails_synced' => count($synced),
                                'retry_count' => $retryCount,
                                'final_limit' => $currentLimit
                            ]);
                        }
                        
                        break; // Exit retry loop on success
                    }
                }

                $syncedEmails = $allSyncedEmails;

                // If no folders succeeded at all, surface an explicit error with debug details
                if (!$hadSuccessfulFolder) {
                    $response = [
                        'success' => false,
                        'message' => 'Sync failed for all folders',
                        'errors' => $debugOutputs,
                    ];
                    return response()->json($response, 422);
                }

                if (isset($syncedEmails['error'])) {
                    Log::error("Email sync error via controller", [
                        'account_id' => $accountId,
                        'error' => $syncedEmails['error'],
                        'debug_info' => $syncedEmails['debug_info'] ?? null
                    ]);
                    
                    $response = [
                        'success' => false,
                        'message' => 'Sync error: ' . $syncedEmails['error'],
                        'emails' => []
                    ];
                    
                    // Include debug information if available
                    if (isset($syncedEmails['debug_info'])) {
                        $response['debug_info'] = $syncedEmails['debug_info'];
                    }
                    
                    return response()->json($response, 422);
                }

                // Process and store emails in batches to improve performance
                $newEmailsCount = 0;
                $duplicateEmailsCount = 0;
                // Removed EmailFolderService dependency

                // Get existing message IDs to avoid duplicates (optimized query)
                $messageIds = array_filter(array_column($syncedEmails, 'message_id'));
                $existingMessageIds = [];
                if (!empty($messageIds)) {
                    $existingMessageIds = Email::where('account_id', $accountId)
                        ->whereIn('message_id', $messageIds)
                        ->pluck('message_id')
                        ->toArray();
                }

                // Prepare batch data for emails
                $emailsToCreate = [];
                $attachmentsToCreate = [];
                $labelAssignments = [];

                foreach ($syncedEmails as $emailData) {
                    // Skip if email already exists
                    if (in_array($emailData['message_id'] ?? '', $existingMessageIds)) {
                        $duplicateEmailsCount++;
                        continue;
                    }

                    // Parse common dates
                    $parsedDate = !empty($emailData['parsed_date']) ? \Carbon\Carbon::parse($emailData['parsed_date']) : null;

                    // Build recipients array from available fields
                    $recipients = [];
                    if (!empty($emailData['to'])) {
                        $recipients = is_array($emailData['to']) ? $emailData['to'] : [$emailData['to']];
                    }
                    if (!empty($emailData['cc'])) {
                        $ccList = is_array($emailData['cc']) ? $emailData['cc'] : [$emailData['cc']];
                        $recipients = array_values(array_filter(array_merge($recipients, $ccList)));
                    }

                    // Prepare email data for batch insert
                    $emailsToCreate[] = [
                        'account_id' => $accountId,
                        'user_id' => $account->user_id,
                        'message_id' => $emailData['message_id'] ?? null,
                        'from_email' => $emailData['from'] ?? null,
                        'sender_email' => $emailData['from'] ?? null,
                        'sender_name' => $emailData['from_name'] ?? ($emailData['from_display'] ?? null),
                        'to_email' => is_array($emailData['to'] ?? null) ? implode(', ', $emailData['to']) : ($emailData['to'] ?? null),
                        'cc' => is_array($emailData['cc'] ?? null) ? implode(', ', $emailData['cc']) : ($emailData['cc'] ?? null),
                        'reply_to' => $emailData['reply_to'] ?? null,
                        'recipients' => $recipients ? json_encode($recipients) : null,
                        'subject' => $emailData['subject'] ?? null,
                        'body' => $emailData['body'] ?? null,
                        'html_body' => $emailData['html_body'] ?? null,
                        'text_body' => $emailData['text_body'] ?? null,
                        'html_content' => $emailData['html_body'] ?? null,
                        'text_content' => $emailData['text_body'] ?? ($emailData['body'] ?? null),
                        'headers' => is_array($emailData['headers'] ?? null) ? json_encode($emailData['headers']) : ($emailData['headers'] ?? null),
                        'folder' => $emailData['folder'] ?? 'Inbox',
                        'received_at' => $parsedDate,
                        'sent_date' => $parsedDate,
                        'date' => $parsedDate,
                        'status' => 'completed',
                        'file_path' => $emailData['file_path'] ?? null,
                        'file_size' => $emailData['file_size'] ?? null,
                        'is_important' => false,
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $newEmailsCount++;
                }

                // Batch insert emails in chunks to avoid memory issues
                if (!empty($emailsToCreate)) {
                    $chunkSize = config('mail_sync.batch_size', 100); // Process emails in configurable batches
                    $chunks = array_chunk($emailsToCreate, $chunkSize);
                    
                    foreach ($chunks as $chunk) {
                        Email::insert($chunk);
                    }
                    
                    // Get the created emails for further processing (optimized query)
                    $createdMessageIds = array_column($emailsToCreate, 'message_id');
                    $createdEmails = Email::where('account_id', $accountId)
                        ->whereIn('message_id', $createdMessageIds)
                        ->get()
                        ->keyBy('message_id');

                    // Process attachments and labels for each created email
                    foreach ($syncedEmails as $emailData) {
                        if (!isset($createdEmails[$emailData['message_id'] ?? ''])) {
                            continue;
                        }
                        
                        $email = $createdEmails[$emailData['message_id']];

                        // Save email content to local storage (async operation)
                        if (!empty($emailData['message_id']) && !empty($emailData['folder'])) {
                            try {
                                $emailContent = $this->buildEmailContent($emailData);
                                $folderService->saveEmailToFile(
                                    $account, 
                                    $emailData['folder'], 
                                    $emailData['message_id'], 
                                    $emailContent
                                );
                            } catch (\Exception $e) {
                                Log::warning('Failed to save email to file', [
                                    'email_id' => $email->id,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }

                        // Process attachments
                        if (!empty($emailData['attachments']) && is_array($emailData['attachments'])) {
                            foreach ($emailData['attachments'] as $att) {
                                try {
                                    $email->attachments()->create([
                                        'filename' => $att['filename'] ?? 'attachment',
                                        'display_name' => $att['display_name'] ?? ($att['filename'] ?? 'attachment'),
                                        'content_type' => $att['content_type'] ?? null,
                                        'file_size' => $att['file_size'] ?? 0,
                                        'file_path' => $att['file_path'] ?? null,
                                        'content_id' => $att['content_id'] ?? null,
                                        'is_inline' => !empty($att['is_inline']),
                                        'headers' => $att['headers'] ?? null,
                                        'extension' => $att['extension'] ?? null,
                                    ]);
                                } catch (\Throwable $t) {
                                    Log::warning('Attachment persistence failed', [
                                        'email_id' => $email->id,
                                        'error' => $t->getMessage(),
                                    ]);
                                }
                            }
                        }

                        // Apply system label for the folder
                        if (!empty($emailData['folder'])) {
                            try {
                                $labelName = ucfirst(strtolower($emailData['folder']));
                                $label = \App\Models\Label::firstOrCreate(
                                    ['user_id' => $account->user_id, 'name' => $labelName],
                                    ['type' => 'system', 'color' => '#6B7280']
                                );
                                $email->labels()->syncWithoutDetaching([$label->id]);
                            } catch (\Throwable $t) {
                                Log::warning('Label assignment failed', [
                                    'email_id' => $email->id,
                                    'error' => $t->getMessage(),
                                ]);
                            }
                        }
                    }
                }

                // Build a friendly, well‑pluralized message with nicer dates
                $emailWord = $newEmailsCount === 1 ? 'email' : 'emails';
                $duplicateWord = $duplicateEmailsCount === 1 ? 'duplicate' : 'duplicates';

                if ($newEmailsCount === 0) {
                    $message = "You're all caught up — no new emails in {$folder}.";
                } else {
                    $message = "Synced {$newEmailsCount} new {$emailWord} from {$folder}.";
                }

                if ($duplicateEmailsCount > 0) {
                    $message .= " ({$duplicateEmailsCount} {$duplicateWord} skipped)";
                }

                if ($startDate || $endDate) {
                    $startFormatted = $startDate ? \Carbon\Carbon::parse($startDate)->format('M j, Y') : null;
                    $endFormatted = $endDate ? \Carbon\Carbon::parse($endDate)->format('M j, Y') : null;

                    if ($startFormatted && $endFormatted) {
                        $message .= " for {$startFormatted} – {$endFormatted}";
                    } elseif ($startFormatted) {
                        $message .= " since {$startFormatted}";
                    } elseif ($endFormatted) {
                        $message .= " up to {$endFormatted}";
                    }
                }
            }

            // Build listing query with optional folder, dates, and search
            $query = Email::where('account_id', $accountId);

            if (strtolower($folder) !== 'all') {
                $query->where('folder', $folder);
            }

            if ($startDate) {
                $query->where(function ($q2) use ($startDate) {
                    $q2->whereDate('received_at', '>=', $startDate)
                        ->orWhereDate('date', '>=', $startDate);
                });
            }
            if ($endDate) {
                $query->where(function ($q2) use ($endDate) {
                    $q2->whereDate('received_at', '<=', $endDate)
                        ->orWhereDate('date', '<=', $endDate);
                });
            }

            if (!empty($q)) {
                $like = '%' . str_replace(['%','_'], ['\\%','\\_'], $q) . '%';
                $searchFieldsArray = explode(',', $searchFields);
                
                $query->where(function ($q3) use ($like, $searchFieldsArray) {
                    if (in_array('subject', $searchFieldsArray)) {
                        $q3->orWhere('subject', 'like', $like);
                    }
                    if (in_array('from', $searchFieldsArray)) {
                        $q3->orWhere('from_email', 'like', $like);
                    }
                    if (in_array('to', $searchFieldsArray)) {
                        $q3->orWhere('to_email', 'like', $like);
                    }
                    if (in_array('cc', $searchFieldsArray)) {
                        $q3->orWhere('cc', 'like', $like);
                    }
                    if (in_array('reply_to', $searchFieldsArray)) {
                        $q3->orWhere('reply_to', 'like', $like);
                    }
                    if (in_array('body', $searchFieldsArray)) {
                        $q3->orWhere('text_body', 'like', $like)
                           ->orWhere('body', 'like', $like);
                    }
                });
            }
            
            // Apply additional filters
            if ($hasAttachments) {
                $query->whereHas('attachments');
            }
            
            if ($isUnread) {
                // For now, we'll assume all emails are unread if this filter is applied
                // TODO: Add read/unread status to emails table
                $query->where('is_read', false);
            }
            
            if ($isFlagged) {
                // For now, we'll assume all emails are unflagged if this filter is applied
                // TODO: Add flagged status to emails table
                $query->where('is_flagged', true);
            }

            // Return emails from database with attachments
            $emails = $query
                ->with('attachments')
                ->orderBy('received_at', 'desc')
                ->limit($limit)
                ->get(['id', 'from_email', 'to_email', 'subject', 'received_at', 'date', 'created_at', 'body', 'text_body', 'html_body', 'cc', 'reply_to', 'headers', 'is_read', 'is_flagged', 'assign_client_id', 'assign_client_matter_id', 'mail_report_tbl_id', 'folder'])
                ->map(function ($email, $index) {
                    $attachments = $email->attachments->map(function ($attachment) {
                        return [
                            'id' => $attachment->id,
                            'filename' => $attachment->filename,
                            'display_name' => $attachment->display_name,
                            'content_type' => $attachment->content_type,
                            'file_size' => $attachment->file_size,
                            'formatted_file_size' => $attachment->formatted_file_size,
                            'extension' => $attachment->extension,
                            'is_inline' => $attachment->is_inline,
                            'can_preview' => $attachment->canPreview(),
                            'preview_type' => $attachment->getPreviewType(),
                        ];
                    });

                    // Get client and matter names if email is allocated
                    $clientName = null;
                    $matterName = null;
                    
                    // Debug log to check allocation data (temporarily disabled)
                    // if ($index < 5) {
                    //     Log::info("Email allocation check - ID: {$email->id}, Client ID: " . ($email->assign_client_id ?? 'null') . ", Matter ID: " . ($email->assign_client_matter_id ?? 'null'));
                    // }
                    
                    // Check for allocation data - handle both cases
                    if ($email->assign_client_id || $email->assign_client_matter_id) {
                        // Simple debug output (only for first email to avoid spam)
                        if ($index == 0) {
                            error_log("ALLOCATION DEBUG: Email ID {$email->id} has Client ID: {$email->assign_client_id}, Matter ID: {$email->assign_client_matter_id}");
                        }
                        try {
                            // Get client name if client_id is set
                            if ($email->assign_client_id) {
                                $client = \App\Models\Admin::where('id', $email->assign_client_id)
                                    ->where('role', 7)
                                    ->select('first_name', 'last_name', 'client_id')
                                    ->first();
                                
                                if ($client) {
                                    $clientName = trim($client->first_name . ' ' . $client->last_name);
                                    if ($client->client_id) {
                                        $clientName .= ' (' . $client->client_id . ')';
                                    }
                                } else {
                                    // Log missing client for debugging (temporarily disabled)
                                    // Log::warning("Email allocation: Client not found - Email ID: {$email->id}, Client ID: {$email->assign_client_id}");
                                }
                            }
                            
                            // Get matter name if matter_id is set
                            if ($email->assign_client_matter_id) {
                                $matter = \App\Models\ClientMatter::where('client_matters.id', $email->assign_client_matter_id)
                                    ->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                                    ->select('client_matters.client_unique_matter_no', 'matters.title', 'client_matters.sel_matter_id')
                                    ->first();
                                
                                if ($matter) {
                                    // Handle General Matter case (sel_matter_id = 1)
                                    if ($matter->sel_matter_id == 1 || empty($matter->title)) {
                                        $matterName = 'General Matter';
                                    } else {
                                        $matterName = $matter->title;
                                    }
                                    
                                    // Add client unique matter number if available
                                    if (!empty($matter->client_unique_matter_no)) {
                                        $matterName .= ' (' . $matter->client_unique_matter_no . ')';
                                    }
                                } else {
                                    // Log missing matter for debugging (temporarily disabled)
                                    // Log::warning("Email allocation: Matter not found - Email ID: {$email->id}, Matter ID: {$email->assign_client_matter_id}");
                                }
                            }
                            
                        } catch (\Exception $e) {
                            // Log any database errors (temporarily disabled)
                            // Log::error("Email allocation: Error retrieving client/matter names - Email ID: {$email->id}, Error: " . $e->getMessage());
                        }
                        
                        // Debug output for first email to see final result
                        if ($index == 0) {
                            error_log("ALLOCATION RESULT: Email ID {$email->id} - Client Name: " . ($clientName ?? 'null') . ", Matter Name: " . ($matterName ?? 'null'));
                        }
                    }

                    $emailData = [
                        'id' => $email->id,
                        'from' => $email->from_email,
                        'to' => $email->to_email,
                        'subject' => $email->subject,
                        'date' => $email->received_at ? $email->received_at->toISOString() : ($email->date ? $email->date->toISOString() : null),
                        'received_at' => $email->received_at ? $email->received_at->toISOString() : null,
                        'created_at' => $email->created_at ? $email->created_at->toISOString() : null,
                        'snippet' => $email->body ? substr(strip_tags($email->body), 0, 100) . '...' : 'No content',
                        'body' => $email->text_body ?? $email->body,
                        'html_body' => $email->html_body,
                        'cc' => $email->cc,
                        'reply_to' => $email->reply_to,
                        'headers' => $email->headers,
                        'has_attachment' => $attachments->count() > 0,
                        'is_read' => $email->is_read ?? false,
                        'is_flagged' => $email->is_flagged ?? false,
                        'assign_client_id' => $email->assign_client_id,
                        'assign_client_matter_id' => $email->assign_client_matter_id,
                        'assign_client_name' => $clientName,
                        'assign_matter_name' => $matterName,
                        'attachments' => $attachments->toArray()
                    ];
                    
                    // Debug logging for allocated emails (temporarily disabled)
                    // if ($email->assign_client_id || $email->assign_client_matter_id) {
                    //     Log::info("Email allocation result - ID: {$email->id}, Client Name: " . ($clientName ?? 'null') . ", Matter Name: " . ($matterName ?? 'null'));
                    // }
                    
                    return $emailData;
                });

            return response()->json([
                'success' => true,
                'message' => $message ?? 'Emails loaded successfully',
                'emails' => $emails
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'emails' => []
            ], 500);
        }
    }

    /**
     * Load existing emails from database (GET requests)
     */
    private function loadEmails($accountId, Request $request)
    {
        try {
            $account = EmailAccount::where('id', $accountId)
                ->where('user_id', Auth::guard('email_users')->id())
                ->firstOrFail();

            $folder = $request->get('folder', 'Inbox');
            $limit = min((int) $request->get('limit', 25), 100); // Default 25, max 100 for performance
            $offset = (int) $request->get('offset', 0); // For infinite scroll pagination
            $searchQuery = $request->get('q', '');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            // Build query to load emails from database
            $query = Email::on('second_db')
                ->where('account_id', $accountId)
                ->where('folder', $folder);

            // Add date filters only if both dates are provided (specific date range search)
            if ($startDate && $endDate) {
                $query->whereDate('date', '>=', $startDate)
                      ->whereDate('date', '<=', $endDate);
            } elseif ($startDate && !$endDate) {
                // If only start date, show emails from that date onwards
                $query->whereDate('date', '>=', $startDate);
            } elseif (!$startDate && $endDate) {
                // If only end date, show emails up to that date
                $query->whereDate('date', '<=', $endDate);
            }
            // If no dates provided, show all emails (no date filter)

            // Add search filter if provided
            if ($searchQuery) {
                $query->where(function($q) use ($searchQuery) {
                    $q->where('subject', 'like', "%{$searchQuery}%")
                      ->orWhere('from_email', 'like', "%{$searchQuery}%")
                      ->orWhere('body', 'like', "%{$searchQuery}%");
                });
            }

            // Debug: Check total emails in database
            $totalEmailsInDb = Email::on('second_db')->where('account_id', $accountId)->count();
            $totalInFolder = Email::on('second_db')->where('account_id', $accountId)->where('folder', $folder)->count();
            
            // Log::info("Email loading debug - Account: {$accountId}, Folder: {$folder}, Total: {$totalInFolder}, Limit: {$limit}, Offset: {$offset}");

            // Get emails with offset-based pagination for infinite scroll
            $emails = $query->orderBy('date', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get(['id', 'from_email', 'to_email', 'subject', 'received_at', 'date', 'created_at', 'body', 'text_body', 'html_body', 'cc', 'reply_to', 'headers', 'is_read', 'is_flagged', 'assign_client_id', 'assign_client_matter_id', 'mail_report_tbl_id', 'folder'])
                ->map(function ($email, $index) {
                    // Get client and matter names if email is allocated
                    $clientName = null;
                    $matterName = null;
                    
                    // Check for allocation data
                    if ($email->assign_client_id || $email->assign_client_matter_id) {
                        // Debug output for first email
                        if ($index == 0) {
                            error_log("ALLOCATION DEBUG: Email ID {$email->id} has Client ID: {$email->assign_client_id}, Matter ID: {$email->assign_client_matter_id}");
                        }
                        
                        try {
                            // Get client name if client_id is set
                            if ($email->assign_client_id) {
                                $client = \App\Models\Admin::where('id', $email->assign_client_id)
                                    ->where('role', 7)
                                    ->select('first_name', 'last_name', 'client_id')
                                    ->first();
                                
                                if ($client) {
                                    $clientName = trim($client->first_name . ' ' . $client->last_name);
                                    if ($client->client_id) {
                                        $clientName .= ' (' . $client->client_id . ')';
                                    }
                                }
                            }
                            
                            // Get matter name if matter_id is set
                            if ($email->assign_client_matter_id) {
                                $matter = \App\Models\ClientMatter::where('client_matters.id', $email->assign_client_matter_id)
                                    ->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                                    ->select('client_matters.client_unique_matter_no', 'matters.title', 'client_matters.sel_matter_id')
                                    ->first();
                                
                                if ($matter) {
                                    // Handle General Matter case (sel_matter_id = 1)
                                    if ($matter->sel_matter_id == 1 || empty($matter->title)) {
                                        $matterName = 'General Matter';
                                    } else {
                                        $matterName = $matter->title;
                                    }
                                    
                                    // Add client unique matter number if available
                                    if (!empty($matter->client_unique_matter_no)) {
                                        $matterName .= ' (' . $matter->client_unique_matter_no . ')';
                                    }
                                }
                            }
                            
                        } catch (\Exception $e) {
                            // Handle errors silently
                        }
                        
                        // Debug output for first email result
                        if ($index == 0) {
                            error_log("ALLOCATION RESULT: Email ID {$email->id} - Client Name: " . ($clientName ?? 'null') . ", Matter Name: " . ($matterName ?? 'null'));
                        }
                    }
                    
                    return [
                        'id' => $email->id,
                        'from' => $email->from_email,
                        'to' => $email->to_email,
                        'subject' => $email->subject,
                        'date' => $email->date ? $email->date->toISOString() : null,
                        'received_at' => $email->received_at ? $email->received_at->toISOString() : null,
                        'created_at' => $email->created_at ? $email->created_at->toISOString() : null,
                        'snippet' => $email->body ? substr(strip_tags($email->body), 0, 100) . '...' : 'No content',
                        'body' => $email->text_body ?? $email->body,
                        'html_body' => $email->html_body,
                        'cc' => $email->cc,
                        'reply_to' => $email->reply_to,
                        'headers' => $email->headers,
                        'folder' => $email->folder,
                        'has_attachment' => false, // Will be updated when attachments are loaded
                        'is_read' => $email->is_read ?? false,
                        'is_flagged' => $email->is_flagged ?? false,
                        'assign_client_id' => $email->assign_client_id,
                        'assign_client_matter_id' => $email->assign_client_matter_id,
                        'assign_client_name' => $clientName,
                        'assign_matter_name' => $matterName,
                        'attachments' => []
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Emails loaded successfully',
                'emails' => $emails,
                'pagination' => [
                    'current_offset' => $offset,
                    'limit' => $limit,
                    'loaded_count' => count($emails),
                    'has_more' => count($emails) >= $limit,
                    'total_in_folder' => $totalInFolder
                ]
            ], 200, [
                'Content-Type' => 'application/json'
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to load emails", [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading emails: ' . $e->getMessage(),
                'emails' => []
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
        }
    }

    /**
     * Sync emails with pagination using robust connection handling
     * Each batch uses a fresh IMAP connection to prevent socket errors
     */
    public function syncWithPagination($accountId, Request $request)
    {
        // Set appropriate timeout for robust batch processing
        set_time_limit(600); // 10 minutes per batch for safety
        ini_set('memory_limit', config('mail_sync.memory_limit', '1G'));

        $account = EmailAccount::where('id', $accountId)
            ->where('user_id', Auth::guard('email_users')->id())
            ->firstOrFail();

        // Validate provider
        $provider = $account->provider ? strtolower(trim($account->provider)) : null;
        if (empty($provider) || !in_array($provider, ['zoho'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing provider. Only zoho is supported.',
            ], 422);
        }

        $folder = $request->get('folder', 'Inbox');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $offset = (int) $request->get('offset', 0);
        $batchSize = config('mail_sync.batch_size', 10); // Default to 10 emails per batch for efficiency

        // Validate date range (max 5 days)
        if (!empty($startDate) && !empty($endDate)) {
            try {
                $sd = new \DateTime($startDate);
                $ed = new \DateTime($endDate);
                if ($sd > $ed) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Start date cannot be after end date.',
                    ], 422);
                }
                $diff = $sd->diff($ed)->days + 1;
                if ($diff > 5) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected range is too large. Please choose a range within 5 days.',
                    ], 422);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date range provided.',
                ], 422);
            }
        }

        try {
            // Get Python executable path (cross-platform with better detection)
            $pythonPath = $this->getPythonExecutablePathWithFallback();
            $script = $this->getSyncScriptPathWithValidation();

            // Get authentication token
            $authToken = $account->access_token;
            if (!$authToken && $account->password) {
                try {
                    $authToken = decrypt($account->password);
                } catch (\Exception $e) {
                    $authToken = $account->password;
                }
            }

            // Build arguments for Python script with pagination
            $args = [
                $pythonPath,
                $script,
                strtolower(trim($account->provider)),
                $account->email,
                $authToken,
                $folder,
                $batchSize,
                $startDate,
                $endDate,
                env('AWS_ACCESS_KEY_ID'),
                env('AWS_SECRET_ACCESS_KEY'),
                env('AWS_DEFAULT_REGION'),
                env('AWS_BUCKET'),
                $offset  // Add offset for pagination
            ];

            Log::info("Starting robust paginated email sync", [
                'account_id' => $accountId,
                'folder' => $folder,
                'batch_size' => $batchSize,
                'offset' => $offset,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'python_path' => $pythonPath,
                'script_path' => $script,
                'args_count' => count($args),
                'strategy' => 'fresh_connection_per_batch'
            ]);

            $process = new Process($args, base_path());
            $process->setTimeout(600); // 10 minutes timeout for robust processing
            $process->setWorkingDirectory($this->getPythonWorkingDirectory());
            
            // Enhanced environment variables for better network access
            $env = $this->getPythonEnvironmentVariables();
            $env['PYTHONPATH'] = base_path('python_outlook_web');
            $env['PYTHONIOENCODING'] = 'utf-8';
            $env['PYTHONUNBUFFERED'] = '1';
            // Add Windows network environment variables
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $env['PATH'] = getenv('PATH');
                $env['SYSTEMROOT'] = getenv('SYSTEMROOT');
                $env['WINDIR'] = getenv('WINDIR');
            }
            $process->setEnv($env);
            // Execute the process
            $process->run();

            $output = trim($process->getOutput());
            $errorOutput = trim($process->getErrorOutput());

            // Enhanced error logging with robust connection details
            Log::info("Robust process execution completed", [
                'account_id' => $accountId,
                'offset' => $offset,
                'exit_code' => $process->getExitCode(),
                'successful' => $process->isSuccessful(),
                'output_length' => strlen($output),
                'error_output_length' => strlen($errorOutput),
                'command_line' => $process->getCommandLine(),
                'connection_strategy' => 'fresh_per_batch'
            ]);

            if (!$process->isSuccessful()) {
                // Check if it's a specific IMAP connection error
                $isImapError = strpos($errorOutput, 'IMAP') !== false || 
                              strpos($errorOutput, 'socket') !== false ||
                              strpos($output, 'IMAP') !== false;
                
                // Log detailed error information
                Log::error("Robust email sync process failed", [
                    'account_id' => $accountId,
                    'offset' => $offset,
                    'exit_code' => $process->getExitCode(),
                    'error_output' => $errorOutput,
                    'stdout' => $output,
                    'command' => $process->getCommandLine(),
                    'is_imap_error' => $isImapError,
                    'python_path' => $pythonPath,
                    'script_path' => $script
                ]);
                
                $errorMessage = $errorOutput ?: $output ?: 'Unknown error';
                
                return response()->json([
                    'success' => false,
                    'message' => $isImapError ? 
                        'IMAP connection error - this batch will be retried with fresh connection' : 
                        'Sync failed: ' . $errorMessage,
                    'hasMore' => $isImapError, // Allow retry for IMAP errors
                    'imap_error' => $isImapError,
                    'retry_recommended' => $isImapError,
                    'debug_info' => [
                        'exit_code' => $process->getExitCode(),
                        'error_type' => $isImapError ? 'imap_connection' : 'general',
                        'error_output' => substr($errorOutput, 0, 500),
                        'stdout' => substr($output, 0, 500)
                    ]
                ], 422);
            }

            // Parse JSON response from robust script with UTF-8 handling
            $synced = $this->safeJsonDecode($output, $accountId, 'robust_script', 0, $offset);
            if ($synced === null) {
                Log::error("JSON parsing failed from robust script", [
                    'account_id' => $accountId,
                    'offset' => $offset,
                    'output_preview' => substr($output, 0, 500),
                    'output_length' => strlen($output)
                ]);
                
                // Try to extract JSON from mixed output (handles debug output)
                $jsonStart = strpos($output, '{');
                $jsonEnd = strrpos($output, '}');
                
                if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
                    $jsonString = substr($output, $jsonStart, $jsonEnd - $jsonStart + 1);
                    $synced = $this->safeJsonDecode($jsonString, $accountId, 'robust_script_extracted', 0, $offset);
                    
                    if ($synced !== null) {
                        Log::info("Successfully extracted and parsed JSON from robust script output", [
                            'account_id' => $accountId,
                            'offset' => $offset
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid JSON response from robust sync script: ' . json_last_error_msg(),
                            'hasMore' => false,
                            'debug_info' => [
                                'output_preview' => substr($output, 0, 200),
                                'json_error' => json_last_error_msg(),
                                'script_type' => 'robust'
                            ]
                        ], 422);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'No valid JSON found in robust script output',
                        'hasMore' => false,
                        'debug_info' => [
                            'output_preview' => substr($output, 0, 200),
                            'script_type' => 'robust'
                        ]
                    ], 422);
                }
            }

            // Check if robust script returned an error
            if (isset($synced['success']) && $synced['success'] === false) {
                Log::error("Robust script returned error", [
                    'account_id' => $accountId,
                    'offset' => $offset,
                    'error' => $synced['error'] ?? 'Unknown error'
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $synced['error'] ?? 'Robust sync script failed',
                    'hasMore' => false,
                    'script_error' => true
                ], 422);
            }

            if (isset($synced['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $synced['error'],
                    'hasMore' => false
                ], 422);
            }

            // Process and store emails with duplicate prevention
            $newEmailsCount = 0;
            $duplicateCount = 0;
            
            // Handle robust script response format
            $emailsToProcess = [];
            if (isset($synced['emails']) && is_array($synced['emails'])) {
                // New robust script format
                $emailsToProcess = $synced['emails'];
            } elseif (is_array($synced)) {
                // Legacy format fallback
                $emailsToProcess = $synced;
            }

            foreach ($emailsToProcess as $emailData) {
                // Check for duplicates using available fields
                if ($this->isDuplicateEmailByData($emailData, $accountId)) {
                    $duplicateCount++;
                    continue;
                }

                // Save new email
                $this->saveEmailData($emailData, $account);
                $newEmailsCount++;
            }

            // Determine if there are more emails to sync
            $actualEmailCount = count($emailsToProcess);
            $hasMore = $actualEmailCount >= $batchSize;

            Log::info("Robust paginated sync completed successfully", [
                'account_id' => $accountId,
                'offset' => $offset,
                'new_emails' => $newEmailsCount,
                'duplicates_skipped' => $duplicateCount,
                'has_more' => $hasMore,
                'connection_strategy' => 'fresh_per_batch',
                'script_type' => 'robust'
            ]);

            // Ensure proper JSON response headers
            return response()->json([
                'success' => true,
                'message' => "Synced {$newEmailsCount} new emails (skipped {$duplicateCount} duplicates) using robust connection",
                'emails' => $emailsToProcess, // Return processed emails
                'newCount' => $newEmailsCount,
                'duplicateCount' => $duplicateCount,
                'hasMore' => $hasMore,
                'nextOffset' => $offset + $batchSize,
                'currentOffset' => $offset,
                'connectionStrategy' => 'fresh_per_batch',
                'scriptType' => 'robust'
            ], 200, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate'
            ]);

        } catch (\Exception $e) {
            Log::error("Paginated sync exception", [
                'account_id' => $accountId,
                'offset' => $offset,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Ensure JSON response even on error
            return response()->json([
                'success' => false,
                'message' => 'Sync error: ' . $e->getMessage(),
                'hasMore' => false
            ], 500, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache'
            ]);
        }
    }

    /**
     * Check if email already exists to prevent duplicates using available database fields
     */
    private function isDuplicateEmailByData($emailData, $accountId)
    {
        try {
            $query = Email::on('second_db') // Use second_db connection
                ->where('account_id', $accountId)
                ->where('subject', $emailData['subject'] ?? '')
                ->where('from_email', $emailData['from'] ?? '');
            
            // Add date filter if available
            if (isset($emailData['date'])) {
                try {
                    $emailDate = new \DateTime($emailData['date']);
                    $query->where('date', $emailDate);
                } catch (\Exception $e) {
                    // If date parsing fails, ignore date filter
                }
            }
            
            return $query->exists();
        } catch (\Exception $e) {
            Log::error("Duplicate check failed", [
                'account_id' => $accountId,
                'subject' => $emailData['subject'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            // If duplicate check fails, assume it's not a duplicate to avoid losing emails
            return false;
        }
    }

    /**
     * Save email data to database
     */
    private function saveEmailData($emailData, $account)
    {
        try {
            // Create email record directly (no folder dependency)
            $folderName = $emailData['folder'] ?? 'Inbox';

            // Create email record using only existing database columns with second_db connection
            $email = new Email();
            $email->setConnection('second_db'); // Ensure we use second_db connection
            $email->account_id = $account->id;
            $email->folder = $folderName;
            $email->from_email = $emailData['from'] ?? '';
            $email->subject = $emailData['subject'] ?? '';
            $email->body = $emailData['body'] ?? $emailData['text_body'] ?? '';
            $email->date = isset($emailData['date']) ? new \DateTime($emailData['date']) : null;
            $email->received_at = isset($emailData['received_at']) ? new \DateTime($emailData['received_at']) : null;
            $email->save();

            // Save attachments using the Attachment model with second_db connection
            if (!empty($emailData['attachments'])) {
                foreach ($emailData['attachments'] as $attachmentData) {
                    $this->saveAttachment($email->id, $attachmentData);
                }
            }

            return $email;
        } catch (\Exception $e) {
            Log::error("Failed to save email", [
                'message_id' => $emailData['message_id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Save email attachment using the Attachment model with second_db connection
     */
    private function saveAttachment($emailId, $attachmentData)
    {
        try {
            // Use the Attachment model which connects to second_db
            $attachment = new Attachment();
            $attachment->setConnection('second_db'); // Ensure second_db connection
            $attachment->email_id = $emailId;
            $attachment->filename = $attachmentData['filename'] ?? 'unknown';
            $attachment->display_name = $attachmentData['display_name'] ?? $attachmentData['filename'] ?? 'unknown';
            $attachment->content_type = $attachmentData['content_type'] ?? 'application/octet-stream';
            $attachment->file_size = $attachmentData['file_size'] ?? 0;
            $attachment->file_path = $attachmentData['file_path'] ?? null;
            $attachment->content_id = $attachmentData['content_id'] ?? null;
            $attachment->is_inline = $attachmentData['is_inline'] ?? false;
            $attachment->description = null; // Not provided in email data
            $attachment->headers = $attachmentData['headers'] ?? [];
            $attachment->extension = $attachmentData['extension'] ?? '';
            $attachment->save();

            Log::info("Attachment saved successfully", [
                'email_id' => $emailId,
                'filename' => $attachment->filename,
                'file_size' => $attachment->file_size
            ]);

            return $attachment;
        } catch (\Exception $e) {
            Log::error("Failed to save attachment", [
                'email_id' => $emailId,
                'filename' => $attachmentData['filename'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Safely decode JSON with UTF-8 handling and multiple fallback strategies
     * This method prevents "Malformed UTF-8 characters, possibly incorrectly encoded" errors
     */
    private function safeJsonDecode($jsonString, $accountId = null, $context = '', $retryCount = 0, $offset = null)
    {
        // Strategy 1: Try original string first (for backward compatibility)
        $decoded = json_decode($jsonString, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        $originalError = json_last_error_msg();
        
        // Strategy 2: Clean UTF-8 encoding
        try {
            $cleanString = mb_convert_encoding($jsonString, 'UTF-8', 'auto');
            $decoded = json_decode($cleanString, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                Log::info("JSON parsed successfully after UTF-8 conversion", [
                    'account_id' => $accountId,
                    'context' => $context,
                    'retry_count' => $retryCount,
                    'offset' => $offset,
                    'original_error' => $originalError
                ]);
                return $decoded;
            }
        } catch (Exception $e) {
            Log::warning("UTF-8 conversion failed", [
                'account_id' => $accountId,
                'context' => $context,
                'error' => $e->getMessage()
            ]);
        }
        
        // Strategy 3: Remove invalid UTF-8 sequences (PHP 7.2+)
        if (function_exists('mb_scrub')) {
            try {
                $scrubbedString = mb_scrub($jsonString, 'UTF-8');
                $decoded = json_decode($scrubbedString, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    Log::info("JSON parsed successfully after UTF-8 scrubbing", [
                        'account_id' => $accountId,
                        'context' => $context,
                        'retry_count' => $retryCount,
                        'offset' => $offset,
                        'original_error' => $originalError
                    ]);
                    return $decoded;
                }
            } catch (Exception $e) {
                Log::warning("UTF-8 scrubbing failed", [
                    'account_id' => $accountId,
                    'context' => $context,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Strategy 4: Use JSON_INVALID_UTF8_IGNORE flag (PHP 7.2+)
        if (defined('JSON_INVALID_UTF8_IGNORE')) {
            try {
                $decoded = json_decode($jsonString, true, 512, JSON_INVALID_UTF8_IGNORE);
                if (json_last_error() === JSON_ERROR_NONE) {
                    Log::info("JSON parsed successfully with UTF-8 ignore flag", [
                        'account_id' => $accountId,
                        'context' => $context,
                        'retry_count' => $retryCount,
                        'offset' => $offset,
                        'original_error' => $originalError
                    ]);
                    return $decoded;
                }
            } catch (Exception $e) {
                Log::warning("JSON decode with UTF-8 ignore flag failed", [
                    'account_id' => $accountId,
                    'context' => $context,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Strategy 5: Character replacement and sanitization
        try {
            // Remove control characters and non-printable characters
            $sanitized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $jsonString);
            
            // Replace common problematic characters
            $replacements = [
                '\u0000' => '',
                '\u0001' => '',
                '\u0002' => '',
                '\u0003' => '',
                '\u0004' => '',
                '\u0005' => '',
                '\u0006' => '',
                '\u0007' => '',
                '\u0008' => '',
                '\u000B' => '',
                '\u000C' => '',
                '\u000E' => '',
                '\u000F' => '',
                '\u0010' => '',
                '\u0011' => '',
                '\u0012' => '',
                '\u0013' => '',
                '\u0014' => '',
                '\u0015' => '',
                '\u0016' => '',
                '\u0017' => '',
                '\u0018' => '',
                '\u0019' => '',
                '\u001A' => '',
                '\u001B' => '',
                '\u001C' => '',
                '\u001D' => '',
                '\u001E' => '',
                '\u001F' => '',
                '\u007F' => ''
            ];
            
            $sanitized = str_replace(array_keys($replacements), array_values($replacements), $sanitized);
            
            // Force UTF-8 encoding
            $sanitized = mb_convert_encoding($sanitized, 'UTF-8', 'UTF-8');
            
            $decoded = json_decode($sanitized, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                Log::info("JSON parsed successfully after character sanitization", [
                    'account_id' => $accountId,
                    'context' => $context,
                    'retry_count' => $retryCount,
                    'offset' => $offset,
                    'original_error' => $originalError,
                    'sanitization_applied' => true
                ]);
                return $decoded;
            }
        } catch (Exception $e) {
            Log::warning("Character sanitization failed", [
                'account_id' => $accountId,
                'context' => $context,
                'error' => $e->getMessage()
            ]);
        }
        
        // Strategy 6: Last resort - try to extract valid JSON structure
        try {
            // Look for array or object patterns
            if (preg_match('/\[.*\]/s', $jsonString, $matches)) {
                $decoded = json_decode($matches[0], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    Log::info("JSON parsed successfully after array extraction", [
                        'account_id' => $accountId,
                        'context' => $context,
                        'retry_count' => $retryCount,
                        'offset' => $offset,
                        'original_error' => $originalError,
                        'extraction_method' => 'array_pattern'
                    ]);
                    return $decoded;
                }
            }
            
            if (preg_match('/\{.*\}/s', $jsonString, $matches)) {
                $decoded = json_decode($matches[0], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    Log::info("JSON parsed successfully after object extraction", [
                        'account_id' => $accountId,
                        'context' => $context,
                        'retry_count' => $retryCount,
                        'offset' => $offset,
                        'original_error' => $originalError,
                        'extraction_method' => 'object_pattern'
                    ]);
                    return $decoded;
                }
            }
        } catch (Exception $e) {
            Log::warning("Pattern extraction failed", [
                'account_id' => $accountId,
                'context' => $context,
                'error' => $e->getMessage()
            ]);
        }
        
        // All strategies failed
        Log::error("All JSON parsing strategies failed", [
            'account_id' => $accountId,
            'context' => $context,
            'retry_count' => $retryCount,
            'offset' => $offset,
            'original_error' => $originalError,
            'final_error' => json_last_error_msg(),
            'string_length' => strlen($jsonString),
            'string_preview' => substr($jsonString, 0, 200)
        ]);
        
        return null;
    }

    /**
     * Get Python executable path with enhanced cross-platform detection and fallback
     */
    private function getPythonExecutablePathWithFallback()
    {
        // Try multiple Python paths in order of preference
        $pythonPaths = [];
        
        // Check if we're on Windows or Linux
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        
        if ($isWindows) {
            // Windows paths - prioritize venv Python (as requested)
            $pythonPaths = [
                base_path('python_outlook_web/venv/Scripts/python.exe'),  // venv first
                base_path('python_outlook_web\\venv\\Scripts\\python.exe'),
                'python.exe',  // System Python as fallback
                'python',
                'py -3',
                'C:\\Python313\\python.exe',
                'C:\\Python39\\python.exe',
                'C:\\Python38\\python.exe',
                'C:\\Python37\\python.exe',
            ];
        } else {
            // Linux paths
            $pythonPaths = [
                base_path('python_outlook_web/venv/bin/python'),
                base_path('python_outlook_web/venv/bin/python3'),
                '/usr/bin/python3',
                '/usr/bin/python',
                'python3',
                'python',
            ];
        }
        
        // Test each path to find working Python executable
        foreach ($pythonPaths as $pythonPath) {
            if ($this->testPythonExecutable($pythonPath)) {
                Log::info("Using Python executable: {$pythonPath}");
                return $pythonPath;
            }
        }
        
        // Fallback to config or default
        $configPath = config('mail_sync.python_executable');
        if ($configPath && $this->testPythonExecutable($configPath)) {
            return $configPath;
        }
        
        // Final fallback
        return $isWindows ? 'python.exe' : 'python3';
    }
    
    /**
     * Test if Python executable works
     */
    private function testPythonExecutable($pythonPath)
    {
        try {
            $process = new Process([$pythonPath, '--version'], base_path());
            $process->setTimeout(10);
            $process->run();
            
            return $process->isSuccessful();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get sync script path with validation
     */
    private function getSyncScriptPathWithValidation()
    {
        $possibleScripts = [
            base_path('python_outlook_web/sync_emails_optimized.py'),
            base_path('python_outlook_web\\sync_emails_optimized.py'),
            base_path('python_outlook_web/sync_emails.py'),
            base_path('python_outlook_web\\sync_emails.py'),
        ];
        
        foreach ($possibleScripts as $script) {
            if (file_exists($script)) {
                Log::info("Using sync script: {$script}");
                return $script;
            }
        }
        
        // Fallback to config
        $configScript = config('mail_sync.python_script_dir', base_path('python_outlook_web')) . '/sync_emails_optimized.py';
        Log::warning("No sync script found, using config fallback: {$configScript}");
        return $configScript;
    }

    /**
     * Handle bulk actions on emails
     */
    public function bulkAction(Request $request)
    {  
        // Check authentication first
        if (!$request->user()) {
            Log::error('Bulk action attempted without authentication', [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_data' => $request->all(),
                'timestamp' => now()->toISOString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.'
            ], 401);
        }

        // Log the incoming request
        Log::info('Bulk action request received', [
            'user_id' => $request->user()->id,
            'request_data' => $request->all(),
            'email_ids_raw' => $request->input('email_ids'),
            'email_ids_type' => gettype($request->input('email_ids')),
            'email_ids_count' => is_array($request->input('email_ids')) ? count($request->input('email_ids')) : 'not_array',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString()
        ]);

        try {
            // Pre-process email_ids to handle different formats
            $emailIdsInput = $request->input('email_ids', []);
            
            // Handle different input formats
            if (is_string($emailIdsInput)) {
                // If it's a JSON string, decode it
                $emailIdsInput = json_decode($emailIdsInput, true) ?? [];
            }
            
            // Ensure it's an array
            if (!is_array($emailIdsInput)) {
                $emailIdsInput = [$emailIdsInput];
            }
            
            // Filter out empty values and convert to integers
            $emailIdsInput = array_filter(array_map('intval', $emailIdsInput), function($id) {
                return $id > 0;
            });
            
            // Replace the input with processed data
            $request->merge(['email_ids' => $emailIdsInput]);
            
            Log::info('Email IDs pre-processed', [
                'user_id' => $request->user()->id,
                'original_email_ids' => $request->input('email_ids'),
                'processed_email_ids' => $emailIdsInput,
                'processed_count' => count($emailIdsInput),
                'database_connection' => config('database.default'),
                'database_config' => config('database.connections.' . config('database.default')),
                'timestamp' => now()->toISOString()
            ]);
            
            // Custom validation for email_ids with ownership check
            $validator = Validator::make($request->all(), [
                'action' => ['required', 'string', 'in:mark_read,mark_unread,flag,unflag,delete'],
                'email_ids' => ['required', 'array', 'min:1'],
                'email_ids.*' => ['required', 'integer', 'min:1']
            ]);

            // Add custom validation for email ownership
            $validator->after(function ($validator) use ($request, $emailIdsInput) {
                if (empty($emailIdsInput)) {
                    $validator->errors()->add('email_ids', 'No valid email IDs provided.');
                    return;
                }

                // Check if emails exist and belong to the user
                try {
                    // First, test basic database connectivity
                    $totalEmails = Email::count();
                    Log::info('Database connectivity test', [
                        'user_id' => $request->user()->id,
                        'total_emails_in_db' => $totalEmails,
                        'database_connection' => config('database.default')
                    ]);

                    $existingEmails = Email::whereIn('id', $emailIdsInput)
                        ->whereHas('emailAccount', function ($query) use ($request) {
                            $query->where('user_id', $request->user()->id);
                        })
                        ->pluck('id')
                        ->toArray();

                    Log::info('Email ownership query result', [
                        'user_id' => $request->user()->id,
                        'requested_email_ids' => $emailIdsInput,
                        'found_email_ids' => $existingEmails,
                        'query_successful' => true
                    ]);
                } catch (\Exception $e) {
                    Log::error('Database query failed during email validation', [
                        'user_id' => $request->user()->id,
                        'email_ids' => $emailIdsInput,
                        'error' => $e->getMessage(),
                        'database_connection' => config('database.default'),
                        'timestamp' => now()->toISOString()
                    ]);
                    
                    // Fallback: try with explicit database connection
                    try {
                        $existingEmails = Email::on('mysql')->whereIn('id', $emailIdsInput)
                            ->whereHas('emailAccount', function ($query) use ($request) {
                                $query->where('user_id', $request->user()->id);
                            })
                            ->pluck('id')
                            ->toArray();
                    } catch (\Exception $e2) {
                        Log::error('Fallback database query also failed', [
                            'user_id' => $request->user()->id,
                            'email_ids' => $emailIdsInput,
                            'error' => $e2->getMessage(),
                            'timestamp' => now()->toISOString()
                        ]);
                        $existingEmails = [];
                    }
                }

                $missingEmails = array_diff($emailIdsInput, $existingEmails);
                
                if (!empty($missingEmails)) {
                    Log::warning('Bulk action validation - some emails not found or access denied', [
                        'user_id' => $request->user()->id,
                        'requested_email_ids' => $emailIdsInput,
                        'found_email_ids' => $existingEmails,
                        'missing_email_ids' => $missingEmails
                    ]);

                    foreach ($missingEmails as $missingId) {
                        $validator->errors()->add('email_ids', "Email ID {$missingId} not found or access denied.");
                    }
                }
            });

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            $validated = $validator->validated();

            Log::info('Bulk action validation passed', [
                'user_id' => $request->user()->id,
                'action' => $validated['action'],
                'email_count' => count($validated['email_ids']),
                'email_ids' => $validated['email_ids']
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = [];
            
            // Extract specific error messages
            foreach ($errors as $field => $messages) {
                foreach ($messages as $message) {
                    $errorMessages[] = $field . ': ' . $message;
                }
            }

            Log::error('Bulk action validation failed', [
                'user_id' => $request->user() ? $request->user()->id : 'not_authenticated',
                'validation_errors' => $errors,
                'request_data' => $request->all(),
                'email_ids_raw' => $request->input('email_ids', []),
                'email_ids_type' => gettype($request->input('email_ids')),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode('; ', $errorMessages),
                'errors' => $errors,
                'debug_info' => [
                    'email_ids_received' => $request->input('email_ids', []),
                    'email_ids_type' => gettype($request->input('email_ids')),
                    'email_ids_count' => is_array($request->input('email_ids')) ? count($request->input('email_ids')) : 'not_array'
                ]
            ], 422);
        } catch (\Exception $e) {
            Log::error('Bulk action validation exception', [
                'user_id' => $request->user() ? $request->user()->id : 'not_authenticated',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ], 422);
        }

        // Sanitize and convert email IDs to integers
        $emailIds = array_map('intval', $validated['email_ids']);
        $action = $validated['action'];

        // Additional validation to ensure all IDs are positive integers
        $emailIds = array_filter($emailIds, function($id) {
            return $id > 0;
        });

        if (empty($emailIds)) {
            Log::error('Bulk action failed - no valid email IDs after sanitization', [
                'user_id' => $request->user()->id,
                'original_email_ids' => $validated['email_ids'],
                'sanitized_email_ids' => $emailIds
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No valid email IDs provided.'
            ], 422);
        }

        Log::info('Starting bulk action processing', [
            'user_id' => $request->user()->id,
            'action' => $action,
            'email_ids' => $emailIds,
            'email_count' => count($emailIds)
        ]);

        try {
            // Use helper method to validate email ownership
            $emails = $this->validateEmailOwnership($emailIds, $request->user()->id);
            
            if ($emails === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some emails not found or access denied.',
                    'requested_count' => count($emailIds)
                ], 403);
            }

            $this->logBulkActionDebug('Email ownership validation passed', [
                'user_id' => $request->user()->id,
                'email_count' => $emails->count(),
                'email_ids' => $emailIds
            ]);

            // Log before performing the action
            Log::info('Performing bulk action', [
                'user_id' => $request->user()->id,
                'action' => $action,
                'email_count' => $emails->count(),
                'email_ids' => $emailIds
            ]);

            $affectedCount = 0;
            $startTime = microtime(true);

            switch ($action) {
                case 'mark_read':
                    $this->logBulkActionDebug('Starting mark_read action', ['email_ids' => $emailIds]);
                    $affectedCount = Email::whereIn('id', $emailIds)->update(['is_read' => true, 'updated_at' => now()]);
                    Log::info('Bulk action completed - mark_read', [
                        'user_id' => $request->user()->id,
                        'affected_count' => $affectedCount,
                        'email_ids' => $emailIds
                    ]);
                    break;

                case 'mark_unread':
                    $this->logBulkActionDebug('Starting mark_unread action', ['email_ids' => $emailIds]);
                    $affectedCount = Email::whereIn('id', $emailIds)->update(['is_read' => false, 'updated_at' => now()]);
                    Log::info('Bulk action completed - mark_unread', [
                        'user_id' => $request->user()->id,
                        'affected_count' => $affectedCount,
                        'email_ids' => $emailIds
                    ]);
                    break;

                case 'flag':
                    $this->logBulkActionDebug('Starting flag action', ['email_ids' => $emailIds]);
                    $affectedCount = Email::whereIn('id', $emailIds)->update(['is_flagged' => true, 'updated_at' => now()]);
                    Log::info('Bulk action completed - flag', [
                        'user_id' => $request->user()->id,
                        'affected_count' => $affectedCount,
                        'email_ids' => $emailIds
                    ]);
                    break;

                case 'unflag':
                    $this->logBulkActionDebug('Starting unflag action', ['email_ids' => $emailIds]);
                    $affectedCount = Email::whereIn('id', $emailIds)->update(['is_flagged' => false, 'updated_at' => now()]);
                    Log::info('Bulk action completed - unflag', [
                        'user_id' => $request->user()->id,
                        'affected_count' => $affectedCount,
                        'email_ids' => $emailIds
                    ]);
                    break;

                case 'delete':
                    $this->logBulkActionDebug('Starting delete action', ['email_ids' => $emailIds]);
                    // For delete, we need to count before deletion
                    $affectedCount = $emails->count();
                    
                    // Log before deletion for safety
                    Log::warning('About to delete emails', [
                        'user_id' => $request->user()->id,
                        'email_count' => $affectedCount,
                        'email_ids' => $emailIds
                    ]);
                    
                    Email::whereIn('id', $emailIds)->delete();
                    Log::info('Bulk action completed - delete', [
                        'user_id' => $request->user()->id,
                        'affected_count' => $affectedCount,
                        'email_ids' => $emailIds
                    ]);
                    break;

                default:
                    Log::error('Unknown bulk action requested', [
                        'user_id' => $request->user()->id,
                        'action' => $action,
                        'email_ids' => $emailIds
                    ]);
                    throw new \InvalidArgumentException("Unknown action: {$action}");
            }

            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2); // Convert to milliseconds

            Log::info('Bulk action completed successfully', [
                'user_id' => $request->user()->id,
                'action' => $action,
                'affected_count' => $affectedCount,
                'execution_time_ms' => $executionTime,
                'email_ids' => $emailIds,
                'timestamp' => now()->toISOString()
            ]);

            // Get updated email data for flag/unflag actions to enable immediate frontend updates
            $updatedEmails = [];
            if (in_array($action, ['flag', 'unflag']) && $affectedCount > 0) {
                try {
                    $updatedEmailRecords = Email::on('second_db')
                        ->whereIn('id', $emailIds)
                        ->get(['id', 'from_email', 'to_email', 'subject', 'received_at', 'date', 'created_at', 'body', 'text_body', 'html_body', 'cc', 'reply_to', 'headers', 'is_read', 'is_flagged', 'assign_client_id', 'assign_client_matter_id', 'mail_report_tbl_id', 'folder']);
                    
                    foreach ($updatedEmailRecords as $email) {
                        // Get client and matter names if email is allocated
                        $clientName = null;
                        $matterName = null;
                        
                        if ($email->assign_client_id || $email->assign_client_matter_id) {
                            try {
                                // Get client name if client_id is set
                                if ($email->assign_client_id) {
                                    $client = \App\Models\Admin::where('id', $email->assign_client_id)
                                        ->where('role', 7)
                                        ->select('first_name', 'last_name', 'client_id')
                                        ->first();
                                    
                                    if ($client) {
                                        $clientName = trim($client->first_name . ' ' . $client->last_name);
                                        if ($client->client_id) {
                                            $clientName .= ' (' . $client->client_id . ')';
                                        }
                                    }
                                }
                                
                                // Get matter name if matter_id is set
                                if ($email->assign_client_matter_id) {
                                    $matter = \App\Models\ClientMatter::where('client_matters.id', $email->assign_client_matter_id)
                                        ->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                                        ->select('client_matters.client_unique_matter_no', 'matters.title', 'client_matters.sel_matter_id')
                                        ->first();
                                    
                                    if ($matter) {
                                        // Handle General Matter case (sel_matter_id = 1)
                                        if ($matter->sel_matter_id == 1 || empty($matter->title)) {
                                            $matterName = 'General Matter';
                                        } else {
                                            $matterName = $matter->title;
                                        }
                                        
                                        // Add client unique matter number if available
                                        if (!empty($matter->client_unique_matter_no)) {
                                            $matterName .= ' (' . $matter->client_unique_matter_no . ')';
                                        }
                                    }
                                }
                            } catch (\Exception $e) {
                                // Handle errors silently for individual emails
                            }
                        }
                        
                        $updatedEmails[] = [
                            'id' => $email->id,
                            'from' => $email->from_email,
                            'to' => $email->to_email,
                            'subject' => $email->subject,
                            'date' => $email->date ? $email->date->toISOString() : null,
                            'received_at' => $email->received_at ? $email->received_at->toISOString() : null,
                            'created_at' => $email->created_at ? $email->created_at->toISOString() : null,
                            'snippet' => $email->body ? substr(strip_tags($email->body), 0, 100) . '...' : 'No content',
                            'body' => $email->text_body ?? $email->body,
                            'html_body' => $email->html_body,
                            'cc' => $email->cc,
                            'reply_to' => $email->reply_to,
                            'headers' => $email->headers,
                            'folder' => $email->folder,
                            'has_attachment' => false,
                            'is_read' => $email->is_read ?? false,
                            'is_flagged' => $email->is_flagged ?? false,
                            'assign_client_id' => $email->assign_client_id,
                            'assign_client_matter_id' => $email->assign_client_matter_id,
                            'assign_client_name' => $clientName,
                            'assign_matter_name' => $matterName,
                            'attachments' => []
                        ];
                    }
                } catch (\Exception $e) {
                    // If fetching updated data fails, just continue with basic response
                    error_log("Failed to fetch updated email data after flag action: " . $e->getMessage());
                }
            }

            $response = [
                'success' => true,
                'message' => 'Bulk action completed successfully.',
                'affected_count' => $affectedCount,
                'action' => $action,
                'execution_time_ms' => $executionTime
            ];
            
            // Add updated emails for flag/unflag actions
            if (!empty($updatedEmails)) {
                $response['updated_emails'] = $updatedEmails;
            }
            
            return response()->json($response);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Bulk action database error', [
                'user_id' => $request->user()->id,
                'action' => $action,
                'email_ids' => $emailIds,
                'error' => $e->getMessage(),
                'sql_state' => method_exists($e, 'getSqlState') ? $e->getSqlState() : 'unknown',
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Database error during bulk action: ' . $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Bulk action general error', [
                'user_id' => $request->user()->id,
                'action' => $action,
                'email_ids' => $emailIds,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error performing bulk action: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug helper method to log bulk action details
     */
    private function logBulkActionDebug($message, $data = [])
    {
        Log::debug('Bulk Action Debug: ' . $message, array_merge([
            'timestamp' => now()->toISOString(),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true)
        ], $data));
    }

    /**
     * Validate email IDs and check ownership
     */
    private function validateEmailOwnership($emailIds, $userId)
    {
        // Check if email IDs are valid integers
        $validEmailIds = array_filter($emailIds, function($id) {
            return is_numeric($id) && (int)$id > 0;
        });

        if (count($validEmailIds) !== count($emailIds)) {
            Log::warning('Invalid email IDs provided', [
                'user_id' => $userId,
                'provided_ids' => $emailIds,
                'valid_ids' => $validEmailIds
            ]);
            return false;
        }

        // Check if emails exist and belong to user
        $emails = Email::whereIn('id', $validEmailIds)
            ->whereHas('emailAccount', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();

        if ($emails->count() !== count($validEmailIds)) {
            Log::warning('Email ownership validation failed', [
                'user_id' => $userId,
                'requested_count' => count($validEmailIds),
                'found_count' => $emails->count(),
                'found_email_ids' => $emails->pluck('id')->toArray(),
                'missing_email_ids' => array_diff($validEmailIds, $emails->pluck('id')->toArray())
            ]);
            return false;
        }

        return $emails;
    }

    /**
     * Debug method to test bulk action logging and validation
     */
    public function debugBulkAction(Request $request)
    {
        Log::info('Debug bulk action method called', [
            'user_id' => $request->user() ? $request->user()->id : 'not_authenticated',
            'request_data' => $request->all(),
            'timestamp' => now()->toISOString()
        ]);

        // Test the helper methods
        $this->logBulkActionDebug('Testing debug logging', [
            'test_data' => 'This is a test message',
            'user_id' => $request->user() ? $request->user()->id : 'not_authenticated'
        ]);

        // Test email ID validation
        $testEmailIds = [1, 2, 3]; // Example IDs
        $emails = $this->validateEmailOwnership($testEmailIds, $request->user() ? $request->user()->id : 1);
        
        Log::info('Debug email validation test', [
            'test_email_ids' => $testEmailIds,
            'validation_result' => $emails !== false ? 'passed' : 'failed',
            'user_id' => $request->user() ? $request->user()->id : 'not_authenticated'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Debug logging test completed. Check Laravel logs for details.',
            'user_id' => $request->user() ? $request->user()->id : 'not_authenticated',
            'test_validation' => $emails !== false ? 'passed' : 'failed',
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Save email as draft
     */
    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'account_id' => ['nullable', 'integer', 'exists:email_accounts,id'],
            'to' => ['nullable', 'email'],
            'cc' => ['nullable', 'string'],
            'bcc' => ['nullable', 'string'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
        ]);

        try {
            $userId = Auth::guard('email_users')->id();
            
            // Store draft in database
            $draft = EmailDraft::create([
                'user_id' => $userId,
                'account_id' => $validated['account_id'] ?? null,
                'to_email' => $validated['to'] ?? null,
                'cc_email' => $validated['cc'] ?? null,
                'bcc_email' => $validated['bcc'] ?? null,
                'subject' => $validated['subject'] ?? null,
                'message' => $validated['body'] ?? null,
                'attachments' => $validated['attachments'] ?? [],
            ]);

            Log::info('Email draft saved', [
                'draft_id' => $draft->id,
                'user_id' => $userId,
                'subject' => $validated['subject'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Draft saved successfully!',
                'draft_id' => $draft->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save draft', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's email drafts
     */
    public function drafts()
    {
        $userId = Auth::guard('email_users')->id();
        
        $drafts = EmailDraft::forUser($userId)
            ->with('emailAccount')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'drafts' => $drafts->items(),
            'pagination' => [
                'current_page' => $drafts->currentPage(),
                'last_page' => $drafts->lastPage(),
                'per_page' => $drafts->perPage(),
                'total' => $drafts->total(),
            ]
        ]);
    }

    /**
     * Get draft data for editing
     */
    public function getDraft(int $id)
    {
        $userId = Auth::guard('email_users')->id();
        $draft = EmailDraft::where('user_id', $userId)->findOrFail($id);

        return response()->json([
            'success' => true,
            'draft' => $draft
        ]);
    }

    /**
     * Delete a draft
     */
    public function deleteDraft(int $id)
    {
        $userId = Auth::guard('email_users')->id();
        $draft = EmailDraft::where('user_id', $userId)->findOrFail($id);
        $draft->delete();

        return response()->json([
            'success' => true,
            'message' => 'Draft deleted successfully'
        ]);
    }

    /**
     * Get reply data for an email
     */
    public function getReplyData(int $id)
    {
        $userId = Auth::guard('email_users')->id();
        $email = Email::where('user_id', $userId)->findOrFail($id);

        return response()->json([
            'success' => true,
            'subject' => 'Re: ' . ($email->subject ?: '(No subject)'),
            'to_email' => $email->from_email,
            'sender_name' => $email->sender_name,
            'original_message' => $email->text_body ?: $email->body,
            'original_date' => $email->received_at ? $email->received_at->format('Y-m-d H:i') : '',
        ]);
    }

    /**
     * Store sent email in database for tracking
     */
    private function storeSentEmail(Request $request, int $userId, int $accountId)
    {
        try {
            // Create a mail message record
            $email = Email::create([
                'user_id' => $userId,
                'account_id' => $accountId,
                'subject' => $request->input('subject'),
                'from_email' => $request->input('from_email'),
                'sender_email' => $request->input('from_email'),
                'sender_name' => Auth::user()->name,
                'to_email' => $request->input('to'),
                'recipients' => json_encode([$request->input('to')]),
                'text_body' => $request->input('body'),
                'html_body' => nl2br($request->input('body')),
                'sent_date' => now(),
                'status' => 'sent',
                'folder' => 'sent',
            ]);

            // Store attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $path = $attachment->store('email-attachments', 'public');
                    
                    $email->attachments()->create([
                        'filename' => $attachment->getClientOriginalName(),
                        'display_name' => $attachment->getClientOriginalName(),
                        'storage_path' => $path,
                        'content_type' => $attachment->getMimeType(),
                        'file_size' => $attachment->getSize(),
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::warning('Failed to store sent email', [
                'error' => $e->getMessage(),
                'mail_data' => $request->all(),
            ]);
        }
    }

    /**
     * Get email content from EML file stored in S3
     */
    public function getEmailContent(int $id)
    {
        $email = Email::on('second_db')->where('id', $id)
            ->whereHas('emailAccount', function ($query) {
                $query->where('user_id', Auth::guard('email_users')->id());
            })
            ->with('emailAccount')
            ->firstOrFail();

        // Skip EML file reading for now (EmailFolderService dependency removed)
        // Return email content directly from database

        // Return email content from database using available columns
        return response()->json([
            'success' => true,
            'content' => [
                'body' => $email->body ?? '',
                'subject' => $email->subject ?? '',
                'from_email' => $email->from_email ?? '',
                'date' => $email->date ? $email->date->format('Y-m-d H:i:s') : null,
                'received_at' => $email->received_at ? $email->received_at->format('Y-m-d H:i:s') : null,
                'folder' => $email->folder ?? 'Inbox'
            ],
            'source' => 'database'
        ], 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * Parse EML content to extract body and headers
     */
    private function parseEmlContent(string $emlContent): array
    {
        // Split headers and body
        $parts = explode("\r\n\r\n", $emlContent, 2);
        $headers = $parts[0] ?? '';
        $body = $parts[1] ?? '';

        // Parse headers
        $parsedHeaders = [];
        $headerLines = explode("\r\n", $headers);
        foreach ($headerLines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $parsedHeaders[trim($key)] = trim($value);
            }
        }

        // Check if body is multipart
        $contentType = $parsedHeaders['Content-Type'] ?? '';
        $isMultipart = strpos($contentType, 'multipart/') === 0;

        if ($isMultipart) {
            // Extract boundary
            preg_match('/boundary="([^"]+)"/', $contentType, $matches);
            $boundary = $matches[1] ?? '--boundary';
            
            // Parse multipart content
            $multipartParts = explode('--' . $boundary, $body);
            $textBody = '';
            $htmlBody = '';
            
            foreach ($multipartParts as $part) {
                if (strpos($part, 'Content-Type: text/plain') !== false) {
                    $textPart = explode("\r\n\r\n", $part, 2);
                    $textBody = trim($textPart[1] ?? '');
                } elseif (strpos($part, 'Content-Type: text/html') !== false) {
                    $htmlPart = explode("\r\n\r\n", $part, 2);
                    $htmlBody = trim($htmlPart[1] ?? '');
                }
            }
            
            return [
                'text_body' => $textBody,
                'html_body' => $htmlBody,
                'body' => $textBody ?: $htmlBody,
                'headers' => $parsedHeaders
            ];
        } else {
            // Single part content
            $isHtml = strpos($contentType, 'text/html') !== false;
            
            return [
                'text_body' => $isHtml ? '' : $body,
                'html_body' => $isHtml ? $body : '',
                'body' => $body,
                'headers' => $parsedHeaders
            ];
        }
    }

    /**
     * Build email content in RFC 2822 format for local storage
     */
    private function buildEmailContent(array $emailData): string
    {
        $headers = [];
        
        // Basic headers
        if (!empty($emailData['message_id'])) {
            $headers[] = 'Message-ID: ' . $emailData['message_id'];
        }
        
        if (!empty($emailData['from'])) {
            $fromName = !empty($emailData['from_name']) ? $emailData['from_name'] : '';
            $fromEmail = $emailData['from'];
            if ($fromName) {
                $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
            } else {
                $headers[] = 'From: ' . $fromEmail;
            }
        }
        
        if (!empty($emailData['to'])) {
            $headers[] = 'To: ' . $emailData['to'];
        }
        
        if (!empty($emailData['cc'])) {
            $headers[] = 'Cc: ' . $emailData['cc'];
        }
        
        if (!empty($emailData['reply_to'])) {
            $headers[] = 'Reply-To: ' . $emailData['reply_to'];
        }
        
        if (!empty($emailData['subject'])) {
            $headers[] = 'Subject: ' . $emailData['subject'];
        }
        
        if (!empty($emailData['date'])) {
            $headers[] = 'Date: ' . $emailData['date'];
        } elseif (!empty($emailData['parsed_date'])) {
            $headers[] = 'Date: ' . $emailData['parsed_date'];
        }
        
        // Add custom headers if available
        if (!empty($emailData['headers']) && is_array($emailData['headers'])) {
            foreach ($emailData['headers'] as $key => $value) {
                if (!in_array(strtolower($key), ['message-id', 'from', 'to', 'cc', 'reply-to', 'subject', 'date'])) {
                    $headers[] = $key . ': ' . $value;
                }
            }
        }
        
        // Content-Type header
        $hasHtml = !empty($emailData['html_body']);
        $hasText = !empty($emailData['text_body']) || !empty($emailData['body']);
        
        if ($hasHtml && $hasText) {
            $boundary = 'boundary_' . uniqid();
            $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
        } elseif ($hasHtml) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
        } else {
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        }
        
        // Build the email content
        $content = implode("\r\n", $headers) . "\r\n\r\n";
        
        if ($hasHtml && $hasText) {
            // Multipart email
            $content .= "--" . $boundary . "\r\n";
            $content .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
            $content .= ($emailData['text_body'] ?? $emailData['body'] ?? '') . "\r\n\r\n";
            
            $content .= "--" . $boundary . "\r\n";
            $content .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
            $content .= ($emailData['html_body'] ?? '') . "\r\n\r\n";
            
            $content .= "--" . $boundary . "--\r\n";
        } elseif ($hasHtml) {
            $content .= $emailData['html_body'];
        } else {
            $content .= ($emailData['text_body'] ?? $emailData['body'] ?? '');
        }
        
        return $content;
    }

    /**
     * Get the appropriate Python executable path based on operating system with enhanced Linux support
     */
    private function getPythonExecutablePath()
    {
        return $this->getPythonExecutablePathWithFallback();
    }

    /**
     * Auto-detect Python executable in the script directory
     */
    private function autoDetectPythonExecutable($scriptDir)
    {
        // Common Python executable paths to check
        $possiblePaths = [
            // Linux/Unix paths
            $scriptDir . '/venv/bin/python',
            $scriptDir . '/venv/bin/python3',
            $scriptDir . '/venv/bin/python3.8',
            $scriptDir . '/venv/bin/python3.9',
            $scriptDir . '/venv/bin/python3.10',
            $scriptDir . '/venv/bin/python3.11',
            $scriptDir . '/venv/bin/python3.12',
            // Windows paths
            $scriptDir . '/venv/Scripts/python.exe',
            $scriptDir . '/venv/Scripts/python3.exe',
            // System Python paths
            '/usr/bin/python3',
            '/usr/local/bin/python3',
            '/usr/bin/python',
            '/usr/local/bin/python',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                Log::info("Auto-detected Python executable", ['path' => $path]);
                return $path;
            }
        }

        return null;
    }

    /**
     * Get the appropriate sync script path with robust connection handling
     */
    private function getSyncScriptPath()
    {
        // Get script directory from config
        $scriptDir = config('mail_sync.python_script_dir');
        
        // Use robust script for reliable connection handling
        $scriptPath = $scriptDir . '/sync_emails_robust.py';
        
        // Fallback to optimized script if robust doesn't exist
        if (!file_exists($scriptPath)) {
            $scriptPath = $scriptDir . '/sync_emails_optimized.py';
        }
        
        // Fallback to simple script
        if (!file_exists($scriptPath)) {
            $scriptPath = $scriptDir . '/sync_emails_simple.py';
        }
        
        // Final fallback to basic script
        if (!file_exists($scriptPath)) {
            $scriptPath = $scriptDir . '/sync_emails.py';
        }

        if (!file_exists($scriptPath)) {
            throw new \Exception("No sync script found in: {$scriptDir}. Please ensure Python scripts are properly installed.");
        }

        Log::info("Using sync script", ['path' => $scriptPath]);
        return $scriptPath;
    }

    /**
     * Get the send mail script path
     */
    private function getSendMailScriptPath()
    {
        // Get script directory from config
        $scriptDir = config('mail_sync.python_script_dir');
        
        $scriptPath = $scriptDir . '/send_mail.py';

        if (!file_exists($scriptPath)) {
            throw new \Exception("Send mail script not found at: {$scriptPath}. Please ensure Python scripts are properly installed.");
        }

        return $scriptPath;
    }

    /**
     * Get the appropriate Python runner script for the operating system
     */
    private function getPythonRunnerScript()
    {
        // Check if custom runner script is configured
        $customRunner = config('mail_sync.python_runner_script') ?? env('MAIL_PYTHON_RUNNER_SCRIPT');
        if ($customRunner && file_exists($customRunner)) {
            return $customRunner;
        }

        // Get script directory from config
        $scriptDir = config('mail_sync.python_script_dir');

        // Default OS-based detection
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows batch file
            $runnerPath = $scriptDir . '/' . config('mail_sync.windows.python_runner_script', 'run_python.bat');
        } else {
            // Unix-like systems (Linux, macOS, etc.)
            $runnerPath = $scriptDir . '/' . config('mail_sync.linux.python_runner_script', 'run_python.sh');
        }

        // Verify the path exists
        if (!file_exists($runnerPath)) {
            throw new \Exception("Python runner script not found at: {$runnerPath}. Please ensure the runner scripts are properly installed.");
        }

        return $runnerPath;
    }

    /**
     * Get the appropriate working directory for Python scripts
     */
    private function getPythonWorkingDirectory()
    {
        // Get script directory from config
        $scriptDir = config('mail_sync.python_script_dir');
        
        if (is_dir($scriptDir)) {
            return $scriptDir;
        }

        // Fallback to default directory
        return base_path('python_outlook_web');
    }

    /**
     * Get environment variables for Python processes
     */
    private function getPythonEnvironmentVariables()
    {
        $defaultEnv = [
            'PYTHONIOENCODING' => 'utf-8',
            'LANG' => env('LANG', 'en_US.UTF-8'),
            'LC_ALL' => env('LC_ALL', 'en_US.UTF-8'),
        ];

        // Get custom environment variables from config
        $customEnv = config('mail_sync.python_env_vars', []);
        
        return array_merge($defaultEnv, $customEnv);
    }

    /**
     * Get clients for allocation dropdown
     */
    public function getClients()
    {
        try {
            $clients = \App\Models\Admin::where('role', 7)
                ->where('is_archived', 0)
                ->whereNull('is_deleted')
                ->select('id', 'first_name', 'last_name', 'client_id', 'email')
                ->orderBy('first_name')
                ->get();

            return response()->json([
                'success' => true,
                'clients' => $clients
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading clients: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get client matters for allocation dropdown
     */
    public function getClientMatters($clientId)
    {
        try {
            $matters = \App\Models\ClientMatter::where('client_id', $clientId)
                ->where('matter_status', 1)
                ->join('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                ->select('client_matters.id', 'client_matters.client_unique_matter_no', 'matters.title')
                ->orderBy('client_matters.id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'matters' => $matters
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading matters: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Allocate emails to client and matter
     * 
     * Database Usage:
     * - Second Database (second_db): emails table operations (read/update)
     * - Primary Database (mysql): mail_reports, admins, client_matters tables
     */
    public function allocateEmails(Request $request)
    {
        // Check authentication first
        if (!$request->user()) {
            Log::error('Email allocation attempted without authentication', [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_data' => $request->all(),
                'timestamp' => now()->toISOString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.'
            ], 401);
        }

        // Log the incoming request
        Log::info('Email allocation request received', [
            'user_id' => $request->user()->id,
            'request_data' => $request->all(),
            'email_ids_raw' => $request->input('email_ids'),
            'client_id_raw' => $request->input('client_id'),
            'matter_id_raw' => $request->input('matter_id'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString()
        ]);

        try {
            // Pre-process email_ids to handle different formats
            $emailIdsInput = $request->input('email_ids', []);
            
            // Handle different input formats
            if (is_string($emailIdsInput)) {
                $emailIdsInput = json_decode($emailIdsInput, true) ?? [];
            }
            
            // Ensure it's an array
            if (!is_array($emailIdsInput)) {
                $emailIdsInput = [$emailIdsInput];
            }
            
            // Filter out empty values and convert to integers
            $emailIdsInput = array_filter(array_map('intval', $emailIdsInput), function($id) {
                return $id > 0;
            });
            
            // Replace the input with processed data
            $request->merge(['email_ids' => $emailIdsInput]);

            // Custom validation for email allocation
            $validator = Validator::make($request->all(), [
                'email_ids' => ['required', 'array', 'min:1'],
                'email_ids.*' => ['required', 'integer', 'min:1'],
                'client_id' => ['required', 'integer', 'min:1'],
                'matter_id' => ['required', 'integer', 'min:1']
            ]);

            // Add custom validation for email ownership and client/matter existence
            $validator->after(function ($validator) use ($request, $emailIdsInput) {
                if (empty($emailIdsInput)) {
                    $validator->errors()->add('email_ids', 'No valid email IDs provided.');
                    return;
                }

                // Check if emails exist and belong to the user using second database
                try {
                    $existingEmails = Email::on('second_db')->whereIn('id', $emailIdsInput)
                        ->whereHas('emailAccount', function ($query) use ($request) {
                            $query->where('user_id', $request->user()->id);
                        })
                        ->get();

                    Log::info('Email ownership validation for allocation', [
                        'user_id' => $request->user()->id,
                        'requested_email_ids' => $emailIdsInput,
                        'found_emails_count' => $existingEmails->count(),
                        'found_email_ids' => $existingEmails->pluck('id')->toArray(),
                        'database_connection' => 'second_db'
                    ]);

                    if ($existingEmails->count() !== count($emailIdsInput)) {
                        $missingEmails = array_diff($emailIdsInput, $existingEmails->pluck('id')->toArray());
                        foreach ($missingEmails as $missingId) {
                            $validator->errors()->add('email_ids', "Email ID {$missingId} not found or access denied.");
                        }
                    }

                    // Check if client exists using primary database (admins table)
                    $clientExists = \App\Models\Admin::where('id', $request->input('client_id'))
                        ->where('role', 7)
                        ->exists();

                    if (!$clientExists) {
                        $validator->errors()->add('client_id', 'Client not found or invalid.');
                    }

                    // Check if matter exists using primary database (client_matters table)
                    $matterExists = \App\Models\ClientMatter::where('id', $request->input('matter_id'))
                        ->where('matter_status', 1)
                        ->exists();

                    if (!$matterExists) {
                        $validator->errors()->add('matter_id', 'Matter not found or invalid.');
                    }

                } catch (\Exception $e) {
                    Log::error('Database validation failed during email allocation', [
                        'user_id' => $request->user()->id,
                        'email_ids' => $emailIdsInput,
                        'client_id' => $request->input('client_id'),
                        'matter_id' => $request->input('matter_id'),
                        'error' => $e->getMessage(),
                        'database_connection' => 'second_db',
                        'timestamp' => now()->toISOString()
                    ]);
                    
                    $validator->errors()->add('email_ids', 'Database validation failed: ' . $e->getMessage());
                }
            });

            if ($validator->fails()) {
                $errors = $validator->errors();
                $errorMessages = [];
                
                foreach ($errors->all() as $message) {
                    $errorMessages[] = $message;
                }

                Log::error('Email allocation validation failed', [
                    'user_id' => $request->user()->id,
                    'validation_errors' => $errors->toArray(),
                    'request_data' => $request->all(),
                    'timestamp' => now()->toISOString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode('; ', $errorMessages),
                    'errors' => $errors->toArray()
                ], 422);
            }

            $validated = $validator->validated();

            Log::info('Email allocation validation passed', [
                'user_id' => $request->user()->id,
                'email_ids' => $validated['email_ids'],
                'client_id' => $validated['client_id'],
                'matter_id' => $validated['matter_id'],
                'timestamp' => now()->toISOString()
            ]);

            // Verify all emails belong to the authenticated user using second database
            $emails = Email::on('second_db')->whereIn('id', $validated['email_ids'])
                ->whereHas('emailAccount', function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                })
                ->get();

            Log::info('Email allocation - emails retrieved', [
                'user_id' => $request->user()->id,
                'requested_count' => count($validated['email_ids']),
                'found_count' => $emails->count(),
                'email_ids' => $emails->pluck('id')->toArray(),
                'database_connection' => 'second_db'
            ]);

            if ($emails->count() !== count($validated['email_ids'])) {
                Log::warning('Email allocation access denied - some emails not found', [
                    'user_id' => $request->user()->id,
                    'requested_count' => count($validated['email_ids']),
                    'found_count' => $emails->count(),
                    'missing_email_ids' => array_diff($validated['email_ids'], $emails->pluck('id')->toArray())
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Some emails not found or access denied.'
                ], 403);
            }

            $allocatedCount = 0;
            $errors = [];

            // Process each email individually to handle mail_report_tbl_id logic
            foreach ($emails as $email) {
                try {
                    Log::info('Processing email for allocation', [
                        'user_id' => $request->user()->id,
                        'email_id' => $email->id,
                        'email_subject' => $email->subject,
                        'current_mail_report_tbl_id' => $email->mail_report_tbl_id,
                        'client_id' => $validated['client_id'],
                        'matter_id' => $validated['matter_id']
                    ]);

                    // Check if mail_report_tbl_id is blank or not
                    if (empty($email->mail_report_tbl_id)) {
                        // Scenario 1: mail_report_tbl_id is blank - create new record
                        Log::info('Creating new mail report record', [
                            'user_id' => $request->user()->id,
                            'email_id' => $email->id,
                            'scenario' => 'new_record'
                        ]);
                        
                        // Determine mail_body_type based on folder
                        $mailBodyType = 'inbox'; // default
                        if (isset($email->folder)) {
                            $mailBodyType = strtolower($email->folder);
                        }

                        // Create new mail report record using second database
                        $mailReport = \App\Models\MailReport::create([
                            'conversion_type' => 'conversion_email_fetch',
                            'mail_body_type' => $mailBodyType,
                            'from_mail' => $email->from_email,
                            'to_mail' => $email->to_email,
                            'subject' => $email->subject,
                            'fetch_mail_sent_time' => $email->received_at ?? $email->date,
                            'client_id' => $validated['client_id'],
                            'client_matter_id' => $validated['matter_id'],
                            'user_id' => $request->user()->id,
                            'message' => $email->body ?? $email->text_body,
                            'type' => 'client',
                            'mail_type' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        Log::info('Mail report record created', [
                            'user_id' => $request->user()->id,
                            'email_id' => $email->id,
                            'mail_report_id' => $mailReport->id,
                            'database_connection' => 'primary'
                        ]);

                        // Update the email record with allocation data and new mail_report_tbl_id using second database
                        Email::on('second_db')->where('id', $email->id)->update([
                            'assign_client_id' => $validated['client_id'],
                            'assign_client_matter_id' => $validated['matter_id'],
                            'mail_report_tbl_id' => $mailReport->id,
                            'updated_at' => now()
                        ]);

                        Log::info('Email record updated with allocation', [
                            'user_id' => $request->user()->id,
                            'email_id' => $email->id,
                            'mail_report_id' => $mailReport->id,
                            'database_connection' => 'second_db'
                        ]);

                    } else {
                        // Scenario 2: mail_report_tbl_id is not blank - delete existing and create new
                        Log::info('Updating existing mail report record', [
                            'user_id' => $request->user()->id,
                            'email_id' => $email->id,
                            'existing_mail_report_id' => $email->mail_report_tbl_id,
                            'scenario' => 'update_record'
                        ]);
                        
                        // Delete existing mail report record using second database
                        \App\Models\MailReport::where('id', $email->mail_report_tbl_id)->delete();

                        // Determine mail_body_type based on folder
                        $mailBodyType = 'inbox'; // default
                        if (isset($email->folder)) {
                            $mailBodyType = strtolower($email->folder);
                        }

                        // Create new mail report record using second database
                        $mailReport = \App\Models\MailReport::create([
                            'conversion_type' => 'conversion_email_fetch',
                            'mail_body_type' => $mailBodyType,
                            'from_mail' => $email->from_email,
                            'to_mail' => $email->to_email,
                            'subject' => $email->subject,
                            'fetch_mail_sent_time' => $email->received_at ?? $email->date,
                            'client_id' => $validated['client_id'],
                            'client_matter_id' => $validated['matter_id'],
                            'user_id' => $request->user()->id,
                            'message' => $email->body ?? $email->text_body,
                            'type' => 'client',
                            'mail_type' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        Log::info('New mail report record created (replacing existing)', [
                            'user_id' => $request->user()->id,
                            'email_id' => $email->id,
                            'old_mail_report_id' => $email->mail_report_tbl_id,
                            'new_mail_report_id' => $mailReport->id,
                            'database_connection' => 'primary'
                        ]);

                        // Update the email record with new allocation data and new mail_report_tbl_id using second database
                        Email::on('second_db')->where('id', $email->id)->update([
                            'assign_client_id' => $validated['client_id'],
                            'assign_client_matter_id' => $validated['matter_id'],
                            'mail_report_tbl_id' => $mailReport->id,
                            'updated_at' => now()
                        ]);

                        Log::info('Email record updated with new allocation', [
                            'user_id' => $request->user()->id,
                            'email_id' => $email->id,
                            'mail_report_id' => $mailReport->id,
                            'database_connection' => 'second_db'
                        ]);
                    }

                    $allocatedCount++;

                } catch (\Exception $e) {
                    Log::error('Error processing individual email for allocation', [
                        'user_id' => $request->user()->id,
                        'email_id' => $email->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'timestamp' => now()->toISOString()
                    ]);
                    
                    $errors[] = "Error processing email ID {$email->id}: " . $e->getMessage();
                }
            }

            Log::info('Email allocation completed', [
                'user_id' => $request->user()->id,
                'total_emails' => $emails->count(),
                'allocated_count' => $allocatedCount,
                'error_count' => count($errors),
                'errors' => $errors,
                'timestamp' => now()->toISOString()
            ]);

            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some emails could not be allocated: ' . implode('; ', $errors),
                    'allocated_count' => $allocatedCount,
                    'error_count' => count($errors)
                ], 422);
            }

            // Get the updated email data with client/matter names
            $updatedEmails = [];
            try {
                $updatedEmailRecords = Email::on('second_db')
                    ->whereIn('id', $validated['email_ids'])
                    ->get(['id', 'from_email', 'to_email', 'subject', 'received_at', 'date', 'created_at', 'body', 'text_body', 'html_body', 'cc', 'reply_to', 'headers', 'is_read', 'is_flagged', 'assign_client_id', 'assign_client_matter_id', 'mail_report_tbl_id', 'folder']);
                
                foreach ($updatedEmailRecords as $email) {
                    $clientName = null;
                    $matterName = null;
                    
                    if ($email->assign_client_id || $email->assign_client_matter_id) {
                        try {
                            // Get client name if client_id is set
                            if ($email->assign_client_id) {
                                $client = \App\Models\Admin::where('id', $email->assign_client_id)
                                    ->where('role', 7)
                                    ->select('first_name', 'last_name', 'client_id')
                                    ->first();
                                
                                if ($client) {
                                    $clientName = trim($client->first_name . ' ' . $client->last_name);
                                    if ($client->client_id) {
                                        $clientName .= ' (' . $client->client_id . ')';
                                    }
                                }
                            }
                            
                            // Get matter name if matter_id is set
                            if ($email->assign_client_matter_id) {
                                $matter = \App\Models\ClientMatter::where('client_matters.id', $email->assign_client_matter_id)
                                    ->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                                    ->select('client_matters.client_unique_matter_no', 'matters.title', 'client_matters.sel_matter_id')
                                    ->first();
                                
                                if ($matter) {
                                    // Handle General Matter case (sel_matter_id = 1)
                                    if ($matter->sel_matter_id == 1 || empty($matter->title)) {
                                        $matterName = 'General Matter';
                                    } else {
                                        $matterName = $matter->title;
                                    }
                                    
                                    // Add client unique matter number if available
                                    if (!empty($matter->client_unique_matter_no)) {
                                        $matterName .= ' (' . $matter->client_unique_matter_no . ')';
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Handle errors silently for individual emails
                        }
                    }
                    
                    $updatedEmails[] = [
                        'id' => $email->id,
                        'from' => $email->from_email,
                        'to' => $email->to_email,
                        'subject' => $email->subject,
                        'date' => $email->date ? $email->date->toISOString() : null,
                        'received_at' => $email->received_at ? $email->received_at->toISOString() : null,
                        'created_at' => $email->created_at ? $email->created_at->toISOString() : null,
                        'snippet' => $email->body ? substr(strip_tags($email->body), 0, 100) . '...' : 'No content',
                        'body' => $email->text_body ?? $email->body,
                        'html_body' => $email->html_body,
                        'cc' => $email->cc,
                        'reply_to' => $email->reply_to,
                        'headers' => $email->headers,
                        'folder' => $email->folder,
                        'has_attachment' => false,
                        'is_read' => $email->is_read ?? false,
                        'is_flagged' => $email->is_flagged ?? false,
                        'assign_client_id' => $email->assign_client_id,
                        'assign_client_matter_id' => $email->assign_client_matter_id,
                        'assign_client_name' => $clientName,
                        'assign_matter_name' => $matterName,
                        'attachments' => []
                    ];
                }
            } catch (\Exception $e) {
                // If fetching updated data fails, just return basic success
                error_log("Failed to fetch updated email data after allocation: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Emails allocated successfully.',
                'affected_count' => $allocatedCount,
                'updated_emails' => $updatedEmails
            ]);

        } catch (\Exception $e) {
            Log::error('Email allocation general error', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error allocating emails: ' . $e->getMessage()
            ], 500);
        }
    }

}



