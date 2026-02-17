<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\ClientMatter;
use App\Models\ClientTrReference;
use App\Traits\ClientAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class TrSheetController extends Controller
{
    use ClientAuthorization;

    public const TABS = ['ongoing', 'lodged', 'checklist', 'discontinue'];

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display the TR Sheet with 4 tabs.
     */
    public function index(Request $request, $tab = null)
    {
        if (!$this->hasModuleAccess('20')) {
            abort(403, 'Unauthorized');
        }

        $tab = $tab ?? $request->input('tab', 'ongoing');
        if (!in_array($tab, self::TABS, true)) {
            $tab = 'ongoing';
        }

        $setupRequired = !Schema::hasTable('client_tr_references') || !Schema::hasColumn('client_matters', 'tr_checklist_status') || !Schema::hasTable('tr_matter_reminders');

        $config = self::getTabConfig($tab);
        $sessionKey = $config['session_key'];

        if ($request->has('clear_filters')) {
            session()->forget($sessionKey);
            return redirect()->route('clients.sheets.tr', ['tab' => $tab]);
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
            $rows = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, 1, ['path' => route('clients.sheets.tr'), 'pageName' => 'page']);
            $rows->appends(array_merge($request->except('page'), ['tab' => $tab]));
        } else {
            $query = $this->buildBaseQuery($request, $tab);
            $query = $this->applyFilters($query, $request);
            $query = $this->applySorting($query, $request, $tab);

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
        $currentStages = $this->getCurrentStagesForTab($tab);
        $activeFilterCount = $this->countActiveFilters($request);

        return view('crm.clients.sheets.tr', compact(
            'rows',
            'tab',
            'perPage',
            'activeFilterCount',
            'branches',
            'assignees',
            'currentStages',
            'config',
            'setupRequired'
        ));
    }

    public static function getTabConfig(string $tab): array
    {
        $configs = [
            'ongoing'    => ['title' => 'Ongoing', 'session_key' => 'tr_sheet_ongoing_filters'],
            'lodged'     => ['title' => 'Lodged', 'session_key' => 'tr_sheet_lodged_filters'],
            'checklist'  => ['title' => 'Checklist', 'session_key' => 'tr_sheet_checklist_filters'],
            'discontinue'=> ['title' => 'Discontinue', 'session_key' => 'tr_sheet_discontinue_filters'],
        ];
        return $configs[$tab] ?? $configs['ongoing'];
    }

    protected function getFiltersFromSession(Request $request, string $sessionKey): array
    {
        $filterParams = ['branch', 'assignee', 'current_stage', 'visa_expiry_from', 'visa_expiry_to', 'search', 'per_page'];
        $hasAnyParam = false;
        foreach ($filterParams as $key) {
            if ($request->has($key) && $request->input($key) !== null && $request->input($key) !== '') {
                $hasAnyParam = true;
                break;
            }
        }
        if ($hasAnyParam) {
            return [];
        }
        return session($sessionKey, []);
    }

    protected function persistFiltersToSession(Request $request, string $sessionKey): void
    {
        $payload = [
            'branch' => $request->input('branch'),
            'assignee' => $request->input('assignee'),
            'current_stage' => $request->input('current_stage'),
            'visa_expiry_from' => $request->input('visa_expiry_from'),
            'visa_expiry_to' => $request->input('visa_expiry_to'),
            'search' => $request->input('search'),
            'per_page' => $request->input('per_page'),
        ];
        $payload = array_filter($payload, fn ($v) => $v !== null && $v !== '');
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

    protected function getCurrentStagesForTab(string $tab)
    {
        $key = match ($tab) {
            'lodged' => 'sheets.tr.lodged_stages',
            'discontinue' => 'sheets.tr.discontinue_stages',
            'checklist' => 'sheets.tr.checklist_early_stages',
            default => 'sheets.tr.ongoing_stages',
        };
        $stages = config($key, []);
        if (!is_array($stages)) {
            $stages = [];
        }
        return collect($stages)->filter(fn ($s) => $s !== null && trim((string) $s) !== '')
            ->values()->mapWithKeys(fn ($s) => [trim((string) $s) => trim((string) $s)]);
    }

    protected function buildBaseQuery(Request $request, string $tab)
    {
        $driver = DB::connection()->getDriverName();
        $trCondition = $this->getTrMatterCondition();

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
                cm.tr_checklist_status,
                m.title as matter_title
            FROM client_matters cm
            INNER JOIN matters m ON m.id = cm.sel_matter_id
            WHERE {$trCondition}
            ORDER BY cm.client_id, cm.id DESC
        ";

        if ($driver === 'mysql') {
            $latestSql = "
                SELECT cm.id AS matter_id, cm.client_id, cm.client_unique_matter_no,
                       cm.other_reference, cm.department_reference, cm.sel_migration_agent,
                       cm.sel_person_responsible, cm.sel_person_assisting,
                       cm.office_id, cm.workflow_stage_id, cm.matter_status, cm.tr_checklist_status,
                       m.title as matter_title
                FROM client_matters cm
                INNER JOIN matters m ON m.id = cm.sel_matter_id
                INNER JOIN (
                    SELECT client_id, MAX(id) AS max_id FROM client_matters cm2
                    INNER JOIN matters m2 ON m2.id = cm2.sel_matter_id
                    WHERE {$trCondition}
                    GROUP BY client_id
                ) latest ON latest.client_id = cm.client_id AND latest.max_id = cm.id
                WHERE {$trCondition}
            ";
        }

        $query = DB::table(DB::raw('(' . $latestSql . ') AS latest_tr'))
            ->leftJoin('client_tr_references as tr_ref', function ($join) {
                $join->on('tr_ref.client_id', '=', 'latest_tr.client_id')
                    ->on('tr_ref.client_matter_id', '=', 'latest_tr.matter_id');
            })
            ->leftJoin('workflow_stages as ws', 'latest_tr.workflow_stage_id', '=', 'ws.id')
            ->join('admins', 'latest_tr.client_id', '=', 'admins.id')
            ->leftJoin('staff as agent', 'latest_tr.sel_migration_agent', '=', 'agent.id')
            ->leftJoin('branches', 'latest_tr.office_id', '=', 'branches.id')
            ->select(
                'latest_tr.matter_id as matter_internal_id',
                'latest_tr.client_id',
                'admins.client_id as crm_ref',
                'admins.first_name',
                'admins.last_name',
                'admins.dob',
                DB::raw('admins."visaExpiry" as visa_expiry'),
                'latest_tr.client_unique_matter_no',
                'latest_tr.matter_title',
                'latest_tr.other_reference',
                'latest_tr.department_reference',
                'latest_tr.office_id',
                'latest_tr.sel_migration_agent as assignee_id',
                DB::raw("CONCAT(COALESCE(agent.first_name, ''), ' ', COALESCE(agent.last_name, '')) as assignee_name"),
                'branches.office_name as branch_name',
                'ws.name as application_stage',
                'tr_ref.current_status',
                'tr_ref.payment_display_note',
                'tr_ref.institute_override',
                'tr_ref.comments as sheet_comment_text',
                'tr_ref.checklist_sent_at',
                'tr_ref.is_pinned',
                'latest_tr.tr_checklist_status'
            )
            ->where('admins.is_archived', 0)
            ->where('admins.role', 7)
            ->whereNull('admins.is_deleted');

        $this->applyTabFilter($query, $tab);

        if ($tab === 'checklist' && Schema::hasTable('tr_matter_reminders')) {
            $query->addSelect(
                DB::raw("(SELECT MAX(ar.reminded_at) FROM tr_matter_reminders ar WHERE ar.client_matter_id = latest_tr.matter_id AND ar.type = 'email') as email_reminder_latest"),
                DB::raw("(SELECT COUNT(*) FROM tr_matter_reminders ar WHERE ar.client_matter_id = latest_tr.matter_id AND ar.type = 'email') as email_reminder_count"),
                DB::raw("(SELECT MAX(ar.reminded_at) FROM tr_matter_reminders ar WHERE ar.client_matter_id = latest_tr.matter_id AND ar.type = 'sms') as sms_reminder_latest"),
                DB::raw("(SELECT COUNT(*) FROM tr_matter_reminders ar WHERE ar.client_matter_id = latest_tr.matter_id AND ar.type = 'sms') as sms_reminder_count"),
                DB::raw("(SELECT MAX(ar.reminded_at) FROM tr_matter_reminders ar WHERE ar.client_matter_id = latest_tr.matter_id AND ar.type = 'phone') as phone_reminder_latest"),
                DB::raw("(SELECT COUNT(*) FROM tr_matter_reminders ar WHERE ar.client_matter_id = latest_tr.matter_id AND ar.type = 'phone') as phone_reminder_count")
            );
        }

        return $query;
    }

    protected function getTrMatterCondition(): string
    {
        $nickNames = config('sheets.tr.matter_nick_names', ['tr', 'tr checklist']);
        $patterns = config('sheets.tr.matter_title_patterns', ['tr', 'tr checklist', 'temporary residence']);
        $cond = [];
        foreach ($nickNames as $n) {
            $cond[] = "LOWER(COALESCE(m.nick_name, '')) = '" . addslashes(strtolower($n)) . "'";
        }
        foreach ($patterns as $p) {
            $cond[] = "LOWER(COALESCE(m.title, '')) LIKE '%" . addslashes(strtolower($p)) . "%'";
        }
        return '(' . implode(' OR ', $cond) . ')';
    }

    protected function applyTabFilter($query, string $tab): void
    {
        $ongoingStages = array_map('strtolower', config('sheets.tr.ongoing_stages', []));
        $lodgedStages = array_map('strtolower', config('sheets.tr.lodged_stages', []));
        $checklistStages = array_map('strtolower', config('sheets.tr.checklist_early_stages', []));
        $discontinueStages = array_map('strtolower', config('sheets.tr.discontinue_stages', []));
        $excludedForOngoing = array_merge($lodgedStages, $checklistStages, $discontinueStages);

        if ($tab === 'discontinue') {
            $query->where(function ($q) use ($discontinueStages) {
                $q->whereRaw('latest_tr.matter_status = 0');
                if (!empty($discontinueStages)) {
                    $ph = implode(',', array_fill(0, count($discontinueStages), '?'));
                    $q->orWhereRaw('LOWER(TRIM(ws.name)) IN (' . $ph . ')', $discontinueStages);
                }
            });
        } elseif ($tab === 'lodged') {
            $query->whereRaw('latest_tr.matter_status = 1');
            if (!empty($lodgedStages)) {
                $ph = implode(',', array_fill(0, count($lodgedStages), '?'));
                $query->whereRaw('LOWER(TRIM(ws.name)) IN (' . $ph . ')', $lodgedStages);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($tab === 'checklist') {
            $query->whereRaw('latest_tr.matter_status = 1');
            if (!empty($checklistStages)) {
                $ph = implode(',', array_fill(0, count($checklistStages), '?'));
                $query->whereRaw('LOWER(TRIM(ws.name)) IN (' . $ph . ')', $checklistStages);
            } else {
                $query->whereRaw('1 = 0');
            }
            $query->where(function ($q) {
                $q->whereNull('latest_tr.tr_checklist_status')
                    ->orWhereIn('latest_tr.tr_checklist_status', ['active', 'hold']);
            });
        } else {
            $query->whereRaw('latest_tr.matter_status = 1');
            if (!empty($excludedForOngoing)) {
                $ph = implode(',', array_fill(0, count($excludedForOngoing), '?'));
                $query->whereRaw('(LOWER(TRIM(ws.name)) NOT IN (' . $ph . ') OR ws.name IS NULL)', $excludedForOngoing);
            }
        }
    }

    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('branch')) {
            $branchIds = is_array($request->input('branch')) ? $request->input('branch') : [$request->input('branch')];
            $query->whereIn('latest_tr.office_id', $branchIds);
        }
        if ($request->filled('assignee') && $request->input('assignee') !== 'all') {
            $assigneeId = $request->input('assignee') === 'me' ? Auth::id() : $request->input('assignee');
            if ($assigneeId) {
                $query->where(function ($q) use ($assigneeId) {
                    $q->where('latest_tr.sel_migration_agent', $assigneeId)
                        ->orWhere('latest_tr.sel_person_responsible', $assigneeId)
                        ->orWhere('latest_tr.sel_person_assisting', $assigneeId);
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
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(admins.first_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(admins.last_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(admins.client_id) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(tr_ref.current_status) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(ws.name) LIKE ?', [$search]);
            });
        }
        return $query;
    }

    protected function applySorting($query, Request $request, string $tab)
    {
        // First priority: pinned items (is_pinned DESC) - pinned items on top
        // Use CASE to convert boolean to integer for PostgreSQL compatibility
        $query->orderByRaw("CASE WHEN tr_ref.is_pinned = true THEN 1 ELSE 0 END DESC");
        
        // Second priority: checklist hold status (only for checklist tab)
        if ($tab === 'checklist') {
            $query->orderByRaw("CASE WHEN COALESCE(latest_tr.tr_checklist_status, 'active') = 'hold' THEN 1 ELSE 0 END ASC");
        }
        
        // Third priority: visa expiry (ASC) - closest expiry dates first
        // NULL expiry dates go to the end
        // Use quoted "visaExpiry" for PostgreSQL (case-sensitive column)
        $query->orderByRaw("CASE WHEN admins.\"visaExpiry\" IS NULL OR admins.\"visaExpiry\"::text = '0000-00-00' THEN 1 ELSE 0 END ASC");
        $query->orderByRaw('admins."visaExpiry" ASC');
        
        // Fourth priority: custom user sort if provided
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
        $query->orderBy('latest_tr.matter_id', 'asc');
        
        return $query;
    }

    protected function countActiveFilters(Request $request)
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

    protected function calculatePaymentsForMatter($clientId, $matterInternalId)
    {
        if (!$clientId || !$matterInternalId) {
            return ['total' => '0.00', 'pending' => '0.00'];
        }
        $total = (float) DB::table('account_all_invoice_receipts')
            ->where('client_id', $clientId)
            ->where('client_matter_id', $matterInternalId)
            ->where(function ($q) {
                $q->whereNull('invoice_status')->orWhere('invoice_status', '!=', 2);
            })
            ->sum(DB::raw('COALESCE(withdraw_amount, 0)'));
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
     * Toggle pin status for a matter in the TR sheet.
     */
    public function togglePin(Request $request)
    {
        $clientId = $request->input('client_id');
        $matterInternalId = $request->input('matter_internal_id');

        if (!$clientId || !$matterInternalId) {
            return response()->json(['success' => false, 'message' => 'Missing required parameters'], 400);
        }

        if (!Schema::hasTable('client_tr_references')) {
            return response()->json(['success' => false, 'message' => 'Reference table not found'], 404);
        }

        try {
            $reference = DB::table('client_tr_references')
                ->where('client_id', $clientId)
                ->where('client_matter_id', $matterInternalId)
                ->first();

            if ($reference) {
                // Toggle existing pin
                $newPinStatus = !($reference->is_pinned ?? false);
                DB::table('client_tr_references')
                    ->where('client_id', $clientId)
                    ->where('client_matter_id', $matterInternalId)
                    ->update([
                        'is_pinned' => $newPinStatus,
                        'updated_by' => Auth::id(),
                        'updated_at' => now(),
                    ]);
            } else {
                // Create new reference record with pin
                DB::table('client_tr_references')->insert([
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
