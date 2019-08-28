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
                        <li><a href="{{route('admin.logout')}}"><i class="material-icons">input</i>Sign Out</a></li>
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
                    <a href="{{route('admin-dashboard')}}">
                    <i class="material-icons">home</i>
                    <span>Dashboard</span>
                    </a>
                </li>
                <li class="@yield('hiring_active')">
                    <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">airline_seat_recline_normal</i>
                    <span>Hire Driver</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="@yield('hiring_package_add_active')">
                            <a href="{{route('admin.hiring.package.add.show')}}">+ Add Package</a>
                        </li>
                        <li class="@yield('hiring_packages_active')">
                            <a href="{{route('admin.hiring.packages.show')}}">Packages</a>
                        </li>
                    </ul>
                </li>
                <li class="@yield('rides_active')">
                    <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">send</i>
                    <span>Rides</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="@yield('intracity_rides_active')">
                            <a href="{{route('admin.rides.city')}}">City Rides</a>
                        </li>
                        <li class="@yield('higiway_trips_active')">
                            <a href="{{route('admin.show.trips')}}">Outside Rides</a>
                        </li>
                        <li class="@yield('higiway_trips_bookings_active')">
                            <a href="{{route('admin.show.bookings')}}">Bookings</a>
                        </li>
                        <li class="@yield('canceled_bookings_active')">
                            <a href="{{route('admin.show_canceled_bookings')}}">Canceled Bookings</a>
                        </li>
                        <li class="@yield('driver_bookings_active')">
                            <a href="{{route('admin.hiring.bookings')}}">Driver Bookings</a>
                        </li>
                    </ul>
                </li>
                <li class="@yield('users_active')">
                    <a href="{{route('admin-users')}}">
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
                            <a href="{{route('admin.settings.referral')}}">Settings</a>
                        </li>
                        <li class="@yield('user_referrals_active')">
                            <a href="{{route('admin.show_referral_users')}}">User Referrals</a>
                        </li>
                    </ul>
                </li>

                <li class="@yield('coupons_active')">
                    <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">content_cut</i>
                    <span>Coupons</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="@yield('coupons_add_active')">
                            <a href="{{route('admin.coupons.show.add-new')}}">Add New</a>
                        </li>
                        <li class="@yield('coupons_view_active')">
                            <a href="{{route('admin.coupons.show')}}">List</a>
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
                            <a href="{{route('admin-drivers')}}">Show Drivers List</a>
                        </li>
                        <li class="@yield('driver_accounts_active')">
                            <a href="{{route('admin.drivers.accounts')}}">Accounts Management</a>
                        </li>
                        <li class="@yield('driver_map_active')">
                            <a href="{{route('admin-drivers-map')}}">Drivers In Map</a>
                        </li>
                        <li class="@yield('payouts_active')">
                            <a href="{{route('admin.payouts.show')}}">Generate Payouts</a>
                        </li>
                        <li class="@yield('payout_settings_active')">
                            <a href="{{route('admin.payouts.settings.show')}}">Payout Settings</a>
                        </li>
                        <li class="@yield('fake_locations_active')">
                            <a href="{{route('admin.driver.fake.locations.show')}}">Fake Locations</a>
                        </li>
                    </ul>
                </li>
                <li class="@yield('services_active')">
                    <a href="{{route('admin.services')}}">
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
                            <a href="{{route('admin.settings.general')}}">General</a>
                        </li>
                        <li class="@yield('settings_seo_active')">
                            <a href="{{route('admin.show.seo')}}">Seo Management</a>
                        </li>
                        <li class="@yield('settings_razorpay_active')">
                            <a href="{{route('admin.settings.razorpay')}}">Razorpay Gateway</a>
                        </li>
                        <li class="@yield('settings_email_active')">
                            <a href="{{route('admin.settings.email')}}">Email</a>
                        </li>
                        <li class="@yield('settings_sms_active')">
                            <a href="{{route('admin.settings.sms')}}">Sms</a>
                        </li>
                        <li class="@yield('settings_firebase_active')">
                            <a href="{{route('admin.settings.firebase')}}">Firebase</a>
                        </li>
                        <li class="@yield('settings_facebook_active')">
                            <a href="{{route('admin.settings.facebook')}}">Facebook</a>
                        </li>
                        <li class="@yield('settings_google_active')">
                            <a href="{{route('admin.settings.google')}}">Google</a>
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
                            <a href="{{route('admin.show_all_locations')}}">Locations</a>
                        </li>
                        <li class="@yield('trips_new_route_active')">
                            <a href="{{route('admin.show-new-route')}}">New Route</a>
                        </li>
                        <li class="@yield('trips_all_routes_active')">
                            <a href="{{route('admin.show-all-routes')}}">Routes</a>
                        </li>
                    </ul>
                </li>
                <li class="@yield('support_active')">
                    <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">headset_mic</i>
                    <span>Support</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="@yield('support_settings')">
                            <a href="{{route('admin.support.show.settings')}}">Settings</a>
                        </li>
                        <li class="@yield('user_support_tickets')">
                            <a href="{{route('admin.support.user.tickets')}}">User Tickets</a>
                        </li>
                        <li class="@yield('driver_support_tickets')">
                            <a href="{{route('admin.support.driver.tickets')}}">Driver Tickets</a>
                        </li>
                    </ul>
                </li>
                <li class="@yield('promotions_active')">
                    <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">cast</i>
                    <span>Promotions</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="@yield('promotion_add_active')">
                            <a href="{{route('admin.show.add.promotion')}}">Add New</a>
                        </li>
                        <li class="@yield('promotions_list_active')">
                            <a href="{{route('admin.promotions')}}">Promotions</a>
                        </li>
                    </ul>
                </li>
                <li class="@yield('content_management_active')">
                    <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">content_paste</i>
                    <span>Contents</span>
                    </a>
                    <ul class="ml-menu">
                        <li class="@yield('privacy_policy_active')">
                            <a href="{{route('admin.show.content.privacy-policy')}}">Privacy Policy</a>
                        </li>
                        <li class="@yield('terms_active')">
                            <a href="{{route('admin.show.content.terms')}}">Terms & Conditions</a>
                        </li>
                        <li class="@yield('driver_terms_active')">
                            <a href="{{route('admin.show.content.driver.terms')}}">Driver Terms & Conditions</a>
                        </li>
                        <li class="@yield('cancellation_policy_active')">
                            <a href="{{route('admin.show.content.cancellation.policy')}}">Cancellation Policy</a>
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
</section>