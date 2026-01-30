<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\UserLog;
use App\Models\Team;
use App\Models\Branch;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ActiveUserService
{
    /**
     * Fetch active users leveraging session and login logs.
     *
     * @param  int  $onlineThresholdMinutes
     * @param  string|null  $search
     * @param  int|null  $roleId
     * @param  int|null  $teamId
     * @param  string|null  $sortBy
     * @param  string  $sortDir
     * @param  int  $perPage
     * @param  int  $page
     * @return array{data: Collection, meta: array}
     */
    public function getActiveUsers(
        int $onlineThresholdMinutes = 5,
        ?string $search = null,
        ?int $roleId = null,
        ?int $teamId = null,
        ?string $sortBy = 'name',
        string $sortDir = 'asc',
        int $perPage = 15,
        int $page = 1
    ): array {
        $cacheKey = "active_users_{$onlineThresholdMinutes}_" . md5(json_encode([$search, $roleId, $teamId, $sortBy, $sortDir]));

        $result = Cache::remember($cacheKey, 30, function () use ($onlineThresholdMinutes, $search, $roleId, $teamId, $sortBy, $sortDir) {
            $threshold = Carbon::now()->subMinutes($onlineThresholdMinutes)->timestamp;

            $sessions = DB::table('sessions')
                ->select(['user_id', 'last_activity'])
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', $threshold)
                ->get()
                ->groupBy(fn ($session) => (int) $session->user_id)
                ->map(function ($records) {
                    $latest = $records->max('last_activity');

                    return [
                        'last_activity' => Carbon::createFromTimestamp((int) $latest),
                    ];
                });

            $adminIds = $sessions->keys();

            if ($adminIds->isEmpty()) {
                return collect();
            }

            $query = Admin::query()
                ->select(['id', 'first_name', 'last_name', 'email', 'role', 'team', 'office_id', 'profile_img', 'updated_at'])
                ->whereIn('id', $adminIds)
                ->with(['usertype']);

            // Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $searchLower = strtolower($search);
                    $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$searchLower}%"])
                      ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$searchLower}%"])
                      ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchLower}%"]);
                });
            }

            // Apply role filter
            if ($roleId) {
                $query->where('role', $roleId);
            }

            // Apply team filter
            if ($teamId) {
                $query->where('team', $teamId);
            }

            // Apply sorting
            $allowedSorts = ['name', 'role', 'team', 'last_activity'];
            if (in_array($sortBy, $allowedSorts)) {
                if ($sortBy === 'name') {
                    $query->orderBy('first_name', $sortDir)->orderBy('last_name', $sortDir);
                } else {
                    $query->orderBy($sortBy, $sortDir);
                }
            } else {
                $query->orderBy('first_name', 'asc');
            }

            $admins = $query->get();

            // Get team and office data
            $teamIds = $admins->pluck('team')->filter()->unique();
            $officeIds = $admins->pluck('office_id')->filter()->unique();

            $teams = Team::whereIn('id', $teamIds)->get()->keyBy('id');
            $offices = Branch::whereIn('id', $officeIds)->get()->keyBy('id');

            $mapped = $admins->map(function (Admin $admin) use ($sessions, $teams, $offices) {
                $session = $sessions->get($admin->id);
                $lastLogin = $this->resolveLastLogin($admin->id, $admin->updated_at);
                $team = $admin->team ? $teams->get($admin->team) : null;
                $office = $admin->office_id ? $offices->get($admin->office_id) : null;
                $roleName = $admin->usertype ? $admin->usertype->name : null;

                return [
                    'id' => $admin->id,
                    'name' => trim("{$admin->first_name} {$admin->last_name}") ?: $admin->email,
                    'first_name' => $admin->first_name,
                    'last_name' => $admin->last_name,
                    'email' => $admin->email,
                    'profile_img' => $admin->profile_img,
                    'role_id' => $admin->role,
                    'role_name' => $roleName,
                    'team_id' => $admin->team,
                    'team_name' => $team ? $team->name : null,
                    'team_color' => $team ? $team->color : null,
                    'office_id' => $admin->office_id,
                    'office_name' => $office ? $office->office_name : null,
                    'status' => 'online',
                    'last_activity' => $session ? $session['last_activity']->toIso8601String() : null,
                    'last_activity_timestamp' => $session ? $session['last_activity']->timestamp : null,
                    'last_login' => $lastLogin?->toIso8601String(),
                ];
            });

            // Apply client-side sorting for last_activity if needed
            if ($sortBy === 'last_activity') {
                $mapped = $mapped->sortBy(function ($user) {
                    return $user['last_activity_timestamp'] ?? 0;
                }, SORT_REGULAR, $sortDir === 'desc');
            }

            return $mapped->values();
        });

        // Apply pagination
        $total = $result->count();
        $offset = ($page - 1) * $perPage;
        $paginated = $result->slice($offset, $perPage)->values();

        return [
            'data' => $paginated,
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'threshold_minutes' => $onlineThresholdMinutes,
                'generated_at' => now()->toIso8601String(),
            ],
        ];
    }

    /**
     * Resolve the most recent login timestamp for a user.
     */
    protected function resolveLastLogin(int $userId, ?Carbon $fallback): ?Carbon
    {
        $userLog = UserLog::query()
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->where('message', 'like', '%Logged in%')
                    ->orWhere('message', 'like', '%Logged in successfully%');
            })
            ->latest('created_at')
            ->first();

        if ($userLog) {
            return Carbon::parse($userLog->created_at);
        }

        return $fallback ? Carbon::parse($fallback) : null;
    }
}


