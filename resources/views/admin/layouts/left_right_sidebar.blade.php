<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <!-- User Info -->
        <div class="user-info">
            <div class="image">
                <img src="{{url('admin_assets/admin_bsb')}}/images/user.png" width="48" height="48" alt="User" />
            </div>
            <div class="info-container">
                <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$admin->name}}</div>
                <div class="email">{{$admin->email}}</div>
                <div class="btn-group user-helper-dropdown">
                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:void(0);"><i class="material-icons">person</i>Profile</a></li>
                        <!-- <li role="seperator" class="divider"></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">group</i>Followers</a></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">shopping_cart</i>Sales</a></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">favorite</i>Likes</a></li>
                        <li role="seperator" class="divider"></li> -->
                        <li><a href="{{url('admin/logout')}}"><i class="material-icons">input</i>Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #User Info -->
        <!-- Menu -->
        <div class="menu">
            <ul class="list">
                <li class="header">MAIN NAVIGATION</li>
                <li class="@yield('dashboard_active')">
                    <a href="{{url('admin/dashboard')}}">
                    <i class="material-icons">home</i>
                    <span>Dashboard</span>
                    </a>
                </li>
                <li class="@yield('rides_active')">
                    <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">send</i>
                    <span>Rides</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="@yield('intracity_rides_active')">
                            <a href="{{url('admin/rides/intracity')}}">Inside City</a>
                        </li>
                        <li class="@yield('intercity_rides_active')">
                            <a href="{{url('')}}">Outside City</a>
                        </li>
                    </ul>
                </li>
                <li class="@yield('users_active')">
                    <a href="{{url('admin/users')}}">
                    <i class="material-icons">people</i>
                    <span>Users</span>
                    </a>
                </li>
            
                <li class="@yield('referral_active')">
                    <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">share</i>
                    <span>Referral</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="@yield('settings_referral_active')">
                            <a href="{{url('admin/referral/settings')}}">Settings</a>
                        </li>
                        <li class="@yield('user_referrals_active')">
                            <a href="{{url('admin/referral/users')}}">User Referrals</a>
                        </li>
                    </ul>
                </li>

                <li class="@yield('driver_active')">
                    <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">drive_eta</i>
                    <span>Drivers</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="@yield('driver_list_active')">
                            <a href="{{url('admin/drivers')}}">List View</a>
                        </li>
                        <li class="@yield('driver_map_active')">
                            <a href="{{url('admin/drivers/map')}}">Map View</a>
                        </li>
                    </ul>
                </li>
                <li class="@yield('services_active')">
                    <a href="{{url('admin/services')}}">
                    <i class="material-icons">dialpad</i>
                    <span>Services</span>
                    </a>
                </li>
                <li class="@yield('settings_active')">
                    <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">settings</i>
                    <span>Settings</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="@yield('settings_general_active')">
                            <a href="{{url('admin/settings/general')}}">General</a>
                        </li>
                        <li class="@yield('settings_razorpay_active')">
                            <a href="{{url('admin/settings/razorpay')}}">Razorpay Gateway</a>
                        </li>
                        <li class="@yield('settings_email_active')">
                            <a href="{{url('admin/settings/email')}}">Email</a>
                        </li>
                        <li class="@yield('settings_sms_active')">
                            <a href="{{url('admin/settings/sms')}}">Sms</a>
                        </li>
                        <li class="@yield('settings_firebase_active')">
                            <a href="{{url('admin/settings/firebase')}}">Firebase</a>
                        </li>
                        <li class="@yield('settings_facebook_active')">
                            <a href="{{url('admin/settings/facebook')}}">Facebook</a>
                        </li>
                        <li class="@yield('settings_google_active')">
                            <a href="{{url('admin/settings/google')}}">Google</a>
                        </li>
                    </ul>
                </li>
                <li class="@yield('trips_active')">
                    <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">directions_bus</i>
                    <span>Trips</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="@yield('trips_route_locations')">
                            <a href="{{url('admin/routes/locations')}}">Locations</a>
                        </li>
                        <li class="@yield('trips_new_route_active')">
                            <a href="{{url('admin/routes/new')}}">New Route</a>
                        </li>
                        <li class="@yield('trips_all_routes_active')">
                            <a href="{{url('admin/routes')}}">Routes</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- #Menu -->
        <!-- Footer -->
        <div class="legal">
            <div class="copyright">
                &copy; {{$website_copyright}} <a href="javascript:void(0);">{{$website_company_name}}</a>
            </div>
            <!-- <div class="version">
                <b>Version: </b> 1.0.5
            </div> -->
        </div>
        <!-- #Footer -->
    </aside>
    <!-- #END# Left Sidebar -->
    <!-- Right Sidebar -->
    <!-- <aside id="rightsidebar" class="right-sidebar">
        <ul class="nav nav-tabs tab-nav-right" role="tablist">
            <li role="presentation" class="active"><a href="#skins" data-toggle="tab">SKINS</a></li>
            <li role="presentation"><a href="#settings" data-toggle="tab">SETTINGS</a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active in active" id="skins">
                <ul class="demo-choose-skin">
                    <li data-theme="red" class="active">
                        <div class="red"></div>
                        <span>Red</span>
                    </li>
                    <li data-theme="pink">
                        <div class="pink"></div>
                        <span>Pink</span>
                    </li>
                    <li data-theme="purple">
                        <div class="purple"></div>
                        <span>Purple</span>
                    </li>
                    <li data-theme="deep-purple">
                        <div class="deep-purple"></div>
                        <span>Deep Purple</span>
                    </li>
                    <li data-theme="indigo">
                        <div class="indigo"></div>
                        <span>Indigo</span>
                    </li>
                    <li data-theme="blue">
                        <div class="blue"></div>
                        <span>Blue</span>
                    </li>
                    <li data-theme="light-blue">
                        <div class="light-blue"></div>
                        <span>Light Blue</span>
                    </li>
                    <li data-theme="cyan">
                        <div class="cyan"></div>
                        <span>Cyan</span>
                    </li>
                    <li data-theme="teal">
                        <div class="teal"></div>
                        <span>Teal</span>
                    </li>
                    <li data-theme="green">
                        <div class="green"></div>
                        <span>Green</span>
                    </li>
                    <li data-theme="light-green">
                        <div class="light-green"></div>
                        <span>Light Green</span>
                    </li>
                    <li data-theme="lime">
                        <div class="lime"></div>
                        <span>Lime</span>
                    </li>
                    <li data-theme="yellow">
                        <div class="yellow"></div>
                        <span>Yellow</span>
                    </li>
                    <li data-theme="amber">
                        <div class="amber"></div>
                        <span>Amber</span>
                    </li>
                    <li data-theme="orange">
                        <div class="orange"></div>
                        <span>Orange</span>
                    </li>
                    <li data-theme="deep-orange">
                        <div class="deep-orange"></div>
                        <span>Deep Orange</span>
                    </li>
                    <li data-theme="brown">
                        <div class="brown"></div>
                        <span>Brown</span>
                    </li>
                    <li data-theme="grey">
                        <div class="grey"></div>
                        <span>Grey</span>
                    </li>
                    <li data-theme="blue-grey">
                        <div class="blue-grey"></div>
                        <span>Blue Grey</span>
                    </li>
                    <li data-theme="black">
                        <div class="black"></div>
                        <span>Black</span>
                    </li>
                </ul>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="settings">
                <div class="demo-settings">
                    <p>GENERAL SETTINGS</p>
                    <ul class="setting-list">
                        <li>
                            <span>Report Panel Usage</span>
                            <div class="switch">
                                <label><input type="checkbox" checked><span class="lever"></span></label>
                            </div>
                        </li>
                        <li>
                            <span>Email Redirect</span>
                            <div class="switch">
                                <label><input type="checkbox"><span class="lever"></span></label>
                            </div>
                        </li>
                    </ul>
                    <p>SYSTEM SETTINGS</p>
                    <ul class="setting-list">
                        <li>
                            <span>Notifications</span>
                            <div class="switch">
                                <label><input type="checkbox" checked><span class="lever"></span></label>
                            </div>
                        </li>
                        <li>
                            <span>Auto Updates</span>
                            <div class="switch">
                                <label><input type="checkbox" checked><span class="lever"></span></label>
                            </div>
                        </li>
                    </ul>
                    <p>ACCOUNT SETTINGS</p>
                    <ul class="setting-list">
                        <li>
                            <span>Offline</span>
                            <div class="switch">
                                <label><input type="checkbox"><span class="lever"></span></label>
                            </div>
                        </li>
                        <li>
                            <span>Location Permission</span>
                            <div class="switch">
                                <label><input type="checkbox" checked><span class="lever"></span></label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </aside> -->
    <!-- #END# Right Sidebar -->
</section>