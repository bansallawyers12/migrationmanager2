<?php

namespace App\Traits;

use App\Models\Admin;

trait ClientQueries
{
    /**
     * Get base query for clients
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getBaseClientQuery()
    {
        return Admin::where('is_archived', '=', '0')
            ->where('role', '=', '7')
            ->where('type', '=', 'client')
            ->whereNull('is_deleted');
    }

    /**
     * Get empty client query (for no access scenarios)
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getEmptyClientQuery()
    {
        return Admin::where('id', '=', '')
            ->where('role', '=', '7')
            ->whereNull('is_deleted');
    }

    /**
     * Apply search filters to client query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyClientFilters($query, $request)
    {
        if ($request->has('client_id')) {
            $client_id = $request->input('client_id');
            if (trim($client_id) != '') {
                $query->where('client_id', '=', $client_id);
            }
        }

        if ($request->has('type')) {
            $type = $request->input('type');
            if (trim($type) != '') {
                $query->where('type', 'LIKE', $type);
            }
        }

        if ($request->has('name')) {
            $name = trim($request->input('name'));
            if ($name != '') {
                $query->where(function ($q) use ($name) {
                    $q->where('first_name', 'LIKE', '%' . $name . '%')
                      ->orWhere('last_name', 'LIKE', '%' . $name . '%')
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$name}%"]);
                });
            }
        }

        if ($request->has('email')) {
            $email = $request->input('email');
            if (trim($email) != '') {
                $query->where('email', $email);
            }
        }

        if ($request->has('phone')) {
            $phone = trim($request->input('phone'));
            if ($phone != '') {
                $query->where(function ($q) use ($phone) {
                    $q->where('phone', 'LIKE', '%' . $phone . '%')
                      ->orWhere('country_code', 'LIKE', '%' . $phone . '%')
                      ->orWhereRaw("CONCAT(country_code, phone) LIKE ?", ["%{$phone}%"]);
                });
            }
        }

        return $query;
    }
}

