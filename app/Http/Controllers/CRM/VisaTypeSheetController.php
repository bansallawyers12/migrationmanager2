<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Traits\ClientAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

/**
 * Generic controller for visa-type sheets (TR, Visitor, Student, PR, etc.).
 * Config-driven via config/sheets/visa_types.php.
 */
class VisaTypeSheetController extends Controller
{
    use ClientAuthorization;

    public const TABS = ['ongoing', 'lodged', 'checklist', 'discontinue'];

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display the visa type sheet with 4 tabs.
     */
    public function index(Request $request, string $visaType, $tab = null)
    {
        $config = $this->getVisaTypeConfig($visaType);
        if (!$config) {
            abort(404, "Visa type sheet '{$visaType}' not configured.");
        }

        if (!$this->hasModuleAccess('20')) {
            abort(403, 'Unauthorized');
        }

        $tab = $tab ?? $request->input('tab', 'ongoing');
        if (!in_array($tab, self::TABS, true)) {
            $tab = 'ongoing';
        }

        $tabConfig = $this->getTabConfig($visaType, $tab);
        $sessionKey = $tabConfig['session_key'];

        $setupRequired = $this->isSetupRequired($config);

        if ($request->has('clear_filters')) {
            session()->forget($sessionKey);
            return redirect()->route($config['route'], ['visaType' => $visaType, 'tab' => $tab]);
        }

        $request->merge($this->getFiltersFromSession($request, $sessionKey));

        if (!$request->has('assignee') || $request->input('assignee') === '') {
            $request->merge(['assignee' => 'me']);
        }

        $perPage = (int) $request->get('per_page', 50);
        $allowedPerPage = [10, 25, 50, 100, 200];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 50;
        }

        $this->persistFiltersToSession($request, $sessionKey);

        if ($setupRequired) {
            $rows = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, 1, [
                'path' => route($config['route'], ['visaType' => $visaType]),
                'pageName' => 'page',
            ]);
            $rows->appends(array_merge($request->except('page'), ['tab' => $tab]));
        } else {
            $query = $this->buildBaseQuery($request, $tab, $config);
            $query = $this->applyFilters($query, $request, $config);
            $query = $this->applySorting($query, $request, $tab, $config);

            $rows = $query->paginate($perPage)->appends(array_merge($request->except('page'), ['tab' => $tab]));

            $rows->getCollection()->transform(function ($row) {
                $payments = $this->calculatePaymentsForMatter($row->client_id, $row->matter_internal_id);
                $row->total_payment = $payments['total'];
                $row->pending_payment = $payments['pending'];
                return $row;
            });
        }

        $branches = Branch::orderBy('office_name')->get(['id', 'office_name']);
        $assignees = $this->getAssignees();
        $currentStages = $this->getCurrentStagesForTab($tab, $config);
        $activeFilterCount = $this->countActiveFilters($request);

        return view('crm.clients.sheets.visa-type-sheet', compact(
            'rows',
            'tab',
            'perPage',
            'activeFilterCount',
            'branches',
            'assignees',
            'currentStages',
            'config',
            'tabConfig',
            'visaType',
            'setupRequired'
        ));
    }

    protected function getVisaTypeConfig(string $visaType): ?array
    {
        $configs = config('sheets.visa_types', []);
        return $configs[$visaType] ?? null;
    }

    protected function isSetupRequired(array $config): bool
    {
        $refTable = $config['reference_table'] ?? '';
        $remindersTable = $config['reminders_table'] ?? '';
        $checklistCol = $config['checklist_status_column'] ?? '';

        return !Schema::hasTable($refTable)
            || !Schema::hasColumn('client_matters', $checklistCol)
            || !Schema::hasTable($remindersTable);
    }

    protected function getTabConfig(string $visaType, string $tab): array
    {
        $config = $this->getVisaTypeConfig($visaType);
        $prefix = $config['session_prefix'] ?? $visaType . '_sheet_';

        $titles = [
            'ongoing' => 'Ongoing',
            'lodged' => 'Lodged',
            'checklist' => 'Checklist',
            'discontinue' => 'Discontinue',
        ];

        return [
            'title' => $titles[$tab] ?? 'Ongoing',
            'session_key' => $prefix . $tab . '_filters',
        ];
    }

    protected function getFiltersFromSession(Request $request, string $sessionKey): array
    {
        $filterParams = ['branch', 'assignee', 'current_stage', 'visa_expiry_from', 'visa_expiry_to', 'search', 'per_page'];
        foreach ($filterParams as $key) {
            if ($request->has($key) && $request->input($key) !== null && $request->input($key) !== '') {
                return [];
            }
        }
        return session($sessionKey, []);
    }

    protected function persistFiltersToSession(Request $request, string $sessionKey): void
    {
        $payload = array_filter([
            'branch' => $request->input('branch'),
            'assignee' => $request->input('assignee'),
            'current_stage' => $request->input('current_stage'),
            'visa_expiry_from' => $request->input('visa_expiry_from'),
            'visa_expiry_to' => $request->input('visa_expiry_to'),
            'search' => $request->input('search'),
            'per_page' => $request->input('per_page'),
        ], fn ($v) => $v !== null && $v !== '');
        session()->put($sessionKey, $payload);
    }

    protected function getAssignees()
    {
        $allIds = DB::table('client_matters')
            ->select('sel_migration_agent')
            ->whereNotNull('sel_migration_agent')
            ->distinct()
            ->pluck('sel_migration_agent')
            ->merge(
                DB::table('client_matters')->select('sel_person_responsible')->whereNotNull('sel_person_responsible')->distinct()->pluck('sel_person_responsible')
            )
            ->merge(
                DB::table('client_matters')->select('sel_person_assisting')->whereNotNull('sel_person_assisting')->distinct()->pluck('sel_person_assisting')
            )
            ->unique()
            ->filter()
            ->values();
        $assignees = \App\Models\Staff::where('status', 1)
            ->whereIn('id', $allIds)
            ->orderBy('first_name')->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);
        $currentUser = Auth::user();
        if ($currentUser && $assignees->pluck('id')->doesntContain($currentUser->id)) {
            $assignees->push($currentUser);
            $assignees = $assignees->sortBy(fn ($a) => trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? '')))->values();
        }
        return $assignees;
    }

    protected function getCurrentStagesForTab(string $tab, array $config)
    {
        $key = match ($tab) {
            'lodged' => 'lodged_stages',
            'discontinue' => 'discontinue_stages',
            'checklist' => 'checklist_early_stages',
            default => 'ongoing_stages',
        };
        $stages = $config[$key] ?? [];
        if (!is_array($stages)) {
            $stages = [];
        }
        return collect($stages)->filter(fn ($s) => $s !== null && trim((string) $s) !== '')
            ->values()->mapWithKeys(fn ($s) => [trim((string) $s) => trim((string) $s)]);
    }

    protected function getMatterCondition(array $config): string
    {
        $nickNames = $config['matter_nick_names'] ?? [];
        $patterns = $config['matter_title_patterns'] ?? [];
        $cond = [];
        foreach ($nickNames as $n) {
            $cond[] = "LOWER(COALESCE(m.nick_name, '')) = '" . addslashes(strtolower($n)) . "'";
        }
        foreach ($patterns as $p) {
            $cond[] = "LOWER(COALESCE(m.title, '')) LIKE '%" . addslashes(strtolower($p)) . "%'";
        }
        return $cond ? '(' . implode(' OR ', $cond) . ')' : '1 = 0';
    }

    protected function buildBaseQuery(Request $request, string $tab, array $config)
    {
        $driver = DB::connection()->getDriverName();
        $matterCondition = $this->getMatterCondition($config);
        $refTable = $config['reference_table'];
        $refAlias = $config['reference_alias'];
        $checklistCol = $config['checklist_status_column'];

        $latestSql = "
            SELECT DISTINCT ON (cm.client_id)
                cm.id AS matter_id,
                cm.client_id,
                cm.client_unique_matter_no,
                cm.other_reference,
                cm.department_reference,
                cm.sel_migration_agent,
                cm.sel_person_responsible,
                cm.sel_person_assisting,
                cm.office_id,
                cm.workflow_stage_id,
                cm.matter_status,
                cm.deadline,
                cm.{$checklistCol} as checklist_status,
                m.title as matter_title
            FROM client_matters cm
            INNER JOIN matters m ON m.id = cm.sel_matter_id
            WHERE {$matterCondition}
            ORDER BY cm.client_id, cm.id DESC
        ";

        if ($driver === 'mysql') {
            $latestSql = "
                SELECT cm.id AS matter_id, cm.client_id, cm.client_unique_matter_no,
                       cm.other_reference, cm.department_reference, cm.sel_migration_agent,
                       cm.sel_person_responsible, cm.sel_person_assisting,
                       cm.office_id, cm.workflow_stage_id, cm.matter_status, cm.deadline,
                       cm.{$checklistCol} as checklist_status, m.title as matter_title
                FROM client_matters cm
                INNER JOIN matters m ON m.id = cm.sel_matter_id
                INNER JOIN (
                    SELECT client_id, MAX(id) AS max_id FROM client_matters cm2
                    INNER JOIN matters m2 ON m2.id = cm2.sel_matter_id
                    WHERE {$matterCondition}
                    GROUP BY client_id
                ) latest ON latest.client_id = cm.client_id AND latest.max_id = cm.id
                WHERE {$matterCondition}
            ";
        }

        $query = DB::table(DB::raw('(' . $latestSql . ') AS latest_matter'))
            ->leftJoin("{$refTable} as {$refAlias}", function ($join) use ($refAlias) {
                $join->on("{$refAlias}.client_id", '=', 'latest_matter.client_id')
                    ->on("{$refAlias}.client_matter_id", '=', 'latest_matter.matter_id');
            })
            ->leftJoin('workflow_stages as ws', 'latest_matter.workflow_stage_id', '=', 'ws.id')
            ->join('admins', 'latest_matter.client_id', '=', 'admins.id')
            ->leftJoin('staff as agent', 'latest_matter.sel_migration_agent', '=', 'agent.id')
            ->leftJoin('branches', 'latest_matter.office_id', '=', 'branches.id')
            ->select(
                'latest_matter.matter_id as matter_internal_id',
                'latest_matter.client_id',
                'admins.client_id as crm_ref',
                'admins.first_name',
                'admins.last_name',
                'admins.dob',
                DB::raw('admins."visaExpiry" as visa_expiry'),
                'latest_matter.client_unique_matter_no',
                'latest_matter.matter_title',
                'latest_matter.deadline',
                'latest_matter.other_reference',
                'latest_matter.department_reference',
                'latest_matter.office_id',
                'latest_matter.sel_migration_agent as assignee_id',
                DB::raw("CONCAT(COALESCE(agent.first_name, ''), ' ', COALESCE(agent.last_name, '')) as assignee_name"),
                'branches.office_name as branch_name',
                'ws.name as application_stage',
                "{$refAlias}.current_status",
                "{$refAlias}.payment_display_note",
                "{$refAlias}.institute_override",
                "{$refAlias}.comments as sheet_comment_text",
                "{$refAlias}.checklist_sent_at",
                "{$refAlias}.is_pinned",
                'latest_matter.checklist_status as tr_checklist_status'
            )
            ->where('admins.is_archived', 0)
            ->where('admins.role', 7)
            ->whereNull('admins.is_deleted');

        $this->applyTabFilter($query, $tab, $config);

        $remindersTable = $config['reminders_table'] ?? '';
        if ($tab === 'checklist' && $remindersTable && Schema::hasTable($remindersTable)) {
            $query->addSelect(
                DB::raw("(SELECT MAX(ar.reminded_at) FROM {$remindersTable} ar WHERE ar.client_matter_id = latest_matter.matter_id AND ar.type = 'email') as email_reminder_latest"),
                DB::raw("(SELECT COUNT(*) FROM {$remindersTable} ar WHERE ar.client_matter_id = latest_matter.matter_id AND ar.type = 'email') as email_reminder_count"),
                DB::raw("(SELECT MAX(ar.reminded_at) FROM {$remindersTable} ar WHERE ar.client_matter_id = latest_matter.matter_id AND ar.type = 'sms') as sms_reminder_latest"),
                DB::raw("(SELECT COUNT(*) FROM {$remindersTable} ar WHERE ar.client_matter_id = latest_matter.matter_id AND ar.type = 'sms') as sms_reminder_count"),
                DB::raw("(SELECT MAX(ar.reminded_at) FROM {$remindersTable} ar WHERE ar.client_matter_id = latest_matter.matter_id AND ar.type = 'phone') as phone_reminder_latest"),
                DB::raw("(SELECT COUNT(*) FROM {$remindersTable} ar WHERE ar.client_matter_id = latest_matter.matter_id AND ar.type = 'phone') as phone_reminder_count")
            );
        }

        return $query;
    }

    protected function applyTabFilter($query, string $tab, array $config): void
    {
        $ongoingStages = array_map('strtolower', $config['ongoing_stages'] ?? []);
        $lodgedStages = array_map('strtolower', $config['lodged_stages'] ?? []);
        $checklistStages = array_map('strtolower', $config['checklist_early_stages'] ?? []);
        $discontinueStages = array_map('strtolower', $config['discontinue_stages'] ?? []);
        $excludedForOngoing = array_merge($lodgedStages, $checklistStages, $discontinueStages);

        if ($tab === 'discontinue') {
            $query->where(function ($q) use ($discontinueStages) {
                $q->whereRaw('latest_matter.matter_status = 0');
                if (!empty($discontinueStages)) {
                    $ph = implode(',', array_fill(0, count($discontinueStages), '?'));
                    $q->orWhereRaw('LOWER(TRIM(ws.name)) IN (' . $ph . ')', $discontinueStages);
                }
            });
        } elseif ($tab === 'lodged') {
            $query->whereRaw('latest_matter.matter_status = 1');
            if (!empty($lodgedStages)) {
                $ph = implode(',', array_fill(0, count($lodgedStages), '?'));
                $query->whereRaw('LOWER(TRIM(ws.name)) IN (' . $ph . ')', $lodgedStages);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($tab === 'checklist') {
            $query->whereRaw('latest_matter.matter_status = 1');
            if (!empty($checklistStages)) {
                $ph = implode(',', array_fill(0, count($checklistStages), '?'));
                $query->whereRaw('LOWER(TRIM(ws.name)) IN (' . $ph . ')', $checklistStages);
            } else {
                $query->whereRaw('1 = 0');
            }
            $query->where(function ($q) {
                $q->whereNull('latest_matter.checklist_status')
                    ->orWhereIn('latest_matter.checklist_status', ['active', 'hold']);
            });
        } else {
            $query->whereRaw('latest_matter.matter_status = 1');
            if (!empty($excludedForOngoing)) {
                $ph = implode(',', array_fill(0, count($excludedForOngoing), '?'));
                $query->whereRaw('(LOWER(TRIM(ws.name)) NOT IN (' . $ph . ') OR ws.name IS NULL)', $excludedForOngoing);
            }
        }
    }

    protected function applyFilters($query, Request $request, array $config)
    {
        $refAlias = $config['reference_alias'] ?? 'ref';

        if ($request->filled('branch')) {
            $branchIds = is_array($request->input('branch')) ? $request->input('branch') : [$request->input('branch')];
            $query->whereIn('latest_matter.office_id', $branchIds);
        }
        if ($request->filled('assignee') && $request->input('assignee') !== 'all') {
            $assigneeId = $request->input('assignee') === 'me' ? Auth::id() : $request->input('assignee');
            if ($assigneeId) {
                $query->where(function ($q) use ($assigneeId) {
                    $q->where('latest_matter.sel_migration_agent', $assigneeId)
                        ->orWhere('latest_matter.sel_person_responsible', $assigneeId)
                        ->orWhere('latest_matter.sel_person_assisting', $assigneeId);
                });
            }
        }
        if ($request->filled('current_stage')) {
            $query->where('ws.name', $request->input('current_stage'));
        }
        if ($request->filled('visa_expiry_from')) {
            try {
                $from = Carbon::createFromFormat('d/m/Y', $request->input('visa_expiry_from'))->startOfDay();
                $query->whereRaw('admins."visaExpiry" >= ?', [$from]);
            } catch (\Exception $e) {}
        }
        if ($request->filled('visa_expiry_to')) {
            try {
                $to = Carbon::createFromFormat('d/m/Y', $request->input('visa_expiry_to'))->endOfDay();
                $query->whereRaw('admins."visaExpiry" <= ?', [$to]);
            } catch (\Exception $e) {}
        }
        if ($request->filled('search')) {
            $search = '%' . strtolower($request->input('search')) . '%';
            $query->where(function ($q) use ($search, $refAlias) {
                $q->whereRaw('LOWER(admins.first_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(admins.last_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(admins.client_id) LIKE ?', [$search])
                    ->orWhereRaw("LOWER({$refAlias}.current_status) LIKE ?", [$search])
                    ->orWhereRaw('LOWER(ws.name) LIKE ?', [$search]);
            });
        }
        return $query;
    }

    protected function applySorting($query, Request $request, string $tab, array $config)
    {
        $refAlias = $config['reference_alias'] ?? 'ref';
        
        // First priority: pinned items (is_pinned DESC) - pinned items on top
        // Use CASE to convert boolean to integer for PostgreSQL compatibility
        $query->orderByRaw("CASE WHEN {$refAlias}.is_pinned = true THEN 1 ELSE 0 END DESC");
        
        // Second priority: checklist hold status (only for checklist tab)
        if ($tab === 'checklist') {
            $query->orderByRaw("CASE WHEN COALESCE(latest_matter.checklist_status, 'active') = 'hold' THEN 1 ELSE 0 END ASC");
        }
        
        // Third priority: deadline (ASC) - nearest deadline first, nulls last
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            $query->orderByRaw('latest_matter.deadline IS NULL ASC, latest_matter.deadline ASC');
        } else {
            $query->orderByRaw('latest_matter.deadline ASC NULLS LAST');
        }
        
        // Fourth priority: visa expiry (ASC) - closest expiry dates first
        // NULL expiry dates go to the end
        // Use quoted "visaExpiry" for PostgreSQL (case-sensitive column)
        $query->orderByRaw("CASE WHEN admins.\"visaExpiry\" IS NULL OR admins.\"visaExpiry\"::text = '0000-00-00' THEN 1 ELSE 0 END ASC");
        $query->orderByRaw('admins."visaExpiry" ASC');
        
        // Fifth priority: custom user sort if provided
        $sortField = $request->get('sort');
        $sortDirection = $request->get('direction', 'asc');
        if ($sortField && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortable = [
                'crm_ref' => 'admins.client_id',
                'name' => 'admins.first_name',
                'dob' => 'admins.dob',
                'stage' => 'ws.name',
            ];
            if (isset($sortable[$sortField])) {
                $query->orderBy($sortable[$sortField], $sortDirection);
            }
        }
        
        // Final tiebreaker: matter ID
        $query->orderBy('latest_matter.matter_id', 'asc');
        
        return $query;
    }

    protected function countActiveFilters(Request $request): int
    {
        $count = 0;
        if ($request->filled('branch')) $count++;
        if ($request->filled('assignee') && $request->input('assignee') !== 'all') $count++;
        if ($request->filled('current_stage')) $count++;
        if ($request->filled('visa_expiry_from')) $count++;
        if ($request->filled('visa_expiry_to')) $count++;
        if ($request->filled('search')) $count++;
        return $count;
    }

    protected function calculatePaymentsForMatter($clientId, $matterInternalId): array
    {
        if (!$clientId || !$matterInternalId) {
            return ['total' => '0.00', 'pending' => '0.00'];
        }
        // Payment received = Client Fund Ledger (Deposits only) + Office Receipts
        // Show total for client (sheet displays one row per client with their matter)
        $total = (float) DB::table('account_client_receipts')
            ->where('client_id', $clientId)
            ->where(function ($q) {
                $q->where(function ($q1) {
                    // Client fund: only Deposits (exclude Fee Transfers which have deposit_amount=0)
                    $q1->where('receipt_type', 1)
                        ->where(function ($q2) {
                            $q2->where('client_fund_ledger_type', 'Deposit')
                                ->orWhereNull('client_fund_ledger_type');
                        });
                })->orWhere(function ($q2) {
                    $q2->where('receipt_type', 2)->where('save_type', 'final'); // Office receipts (finalized only)
                });
            })
            ->where(function ($q) {
                $q->whereNull('void_fee_transfer')->orWhere('void_fee_transfer', '!=', 1);
            })
            ->where(function ($q) {
                $q->whereNull('void_invoice')->orWhere('void_invoice', '!=', 1);
            })
            ->sum(DB::raw('COALESCE(deposit_amount, 0)'));
        $pending = (float) DB::table('account_client_receipts')
            ->where('client_id', $clientId)
            ->where('receipt_type', 3)
            ->where(function ($q) {
                $q->whereNull('void_fee_transfer')->orWhere('void_fee_transfer', '!=', 1);
            })
            ->sum(DB::raw('COALESCE(balance_amount, 0)'));
        return ['total' => number_format($total, 2), 'pending' => number_format($pending, 2)];
    }

    /**
     * Toggle pin status for a matter in the sheet.
     */
    public function togglePin(Request $request, string $visaType)
    {
        $config = $this->getVisaTypeConfig($visaType);
        if (!$config) {
            return response()->json(['success' => false, 'message' => 'Invalid visa type'], 404);
        }

        $clientId = $request->input('client_id');
        $matterInternalId = $request->input('matter_internal_id');

        if (!$clientId || !$matterInternalId) {
            return response()->json(['success' => false, 'message' => 'Missing required parameters'], 400);
        }

        $refTable = $config['reference_table'];
        
        if (!Schema::hasTable($refTable)) {
            return response()->json(['success' => false, 'message' => 'Reference table not found'], 404);
        }

        try {
            $reference = DB::table($refTable)
                ->where('client_id', $clientId)
                ->where('client_matter_id', $matterInternalId)
                ->first();

            if ($reference) {
                // Toggle existing pin
                $newPinStatus = !($reference->is_pinned ?? false);
                DB::table($refTable)
                    ->where('client_id', $clientId)
                    ->where('client_matter_id', $matterInternalId)
                    ->update([
                        'is_pinned' => $newPinStatus,
                        'updated_by' => Auth::id(),
                        'updated_at' => now(),
                    ]);
            } else {
                // Create new reference record with pin
                DB::table($refTable)->insert([
                    'client_id' => $clientId,
                    'client_matter_id' => $matterInternalId,
                    'is_pinned' => true,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $newPinStatus = true;
            }

            return response()->json([
                'success' => true,
                'is_pinned' => $newPinStatus,
                'message' => $newPinStatus ? 'Item pinned to top' : 'Item unpinned'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating pin status: ' . $e->getMessage()], 500);
        }
    }
}
