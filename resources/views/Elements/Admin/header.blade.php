<style>
.ui.label {
    display: inline-block;
    line-height: 1;
    vertical-align: baseline;
    margin: 0 0.14285714em;
    background-color: #e8e8e8;
    background-image: none;
    padding: 0.5833em 0.833em;
    color: rgba(0,0,0,.6);
    text-transform: none;
    font-weight: 700;
    border: 0 solid transparent;
    border-radius: 0.28571429rem;
    -webkit-transition: background .1s ease;
    transition: background .1s ease;
}
.ui.yellow.label, .ui.yellow.labels .label {
    background-color: #fbbd08!important;
    border-color: #fbbd08!important;
    color: #fff!important;
}
.ui.red.label, .ui.red.labels .label {
    background-color: #db2828!important;
    border-color: #db2828!important;
    color: #fff!important;
}
</style>
<nav class="navbar navbar-expand-lg main-navbar sticky">
	<div class="form-inline mr-auto">
		<ul class="navbar-nav mr-3">
			<li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn "> <i data-feather="align-justify" id="feather-icon"></i></a></li>
			<li><a href="#" class="nav-link nav-link-lg fullscreen-btn"><i data-feather="maximize"></i></a></li>

            <?php
            if( Auth::user()->role == 1 || Auth::user()->role == 12 ){ //super admin or admin
            ?>
            <li class="dropdown dropdown-list-toggle">
			    <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle"><i data-feather="plus"></i></a>
                <div style="width: 50px;" class="dropdown-menu dropdown-list dropdown-menu-right pullDown">

                    <div class="">
                        <a href="{{URL::to('/admin/leads/create')}}" class="dropdown-item">Add Lead</a>
                        <a href="{{route('adminconsole.system.users.active')}}" class="dropdown-item">User</a>
                    </div>
			    </div>
		    </li>
            <?php }?>

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
	<ul class="navbar-nav navbar-right">
	<li class="dropdown dropdown-list-toggle">
	<a href="javascript:;" data-toggle="dropdown" title="Add Office Check-In" class="nav-link nav-link-lg opencheckin"><i data-feather="log-in"></i></a>
	</li>
		<!-- {{--	<li class="dropdown dropdown-list-toggle">
			<a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle"><i data-feather="mail"></i><span class="badge headerBadge1">6</span></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
				<div class="dropdown-header">Messages
					<div class="float-right">
						<a href="#">Mark All As Read</a>
					</div>
				</div>
				<div class="dropdown-list-content dropdown-list-message">

					<a href="#" class="dropdown-item">
						<span class="dropdown-item-avatar text-white">
							<img alt="image" src="{!! asset('img/users/user-2.png') !!}" class="rounded-circle">
						</span>
						<span class="dropdown-item-desc">
							<span class="message-user"></span>
							<span class="time messege-text">Please check your mail !!</span>
							<span class="time">2 Min Ago</span>
						</span>
					</a>

					<a href="#" class="dropdown-item">
						<span class="dropdown-item-avatar text-white">
							<img alt="image" src="{!! asset('img/users/user-2.png') !!}" class="rounded-circle">
						</span>
						<span class="dropdown-item-desc">
							<span class="message-user">Sarah Smith</span>
							<span class="time messege-text">Request for leave application</span>
							<span class="time">5 Min Ago</span>
						</span>
					</a>
					<a href="#" class="dropdown-item">
						<span class="dropdown-item-avatar text-white">
							<img alt="image" src="{!! asset('img/users/user-5.png') !!}" class="rounded-circle">
						</span>
						<span class="dropdown-item-desc">
							<span class="message-user">Jacob Ryan</span>
							<span class="time messege-text">Your payment invoice is generated.</span>
							<span class="time">12 Min Ago</span>
						</span>
					</a>
				</div>
				<div class="dropdown-footer text-center">
					<a href="#">View All <i class="fas fa-chevron-right"></i></a>
				</div>
			</div>
		</li>--}} -->
	<li class="dropdown dropdown-list-toggle">
		@if(Auth::user())
			<a href="#" class="nav-link notification-toggle nav-link-lg" data-toggle="tooltip" data-placement="bottom" title="Click To See Notifications"><i data-feather="bell" class="bell"></i><span class="countbell" id="countbell_notification"><?php  echo \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->count(); ?></span></a>
        @endif
			<!--<div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
				<div class="dropdown-header">Notifications
					<div class="float-right">

					</div>
				</div>
				<div class="dropdown-list-content dropdown-list-icons showallnotifications">
				     <?php
					/*if(Auth::user()){
						$notificalists = \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->orderby('created_at','DESC')->paginate(5);
				    foreach($notificalists as $listnoti){*/
				    ?>
					<a href="{{--$listnoti->url--}}?t={{--$listnoti->id--}}" class="dropdown-item dropdown-item-unread">
						<span class="dropdown-item-icon bg-primary text-white">
							<i class="fas fa-code"></i>
						</span>
						<span class="dropdown-item-desc">{{--$listnoti->message--}} <span class="time">{{--date('d/m/Y h:i A',strtotime($listnoti->created_at))--}}</span></span>
					</a>
					<?php //} }?>

				</div>
				<div class="dropdown-footer text-center">
					<a href="{{--URL::to('/admin/all-notifications')--}}">View All <i class="fas fa-chevron-right"></i></a>
				</div>
			</div>-->
		</li>
		@if(Auth::check())
		<li class="dropdown">
			<a href="#" data-toggle="dropdown"class="nav-link dropdown-toggle nav-link-lg nav-link-user">
				@if(@Auth::user()->profile_img == '')
				<img alt="user image" src="{{ asset('img/user.png') }}" class="user-img-radious-style">
				@else
				<img  alt="{{str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...')}}" src="{{ asset('img/user.png') }}" class="user-img-radious-style"/>
				@endif
				<span class="d-sm-none d-lg-inline-block"></span>
			</a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
				<div class="dropdown-title">{{str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...')}}</div>
				<a href="{{route('admin.my_profile')}}" class="dropdown-item has-icon">
					<i class="far fa-user"></i> Profile
				</a>
                <?php
                if( Auth::check() && Auth::user()->role == 1 ){ //super admin or admin
                ?>
			    <a href="{{route('adminconsole.features.matter.index')}}" class="dropdown-item has-icon">
					<i class="fas fa-cogs"></i> Admin Console
				</a>
                <?php } ?>

				<div class="dropdown-divider"></div>
				<a href="{{route('admin.logout')}}" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <i class="fas fa-sign-out-alt"></i> Logout</a>
				<form ...>
				<input type="hidden" name="id" value="{{Auth::user()->id}}">
				</form>
            </div>
		</li>
		@endif
	</ul>
</nav>
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="id" value="{{ Auth::user()->id }}">
</form>
