<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Matter;
use App\Models\AnzscoOccupation;
use Illuminate\Http\Request;

class ClientPortalCommonListingController extends BaseController
{
    /**
     * Get list of all countries
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountries(Request $request)
    {
        try {
            // Get optional query parameters
            $status = $request->query('status', null);

            // Build query
            $query = Country::query();

            // Filter by status if provided
            if ($status !== null) {
                $query->where('status', $status);
            }

            // Get all countries
            $countries = $query->get();

            // Separate India and Australia from the rest
            $india = null;
            $australia = null;
            $otherCountries = [];

            foreach ($countries as $country) {
                if ($country->name === 'India') {
                    $india = $country;
                } elseif ($country->name === 'Australia') {
                    $australia = $country;
                } else {
                    $otherCountries[] = $country;
                }
            }

            // Sort other countries alphabetically by name
            usort($otherCountries, function ($a, $b) {
                return strcmp($a->name, $b->name);
            });

            // Combine: India first, then Australia, then others
            $sortedCountries = [];
            if ($india) {
                $sortedCountries[] = $india;
            }
            if ($australia) {
                $sortedCountries[] = $australia;
            }
            $sortedCountries = array_merge($sortedCountries, $otherCountries);

            // Format response - only name
            $result = array_map(function ($country) {
                return [
                    'name' => $country->name,
                ];
            }, $sortedCountries);

            return $this->sendResponse($result, 'Countries retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get list of visa types
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVisaTypes(Request $request)
    {
        try {
            // Get visa types from Matter table
            $visaTypes = Matter::select('id', 'title', 'nick_name')
                ->where('title', 'NOT LIKE', '%skill assessment%')
                ->where('status', 1)
                ->orderBy('title', 'ASC')
                ->get();

            // Format response
            $result = $visaTypes->map(function ($visaType) {
                return [
                    'id' => $visaType->id,
                    'title' => $visaType->title,
                    'nick_name' => $visaType->nick_name,
                ];
            });

            return $this->sendResponse($result, 'Visa types retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Search occupations — aligned with CRM-style response: paginated `data` items with only
     * `anzsco_code` and `occupation_title`, plus count/total/page/per_page/last_page/search_query.
     *
     * Requires `q` with at least 2 characters. Optional `limit` and `page` for pagination.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchOccupationDetail(Request $request)
    {
        try {
            $q = trim((string) $request->input('q', ''));
            $perPage = min(max(1, (int) $request->input('limit', 20)), 50);
            $page = max(1, (int) $request->input('page', 1));

            if (strlen($q) < 2) {
                return $this->sendError(
                    'Search query (q) must be at least 2 characters.',
                    [],
                    422
                );
            }

            $occupationsQuery = AnzscoOccupation::active()
                ->search($q)
                ->orderBy('occupation_title')
                ->orderBy('anzsco_code');

            $total = (clone $occupationsQuery)->count();
            $rows = $occupationsQuery
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get(['anzsco_code', 'occupation_title']);

            $data = $rows->map(function (AnzscoOccupation $occ) {
                return [
                    'anzsco_code' => $occ->anzsco_code,
                    'occupation_title' => $occ->occupation_title,
                ];
            })->values()->all();

            $lastPage = $total === 0 ? 1 : (int) ceil($total / $perPage);

            return response()->json([
                'success' => true,
                'message' => 'Occupations retrieved successfully',
                'data' => $data,
                'count' => count($data),
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'last_page' => $lastPage,
                'search_query' => $q,
            ], 200);

        } catch (\Exception $e) {
            return $this->sendError('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Full occupation record by ANZSCO code — CRM-style: list_label, is_csol, visa_options, etc.
     * Query: `occupation_code` (required).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOccupationResult(Request $request)
    {
        try {
            $code = trim((string) $request->input('occupation_code', ''));

            if ($code === '') {
                return $this->sendError('occupation_code is required', [], 422);
            }

            $occupation = AnzscoOccupation::active()
                ->where('anzsco_code', $code)
                ->first();

            if (! $occupation) {
                return $this->sendError('Occupation not found', [], 404);
            }

            return $this->sendResponse(
                $this->formatOccupationCrmDetail($occupation),
                'Occupation retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->sendError('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Primary list label for display (matches CRM single-label style).
     */
    private function primaryListLabel(AnzscoOccupation $occ): string
    {
        if ($occ->is_on_mltssl) {
            return 'MLTSSL';
        }
        if ($occ->is_on_stsol) {
            return 'STSOL';
        }
        if ($occ->is_on_rol) {
            return 'ROL';
        }
        if ($occ->is_on_csol) {
            return 'CSOL';
        }

        return '';
    }

    /**
     * Visa eligibility blocks derived from `anzsco_occupations` list flags (CRM-style shape).
     *
     * @return array<string, array<string, mixed>>
     */
    private function buildVisaOptions(AnzscoOccupation $occ): array
    {
        $mltssl = (bool) $occ->is_on_mltssl;
        $stsol = (bool) $occ->is_on_stsol;
        $rol = (bool) $occ->is_on_rol;
        $csol = (bool) $occ->is_on_csol;

        $listFlags = [
            'MLTSSL' => $mltssl,
            'STSOL' => $stsol,
            'ROL' => $rol,
            'CSOL' => $csol,
        ];

        $block = function (
            string $visaType,
            string $visaName,
            bool $eligibility,
            bool $permanentVisa,
            string $dhaPath
        ) use ($listFlags) {
            return array_merge([
                'visa_type' => $visaType,
                'visa_name' => $visaName,
                'eligibility' => $eligibility,
            ], $listFlags, [
                'permanent_visa' => $permanentVisa,
                'dha_path' => $dhaPath,
            ]);
        };

        return [
            'eligible_189' => $block('189', 'Skilled Independent', $mltssl, true, '/skilled-189/skilled-independent'),
            'eligible_190' => $block('190', 'Skilled Nominated', $mltssl || $stsol, true, '/skilled-190/skilled-nominated'),
            'eligible_491' => $block('491', 'Skilled Work Regional (Provisional)', $rol || $mltssl || $stsol, false, '/skilled-491/skilled-work-regional'),
            'eligible_482' => $block('482', 'Skills in Demand visa (TSS)', $csol || $mltssl || $stsol, false, '/skilled-482/skills-in-demand'),
            'eligible_186' => $block('186', 'Employer Nomination Scheme', $mltssl || $csol, true, '/skilled-186/employer-nomination'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatOccupationCrmDetail(AnzscoOccupation $occ): array
    {
        return [
            'anzsco_code' => $occ->anzsco_code,
            'occupation_title' => $occ->occupation_title,
            'list_label' => $this->primaryListLabel($occ),
            'assessing_authority' => $occ->assessing_authority,
            'skill_level' => $occ->skill_level,
            'is_csol' => (bool) $occ->is_on_csol,
            'visa_options' => $this->buildVisaOptions($occ),
            'assessment_validity_years' => $occ->assessment_validity_years,
            'alternate_titles' => $occ->alternate_titles,
            'additional_info' => $occ->additional_info,
        ];
    }
}

