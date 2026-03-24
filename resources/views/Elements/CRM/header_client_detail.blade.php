<nav class="main-topbar">
    <button class="topbar-toggle" title="Show menu" aria-label="Toggle topbar">
        <i class="fas fa-ellipsis-h"></i>
    </button>
    <div class="topbar-left">
        <div class="icon-group">
            <a href="{{route('dashboard')}}" class="icon-btn" title="Dashboard"><i class="fas fa-tachometer-alt"></i></a>
            <a href="{{ route('signatures.index') }}" class="icon-btn" title="Signature Dashboard"><i class="fas fa-pen"></i></a>
            <div class="icon-dropdown js-dropdown">
                <a href="{{ route('booking.appointments.index') }}" class="icon-btn" title="Website Bookings" style="position: relative;">
                    <i class="fas fa-globe"></i>
                    @php
                        $pendingCount = \App\Models\BookingAppointment::where('status', 'pending')->where('is_paid', 1)->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="badge badge-danger" style="position: absolute; top: -5px; right: -5px; font-size: 10px; padding: 2px 5px; border-radius: 10px;">{{ $pendingCount }}</span>
                    @endif
                </a>
                <div class="icon-dropdown-menu">
                    <a class="dropdown-item" href="{{ route('booking.appointments.index') }}">
                        <i class="fas fa-list mr-2"></i> All Bookings
                    </a>
                    <a class="dropdown-item" href="{{ route('booking.appointments.index', ['status' => 'pending']) }}">
                        <i class="fas fa-clock mr-2"></i> Payment Pending
                        @if($pendingCount > 0)
                            <span class="badge badge-warning ml-1">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('booking.appointments.calendar', ['type' => 'paid']) }}">
                        <i class="far fa-calendar-check mr-2"></i> Pr_complex matters
                    </a>
                    <a class="dropdown-item" href="{{ route('booking.appointments.calendar', ['type' => 'jrp']) }}">
                        <i class="far fa-calendar mr-2"></i> JRP Calendar
                    </a>
                    <a class="dropdown-item" href="{{ route('booking.appointments.calendar', ['type' => 'education']) }}">
                        <i class="fas fa-graduation-cap mr-2"></i> Education
                    </a>
                    <a class="dropdown-item" href="{{ route('booking.appointments.calendar', ['type' => 'tourist']) }}">
                        <i class="fas fa-plane mr-2"></i> Tourist Visa
                    </a>
                    <a class="dropdown-item" href="{{ route('booking.appointments.calendar', ['type' => 'adelaide']) }}">
                        <i class="fas fa-city mr-2"></i> Adelaide
                    </a>
                    <a class="dropdown-item" href="{{ route('booking.appointments.calendar', ['type' => 'ajay']) }}">
                        <i class="fas fa-calendar-alt mr-2"></i> Ajay Calendar
                    </a>
                    <a class="dropdown-item" href="{{ route('booking.appointments.calendar', ['type' => 'kunal']) }}">
                        <i class="fas fa-calendar-alt mr-2"></i> Kunal Calendar
                    </a>
                    @if(Auth::user() && in_array(Auth::user()->role, [1, 12]))
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('booking.sync.dashboard') }}">
                        <i class="fas fa-sync mr-2"></i> Sync Status
                    </a>
                    @endif
                </div>
            </div>
            <a href="{{route('officevisits.waiting')}}" class="icon-btn" title="In Person"><i class="fas fa-user-check"></i></a>
            <a href="{{route('assignee.action')}}" class="icon-btn" title="Action"><i class="fas fa-tasks"></i></a>
            <a href="{{ route('notifications.broadcasts.index') }}" class="icon-btn" title="Broadcasts">
                <i class="fas fa-bullhorn"></i>
            </a>
            <div class="icon-dropdown js-dropdown">
                <a href="{{route('clients.index')}}" class="icon-btn" title="Clients"><i class="fas fa-users"></i></a>
                <div class="icon-dropdown-menu">
                    <a class="dropdown-item" href="{{route('clients.index')}}"><i class="fas fa-list mr-2"></i> Client List</a>
                    <a class="dropdown-item" href="{{route('clients.clientsmatterslist')}}"><i class="fas fa-folder-open mr-2"></i> Matter List</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{route('leads.index')}}"><i class="fas fa-list-alt mr-2"></i> Lead List</a>
                    <a class="dropdown-item" href="{{route('leads.create')}}"><i class="fas fa-plus-circle mr-2"></i> Add Lead</a>
                </div>
            </div>
            <div class="icon-dropdown js-dropdown">
                <a href="{{route('clients.invoicelist')}}" class="icon-btn" title="Accounts"><i class="fas fa-briefcase"></i></a>
                <div class="icon-dropdown-menu">
                    @if(Auth::user() && in_array(Auth::user()->role, [1, 12]))
                    <a class="dropdown-item" href="{{route('clients.analytics-dashboard')}}" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); font-weight: 600;"><i class="fas fa-chart-line mr-2" style="color: #667eea;"></i> Analytics Dashboard</a>
                    <div class="dropdown-divider"></div>
                    @endif
                    <a class="dropdown-item" href="{{route('clients.clientreceiptlist')}}"><i class="fas fa-receipt mr-2"></i> Client Receipts</a>
                    <a class="dropdown-item" href="{{route('clients.invoicelist')}}"><i class="fas fa-file-invoice-dollar mr-2"></i> Invoice Lists</a>
                    <a class="dropdown-item" href="{{route('clients.officereceiptlist')}}"><i class="fas fa-building mr-2"></i> Office Receipts</a>
                    <a class="dropdown-item" href="{{route('clients.journalreceiptlist')}}"><i class="fas fa-book mr-2"></i> Journal Receipts</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{route('reports.visaexpires')}}"><i class="fas fa-calendar-times mr-2"></i> Visa Expiry Report</a>
                </div>
            </div>
            @php
                $u = Auth::user();
                $visibleCrmSheets = ($u && $u instanceof \App\Models\Staff)
                    ? $u->visibleCrmSheetMenuItems()
                    : [];
                $firstSheetKey = $visibleCrmSheets === [] ? null : array_key_first($visibleCrmSheets);
            @endphp
            @if($firstSheetKey !== null)
            <div class="icon-dropdown js-dropdown">
                <a href="{{ \App\Support\CrmSheets::urlForKey($firstSheetKey) }}" class="icon-btn" title="Sheets"><i class="fas fa-table"></i></a>
                <div class="icon-dropdown-menu">
                    @foreach($visibleCrmSheets as $vt => $vc)
                    <a class="dropdown-item" href="{{ \App\Support\CrmSheets::urlForKey($vt) }}"><i class="fas fa-{{ $vt === 'eoi-roi' ? 'passport' : ($vt === 'art' ? 'gavel' : 'clipboard-list') }} mr-2"></i> {{ $vc }}</a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="topbar-center">
        <form class="topbar-search">
            <i class="fas fa-search"></i>
            <select class="form-control js-data-example-ajaxccsearch" type="search" placeholder="Search" aria-label="Search" data-width="320"></select>
        </form>
    </div>
    <div class="topbar-right">
        <a href="javascript:;" title="Add Office Check-In" class="icon-btn opencheckin"><i class="fas fa-person-booth"></i></a>
        @if(Auth::user())
            @php
                $headerStaff = Auth::user();
                $crmAccessIsApprover = $headerStaff instanceof \App\Models\Staff
                    && app(\App\Services\CrmAccess\CrmAccessService::class)->isApprover($headerStaff);
                $crmAccessPendingSupervisor = $crmAccessIsApprover
                    ? \App\Models\ClientAccessGrant::query()
                        ->where('status', 'pending')
                        ->where('grant_type', 'supervisor_approved')
                        ->count()
                    : 0;
                $notifUnread = \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->count();
            @endphp
            @if($crmAccessIsApprover)
            <div class="icon-dropdown js-dropdown" id="crm-access-notification-dropdown" style="position:relative;">
                <a href="#" class="icon-btn notification-toggle" title="Notifications" style="position:relative;">
                    <i class="fas fa-bell"></i>
                    <span class="countbell" id="countbell_notification">{{ $notifUnread > 0 ? $notifUnread : '' }}</span>
                    @if($crmAccessPendingSupervisor > 0)
                        <span class="badge badge-warning" style="position:absolute;top:-6px;right:-6px;font-size:9px;padding:2px 4px;border-radius:8px;" title="Pending access approvals">{{ $crmAccessPendingSupervisor }}</span>
                    @endif
                </a>
                <div class="icon-dropdown-menu" style="min-width:300px;max-width:400px;max-height:420px;overflow:auto;left:auto;right:0;">
                    <div class="px-3 py-2 border-bottom font-weight-bold small">Access approvals</div>
                    <div id="crm-access-mini-queue" class="px-2 py-2 small text-muted">Loading…</div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('crm.access.queue') }}"><i class="fas fa-inbox mr-2"></i>Full access queue</a>
                    <a class="dropdown-item" href="{{ route('crm.access.dashboard') }}"><i class="fas fa-chart-bar mr-2"></i>Grants dashboard</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/all-notifications"><i class="fas fa-bell mr-2"></i>All notifications</a>
                </div>
            </div>
            @once
            @push('scripts')
            <script>
            (function () {
                var miniUrl = @json(route('crm.access.queue.mini'));
                var reasonLabels = @json(config('crm_access.quick_reason_options', []));
                var approveTpl = @json(str_replace('999999999', '__ID__', route('crm.access.approve', ['grant' => 999999999])));
                var rejectTpl = @json(str_replace('999999999', '__ID__', route('crm.access.reject', ['grant' => 999999999])));
                var token = document.querySelector('meta[name="csrf-token"]');
                token = token ? token.getAttribute('content') : '';

                function renderMini() {
                    var box = document.getElementById('crm-access-mini-queue');
                    if (!box) return;
                    box.innerHTML = 'Loading…';
                    fetch(miniUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            var items = data.items || [];
                            if (items.length === 0) {
                                box.innerHTML = '<span class="text-muted">No pending supervisor requests.</span>';
                                return;
                            }
                            var html = '';
                            items.forEach(function (g) {
                                var req = g.staff ? (g.staff.first_name + ' ' + g.staff.last_name).trim() : ('#' + g.staff_id);
                                var rec = g.admin ? (g.admin.first_name + ' ' + g.admin.last_name).trim() : ('#' + g.admin_id);
                                var rc = g.quick_reason_code || '';
                                var reasonTxt = rc && reasonLabels[rc] ? String(reasonLabels[rc]).replace(/</g, '&lt;') : '';
                                var note = g.requester_note ? String(g.requester_note).replace(/</g, '&lt;').slice(0, 120) : '';
                                var detail = '';
                                if (reasonTxt && note) {
                                    detail = reasonTxt + ' · ' + note;
                                } else {
                                    detail = reasonTxt || note;
                                }
                                html += '<div class="border rounded p-2 mb-2 bg-light" data-grant-mini="' + g.id + '">' +
                                    '<div class="font-weight-bold">' + rec + ' <span class="text-muted font-weight-normal">(' + g.record_type + ' #' + g.admin_id + ')</span></div>' +
                                    '<div class="text-muted" style="font-size:11px;">' + (g.requested_at || '') + ' · ' + req + '</div>' +
                                    (detail ? '<div class="mt-1" style="font-size:11px;">' + detail + '</div>' : '') +
                                    '<div class="mt-2">' +
                                    '<button type="button" class="btn btn-sm btn-success py-0 px-2 js-cag-mini-approve" data-id="' + g.id + '">Approve</button> ' +
                                    '<button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 js-cag-mini-reject" data-id="' + g.id + '">Reject</button>' +
                                    '</div></div>';
                            });
                            box.innerHTML = html;
                        })
                        .catch(function () {
                            box.innerHTML = '<span class="text-danger">Could not load access queue.</span>';
                        });
                }

                document.addEventListener('click', function (e) {
                    if (e.target.matches('.js-cag-mini-approve')) {
                        var id = e.target.getAttribute('data-id');
                        fetch(approveTpl.replace('__ID__', id), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: '{}'
                        }).then(function () { renderMini(); });
                    }
                    if (e.target.matches('.js-cag-mini-reject')) {
                        var id2 = e.target.getAttribute('data-id');
                        var reason = window.prompt('Reject reason (optional):') || '';
                        fetch(rejectTpl.replace('__ID__', id2), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ reason: reason })
                        }).then(function () { renderMini(); });
                    }
                });

                document.addEventListener('click', function (e) {
                    var wrap = document.getElementById('crm-access-notification-dropdown');
                    if (!wrap || !wrap.classList.contains('js-dropdown')) return;
                    var trigger = e.target.closest('#crm-access-notification-dropdown > .notification-toggle');
                    if (!trigger) return;
                    setTimeout(renderMini, 80);
                });
            })();
            </script>
            @endpush
            @endonce
            @else
            <a href="#" class="icon-btn notification-toggle" title="Notifications">
                <i class="fas fa-bell"></i><span class="countbell" id="countbell_notification"><?php echo $notifUnread > 0 ? $notifUnread : ''; ?></span>
            </a>
            @endif
        @endif
        <div class="profile-dropdown js-dropdown-right">
            <a href="#" class="profile-trigger" id="profile-trigger">
                <img alt="{{ Auth::user() ? Str::limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...') : 'Staff' }}" src="{{ Auth::user() ? Auth::user()->profile_img : asset('img/avatar.png') }}" class="user-img-radious-style"/>
            </a>
            <div class="profile-menu" id="profile-menu">
                <a href="{{route('my_profile')}}">
                    <i class="far fa-user"></i> 
                    <span>Profile</span>
                </a>
                @if(Auth::user() && in_array(Auth::user()->role, [1, 12]))
                <a href="{{route('adminconsole.features.matter.index')}}">
                    <i class="fas fa-cogs"></i> 
                    <span>Admin Console</span>
                </a>
                @endif
                <div class="dropdown-divider"></div>
                <a href="javascript:void(0)" class="text-danger dropdown-item" data-logout="all">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log out everywhere</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<form id="crm-logout-form" action="{{ route('crm.logout') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="id" value="{{ Auth::user() ? Auth::user()->id : '' }}">
</form>
