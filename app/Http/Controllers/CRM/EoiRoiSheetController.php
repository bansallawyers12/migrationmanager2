<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ClientEoiReference;
use App\Models\ActivitiesLog;
use App\Services\PointsService;
use App\Traits\ClientAuthorization;
use App\Mail\EoiConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EoiRoiSheetController extends Controller
{
    use ClientAuthorization;

    protected PointsService $pointsService;

    public function __construct(PointsService $pointsService)
    {
        $this->middleware('auth:admin');
        $this->pointsService = $pointsService;
    }

    /**
     * Display the EOI/ROI Sheet - List view
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Check authorization
        if (!$this->hasModuleAccess('20')) {
            abort(403, 'Unauthorized');
        }

        $perPage = (int) $request->get('per_page', 50);
        $allowedPerPage = [10, 25, 50, 100, 200];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 50;
        }

        // Build base query
        $query = $this->buildBaseQuery($request);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Apply sorting
        $query = $this->applySorting($query, $request);

        // Paginate
        $rows = $query->paginate($perPage)->appends($request->except('page'));

        // Calculate partner points and warnings (for Comments column) for each row
        $warningsCache = [];
        $rows->getCollection()->transform(function ($row) use (&$warningsCache) {
            $row->partner_points = $this->calculatePartnerPoints($row->client_id);

            // Warnings (English expiry, age bracket, employment) for Comments column – cache by client + subclass
            $subclass = $this->getFirstSubclassFromRow($row);
            $cacheKey = $row->client_id . '_' . ($subclass ?? '');
            if (!isset($warningsCache[$cacheKey])) {
                $warningsCache[$cacheKey] = $this->getWarningsTextForClient($row->client_id, $subclass);
            }
            $row->warnings_text = $warningsCache[$cacheKey];

            return $row;
        });

        // Count active filters
        $activeFilterCount = $this->countActiveFilters($request);

        return view('crm.clients.sheets.eoi-roi', compact('rows', 'perPage', 'activeFilterCount'));
    }

    /**
     * Display the EOI/ROI Sheet - Insights view
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function insights(Request $request)
    {
        // Check authorization
        if (!$this->hasModuleAccess('20')) {
            abort(403, 'Unauthorized');
        }

        // Build base query (without pagination)
        $baseQuery = $this->buildBaseQuery($request);
        
        // Apply same filters as list view
        $baseQuery = $this->applyFilters($baseQuery, $request);

        // Get all matching records for insights
        $allRecords = $baseQuery->get();

        // Calculate insights metrics
        $insights = $this->calculateInsights($allRecords);

        // Count active filters
        $activeFilterCount = $this->countActiveFilters($request);

        return view('crm.clients.sheets.eoi-roi-insights', compact('insights', 'activeFilterCount'));
    }

    /**
     * Build the base query for EOI/ROI sheet
     * Uses a standalone subquery for "latest EOI matter per client" so we never reference outer tables inside the subquery (PostgreSQL scope).
     *
     * @param Request $request
     * @return \Illuminate\Database\Query\Builder
     */
    protected function buildBaseQuery(Request $request)
    {
        // Standalone subquery: one row per client_id = latest EOI matter (DISTINCT ON in PostgreSQL)
        $latestEoiMatterSql = "
            SELECT DISTINCT ON (cm.client_id)
                cm.client_id,
                cm.client_unique_matter_no,
                cm.id AS matter_id
            FROM client_matters cm
            INNER JOIN matters m ON m.id = cm.sel_matter_id
            WHERE cm.matter_status = 1
              AND (
                  LOWER(m.nick_name) = 'eoi'
                  OR LOWER(m.title) LIKE '%eoi%'
                  OR LOWER(m.title) LIKE '%expression of interest%'
                  OR LOWER(m.title) LIKE '%expression%'
              )
            ORDER BY cm.client_id, cm.id DESC
        ";

        $query = DB::table('client_eoi_references as eoi')
            ->join('admins', 'eoi.client_id', '=', 'admins.id')
            ->join(DB::raw('(' . $latestEoiMatterSql . ') AS latest_eoi_matter'), 'latest_eoi_matter.client_id', '=', 'admins.id')
            ->select(
                'eoi.id as eoi_id',
                DB::raw('eoi."EOI_number" as "EOI_number"'),
                DB::raw('eoi."EOI_occupation" as "EOI_occupation"'),
                DB::raw('eoi."EOI_point" as individual_points'),
                DB::raw('eoi."EOI_submission_date" as "EOI_submission_date"'),
                DB::raw('eoi."EOI_ROI" as "EOI_ROI"'),
                'eoi.eoi_status',
                'eoi.eoi_subclasses',
                'eoi.eoi_states',
                DB::raw('eoi."EOI_state" as "EOI_state"'),
                'eoi.client_id',
                'admins.first_name',
                'admins.last_name',
                'admins.marital_status',
                'latest_eoi_matter.client_unique_matter_no as matter_id',
                'latest_eoi_matter.matter_id as matter_internal_id'
            )
            ->where('admins.is_archived', 0)
            ->where('admins.role', 7)
            ->whereNull('admins.is_deleted')
            ->whereNotNull('latest_eoi_matter.matter_id');

        return $query;
    }

    /**
     * Apply filters to the query
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param Request $request
     * @return \Illuminate\Database\Query\Builder
     */
    protected function applyFilters($query, Request $request)
    {
        // EOI Status filter
        if ($request->filled('eoi_status')) {
            $query->where('eoi.eoi_status', $request->input('eoi_status'));
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $fromDate = Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->startOfDay();
            $query->whereRaw('eoi."EOI_submission_date" >= ?', [$fromDate]);
        }

        if ($request->filled('to_date')) {
            $toDate = Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->endOfDay();
            $query->whereRaw('eoi."EOI_submission_date" <= ?', [$toDate]);
        }

        // Subclass filter (JSON array contains)
        if ($request->filled('subclass')) {
            $subclasses = is_array($request->input('subclass')) ? $request->input('subclass') : [$request->input('subclass')];
            $query->where(function ($q) use ($subclasses) {
                foreach ($subclasses as $subclass) {
                    // PostgreSQL JSON contains
                    $q->orWhereRaw("eoi.eoi_subclasses::jsonb @> ?", [json_encode([$subclass])]);
                }
            });
        }

        // State filter (JSON array contains)
        if ($request->filled('state')) {
            $states = is_array($request->input('state')) ? $request->input('state') : [$request->input('state')];
            $query->where(function ($q) use ($states) {
                foreach ($states as $state) {
                    // PostgreSQL JSON contains
                    $q->orWhereRaw("eoi.eoi_states::jsonb @> ?", [json_encode([$state])]);
                }
            });
        }

        // Search filter (client name or EOI number)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(admins.first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(admins.last_name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(eoi."EOI_number") LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        // Occupation filter (nominated occupation – partial match on EOI_occupation)
        if ($request->filled('occupation')) {
            $occupation = $request->input('occupation');
            $query->whereRaw('LOWER(eoi."EOI_occupation") LIKE ?', ['%' . strtolower($occupation) . '%']);
        }

        return $query;
    }

    /**
     * Apply sorting to the query
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param Request $request
     * @return \Illuminate\Database\Query\Builder
     */
    protected function applySorting($query, Request $request)
    {
        $sortField = $request->get('sort', 'submission_date');
        $sortDirection = $request->get('direction', 'desc');

        // Validate direction
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        $dir = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        // Map sort fields to actual columns (use raw SQL for eoi mixed-case columns so PostgreSQL preserves case)
        $sortableFieldsRaw = [
            'eoi_number' => 'eoi."EOI_number"',
            'client_name' => 'admins.first_name',
            'occupation' => 'eoi."EOI_occupation"',
            'individual_points' => 'eoi."EOI_point"',
            'marital_status' => 'admins.marital_status',
            'eoi_status' => 'eoi.eoi_status',
            'submission_date' => 'eoi."EOI_submission_date"',
        ];

        $actualSortField = $sortableFieldsRaw[$sortField] ?? 'eoi."EOI_submission_date"';

        $query->orderByRaw($actualSortField . ' ' . $dir);

        return $query;
    }

    /**
     * Calculate partner points for a client (Single = 10, partner citizen/PR = 10, partner skills = 10, partner English = 5, else 0).
     *
     * @param int $clientId
     * @return int|null
     */
    protected function calculatePartnerPoints($clientId)
    {
        try {
            $client = Admin::find($clientId);
            if (!$client) {
                return null;
            }
            return $this->pointsService->getPartnerPoints($client);
        } catch (\Exception $e) {
            Log::error('Error calculating partner points', [
                'client_id' => $clientId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Calculate insights metrics from records
     * 
     * @param \Illuminate\Support\Collection $records
     * @return array
     */
    protected function calculateInsights($records)
    {
        $insights = [
            'total_records' => $records->count(),
            'by_status' => [],
            'by_subclass' => [],
            'by_state' => [],
            'avg_individual_points' => 0,
            'recent_submissions_7d' => 0,
            'recent_submissions_30d' => 0,
            'submissions_by_month' => [],
        ];

        if ($records->isEmpty()) {
            return $insights;
        }

        // By status
        $insights['by_status'] = $records->groupBy('eoi_status')
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();

        // By subclass (unnest JSON arrays)
        $subclassCounts = [];
        foreach ($records as $record) {
            $subclasses = json_decode($record->eoi_subclasses, true) ?? [];
            foreach ($subclasses as $subclass) {
                $subclassCounts[$subclass] = ($subclassCounts[$subclass] ?? 0) + 1;
            }
        }
        $insights['by_subclass'] = $subclassCounts;

        // By state (unnest JSON arrays)
        $stateCounts = [];
        foreach ($records as $record) {
            $states = json_decode($record->eoi_states, true) ?? [];
            foreach ($states as $state) {
                $stateCounts[$state] = ($stateCounts[$state] ?? 0) + 1;
            }
        }
        $insights['by_state'] = $stateCounts;

        // Average individual points (cast to float in case DB returns strings)
        $pointsSum = $records->sum(function ($record) {
            $v = $record->individual_points ?? 0;
            return is_numeric($v) ? (float) $v : 0;
        });
        $pointsCount = $records->filter(function ($record) {
            return $record->individual_points !== null && $record->individual_points !== '' && is_numeric($record->individual_points);
        })->count();
        $insights['avg_individual_points'] = $pointsCount > 0 ? round($pointsSum / $pointsCount, 1) : 0;

        // Recent submissions
        $now = Carbon::now();
        $insights['recent_submissions_7d'] = $records->filter(function ($record) use ($now) {
            return $record->EOI_submission_date && Carbon::parse($record->EOI_submission_date)->greaterThanOrEqualTo($now->copy()->subDays(7));
        })->count();

        $insights['recent_submissions_30d'] = $records->filter(function ($record) use ($now) {
            return $record->EOI_submission_date && Carbon::parse($record->EOI_submission_date)->greaterThanOrEqualTo($now->copy()->subDays(30));
        })->count();

        // Submissions by month (last 6 months)
        $monthlySubmissions = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $monthLabel = $month->format('M Y');
            
            $count = $records->filter(function ($record) use ($month) {
                return $record->EOI_submission_date && 
                       Carbon::parse($record->EOI_submission_date)->format('Y-m') === $month->format('Y-m');
            })->count();
            
            $monthlySubmissions[$monthLabel] = $count;
        }
        $insights['submissions_by_month'] = $monthlySubmissions;

        return $insights;
    }

    /**
     * Count active filters
     * 
     * @param Request $request
     * @return int
     */
    protected function countActiveFilters(Request $request)
    {
        $filters = ['eoi_status', 'from_date', 'to_date', 'subclass', 'state', 'search', 'occupation'];
        $count = 0;
        
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Get first subclass from EOI row (for points calculation context)
     */
    protected function getFirstSubclassFromRow($row): ?string
    {
        $raw = $row->eoi_subclasses ?? null;
        if ($raw === null) {
            return null;
        }
        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
        if (!is_array($decoded) || empty($decoded)) {
            return null;
        }
        return $decoded[0];
    }

    /**
     * Get formatted warnings text for a client (English expiry, age bracket, employment) for Comments column
     */
    protected function getWarningsTextForClient(int $clientId, ?string $subclass): string
    {
        try {
            $client = Admin::with(['testScores', 'qualifications', 'experiences', 'partner', 'occupations'])
                ->find($clientId);
            if (!$client) {
                return '';
            }
            $result = $this->pointsService->compute($client, $subclass, 6);
            $warnings = $result['warnings'] ?? [];
            return $this->formatWarningsForDisplay($warnings);
        } catch (\Throwable $e) {
            Log::warning('EoiRoiSheet: failed to get warnings for client', [
                'client_id' => $clientId,
                'error' => $e->getMessage(),
            ]);
            return '';
        }
    }

    /**
     * Format warnings array as a single line for display in Comments column
     */
    protected function formatWarningsForDisplay(array $warnings): string
    {
        if (empty($warnings)) {
            return '';
        }
        $messages = array_map(function ($w) {
            return $w['message'] ?? '';
        }, $warnings);
        return implode(' | ', array_filter($messages));
    }

    /**
     * Staff verifies EOI details
     * 
     * @param Request $request
     * @param int $eoiId
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyByStaff(Request $request, $eoiId)
    {
        // Check authorization
        if (!$this->hasModuleAccess('20')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $eoi = ClientEoiReference::findOrFail($eoiId);
            
            // Update verification fields
            $eoi->staff_verified = true;
            $eoi->confirmation_date = Carbon::now();
            $eoi->checked_by = auth()->guard('admin')->id();
            $eoi->save();

            // Log activity
            $this->logActivity(
                $eoi->client_id,
                'EOI Verified by Staff',
                'EOI details verified by ' . auth()->guard('admin')->user()->first_name . ' ' . auth()->guard('admin')->user()->last_name . 
                ' for EOI #' . $eoi->EOI_number,
                'eoi_verification'
            );

            return response()->json([
                'success' => true,
                'message' => 'EOI details verified successfully. You can now send confirmation email to the client.',
                'confirmation_date' => $eoi->confirmation_date->format('d/m/Y H:i'),
                'checked_by' => auth()->guard('admin')->user()->first_name . ' ' . auth()->guard('admin')->user()->last_name
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying EOI', [
                'eoi_id' => $eoiId,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Error verifying EOI details'], 500);
        }
    }

    /**
     * Send confirmation email to client
     * 
     * @param Request $request
     * @param int $eoiId
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendConfirmationEmail(Request $request, $eoiId)
    {
        // Check authorization
        if (!$this->hasModuleAccess('20')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $eoi = ClientEoiReference::with('client')->findOrFail($eoiId);
            
            // Check if staff has verified first
            if (!$eoi->staff_verified) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Please verify the EOI details first before sending to client.'
                ], 400);
            }

            // Check if client exists and has email
            if (!$eoi->client || !$eoi->client->email) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Client email not found. Please update client email first.'
                ], 400);
            }

            // Generate unique token for confirmation
            $token = Str::random(64);
            $eoi->client_confirmation_token = $token;
            $eoi->confirmation_email_sent_at = Carbon::now();
            $eoi->client_confirmation_status = 'pending';
            $eoi->save();

            // Send email
            Mail::to($eoi->client->email)->send(new EoiConfirmationMail($eoi, $eoi->client, $token));

            // Log activity
            $this->logActivity(
                $eoi->client_id,
                'EOI Confirmation Email Sent',
                'Confirmation email sent to ' . $eoi->client->email . ' for EOI #' . $eoi->EOI_number,
                'email'
            );

            return response()->json([
                'success' => true,
                'message' => 'Confirmation email sent successfully to ' . $eoi->client->email,
                'sent_at' => $eoi->confirmation_email_sent_at->format('d/m/Y H:i')
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending confirmation email', [
                'eoi_id' => $eoiId,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Error sending confirmation email'], 500);
        }
    }

    /**
     * Client confirms EOI details (public route)
     * 
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function showConfirmationPage($token)
    {
        $eoi = ClientEoiReference::with('client')->where('client_confirmation_token', $token)->firstOrFail();
        
        return view('crm.clients.sheets.eoi-client-confirmation', [
            'eoi' => $eoi,
            'token' => $token,
            'action' => 'confirm'
        ]);
    }

    /**
     * Client requests amendment (public route)
     * 
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function showAmendmentPage($token)
    {
        $eoi = ClientEoiReference::with('client')->where('client_confirmation_token', $token)->firstOrFail();
        
        return view('crm.clients.sheets.eoi-client-confirmation', [
            'eoi' => $eoi,
            'token' => $token,
            'action' => 'amend'
        ]);
    }

    /**
     * Process client confirmation
     * 
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processClientConfirmation(Request $request, $token)
    {
        try {
            $eoi = ClientEoiReference::with('client')->where('client_confirmation_token', $token)->firstOrFail();
            
            $action = $request->input('action');
            
            if ($action === 'confirm') {
                // Client confirms details
                $eoi->client_confirmation_status = 'confirmed';
                $eoi->client_last_confirmation = Carbon::now();
                $eoi->save();

                // Log activity
                $this->logActivity(
                    $eoi->client_id,
                    'EOI Details Confirmed by Client',
                    'Client confirmed EOI details for EOI #' . $eoi->EOI_number,
                    'eoi_confirmation'
                );

                return redirect()->route('client.eoi.success', ['token' => $token])
                    ->with('success', 'Thank you! Your EOI details have been confirmed.');

            } elseif ($action === 'amend') {
                // Client requests amendments
                $request->validate([
                    'notes' => 'required|string|max:1000'
                ]);

                $eoi->client_confirmation_status = 'amendment_requested';
                $eoi->client_confirmation_notes = $request->input('notes');
                $eoi->client_last_confirmation = Carbon::now();
                $eoi->save();

                // Log activity
                $this->logActivity(
                    $eoi->client_id,
                    'EOI Amendment Requested by Client',
                    'Client requested amendments for EOI #' . $eoi->EOI_number . '. Notes: ' . $request->input('notes'),
                    'eoi_amendment'
                );

                return redirect()->route('client.eoi.success', ['token' => $token])
                    ->with('success', 'Thank you! Your amendment request has been submitted.');
            }

            return redirect()->back()->with('error', 'Invalid action');

        } catch (\Exception $e) {
            Log::error('Error processing client confirmation', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    /**
     * Show success page after client confirmation
     * 
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function showSuccessPage($token)
    {
        $eoi = ClientEoiReference::with('client')->where('client_confirmation_token', $token)->firstOrFail();
        
        return view('crm.clients.sheets.eoi-confirmation-success', ['eoi' => $eoi]);
    }

    /**
     * Log activity to activities_logs table
     * 
     * @param int $clientId
     * @param string $subject
     * @param string $description
     * @param string $activityType
     * @return void
     */
    protected function logActivity($clientId, $subject, $description, $activityType = 'note')
    {
        try {
            ActivitiesLog::create([
                'client_id' => $clientId,
                'created_by' => auth()->guard('admin')->check() ? auth()->guard('admin')->id() : null,
                'subject' => $subject,
                'description' => $description,
                'activity_type' => $activityType,
                'use_for' => 'client'
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging activity', [
                'client_id' => $clientId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
