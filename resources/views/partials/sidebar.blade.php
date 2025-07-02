<!--  BEGIN SIDEBAR  -->
<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <!-- BEGIN NAVBAR TOGGLER -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- END NAVBAR TOGGLER -->
        <!-- BEGIN NAVBAR LOGO -->
        <div class="navbar-brand navbar-brand-autodark">
            <a href="." aria-label="Tabler"><img src="/logo.png" alt="" class="navbar-brand-image"></a>
        </div>
        <!-- END NAVBAR LOGO -->
        
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <!-- BEGIN NAVBAR MENU -->
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i
                                class="ti ti-dashboard fs-2"></i></span>
                        <span class="nav-link-title"> Dashboard </span>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('home-page.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('home-page.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-home fs-2"></i>
                        </span>
                        <span class="nav-link-title">Home Page</span>
                    </a>
                </li>
                {{-- <li class="nav-item dropdown {{ Route::is('development.*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="false"
                        role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-folder fs-2"></i></span>
                        <span class="nav-link-title"> Development </span>
                    </a>
                    <div class="dropdown-menu {{ Route::is('development.*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item {{ Route::is('development.category.index') ? 'active' : '' }}"
                                    href="{{ route('development.category.index') }}">
                                    Category
                                </a>
                                <a class="dropdown-item {{ Route::is('development.project.*') || Route::is('development.unit.*') ? 'active' : '' }}"
                                    href="{{ route('development.project.index') }}">
                                    Project
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item {{ Route::is('accessibilities.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('accessibilities.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i
                                class="ti ti-building fs-2"></i></span>
                        <span class="nav-link-title"> Accessibility </span>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('concept.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('concept.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-layout-dashboard fs-2"></i>
                        </span>
                        <span class="nav-link-title">Concept Page</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('news.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('news.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-news fs-2"></i></span>
                        <span class="nav-link-title"> News </span>
                    </a>
                </li>
                <li class="nav-item dropdown {{ Route::is('crm.*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="false"
                        role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-components fs-2"></i></span>
                        <span class="nav-link-title"> CRM </span>
                    </a>
                    <div class="dropdown-menu {{ Route::is('crm.*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item {{ Route::is('crm.platform.*') ? 'active' : '' }}"
                                    href="{{ route('crm.platform.index') }}">
                                    Platform
                                </a>
                                <a class="dropdown-item {{ Route::is('crm.sales.*') ? 'active' : '' }}"
                                    href="{{ route('crm.sales.index') }}">
                                    Sales
                                </a>
                                <a class="dropdown-item {{ Route::is('crm.leads.*') ? 'active' : '' }}"
                                    href="{{ route('crm.leads.index') }}">
                                    Leads
                                    @php
                                        $newLeadsCount = \App\Models\Lead::getNewLeadsCount();
                                    @endphp
                                    @if($newLeadsCount > 0)
                                        <span class="badge bg-red text-white ms-2">{{ $newLeadsCount }}</span>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item {{ Route::is('contact-messages.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('contact-messages.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-message fs-2"></i>
                        </span>
                        <span class="nav-link-title">
                            Contact Messages
                            @php
                                $unreadCount = \App\Models\ContactMessage::getUnreadCount();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="badge bg-red text-white" style="top: 10px; transform: none;">{{ $unreadCount }}</span>
                            @endif
                        </span>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('faqs.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('faqs.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-help-circle fs-2"></i>
                        </span>
                        <span class="nav-link-title">FAQ</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('company-profile.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('company-profile.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-building fs-2"></i>
                        </span>
                        <span class="nav-link-title">Company Profile</span>
                    </a>
                </li>

                <li class="nav-item {{ Route::is('settings.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('settings.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-settings fs-2"></i>
                        </span>
                        <span class="nav-link-title">Settings</span>
                    </a>
                </li> --}}

                <li class="nav-item {{ Route::is('news.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('news.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-news fs-2"></i></span>
                        <span class="nav-link-title"> News </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-danger" href="{{ route('logout') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-logout fs-2"></i></span>
                        <span class="nav-link-title"> Logout </span>
                    </a>
                </li>

            </ul>
            <!-- END NAVBAR MENU -->
        </div>
    </div>
</aside>
<!--  END SIDEBAR  -->