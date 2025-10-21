<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Signer;
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
            return redirect('/')->with('error', 'Invalid document link.');
        }

        // Validate token format
        if (!$token || !is_string($token) || strlen($token) < 32 || !preg_match('/^[a-zA-Z0-9]+$/', $token)) {
            Log::warning('Invalid token format in public sign method', ['token_length' => strlen($token ?? '')]);
            return redirect('/')->with('error', 'Invalid or expired signing link.');
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
                return redirect('/')->with('error', 'Invalid or expired signing link.');
            }

            // Track when document was opened
            if (!$signer->opened_at) {
                $signer->update(['opened_at' => now()]);
            }

            $signatureFields = $document->signatureFields()->get();
            
            // Get PDF path from S3
            $url = $document->myfile;
            $pdfPath = null;
            
            if ($url) {
                $parsed = parse_url($url);
                if (isset($parsed['path'])) {
                    $pdfPath = ltrim(urldecode($parsed['path']), '/');
                }
            }

            if (empty($pdfPath) && !empty($document->myfile_key) && !empty($document->doc_type) && !empty($document->client_id)) {
                $admin = \DB::table('admins')->select('client_id')->where('id', $document->client_id)->first();
                if ($admin && $admin->client_id) {
                    $pdfPath = $admin->client_id . '/' . $document->doc_type . '/' . $document->myfile_key;
                }
            }

            // Count PDF pages
            $pdfPages = 1;
            if ($pdfPath && Storage::disk('s3')->exists($pdfPath)) {
                $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
                $pdfStream = Storage::disk('s3')->get($pdfPath);
                file_put_contents($tmpPdfPath, $pdfStream);
                $pdfPages = $this->countPdfPages($tmpPdfPath) ?: 1;
                @unlink($tmpPdfPath);
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
        // Validation
        $request->validate([
            'signer_id' => 'required|integer|exists:signers,id',
            'signatures' => 'required|array',
            'signatures.*' => 'nullable|string',
            'signature_positions' => 'required|array',
            'signature_positions.*' => 'nullable|string'
        ]);

        $documentId = (int) $id;
        if ($documentId <= 0) {
            return redirect('/')->with('error', 'Invalid document ID.');
        }

        try {
            $document = Document::findOrFail($documentId);
            $signer = Signer::findOrFail($request->signer_id);

            // Verify signer belongs to this document
            if ($signer->document_id !== $document->id) {
                Log::warning('Signer does not belong to document', [
                    'signer_id' => $signer->id,
                    'document_id' => $document->id
                ]);
                return redirect('/')->with('error', 'Invalid signing attempt.');
            }

            if ($signer->token !== null && $signer->status === 'pending') {
                // Get S3 paths
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

                if (!$s3Key) {
                    return redirect('/')->with('error', 'Document file not found.');
                }

                // Download PDF from S3
                $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
                $pdfStream = Storage::disk('s3')->get($s3Key);
                file_put_contents($tmpPdfPath, $pdfStream);
                $outputTmpPath = storage_path('app/tmp_' . uniqid() . '_signed.pdf');

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

                        // Upload to S3
                        $filename = sprintf('%d_field_%d_%s.png', $signer->id, $sanitizedFieldId, bin2hex(random_bytes(8)));
                        $s3SignaturePath = $clientId . '/' . $docType . '/signatures/' . $filename;
                        Storage::disk('s3')->put($s3SignaturePath, $imageData);
                        $s3SignatureUrl = Storage::disk('s3')->url($s3SignaturePath);

                        // Store position
                        $position = $positions[$fieldId] ?? [];
                        $sanitizedPosition = $this->sanitizePositionData($position);

                        $signaturePositions[$sanitizedFieldId] = [
                            'path' => $s3SignaturePath,
                            'page' => $pageNum,
                            'x_percent' => $sanitizedPosition['x_percent'],
                            'y_percent' => $sanitizedPosition['y_percent'],
                            'w_percent' => $sanitizedPosition['w_percent'],
                            'h_percent' => $sanitizedPosition['h_percent']
                        ];
                        $signatureLinks[$sanitizedFieldId] = $s3SignatureUrl;
                        $signaturesSaved = true;
                    }
                }

                if (!$signaturesSaved) {
                    return redirect('/')->with('error', 'No signatures provided. Please draw signatures before submitting.');
                }

                // Create signed PDF
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
                            $s3SignaturePath = $signatureInfo['path'];
                            
                            $pdfWidth = $specs['width'];
                            $pdfHeight = $specs['height'];
                            $x_mm = $signatureInfo['x_percent'] * $pdfWidth;
                            $y_mm = $signatureInfo['y_percent'] * $pdfHeight;
                            $w_mm = max(15, $signatureInfo['w_percent'] * $pdfWidth);
                            $h_mm = max(15, $signatureInfo['h_percent'] * $pdfHeight);

                            // Download signature from S3
                            $tmpSignaturePath = storage_path('app/tmp_signature_' . uniqid() . '.png');
                            $s3Image = Storage::disk('s3')->get($s3SignaturePath);
                            file_put_contents($tmpSignaturePath, $s3Image);

                            if (file_exists($tmpSignaturePath)) {
                                $pdf->Image($tmpSignaturePath, $x_mm, $y_mm, $w_mm, $h_mm, 'PNG');
                                @unlink($tmpSignaturePath);
                            }
                        }
                    }
                }

                // Save signed PDF
                $pdf->Output($outputTmpPath, 'F');

                if (!file_exists($outputTmpPath) || filesize($outputTmpPath) === 0) {
                    return redirect('/')->with('error', 'Failed to save the signed PDF. Please contact support.');
                }

                // Generate SHA-256 hash for tamper detection (Phase 7)
                $signedHash = hash_file('sha256', $outputTmpPath);
                Log::info('Generated document hash', [
                    'document_id' => $document->id,
                    'hash' => $signedHash
                ]);

                // Upload to S3
                $s3SignedPath = $clientId . '/' . $docType . '/signed/' . $document->id . '_signed.pdf';
                Storage::disk('s3')->put($s3SignedPath, fopen($outputTmpPath, 'r'));
                $s3SignedUrl = Storage::disk('s3')->url($s3SignedPath);

                // Clean up temp files
                @unlink($tmpPdfPath);
                @unlink($outputTmpPath);

                // Update statuses and save hash
                $signer->update(['status' => 'signed', 'signed_at' => now()]);
                $document->status = 'signed';
                $document->signature_doc_link = json_encode($signatureLinks);
                $document->signed_doc_link = $s3SignedUrl;
                $document->signed_hash = $signedHash;
                $document->hash_generated_at = now();
                $document->save();

                Log::info("Public document signed successfully", [
                    'document_id' => $document->id,
                    'signer_id' => $signer->id
                ]);

                // Redirect to thank you page
                return redirect()->route('public.documents.thankyou', ['id' => $document->id]);
            }

            return redirect('/')->with('error', 'Invalid signing attempt.');
        } catch (\Exception $e) {
            Log::error("Error in public submitSignatures", [
                'error' => $e->getMessage(),
                'document_id' => $id
            ]);
            return redirect('/')->with('error', 'An unexpected error occurred: ' . $e->getMessage());
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
        try {
            $document = Document::findOrFail($id);
            $url = $document->myfile;
            $pdfPath = null;

            if ($url) {
                $parsed = parse_url($url);
                if (isset($parsed['path'])) {
                    $pdfPath = ltrim(urldecode($parsed['path']), '/');
                }
            }

            if (empty($pdfPath) && !empty($document->myfile_key) && !empty($document->doc_type) && !empty($document->client_id)) {
                $admin = \DB::table('admins')->select('client_id')->where('id', $document->client_id)->first();
                if ($admin && $admin->client_id) {
                    $pdfPath = $admin->client_id . '/' . $document->doc_type . '/' . $document->myfile_key;
                }
            }

            if (!$pdfPath || !Storage::disk('s3')->exists($pdfPath)) {
                abort(404, 'Document file not found');
            }

            // Download PDF from S3
            $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
            $pdfStream = Storage::disk('s3')->get($pdfPath);
            file_put_contents($tmpPdfPath, $pdfStream);

            try {
                // Try Python PDF Service first
                $pythonPDFService = new \App\Services\PythonPDFService();
                
                if ($pythonPDFService->isHealthy()) {
                    $result = $pythonPDFService->convertPageToImage($tmpPdfPath, (int)$page, 150);
                    
                    if ($result && $result['success']) {
                        @unlink($tmpPdfPath);
                        $imageData = $result['image_data'];
                        
                        if (strpos($imageData, 'data:image/png;base64,') === 0) {
                            $imageData = substr($imageData, strlen('data:image/png;base64,'));
                        }
                        
                        $imageBytes = base64_decode($imageData);
                        
                        return response($imageBytes)
                            ->header('Content-Type', 'image/png')
                            ->header('Cache-Control', 'public, max-age=3600');
                    }
                }

                // Fallback to Spatie
                $imagePath = storage_path('app/public/page_' . $id . '_' . $page . '.jpg');
                (new \Spatie\PdfToImage\Pdf($tmpPdfPath))
                    ->selectPage($page)
                    ->resolution(150)
                    ->save($imagePath);

                @unlink($tmpPdfPath);

                if (!file_exists($imagePath)) {
                    throw new \Exception('Failed to generate page image');
                }

                return response()->file($imagePath);
            } catch (\Exception $e) {
                @unlink($tmpPdfPath);
                Log::error('Error generating PDF page image', [
                    'document_id' => $id,
                    'page' => $page,
                    'error' => $e->getMessage()
                ]);
                abort(500, 'Error generating page image');
            }
        } catch (\Exception $e) {
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
                $parsed = parse_url($signedDocUrl);
                
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
                $parsed = parse_url($signedDocUrl);
                
                if (isset($parsed['path'])) {
                    $s3Key = ltrim($parsed['path'], '/');
                    $disk = Storage::disk('s3');
                    
                    if ($disk->exists($s3Key)) {
                        $tempUrl = $disk->temporaryUrl(
                            $s3Key,
                            now()->addMinutes(5),
                            ['ResponseContentDisposition' => 'attachment; filename="' . $document->id . '_signed.pdf"']
                        );
                        
                        return view('documents.download_and_thankyou', [
                            'downloadUrl' => $tempUrl,
                            'thankyouUrl' => route('public.documents.thankyou', ['id' => $id])
                        ]);
                    }
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
}

