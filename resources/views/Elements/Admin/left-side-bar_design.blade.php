<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#"><span class="logo-name">CRM</span></a>
        </div>
        <ul class="sidebar-menu">
            <?php
            $roles = \App\Models\UserRole::find(Auth::user()->role);
            $newarray = json_decode($roles->module_access);
            $module_access = (array) $newarray;
            ?>
            <li class="menu-header">Main</li>
            <?php
            if(Route::currentRouteName() == 'admin.dashboard'){
                $dashclasstype = 'active';
            }
            ?>
            <li class="dropdown {{@$dashclasstype}}">
                <a href="{{route('admin.dashboard')}}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
            </li>
            <?php
            if(Route::currentRouteName() == 'appointments.index' || Route::currentRouteName() == 'appointments-education' || Route::currentRouteName() == 'appointments-jrp' || Route::currentRouteName() == 'appointments-tourist' || Route::currentRouteName() == 'appointments-others'){
                $appointmentsclasstype = 'active';
            }
            ?>
            <li class="dropdown {{@$appointmentsclasstype}}">
                <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="file-text"></i><span>Appointments</span></a>
                <ul class="dropdown-menu">
                    <li class=""><a class="nav-link" href="{{ route('appointments.index') }}">Listings</a></li>
                    <li class="dropdown {{@$appointmentsclasstype}}">
                        <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="calendar"></i><span>Calendars</span></a>
                        <ul class="dropdown-menu">
                            <li class="{{(Route::currentRouteName() == 'appointments-others') ? 'active' : ''}}"><a class="nav-link" href="{{URL::to('/admin/appointments-others')}}">User1 Calendar</a></li>
                            <li class="{{(Route::currentRouteName() == 'appointments-jrp') ? 'active' : ''}}"><a class="nav-link" href="{{URL::to('/admin/appointments-jrp')}}">User2 Calendar</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <?php
            if(Route::currentRouteName() == 'assignee.activities' || Route::currentRouteName() == 'assignee.activities_completed'){
                $assigneetype = 'active';
            }
            if(\Auth::user()->role == 1){
                $assigneesCount = \App\Models\Note::where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->count();
            }else{
                $assigneesCount = \App\Models\Note::where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->count();
            }
            ?>
            <li class="dropdown {{@$assigneetype}}">
                <a href="{{route('assignee.activities')}}" class="nav-link">
                    <i data-feather="check"></i>
                    <span>Action<span class="countTotalActivityAction" style="background: #1f1655; padding: 0px 5px; border-radius: 50%; color: #fff; margin-left: 5px;">{{ $assigneesCount }}</span></span>
                </a>
            </li>
            <?php
            if(Route::currentRouteName() == 'admin.clients.index' || Route::currentRouteName() == 'admin.clients.create' || Route::currentRouteName() == 'admin.clients.edit' || Route::currentRouteName() == 'admin.clients.detail' || Route::currentRouteName() == 'admin.clients.invoicelist' || Route::currentRouteName() == 'admin.leads.index' || Route::currentRouteName() == 'admin.leads.create' || Route::currentRouteName() == 'admin.leads.edit' || Route::currentRouteName() == 'admin.leads.history' || Route::currentRouteName() == 'admin.clients.clientsmatterslist'){
                $clientmanagerclasstype = 'active';
            }
            ?>
            <?php if(array_key_exists('21', $module_access)) { ?>
            <li class="dropdown {{@$clientmanagerclasstype}}">
                <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="user"></i><span>Clients Manager</span></a>
                <ul class="dropdown-menu">
                    @if(Auth::user()->role == 1)
                    <li class="{{(Route::currentRouteName() == 'admin.clients.index') ? 'active' : ''}}">
                        <a href="{{route('admin.clients.index')}}" class="nav-link"><i data-feather="file-text"></i><span>Client Lists</span></a>
                    </li>
                    <li class="{{(Route::currentRouteName() == 'admin.clients.clientsmatterslist') ? 'active' : ''}}">
                        <a href="{{route('admin.clients.clientsmatterslist')}}" class="nav-link"><i data-feather="file-text"></i><span>Matter Lists</span></a>
                    </li>
                    @endif
                    <li class="{{(Route::currentRouteName() == 'admin.leads.create') ? 'active' : ''}}">
                        <a href="{{route('admin.leads.create')}}" class="nav-link"><i data-feather="file-text"></i><span>Add Lead</span></a>
                    </li>
                </ul>
            </li>
            <?php } ?>
            <?php
            if(Auth::user()->role == 1){
                if(Route::currentRouteName() == 'admin.clients.invoicelist' || Route::currentRouteName() == 'admin.clients.clientreceiptlist' || Route::currentRouteName() == 'admin.clients.officereceiptlist' || Route::currentRouteName() == 'admin.clients.journalreceiptlist'){
                    $clientaccountmanagerclasstype = 'active';
                }
            ?>
            <li class="dropdown {{@$clientaccountmanagerclasstype}}">
                <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="file-text"></i><span>Account Manager</span></a>
                <ul class="dropdown-menu">
                    <li class="{{(Route::currentRouteName() == 'admin.clients.invoicelist') ? 'active' : ''}}">
                        <a href="{{route('admin.clients.invoicelist')}}" class="nav-link"><i data-feather="file-text"></i><span>Invoice Lists</span></a>
                    </li>
                    <li class="{{(Route::currentRouteName() == 'admin.clients.clientreceiptlist') ? 'active' : ''}}">
                        <a href="{{route('admin.clients.clientreceiptlist')}}" class="nav-link"><i data-feather="file-text"></i><span>Client Receipts</span></a>
                    </li>
                    <li class="{{(Route::currentRouteName() == 'admin.clients.officereceiptlist') ? 'active' : ''}}">
                        <a href="{{route('admin.clients.officereceiptlist')}}" class="nav-link"><i data-feather="file-text"></i><span>Office Receipts</span></a>
                    </li>
                    <li class="{{(Route::currentRouteName() == 'admin.clients.journalreceiptlist') ? 'active' : ''}}">
                        <a href="{{route('admin.clients.journalreceiptlist')}}" class="nav-link"><i data-feather="file-text"></i><span>Journal Receipts</span></a>
                    </li>
                </ul>
            </li>
            <?php } ?>
            <?php
            if(Route::currentRouteName() == 'admin.auditlogs.index'){
                $auditlogsclasstype = 'active';
            }
            ?>
            @if(Auth::user()->role === 1)
            <li class="dropdown {{@$auditlogsclasstype}}">
                <a href="{{route('admin.auditlogs.index')}}" class="nav-link"><i data-feather="log-in"></i><span>Login Report</span></a>
            </li>
            @endif
            <li class="dropdown">
                <a href="{{route('admin.logout')}}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i data-feather="log-out"></i><span>Logout</span></a>
                {{ Form::open(array('url' => 'admin/logout', 'name'=>'admin_login', 'id' => 'logout-form')) }}
                <input type="hidden" name="id" value="{{Auth::user()->id}}">
                {{ Form::close() }}
            </li>
        </ul>
    </aside>
</div>
