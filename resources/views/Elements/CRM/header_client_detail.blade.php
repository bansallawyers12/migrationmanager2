<nav class="main-topbar">
    <button class="topbar-toggle" title="Show menu" aria-label="Toggle topbar">
        <i class="fas fa-ellipsis-h"></i>
    </button>
    <div class="topbar-left">
        <div class="icon-group">
            <a href="{{route('dashboard')}}" class="icon-btn" title="Dashboard"><i class="fas fa-tachometer-alt"></i></a>
            <a href="{{ route('signatures.index') }}" class="icon-btn" title="Signature Dashboard"><i class="fas fa-pen"></i></a>
            <div class="icon-dropdown js-dropdown">
                <a href="{{ route('appointments.index') }}" class="icon-btn" title="Appointments"><i class="fas fa-calendar-alt"></i></a>
                <div class="icon-dropdown-menu">
                    <a class="dropdown-item" href="{{ route('appointments.index') }}"><i class="far fa-calendar-alt mr-2"></i> Listings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{route('appointments-others')}}"><i class="far fa-calendar-check mr-2"></i> Arun Calendar</a>
                    <a class="dropdown-item" href="{{route('appointments-jrp')}}"><i class="far fa-calendar mr-2"></i> Tr Calendar</a>
                    <a class="dropdown-item" href="{{route('appointments-education')}}"><i class="fas fa-graduation-cap mr-2"></i> Education</a>
                    <a class="dropdown-item" href="{{route('appointments-tourist')}}"><i class="fas fa-plane mr-2"></i> Tourist Visa</a>
                    <a class="dropdown-item" href="{{route('appointments-adelaide')}}"><i class="fas fa-city mr-2"></i> Adelaide</a>
                    @if(Auth::user() && (Auth::user()->role == 1 || Auth::user()->role == 12))
                    @endif
                </div>
            </div>
            <div class="icon-dropdown js-dropdown">
                <a href="{{ route('booking.appointments.index') }}" class="icon-btn" title="Website Bookings" style="position: relative;">
                    <i class="fas fa-globe"></i>
                    @php
                        $pendingCount = \App\Models\BookingAppointment::where('status', 'pending')->count();
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
                        <i class="fas fa-clock mr-2"></i> Pending
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
                    <a class="dropdown-item" href="{{route('clients.clientreceiptlist')}}"><i class="fas fa-receipt mr-2"></i> Client Receipts</a>
                    <a class="dropdown-item" href="{{route('clients.invoicelist')}}"><i class="fas fa-file-invoice-dollar mr-2"></i> Invoice Lists</a>
                    <a class="dropdown-item" href="{{route('clients.officereceiptlist')}}"><i class="fas fa-building mr-2"></i> Office Receipts</a>
                </div>
            </div>
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
        <a href="#" class="icon-btn notification-toggle" title="Notifications">
            <i class="fas fa-bell"></i><span class="countbell" id="countbell_notification"><?php echo \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->count(); ?></span>
        </a>
        @endif
        <div class="profile-dropdown js-dropdown-right">
            <a href="#" class="profile-trigger" id="profile-trigger">
                @if(Auth::user() && Auth::user()->profile_img == '')
                    <img alt="user image" src="{{ asset('img/user.png') }}" class="user-img-radious-style">
                @else
                    <img alt="{{Auth::user() ? str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...') : 'User'}}" src="{{ asset('img/user.png') }}" class="user-img-radious-style"/>
                @endif
            </a>
            <div class="profile-menu" id="profile-menu">
                <a href="{{route('my_profile')}}">
                    <i class="far fa-user"></i> 
                    <span>Profile</span>
                </a>
                @if(Auth::user() && Auth::user()->role == 1)
                <a href="{{route('adminconsole.features.matter.index')}}">
                    <i class="fas fa-cogs"></i> 
                    <span>Admin Console</span>
                </a>
                @endif
                <div class="dropdown-divider"></div>
                <a href="javascript:void(0)" class="text-danger" onclick="event.preventDefault(); document.getElementById('crm-logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> 
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<form id="crm-logout-form" action="{{ route('crm.logout') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="id" value="{{ Auth::user() ? Auth::user()->id : '' }}">
</form>
