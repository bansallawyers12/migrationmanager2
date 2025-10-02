<nav class="navbar navbar-expand-lg main-navbar sticky">
    <div class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"><i data-feather="align-justify" id="feather-icon"></i></a></li>
            <li><a href="#" class="nav-link nav-link-lg fullscreen-btn"><i data-feather="maximize"></i></a></li>
            @if(Auth::user()->role == 1 || Auth::user()->role == 12)
            <li class="dropdown dropdown-list-toggle">
                <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle"><i data-feather="plus"></i></a>
                <div style="width: 50px;" class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
                    <div class="">
                        <a href="{{URL::to('/admin/clients')}}" class="dropdown-item">Client</a>
                        <a href="#" class="dropdown-item">Appointment</a>
                        <a href="{{URL::to('/admin/partners')}}" class="dropdown-item">Partner</a>
                        <a href="{{URL::to('/admin/products')}}" class="dropdown-item">Product</a>
                        <a href="#" class="dropdown-item">Workflow</a>
                        <a href="{{URL::to('/admin/users/active')}}" class="dropdown-item">User</a>
                    </div>
                </div>
            </li>
            @endif
            <li>
                <form class="form-inline mr-auto">
                    <div class="search-element">
                        <select class="form-control js-data-example-ajaxccsearch" type="search" placeholder="Search" aria-label="Search" data-width="200"></select>
                        <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
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
                @if(@Auth::user()->profile_img == '')
                    <img alt="user image" src="{{ asset('img/user.png') }}" class="user-img-radious-style">
                @else
                    <img alt="{{str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...')}}" src="{{ asset('img/user.png') }}" class="user-img-radious-style"/>
                @endif
                <span class="d-sm-none d-lg-inline-block"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
                <div class="dropdown-title">{{str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...')}}</div>
                <a href="{{route('admin.my_profile')}}" class="dropdown-item has-icon"><i class="far fa-user"></i> Profile</a>
                @if(Auth::user()->role == 1)
                <a href="{{route('admin.feature.matter.index')}}" class="dropdown-item has-icon"><i class="fas fa-cogs"></i> Admin Console</a>
                @endif
                <div class="dropdown-divider"></div>
                <a href="{{route('admin.logout')}}" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a>
                {{ Form::open(array('url' => 'admin/logout', 'name'=>'admin_login', 'id' => 'logout-form')) }}
                <input type="hidden" name="id" value="{{Auth::user()->id}}">
                {{ Form::close() }}
            </div>
        </li>
    </ul>
</nav>
