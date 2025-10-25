<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Signer;
use App\Services\PythonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use setasign\Fpdi\TcpdfFpdi;
use Smalot\PdfParser\Parser;

/**
 * Public Document Controller
 * 
 * Handles public-facing document signing operations without authentication.
 * Access is controlled through unique tokens sent via email.
 */
class PublicDocumentController extends Controller
{
    /**
     * No authentication required - using token-based validation
     */
    public function __construct()
    {
        // Public controller - no authentication middleware
    }

    /**
     * Show the public signing form for a document using a tokenized link.
     * 
     * @param int $id Document ID
     * @param string $token Unique signer token
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function sign($id, $token)
    {
        // Sanitize and validate inputs
        $documentId = (int) $id;
        if ($documentId <= 0) {
            Log::warning('Invalid document ID in public sign method', ['id' => $id]);
            // ✅ FIX #4: Don't redirect public users to admin login
            abort(404, 'Invalid document link.');
        }

        // Validate token format
        if (!$token || !is_string($token) || strlen($token) < 32 || !preg_match('/^[a-zA-Z0-9]+$/', $token)) {
            Log::warning('Invalid token format in public sign method', ['token_length' => strlen($token ?? '')]);
            // ✅ FIX #4: Don't redirect public users to admin login
            abort(403, 'Invalid or expired signing link.');
        }

        try {
            $document = Document::findOrFail($documentId);
            
            // Handle agreement type documents specially
            if (isset($document->doc_type) && $document->doc_type == 'agreement') {
                $signer = $document->signers()->where('document_id', $documentId)->first();
                if ($signer) {
                    $signer->update(['token' => $token, 'status' => 'pending']);
                    $signer = $document->signers()->where('token', $token)->first();
                } else {
                    $signer = $document->signers()->create([
                        'token' => $token,
                        'status' => 'pending'
                    ]);
                }
            } else {
                $signer = $document->signers()->where('token', $token)->first();
            }

            if (!$signer || $signer->status === 'signed') {
                Log::warning('Invalid signer or already signed', [
                    'document_id' => $documentId,
                    'signer_exists' => !is_null($signer),
                    'signer_status' => $signer ? $signer->status : 'none'
                ]);
                // ✅ FIX #4: If already signed, go to thank you page
                if ($signer && $signer->status === 'signed') {
                    return redirect()->route('public.documents.thankyou', ['id' => $document->id])
                        ->with('info', 'This document has already been signed.');
                }
                abort(403, 'Invalid or expired signing link.');
            }

            // Track when document was opened
            if (!$signer->opened_at) {
                $signer->update(['opened_at' => now()]);
            }

            $signatureFields = $document->signatureFields()->get();
            
            // Get PDF path - handle both S3 and local files
            $url = $document->myfile;
            $pdfPath = null;
            $tmpPdfPath = null;
            $isLocalFile = false;
            
            // Check if URL is a full S3 URL or local path
            if ($url && filter_var($url, FILTER_VALIDATE_URL) && strpos($url, 's3') !== false) {
                // This is an S3 URL - extract the key
                $parsed = parse_url($url);
                if (isset($parsed['path'])) {
                    $pdfPath = ltrim(urldecode($parsed['path']), '/');
                }
                
                if ($pdfPath && Storage::disk('s3')->exists($pdfPath)) {
                    // Download PDF from S3 to a temp file
                    $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
                    $pdfStream = Storage::disk('s3')->get($pdfPath);
                    file_put_contents($tmpPdfPath, $pdfStream);
                }
            } elseif ($url && file_exists(storage_path('app/public/' . $url))) {
                // This is a local file path and file exists
                $tmpPdfPath = storage_path('app/public/' . $url);
                $isLocalFile = true;
                Log::info('Using local file for document signing page', ['path' => $tmpPdfPath]);
            } else {
                // Try to build S3 key from DB fields as fallback
                if (!empty($document->myfile_key) && !empty($document->doc_type) && !empty($document->client_id)) {
                    $admin = \DB::table('admins')->select('client_id')->where('id', $document->client_id)->first();
                    if ($admin && $admin->client_id) {
                        $pdfPath = $admin->client_id . '/' . $document->doc_type . '/' . $document->myfile_key;
                        
                        if (Storage::disk('s3')->exists($pdfPath)) {
                            // Download PDF from S3 to a temp file
                            $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
                            $pdfStream = Storage::disk('s3')->get($pdfPath);
                            file_put_contents($tmpPdfPath, $pdfStream);
                        }
                    }
                }
            }

            // Count PDF pages
            $pdfPages = 1;
            if ($tmpPdfPath && file_exists($tmpPdfPath)) {
                $pdfPages = $this->countPdfPages($tmpPdfPath) ?: 1;
                // Only delete temp files, not local files
                if (!$isLocalFile && strpos($tmpPdfPath, 'tmp_') !== false) {
                    @unlink($tmpPdfPath);
                }
            }

            return view('documents.sign', compact('document', 'signer', 'signatureFields', 'pdfPages'));
        } catch (\Exception $e) {
            Log::error('Error in public sign method', [
                'error' => $e->getMessage(),
                'document_id' => $documentId,
                'token_present' => !empty($token)
            ]);
            return redirect('/')->with('error', 'An error occurred while loading the signing page.');
        }
    }

    /**
     * Submit signatures for a public document
     * 
     * @param Request $request
     * @param int $id Document ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitSignatures(Request $request, $id)
    {
        Log::info('=== START submitSignatures ===', [
            'document_id' => $id,
            'signer_id' => $request->signer_id,
            'has_signatures' => $request->has('signatures'),
            'has_positions' => $request->has('signature_positions')
        ]);
        
        // Validation
        $request->validate([
            'signer_id' => 'required|integer|exists:signers,id',
            'token' => 'required|string|min:32',
            'signatures' => 'required|array',
            'signatures.*' => 'nullable|string',
            'signature_positions' => 'required|array',
            'signature_positions.*' => 'nullable|string'
        ]);

        $documentId = (int) $id;
        if ($documentId <= 0) {
            Log::error('Invalid document ID in submitSignatures', ['id' => $id]);
            return redirect('/')->with('error', 'Invalid document ID.');
        }

        try {
            $document = Document::findOrFail($documentId);
            $signer = Signer::findOrFail($request->signer_id);
            
            Log::info('Document and signer loaded', [
                'document_id' => $document->id,
                'signer_id' => $signer->id,
                'signer_status' => $signer->status,
                'signer_token_set' => $signer->token !== null
            ]);

            // Verify signer belongs to this document
            if ($signer->document_id !== $document->id) {
                Log::warning('Signer does not belong to document', [
                    'signer_id' => $signer->id,
                    'document_id' => $document->id
                ]);
                return redirect('/')->with('error', 'Invalid signing attempt.');
            }

            // Verify token matches
            if ($signer->token !== $request->token) {
                Log::warning('Token mismatch for signer', [
                    'signer_id' => $signer->id,
                    'document_id' => $document->id,
                    'provided_token' => substr($request->token, 0, 8) . '...',
                    'expected_token' => substr($signer->token, 0, 8) . '...'
                ]);
                return redirect('/')->with('error', 'Invalid or expired signing link.');
            }

            // Check if signer has already signed this document
            if ($signer->status === 'signed') {
                Log::warning('Signer attempting to re-sign already signed document', [
                    'signer_id' => $signer->id,
                    'document_id' => $document->id
                ]);
                return redirect()->route('public.documents.thankyou', ['id' => $document->id])
                    ->with('info', 'This document has already been signed. You can download the signed copy below.');
            }
            
            if ($signer->token !== null && $signer->status === 'pending') {
                // Get PDF file using multiple fallback methods (like CRM)
                $url = $document->myfile;
                $tmpPdfPath = null;
                $isLocalFile = false;
                $pdfPath = null;
                
                // Fallback 1: Check if URL is a full S3 URL
                if ($url && filter_var($url, FILTER_VALIDATE_URL) && strpos($url, 's3') !== false) {
                    $parsed = parse_url($url);
                    if (isset($parsed['path'])) {
                        $pdfPath = ltrim(urldecode($parsed['path']), '/');
                    }
                    
                    if ($pdfPath && Storage::disk('s3')->exists($pdfPath)) {
                        $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
                        try {
                            $pdfStream = Storage::disk('s3')->get($pdfPath);
                            if (!$pdfStream || strlen($pdfStream) === 0) {
                                throw new \Exception('Empty PDF file downloaded from S3');
                            }
                            file_put_contents($tmpPdfPath, $pdfStream);
                            Log::info('Using S3 URL for document submission', ['url' => $url, 'path' => $pdfPath]);
                        } catch (\Exception $e) {
                            Log::error('Failed to download from S3 URL', ['url' => $url, 'error' => $e->getMessage()]);
                            $tmpPdfPath = null;
                        }
                    }
                }
                
                // Fallback 2: Check if file exists locally
                if (!$tmpPdfPath && $url && file_exists(storage_path('app/public/' . $url))) {
                    $tmpPdfPath = storage_path('app/public/' . $url);
                    $isLocalFile = true;
                    Log::info('Using local file for document submission', ['path' => $tmpPdfPath]);
                }
                
                // Fallback 3: Try media library (legacy support)
                if (!$tmpPdfPath) {
                    $mediaPath = $document->getFirstMediaPath('documents');
                    if ($mediaPath && file_exists($mediaPath)) {
                        $tmpPdfPath = $mediaPath;
                        $isLocalFile = true;
                        Log::info('Using media library for document submission', ['path' => $tmpPdfPath]);
                    }
                }
                
                // Fallback 4: Try to build S3 key from DB fields
                if (!$tmpPdfPath) {
                    $clientId = null;
                    if ($document->client_id) {
                        $admin = \DB::table('admins')->select('client_id')->where('id', $document->client_id)->first();
                        if ($admin && $admin->client_id) {
                            $clientId = $admin->client_id;
                        }
                    }
                    
                    $docType = $document->doc_type ?? '';
                    $myfileKey = $document->myfile_key ?? '';
                    $s3Key = $clientId && $docType && $myfileKey ? ($clientId . '/' . $docType . '/' . $myfileKey) : null;
                    
                    if ($s3Key && Storage::disk('s3')->exists($s3Key)) {
                        $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
                        try {
                            $pdfStream = Storage::disk('s3')->get($s3Key);
                            if (!$pdfStream || strlen($pdfStream) === 0) {
                                throw new \Exception('Empty PDF file downloaded from S3');
                            }
                            file_put_contents($tmpPdfPath, $pdfStream);
                            Log::info('Using S3 key from DB fields for document submission', ['s3_key' => $s3Key]);
                        } catch (\Exception $e) {
                            Log::error('Failed to download from S3 using DB fields', ['s3_key' => $s3Key, 'error' => $e->getMessage()]);
                            $tmpPdfPath = null;
                        }
                    } else {
                        Log::error('S3 key not found or file does not exist', [
                            'document_id' => $document->id,
                            'client_id' => $clientId,
                            'doc_type' => $docType,
                            'myfile_key' => $myfileKey,
                            's3_key' => $s3Key
                        ]);
                    }
                }
                
                // Final check: If no file found, return error
                if (!$tmpPdfPath || !file_exists($tmpPdfPath) || filesize($tmpPdfPath) === 0) {
                    Log::error('PDF file not found for document submission', [
                        'document_id' => $document->id,
                        'url' => $url,
                        'tmp_pdf_path' => $tmpPdfPath,
                        'file_exists' => $tmpPdfPath ? file_exists($tmpPdfPath) : false
                    ]);
                    return redirect()->back()->with('error', 'Document file not found. Please contact support.');
                }
                
                $outputTmpPath = storage_path('app/tmp_' . uniqid() . '_signed.pdf');
                
                // Get client ID and doc type for S3 storage (if needed)
                $clientId = null;
                $docType = $document->doc_type ?? '';
                if ($document->client_id) {
                    $admin = \DB::table('admins')->select('client_id')->where('id', $document->client_id)->first();
                    if ($admin && $admin->client_id) {
                        $clientId = $admin->client_id;
                    }
                }

                // Process signatures
                $signaturePositions = [];
                $signatureLinks = [];
                $signaturesSaved = false;

                foreach ($request->signatures as $page => $signaturesJson) {
                    $pageNum = (int) $page;
                    if ($pageNum < 1 || $pageNum > 999 || !$signaturesJson) {
                        continue;
                    }

                    $signatures = json_decode($signaturesJson, true);
                    $positions = json_decode($request->signature_positions[$page] ?? '{}', true);

                    if (!is_array($signatures) || !is_array($positions)) {
                        continue;
                    }

                    foreach ($signatures as $fieldId => $signatureData) {
                        $sanitizedFieldId = (int) $fieldId;
                        if ($sanitizedFieldId <= 0) continue;

                        // Validate and decode signature
                        $sanitizedSignature = $this->sanitizeSignatureData($signatureData, $sanitizedFieldId);
                        if ($sanitizedSignature === false) continue;

                        $imageData = $sanitizedSignature['imageData'];

                        // Store signature (try S3 first, fallback to local storage)
                        $filename = sprintf('%d_field_%d_%s.png', $signer->id, $sanitizedFieldId, bin2hex(random_bytes(8)));
                        $signaturePath = null;
                        $signatureUrl = null;
                        
                        if ($clientId && $docType) {
                            // Upload to S3 if we have the necessary info
                            try {
                                $s3SignaturePath = $clientId . '/' . $docType . '/signatures/' . $filename;
                                Storage::disk('s3')->put($s3SignaturePath, $imageData);
                                $signaturePath = $s3SignaturePath;
                                $signatureUrl = Storage::disk('s3')->url($s3SignaturePath);
                                Log::info('Signature uploaded to S3', ['path' => $s3SignaturePath]);
                            } catch (\Exception $e) {
                                Log::warning('Failed to upload signature to S3, using local storage', ['error' => $e->getMessage()]);
                                $signaturePath = null;
                            }
                        }
                        
                        // Fallback to local storage
                        if (!$signaturePath) {
                            $localSignaturePath = 'signatures/' . $filename;
                            Storage::disk('public')->put($localSignaturePath, $imageData);
                            $signaturePath = storage_path('app/public/' . $localSignaturePath);
                            $signatureUrl = asset('storage/' . $localSignaturePath);
                            Log::info('Signature saved locally', ['path' => $signaturePath]);
                        }

                        // Store position
                        $position = $positions[$fieldId] ?? [];
                        $sanitizedPosition = $this->sanitizePositionData($position);

                        $signaturePositions[$sanitizedFieldId] = [
                            'path' => $signaturePath,
                            'page' => $pageNum,
                            'x_percent' => $sanitizedPosition['x_percent'],
                            'y_percent' => $sanitizedPosition['y_percent'],
                            'w_percent' => $sanitizedPosition['w_percent'],
                            'h_percent' => $sanitizedPosition['h_percent']
                        ];
                        $signatureLinks[$sanitizedFieldId] = $signatureUrl;
                        $signaturesSaved = true;
                    }
                }

                if (!$signaturesSaved) {
                    // ✅ FIX #4: Return to signing page with error
                    Log::error('No valid signatures saved', [
                        'document_id' => $document->id,
                        'signer_id' => $signer->id
                    ]);
                    return redirect()->back()
                        ->with('error', 'No valid signatures were detected. Please ensure all signature fields are properly signed.')
                        ->withInput();
                }

                // ✅ PYTHON SERVICE: Try Python service first (supports ALL PDF compressions)
                $pythonService = app(\App\Services\PythonService::class);
                $usedPythonService = false;
                
                if ($pythonService->isHealthy()) {
                    Log::info('Attempting to create signed PDF with Python service');
                    
                    // Prepare signatures in Python service format
                    $signaturesForPython = [];
                    foreach ($signaturePositions as $fieldId => $signatureInfo) {
                        // Get the signature file path (handle both S3 and local)
                        $signaturePath = $signatureInfo['path'];
                        $tmpSignaturePath = null;
                        
                        if (file_exists($signaturePath)) {
                            // Local file
                            $tmpSignaturePath = $signaturePath;
                        } else {
                            // Try S3
                            try {
                                $tmpSignaturePath = storage_path('app/tmp_signature_' . uniqid() . '.png');
                                $s3Image = Storage::disk('s3')->get($signaturePath);
                                file_put_contents($tmpSignaturePath, $s3Image);
                            } catch (\Exception $e) {
                                Log::warning('Failed to get signature file from S3', ['path' => $signaturePath, 'error' => $e->getMessage()]);
                                continue; // Skip this signature
                            }
                        }
                        
                        if ($tmpSignaturePath && file_exists($tmpSignaturePath)) {
                            // Read signature as base64
                            $signatureBase64 = base64_encode(file_get_contents($tmpSignaturePath));
                            
                            $signaturesForPython[] = [
                                'field_id' => $fieldId,
                                'page_number' => $signatureInfo['page'],
                                'x_percent' => $signatureInfo['x_percent'],
                                'y_percent' => $signatureInfo['y_percent'],
                                'width_percent' => $signatureInfo['w_percent'],
                                'height_percent' => $signatureInfo['h_percent'],
                                'signature_data' => $signatureBase64
                            ];
                            
                            // Clean up temp S3 file
                            if (strpos($tmpSignaturePath, 'tmp_signature_') !== false) {
                                @unlink($tmpSignaturePath);
                            }
                        }
                    }
                    
                    // Call Python service to add signatures
                    $pythonSuccess = $pythonService->addSignaturesToPdf(
                        $tmpPdfPath,
                        $outputTmpPath,
                        $signaturesForPython
                    );
                    
                    if ($pythonSuccess && file_exists($outputTmpPath) && filesize($outputTmpPath) > 0) {
                        Log::info('Signed PDF created successfully with Python service', [
                            'document_id' => $document->id,
                            'output_size' => filesize($outputTmpPath)
                        ]);
                        $usedPythonService = true;
                    } else {
                        Log::warning('Python service returned success but file invalid, falling back to FPDI', [
                            'file_exists' => file_exists($outputTmpPath),
                            'file_size' => file_exists($outputTmpPath) ? filesize($outputTmpPath) : 0
                        ]);
                    }
                } else {
                    Log::warning('Python PDF service not available, using FPDI fallback');
                }
                
                // ❌ FPDI FALLBACK: Use FPDI if Python service failed or unavailable
                if (!$usedPythonService) {
                    try {
                        Log::info('Creating signed PDF with FPDI');
                        
                        $pdf = new TcpdfFpdi('P', 'mm', 'A4', true, 'UTF-8', false);
                        $pdf->SetAutoPageBreak(false);
                        $pageCount = $pdf->setSourceFile($tmpPdfPath);

                        for ($page = 1; $page <= $pageCount; $page++) {
                            $tplIdx = $pdf->importPage($page);
                            $specs = $pdf->getTemplateSize($tplIdx);
                            $pdf->AddPage($specs['orientation'], [$specs['width'], $specs['height']]);
                            $pdf->useTemplate($tplIdx, 0, 0, $specs['width'], $specs['height']);

                            // Add signatures
                            $fields = $document->signatureFields()->where('page_number', $page)->get();
                            foreach ($fields as $field) {
                                if (isset($signaturePositions[$field->id])) {
                                    $signatureInfo = $signaturePositions[$field->id];
                                    $signaturePath = $signatureInfo['path'];
                                    
                                    $pdfWidth = $specs['width'];
                                    $pdfHeight = $specs['height'];
                                    $x_mm = $signatureInfo['x_percent'] * $pdfWidth;
                                    $y_mm = $signatureInfo['y_percent'] * $pdfHeight;
                                    $w_mm = max(15, $signatureInfo['w_percent'] * $pdfWidth);
                                    $h_mm = max(15, $signatureInfo['h_percent'] * $pdfHeight);

                                    // Get signature file (handle both S3 and local)
                                    $tmpSignaturePath = null;
                                    
                                    if (file_exists($signaturePath)) {
                                        // Local file
                                        $tmpSignaturePath = $signaturePath;
                                    } else {
                                        // Try S3
                                        try {
                                            $tmpSignaturePath = storage_path('app/tmp_signature_' . uniqid() . '.png');
                                            $s3Image = Storage::disk('s3')->get($signaturePath);
                                            file_put_contents($tmpSignaturePath, $s3Image);
                                        } catch (\Exception $e) {
                                            Log::warning('Failed to get signature file', ['path' => $signaturePath, 'error' => $e->getMessage()]);
                                            $tmpSignaturePath = null;
                                        }
                                    }

                                    if ($tmpSignaturePath && file_exists($tmpSignaturePath)) {
                                        $pdf->Image($tmpSignaturePath, $x_mm, $y_mm, $w_mm, $h_mm, 'PNG');
                                        // Only delete if it's a temp file (not the original local file)
                                        if (strpos($tmpSignaturePath, 'tmp_signature_') !== false) {
                                            @unlink($tmpSignaturePath);
                                        }
                                    }
                                }
                            }
                        }

                        // Save signed PDF
                        $pdf->Output($outputTmpPath, 'F');
                        
                        Log::info('Signed PDF created with FPDI fallback', [
                            'document_id' => $document->id,
                            'output_size' => file_exists($outputTmpPath) ? filesize($outputTmpPath) : 0
                        ]);
                        
                    } catch (\Exception $e) {
                        Log::error('FPDI also failed to create signed PDF', [
                            'document_id' => $document->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        
                        // If FPDI also fails, return error with helpful message
                        return redirect()->back()->with('error', 
                            'Unable to process this PDF document. The document may use an unsupported compression format. ' .
                            'Please contact support or try converting the PDF to a standard format.');
                    }
                }

                if (!file_exists($outputTmpPath) || filesize($outputTmpPath) === 0) {
                    Log::error('Failed to create signed PDF', [
                        'document_id' => $document->id,
                        'output_path' => $outputTmpPath,
                        'file_exists' => file_exists($outputTmpPath),
                        'file_size' => file_exists($outputTmpPath) ? filesize($outputTmpPath) : 0
                    ]);
                    return redirect()->back()->with('error', 'Failed to create the signed document. Please try again or contact support.');
                }

                // Generate SHA-256 hash for tamper detection (Phase 7)
                $signedHash = hash_file('sha256', $outputTmpPath);
                Log::info('Generated document hash', [
                    'document_id' => $document->id,
                    'hash' => $signedHash
                ]);

                // Upload signed PDF (try S3 first, fallback to local)
                $signedPdfUrl = null;
                $signedPdfPath = null;
                
                if ($clientId && $docType) {
                    try {
                        $s3SignedPath = $clientId . '/' . $docType . '/signed/' . $document->id . '_signed.pdf';
                        Storage::disk('s3')->put($s3SignedPath, fopen($outputTmpPath, 'r'));
                        $signedPdfUrl = Storage::disk('s3')->url($s3SignedPath);
                        $signedPdfPath = $s3SignedPath;
                        Log::info('Signed PDF uploaded to S3', ['path' => $s3SignedPath]);
                    } catch (\Exception $e) {
                        Log::warning('Failed to upload signed PDF to S3, using local storage', ['error' => $e->getMessage()]);
                        $signedPdfUrl = null;
                    }
                }
                
                // Fallback to local storage
                if (!$signedPdfUrl) {
                    $localSignedPath = 'signed/' . $document->id . '_signed.pdf';
                    Storage::disk('public')->put($localSignedPath, fopen($outputTmpPath, 'r'));
                    $signedPdfUrl = asset('storage/' . $localSignedPath);
                    $signedPdfPath = storage_path('app/public/' . $localSignedPath);
                    Log::info('Signed PDF saved locally', ['path' => $signedPdfPath]);
                }

                // Clean up temp files (only if they were temp files)
                if (!$isLocalFile || strpos($tmpPdfPath, 'tmp_') !== false) {
                    @unlink($tmpPdfPath);
                }
                @unlink($outputTmpPath);

                // Update statuses and save hash
                $signer->update(['status' => 'signed', 'signed_at' => now()]);
                $document->status = 'signed';
                $document->signature_doc_link = json_encode($signatureLinks);
                $document->signed_doc_link = $signedPdfUrl;
                $document->signed_hash = $signedHash;
                $document->hash_generated_at = now();
                $document->save();

                Log::info("Public document signed successfully", [
                    'document_id' => $document->id,
                    'signer_id' => $signer->id,
                    'signed_at' => now()->toISOString()
                ]);

                // Create notifications and activity logs
                $this->createSignatureNotifications($document, $signer);

                // Redirect to thank you page with success message
                return redirect()->route('public.documents.thankyou', ['id' => $document->id])
                    ->with('success', 'Document signed successfully! You can now download your signed document.');
            }

            // If we reach here, something unexpected happened
            Log::error('Unexpected state in submitSignatures', [
                'document_id' => $document->id,
                'signer_id' => $signer->id,
                'signer_status' => $signer->status,
                'signer_token_set' => $signer->token !== null
            ]);
            // ✅ FIX #4: Return to signing page with error
            return redirect()->back()
                ->with('error', 'An unexpected error occurred while processing your signature. Please try again or contact support if the issue persists.');
        } catch (\Exception $e) {
            Log::error("Error in public submitSignatures", [
                'error' => $e->getMessage(),
                'document_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            // ✅ FIX #4: Return to signing page with error
            return redirect()->back()
                ->with('error', 'An unexpected error occurred while processing your signature. Please try again or contact support.');
        }
    }

    /**
     * Get a specific page of the PDF as an image
     * 
     * @param int $id Document ID
     * @param int $page Page number
     * @return \Illuminate\Http\Response
     */
    public function getPage($id, $page)
    {
        // Clear any existing output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        try {
            $document = Document::findOrFail($id);
            
            // Check if cached image already exists
            $cachedImagePath = storage_path('app/public/pdf_pages/doc_' . $id . '_page_' . $page . '.png');
            if (file_exists($cachedImagePath) && filesize($cachedImagePath) > 0) {
                Log::info('Serving cached page image', [
                    'document_id' => $id,
                    'page' => $page,
                    'cached_path' => $cachedImagePath
                ]);
                return response()->file($cachedImagePath, [
                    'Content-Type' => 'image/png',
                    'Cache-Control' => 'public, max-age=86400', // Cache for 24 hours
                ]);
            }
            
            $url = $document->myfile;
            $pdfPath = null;
            $tmpPdfPath = null;
            $isLocalFile = false;

            // Check if URL is a full S3 URL or local path
            if ($url && filter_var($url, FILTER_VALIDATE_URL) && strpos($url, 's3') !== false) {
                // This is an S3 URL - extract the key
                $parsed = parse_url($url);
                if (isset($parsed['path'])) {
                    $pdfPath = ltrim(urldecode($parsed['path']), '/');
                }
                
                if (!$pdfPath || !Storage::disk('s3')->exists($pdfPath)) {
                    Log::error('PDF file not found in S3 for document: ' . $id, [
                        'document_id' => $id,
                        's3Key' => $pdfPath,
                        'myfile' => $url,
                        's3_exists' => $pdfPath ? Storage::disk('s3')->exists($pdfPath) : 'no_path'
                    ]);
                    abort(404, 'Document file not found');
                }

                // Download PDF from S3 to a temp file
                $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
                $pdfStream = Storage::disk('s3')->get($pdfPath);
                file_put_contents($tmpPdfPath, $pdfStream);
                Log::info('Downloaded S3 file for document page', ['s3Key' => $pdfPath, 'tempPath' => $tmpPdfPath]);
            } elseif ($url && file_exists(storage_path('app/public/' . $url))) {
                // This is a local file path and file exists
                $tmpPdfPath = storage_path('app/public/' . $url);
                $isLocalFile = true;
                Log::info('Using local file for document page', ['path' => $tmpPdfPath]);
            } else {
                // Try to build S3 key from DB fields as fallback
                if (!empty($document->myfile_key) && !empty($document->doc_type) && !empty($document->client_id)) {
                    $admin = \DB::table('admins')->select('client_id')->where('id', $document->client_id)->first();
                    if ($admin && $admin->client_id) {
                        $pdfPath = $admin->client_id . '/' . $document->doc_type . '/' . $document->myfile_key;
                        
                        if (Storage::disk('s3')->exists($pdfPath)) {
                            // Download PDF from S3 to a temp file
                            $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
                            $pdfStream = Storage::disk('s3')->get($pdfPath);
                            file_put_contents($tmpPdfPath, $pdfStream);
                            Log::info('Downloaded S3 file via fallback for document page', ['s3Key' => $pdfPath, 'tempPath' => $tmpPdfPath]);
                        } else {
                            Log::error('PDF file not found in S3 fallback for document: ' . $id, [
                                'document_id' => $id,
                                's3Key' => $pdfPath,
                                'myfile' => $url,
                                'myfile_key' => $document->myfile_key,
                                'doc_type' => $document->doc_type,
                                'client_id' => $document->client_id,
                                'local_exists' => $url ? file_exists(storage_path('app/public/' . $url)) : false
                            ]);
                            abort(404, 'Document file not found');
                        }
                    } else {
                        Log::error('Admin not found for document: ' . $id, [
                            'document_id' => $id,
                            'client_id' => $document->client_id
                        ]);
                        abort(404, 'Document file not found');
                    }
                } else {
                    Log::error('PDF file not found for document: ' . $id, [
                        'document_id' => $id,
                        'myfile' => $url,
                        'myfile_key' => $document->myfile_key,
                        'doc_type' => $document->doc_type,
                        'client_id' => $document->client_id,
                        'local_exists' => $url ? file_exists(storage_path('app/public/' . $url)) : false
                    ]);
                    abort(404, 'Document file not found');
                }
            }

            // Ensure we have a valid PDF path
            if (!$tmpPdfPath || !file_exists($tmpPdfPath)) {
                Log::error('No valid PDF path found for document: ' . $id, [
                    'document_id' => $id,
                    'tmpPdfPath' => $tmpPdfPath,
                    'file_exists' => $tmpPdfPath ? file_exists($tmpPdfPath) : false
                ]);
                abort(404, 'Document file not found');
            }

            try {
                // Try Python Service first
                $pythonService = app(\App\Services\PythonService::class);
                
                if ($pythonService->isHealthy()) {
                    $result = $pythonService->convertPageToImage($tmpPdfPath, (int)$page, 72);
                    
                    if ($result && $result['success']) {
                        $imageData = $result['image_data'];
                        
                        // Remove data URI prefix if present
                        if (strpos($imageData, 'data:image/png;base64,') === 0) {
                            $imageData = substr($imageData, strlen('data:image/png;base64,'));
                        }
                        
                        // Decode base64 to image bytes
                        $imageBytes = base64_decode($imageData);
                        
                        // Ensure cache directory exists
                        $cacheDir = storage_path('app/public/pdf_pages');
                        if (!file_exists($cacheDir)) {
                            mkdir($cacheDir, 0755, true);
                        }
                        
                        // Save to cache directory for reuse
                        $imagePath = storage_path('app/public/pdf_pages/doc_' . $id . '_page_' . $page . '.png');
                        file_put_contents($imagePath, $imageBytes);
                        
                        // Only delete temp PDF files, not local files
                        if (!$isLocalFile && $tmpPdfPath && strpos($tmpPdfPath, 'tmp_') !== false) {
                            @unlink($tmpPdfPath);
                        }
                        
                        // Verify image was saved successfully
                        if (file_exists($imagePath)) {
                            Log::info('Page image generated using Python service', [
                                'document_id' => $id,
                                'page' => $page,
                                'image_path' => $imagePath
                            ]);
                            
                            // Use response()->file() instead of response($imageBytes)
                            return response()->file($imagePath);
                        } else {
                            Log::error('Failed to save image file', [
                                'document_id' => $id,
                                'page' => $page,
                                'image_path' => $imagePath
                            ]);
                        }
                    }
                }

                // Fallback to Spatie
                Log::info('Falling back to Spatie for page conversion', [
                    'document_id' => $id,
                    'page' => $page
                ]);
                
                $imagePath = storage_path('app/public/page_' . $id . '_' . $page . '.jpg');
                (new \Spatie\PdfToImage\Pdf($tmpPdfPath))
                    ->selectPage($page)
                    ->resolution(72)
                    ->save($imagePath);

                // Only delete temp files, not local files
                if (!$isLocalFile && $tmpPdfPath && strpos($tmpPdfPath, 'tmp_') !== false) {
                    @unlink($tmpPdfPath);
                }

                if (!file_exists($imagePath)) {
                    throw new \Exception('Failed to generate page image');
                }

                // Clear any output buffers before sending file
                if (ob_get_level()) {
                    ob_end_clean();
                }

                return response()->file($imagePath);
            } catch (\Exception $e) {
                // Only delete temp files, not local files
                if (!$isLocalFile && $tmpPdfPath && strpos($tmpPdfPath, 'tmp_') !== false) {
                    @unlink($tmpPdfPath);
                }
                Log::error('Error generating PDF page image', [
                    'document_id' => $id,
                    'page' => $page,
                    'error' => $e->getMessage()
                ]);
                abort(500, 'Error generating page image');
            }
        } catch (\Exception $e) {
            // Only delete temp files, not local files
            if (isset($isLocalFile) && !$isLocalFile && isset($tmpPdfPath) && $tmpPdfPath && strpos($tmpPdfPath, 'tmp_') !== false) {
                @unlink($tmpPdfPath);
            }
            Log::error('Error in getPage', [
                'document_id' => $id,
                'page' => $page,
                'error' => $e->getMessage()
            ]);
            abort(500, 'An error occurred while retrieving the page');
        }
    }

    /**
     * Download signed document
     * 
     * @param int $id Document ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downloadSigned($id)
    {
        try {
            $document = Document::findOrFail($id);
            
            if ($document->signed_doc_link) {
                $signedDocUrl = $document->signed_doc_link;
                
                // Parse the URL to get the path
                $parsed = parse_url($signedDocUrl);
                $urlPath = $parsed['path'] ?? '';
                
                // Check if it's a local storage path (contains /storage/)
                if (strpos($urlPath, '/storage/') !== false) {
                    // Extract the path after /storage/
                    $parts = explode('/storage/', $urlPath);
                    $relativePath = end($parts);
                    
                    // Check if file exists in local storage
                    if (Storage::disk('public')->exists($relativePath)) {
                        $filePath = storage_path('app/public/' . $relativePath);
                        return response()->download($filePath, $document->id . '_signed.pdf');
                    }
                }
                
                // Try S3 storage
                if (isset($parsed['path'])) {
                    $s3Key = ltrim($parsed['path'], '/');
                    $disk = Storage::disk('s3');
                    
                    if ($disk->exists($s3Key)) {
                        $tempUrl = $disk->temporaryUrl(
                            $s3Key,
                            now()->addMinutes(5),
                            ['ResponseContentDisposition' => 'attachment; filename="' . $document->id . '_signed.pdf"']
                        );
                        return redirect($tempUrl);
                    }
                }
                
                return redirect($signedDocUrl);
            }
            
            return redirect('/')->with('error', 'Signed document not found.');
        } catch (\Exception $e) {
            Log::error('Error downloading signed document', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect('/')->with('error', 'An error occurred while downloading the document.');
        }
    }

    /**
     * Download signed document and redirect to thank you page
     * 
     * @param int $id Document ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function downloadSignedAndThankyou($id)
    {
        try {
            $document = Document::findOrFail($id);
            
            if ($document->signed_doc_link) {
                $signedDocUrl = $document->signed_doc_link;
                $downloadUrl = null;
                
                // Parse the URL to get the path
                $parsed = parse_url($signedDocUrl);
                $urlPath = $parsed['path'] ?? '';
                
                // Check if it's a local storage path (contains /storage/)
                if (strpos($urlPath, '/storage/') !== false) {
                    // Extract the path after /storage/
                    $parts = explode('/storage/', $urlPath);
                    $relativePath = end($parts);
                    
                    // Check if file exists in local storage
                    if (Storage::disk('public')->exists($relativePath)) {
                        // Generate a temporary download route for local files
                        $downloadUrl = route('public.documents.download.signed', $document->id);
                    }
                } else {
                    // Try S3 storage
                    if (isset($parsed['path'])) {
                        $s3Key = ltrim($parsed['path'], '/');
                        $disk = Storage::disk('s3');
                        
                        if ($disk->exists($s3Key)) {
                            $downloadUrl = $disk->temporaryUrl(
                                $s3Key,
                                now()->addMinutes(5),
                                ['ResponseContentDisposition' => 'attachment; filename="' . $document->id . '_signed.pdf"']
                            );
                        }
                    }
                }
                
                // If we found a valid download URL, show the download & thank you view
                if ($downloadUrl) {
                    return view('documents.download_and_thankyou', [
                        'downloadUrl' => $downloadUrl,
                        'thankyouUrl' => route('public.documents.thankyou', ['id' => $id])
                    ]);
                }
                
                return view('documents.download_and_thankyou', [
                    'downloadUrl' => $signedDocUrl,
                    'thankyouUrl' => route('public.documents.thankyou', ['id' => $id])
                ]);
            }
            
            return redirect('/')->with('error', 'Signed document not found.');
        } catch (\Exception $e) {
            Log::error('Error in downloadSignedAndThankyou', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect('/')->with('error', 'An error occurred while preparing your download.');
        }
    }

    /**
     * Show thank you page after signing
     * 
     * @param Request $request
     * @param int|null $id Document ID
     * @return \Illuminate\View\View
     */
    public function thankyou(Request $request, $id = null)
    {
        $downloadUrl = null;
        $document = null;
        
        if ($id) {
            $document = Document::find($id);
            if ($document && $document->signed_doc_link) {
                $parsed = parse_url($document->signed_doc_link);
                if (isset($parsed['path'])) {
                    $s3Key = ltrim($parsed['path'], '/');
                    $disk = Storage::disk('s3');
                    if ($disk->exists($s3Key)) {
                        $downloadUrl = $disk->temporaryUrl(
                            $s3Key,
                            now()->addMinutes(5),
                            ['ResponseContentDisposition' => 'attachment; filename="' . $document->id . '_signed.pdf"']
                        );
                    }
                }
            }
        }
        
        $message = 'You have successfully signed your document.';
        return view('thanks', compact('downloadUrl', 'message', 'id', 'document'));
    }

    /**
     * Send reminder to signer
     * 
     * @param Request $request
     * @param int $id Document ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendReminder(Request $request, $id)
    {
        $documentId = (int) $id;
        if ($documentId <= 0) {
            return redirect()->back()->with('error', 'Invalid document ID.');
        }

        $request->validate([
            'signer_id' => 'required|integer|exists:signers,id'
        ]);

        $signerId = (int) $request->signer_id;

        try {
            $document = Document::findOrFail($documentId);
            $signer = $document->signers()->findOrFail($signerId);

            if ($signer->status === 'signed') {
                return redirect()->back()->with('error', 'Document is already signed.');
            }

            if ($signer->reminder_count >= 3) {
                return redirect()->back()->with('error', 'Maximum reminders already sent.');
            }

            if ($signer->last_reminder_sent_at && $signer->last_reminder_sent_at->diffInHours(now()) < 24) {
                return redirect()->back()->with('error', 'Please wait 24 hours between reminders.');
            }

            // Send reminder email
            $signingUrl = url("/sign/{$document->id}/{$signer->token}");
            Mail::raw("This is a reminder to sign your document: " . $signingUrl, function ($message) use ($signer) {
                $message->to($signer->email, $signer->name)
                        ->subject('Reminder: Please Sign Your Document');
            });

            $signer->update([
                'last_reminder_sent_at' => now(),
                'reminder_count' => $signer->reminder_count + 1
            ]);

            return redirect()->back()->with('success', 'Reminder sent successfully!');
        } catch (\Exception $e) {
            Log::error('Error sending reminder', [
                'document_id' => $documentId,
                'signer_id' => $signerId,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'An error occurred while sending the reminder.');
        }
    }

    /**
     * Show document index (optional)
     * 
     * @param int|null $id Document ID
     * @return \Illuminate\View\View
     */
    public function index($id = null)
    {
        // This is typically not used for public access
        // Redirect to home or show error
        return redirect('/')->with('info', 'Please use the link provided in your email.');
    }

    // ==================== Private Helper Methods ====================

    /**
     * Count PDF pages
     */
    protected function countPdfPages($pathToPdf)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pathToPdf);
            $pages = $pdf->getPages();
            return count($pages);
        } catch (\Exception $e) {
            Log::warning('PDF page count failed', ['error' => $e->getMessage()]);
            if (class_exists('Spatie\\PdfToImage\\Pdf')) {
                try {
                    $pdf = new \Spatie\PdfToImage\Pdf($pathToPdf);
                    return $pdf->getNumberOfPages();
                } catch (\Exception $ex) {
                    Log::error('Spatie PDF page count also failed', ['error' => $ex->getMessage()]);
                }
            }
            return null;
        }
    }

    /**
     * Sanitize signature data
     */
    private function sanitizeSignatureData($signatureData, $fieldId)
    {
        if (!is_string($signatureData) || empty($signatureData)) {
            return false;
        }

        $signatureData = strip_tags($signatureData);

        $dangerousPatterns = [
            '/javascript:/i', '/vbscript:/i', '/data:text\/html/i',
            '/data:application\/javascript/i', '/onclick/i', '/onload/i',
            '/onerror/i', '/<script/i', '/<iframe/i'
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $signatureData)) {
                return false;
            }
        }

        if (!preg_match('/^data:image\/png;base64,([A-Za-z0-9+\/=]+)$/', $signatureData, $matches)) {
            return false;
        }

        $base64Data = $matches[1];

        if (strlen($base64Data) > 500000) {
            return false;
        }

        $imageData = base64_decode($base64Data, true);
        if ($imageData === false || strlen($imageData) < 100 || strlen($imageData) > 1000000) {
            return false;
        }

        // Validate PNG signature
        $pngSignature = "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A";
        if (substr($imageData, 0, 8) !== $pngSignature) {
            return false;
        }

        return [
            'imageData' => $imageData,
            'base64Data' => $base64Data,
            'size' => strlen($imageData)
        ];
    }

    /**
     * Sanitize position data
     */
    private function sanitizePositionData($position)
    {
        $sanitized = [];
        $fields = ['x_percent', 'y_percent', 'w_percent', 'h_percent'];

        foreach ($fields as $field) {
            $value = $position[$field] ?? 0;
            $value = (float) $value;
            $value = max(0, min(1, $value));
            if ($field === 'w_percent') $value = max(0.1, $value);
            if ($field === 'h_percent') $value = max(0.05, $value);
            $sanitized[$field] = $value;
        }

        return $sanitized;
    }

    /**
     * Create notifications and activity logs when document is signed
     */
    private function createSignatureNotifications($document, $signer)
    {
        try {
            // Get signer information
            $signerName = $signer->name ?? 'Unknown Signer';
            $signedAt = now()->format('d/m/Y h:i A');
            
            // Determine the associated client/lead and responsible person
            $associatedEntity = null;
            $responsiblePersonId = null;
            $entityType = null;
            $notificationUrl = null;
            
            // Check if document is associated with a client matter
            if ($document->client_matter_id) {
                $clientMatter = \App\Models\ClientMatter::select('client_unique_matter_no', 'sel_person_responsible', 'client_id')
                    ->where('id', $document->client_matter_id)
                    ->first();
                
                if ($clientMatter) {
                    $associatedEntity = \App\Models\Admin::find($clientMatter->client_id);
                    $responsiblePersonId = $clientMatter->sel_person_responsible;
                    $entityType = 'client';
                    $notificationUrl = url("/clients/detail/{$clientMatter->client_id}");
                    
                    if ($associatedEntity) {
                        $this->createActivityAndNotification(
                            $document,
                            $associatedEntity,
                            $responsiblePersonId,
                            $signerName,
                            $signedAt,
                            $entityType,
                            $notificationUrl,
                            $clientMatter->client_unique_matter_no
                        );
                    }
                }
            }
            // Check if document has client_id (direct client association)
            elseif ($document->client_id) {
                $associatedEntity = \App\Models\Admin::find($document->client_id);
                
                if ($associatedEntity) {
                    $entityType = ($associatedEntity->type === 'lead') ? 'lead' : 'client';
                    $notificationUrl = url("/clients/detail/{$associatedEntity->id}");
                    
                    // For direct client associations, try to find responsible person
                    // You can set this to a default admin or get from document creator
                    $responsiblePersonId = $document->created_by ?? null;
                    
                    $this->createActivityAndNotification(
                        $document,
                        $associatedEntity,
                        $responsiblePersonId,
                        $signerName,
                        $signedAt,
                        $entityType,
                        $notificationUrl
                    );
                }
            }
            // Check if document has polymorphic relationship (documentable)
            elseif ($document->documentable_id && $document->documentable_type) {
                if ($document->documentable_type === 'App\\Models\\Admin') {
                    $associatedEntity = \App\Models\Admin::find($document->documentable_id);
                    
                    if ($associatedEntity) {
                        $entityType = ($associatedEntity->type === 'lead') ? 'lead' : 'client';
                        $notificationUrl = url("/clients/detail/{$associatedEntity->id}");
                        $responsiblePersonId = $document->created_by ?? null;
                        
                        $this->createActivityAndNotification(
                            $document,
                            $associatedEntity,
                            $responsiblePersonId,
                            $signerName,
                            $signedAt,
                            $entityType,
                            $notificationUrl
                        );
                    }
                } elseif ($document->documentable_type === 'App\\Models\\ClientMatter') {
                    $clientMatter = \App\Models\ClientMatter::select('client_unique_matter_no', 'sel_person_responsible', 'client_id')
                        ->where('id', $document->documentable_id)
                        ->first();
                    
                    if ($clientMatter) {
                        $associatedEntity = \App\Models\Admin::find($clientMatter->client_id);
                        $responsiblePersonId = $clientMatter->sel_person_responsible;
                        $entityType = 'client';
                        $notificationUrl = url("/clients/detail/{$clientMatter->client_id}");
                        
                        if ($associatedEntity) {
                            $this->createActivityAndNotification(
                                $document,
                                $associatedEntity,
                                $responsiblePersonId,
                                $signerName,
                                $signedAt,
                                $entityType,
                                $notificationUrl,
                                $clientMatter->client_unique_matter_no
                            );
                        }
                    }
                }
            }
            
            Log::info('Document signature notifications processed', [
                'document_id' => $document->id,
                'entity_type' => $entityType,
                'has_entity' => !is_null($associatedEntity),
                'has_responsible_person' => !is_null($responsiblePersonId)
            ]);
            
        } catch (\Exception $e) {
            // Log the error but don't break the signing flow
            Log::error('Error creating signature notifications', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Helper method to create activity log and notification
     */
    private function createActivityAndNotification(
        $document, 
        $entity, 
        $responsiblePersonId, 
        $signerName, 
        $signedAt, 
        $entityType,
        $notificationUrl,
        $matterReference = null
    ) {
        $entityName = $entity->first_name . ' ' . $entity->last_name;
        $documentTitle = $document->title ?? $document->file_name ?? 'Document #' . $document->id;
        
        // Create subject line
        if ($matterReference) {
            $subject = "{$signerName} signed document '{$documentTitle}' for matter {$matterReference}";
        } else {
            $subject = "{$signerName} signed document '{$documentTitle}' for {$entityType} {$entityName}";
        }
        
        $description = "Document signed by {$signerName} at {$signedAt}";
        
        // Create Activity Log (appears on client/lead timeline)
        \App\Models\ActivitiesLog::create([
            'client_id' => $entity->id,
            'created_by' => $responsiblePersonId ?? 1, // Default to system user if no responsible person
            'subject' => $subject,
            'description' => $description,
            'activity_type' => 'document',
        ]);
        
        Log::info('Activity log created for document signature', [
            'document_id' => $document->id,
            'client_id' => $entity->id,
            'entity_type' => $entityType
        ]);
        
        // Create Notification (appears in notification tab)
        if ($responsiblePersonId) {
            \App\Models\Notification::create([
                'sender_id' => $entity->id, // The client/lead is the "sender"
                'receiver_id' => $responsiblePersonId, // The responsible person receives it
                'module_id' => $document->id,
                'url' => $notificationUrl,
                'notification_type' => 'document',
                'message' => $subject,
                'receiver_status' => 0, // Unread
                'seen' => 0,
            ]);
            
            Log::info('Notification created for document signature', [
                'document_id' => $document->id,
                'receiver_id' => $responsiblePersonId,
                'entity_type' => $entityType
            ]);
        } else {
            Log::warning('No responsible person found for notification', [
                'document_id' => $document->id,
                'entity_id' => $entity->id
            ]);
        }
    }
}

