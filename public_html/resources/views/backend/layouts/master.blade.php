<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Solar Monitoring - Bright Current')</title>
    
    <!-- Theme initialization script (must be before any CSS) -->
    <script>
        // Initialize theme before page loads to prevent flash
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link
        rel="icon"
        type="image/png"
        href="{{ asset('assets/images/favicon.png') }}"
        sizes="16x16"
    />
    <!-- remix icon font css  -->
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}" />
    <!-- BootStrap css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}" />
    <!-- Apex Chart css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/apexcharts.css') }}" />
    <!-- Data Table css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/dataTables.min.css') }}" />
    <!-- Text Editor css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor-katex.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.atom-one-dark.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.quill.snow.css') }}" />
    <!-- Date picker css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/flatpickr.min.css') }}" />
    <!-- Calendar css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/full-calendar.css') }}" />
    <!-- Vector Map css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/jquery-jvectormap-2.0.5.css') }}" />
    <!-- Popup css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/magnific-popup.css') }}" />
    <!-- Slick Slider css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/slick.css') }}" />
    <!-- prism css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/prism.css') }}" />
    <!-- file upload css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/file-upload.css') }}" />
    <!-- main css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />

</head>
<body></body>
</html>

<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="index.html" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo" style="width: 209px; height: 80px;">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li>
                <a class="{{ request()->routeIs('dashboard') ? 'active-page' : '' }}" href="{{ route('dashboard') }}">
                    <i class="ri-home-4-line text-xl me-14 d-flex w-auto"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a class="{{ request()->routeIs('system.index') || request()->routeIs('system.show') || request()->routeIs('system.edit') || request()->routeIs('system.create') ? 'active-page' : '' }}" href="{{ route('system.index') }}">
                    <i class="ri-bar-chart-line text-xl me-14 d-flex w-auto"></i>
                    <span>System</span>
                </a>
            </li>
            <li>
                <a class="{{ request()->routeIs('alert.index') ? 'active-page' : '' }}" href="{{ route('alert.index') }}">
                    <i class="ri-notification-2-line text-xl me-14 d-flex w-auto"></i>
                    <span>Alerts</span>
                </a>
            </li>
            <li>
                <a class="{{ request()->routeIs('service-schedules.index') || request()->routeIs('service-schedules.create') || request()->routeIs('service-schedules.edit') ? 'active-page' : '' }}" href="{{ route('service-schedules.index') }}">
                    <i class="ri-calendar-todo-line text-xl me-14 d-flex w-auto"></i>
                    <span>Scheduling</span>
                </a>
            </li>
            <li>
                <a class="{{ request()->routeIs('reports.index') ? 'active-page' : '' }}" href="{{ route('reports.index') }}">
                    <i class="ri-file-chart-line text-xl me-14 d-flex w-auto"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li>
                <a class="{{ request()->routeIs('solar-api.*') ? 'active-page' : '' }}" href="{{ route('solar-api.index') }}">
                    <i class="ri-plug-line text-xl me-14 d-flex w-auto"></i>
                    <span>Solar API</span>
                </a>
            </li>
            <li>
                <a class="{{ request()->routeIs('settings.index') ? 'active-page' : '' }}" href="{{ route('settings.index') }}">
                    <i class="ri-settings-2-fill text-xl me-14 d-flex w-auto"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
        <div>
            <a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit();" class="btn color-bg-brand text-dark text-sm d-block px-8 py-8 radius-4">
                Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</aside>

<main class="dashboard-main">
    <div class="navbar-header">
        <div class="row align-items-center justify-content-between">
            <div class="col-auto">
                <div class="d-flex flex-wrap align-items-center gap-4">
                    <button type="button" class="sidebar-toggle d-none">
                        <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
                        <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                    </button>
                    <button type="button" class="sidebar-mobile-toggle d-none">
                        <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
                    </button>
                    
                    {{-- Page Header Content --}}
                    <div>
                        {{-- Breadcrumb --}}
                        <nav class="mb-2">
                            <span class="text-secondary-light text-sm">@yield('breadcrumb', 'Pages / Dashboard')</span>
                        </nav>
                        {{-- Page Title --}}
                        <h2 class="fw-bold mb-0 text-primary-light" style="font-size: 1.5rem;">@yield('page-title', 'Main Dashboard')</h2>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <form class="navbar-search">
                        <input type="text" name="search" placeholder="Search">
                        <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                    </form>
                    <button type="button" data-theme-toggle class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>

                    <div class="dropdown">
                        <button class="d-flex justify-content-center align-items-center rounded-circle" type="button" data-bs-toggle="dropdown">
                            <img src="{{ asset('assets/images/user.png') }}" alt="image" class="w-40-px h-40-px object-fit-cover rounded-circle">
                        </button>
                        <div class="dropdown-menu to-top dropdown-menu-sm">
                            <div class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <h6 class="text-lg text-primary-light fw-semibold mb-2">Bright Current</h6>
                                    <span class="text-secondary-light fw-medium text-sm">Admin</span>
                                </div>
                                <button type="button" class="hover-text-danger">
                                    <iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
                                </button>
                            </div>
                            <ul class="to-top-list">
                                <li>
                                    <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="view-profile.html">
                                        <iconify-icon icon="solar:user-linear" class="icon text-xl"></iconify-icon>  My Profile</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="email.html">
                                        <iconify-icon icon="tabler:message-check" class="icon text-xl"></iconify-icon>  Inbox</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="company.html">
                                        <iconify-icon icon="icon-park-outline:setting-two" class="icon text-xl"></iconify-icon>  Setting</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3" href="javascript:void(0)">
                                        <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon>  Log Out</a>
                                </li>
                            </ul>
                        </div>
                    </div><!-- Profile dropdown end -->
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-main-body">
        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </div>

    <!-- <footer class="d-footer">
        <div class="row align-items-center justify-content-between">
            <div class="col-auto">
                <p class="mb-0">© 2025 Bright Current. All Rights Reserved.</p>
            </div>
            <div class="col-auto">
                <p class="mb-0">Made by <span class="text-primary-600">Sly Nerds</span></p>
            </div>
        </div>
    </footer> -->
</main>

<!-- jQuery library js -->
<script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
<!-- Bootstrap js -->
<script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
<!-- Data Table js -->
<script src="{{ asset('assets/js/lib/dataTables.min.js') }}"></script>
<!-- Iconify Font js -->
<script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
<!-- jQuery UI js -->
<script src="{{ asset('assets/js/lib/jquery-ui.min.js') }}"></script>
<!-- Vector Map js -->
<script src="{{ asset('assets/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- Popup js -->
<script src="{{ asset('assets/js/lib/magnifc-popup.min.js') }}"></script>
<!-- Slick Slider js -->
<script src="{{ asset('assets/js/lib/slick.min.js') }}"></script>
<!-- prism js -->
<script src="{{ asset('assets/js/lib/prism.js') }}"></script>
<!-- file upload js -->
<script src="{{ asset('assets/js/lib/file-upload.js') }}"></script>

<!-- main js -->
<script src="{{ asset('assets/js/app.js') }}"></script>
<!-- custom js -->
<script src="{{ asset('assets/js/custom.js') }}"></script>

@stack('script');

</body>
</html>
