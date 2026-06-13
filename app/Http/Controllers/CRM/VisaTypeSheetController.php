<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ClientMatter;
use App\Support\StaffClientVisibility;
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

        if (! $this->hasModuleAccess('20') || ! $this->canAccessCrmSheet($visaType)) {
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

        // Default to 'all' so Ongoing and other tabs show all matters when no filter is set.
        // Previously defaulted to 'me' which hid records not assigned to the current staff member.
        if (!$request->has('assignee') || $request->input('assignee') === '') {
            $request->merge(['assignee' => 'all']);
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
            if ($tab === 'checklist') {
                $rows = $this->buildChecklistTabWithLeads($request, $config, $perPage, $visaType);
            } else {
                $query = $this->buildBaseQuery($request, $tab, $config);
                $query = $this->applyFilters($query, $request, $config);
                $query = $this->applySorting($query, $request, $tab, $config);

                $rows = $query->paginate($perPage)->appends(array_merge($request->except('page'), ['tab' => $tab]));
            }

            $rows->appends(array_merge($request->except('page'), ['tab' => $tab]));

            $rows->getCollection()->transform(function ($row) use ($tab) {
                $row->is_lead = $row->is_lead ?? false;
                // Checklist tab shows cost-assignment Our Cost (Block Fees); skip ledger payment totals.
                if ($tab !== 'checklist') {
                    if (!$row->is_lead && isset($row->matter_internal_id)) {
                        $payments = $this->calculatePaymentsForMatter($row->client_id, $row->matter_internal_id);
                        $row->total_payment = $payments['total'];
                        $row->pending_payment = $payments['pending'];
                    } else {
                        $row->total_payment = 0;
                        $row->pending_payment = 0;
                    }
                }
                // Ongoing: Payment Receipt column shows same balance as Account tab → Current Funds Held.
                if ($tab === 'ongoing' && !$row->is_lead && isset($row->matter_internal_id)) {
                    $held = $this->currentFundsHeldForClientMatter((int) $row->client_id, (int) $row->matter_internal_id);
                    $row->current_funds_held = $held;
                }

                return $row;
            });
        }

        $branches = Branch::orderBy('office_name')->get(['id', 'office_name']);
        $assignees = $this->getAssignees();
        $currentStages = $this->getCurrentStagesForTab($tab, $config);
        $matterTypes = $this->getMatterTypesForVisaType($config);
        $activeFilterCount = $this->countActiveFilters($request, $config);
        $showRefusedVisaType = $this->hasRefusedVisaTypeFeature($config);
        $refusedVisaTypeOptions = $showRefusedVisaType ? $this->getRefusedVisaTypeOptions($config) : [];
        $refusedVisaTypeLabel = $showRefusedVisaType ? $this->getRefusedVisaTypeLabel($config) : '';
        $refusedVisaTypeSuggestRules = $showRefusedVisaType ? $this->getRefusedVisaTypeSuggestRules($config) : [];

        return view('crm.clients.sheets.visa-type-sheet', compact(
            'rows',
            'tab',
            'perPage',
            'activeFilterCount',
            'branches',
            'assignees',
            'currentStages',
            'matterTypes',
            'config',
            'tabConfig',
            'visaType',
            'setupRequired',
            'showRefusedVisaType',
            'refusedVisaTypeOptions',
            'refusedVisaTypeLabel',
            'refusedVisaTypeSuggestRules'
        ));
    }

    protected function getVisaTypeConfig(string $visaType): ?array
    {
        $configs = config('sheets.visa_types', []);
        return $configs[$visaType] ?? null;
    }

    protected function hasRefusedVisaTypeFeature(array $config): bool
    {
        return ! empty($config['has_refused_visa_type'])
            && ($config['reference_table'] ?? '') === 'client_matter_references'
            && Schema::hasColumn('client_matter_references', 'refused_visa_type');
    }

    /**
     * @return array<string, string>
     */
    protected function getRefusedVisaTypeOptions(array $config): array
    {
        $options = $config['refused_visa_type_options'] ?? [];

        return is_array($options) ? $options : [];
    }

    protected function getRefusedVisaTypeLabel(array $config): string
    {
        $label = trim((string) ($config['refused_visa_type_label'] ?? 'Category'));

        return $label !== '' ? $label : 'Category';
    }

    /**
     * @return array<string, list<string>> option key => title needles (lowercase match)
     */
    protected function getRefusedVisaTypeSuggestRules(array $config): array
    {
        $rules = $config['refused_visa_type_suggest'] ?? [];

        return is_array($rules) ? $rules : [];
    }

    /**
     * @return list<string>
     */
    protected function allowedRefusedVisaTypeKeys(array $config): array
    {
        return array_keys($this->getRefusedVisaTypeOptions($config));
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
        $filterParams = ['branch', 'assignee', 'current_stage', 'visa_expiry_from', 'visa_expiry_to', 'deadline_from', 'deadline_to', 'matter_type', 'refused_visa_type', 'search', 'per_page'];
        foreach ($filterParams as $key) {
            $val = $request->input($key);
            if ($request->has($key) && $val !== null && $val !== '' && (!is_array($val) || !empty($val))) {
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
            'deadline_from' => $request->input('deadline_from'),
            'deadline_to' => $request->input('deadline_to'),
            'matter_type' => $request->input('matter_type'),
            'refused_visa_type' => $request->input('refused_visa_type'),
            'search' => $request->input('search'),
            'per_page' => $request->input('per_page'),
        ], function ($v) {
            if (is_array($v)) {
                return !empty($v);
            }
            return $v !== null && $v !== '';
        });
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

    /**
     * Get distinct matter types (titles) for the visa type, for filter dropdown.
     */
    protected function getMatterTypesForVisaType(array $config): array
    {
        $matterCondition = $this->getMatterCondition($config);
        $titles = DB::table('matters as m')
            ->whereRaw($matterCondition)
            ->whereNotNull('m.title')
            ->where('m.title', '!=', '')
            ->distinct()
            ->orderBy('m.title')
            ->pluck('m.title')
            ->map(fn ($t) => trim((string) $t))
            ->filter()
            ->unique()
            ->values()
            ->all();
        return array_combine($titles, $titles) ?: [];
    }

    /**
     * Build Checklist tab results including both client matters and leads.
     */
    protected function buildChecklistTabWithLeads(Request $request, array $config, int $perPage, string $visaType = ''): \Illuminate\Pagination\LengthAwarePaginator
    {
        $refTable = $config['reference_table'];
        $refAlias = $config['reference_alias'];
        $refType = $config['reference_type'] ?? $visaType;
        $checklistCol = $config['checklist_status_column'];
        $leadRefTable = $config['lead_reference_table'] ?? null;
        $remindersTable = $config['reminders_table'] ?? null;
        $leadRemindersTable = $config['lead_reminders_table'] ?? null;
        $matterCondition = $this->getMatterCondition($config);

        // PostgreSQL lowercases unquoted identifiers; TotalBLOCKFEE requires quoting (same pattern as admins."visaExpiry").
        $cafTotalBlockFee = DB::connection()->getDriverName() === 'pgsql'
            ? 'caf."TotalBLOCKFEE"'
            : 'caf.TotalBLOCKFEE';

        $checklistBlockFeeSelect = Schema::hasTable('cost_assignment_forms')
            ? "(SELECT {$cafTotalBlockFee} FROM cost_assignment_forms AS caf WHERE caf.client_matter_id = cm.id ORDER BY caf.created_at DESC, caf.id DESC LIMIT 1) AS checklist_block_fee"
            : 'NULL AS checklist_block_fee';

        // Client matters (clients + leads): workflow Checklist and/or at least one cp_doc_checklists row for this matter.
        $clientQuery = DB::table('client_matters as cm')
            ->join('matters as m', 'm.id', '=', 'cm.sel_matter_id')
            ->leftJoin("{$refTable} as {$refAlias}", function ($j) use ($refAlias, $refType, $refTable) {
                $j->on("{$refAlias}.client_id", '=', 'cm.client_id')
                    ->on("{$refAlias}.client_matter_id", '=', 'cm.id');
                if (!empty($refType) && $refTable === 'client_matter_references') {
                    $j->where("{$refAlias}.type", '=', $refType);
                }
            })
            ->leftJoin('workflow_stages as ws', 'cm.workflow_stage_id', '=', 'ws.id')
            ->join('admins', 'cm.client_id', '=', 'admins.id')
            ->leftJoin('staff as agent', 'cm.sel_migration_agent', '=', 'agent.id')
            ->leftJoin('branches', 'cm.office_id', '=', 'branches.id')
            ->whereRaw($matterCondition)
            ->whereRaw('cm.matter_status = 1')
            ->where(function ($q) {
                $q->whereRaw("LOWER(TRIM(COALESCE(ws.name, ''))) = 'checklist'");
                if (Schema::hasTable('cp_doc_checklists')) {
                    $q->orWhereExists(function ($sub) {
                        $sub->select(DB::raw('1'))
                            ->from('cp_doc_checklists as cpdc')
                            ->whereColumn('cpdc.client_matter_id', 'cm.id');
                    });
                }
            })
            ->where(function ($q) use ($checklistCol) {
                $q->whereNull("cm.{$checklistCol}")
                    ->orWhereIn("cm.{$checklistCol}", ['active', 'hold']);
            })
            ->where('admins.is_archived', 0)
            ->whereIn('admins.type', ['client', 'lead'])
            ->whereNull('admins.is_deleted');

        // Person Assisting role: restrict to matters where they are MA / PR / PA,
        // or leads allocated via admins.user_id (aligned with lead_matter_references branch).
        if ($paId = StaffClientVisibility::personAssistingStaffIdOrNull(Auth::user())) {
            $clientQuery->where(function ($q) use ($paId) {
                $q->where(function ($q2) use ($paId) {
                    $q2->where('cm.sel_migration_agent', $paId)
                        ->orWhere('cm.sel_person_responsible', $paId)
                        ->orWhere('cm.sel_person_assisting', $paId);
                })->orWhere(function ($q2) use ($paId) {
                    $q2->where('admins.type', 'lead')->where('admins.user_id', $paId);
                });
            });
        }

        $clientQuery->select(
                'cm.id as matter_internal_id',
                'cm.client_id',
                'cm.sel_matter_id',
                'admins.type as admin_entity_type',
                'admins.client_id as crm_ref',
                'admins.first_name',
                'admins.last_name',
                'admins.dob',
                DB::raw('admins."visaExpiry" as visa_expiry'),
                'cm.client_unique_matter_no',
                'm.title as matter_title',
                'cm.deadline',
                'cm.other_reference',
                'cm.department_reference',
                'cm.office_id',
                'cm.sel_migration_agent as assignee_id',
                DB::raw("CONCAT(COALESCE(agent.first_name, ''), ' ', COALESCE(agent.last_name, '')) as assignee_name"),
                'branches.office_name as branch_name',
                'ws.name as application_stage',
                "{$refAlias}.current_status",
                "{$refAlias}.payment_display_note",
                "{$refAlias}.comments as sheet_comment_text",
                "{$refAlias}.checklist_sent_at",
                "{$refAlias}.is_pinned",
                $this->refusedVisaTypeSelectColumn($config, $refAlias),
                DB::raw("COALESCE(cm.{$checklistCol}, 'active') as tr_checklist_status"),
                DB::raw($checklistBlockFeeSelect),
                DB::raw('cm.created_at as sheet_row_created_at'),
                DB::raw('NULL as lead_ref_row_id'),
                DB::raw('0 as is_lead')
            );
        $this->applyFilters($clientQuery, $request, $config, 'cm');
        $clientRows = $clientQuery->get();

        $leadRows = collect();
        $skipLeadsForRefusedFilter = $this->hasRefusedVisaTypeFeature($config)
            && $request->filled('refused_visa_type');
        if ($leadRefTable && Schema::hasTable($leadRefTable) && ! $skipLeadsForRefusedFilter) {
            $matterIds = DB::table(DB::raw('matters as m'))->whereRaw($matterCondition)->pluck('id');
            if ($matterIds->isNotEmpty()) {
                $leadBlockFeeSql = Schema::hasTable('cost_assignment_forms')
                    ? "(SELECT {$cafTotalBlockFee} FROM cost_assignment_forms AS caf INNER JOIN client_matters AS lcm ON caf.client_matter_id = lcm.id WHERE lcm.client_id = a.id AND lcm.sel_matter_id = lr.matter_id ORDER BY caf.created_at DESC, caf.id DESC LIMIT 1) AS checklist_block_fee"
                    : 'NULL AS checklist_block_fee';
                $leadQuery = DB::table($leadRefTable . ' as lr')
                    ->where('lr.type', $refType)
                    ->join('admins as a', 'lr.lead_id', '=', 'a.id')
                    ->join('matters as m', 'lr.matter_id', '=', 'm.id')
                    ->whereIn('lr.matter_id', $matterIds)
                    ->where('a.is_archived', 0)
                    ->where('a.type', 'lead')
                    ->whereNull('a.is_deleted')
                    ->select(
                        DB::raw('NULL as matter_internal_id'),
                        'lr.lead_id as client_id',
                        'a.client_id as crm_ref',
                        'a.first_name',
                        'a.last_name',
                        'a.dob',
                        DB::raw('a."visaExpiry" as visa_expiry'),
                        DB::raw("CONCAT('Lead - ', COALESCE(m.title, '')) as client_unique_matter_no"),
                        'm.title as matter_title',
                        'lr.matter_id as sel_matter_id',
                        DB::raw('NULL as deadline'),
                        DB::raw('NULL as other_reference'),
                        DB::raw('NULL as department_reference'),
                        DB::raw('NULL as office_id'),
                        DB::raw('NULL as assignee_id'),
                        DB::raw("'' as assignee_name"),
                        DB::raw('NULL as branch_name'),
                        DB::raw("'Lead' as application_stage"),
                        DB::raw('NULL as current_status'),
                        DB::raw('NULL as payment_display_note'),
                        DB::raw('NULL as sheet_comment_text'),
                        'lr.checklist_sent_at',
                        DB::raw('false as is_pinned'),
                        DB::raw('NULL as refused_visa_type'),
                        DB::raw("'active' as tr_checklist_status"),
                        DB::raw($leadBlockFeeSql),
                        'lr.created_at as sheet_row_created_at',
                        'lr.id as lead_ref_row_id',
                        DB::raw('1 as is_lead')
                    );
                // Person Assisting: restrict leads to those assigned to them
                if ($paId = StaffClientVisibility::personAssistingStaffIdOrNull(Auth::user())) {
                    $leadQuery->where('a.user_id', $paId);
                }

                if ($request->filled('search')) {
                    $search = '%' . strtolower($request->input('search')) . '%';
                    $leadQuery->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(a.first_name) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(a.last_name) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(a.client_id) LIKE ?', [$search]);
                    });
                }
                if ($request->filled('matter_type')) {
                    $val = $request->input('matter_type');
                    $leadQuery->whereRaw('LOWER(m.title) LIKE ?', ['%' . strtolower($val) . '%']);
                }
                $leadRows = $leadQuery->get();
            }
        }

        // Prefer the client_matters row when both exist (sent checklist created lead_matter_references + same matter).
        $clientLeadMatterKeys = [];
        foreach ($clientRows as $r) {
            if (isset($r->sel_matter_id)) {
                $clientLeadMatterKeys[(int) $r->client_id . ':' . (int) $r->sel_matter_id] = true;
            }
        }
        $leadRows = $leadRows->filter(function ($r) use ($clientLeadMatterKeys) {
            $mid = isset($r->sel_matter_id) ? (int) $r->sel_matter_id : 0;
            if ($mid <= 0) {
                return true;
            }

            return ! isset($clientLeadMatterKeys[(int) $r->client_id . ':' . $mid]);
        })->values();

        foreach ($clientRows as $r) {
            $r->is_lead = (($r->admin_entity_type ?? '') === 'lead') ? 1 : 0;
            unset($r->admin_entity_type);
        }
        foreach ($leadRows as $r) {
            $r->is_lead = 1;
        }

        $all = $clientRows->concat($leadRows);
        $sheetRowTimestamp = static function ($row): int {
            $raw = $row->sheet_row_created_at ?? null;
            if ($raw === null || $raw === '') {
                return 0;
            }
            try {
                return Carbon::parse($raw)->timestamp;
            } catch (\Exception $e) {
                return 0;
            }
        };
        $checklistSortTieBreaker = static function ($row): int {
            return !empty($row->is_lead)
                ? (int) ($row->lead_ref_row_id ?? 0)
                : (int) ($row->matter_internal_id ?? 0);
        };
        if ($request->filled('sort')) {
            $all = $this->sortChecklistCollection($all, $request);
        } else {
            // Newest-created first globally (matter created_at vs lead reference created_at).
            $all = $all->sort(function ($a, $b) use ($sheetRowTimestamp, $checklistSortTieBreaker) {
                $ta = $sheetRowTimestamp($a);
                $tb = $sheetRowTimestamp($b);
                if ($tb !== $ta) {
                    return $tb <=> $ta;
                }

                return $checklistSortTieBreaker($b) <=> $checklistSortTieBreaker($a);
            })->values();
        }
        if ($remindersTable && Schema::hasTable($remindersTable)) {
            foreach ($all as $row) {
                if ($row->is_lead) {
                    if ($leadRemindersTable && Schema::hasTable($leadRemindersTable)) {
                        $row->email_reminder_latest = DB::table($leadRemindersTable)->where('visa_type', $refType)->where('lead_id', $row->client_id)->where('type', 'email')->max('reminded_at');
                        $row->email_reminder_count = DB::table($leadRemindersTable)->where('visa_type', $refType)->where('lead_id', $row->client_id)->where('type', 'email')->count();
                        $row->sms_reminder_latest = DB::table($leadRemindersTable)->where('visa_type', $refType)->where('lead_id', $row->client_id)->where('type', 'sms')->max('reminded_at');
                        $row->sms_reminder_count = DB::table($leadRemindersTable)->where('visa_type', $refType)->where('lead_id', $row->client_id)->where('type', 'sms')->count();
                        $row->phone_reminder_latest = DB::table($leadRemindersTable)->where('visa_type', $refType)->where('lead_id', $row->client_id)->where('type', 'phone')->max('reminded_at');
                        $row->phone_reminder_count = DB::table($leadRemindersTable)->where('visa_type', $refType)->where('lead_id', $row->client_id)->where('type', 'phone')->count();
                    } else {
                        $row->email_reminder_latest = $row->email_reminder_count = $row->sms_reminder_latest = $row->sms_reminder_count = $row->phone_reminder_latest = $row->phone_reminder_count = null;
                    }
                } else {
                    $row->email_reminder_latest = DB::table($remindersTable)->where('visa_type', $refType)->where('client_matter_id', $row->matter_internal_id)->where('type', 'email')->max('reminded_at');
                    $row->email_reminder_count = DB::table($remindersTable)->where('visa_type', $refType)->where('client_matter_id', $row->matter_internal_id)->where('type', 'email')->count();
                    $row->sms_reminder_latest = DB::table($remindersTable)->where('visa_type', $refType)->where('client_matter_id', $row->matter_internal_id)->where('type', 'sms')->max('reminded_at');
                    $row->sms_reminder_count = DB::table($remindersTable)->where('visa_type', $refType)->where('client_matter_id', $row->matter_internal_id)->where('type', 'sms')->count();
                    $row->phone_reminder_latest = DB::table($remindersTable)->where('visa_type', $refType)->where('client_matter_id', $row->matter_internal_id)->where('type', 'phone')->max('reminded_at');
                    $row->phone_reminder_count = DB::table($remindersTable)->where('visa_type', $refType)->where('client_matter_id', $row->matter_internal_id)->where('type', 'phone')->count();
                }
            }
        }
        $this->attachClientEmails($all);

        $total = $all->count();
        $page = (int) $request->get('page', 1);
        $slice = $all->slice(($page - 1) * $perPage, $perPage)->values();
        $visaTypeParam = $request->route('visaType');
        $path = route($config['route'], ['visaType' => $visaTypeParam]);
        return new \Illuminate\Pagination\LengthAwarePaginator($slice, $total, $perPage, $page, ['path' => $path, 'pageName' => 'page']);
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
        $matterCondition = $this->getMatterCondition($config);
        $refTable = $config['reference_table'];
        $refAlias = $config['reference_alias'];
        $visaType = $request->route('visaType', '');
        $refType = $config['reference_type'] ?? $visaType;
        $checklistCol = $config['checklist_status_column'];

        // One row per matter (per plan: "one row per TR matter — filtered by active tab").
        // Show ALL matters matching the visa type and tab, not just latest per client.
        $baseMattersSql = "
            SELECT
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
                cm.decision_outcome,
                cm.decision_note,
                cm.matter_status,
                cm.deadline,
                cm.{$checklistCol} as checklist_status,
                m.title as matter_title
            FROM client_matters cm
            INNER JOIN matters m ON m.id = cm.sel_matter_id
            WHERE {$matterCondition}
        ";

        $query = DB::table(DB::raw('(' . $baseMattersSql . ') AS latest_matter'))
            ->leftJoin("{$refTable} as {$refAlias}", function ($join) use ($refAlias, $refType, $refTable) {
                $join->on("{$refAlias}.client_id", '=', 'latest_matter.client_id')
                    ->on("{$refAlias}.client_matter_id", '=', 'latest_matter.matter_id');
                if (!empty($refType) && $refTable === 'client_matter_references') {
                    $join->where("{$refAlias}.type", '=', $refType);
                }
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
                $this->refusedVisaTypeSelectColumn($config, $refAlias),
                'latest_matter.checklist_status as tr_checklist_status',
                'latest_matter.decision_outcome',
                'latest_matter.decision_note'
            )
            ->where('admins.is_archived', 0)
            ->whereIn('admins.type', ['client', 'lead'])
            ->whereNull('admins.is_deleted');

        // Person Assisting role: restrict to matters where they are MA / PR / PA
        if ($paId = StaffClientVisibility::personAssistingStaffIdOrNull(Auth::user())) {
            $query->where(function ($q) use ($paId) {
                $q->where('latest_matter.sel_migration_agent', $paId)
                    ->orWhere('latest_matter.sel_person_responsible', $paId)
                    ->orWhere('latest_matter.sel_person_assisting', $paId);
            });
        }

        $this->applyTabFilter($query, $tab, $config);

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

    /**
     * Apply filters to a visa sheet query.
     *
     * @param string $matterAlias Table alias for the matter columns (e.g. 'latest_matter' for buildBaseQuery subquery, 'cm' for buildChecklistTabWithLeads client query)
     */
    protected function applyFilters($query, Request $request, array $config, string $matterAlias = 'latest_matter')
    {
        $refAlias = $config['reference_alias'] ?? 'ref';

        if ($request->filled('branch')) {
            $branchIds = is_array($request->input('branch')) ? $request->input('branch') : [$request->input('branch')];
            $query->whereIn("{$matterAlias}.office_id", $branchIds);
        }
        if ($request->filled('assignee') && $request->input('assignee') !== 'all') {
            $assigneeId = $request->input('assignee') === 'me' ? Auth::id() : $request->input('assignee');
            if ($assigneeId) {
                $query->where(function ($q) use ($assigneeId, $matterAlias) {
                    $q->where("{$matterAlias}.sel_migration_agent", $assigneeId)
                        ->orWhere("{$matterAlias}.sel_person_responsible", $assigneeId)
                        ->orWhere("{$matterAlias}.sel_person_assisting", $assigneeId);
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
        if ($request->filled('deadline_from')) {
            try {
                $from = Carbon::createFromFormat('d/m/Y', $request->input('deadline_from'))->startOfDay();
                $query->whereRaw("{$matterAlias}.deadline >= ?", [$from]);
            } catch (\Exception $e) {}
        }
        if ($request->filled('deadline_to')) {
            try {
                $to = Carbon::createFromFormat('d/m/Y', $request->input('deadline_to'))->endOfDay();
                $query->whereRaw("{$matterAlias}.deadline <= ?", [$to]);
            } catch (\Exception $e) {}
        }
        if ($request->filled('matter_type')) {
            $val = $request->input('matter_type');
            $matterTitleCol = $matterAlias === 'latest_matter' ? 'latest_matter.matter_title' : 'm.title';
            $query->whereRaw("LOWER({$matterTitleCol}) LIKE ?", ['%' . strtolower($val) . '%']);
        }
        if ($this->hasRefusedVisaTypeFeature($config) && $request->filled('refused_visa_type')) {
            $refusedType = (string) $request->input('refused_visa_type');
            if (in_array($refusedType, $this->allowedRefusedVisaTypeKeys($config), true)) {
                $query->where("{$refAlias}.refused_visa_type", $refusedType);
            }
        }
        if ($request->filled('search')) {
            $search = '%' . strtolower($request->input('search')) . '%';
            $query->where(function ($q) use ($search, $refAlias, $matterAlias) {
                $q->whereRaw('LOWER(admins.first_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(admins.last_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(admins.client_id) LIKE ?', [$search])
                    ->orWhereRaw("LOWER({$refAlias}.current_status) LIKE ?", [$search])
                    ->orWhereRaw('LOWER(ws.name) LIKE ?', [$search])
                    ->orWhereRaw("LOWER({$matterAlias}.other_reference) LIKE ?", [$search])
                    ->orWhereRaw("LOWER({$matterAlias}.department_reference) LIKE ?", [$search])
                    ->orWhereRaw("LOWER({$matterAlias}.client_unique_matter_no) LIKE ?", [$search]);
            });
        }
        return $query;
    }

    protected function applySorting($query, Request $request, string $tab, array $config)
    {
        $refAlias = $config['reference_alias'] ?? 'ref';
        $driver = DB::connection()->getDriverName();

        // First priority: pinned items (is_pinned DESC) - pinned items on top
        $query->orderByRaw("CASE WHEN {$refAlias}.is_pinned = true THEN 1 ELSE 0 END DESC");

        // Second priority: checklist hold status (only for checklist tab)
        if ($tab === 'checklist') {
            $query->orderByRaw("CASE WHEN COALESCE(latest_matter.checklist_status, 'active') = 'hold' THEN 1 ELSE 0 END ASC");
        }

        $sortField = $request->get('sort');
        $sortDirection = strtolower($request->get('direction', 'asc'));
        if (!in_array($sortDirection, ['asc', 'desc'], true)) {
            $sortDirection = 'asc';
        }

        if ($sortField) {
            $this->applyExplicitSort($query, $sortField, $sortDirection, $driver);
        } else {
            // Default: nearest deadline, then visa expiry
            if ($driver === 'mysql') {
                $query->orderByRaw('latest_matter.deadline IS NULL ASC, latest_matter.deadline ASC');
            } else {
                $query->orderByRaw('latest_matter.deadline ASC NULLS LAST');
            }

            $query->orderByRaw("CASE WHEN admins.\"visaExpiry\" IS NULL OR admins.\"visaExpiry\"::text = '0000-00-00' THEN 1 ELSE 0 END ASC");
            $query->orderByRaw('admins."visaExpiry" ASC');
        }

        $query->orderBy('latest_matter.matter_id', 'asc');

        return $query;
    }

    protected function applyExplicitSort($query, string $sortField, string $sortDirection, string $driver): void
    {
        $nullsLast = $sortDirection === 'asc' ? 'LAST' : 'FIRST';

        switch ($sortField) {
            case 'crm_ref':
                $query->orderBy('admins.client_id', $sortDirection);
                break;
            case 'name':
                $query->orderBy('admins.last_name', $sortDirection);
                $query->orderBy('admins.first_name', $sortDirection);
                break;
            case 'dob':
                if ($driver === 'mysql') {
                    $query->orderByRaw('admins.dob IS NULL ASC, admins.dob ' . $sortDirection);
                } else {
                    $query->orderByRaw('admins.dob ' . $sortDirection . ' NULLS ' . $nullsLast);
                }
                break;
            case 'stage':
                $query->orderBy('ws.name', $sortDirection);
                break;
            case 'assignee':
                $query->orderBy('agent.last_name', $sortDirection);
                $query->orderBy('agent.first_name', $sortDirection);
                break;
            case 'matter':
                $query->orderBy('latest_matter.matter_title', $sortDirection);
                break;
            case 'visa_expiry':
                $query->orderByRaw("CASE WHEN admins.\"visaExpiry\" IS NULL OR admins.\"visaExpiry\"::text = '0000-00-00' THEN 1 ELSE 0 END ASC");
                if ($driver === 'mysql') {
                    $query->orderByRaw('admins."visaExpiry" ' . $sortDirection);
                } else {
                    $query->orderByRaw('admins."visaExpiry" ' . $sortDirection . ' NULLS ' . $nullsLast);
                }
                break;
            case 'deadline':
                if ($driver === 'mysql') {
                    $query->orderByRaw('latest_matter.deadline IS NULL ASC, latest_matter.deadline ' . $sortDirection);
                } else {
                    $query->orderByRaw('latest_matter.deadline ' . $sortDirection . ' NULLS ' . $nullsLast);
                }
                break;
        }
    }

    protected function sortChecklistCollection($collection, Request $request)
    {
        $sortField = $request->get('sort');
        $sortDirection = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $multiplier = $sortDirection === 'asc' ? 1 : -1;

        $valueFor = static function ($row, string $field) {
            switch ($field) {
                case 'crm_ref':
                    return strtolower((string) ($row->crm_ref ?? ''));
                case 'name':
                    return strtolower(trim(($row->last_name ?? '') . ' ' . ($row->first_name ?? '')));
                case 'dob':
                    return (string) ($row->dob ?? '');
                case 'visa_expiry':
                    $expiry = $row->visa_expiry ?? null;
                    return ($expiry && $expiry !== '0000-00-00') ? (string) $expiry : '9999-99-99';
                case 'deadline':
                    $dl = $row->deadline ?? null;
                    return $dl ? (string) $dl : '9999-99-99';
                case 'assignee':
                    return strtolower(trim((string) ($row->assignee_name ?? '')));
                case 'matter':
                    return strtolower((string) ($row->matter_title ?? ''));
                default:
                    return '';
            }
        };

        return $collection->sort(function ($a, $b) use ($sortField, $multiplier, $valueFor) {
            $aPin = !empty($a->is_pinned) ? 1 : 0;
            $bPin = !empty($b->is_pinned) ? 1 : 0;
            if ($bPin !== $aPin) {
                return $bPin <=> $aPin;
            }

            $aHold = (($a->tr_checklist_status ?? 'active') === 'hold') ? 1 : 0;
            $bHold = (($b->tr_checklist_status ?? 'active') === 'hold') ? 1 : 0;
            if ($aHold !== $bHold) {
                return $aHold <=> $bHold;
            }

            $va = $valueFor($a, (string) $sortField);
            $vb = $valueFor($b, (string) $sortField);
            if ($va === $vb) {
                return 0;
            }

            return ($va < $vb ? -1 : 1) * $multiplier;
        })->values();
    }

    protected function countActiveFilters(Request $request, array $config = []): int
    {
        $count = 0;
        if ($request->filled('branch')) $count++;
        if ($request->filled('assignee') && $request->input('assignee') !== 'all') $count++;
        if ($request->filled('current_stage')) $count++;
        if ($request->filled('visa_expiry_from')) $count++;
        if ($request->filled('visa_expiry_to')) $count++;
        if ($request->filled('deadline_from')) $count++;
        if ($request->filled('deadline_to')) $count++;
        if ($request->filled('matter_type')) $count++;
        if ($this->hasRefusedVisaTypeFeature($config) && $request->filled('refused_visa_type')) $count++;
        if ($request->filled('search')) $count++;
        return $count;
    }

    /**
     * SELECT fragment for refused_visa_type when sheet config enables the feature.
     */
    protected function refusedVisaTypeSelectColumn(array $config, string $refAlias): \Illuminate\Database\Query\Expression|string
    {
        if ($this->hasRefusedVisaTypeFeature($config)) {
            return "{$refAlias}.refused_visa_type";
        }

        return DB::raw('NULL as refused_visa_type');
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
     * Mirrors Client Funds Ledger "Current Funds Held" on Account tab (account.blade.php)
     * for one client + matter: receipt_type ledger rows, excluding void_fee_transfer.
     */
    protected function currentFundsHeldForClientMatter(?int $clientId, ?int $matterInternalId): ?float
    {
        if (!$clientId || !$matterInternalId || ! Schema::hasTable('account_client_receipts')) {
            return null;
        }

        $ledgerEntries = DB::table('account_client_receipts')
            ->select('deposit_amount', 'withdraw_amount', 'void_fee_transfer')
            ->where('client_id', $clientId)
            ->where('client_matter_id', $matterInternalId)
            ->where('receipt_type', 1)
            ->get();

        $calculatedBalance = 0.0;
        foreach ($ledgerEntries as $entry) {
            if (isset($entry->void_fee_transfer) && $entry->void_fee_transfer == 1) {
                continue;
            }
            $calculatedBalance += floatval($entry->deposit_amount ?? 0) - floatval($entry->withdraw_amount ?? 0);
        }

        return round($calculatedBalance, 2);
    }

    /**
     * Toggle pin status for a matter in the sheet.
     */
    public function togglePin(Request $request, string $visaType)
    {
        if (! $this->hasModuleAccess('20') || ! $this->canAccessCrmSheet($visaType)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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
        $refType = $config['reference_type'] ?? $visaType;

        if (!Schema::hasTable($refTable)) {
            return response()->json(['success' => false, 'message' => 'Reference table not found'], 404);
        }

        if ($refTable === 'client_matter_references' && (empty($refType) || $refType === '')) {
            return response()->json(['success' => false, 'message' => 'Visa type config missing reference_type'], 500);
        }

        try {
            $query = DB::table($refTable)
                ->where('client_id', $clientId)
                ->where('client_matter_id', $matterInternalId);
            if (!empty($refType) && $refTable === 'client_matter_references') {
                $query->where('type', $refType);
            }
            $reference = $query->first();

            if ($reference) {
                // Toggle existing pin
                $newPinStatus = !($reference->is_pinned ?? false);
                $updateQuery = DB::table($refTable)
                    ->where('client_id', $clientId)
                    ->where('client_matter_id', $matterInternalId);
                if (!empty($refType) && $refTable === 'client_matter_references') {
                    $updateQuery->where('type', $refType);
                }
                $updateQuery->update([
                        'is_pinned' => $newPinStatus,
                        'updated_by' => Auth::id(),
                        'updated_at' => now(),
                    ]);
            } else {
                // Create new reference record with pin
                $insertData = [
                    'client_id' => $clientId,
                    'client_matter_id' => $matterInternalId,
                    'is_pinned' => true,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if ($refTable === 'client_matter_references') {
                    $insertData['type'] = $refType;
                }
                DB::table($refTable)->insert($insertData);
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

    /**
     * Update checklist tab status (active, hold, convert_to_client, discontinue) on client_matters.
     * Applies to both clients and leads — same column per visa sheet type.
     */
    public function updateChecklistStatus(Request $request, string $visaType)
    {
        if (! $this->hasModuleAccess('20') || ! $this->canAccessCrmSheet($visaType)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $config = $this->getVisaTypeConfig($visaType);
        if (! $config) {
            return response()->json(['success' => false, 'message' => 'Invalid visa type'], 404);
        }

        $checklistCol = $config['checklist_status_column'] ?? '';
        if ($checklistCol === '' || ! Schema::hasColumn('client_matters', $checklistCol)) {
            return response()->json(['success' => false, 'message' => 'Checklist status is not available'], 500);
        }

        $matterInternalId = (int) $request->input('matter_internal_id', 0);
        $status = strtolower(trim((string) $request->input('status', '')));

        $allowed = ['active', 'hold', 'convert_to_client', 'discontinue'];
        if ($matterInternalId <= 0 || ! in_array($status, $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'Invalid matter or status'], 400);
        }

        $matterCondition = $this->getMatterCondition($config);

        $matterRow = DB::table('client_matters as cm')
            ->join('matters as m', 'm.id', '=', 'cm.sel_matter_id')
            ->where('cm.id', $matterInternalId)
            ->whereRaw($matterCondition)
            ->whereRaw('cm.matter_status = 1')
            ->select('cm.id', 'cm.client_id')
            ->first();

        if (! $matterRow) {
            return response()->json(['success' => false, 'message' => 'Matter not found or not on this sheet'], 404);
        }

        if (! StaffClientVisibility::canAccessClientOrLead((int) $matterRow->client_id, Auth::user())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            DB::table('client_matters')
                ->where('id', $matterInternalId)
                ->update([
                    $checklistCol => $status,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated',
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Could not update status'], 500);
        }
    }

    /**
     * Create or update the sheet comment for a client matter reference row.
     */
    public function updateComment(Request $request, string $visaType)
    {
        if (! $this->hasModuleAccess('20') || ! $this->canAccessCrmSheet($visaType)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $config = $this->getVisaTypeConfig($visaType);
        if (! $config) {
            return response()->json(['success' => false, 'message' => 'Invalid visa type'], 404);
        }

        $clientId = (int) $request->input('client_id', 0);
        $matterInternalId = (int) $request->input('matter_internal_id', 0);
        $comment = $request->input('comment');
        if (is_string($comment)) {
            $comment = trim($comment);
            if ($comment === '') {
                $comment = null;
            }
        } else {
            $comment = null;
        }

        if ($clientId <= 0 || $matterInternalId <= 0) {
            return response()->json(['success' => false, 'message' => 'Missing required parameters'], 400);
        }

        if ($comment !== null && mb_strlen($comment) > 5000) {
            return response()->json(['success' => false, 'message' => 'Comment must be 5000 characters or fewer'], 422);
        }

        $refTable = $config['reference_table'];
        $refType = $config['reference_type'] ?? $visaType;

        if (! Schema::hasTable($refTable)) {
            return response()->json(['success' => false, 'message' => 'Reference table not found'], 404);
        }

        if ($refTable === 'client_matter_references' && (empty($refType) || $refType === '')) {
            return response()->json(['success' => false, 'message' => 'Visa type config missing reference_type'], 500);
        }

        $matterCondition = $this->getMatterCondition($config);

        $matterRow = DB::table('client_matters as cm')
            ->join('matters as m', 'm.id', '=', 'cm.sel_matter_id')
            ->where('cm.id', $matterInternalId)
            ->where('cm.client_id', $clientId)
            ->whereRaw($matterCondition)
            ->whereRaw('cm.matter_status = 1')
            ->select('cm.id', 'cm.client_id')
            ->first();

        if (! $matterRow) {
            return response()->json(['success' => false, 'message' => 'Matter not found or not on this sheet'], 404);
        }

        if (! StaffClientVisibility::canAccessClientOrLead((int) $matterRow->client_id, Auth::user())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $query = DB::table($refTable)
                ->where('client_id', $clientId)
                ->where('client_matter_id', $matterInternalId);
            if (! empty($refType) && $refTable === 'client_matter_references') {
                $query->where('type', $refType);
            }
            $reference = $query->first();

            if ($reference) {
                $updateQuery = DB::table($refTable)
                    ->where('client_id', $clientId)
                    ->where('client_matter_id', $matterInternalId);
                if (! empty($refType) && $refTable === 'client_matter_references') {
                    $updateQuery->where('type', $refType);
                }
                $updateQuery->update([
                    'comments' => $comment,
                    'updated_by' => Auth::id(),
                    'updated_at' => now(),
                ]);
            } else {
                $insertData = [
                    'client_id' => $clientId,
                    'client_matter_id' => $matterInternalId,
                    'comments' => $comment,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if ($refTable === 'client_matter_references') {
                    $insertData['type'] = $refType;
                }
                DB::table($refTable)->insert($insertData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Comment saved',
                'comment' => $comment ?? '',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Could not save comment'], 500);
        }
    }

    /**
     * Set refused visa / matter type on an ART Matters sheet reference row.
     */
    public function updateRefusedVisaType(Request $request, string $visaType)
    {
        if (! $this->hasModuleAccess('20') || ! $this->canAccessCrmSheet($visaType)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $config = $this->getVisaTypeConfig($visaType);
        if (! $config || ! $this->hasRefusedVisaTypeFeature($config)) {
            return response()->json(['success' => false, 'message' => 'Not available for this sheet'], 404);
        }

        $clientId = (int) $request->input('client_id', 0);
        $matterInternalId = (int) $request->input('matter_internal_id', 0);
        $refusedType = trim((string) $request->input('refused_visa_type', ''));
        $allowed = $this->allowedRefusedVisaTypeKeys($config);

        if ($clientId <= 0 || $matterInternalId <= 0) {
            return response()->json(['success' => false, 'message' => 'Missing required parameters'], 400);
        }

        if ($refusedType !== '' && ! in_array($refusedType, $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'Invalid refused visa type'], 422);
        }

        $refTable = $config['reference_table'];
        $refType = $config['reference_type'] ?? $visaType;
        $matterCondition = $this->getMatterCondition($config);

        $matterRow = DB::table('client_matters as cm')
            ->join('matters as m', 'm.id', '=', 'cm.sel_matter_id')
            ->where('cm.id', $matterInternalId)
            ->where('cm.client_id', $clientId)
            ->whereRaw($matterCondition)
            ->select('cm.id', 'cm.client_id')
            ->first();

        if (! $matterRow) {
            return response()->json(['success' => false, 'message' => 'Matter not found or not on this sheet'], 404);
        }

        if (! StaffClientVisibility::canAccessClientOrLead((int) $matterRow->client_id, Auth::user())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $valueToStore = $refusedType === '' ? null : $refusedType;

        try {
            $query = DB::table($refTable)
                ->where('client_id', $clientId)
                ->where('client_matter_id', $matterInternalId);
            if ($refTable === 'client_matter_references') {
                $query->where('type', $refType);
            }
            $reference = $query->first();

            if ($reference) {
                DB::table($refTable)
                    ->where('client_id', $clientId)
                    ->where('client_matter_id', $matterInternalId)
                    ->when($refTable === 'client_matter_references', fn ($q) => $q->where('type', $refType))
                    ->update([
                        'refused_visa_type' => $valueToStore,
                        'updated_by' => Auth::id(),
                        'updated_at' => now(),
                    ]);
            } else {
                $insertData = [
                    'client_id' => $clientId,
                    'client_matter_id' => $matterInternalId,
                    'refused_visa_type' => $valueToStore,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if ($refTable === 'client_matter_references') {
                    $insertData['type'] = $refType;
                }
                DB::table($refTable)->insert($insertData);
            }

            $label = $valueToStore === null
                ? ''
                : ($this->getRefusedVisaTypeOptions($config)[$valueToStore] ?? $valueToStore);

            return response()->json([
                'success' => true,
                'message' => $valueToStore === null
                    ? ($this->getRefusedVisaTypeLabel($config) . ' cleared')
                    : ($this->getRefusedVisaTypeLabel($config) . ' saved'),
                'refused_visa_type' => $valueToStore,
                'refused_visa_type_label' => $label,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Could not save refused visa type'], 500);
        }
    }

    /**
     * Record a checklist reminder (email, sms, or phone) for a client matter on the visa sheet.
     */
    public function recordReminder(Request $request, string $visaType)
    {
        if (! $this->hasModuleAccess('20') || ! $this->canAccessCrmSheet($visaType)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $config = $this->getVisaTypeConfig($visaType);
        if (! $config) {
            return response()->json(['success' => false, 'message' => 'Invalid visa type'], 404);
        }

        $matterInternalId = (int) $request->input('matter_internal_id', 0);
        $type = strtolower(trim((string) $request->input('type', '')));
        $allowed = ['email', 'sms', 'phone'];

        if ($matterInternalId <= 0 || ! in_array($type, $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'Invalid matter or reminder type'], 400);
        }

        $matterCondition = $this->getMatterCondition($config);

        $matterRow = DB::table('client_matters as cm')
            ->join('matters as m', 'm.id', '=', 'cm.sel_matter_id')
            ->where('cm.id', $matterInternalId)
            ->whereRaw($matterCondition)
            ->whereRaw('cm.matter_status = 1')
            ->select('cm.id', 'cm.client_id')
            ->first();

        if (! $matterRow) {
            return response()->json(['success' => false, 'message' => 'Matter not found or not on this sheet'], 404);
        }

        if (! StaffClientVisibility::canAccessClientOrLead((int) $matterRow->client_id, Auth::user())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $clientMatter = ClientMatter::find($matterInternalId);
        if (! $clientMatter || ! $clientMatter->recordMatterReminder($type, Auth::id())) {
            return response()->json(['success' => false, 'message' => 'Could not record reminder'], 422);
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' reminder recorded',
            'reminded_at' => now()->format('d/m/Y'),
        ]);
    }

    /**
     * Attach a resolved client email to each checklist row (admins.email or first client_emails row).
     */
    protected function attachClientEmails($rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $clientIds = $rows->pluck('client_id')->filter()->unique()->values();
        if ($clientIds->isEmpty()) {
            return;
        }

        $adminEmails = DB::table('admins')->whereIn('id', $clientIds)->pluck('email', 'id');
        $fallbackEmails = collect();
        if (Schema::hasTable('client_emails')) {
            $fallbackEmails = DB::table('client_emails')
                ->whereIn('client_id', $clientIds)
                ->orderByRaw("CASE WHEN email_type = 'Personal' THEN 0 ELSE 1 END")
                ->orderBy('id')
                ->get(['client_id', 'email'])
                ->groupBy('client_id');
        }

        foreach ($rows as $row) {
            $clientId = (int) ($row->client_id ?? 0);
            $email = trim((string) ($adminEmails[$clientId] ?? ''));
            if ($email === '' && $fallbackEmails->has($clientId)) {
                $email = trim((string) ($fallbackEmails[$clientId]->first()->email ?? ''));
            }
            $row->client_email = $email;
        }
    }
}
