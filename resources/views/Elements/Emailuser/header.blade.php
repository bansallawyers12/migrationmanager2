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
			<li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"> <i data-feather="align-justify"></i></a></li>
			<li><a href="#" class="nav-link nav-link-lg fullscreen-btn"><i data-feather="maximize"></i></a></li>
			<li class="dropdown dropdown-list-toggle">
                <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle"><i data-feather="plus"></i></a>
                <div style="width: 50px;" class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
                    <div class="">
                        <a href="{{URL::to('/admin/clients')}}" class="dropdown-item">
                            Client
                        </a>
                    </div>
                </div>
		    </li>

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
			<a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg"><i data-feather="bell" class="bell"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
				<div class="dropdown-header">Notifications
					<div class="float-right">
					<a href="#">Mark All As Read</a>
					</div>
				</div>
				<div class="dropdown-list-content dropdown-list-icons">
					<a href="#" class="dropdown-item dropdown-item-unread">
						<span class="dropdown-item-icon bg-primary text-white">
							<i class="fas fa-code"></i>
						</span>
						<span class="dropdown-item-desc">Template update is available now! <span class="time">2 MinAgo</span></span>
					</a>
					<a href="#" class="dropdown-item">
						<span class="dropdown-item-icon bg-info text-white">
							<i class="far fa-user"></i>
						</span>
						<span class="dropdown-item-desc"> <b>You</b> and <b>Dedik Sugiharto</b> are now friends <span class="time">10 Hours Ago</span>
						</span>
					</a>
				</div>
				<div class="dropdown-footer text-center">
					<a href="#">View All <i class="fas fa-chevron-right"></i></a>
				</div>
			</div>
		</li>
		<li class="dropdown">
			<a href="#" data-toggle="dropdown"class="nav-link dropdown-toggle nav-link-lg nav-link-user">
				@if(@Auth::user()->profile_img == '')
				<img alt="user image" src="{{ asset('/img/user.png') }}" class="user-img-radious-style">
				@else
				<img  alt="{{str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...')}}" src="{{URL::to('/img/profile_imgs')}}/{{@Auth::user()->profile_img}}" class="user-img-radious-style"/>
				@endif
				<span class="d-sm-none d-lg-inline-block"></span>
			</a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
				<div class="dropdown-title">{{str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...')}}</div>
				<a href="{{route('my_profile')}}" class="dropdown-item has-icon">
					<i class="far fa-user"></i> Profile
				</a>

				<div class="dropdown-divider"></div>
				<a href="javascript:void(0)" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('email-header-logout-form').submit();"> <i class="fas fa-sign-out-alt"></i> Logout</a>
				<form action="{{ url('email_users/logout') }}" method="post" name="admin_login" id="email-header-logout-form">
					@csrf
					<input type="hidden" name="id" value="{{Auth::user()->id}}">
				</form>
            </div>
		</li>
	</ul>
</nav>
