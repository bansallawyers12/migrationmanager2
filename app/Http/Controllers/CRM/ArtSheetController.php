<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ClientArtReference;
use App\Models\ActivitiesLog;
use App\Traits\ClientAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ArtSheetController extends Controller
{
    use ClientAuthorization;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display the ART Sheet - List view
     */
    public function index(Request $request)
    {
        if (!$this->hasModuleAccess('20')) {
            abort(403, 'Unauthorized');
        }

        $perPage = (int) $request->get('per_page', 50);
        $allowedPerPage = [10, 25, 50, 100, 200];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 50;
        }

        $query = $this->buildBaseQuery($request);
        $query = $this->applyFilters($query, $request);
        $query = $this->applySorting($query, $request);

        $rows = $query->paginate($perPage)->appends($request->except('page'));

        $rows->getCollection()->transform(function ($row) {
            $payments = $this->calculatePaymentsForMatter($row->client_id, $row->matter_internal_id);
            $row->total_payment = $payments['total'];
            $row->pending_payment = $payments['pending'];
            return $row;
        });

        $activeFilterCount = $this->countActiveFilters($request);
        $agents = Admin::where('is_migration_agent', 1)->where('status', 1)->orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $statusOptions = $this->getStatusOptions();

        return view('crm.clients.sheets.art', compact('rows', 'perPage', 'activeFilterCount', 'agents', 'statusOptions'));
    }

    /**
     * Display the ART Sheet - Insights view
     */
    public function insights(Request $request)
    {
        if (!$this->hasModuleAccess('20')) {
            abort(403, 'Unauthorized');
        }

        $baseQuery = $this->buildBaseQuery($request);
        $baseQuery = $this->applyFilters($baseQuery, $request);
        $allRecords = $baseQuery->get();

        $insights = $this->calculateInsights($allRecords);
        $activeFilterCount = $this->countActiveFilters($request);

        return view('crm.clients.sheets.art-insights', compact('insights', 'activeFilterCount'));
    }

    /**
     * Build base query: latest ART matter per client, left join client_art_references
     */
    protected function buildBaseQuery(Request $request)
    {
        $driver = DB::connection()->getDriverName();
        $latestArtMatterSql = "
            SELECT DISTINCT ON (cm.client_id)
                cm.client_id,
                cm.client_unique_matter_no,
                cm.id AS matter_id,
                cm.other_reference,
                cm.department_reference,
                cm.sel_migration_agent
            FROM client_matters cm
            INNER JOIN matters m ON m.id = cm.sel_matter_id
            WHERE cm.matter_status = 1
              AND (
                  LOWER(COALESCE(m.nick_name, '')) = 'art'
                  OR LOWER(COALESCE(m.title, '')) LIKE '%art%'
                  OR LOWER(COALESCE(m.title, '')) LIKE '%administrative appeals%'
                  OR LOWER(COALESCE(m.title, '')) LIKE '%tribunal%'
              )
            ORDER BY cm.client_id, cm.id DESC
        ";

        if ($driver === 'mysql') {
            $latestArtMatterSql = "
                SELECT cm.client_id, cm.client_unique_matter_no, cm.id AS matter_id,
                       cm.other_reference, cm.department_reference, cm.sel_migration_agent
                FROM client_matters cm
                INNER JOIN matters m ON m.id = cm.sel_matter_id
                INNER JOIN (
                    SELECT client_id, MAX(id) AS max_id FROM client_matters cm2
                    INNER JOIN matters m2 ON m2.id = cm2.sel_matter_id
                    WHERE cm2.matter_status = 1
                      AND (LOWER(COALESCE(m2.nick_name, '')) = 'art'
                           OR LOWER(COALESCE(m2.title, '')) LIKE '%art%'
                           OR LOWER(COALESCE(m2.title, '')) LIKE '%administrative appeals%'
                           OR LOWER(COALESCE(m2.title, '')) LIKE '%tribunal%')
                    GROUP BY client_id
                ) latest ON latest.client_id = cm.client_id AND latest.max_id = cm.id
                WHERE cm.matter_status = 1
                  AND (LOWER(COALESCE(m.nick_name, '')) = 'art'
                       OR LOWER(COALESCE(m.title, '')) LIKE '%art%'
                       OR LOWER(COALESCE(m.title, '')) LIKE '%administrative appeals%'
                       OR LOWER(COALESCE(m.title, '')) LIKE '%tribunal%')
            ";
        }

        $query = DB::table(DB::raw('(' . $latestArtMatterSql . ') AS latest_art_matter'))
            ->leftJoin('client_art_references as art', function ($join) {
                $join->on('art.client_id', '=', 'latest_art_matter.client_id')
                    ->on('art.client_matter_id', '=', 'latest_art_matter.matter_id');
            })
            ->join('admins', 'latest_art_matter.client_id', '=', 'admins.id')
            ->leftJoin('admins as agents', 'latest_art_matter.sel_migration_agent', '=', 'agents.id')
            ->select(
                'art.id as art_id',
                'art.submission_last_date',
                'art.status_of_file',
                'art.hearing_time',
                'art.member_name',
                'art.outcome',
                'art.comments',
                'latest_art_matter.client_id',
                'admins.client_id as crm_ref',
                'admins.first_name',
                'admins.last_name',
                'latest_art_matter.client_unique_matter_no as matter_id',
                'latest_art_matter.matter_id as matter_internal_id',
                'latest_art_matter.other_reference',
                'latest_art_matter.department_reference',
                DB::raw("CONCAT(COALESCE(agents.first_name, ''), ' ', COALESCE(agents.last_name, '')) as agent_name")
            )
            ->where('admins.is_archived', 0)
            ->where('admins.role', 7)
            ->whereNull('admins.is_deleted');

        return $query;
    }

    /**
     * Calculate total and pending payments for a client matter
     */
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

        return [
            'total' => number_format($total, 2),
            'pending' => number_format($pending, 2),
        ];
    }

    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('status')) {
            $query->where('art.status_of_file', $request->input('status'));
        }

        if ($request->filled('from_date')) {
            try {
                $fromDate = Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->startOfDay();
                $query->whereRaw('art.submission_last_date >= ?', [$fromDate]);
            } catch (\Exception $e) {
                // ignore invalid date
            }
        }

        if ($request->filled('to_date')) {
            try {
                $toDate = Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->endOfDay();
                $query->whereRaw('art.submission_last_date <= ?', [$toDate]);
            } catch (\Exception $e) {
                // ignore invalid date
            }
        }

        if ($request->filled('agent')) {
            $query->where('latest_art_matter.sel_migration_agent', $request->input('agent'));
        }

        if ($request->filled('search')) {
            $search = '%' . strtolower($request->input('search')) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(admins.first_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(admins.last_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(admins.client_id) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(latest_art_matter.other_reference) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(latest_art_matter.department_reference) LIKE ?', [$search]);
            });
        }

        return $query;
    }

    protected function applySorting($query, Request $request)
    {
        $sortField = $request->get('sort', 'submission_date');
        $sortDirection = $request->get('direction', 'desc');
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        $dir = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        $sortableFields = [
            'crm_ref' => 'admins.client_id',
            'other_reference' => 'latest_art_matter.other_reference',
            'client_name' => 'admins.first_name',
            'submission_date' => 'art.submission_last_date',
            'status' => 'art.status_of_file',
            'agent_name' => 'agents.first_name',
        ];

        $actualSortField = $sortableFields[$sortField] ?? 'art.submission_last_date';
        $query->orderByRaw($actualSortField . ' ' . $dir);

        return $query;
    }

    protected function countActiveFilters(Request $request)
    {
        $filters = ['status', 'from_date', 'to_date', 'agent', 'search'];
        $count = 0;
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $count++;
            }
        }
        return $count;
    }

    protected function getStatusOptions()
    {
        return [
            'submission_pending' => 'Submission Pending',
            'submission_done' => 'Submission Done',
            'hearing_invitation_sent' => 'Hearing Invitation Sent',
            'waiting_for_hearing' => 'Waiting for Hearing',
            'hearing' => 'Hearing',
            'decided' => 'Decided',
            'withdrawn' => 'Withdrawn',
        ];
    }

    protected function calculateInsights($records)
    {
        $insights = [
            'total_records' => $records->count(),
            'by_status' => [],
            'by_agent' => [],
            'recent_submissions_7d' => 0,
            'recent_submissions_30d' => 0,
            'submissions_by_month' => [],
        ];

        if ($records->isEmpty()) {
            return $insights;
        }

        $insights['by_status'] = $records->groupBy('status_of_file')->map->count()->toArray();

        $insights['by_agent'] = $records->groupBy('agent_name')->map->count()->toArray();

        $now = Carbon::now();
        $insights['recent_submissions_7d'] = $records->filter(function ($record) use ($now) {
            return $record->submission_last_date && Carbon::parse($record->submission_last_date)->greaterThanOrEqualTo($now->copy()->subDays(7));
        })->count();

        $insights['recent_submissions_30d'] = $records->filter(function ($record) use ($now) {
            return $record->submission_last_date && Carbon::parse($record->submission_last_date)->greaterThanOrEqualTo($now->copy()->subDays(30));
        })->count();

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthLabel = $month->format('M Y');
            $count = $records->filter(function ($record) use ($month) {
                return $record->submission_last_date &&
                    Carbon::parse($record->submission_last_date)->format('Y-m') === $month->format('Y-m');
            })->count();
            $insights['submissions_by_month'][$monthLabel] = $count;
        }

        return $insights;
    }
}
