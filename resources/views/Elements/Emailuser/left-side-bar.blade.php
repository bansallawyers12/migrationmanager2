<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#">
                <img alt="Immigration" src="{!! asset('img/logo.png') !!}" class="header-logo" />
                <!--<span class="logo-name"></span>-->
            </a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Main</li>

            <?php
            if(Route::currentRouteName() == 'email_users.dashboard'){
                $dashclasstype = 'active';
            }
            ?>
            <li class="dropdown {{@$dashclasstype}}">
                <a href="{{route('email_users.dashboard')}}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
            </li>

            <?php
            if( !empty($email_users_info) && count($email_users_info) >0 ){
                foreach($email_users_info as $email_key=>$email_val) {
                ?>
                <li class="dropdown {{@$clientmanagerclasstype}}">
                    <a href="#" class="menu-toggle nav-link has-dropdown">
                        <i data-feather="user"></i>
                        <span>
                            <?php
                            $emailU = $email_val->email;
                            // Find the position of the "@" sign
                            $atPosition = strpos($emailU, '@');
                            // Use substr() to get the part before the "@"
                            $beforeAt = substr($emailU, 0, $atPosition);
                            echo ucfirst($beforeAt);

                            //echo Route::currentRouteName();
                            ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{(Route::currentRouteName() == 'email_users.loadinbox') ? 'active' : ''}}">
                            <a href="{{route('email_users.loadinbox', ['email_user_id' =>$email_val->id])}}" class="nav-link"><i data-feather="file-text"></i><span>Load Inbox Emails</span></a>
                        </li>

                        <li class="{{(Route::currentRouteName() == 'email_users.loadsent') ? 'active' : ''}}">
                            <a href="{{route('email_users.loadsent', ['email_user_id' => $email_val->id])}}" class="nav-link"><i data-feather="file-text"></i><span>Load Sent Emails</span></a>
                        </li>

                        <li class="{{(Route::currentRouteName() == 'email_users.inbox') ? 'active' : ''}}">
                            <a href="{{route('email_users.inbox', ['email_user_id' => $email_val->id])}}" class="nav-link"><i data-feather="file-text"></i><span>Inbox</span></a>
                        </li>

                        <li class="{{(Route::currentRouteName() == 'email_users.sent') ? 'active' : ''}}">
                            <a href="{{route('email_users.sent', ['email_user_id' => $email_val->id])}}" class="nav-link"><i data-feather="file-text"></i><span>Sent</span></a>
                        </li>
                    </ul>
                </li>
                <?php
                } //end foreach
            } //end if
            ?>


            <li class="dropdown">
                <a href="{{route('email_users.email_dashboard')}}" class="nav-link"><i data-feather="monitor"></i><span>Email List</span></a>
            </li>

            

            <li class="dropdown">
                <a href="javascript:void(0)" class="nav-link" onclick="event.preventDefault(); document.getElementById('email-sidebar-logout-form').submit();"><i data-feather="log-out"></i><span>Logout</span></a>
                <form action="{{ url('email_users/logout') }}" method="post" name="admin_login" id="email-sidebar-logout-form">
                    @csrf
                    <input type="hidden" name="id" value="{{Auth::user()->id}}">
                </form>
            </li>
        </ul>
    </aside>
</div>
