<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\Document;
use App\Models\Signer;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Str;
use Spatie\PdfToImage\Pdf;
use setasign\Fpdi\TcpdfFpdi;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\ActivitiesLog;
use App\Models\ClientMatter;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use App\Models\UploadChecklist;
use App\Models\Email;
use App\Models\MailReport;

class DocumentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:admin');
    }
    /**
     * Standard error handling for controller methods
     *
     * @param \Exception $e The exception that occurred
     * @param string $context Context of where the error occurred
     * @param string $userMessage User-friendly error message
     * @param string $redirectRoute Route to redirect to (default: back)
     * @param array $additionalContext Additional context for logging
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleError(\Exception $e, $context, $userMessage = 'An error occurred', $redirectRoute = 'back', $additionalContext = [])
    {
        // Log the error with context
        $logContext = array_merge([
            'context' => $context,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'user_id' => auth('admin')->id(),
            'url' => request()->url(),
            'ip' => request()->ip()
        ], $additionalContext);

        \Log::error("Controller error in {$context}", $logContext);

        // Return appropriate redirect
        if ($redirectRoute === 'back') {
            return redirect()->back()->with('error', $userMessage);
        } else {
            return redirect()->route($redirectRoute)->with('error', $userMessage);
        }
    }

    /**
     * Handle validation errors consistently
     *
     * @param array $errors Array of validation errors
     * @param string $context Context where validation failed
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleValidationError($errors, $context = 'validation')
    {
        \Log::warning("Validation failed in {$context}", [
            'errors' => $errors,
            'user_id' => auth('admin')->id(),
            'input' => request()->except(['password', 'password_confirmation', '_token'])
        ]);

        return redirect()->back()
            ->withErrors($errors)
            ->withInput();
    }

    /**
     * General purpose input sanitization for string data
     *
     * @param string $input The input string to sanitize
     * @param bool $allowHtml Whether to allow HTML tags (default: false)
     * @return string Sanitized string
     */
    private function sanitizeStringInput($input, $allowHtml = false)
    {
        if (!is_string($input)) {
            return '';
        }

        // Trim whitespace
        $input = trim($input);

        // Remove null bytes to prevent null byte injection
        $input = str_replace(chr(0), '', $input);

        // Remove or escape HTML based on requirements
        if (!$allowHtml) {
            $input = strip_tags($input);
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }

        // Remove potentially dangerous characters and patterns
        $dangerousPatterns = [
            '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', // Control characters
            '/javascript:/i',
            '/vbscript:/i',
            '/data:/i',
            '/onclick/i',
            '/onload/i',
            '/onerror/i'
        ];

        foreach ($dangerousPatterns as $pattern) {
            $input = preg_replace($pattern, '', $input);
        }

        return $input;
    }

    /**
     * Sanitize and validate base64 signature data to prevent XSS and code injection
     *
     * @param string $signatureData The raw signature data from client
     * @param int $fieldId The field ID for logging
     * @return array|false Returns sanitized data array or false if invalid
     */
    private function sanitizeSignatureData($signatureData, $fieldId)
    {
        // Check if signature data is string and not empty
        if (!is_string($signatureData) || empty($signatureData)) {
            \Log::warning('Empty or non-string signature data', ['fieldId' => $fieldId]);
            return false;
        }

        // Prevent XSS by removing any potential script tags or HTML
        $signatureData = strip_tags($signatureData);

        // Additional XSS protection - remove javascript: protocols and other dangerous patterns
        $dangerousPatterns = [
            '/javascript:/i',
            '/vbscript:/i',
            '/data:text\/html/i',
            '/data:application\/javascript/i',
            '/onclick/i',
            '/onload/i',
            '/onerror/i',
            '/<script/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/<form/i'
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $signatureData)) {
                \Log::warning('Dangerous pattern detected in signature data', [
                    'fieldId' => $fieldId,
                    'pattern' => $pattern
                ]);
                return false;
            }
        }

        // Strict validation of base64 image data format
        if (!preg_match('/^data:image\/png;base64,([A-Za-z0-9+\/=]+)$/', $signatureData, $matches)) {
            \Log::warning('Invalid signature data format', ['fieldId' => $fieldId]);
            return false;
        }

        $base64Data = $matches[1]; // Use captured group instead of substr for safety

        // Validate base64 data length (prevent DoS)
        if (strlen($base64Data) > 500000) { // 500KB limit
            \Log::warning('Signature data too large', ['fieldId' => $fieldId, 'size' => strlen($base64Data)]);
            return false;
        }

        // Validate base64 format more strictly
        if (!preg_match('/^[A-Za-z0-9+\/]*={0,2}$/', $base64Data)) {
            \Log::warning('Invalid base64 format', ['fieldId' => $fieldId]);
            return false;
        }

        // Decode with strict mode
        $imageData = base64_decode($base64Data, true);
        if ($imageData === false) {
            \Log::warning('Failed to decode base64 data', ['fieldId' => $fieldId]);
            return false;
        }

        // Validate decoded data size
        if (strlen($imageData) < 100 || strlen($imageData) > 1000000) { // 100 bytes min, 1MB max
            \Log::warning('Invalid decoded image size', [
                'fieldId' => $fieldId,
                'size' => strlen($imageData)
            ]);
            return false;
        }

        // Validate PNG file signature (magic bytes)
        $pngSignature = "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A";
        if (substr($imageData, 0, 8) !== $pngSignature) {
            \Log::warning('Invalid PNG signature', ['fieldId' => $fieldId]);
            return false;
        }

        // Additional PNG structure validation
        if (!$this->validatePngStructure($imageData)) {
            \Log::warning('Invalid PNG structure', ['fieldId' => $fieldId]);
            return false;
        }

        return [
            'imageData' => $imageData,
            'base64Data' => $base64Data,
            'size' => strlen($imageData)
        ];
    }

    /**
     * Validate PNG file structure to ensure it's a legitimate image
     *
     * @param string $imageData Binary image data
     * @return bool True if valid PNG structure
     */
    private function validatePngStructure($imageData)
    {
        $length = strlen($imageData);

        // PNG must be at least 8 bytes (signature) + 25 bytes (IHDR chunk) + 12 bytes (IEND chunk)
        if ($length < 45) {
            return false;
        }

        // Check for IHDR chunk (must be first chunk after signature)
        $ihdrPos = strpos($imageData, 'IHDR', 8);
        if ($ihdrPos === false || $ihdrPos !== 12) {
            return false;
        }

        // Check for IEND chunk (must be at the end)
        $iendPos = strrpos($imageData, 'IEND');
        if ($iendPos === false || $iendPos + 8 !== $length) {
            return false;
        }

        // Basic dimension validation from IHDR
        $ihdrData = substr($imageData, 16, 8); // Width and height (4 bytes each)
        $width = unpack('N', substr($ihdrData, 0, 4))[1];
        $height = unpack('N', substr($ihdrData, 4, 4))[1];

        // Reasonable dimension limits for signatures
        if ($width < 10 || $width > 2000 || $height < 10 || $height > 2000) {
            \Log::warning('PNG dimensions out of acceptable range', [
                'width' => $width,
                'height' => $height
            ]);
            return false;
        }

        return true;
    }

    /**
     * Sanitize position data to prevent injection attacks
     *
     * @param array $position Raw position data
     * @return array Sanitized position data
     */
    private function sanitizePositionData($position)
    {
        $sanitized = [];

        $fields = ['x_percent', 'y_percent', 'w_percent', 'h_percent'];

        foreach ($fields as $field) {
            $value = $position[$field] ?? 0;
            // Convert to float and validate range, then store as decimal (0-1)
            $value = (float) $value;
            $value = max(0, min(1, $value));  // Clamp 0-1 (since /100 already done)
            if ($field === 'w_percent') $value = max(0.1, $value);  // Min 10%
            if ($field === 'h_percent') $value = max(0.05, $value);  // Min 5%
            $sanitized[$field] = $value;
        }

        return $sanitized;
    }

    /*public function index()
    {
        $documents = Document::with('signers')->get();
        return view('Admin.documents.index', compact('documents'));
    }*/

    public function index($id = null)
    {
        $documents = Document::with('signers')->get();
        $selectedDocument = null;
        if ($id) {
            $selectedDocument = Document::with('signers')->find($id);
        } //dd($selectedDocument);
        return view('Admin.documents.index', compact('documents', 'selectedDocument'));
    }

    public function create()
    {
        return view('Admin.documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-\_\.\(\)]+$/',
            'document' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ], [
            'title.regex' => 'Title can only contain letters, numbers, spaces, hyphens, underscores, periods, and parentheses.'
        ]);

        try {
            // Sanitize title to prevent XSS
            $sanitizedTitle = strip_tags(trim($request->title));
            $sanitizedTitle = htmlspecialchars($sanitizedTitle, ENT_QUOTES, 'UTF-8');

            $document = auth('admin')->user()->documents()->create([
                'title' => $sanitizedTitle,
            ]);

            // Verify the uploaded file is a valid PDF
            $uploadedFile = $request->file('document');
            if (!$uploadedFile->isValid()) {
                throw new \Exception('Invalid file upload');
            }

            // Additional MIME type verification
            $mimeType = $uploadedFile->getMimeType();
            if (!in_array($mimeType, ['application/pdf'])) {
                throw new \Exception('Invalid file type. Only PDF files are allowed.');
            }

            // Store uploaded file temporarily
            $tempPath = $uploadedFile->storeAs('tmp', uniqid('pdf_', true) . '.pdf', 'public');
            $tempFullPath = storage_path('app/public/' . $tempPath);
            \Log::info('After storeAs', ['tempFullPath' => $tempFullPath, 'exists' => file_exists($tempFullPath)]);
            $normalizedDir = storage_path('app/public/tmp/normalized');
            if (!file_exists($normalizedDir)) {
                mkdir($normalizedDir, 0777, true);
            }
            $normalizedPath = $normalizedDir . '/' . basename($tempFullPath);

            // Normalize PDF with Ghostscript
            if ($this->normalizePdfWithGhostscript($tempFullPath, $normalizedPath)) {
                $pdfToAdd = $normalizedPath;
                \Log::info('PDF normalized with Ghostscript', ['original' => $tempFullPath, 'normalized' => $normalizedPath]);
            } else {
                $pdfToAdd = $tempFullPath;
                \Log::warning('Ghostscript normalization failed, using original PDF', ['path' => $tempFullPath]);
            }

            // Add the (possibly normalized) PDF to media collection
            \Log::info('About to add PDF to media library', ['pdfToAdd' => $pdfToAdd, 'exists' => file_exists($pdfToAdd)]);
            $media = $document->addMedia($pdfToAdd)->toMediaCollection('documents');
            \Log::info('Added PDF to media library', ['media_id' => $media->id ?? null, 'pdfToAdd' => $pdfToAdd, 'exists_after' => file_exists($pdfToAdd)]);
            // If you want to keep the original file, you can use ->preserveOriginal() before toMediaCollection()
            // $document->addMedia($pdfToAdd)->preserveOriginal()->toMediaCollection('documents');

            // Clean up temp files (after confirming addMedia)
            \Log::info('About to delete temp files', ['tempFullPath' => $tempFullPath, 'exists' => file_exists($tempFullPath), 'normalizedPath' => $normalizedPath, 'normalized_exists' => file_exists($normalizedPath)]);
            @unlink($tempFullPath);
            if (file_exists($normalizedPath)) {
                @unlink($normalizedPath);
            }

            \Log::info('Document uploaded successfully', [
                'document_id' => $document->id,
                'user_id' => auth('admin')->id(),
                'filename' => $uploadedFile->getClientOriginalName()
            ]);

            return redirect()->route('documents.index')->with('success', 'Document uploaded successfully!');
        } catch (\Exception $e) {
            return $this->handleError(
                $e,
                'document_upload',
                'An error occurred while uploading the document. Please try again.',
                'back',
                ['filename' => request()->file('document')?->getClientOriginalName()]
            );
        }
    }

    /**
     * Normalize a PDF using Ghostscript to ensure compatibility with FPDI.
     * Returns true on success, false on failure.
     */
    private function normalizePdfWithGhostscript($inputPath, $outputPath)
    {
        // Path to Ghostscript executable (ensure it's in your PATH or use full path)
        $gsPath = 'gswin64c'; // or e.g. 'C:\\Program Files\\gs\\gs10.03.0\\bin\\gswin64c.exe'
        $cmd = '"' . $gsPath . '" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=' . escapeshellarg($outputPath) . ' ' . escapeshellarg($inputPath);
        exec($cmd, $output, $returnVar);
        return $returnVar === 0 && file_exists($outputPath);
    }

    public function edit($id)
    {
        // Sanitize and validate document ID
        $documentId = (int) $id;
        if ($documentId <= 0) {
            \Log::warning('Invalid document ID provided for edit', ['id' => $id]);
            return redirect()->route('documents.index')->with('error', 'Invalid document ID.');
        }

        try {
            $document = \App\Models\Document::findOrFail($documentId); //dd($document);
            //$pdfPath = $document->getFirstMediaPath('documents');
            $url = $document->myfile;
            $pdfPath = null;
            $s3Key = null;

            // Try to extract S3 key from URL if possible
            if ($url) {
                $parsed = parse_url($url);
                if (isset($parsed['path'])) {
                    $pdfPath = ltrim(urldecode($parsed['path']), '/');
                }
            }

            // If myfile_key is set, try to build S3 key from DB fields
            if (empty($pdfPath) && !empty($document->myfile_key) && !empty($document->doc_type) && !empty($document->client_id)) {
                $admin = \DB::table('admins')->select('client_id')->where('id', $document->client_id)->first();
                if ($admin && $admin->client_id) {
                    $pdfPath = $admin->client_id . '/' . $document->doc_type . '/' . $document->myfile_key;
                }
            }

            // Check if PDF file exists
            if (!$pdfPath || ! \Storage::disk('s3')->exists($pdfPath)) {
                \Log::error('PDF file not found for document: ' . $documentId);
                return redirect()->route('documents.index')->with('error', 'Document file not found.');
            }

             // Download PDF from S3 to a temp file
             $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
             $pdfStream = \Storage::disk('s3')->get($pdfPath);
             file_put_contents($tmpPdfPath, $pdfStream);

             try {
                 $pdfPages = $this->countPdfPages($tmpPdfPath);
                 if (!$pdfPages || $pdfPages < 1) {
                    \Log::error('Failed to count PDF pages for document: ' . $documentId);
                    return redirect()->route('documents.index')->with('error', 'Failed to read PDF file.');
                }
                // Get page dimensions
                 $pagesDimensions = $this->getPdfPageDimensions($tmpPdfPath, $pdfPages);

                 // Set default dimensions from first page or use A4 defaults
                $pdfWidthMM = $pagesDimensions[1]['width'] ?? 210;
                $pdfHeightMM = $pagesDimensions[1]['height'] ?? 297;
             } catch (\Exception $e) {
                 \Log::error('Error getting PDF pages or size: ' . $e->getMessage());
             }

             // Clean up temp file
             @unlink($tmpPdfPath);
            // Count PDF pages using multiple methods for better compatibility
            /*$pdfPages = $this->countPdfPages($pdfPath);
            if (!$pdfPages || $pdfPages < 1) {
                \Log::error('Failed to count PDF pages for document: ' . $documentId);
                return redirect()->route('documents.index')->with('error', 'Failed to read PDF file.');
            }*/

            // Get page dimensions
            /*$pagesDimensions = $this->getPdfPageDimensions($pdfPath, $pdfPages);

            // Set default dimensions from first page or use A4 defaults
            $pdfWidthMM = $pagesDimensions[1]['width'] ?? 210;
            $pdfHeightMM = $pagesDimensions[1]['height'] ?? 297;*/

            // Use the correct view path for admin documents edit
            return view('Admin.documents.edit', compact('document', 'pdfPages', 'pdfWidthMM', 'pdfHeightMM', 'pagesDimensions'));
        } catch (\Exception $e) {
            \Log::error('Exception in DocumentController@edit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'document_id' => $documentId
            ]);
            return $this->handleError(
                $e,
                'document_edit',
                'An error occurred while loading the document for editing.',
                'documents.index',
                ['document_id' => $documentId]
            );
        }
    }

    /**
     * Count the number of pages in a PDF file using Smalot\PdfParser, with fallback to Spatie\PdfToImage.
     */
    protected function countPdfPages($pathToPdf)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pathToPdf);
            $pages = $pdf->getPages();
            return count($pages);
        } catch (\Exception $e) {
            // Log the error and fallback to Spatie\PdfToImage if available
            \Log::warning('Smalot/PdfParser failed, trying Spatie/pdf-to-image', ['error' => $e->getMessage()]);
            if (class_exists('Spatie\\PdfToImage\\Pdf')) {
                try {
                    $pdf = new \Spatie\PdfToImage\Pdf($pathToPdf);
                    return $pdf->getNumberOfPages();
                } catch (\Exception $ex) {
                    \Log::error('Spatie/pdf-to-image also failed', ['error' => $ex->getMessage()]);
                }
            }
            return null;
        }
    }

    /**
     * Get PDF page dimensions using FPDI
     */
    private function getPdfPageDimensions($pdfPath, $pageCount)
    {
        $pagesDimensions = [];

        try {
            $pdf = new \setasign\Fpdi\TcpdfFpdi('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->setSourceFile($pdfPath);

            for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
                try {
                    $tplIdx = $pdf->importPage($pageNum);
                    $specs = $pdf->getTemplateSize($tplIdx);
                    $pagesDimensions[$pageNum] = [
                        'width' => $specs['width'],
                        'height' => $specs['height'],
                        'orientation' => $specs['orientation']
                    ];
                } catch (\Exception $e) {
                    // Use defaults for this page
                    $pagesDimensions[$pageNum] = [
                        'width' => 210,
                        'height' => 297,
                        'orientation' => 'P'
                    ];
                    \Log::warning("Failed to get dimensions for page {$pageNum}", ['error' => $e->getMessage()]);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to get page dimensions, using defaults', ['error' => $e->getMessage()]);
            // Use defaults for all pages
            for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
                $pagesDimensions[$pageNum] = [
                    'width' => 210,
                    'height' => 297,
                    'orientation' => 'P'
                ];
            }
        }

        return $pagesDimensions;
    }

    public function update(Request $request, $id)
    {
        // Sanitize document ID
        $documentId = (int) $id;
        if ($documentId <= 0) {
            return back()->withErrors(['error' => 'Invalid document ID.']);
        }

        //$document = auth()->user()->documents()->findOrFail($documentId);
        $document = \App\Models\Document::findOrFail($documentId);

        // Updated validation for percentages
        $request->validate([
            'signatures' => 'required|array|min:1|max:100',
            'signatures.*.page_number' => 'required|integer|min:1|max:999',
            'signatures.*.x_percent' => 'required|numeric|min:0|max:100',
            'signatures.*.y_percent' => 'required|numeric|min:0|max:100',
            'signatures.*.w_percent' => 'required|numeric|min:0|max:100',
            'signatures.*.h_percent' => 'required|numeric|min:0|max:100',
        ]);

        try {
            // Remove old fields for this document
            $document->signatureFields()->delete();

            $validatedSignatures = [];
            foreach ($request->signatures as $signature) {
                // Sanitize to decimals (0-1)
                $sanitizedSignature = [
                    'page_number' => max(1, min(999, (int) $signature['page_number'])),
                    'x_percent' => max(0, min(100, (float) $signature['x_percent'])) / 100,
                    'y_percent' => max(0, min(100, (float) $signature['y_percent'])) / 100,
                    'width_percent' => max(0, min(100, (float) ($signature['w_percent'] ?? 0))) / 100,
                    'height_percent' => max(0, min(100, (float) ($signature['h_percent'] ?? 0))) / 100,
                ];
                $validatedSignatures[] = $sanitizedSignature;
            }

            // Create signature fields with validated data
            foreach ($validatedSignatures as $signature) {
                $document->signatureFields()->create([
                    'signer_id' => null, // Will be set when assigning a signer later
                    'page_number' => $signature['page_number'],
                    'x_percent' => $signature['x_percent'],
                    'y_percent' => $signature['y_percent'],
                    'width_percent' => $signature['width_percent'],
                    'height_percent' => $signature['height_percent'],
                ]);
            }

            \Log::info('Signature fields updated', [
                'document_id' => $document->id,
                'fields_count' => count($validatedSignatures),
                'user_id' => auth('admin')->id()
            ]);

            return redirect()->route('documents.index', ['id' => $document->id])->with('success', 'Signature locations added successfully!');
        } catch (\Exception $e) {
            return $this->handleError(
                $e,
                'signature_fields_update',
                'An error occurred while saving signature fields.',
                'back',
                ['document_id' => $documentId, 'fields_count' => count($validatedSignatures ?? [])]
            );
        }
    }

    public function sendSigningLink(Request $request, $id)
    {
        //dd($request->all());
        // Sanitize and validate document ID
        $documentId = (int) $id;
        if ($documentId <= 0) {
            return back()->withErrors(['error' => 'Invalid document ID.']);
        }
        $document = \App\Models\Document::findOrFail($documentId);

        // Enhanced validation for email and name
        /*$request->validate([
            'signer_email' => 'required|email|max:255|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'signer_name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s\-\'\.]+$/',
        ], [
            'signer_email.regex' => 'Please enter a valid email address.',
            'signer_name.regex' => 'Name can only contain letters, spaces, hyphens, apostrophes, and periods.',
            'signer_name.min' => 'Name must be at least 2 characters long.',
            'signer_name.max' => 'Name cannot be longer than 100 characters.',
        ]);*/

        $request->validate([
            'signer_email' => 'required|email',
            'signer_name' => 'required|string|min:2|max:100',
        ], [
            'signer_email.regex' => 'Please enter a valid email address.',
            'signer_name.min' => 'Name must be at least 2 characters long.',
            'signer_name.max' => 'Name cannot be longer than 100 characters.',
        ]);

        try {
            // Sanitize input data
            $signerEmail = strtolower(trim($request->signer_email));
            $signerName = trim($request->signer_name);
            //dd($signerEmail.' '. $signerName);
            // Additional email domain validation (optional - add your trusted domains)
            /*$allowedDomains = config('app.allowed_email_domains', []);
            if (!empty($allowedDomains)) {
                $emailDomain = substr($signerEmail, strpos($signerEmail, '@') + 1);
                if (!in_array($emailDomain, $allowedDomains)) {
                    return back()->withErrors(['signer_email' => 'Email domain not allowed.']);
                }
            }*/

            // Check for duplicate signer
            $existingSigner = $document->signers()->where('email', $signerEmail)->first();
            if ($existingSigner && $existingSigner->status === 'pending') {
                return back()->withErrors(['signer_email' => 'A signing link has already been sent to this email address.']);
            }

            if( isset($request->doc_type) && $request->doc_type == 'agreement')
            {
                $token = $request->pdf_sign_token;
                $isDocumentExistInSignerTbl = $document->signers()->where('document_id', $documentId )->first();
                if($isDocumentExistInSignerTbl)
                {
                    // Update existing document in signer table
                    $signer = $isDocumentExistInSignerTbl->update(['email' => $signerEmail,'name' => $signerName,'token' => $token,'status' => 'pending']);
                    //$signer = $document->signers()->where('token', $token)->first();
                }
                else
                {
                    // Insert document in signer table
                    $signer = $document->signers()->create([
                        'email' => $signerEmail,
                        'name' => $signerName,
                        'token' => $token,
                        'status' => 'pending'
                    ]);
                }
            }
            else
            {
                $token = Str::random(64); // Increased token length for better security
                $signer = $document->signers()->create([
                    'email' => $signerEmail,
                    'name' => $signerName,
                    'token' => $token,
                    'status' => 'pending',
                ]);
            }
            //dd($token);
            $document->status = 'sent';
            $document->save();

            // Define the signing URL before sending the email
            $signingUrl = url("/sign/{$document->id}/{$token}");

            try {
                if( isset($request->doc_type) && $request->doc_type == 'agreement')
                {
                    // Gather uploaded attachments
                    $attachments = [];
                    if ($request->hasFile('attach')) {
                        foreach ($request->file('attach') as $file) {
                            if ($file->isValid()) {
                                $attachments[] = [
                                    'path' => $file->getRealPath(),
                                    'name' => $file->getClientOriginalName(),
                                    'mime' => $file->getMimeType(),
                                ];
                            }
                        }
                    }

                    // Gather checklist files
                    $checklistFiles = [];
                    if ($request->has('checklistfile')) {
                        $checklistIds = $request->input('checklistfile');
                        $checklists = UploadChecklist::whereIn('id', $checklistIds)->get();
                        foreach ($checklists as $checklist) {
                            $filePath = public_path('checklists/' . $checklist->file);
                            if (file_exists($filePath)) {
                                $checklistFiles[] = $filePath;
                            }
                        }
                    }

                    $emailConfig = Email::where('email', $request->email_from)->firstOrFail(); //dd($emailConfig);
                    // Configure mail settings for this specific email
                    config([
                        'mail.mailers.smtp.host' => 'smtp.zoho.com',
                        'mail.mailers.smtp.port' => 587,
                        'mail.mailers.smtp.encryption' => 'tls',
                        'mail.mailers.smtp.username' => $emailConfig->email,
                        'mail.mailers.smtp.password' => $emailConfig->password,
                        'mail.from.address' => $request->email_from,
                        'mail.from.name' => $emailConfig->display_name,
                    ]);
                    //send agreement type document with attachments
                    $sendMail = Mail::send('emails.sign_agreement_document_email', ['signingUrl' => $signingUrl, 'firstName' => $signerName,'emailmessage' =>$request->message], function ($message) use ($signerEmail, $signerName, $attachments, $checklistFiles, $request, $emailConfig) {
                        $message->to($signerEmail, $signerName)
                        ->subject($request->subject)
                        ->from($request->email_from, $emailConfig->display_name);
                        // Attach uploaded files with original name and mime
                        foreach ($attachments as $file) {
                            $message->attach($file['path'], [
                                'as' => $file['name'],
                                'mime' => $file['mime'],
                            ]);
                        }
                        // Attach checklist files
                        foreach ($checklistFiles as $file) {
                            $message->attach($file);
                        }
                    });

                    if($sendMail){
                        //Save to mail reports table
                        $obj5 = new \App\Models\MailReport;
                        $obj5->user_id 		=  @Auth::guard('admin')->user()->id;
                        $obj5->from_mail 	=  $request->email_from;
                        $obj5->to_mail 		=  $document->client_id;
                        $obj5->template_id 	=  $request->template;
                        $obj5->subject		=  $request->subject;//'Bansal Migration Requesting To Sign Your Agreement Document';
                        $obj5->type 		=  'client';
                        $obj5->message		=  $request->message ?? '';
                        $obj5->mail_type    =  1;
                        $obj5->client_id	=  $document->client_id;
                        $obj5->client_matter_id	=  $document->client_matter_id;

                        $attachments = array();
                        if(isset($request->checklistfile)){
                            if(!empty($request->checklistfile)){
                                $checklistfiles = $request->checklistfile;
                                $attachments = array();
                                foreach($checklistfiles as $checklistfile){
                                    $filechecklist =  \App\Models\UploadChecklist::where('id', $checklistfile)->first();
                                    if($filechecklist){
                                        $attachments[] = array('file_name' => $filechecklist->name,'file_url' => $filechecklist->file);
                                    }
                                }
                                $obj5->attachments = json_encode($attachments);
                            }
                        }
                        // Validate required fields before saving
                        if (empty($obj5->from_mail) || empty($obj5->to_mail)) {
                            \Log::error('MailReport validation failed - missing required fields', [
                                'from_mail' => $obj5->from_mail,
                                'to_mail' => $obj5->to_mail
                            ]);
                        } else {
                            try {
                                $saved = $obj5->save();

                                // Log the save result for debugging
                                \Log::info('MailReport save attempt', [
                                    'saved' => $saved,
                                    'from_mail' => $obj5->from_mail,
                                    'to_mail' => $obj5->to_mail,
                                    'client_id' => $obj5->client_id,
                                    'client_matter_id' => $obj5->client_matter_id
                                ]);

                                if (!$saved) {
                                    \Log::error('Failed to save MailReport', [
                                        'data' => $obj5->toArray()
                                    ]);
                                }
                            } catch (\Exception $e) {
                                \Log::error('Exception while saving MailReport', [
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                    'data' => $obj5->toArray()
                                ]);
                            }
                        }
                    }
                }
                else
                {
                    //send visa and personal type document
                    Mail::send('emails.sign_document_email', ['signingUrl' => $signingUrl, 'firstName' => $signerName], function ($message) use ($signerEmail, $signerName) {
                        $message->to($signerEmail, $signerName)
                        ->subject('Bansal Migration Requesting To Sign Your Document')
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                    });
                }
            } catch (\Exception $e) {
                \Log::error('Mail sending failed: ' . $e->getMessage());
            }

            \Log::info('Signing link sent', [
                'document_id' => $document->id,
                'signer_email' => $signerEmail,
                'user_id' => auth('admin')->id()
            ]);

            return redirect()->route('documents.index', ['id' => $document->id])->with('success', 'Signing link sent successfully!');
        } catch (\Exception $e) {
            return $this->handleError(
                $e,
                'sending_signing_link',
                'An error occurred while sending the signing link.',
                'back',
                ['document_id' => $documentId, 'signer_email' => $signerEmail ?? 'unknown']
            );
        }
    }

    public function showSignForm($id)
    {
        try {
            $document = Document::findOrFail($id);
            $signer = $document->signers()->where('user_id', auth('admin')->id())->first();

            if (!$signer || $signer->status === 'signed') {
                return redirect('/')->with('error', 'Invalid or expired signing link.');
            }

            if (!$signer->opened_at) {
                $signer->update(['opened_at' => now()]);
            }

            $signatureFields = $document->signatureFields()->get();
            $pdfPath = $document->getFirstMediaPath('documents');

            if (!$pdfPath || !file_exists($pdfPath)) {
                \Log::error('PDF file not found for document: ' . $id);
                return redirect('/')->with('error', 'Document file not found.');
            }

            // Use the improved PDF page counting method
            $pdfPages = $this->countPdfPages($pdfPath);

            return view('Admin.documents.sign', compact('document', 'signer', 'signatureFields', 'pdfPages'));
        } catch (\Exception $e) {
            return $this->handleError(
                $e,
                'show_sign_form',
                'An error occurred while loading the document.',
                'dashboard',
                ['document_id' => $id]
            );
        }
    }

    public function getPage($id, $page)
    {
        try {
            $document = Document::findOrFail($id);
            //$pdfPath = $document->getFirstMediaPath('documents');
             $url = $document->myfile;
            $pdfPath = null;

            // Try to extract S3 key from URL if possible
            if ($url) {
                $parsed = parse_url($url);
                if (isset($parsed['path'])) {
                    $pdfPath = ltrim(urldecode($parsed['path']), '/');
                }
            }

            // If myfile_key is set, try to build S3 key from DB fields
            if (empty($pdfPath) && !empty($document->myfile_key) && !empty($document->doc_type) && !empty($document->client_id)) {
                $admin = \DB::table('admins')->select('client_id')->where('id', $document->client_id)->first();
                if ($admin && $admin->client_id) {
                    $pdfPath = $admin->client_id . '/' . $document->doc_type . '/' . $document->myfile_key;
                }
            }

            if (!$pdfPath || !\Storage::disk('s3')->exists($pdfPath)) {
                \Log::error('PDF file not found for document: ' . $id);
                abort(404, 'Document file not found');
            }


            // Download PDF from S3 to a temp file
            $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
            $pdfStream = \Storage::disk('s3')->get($pdfPath);
            file_put_contents($tmpPdfPath, $pdfStream);

            $imagePath = storage_path('app/public/page_' . $id . '_' . $page . '.jpg');

            try {
                $image = (new \Spatie\PdfToImage\Pdf($tmpPdfPath))
                    ->selectPage($page)
                    ->resolution(72)  // Set to 72 DPI to match PDF positioning standards (prevents scaling shifts)
                    ->save($imagePath);

                // Clean up temp file
                @unlink($tmpPdfPath);

                if (!file_exists($imagePath)) {
                    throw new \Exception('Failed to generate page image');
                }

                return response()->file($imagePath);
            } catch (\Exception $e) {
                @unlink($tmpPdfPath);
                \Log::error('Error generating PDF page image', [
                    'context' => 'pdf_page_generation',
                    'document_id' => $id,
                    'page' => $page,
                    'error' => $e->getMessage()
                ]);
                abort(500, 'Error generating page image');
            }
        } catch (\Exception $e) {
            \Log::error('Error in getPage', [
                'context' => 'get_page',
                'document_id' => $id,
                'page' => $page,
                'error' => $e->getMessage(),
                'user_id' => auth('admin')->id()
            ]);
            abort(500, 'An error occurred while retrieving the page');
        }
    }

    public function submitSignatures(Request $request, $id)
    {
        // Input validation - Critical security fix
        \Log::debug('submitSignatures: incoming request', [
            'request_all' => $request->all(),
            'signatures_type' => gettype($request->signatures),
            'signature_positions_type' => gettype($request->signature_positions)
        ]);
        $request->validate([
            'signer_id' => 'required|integer|exists:signers,id',
            'signatures' => 'required|array',
            'signatures.*' => 'nullable|string',
            'signature_positions' => 'required|array',
            'signature_positions.*' => 'nullable|string'
        ]);

        // Sanitize and validate the document ID
        $documentId = (int) $id;
        if ($documentId <= 0) {
            \Log::warning('Invalid document ID provided', ['id' => $id]);
            return redirect('/')->with('error', 'Invalid document ID.');
        }

        try {
            $document = Document::findOrFail($documentId);
            $signer = Signer::findOrFail($request->signer_id);

            // Verify signer belongs to this document
            if ($signer->document_id !== $document->id) {
                \Log::warning('Signer does not belong to document', [
                    'signer_id' => $signer->id,
                    'document_id' => $document->id,
                    'signer_document_id' => $signer->document_id
                ]);
                return redirect('documents.index', ['id' => $document->id])->with('error', 'Invalid signing attempt.');
            }

            \Log::info("Starting signature submission", [
                'document_id' => $id,
                'signer_id' => $signer->id,
                'signer_status' => $signer->status,
                'signer_token' => $signer->token
            ]);

            if ($signer->token !== null && $signer->status === 'pending') {
                // S3 path setup
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

                $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
                $pdfStream = \Storage::disk('s3')->get($s3Key);
                file_put_contents($tmpPdfPath, $pdfStream);
                $outputTmpPath = storage_path('app/tmp_' . uniqid() . '_signed.pdf');

                \Log::info("PDF paths", [
                    'input' => $tmpPdfPath,
                    'output' => $outputTmpPath
                ]);

                // Process signatures with their positions - Enhanced security
                $signaturesSaved = false;
                $signaturePositions = [];
                $signatureLinks = [];
                $maxSignatures = 50; // Limit number of signatures to prevent DoS
                $processedCount = 0;
                $errorMessages = [];

                \Log::debug('submitSignatures: signatures array', [
                    'signatures' => $request->signatures,
                    'signature_positions' => $request->signature_positions
                ]);

                foreach ($request->signatures as $page => $signaturesJson) {
                    // Validate page number
                    $pageNum = (int) $page;
                    if ($pageNum < 1 || $pageNum > 999) { // Reasonable page limit
                        \Log::warning('Invalid page number provided', ['page' => $page]);
                        $errorMessages[] = "Invalid page number: $page";
                        continue;
                    }

                    if ($signaturesJson && $processedCount < $maxSignatures) {
                        // Validate JSON and decode safely
                        if (!is_string($signaturesJson) || strlen($signaturesJson) > 100000) { // 100KB limit
                            \Log::warning('Invalid signatures JSON data', ['page' => $page]);
                            $errorMessages[] = "Signature data for page $page is too large or invalid.";
                            continue;
                        }

                        $signatures = json_decode($signaturesJson, true, 10); // Depth limit of 10
                        $positions = json_decode($request->signature_positions[$page] ?? '{}', true, 10);

                        \Log::debug('submitSignatures: decoded signatures and positions', [
                            'page' => $page,
                            'signatures' => $signatures,
                            'positions' => $positions,
                            'json_error' => json_last_error_msg()
                        ]);

                        // Validate JSON decode was successful
                        if (json_last_error() !== JSON_ERROR_NONE || !is_array($signatures)) {
                            \Log::warning('Failed to decode signatures JSON', [
                                'page' => $page,
                                'json_error' => json_last_error_msg()
                            ]);
                            $errorMessages[] = "Failed to decode signature data for page $page.";
                            continue;
                        }

                        if (!is_array($positions)) {
                            $positions = [];
                        }

                        foreach ($signatures as $fieldId => $signatureData) {
                            if ($processedCount >= $maxSignatures) {
                                break 2; // Break out of both loops
                            }

                            // Validate field ID
                            $sanitizedFieldId = (int) $fieldId;
                            if ($sanitizedFieldId <= 0) {
                                \Log::warning('Invalid field ID', ['fieldId' => $fieldId]);
                                $errorMessages[] = "Invalid field ID: $fieldId.";
                                continue;
                            }

                            // Use our enhanced signature data sanitization
                            $sanitizedSignature = $this->sanitizeSignatureData($signatureData, $sanitizedFieldId);
                            if ($sanitizedSignature === false) {
                                $errorMessages[] = "Signature for field $fieldId is invalid or corrupted. Please re-sign.";
                                \Log::warning('Signature sanitization failed', ['fieldId' => $fieldId, 'signatureData' => $signatureData]);
                                continue; // Sanitization failed, skip this signature
                            }

                            $imageData = $sanitizedSignature['imageData'];
                            $base64Data = $sanitizedSignature['base64Data'];

                            // S3 signature path
                            $filename = sprintf(
                                '%d_field_%d_%s.png',
                                $signer->id,
                                $sanitizedFieldId,
                                bin2hex(random_bytes(8)) // Add random component
                            );
                            $s3SignaturePath = $clientId . '/' . $docType . '/signatures/' . $filename;
                            // Upload to S3
                            \Storage::disk('s3')->put($s3SignaturePath, $imageData);
                            $s3SignatureUrl = \Storage::disk('s3')->url($s3SignaturePath);

                            // Use our enhanced position data sanitization
                            $position = $positions[$fieldId] ?? [];
                            $sanitizedPosition = $this->sanitizePositionData($position);

                            $signaturePositions[$sanitizedFieldId] = [
                                'path' => $s3SignaturePath, // S3 path for reference
                                'page' => $pageNum,
                                'x_percent' => $sanitizedPosition['x_percent'],
                                'y_percent' => $sanitizedPosition['y_percent'],
                                'w_percent' => $sanitizedPosition['w_percent'],
                                'h_percent' => $sanitizedPosition['h_percent']
                            ];
                            $signatureLinks[$sanitizedFieldId] = $s3SignatureUrl;

                            \Log::info("Saved signature to S3", [
                                'field_id' => $sanitizedFieldId,
                                's3_path' => $s3SignaturePath,
                                's3_url' => $s3SignatureUrl,
                                'size_bytes' => strlen($imageData),
                                'position' => $sanitizedPosition
                            ]);

                            $signaturesSaved = true;
                            $processedCount++;
                        }
                    }
                }

                if (!$signaturesSaved) {
                    $errorMsg = !empty($errorMessages) ? implode(' ', $errorMessages) : "No signatures provided. Please draw signatures before submitting.";
                    \Log::warning("No valid signatures provided", ['errorMessages' => $errorMessages]);
                    return redirect('/')->with('error', $errorMsg);
                }

                // Initialize FPDI with TCPDF
                $pdf = new \setasign\Fpdi\TcpdfFpdi('P', 'mm', 'A4', true, 'UTF-8', false);
                $pdf->SetAutoPageBreak(false);

                // Load source PDF
                if (!file_exists($tmpPdfPath)) {
                    \Log::error("Source PDF not found", ['path' => $tmpPdfPath]);
                    return redirect('/')->with('error', 'Source PDF not found.');
                }
                $pageCount = $pdf->setSourceFile($tmpPdfPath);
                \Log::info("PDF loaded", ['page_count' => $pageCount]);

                // Process each page (unchanged)
                for ($page = 1; $page <= $pageCount; $page++) {
                    \Log::info("Processing page {$page}");
                    // Import page
                    $tplIdx = $pdf->importPage($page);
                    $specs = $pdf->getTemplateSize($tplIdx);
                    \Log::info("Page {$page} dimensions", [
                        'width_mm' => $specs['width'],
                        'height_mm' => $specs['height'],
                        'orientation' => $specs['orientation']
                    ]);
                    // Add page with matching dimensions
                    $pdf->AddPage($specs['orientation'], [$specs['width'], $specs['height']]);
                    $pdf->useTemplate($tplIdx, 0, 0, $specs['width'], $specs['height']);
                    // Get signature fields for this page
                    $fields = $document->signatureFields()->where('page_number', $page)->get();
                    \Log::info("Found {$fields->count()} signature fields for page {$page}", [
                        'field_ids' => $fields->pluck('id')->toArray(),
                        'signaturePositions_keys' => array_keys($signaturePositions)
                    ]);
                    foreach ($fields as $field) {
                        if (isset($signaturePositions[$field->id])) {
                            $signatureInfo = $signaturePositions[$field->id];
                            $s3SignaturePath = $signatureInfo['path'];
                            $x_percent = $signatureInfo['x_percent'] ?? 0;
                            $y_percent = $signatureInfo['y_percent'] ?? 0;
                            $w_percent = $signatureInfo['w_percent'] ?? 0;
                            $h_percent = $signatureInfo['h_percent'] ?? 0;
                            $pdfWidth = $specs['width'];
                            $pdfHeight = $specs['height'];
                            $x_mm = $x_percent * $pdfWidth;
                            $w_mm = max(15, $w_percent * $pdfWidth);
                            $h_mm = max(15, $h_percent * $pdfHeight);
                            $y_mm = max(0, min($y_percent * $pdfHeight, $pdfHeight - $h_mm));
                            // Download signature from S3 to temp file for PDF overlay
                            $tmpSignaturePath = storage_path('app/tmp_signature_' . uniqid() . '.png');
                            $s3Image = \Storage::disk('s3')->get($s3SignaturePath);
                            file_put_contents($tmpSignaturePath, $s3Image);
                            if (file_exists($tmpSignaturePath)) {
                                $pdf->Image($tmpSignaturePath, $x_mm, $y_mm, $w_mm, $h_mm, 'PNG');
                                @unlink($tmpSignaturePath);
                            }
                        } else {
                            \Log::warning("No signature data for field", ['field_id' => $field->id]);
                        }
                    }
                }

                // Save the final PDF to a temp file
                try {
                    \Log::info("Saving PDF", ['path' => $outputTmpPath]);
                    $pdf->Output($outputTmpPath, 'F');
                    $pdfSaved = file_exists($outputTmpPath) ? filesize($outputTmpPath) : 0;
                    \Log::info("PDF saved", [
                        'path' => $outputTmpPath,
                        'size_bytes' => $pdfSaved
                    ]);
                    if (!$pdfSaved) {
                        return redirect('/')->with('error', 'Failed to save the signed PDF. Please contact support.');
                    }
                } catch (\Exception $e) {
                    \Log::error("Error saving PDF", ['error' => $e->getMessage()]);
                    return redirect('/')->with('error', 'Error saving signed PDF: ' . $e->getMessage());
                }

                // Upload signed PDF to S3
                $s3SignedPath = $clientId . '/' . $docType . '/signed/' . $document->id . '_signed.pdf';
                \Storage::disk('s3')->put($s3SignedPath, fopen($outputTmpPath, 'r'));
                $s3SignedUrl = \Storage::disk('s3')->url($s3SignedPath);

                // Clean up temp files
                @unlink($tmpPdfPath);
                @unlink($outputTmpPath);

                // Update statuses and document links
                $signer->update(['status' => 'signed', 'signed_at' => now()]);


                $document->status = 'signed';
                $document->signature_doc_link = json_encode($signatureLinks);
                $document->signed_doc_link = $s3SignedUrl ?? null;
                $docSigned = $document->save();

                if( $docSigned && $document->doc_type == 'agreement'){
                    $clientMatterInfo = \App\Models\ClientMatter::select('client_unique_matter_no','sel_person_responsible')->where('id', $document->client_matter_id)->first();
                    if(isset($clientMatterInfo->sel_person_responsible) && $clientMatterInfo->sel_person_responsible != ''){
                        $docSignerInfo = \App\Models\Admin::select('first_name','last_name','client_id')->where('id', $document->client_id)->first();
                        if( $docSignerInfo){
                            $docSignerFullName = $docSignerInfo->first_name.' '.$docSignerInfo->last_name;
                            $docSignerClientId = $docSignerInfo->client_id;
                        } else {
                            $docSignerFullName = 'NA';
                            $docSignerClientId = 'NA';
                        }
                        $clientMatterReference = $docSignerClientId.'-'.$clientMatterInfo->client_unique_matter_no;
                        $signedDocName = $document->file_name.'.'.$document->filetype;
                        $subject = $docSignerFullName.' signed cost agreement for matter ref no - '.$clientMatterReference.' at document '.$signedDocName;
                        $objs = new ActivitiesLog;
                        $objs->client_id = $document->client_id;
                        $objs->created_by = $clientMatterInfo->sel_person_responsible;
                        $objs->description = '';
                        $objs->subject = $subject;
                        $objsupd = $objs->save();
                        if($objsupd){
                            //update client matter table
                            \App\Models\ClientMatter::where('id', $document->client_matter_id)->update(['updated_at_type' => 'signed','updated_at' => now()]);
                        }
                    }
                }

                \Log::info("Document and signer status updated, S3 links saved", [
                    'signature_doc_link' => $signatureLinks,
                    'signed_doc_link' => $s3SignedUrl
                ]);

                // Redirect to thank you page with document ID
                return redirect()->route('documents.thankyou', ['id' => $document->id]);
            }

            \Log::warning("Invalid signing attempt", [
                'signer_status' => $signer->status,
                'has_token' => $signer->token !== null
            ]);
            return redirect('/')->with('error', 'Invalid signing attempt.');
        } catch (\Exception $e) {
            \Log::error("Unexpected error in submitSignatures", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/')->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function downloadSigned($id)
    {
        try {
            $document = Document::findOrFail($id);
            if ($document->signed_doc_link) {
                $signedDocUrl = $document->signed_doc_link;
                // Try to parse the S3 key from the URL
                $parsed = parse_url($signedDocUrl);
                if (isset($parsed['path'])) {
                    // Remove leading slash
                    $s3Key = ltrim($parsed['path'], '/');
                    $disk = \Storage::disk('s3');
                    if ($disk->exists($s3Key)) {
                        $tempUrl = $disk->temporaryUrl(
                            $s3Key,
                            now()->addMinutes(5),
                            [
                                'ResponseContentDisposition' => 'attachment; filename="' . $document->id . '_signed.pdf"',
                            ]
                        );
                        return redirect($tempUrl);
                    }
                }
                // Fallback: direct redirect if S3 key not found or file missing
                return redirect($signedDocUrl);
            }
            return redirect()->back()->with('error', 'Signed document not found.');
        } catch (\Exception $e) {
            return $this->handleError(
                $e,
                'download_signed_document',
                'An error occurred while downloading the document.',
                'back',
                ['document_id' => $id]
            );
        }
    }

    public function downloadSignedAndThankyou($id)
    {
        try {
            $document = \App\Models\Document::findOrFail($id);
            if ($document->signed_doc_link) {
                $signedDocUrl = $document->signed_doc_link;
                // Try to parse the S3 key from the URL
                $parsed = parse_url($signedDocUrl);
                if (isset($parsed['path'])) {
                    // Remove leading slash
                    $s3Key = ltrim($parsed['path'], '/');
                    $disk = \Storage::disk('s3');
                    if ($disk->exists($s3Key)) {
                        $tempUrl = $disk->temporaryUrl(
                            $s3Key,
                            now()->addMinutes(5),
                            [
                                'ResponseContentDisposition' => 'attachment; filename="' . $document->id . '_signed.pdf"',
                            ]
                        );
                        // Show a view that triggers the download and then redirects to thank you
                        return view('Admin.documents.download_and_thankyou', [
                            'downloadUrl' => $tempUrl,
                            'thankyouUrl' => route('documents.thankyou')
                        ]);
                    }
                }
                // Fallback: direct download if S3 key not found or file missing
                return view('Admin.documents.download_and_thankyou', [
                    'downloadUrl' => $signedDocUrl,
                    'thankyouUrl' => route('documents.thankyou')
                ]);
            }
            return redirect()->back()->with('error', 'Signed document not found.');
        } catch (\Exception $e) {
            return $this->handleError(
                $e,
                'download_signed_document_and_thankyou',
                'An error occurred while downloading the document.',
                'back',
                ['document_id' => $id]
            );
        }
    }

    public function debugSignaturePad()
    {
        return view('debug-signature');
    }

    public function thankyou(Request $request, $id = null)
    {
        $downloadUrl = null;
        if ($id) {
            $document = \App\Models\Document::find($id);
            if ($document && $document->signed_doc_link) {
                $parsed = parse_url($document->signed_doc_link);
                if (isset($parsed['path'])) {
                    $s3Key = ltrim($parsed['path'], '/');
                    $disk = \Storage::disk('s3');
                    if ($disk->exists($s3Key)) {
                        $downloadUrl = $disk->temporaryUrl(
                            $s3Key,
                            now()->addMinutes(5),
                            [
                                'ResponseContentDisposition' => 'attachment; filename="' . $document->id . '_signed.pdf"',
                            ]
                        );
                    }
                }
            }
        }
        $message = 'You have successfully signed your document.';
        return view('thanks', compact('downloadUrl', 'message','id'));
    }

    public function sendReminder(Request $request, $id)
    {
        // Sanitize and validate input
        $documentId = (int) $id;
        if ($documentId <= 0) {
            return redirect()->back()->with('error', 'Invalid document ID.');
        }

        // Validate signer_id input
        $request->validate([
            'signer_id' => 'required|integer|exists:signers,id'
        ]);

        $signerId = (int) $request->signer_id;
        if ($signerId <= 0) {
            return redirect()->back()->with('error', 'Invalid signer ID.');
        }

        try {
            $document = \App\Models\Document::findOrFail($documentId); //dd($document);
            $signer = $document->signers()->findOrFail($signerId);

            // Verify signer belongs to this document
            if ($signer->document_id !== $document->id) {
                \Log::warning('Attempt to send reminder for mismatched signer', [
                    'document_id' => $document->id,
                    'signer_id' => $signer->id,
                    'signer_document_id' => $signer->document_id
                ]);
                return redirect()->back()->with('error', 'Invalid signer for this document.');
            }

        if ($signer->status === 'signed') {
            return redirect()->back()->with('error', 'Document is already signed.');
        }

        // Check if we can send a reminder (limit to 3 reminders, 24 hours apart)
        if ($signer->reminder_count >= 3) {
            return redirect()->back()->with('error', 'Maximum reminders already sent.');
        }

        if ($signer->last_reminder_sent_at && $signer->last_reminder_sent_at->diffInHours(now()) < 24) {
            return redirect()->back()->with('error', 'Please wait 24 hours between reminders.');
        }

        // Send reminder email
        $signingUrl = url("/sign/{$document->id}/{$signer->token}");
        \Mail::raw("This is a reminder to sign your document: " . $signingUrl, function ($message) use ($signer) {
            $message->to($signer->email, $signer->name)
                    ->subject('Reminder: Please Sign Your Document');
        });

        // Update reminder tracking
        $signer->update([
            'last_reminder_sent_at' => now(),
            'reminder_count' => $signer->reminder_count + 1
        ]);

        return redirect()->back()->with('success', 'Reminder sent successfully!');
        } catch (\Exception $e) {
            return $this->handleError(
                $e,
                'send_reminder',
                'An error occurred while sending the reminder.',
                'back',
                ['document_id' => $documentId, 'signer_id' => $signerId ?? 'unknown']
            );
        }
    }

    /**
     * Show the sign form for a document using a tokenized link.
     */
    public function sign($id, $token)
    {
        // Sanitize and validate inputs
        $documentId = (int) $id;
        if ($documentId <= 0) {
            \Log::warning('Invalid document ID in sign method', ['id' => $id]);
            return redirect()->route('documents.index')->with('error', 'Invalid document link.');
        }

        // Validate token format
        if (!$token || !is_string($token) || strlen($token) < 32 || !preg_match('/^[a-zA-Z0-9]+$/', $token)) {
            \Log::warning('Invalid token format in sign method', ['token_length' => strlen($token ?? '')]);
            return redirect()->route('documents.index', ['id' => $documentId])->with('error', 'Invalid or expired signing link.');
        }

        try {
            $document = Document::findOrFail($documentId);  //dd($document);
            if( isset($document->doc_type) && $document->doc_type == 'agreement')
            {
                $isDocumentExistInSignerTbl = $document->signers()->where('document_id', $documentId )->first(); //dd($isDocumentExistInSignerTbl);
                if($isDocumentExistInSignerTbl)
                {
                    // Update existing document in signer table
                    $isDocumentExistInSignerTbl->update(['token' => $token,'status' => 'pending']);
                    $signer = $document->signers()->where('token', $token)->first();
                }
                else
                {
                    // Insert document in signer table
                    $signer = $document->signers()->create(['token' => $token,'status' => 'pending']);
                }
            } else {
                $signer = $document->signers()->where('token', $token)->first();
            }
            //dd($signer);

            if (!$signer || $signer->status === 'signed') {
                \Log::warning('Invalid signer or already signed', [
                    'document_id' => $documentId,
                    'signer_exists' => !is_null($signer),
                    'signer_status' => $signer ? $signer->status : 'none'
                ]);
                return redirect()->route('documents.index', ['id' => $documentId])->with('error', 'Invalid or expired signing link.');
            }

            // Check token expiration (optional - add expiration logic)
            // if ($signer->created_at->addHours(72)->isPast()) {
            //     return redirect('/')->with('error', 'Signing link has expired.');
            // }

            if (!$signer->opened_at) {
                $signer->update(['opened_at' => now()]);
            }

            $signatureFields = $document->signatureFields()->get();
            //$pdfPath = $document->getFirstMediaPath('documents');
            // Use the same S3 logic as in edit/getPage for PDF path
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

            // Use the improved PDF page counting method
            //$pdfPages = $this->countPdfPages($pdfPath);
            $pdfPages = 1;
            if ($pdfPath && \Storage::disk('s3')->exists($pdfPath)) {
                $tmpPdfPath = storage_path('app/tmp_' . uniqid() . '.pdf');
                $pdfStream = \Storage::disk('s3')->get($pdfPath);
                file_put_contents($tmpPdfPath, $pdfStream);
                $pdfPages = $this->countPdfPages($tmpPdfPath) ?: 1;
                @unlink($tmpPdfPath);
            }

            return view('documents.sign', compact('document', 'signer', 'signatureFields', 'pdfPages'));
        } catch (\Exception $e) {
            return $this->handleError(
                $e,
                'document_sign',
                'An error occurred while loading the signing page.',
                'documents.index',
                ['document_id' => $documentId, 'token_present' => !empty($token)]
            );
        }
    }
}
