<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    <!-- Icon លើ Browser Tab -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=2">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}?v=2">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- 1. CDN Lucide JS -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>

    <!-- Font Awesome (for dashboard icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Plus Jakarta Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body,
        .font-jakarta {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans flex h-screen">

    <!-- Left Sidebar -->
    <aside id="app-sidebar"
        class="w-56 bg-blue-900 text-white flex flex-col rounded-r-3xl scrollbar-none h-screen shrink-0 transition-all duration-300">
        <!-- <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0  z-40 hidden md:hidden"></div> -->
        <div class="p-3 text-xl font-bold border-b border-blue-800 items-center flex flex-col justify-center">
            <img src="{{asset('images/logo.PNG')}}" alt="Logo" class="w-14 h-14 rounded-full object-cover">
            <div class="text-center mt-1">Laundry SIX Plus</div>
        </div>

        <nav class="flex-1 p-3 space-y-1 overflow-y-auto whitespace-nowrap scrollbar-none">
            <!-- Check if the user has any of the allowed roles -->
            @if(in_array(Auth::user()->getCurrentRole(), ['staff', 'delivery', 'receptionist', 'admin']))
            <a href="{{ route('dashboard') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0"></i>
                <span>Dashboard</span>
            </a>
            @endif

            @if(in_array(Auth::user()->getCurrentRole(), ['admin','receptionist']))
            <a href="{{ route('customers.index') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 {{ request()->is('*customer*') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="users" class="w-5 h-5 shrink-0"></i>
                <span>Customers</span>
            </a>
            @endif

            @if(in_array(Auth::user()->getCurrentRole(), ['admin','receptionist']))
            <a href="{{ route('services.index') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 {{ request()->routeIs('services.*') || request()->is('*service*') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="concierge-bell" class="w-5 h-5 shrink-0"></i>
                <span>Service</span>
            </a>
            @endif

            @if(in_array(Auth::user()->getCurrentRole(), ['admin', 'delivery', 'receptionist', 'staff']))
            <a href="{{ route('orders.index') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 {{ request()->is('*order*') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="shopping-bag" class="w-5 h-5 shrink-0"></i>
                <span>Orders</span>
            </a>
            @endif

            @if(in_array(Auth::user()->getCurrentRole(), ['admin', 'receptionist']))
            <a href="{{ route('payment.index') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 whitespace-nowrap {{ request()->is('*payment*') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="credit-card" class="w-5 h-5 shrink-0"></i>
                <span class="whitespace-nowrap">Payments</span>
            </a>
            @endif

            @if(in_array(Auth::user()->getCurrentRole(), ['admin', 'delivery', 'receptionist']))
            <a href="{{ route('pickup-and-delivery.index') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 whitespace-nowrap {{ request()->is('*pickup*') || request()->is('*delivery*') || request()->is('*PickupAndDelivery*') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="truck" class="w-5 h-5 shrink-0"></i>
                <span class="whitespace-nowrap">Pickup & Delivery</span>
            </a>
            @endif

            @if(in_array(Auth::user()->getCurrentRole(), ['admin']))
            <a href="{{ route('employees.index') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 whitespace-nowrap {{ request()->is('*employee*') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="user-check" class="w-5 h-5 shrink-0"></i>
                <span class="whitespace-nowrap">Employees</span>
            </a>
            @endif

            @if(in_array(Auth::user()->getCurrentRole(), ['staff', 'admin']))
            <a href="{{ route('inventory.index') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 whitespace-nowrap {{ request()->is('*inventor*') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="boxes" class="w-5 h-5 shrink-0"></i>
                <span class="whitespace-nowrap">Inventory</span>
            </a>
            @endif

            @if(in_array(Auth::user()->getCurrentRole(), ['admin', 'staff']))
            <a href="{{ route('machines.index') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 whitespace-nowrap {{ request()->is('*machine*') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="washing-machine" class="w-5 h-5 shrink-0"></i>
                <span class="whitespace-nowrap">Machines</span>
            </a>
            @endif

            @if(in_array(Auth::user()->getCurrentRole(), ['receptionist', 'admin']))
            <a href="{{ route('promotions.index') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 whitespace-nowrap {{ request()->is('*discount*') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="badge-percent" class="w-5 h-5 shrink-0"></i>
                <span class="whitespace-nowrap">Discount-Promotion</span>
            </a>
            @endif

            @if(in_array(Auth::user()->getCurrentRole(), ['staff', 'delivery', 'receptionist', 'admin']))
            <a href="{{ route('notifications.index') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 whitespace-nowrap {{ request()->is('*notification*') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="bell" class="w-5 h-5 shrink-0"></i>
                <span class="whitespace-nowrap">Notification</span>
            </a>
            @endif

            @if(in_array(Auth::user()->getCurrentRole(), ['admin','staff', 'delivery', 'receptionist']))
            <a href="{{ route('reports.index') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 whitespace-nowrap {{ request()->is('*report*') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="file-text" class="w-5 h-5 shrink-0"></i>
                <span class="whitespace-nowrap">Reports</span>
            </a>
            @endif

            @if(in_array(Auth::user()->getCurrentRole(), ['admin']))
            <a href="{{ route('settings.index') }}"
                class="flex items-center space-x-3 p-3 rounded-3xl hover:bg-blue-600 whitespace-nowrap {{ request()->is('*setting*') ? 'bg-blue-600 text-white font-semibold' : 'text-gray-300' }} transition">
                <i data-lucide="settings" class="w-5 h-5 shrink-0"></i>
                <span class="whitespace-nowrap">Setting</span>
            </a>
            @endif
        </nav>

        <div class="p-4 border-t border-blue-800">
            <button type="button" onclick="openLogoutModal()"
                class="w-full flex items-center space-x-3 p-3 rounded-lg hover:bg-red-600/20 text-red-400 transition cursor-pointer">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                <span>Logout</span>
            </button>
        </div>
    </aside>

    <!-- Right Section (Header + Content) -->
    <div class="flex-1 flex flex-col overflow-y-auto min-w-0 bg-[#F8FAFC]">
        @hasSection('header')
        @yield('header')
        @else
        @php
        $pageTitle = 'Dashboard';
        $pageSubtitle = "Welcome back, Admin! Here's what's happening today.";
        if (request()->is('*customer*')) {
        $pageTitle = 'Customer Management';
        $pageSubtitle = 'Manage your customers easily and efficiently.';
        } elseif (request()->is('*service*')) {
        $pageTitle = 'Service Management';
        $pageSubtitle = 'Manage laundry services, prices, and categories.';
        } elseif (request()->is('*order*')) {
        $pageTitle = 'Order Management';
        $pageSubtitle = 'Track and manage customer laundry orders.';
        } elseif (request()->is('*payment*')) {
        $pageTitle = 'Payment Management';
        $pageSubtitle = 'Record payments, track transactions and manage invoices across laundry facilities.';
        } elseif (request()->is('*pickup*') || request()->is('*delivery*') || request()->is('*PickupAndDelivery*')) {
        $pageTitle = 'Pickup & Delivery';
        $pageSubtitle = 'Manage schedules, drivers, and delivery routes.';
        } elseif (request()->is('*employee*')) {
        $pageTitle = 'Employee Management';
        $pageSubtitle = 'Manage staff, roles, and attendance.';
        } elseif (request()->is('*inventor*')) {
        $pageTitle = 'Inventory Management';
        $pageSubtitle = 'Track detergents, supplies, and stock levels.';
        } elseif (request()->is('*machine*')) {
        $pageTitle = 'Machine Management';
        $pageSubtitle = 'Monitor washing and drying machine status.';
        } elseif (request()->is('*promo*') || request()->is('*discount*')) {
        $pageTitle = 'Discount & Promotion Management';
        $pageSubtitle = 'Create and manage discounts, promotions and coupon codes.';
        } elseif (request()->is('*notification*')) {
        $pageTitle = 'Notification Management';
        $pageSubtitle = 'Manage system notification for orders, reminders and promotions.';
        } elseif (request()->is('*report*')) {
        $pageTitle = 'Report Management';
        $pageSubtitle = 'Generate and export business performance reports.';
        } elseif (request()->is('*setting*')) {
        $pageTitle = 'Settings';
        $pageSubtitle = 'Configure system settings and preferences.';
        }
        @endphp
        <header
            class="bg-white px-6 sm:px-8 py-3.5 sm:py-4 shadow-md rounded-b-2xl sm:rounded-b-3xl flex items-center justify-between z-30 sticky top-0 border-b border-gray-100">
            <!-- Left: Hamburger & Page Title/Subtitle -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <button type="button" onclick="toggleSidebar()"
                    class="p-1.5 -ml-1 text-gray-800 hover:text-blue-600 hover:bg-gray-100 rounded-xl transition focus:outline-none"
                    title="Toggle Navigation Menu" aria-label="Toggle navigation menu">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"
                        stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div>
                    <h1
                        class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight leading-tight whitespace-nowrap">
                        {{ $pageTitle }}
                    </h1>
                    <p class="text-[12px] sm:text-[13px] text-gray-400 font-medium leading-none mt-1">
                        {{ $pageSubtitle }}
                    </p>
                </div>
            </div>

            <!-- Right: Notification Bell & Role Switcher User Profile -->
            <div class="flex items-center space-x-3 sm:space-x-5">
                <!-- Blue Notification Bell with Solid Dot -->
                <a href="{{ route('notifications.index') }}"
                    class="relative p-2 text-blue-600 hover:bg-blue-50 rounded-full transition focus:outline-none"
                    title="Notifications">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0">
                        </path>
                    </svg>
                    <span
                        class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-white"></span>
                </a>

                @php
                $roleAvatars = [
                'Admin' => asset('images/roles/admin.png'),
                'Staff' => asset('images/roles/staff.png'),
                'Delivery' => asset('images/roles/delivery.png'),
                'Receptionist' => asset('images/roles/receptionist.png'),
                ];
                $currentRole = session('active_role', Auth::check() ? ucfirst(Auth::user()->getCurrentRole()) : 'Admin');
                $authUser = Auth::user();
                $userAvatar = $authUser ? $authUser->profile_image_url : null;
                $avatarImg = !empty($userAvatar) ? $userAvatar : ($roleAvatars[$currentRole] ?? asset('images/default-avatar.png'));
                @endphp


                <!-- User Role Switcher Dropdown Trigger -->
                <div class="relative" id="navbar-role-switcher-container">
                    <button id="navbar-role-menu-button" type="button" onclick="toggleNavbarRoleMenu()"
                        aria-haspopup="menu" aria-expanded="false"
                        class="flex items-center space-x-2.5 pl-2 pr-2.5 py-1.5 rounded-full hover:bg-gray-100 transition focus:outline-none cursor-pointer group">
                        <div
                            class="w-8 h-8 rounded-full overflow-hidden border border-blue-200 group-hover:border-blue-400 transition-colors flex items-center justify-center bg-gray-100 shrink-0">
                            <img src="{{ $avatarImg }}" alt="{{ $currentRole }}"
                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($currentRole) }}&background=2563EB&color=fff';"
                                class="w-full h-full object-cover">
                        </div>
                        <span
                            class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $currentRole }}</span>
                        <i id="navbar-role-chevron"
                            class="fa-solid fa-chevron-down text-xs text-gray-500 transition-transform duration-200"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="navbar-role-menu"
                        class="hidden absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 transform origin-top-right transition-all duration-150"
                        role="menu" tabindex="-1">
                        <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Switch Active
                                    Role</p>
                                <p class="text-sm font-bold text-gray-800">
                                    {{ Auth::check() ? Auth::user()->name : 'Admin User' }}
                                </p>
                            </div>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                {{ Auth::check() ? 'Authenticated' : 'Role-Based' }}
                            </span>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('dashboard.switch-role', 'Admin') }}"
                                class="flex items-center justify-between px-4 py-3 transition hover:bg-blue-50/60 {{ session('active_role', 'Admin') === 'Admin' ? 'bg-blue-50/90 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700' }}">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 rounded-xl flex items-center justify-center bg-blue-100 text-blue-700">
                                        <i class="fa-solid fa-shield-halved text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm font-semibold leading-tight">Administrator</div>
                                        <div class="text-xs text-gray-400 font-normal leading-tight mt-0.5">Full System
                                            Access</div>
                                    </div>
                                </div>
                                @if(session('active_role', 'Admin') === 'Admin')
                                <i class="fa-solid fa-check text-blue-600 text-sm"></i>
                                @endif
                            </a>
                            <a href="{{ route('dashboard.switch-role', 'Staff') }}"
                                class="flex items-center justify-between px-4 py-3 transition hover:bg-blue-50/60 {{ session('active_role', 'Admin') === 'Staff' ? 'bg-blue-50/90 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700' }}">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 rounded-xl flex items-center justify-center bg-emerald-100 text-emerald-700">
                                        <i class="fa-solid fa-user-group text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm font-semibold leading-tight">Laundry Staff</div>
                                        <div class="text-xs text-gray-400 font-normal leading-tight mt-0.5">Operations &
                                            Washing</div>
                                    </div>
                                </div>
                                @if(session('active_role', 'Admin') === 'Staff')
                                <i class="fa-solid fa-check text-blue-600 text-sm"></i>
                                @endif
                            </a>
                            <a href="{{ route('dashboard.switch-role', 'Delivery') }}"
                                class="flex items-center justify-between px-4 py-3 transition hover:bg-blue-50/60 {{ session('active_role', 'Admin') === 'Delivery' ? 'bg-blue-50/90 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700' }}">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 rounded-xl flex items-center justify-center bg-purple-100 text-purple-700">
                                        <i class="fa-solid fa-truck-fast text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm font-semibold leading-tight">Delivery Driver</div>
                                        <div class="text-xs text-gray-400 font-normal leading-tight mt-0.5">Logistics &
                                            Dispatch</div>
                                    </div>
                                </div>
                                @if(session('active_role', 'Admin') === 'Delivery')
                                <i class="fa-solid fa-check text-blue-600 text-sm"></i>
                                @endif
                            </a>
                            <a href="{{ route('dashboard.switch-role', 'Receptionist') }}"
                                class="flex items-center justify-between px-4 py-3 transition hover:bg-blue-50/60 {{ session('active_role', 'Admin') === 'Receptionist' ? 'bg-blue-50/90 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700' }}">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 rounded-xl flex items-center justify-center bg-amber-100 text-amber-700">
                                        <i class="fa-solid fa-bell-concierge text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm font-semibold leading-tight">Receptionist</div>
                                        <div class="text-xs text-gray-400 font-normal leading-tight mt-0.5">Front Desk &
                                            Orders</div>
                                    </div>
                                </div>
                                @if(session('active_role', 'Admin') === 'Receptionist')
                                <i class="fa-solid fa-check text-blue-600 text-sm"></i>
                                @endif
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <button type="button" onclick="openLogoutModal()"
                                class="w-full flex items-center space-x-3 px-4 py-2.5 text-rose-600 hover:bg-rose-50 transition text-sm font-semibold cursor-pointer">
                                <i class="fa-solid fa-arrow-right-from-bracket text-sm text-rose-500"></i>
                                <span>Logout</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        @endif


        <main
            class="{{ (request()->routeIs('dashboard*') || request()->is('*dashboard*')) ? 'p-3 sm:p-4 lg:p-4 flex-1' : 'p-4 flex-1' }} scrollbar-none">
            @yield('content')
        </main>
    </div>

    <!-- Global Logout Confirmation Modal -->
    <div id="logoutConfirmationModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm items-center justify-center p-4"
        style="z-index: 9999;">
        <div
            class="bg-white rounded-3xl max-w-sm sm:max-w-md w-full p-6 sm:p-7 shadow-2xl border border-gray-100 transform transition-all text-center">
            <div
                class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </div>

            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Log Out</h3>
            <p class="text-sm text-gray-500 mt-2 font-medium">
                Are you sure you want to logout?
            </p>

            <form id="globalLogoutForm" method="POST" action="{{ route('logout') }}"
                class="mt-6 flex items-center justify-center space-x-3">
                @csrf
                <button type="button" onclick="closeLogoutModal()"
                    class="flex-1 py-2.5 px-4 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer">
                    Log Out
                </button>
            </form>
        </div>
    </div>

    <!-- Script to toggle mobile sidebar -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('app-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');

            // ប្តូរស្ថានភាព hidden
            sidebar.classList.toggle('hidden');
            backdrop.classList.toggle('hidden');

            if (sidebar) {
                sidebar.classList.toggle('hidden');
            }
        }

        function toggleNavbarRoleMenu() {
            const menu = document.getElementById('navbar-role-menu');
            const chevron = document.getElementById('navbar-role-chevron');
            if (menu) {
                menu.classList.toggle('hidden');
                if (chevron) chevron.classList.toggle('rotate-180');
            }
        }

        function openLogoutModal() {
            const modal = document.getElementById('logoutConfirmationModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutConfirmationModal');
            if (modal) modal.classList.add('hidden');
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('navbar-role-switcher-container');
            const menu = document.getElementById('navbar-role-menu');
            const chevron = document.getElementById('navbar-role-chevron');
            if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }

            const logoutModal = document.getElementById('logoutConfirmationModal');
            if (logoutModal && e.target === logoutModal) {
                closeLogoutModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogoutModal();
            }
        });
    </script>


    <!-- 2. Script to render Lucide icons -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>

</html>