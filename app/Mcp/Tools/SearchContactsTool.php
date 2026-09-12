<?php

namespace App\Mcp\Tools;

use App\Models\Admin;
use App\Support\Mcp\CrmMcpAccess;
use App\Support\StaffClientVisibility;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Search CRM clients, leads, and companies by name, email, phone, CRM reference, or company ABN/name. Results are limited to records the authenticated staff can access.')]
#[IsReadOnly]
class SearchContactsTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): mixed
    {
        $staff = CrmMcpAccess::staff($request);
        if ($staff instanceof Response) {
            return $staff;
        }

        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'type' => ['nullable', 'string', 'in:client,lead,company,any'],
        ], [
            'query.required' => 'Provide a search query of at least 2 characters (name, email, phone, CRM ref, or company).',
            'type.in' => 'type must be one of: client, lead, company, any.',
        ]);

        $q = trim($validated['query']);
        $limit = (int) ($validated['limit'] ?? 20);
        $type = $validated['type'] ?? 'any';
        $like = '%'.mb_strtolower(str_replace(['%', '_'], ['\\%', '\\_'], $q)).'%';

        $query = Admin::query()
            ->with(['company:id,admin_id,company_name,ABN_number,ACN'])
            ->whereIn('type', ['client', 'lead'])
            ->where(function ($outer) use ($like) {
                $outer->whereRaw('LOWER(COALESCE(first_name, \'\')) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(last_name, \'\')) LIKE ?', [$like])
                    ->orWhereRaw("LOWER(TRIM(COALESCE(first_name, '') || ' ' || COALESCE(last_name, ''))) LIKE ?", [$like])
                    ->orWhereRaw('LOWER(COALESCE(email, \'\')) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(phone, \'\')) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(client_id, \'\')) LIKE ?', [$like])
                    ->orWhereHas('company', function ($company) use ($like) {
                        // Wrap mixed-case PG columns (ABN_number / ACN); bare whereRaw folds to lowercase and misses them.
                        $grammar = $company->getQuery()->getGrammar();
                        $abn = $grammar->wrap('ABN_number');
                        $acn = $grammar->wrap('ACN');

                        $company->whereRaw('LOWER(COALESCE(company_name, \'\')) LIKE ?', [$like])
                            ->orWhereRaw("LOWER(COALESCE({$abn}, '')) LIKE ?", [$like])
                            ->orWhereRaw("LOWER(COALESCE({$acn}, '')) LIKE ?", [$like])
                            ->orWhereRaw('LOWER(COALESCE(trading_name, \'\')) LIKE ?', [$like]);
                    });
            });

        if ($type === 'client') {
            $query->where('type', 'client')->where(function ($w) {
                $w->where('is_company', 0)->orWhereNull('is_company');
            });
        } elseif ($type === 'lead') {
            $query->where('type', 'lead');
        } elseif ($type === 'company') {
            $query->where('is_company', 1);
        }

        StaffClientVisibility::restrictAdminEloquentQuery($query);

        $rows = $query
            ->orderByDesc('updated_at')
            ->limit(min(50, max($limit * 3, $limit)))
            ->get([
                'id', 'type', 'first_name', 'last_name', 'email', 'phone', 'country_code',
                'client_id', 'is_company', 'lead_status', 'followup_date', 'user_id', 'updated_at',
            ]);

        $accessMap = StaffClientVisibility::globalSearchCanAccessMap(
            $rows->pluck('id')->all(),
            $staff
        );

        $results = [];
        foreach ($rows as $row) {
            if (! ($accessMap[(int) $row->id] ?? false)) {
                continue;
            }

            $results[] = [
                'id' => (int) $row->id,
                'type' => $row->type,
                'is_company' => (bool) $row->is_company,
                'name' => $row->company_name_or_personal_name,
                'email' => $row->email,
                'phone' => trim(($row->country_code ? '+'.$row->country_code.' ' : '').($row->phone ?? '')),
                'crm_ref' => $row->client_id,
                'lead_status' => $row->lead_status,
                'company' => $row->company ? [
                    'name' => $row->company->company_name,
                    'abn' => $row->company->ABN_number,
                    'acn' => $row->company->ACN,
                ] : null,
            ];

            if (count($results) >= $limit) {
                break;
            }
        }

        return Response::structured([
            'count' => count($results),
            'results' => $results,
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()
                ->description('Search text: person/company name, email, phone, CRM ref, or ABN.')
                ->required(),
            'limit' => $schema->integer()
                ->description('Max results (default 20, max 50).'),
            'type' => $schema->string()
                ->enum(['client', 'lead', 'company', 'any'])
                ->description('Filter by record kind (default any).'),
        ];
    }
}
