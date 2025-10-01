<nav class="navbar navbar-expand-lg main-navbar sticky">
    <div class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            
            <li>
                <form class="form-inline mr-auto">
                    <div class="search-element">
                        <select class="form-control js-data-example-ajaxccsearch" type="search" placeholder="Search" aria-label="Search" data-width="260"></select>
                        <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </li>
            <li class="nav-item d-flex align-items-center top-quick-icons">
                <a href="{{route('admin.dashboard')}}" class="nav-link nav-link-lg" title="Dashboard"><i class="fas fa-tachometer-alt"></i></a>
                <a href="{{route('admin.officevisits.waiting')}}" class="nav-link nav-link-lg" title="In Person"><i class="fas fa-user-check"></i></a>
                <a href="{{route('assignee.activities')}}" class="nav-link nav-link-lg" title="Action"><i class="fas fa-tasks"></i></a>

                <div class="dropdown">
                    <a href="#" class="nav-link nav-link-lg dropdown-toggle" data-toggle="dropdown" title="Appointments">
                        <i class="fas fa-calendar-alt"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-left pullDown">
                        <a class="dropdown-item" href="{{ route('appointments.index') }}"><i class="far fa-calendar-alt mr-2"></i> Listings</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{URL::to('/admin/appointments-others')}}"><i class="far fa-calendar-check mr-2"></i> Arun Calendar</a>
                        <a class="dropdown-item" href="{{URL::to('/admin/appointments-jrp')}}"><i class="far fa-calendar mr-2"></i> Tr Calendar</a>
                        <a class="dropdown-item" href="{{URL::to('/admin/appointments-education')}}"><i class="fas fa-graduation-cap mr-2"></i> Education</a>
                        <a class="dropdown-item" href="{{URL::to('/admin/appointments-tourist')}}"><i class="fas fa-plane mr-2"></i> Tourist Visa</a>
                        <a class="dropdown-item" href="{{URL::to('/admin/appointments-adelaide')}}"><i class="fas fa-city mr-2"></i> Adelaide</a>
                        @if(Auth::user() && (Auth::user()->role == 1 || Auth::user()->role == 12))
                        <a class="dropdown-item" href="{{route('admin.feature.appointmentdisabledate.index')}}"><i class="fas fa-ban mr-2"></i> Block Slot</a>
                        @endif
                    </div>
                </div>

                <div class="dropdown">
                    <a href="#" class="nav-link nav-link-lg dropdown-toggle" data-toggle="dropdown" title="Clients">
                        <i class="fas fa-users"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-left pullDown">
                        <a class="dropdown-item" href="{{route('admin.clients.index')}}"><i class="fas fa-list mr-2"></i> Client List</a>
                        <a class="dropdown-item" href="{{route('admin.clients.clientsmatterslist')}}"><i class="fas fa-folder-open mr-2"></i> Matter List</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{route('admin.leads.index')}}"><i class="fas fa-list-alt mr-2"></i> Lead List</a>
                        <a class="dropdown-item" href="{{route('admin.leads.create')}}"><i class="fas fa-plus-circle mr-2"></i> Add Lead</a>
                    </div>
                </div>

                <div class="dropdown">
                    <a href="#" class="nav-link nav-link-lg dropdown-toggle" data-toggle="dropdown" title="Accounts">
                        <i class="fas fa-briefcase"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-left pullDown">
                        <a class="dropdown-item" href="{{route('admin.clients.clientreceiptlist')}}"><i class="fas fa-receipt mr-2"></i> Client Receipts</a>
                        <a class="dropdown-item" href="{{route('admin.clients.invoicelist')}}"><i class="fas fa-file-invoice-dollar mr-2"></i> Invoice Lists</a>
                        <a class="dropdown-item" href="{{route('admin.clients.officereceiptlist')}}"><i class="fas fa-building mr-2"></i> Office Receipts</a>
                    </div>
                </div>

                <a href="{{route('admin.logout')}}" class="nav-link nav-link-lg text-danger" title="Logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i></a>
            </li>
        </ul>
    </div>
    <ul class="navbar-nav navbar-right" style="margin-right: 50px;">
        <li class="dropdown dropdown-list-toggle">
            <a href="javascript:;" data-toggle="dropdown" title="Add Office Check-In" class="nav-link nav-link-lg opencheckin"><i data-feather="log-in"></i></a>
        </li>
        <li class="dropdown dropdown-list-toggle">
            @if(Auth::user())
                <a href="#" class="nav-link notification-toggle nav-link-lg" data-toggle="tooltip" data-placement="bottom" title="Click To See Notifications"><i data-feather="bell" class="bell"></i><span class="countbell" id="countbell_notification"><?php echo \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->count(); ?></span></a>
            @endif
        </li>
        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                @if(Auth::user() && Auth::user()->profile_img == '')
                    <img alt="user image" src="{{ asset('img/user.png') }}" class="user-img-radious-style">
                @else
                    <img alt="{{Auth::user() ? str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...') : 'User'}}" src="{{ asset('img/user.png') }}" class="user-img-radious-style"/>
                @endif
                <span class="d-sm-none d-lg-inline-block"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
                <div class="dropdown-title">{{Auth::user() ? str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...') : 'User'}}</div>
                <a href="{{route('admin.my_profile')}}" class="dropdown-item has-icon"><i class="far fa-user"></i> Profile</a>
                @if(Auth::user() && Auth::user()->role == 1)
                <a href="{{route('admin.feature.matter.index')}}" class="dropdown-item has-icon"><i class="fas fa-cogs"></i> Admin Console</a>
                @endif
                <div class="dropdown-divider"></div>
                <a href="{{route('admin.logout')}}" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </li>
    </ul>
</nav>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="id" value="{{ Auth::user() ? Auth::user()->id : '' }}">
</form>
