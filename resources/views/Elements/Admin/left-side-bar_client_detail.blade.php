<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#"><span class="logo-name">CRM</span></a>
        </div>
        <ul class="sidebar-menu">
            <?php
            if(Auth::check()) {
                $roles = \App\Models\UserRole::find(Auth::user()->role);
                $newarray = json_decode($roles->module_access);
                $module_access = (array) $newarray;
            } else {
                $module_access = [];
            }
            ?>
            <li class="menu-header">Main</li>
            <?php
            if(Route::currentRouteName() == 'admin.dashboard'){
                $dashclasstype = 'active';
            }
            ?>
            <li class="dropdown {{@$dashclasstype}}">
                <a href="{{route('admin.dashboard')}}" class="nav-link" title="Dashboard"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            </li>


            <?php
            //dd(Route::currentRouteName());
            if( Route::currentRouteName() == 'appointments.index' || Route::currentRouteName() == 'appointments-education'  || Route::currentRouteName() == 'appointments-jrp' || Route::currentRouteName() == 'appointments-tourist' || Route::currentRouteName() == 'appointments-others' || Route::currentRouteName() == 'admin.feature.appointmentdisabledate.index' ){
				$appointmentsclasstype = '';
			}
			?>
			<li class="dropdown {{@$appointmentsclasstype}}">
				<a href="#" class="menu-toggle nav-link has-dropdown"><i class="fas fa-calendar-alt"></i><span>Appointments</span></a>
				<ul class="dropdown-menu">
				    <li class=""><a class="nav-link" href="{{ route('appointments.index') }}">Listings</a></li>
                    <li class="{{(Route::currentRouteName() == 'appointments-others') ? 'active' : ''}}"><a class="nav-link" href="{{URL::to('/admin/appointments-others')}}">Arun Calendar</a></li>
                    <li class="{{(Route::currentRouteName() == 'appointments-jrp') ? 'active' : ''}}"><a class="nav-link" href="{{URL::to('/admin/appointments-jrp')}}">Tr Calendar</a></li>
                    <li class="{{(Route::currentRouteName() == 'appointments-education') ? 'active' : ''}}"><a class="nav-link" href="{{URL::to('/admin/appointments-education')}}">Education</a></li>
                    <li class="{{(Route::currentRouteName() == 'appointments-tourist') ? 'active' : ''}}"><a class="nav-link" href="{{URL::to('/admin/appointments-tourist')}}">Tourist visa</a></li>

                    <li class="{{(Route::currentRouteName() == 'appointments-adelaide') ? 'active' : ''}}"><a class="nav-link" href="{{URL::to('/admin/appointments-adelaide')}}">Adelaide Calendar</a></li>

                    <?php
                    if( Auth::user()->role == 1 || Auth::user()->role == 12 ){ //super admin or admin
                    ?>
                    <li class="{{(Route::currentRouteName() == 'admin.feature.appointmentdisabledate.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.appointmentdisabledate.index')}}">Block Slot</a></li>
                    <?php } ?>

				</ul>
			</li>


            <?php
            if(Route::currentRouteName() == 'admin.officevisits.index' || Route::currentRouteName() == 'admin.officevisits.waiting' || Route::currentRouteName() == 'admin.officevisits.attending' || Route::currentRouteName() == 'admin.officevisits.completed' || Route::currentRouteName() == 'admin.officevisits.archived'){
				$checlasstype = 'active';
			}
            $InPersonwaitingCount = \App\Models\CheckinLog::where('status',0)->count();
            ?>
			<li class="dropdown {{@$checlasstype}}">
				<a href="{{route('admin.officevisits.waiting')}}" class="nav-link" title="In Person" style="position: relative;">
					<i class="fas fa-user-check"></i>
					<span>In Person</span>
					@if($InPersonwaitingCount > 0)
						<span class="countInPersonWaitingAction" style="background: #1f1655; padding: 2px 6px; border-radius: 50%; color: #fff; font-size: 11px; min-width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; line-height: 1; position: absolute; top: 25px; left: 5px;">{{ $InPersonwaitingCount }}</span>
					@endif
				</a>
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
                <a href="{{route('assignee.activities')}}" class="nav-link" title="Action" style="position: relative;">
                    <i class="fas fa-tasks"></i>
                    <span>Action</span>
                    @if($assigneesCount > 0)
						<span class="countTotalActivityAction" style="background: #1f1655; padding: 2px 6px; border-radius: 50%; color: #fff; font-size: 11px; min-width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; line-height: 1; position: absolute; top: 25px; left: 5px;">{{ $assigneesCount }}</span>
					@endif
                </a>
            </li>

            <?php
            $clientmanagerclasstype = '';
            ?>
            <li class="dropdown {{@$clientmanagerclasstype}}">
                <a href="#" class="menu-toggle nav-link has-dropdown"><i class="fas fa-users"></i><span>Clients Manager</span></a>
                <ul class="dropdown-menu">
                    {{-- @if(Auth::user()->role == 1) --}}
                    <li class="{{(Route::currentRouteName() == 'admin.clients.index') ? 'active' : ''}}">
                        <a href="{{route('admin.clients.index')}}" class="nav-link"><i class="fas fa-list"></i><span>Client List</span></a>
                    </li>
                    <li class="{{(Route::currentRouteName() == 'admin.clients.clientsmatterslist') ? 'active' : ''}}">
                        <a href="{{route('admin.clients.clientsmatterslist')}}" class="nav-link"><i class="fas fa-folder-open"></i><span>Matter List</span></a>
                    </li>
                    {{-- @endif --}}

                    <li class="{{(Route::currentRouteName() == 'admin.leads.index') ? 'active' : ''}}">
                        <a href="{{route('admin.leads.index')}}" class="nav-link"><i class="fas fa-list-alt"></i><span>Lead List</span></a>
                    </li>

                    <li class="{{(Route::currentRouteName() == 'admin.leads.create') ? 'active' : ''}}">
                        <a href="{{route('admin.leads.create')}}" class="nav-link"><i class="fas fa-plus-circle"></i><span>Add Lead</span></a>
                    </li>

                </ul>
            </li>

            <?php
            $clientaccountmanagerclasstype = '';
            ?>
            <li class="dropdown {{@$clientaccountmanagerclasstype}}">
                <a href="#" class="menu-toggle nav-link has-dropdown"><i class="fas fa-calculator"></i><span>Account Manager</span></a>
                <ul class="dropdown-menu">
                    <li class="{{(Route::currentRouteName() == 'admin.clients.clientreceiptlist') ? 'active' : ''}}">
                        <a href="{{route('admin.clients.clientreceiptlist')}}" class="nav-link"><i class="fas fa-receipt"></i><span>Client Receipts</span></a>
                    </li>

                    <li class="{{(Route::currentRouteName() == 'admin.clients.invoicelist') ? 'active' : ''}}">
                        <a href="{{route('admin.clients.invoicelist')}}" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>Invoice Lists</span></a>
                    </li>

                    <li class="{{(Route::currentRouteName() == 'admin.clients.officereceiptlist') ? 'active' : ''}}">
                        <a href="{{route('admin.clients.officereceiptlist')}}" class="nav-link"><i class="fas fa-building"></i><span>Office Receipts</span></a>
                    </li>
                </ul>
            </li>

            <?php
            // ANZSCO Occupations menu - Available to all admin users
            if(Route::currentRouteName() == 'admin.anzsco.index' || Route::currentRouteName() == 'admin.anzsco.create' || Route::currentRouteName() == 'admin.anzsco.edit' || Route::currentRouteName() == 'admin.anzsco.import'){
                $anzscoclassstype = 'active';
            }
            ?>
            <li class="dropdown {{@$anzscoclassstype}}">
                <a href="#" class="menu-toggle nav-link has-dropdown" title="ANZSCO Database"><i class="fas fa-briefcase"></i><span>ANZSCO Database</span></a>
                <ul class="dropdown-menu">
                    <li class="{{(Route::currentRouteName() == 'admin.anzsco.index') ? 'active' : ''}}">
                        <a href="{{route('admin.anzsco.index')}}" class="nav-link"><i class="fas fa-list"></i><span>All Occupations</span></a>
                    </li>
                    <li class="{{(Route::currentRouteName() == 'admin.anzsco.create') ? 'active' : ''}}">
                        <a href="{{route('admin.anzsco.create')}}" class="nav-link"><i class="fas fa-plus"></i><span>Add Occupation</span></a>
                    </li>
                    <li class="{{(Route::currentRouteName() == 'admin.anzsco.import') ? 'active' : ''}}">
                        <a href="{{route('admin.anzsco.import')}}" class="nav-link"><i class="fas fa-file-import"></i><span>Import Data</span></a>
                    </li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="{{route('admin.logout')}}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </li>
        </ul>
    </aside>
</div>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="id" value="{{ Auth::user()->id }}">
</form>
