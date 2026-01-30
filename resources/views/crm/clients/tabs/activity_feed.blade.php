<!-- Activity Feed (Only visible with Personal Details) -->
<aside class="activity-feed" id="activity-feed">
    <div class="activity-feed-header">
        <h2><i class="fas fa-history"></i> Activity Feed</h2>
        <label for="increase-activity-feed-width">
           <input type="checkbox" id="increase-activity-feed-width" title="Expand Width">
        </label>
    </div>
    
    <!-- Activity Type Filters -->
    <div class="activity-filters">
        <button class="activity-filter-btn active" data-filter="all">
            <i class="fas fa-list"></i> All
        </button>
        <button class="activity-filter-btn" data-filter="activity">
            <i class="fas fa-bolt"></i> Activity
        </button>
        <button class="activity-filter-btn" data-filter="note">
            <i class="fas fa-sticky-note"></i> Notes
        </button>
        <button class="activity-filter-btn" data-filter="document">
            <i class="fas fa-file-alt"></i> Documents
        </button>
        <button class="activity-filter-btn" data-filter="accounting">
            <i class="fas fa-dollar-sign"></i> Accounting
        </button>
    </div>
    
    <ul class="feed-list">
        @php
        // Handle search parameters
        $user_search = $_REQUEST['user'] ?? '';
        $keyword_search = $_REQUEST['keyword'] ?? '';
        
        // Query activities based on search parameters
        if ($user_search != "" || $keyword_search != "") {
            if ($user_search != "" && $keyword_search != "") {
                // Both user and keyword search
                $activities = \App\Models\ActivitiesLog::select('activities_logs.*')
                    ->leftJoin('admins', 'activities_logs.created_by', '=', 'admins.id')
                    ->where('activities_logs.client_id', $fetchedData->id)
                    ->where(function($query) use ($user_search) {
                        $userSearchLower = strtolower($user_search);
                        $query->whereRaw('LOWER(admins.first_name) LIKE ?', ['%'.$userSearchLower.'%']);
                    })
                    ->where(function($query) use ($keyword_search) {
                        $query->where('activities_logs.description', 'like', '%'.$keyword_search.'%');
                        $query->orWhere('activities_logs.subject', 'like', '%'.$keyword_search.'%');
                    })
                    ->orderby('activities_logs.created_at', 'DESC')
                    ->get();
            } else if ($user_search == "" && $keyword_search != "") {
                // Keyword search only
                $activities = \App\Models\ActivitiesLog::select('activities_logs.*')
                    ->where('activities_logs.client_id', $fetchedData->id)
                    ->where(function($query) use ($keyword_search) {
                        $query->where('activities_logs.description', 'like', '%'.$keyword_search.'%');
                        $query->orWhere('activities_logs.subject', 'like', '%'.$keyword_search.'%');
                    })
                    ->orderby('activities_logs.created_at', 'DESC')
                    ->get();
            } else if ($user_search != "" && $keyword_search == "") {
                // User search only
                $activities = \App\Models\ActivitiesLog::select('activities_logs.*','admins.first_name','admins.last_name','admins.email')
                    ->leftJoin('admins', 'activities_logs.created_by', '=', 'admins.id')
                    ->where('activities_logs.client_id', $fetchedData->id)
                    ->where(function($query) use ($user_search) {
                        $userSearchLower = strtolower($user_search);
                        $query->whereRaw('LOWER(admins.first_name) LIKE ?', ['%'.$userSearchLower.'%']);
                    })
                    ->orderby('activities_logs.created_at', 'DESC')
                    ->get();
            }
        } else {
            // No search - get all activities
            $activities = \App\Models\ActivitiesLog::where('client_id', $fetchedData->id)
                ->orderby('created_at', 'DESC')
                ->get();
        }
        @endphp
        
        @if($activities->count() > 0)
            @foreach($activities as $activit)
                @php
                    $admin = \App\Models\Admin::where('id', $activit->created_by)->first();
                @endphp
                @include('crm.clients.tabs.partials._activity_item', [
                    'activity' => $activit,
                    'admin' => $admin,
                    'clientId' => $fetchedData->id
                ])
            @endforeach
        @else
            <li class="feed-item feed-item--empty" style="text-align: center; padding: 20px; color: #6c757d;">
                <i class="fas fa-inbox" style="font-size: 2em; margin-bottom: 10px; opacity: 0.5;"></i>
                <p>No activities found</p>
            </li>
        @endif
    </ul>
</aside>

