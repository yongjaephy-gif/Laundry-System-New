@extends('layouts.navbar')

@section('title', 'Employees')

@section('content')
<div class="space-y-6 w-full mx-auto">

    <!-- SUCCESS NOTIFICATION -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
        <div class="flex items-center space-x-3">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        <script>
            setTimeout(function() {
                window.location.reload();
            }, 200);
        </script>
    </div>
    @endif

    <!-- VALIDATION ERRORS -->
    @if(isset($errors) && $errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs mb-4">
        <div class="flex items-center space-x-2 font-bold mb-1">
            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
            <span>Please correct the errors below:</span>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- TOP BAR: ADD EMPLOYEE BUTTON -->
    <div class="flex justify-end items-center">
        <button type="button"
            onclick="openAddEmployeeModal()"
            class="bg-[#2563EB] hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-xs transition flex items-center space-x-2 text-sm cursor-pointer active:scale-95">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Add Employee</span>
        </button>
    </div>

    <!-- SUMMARY METRIC CARDS (3 CARDS EXACTLY AS SCREENSHOT) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Total Employees -->
        <div class="bg-[#F0F5FF]/70 border border-blue-100/70 rounded-2xl p-6 shadow-xs flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-100/80 text-[#2563EB] flex items-center justify-center text-2xl flex-shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Employees</p>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5 tracking-tight" id="cardTotalCount">{{ $stats['total'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- Card 2: Active -->
        <div class="bg-[#F0FDF4]/70 border border-emerald-100/70 rounded-2xl p-6 shadow-xs flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-100/80 text-emerald-600 flex items-center justify-center text-2xl flex-shrink-0">
                <i class="fa-solid fa-user"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Active</p>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5 tracking-tight" id="cardActiveCount">{{ $stats['active'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- Card 3: Inactive -->
        <div class="bg-gray-50/90 border border-gray-200/60 rounded-2xl p-6 shadow-xs flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-gray-200/80 text-gray-500 flex items-center justify-center text-2xl flex-shrink-0">
                <i class="fa-solid fa-user"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Inactive</p>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5 tracking-tight" id="cardInactiveCount">{{ $stats['inactive'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- MAIN CARD: SEARCH, FILTERS & TABLE -->
    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-gray-100 p-6 sm:p-7">

        <!-- Controls Toolbar: Search & Dropdowns -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <!-- Search Input -->
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input type="text"
                    id="employeeSearchInput"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Search employee..."
                    oninput="filterEmployeeTable()"
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition shadow-2xs">
            </div>

            <!-- Role & Status Filters -->
            <div class="flex items-center space-x-3">
                <!-- Role Filter -->
                <div class="relative min-w-[140px]">
                    <select id="roleFilterSelect"
                        name="role"
                        onchange="filterEmployeeTable()"
                        class="appearance-none w-full bg-white border border-gray-200 rounded-xl pl-4 pr-9 py-2.5 text-sm text-gray-700 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 cursor-pointer">
                        <option value="All Roles" {{ ($roleFilter ?? '') === 'All Roles' || empty($roleFilter) ? 'selected' : '' }}>All Roles</option>
                        <option value="Admin" {{ ($roleFilter ?? '') === 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Receptionist" {{ ($roleFilter ?? '') === 'Receptionist' ? 'selected' : '' }}>Receptionist</option>
                        <option value="Laundry Staff" {{ ($roleFilter ?? '') === 'Laundry Staff' ? 'selected' : '' }}>Laundry Staff</option>
                        <option value="Ironing Staff" {{ ($roleFilter ?? '') === 'Ironing Staff' ? 'selected' : '' }}>Ironing Staff</option>
                        <option value="Delivery Staff" {{ ($roleFilter ?? '') === 'Delivery Staff' ? 'selected' : '' }}>Delivery Staff</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                        <i class="fa-solid fa-chevron-down text-[11px]"></i>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="relative min-w-[130px]">
                    <select id="statusFilterSelect"
                        name="status"
                        onchange="filterEmployeeTable()"
                        class="appearance-none w-full bg-white border border-gray-200 rounded-xl pl-4 pr-9 py-2.5 text-sm text-gray-700 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 cursor-pointer">
                        <option value="All Status" {{ ($statusFilter ?? '') === 'All Status' || empty($statusFilter) ? 'selected' : '' }}>All Status</option>
                        <option value="Active" {{ ($statusFilter ?? '') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ ($statusFilter ?? '') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                        <i class="fa-solid fa-chevron-down text-[11px]"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- EMPLOYEE DATA TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="employeesTable">
                <thead>
                    <tr class="bg-[#F8FAFC] text-[12px] font-bold text-gray-500 border-b border-gray-100">
                        <th class="py-3.5 px-6 font-semibold w-14">#</th>
                        <th class="py-3.5 px-6 font-semibold">Name</th>
                        <th class="py-3.5 px-6 font-semibold">Role</th>
                        <th class="py-3.5 px-6 font-semibold">Phone</th>
                        <th class="py-3.5 px-6 font-semibold">Email</th>
                        <th class="py-3.5 px-6 font-semibold">Status</th>
                        <th class="py-3.5 px-6 font-semibold text-center w-36">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm" id="employeeTableBody">
                    @forelse($employees as $index => $emp)
                    @php
                    // Assign badges matching screenshot palette
                    $role = $emp->role ?? 'Laundry Staff';
                    $roleClass = 'bg-[#FEF3C7] text-[#D97706]'; // default yellow/amber for Laundry Staff

                    if ($role === 'Admin') {
                    $roleClass = 'bg-[#F3E8FF] text-[#9333EA]'; // soft purple
                    } elseif ($role === 'Receptionist') {
                    $roleClass = 'bg-[#DBEAFE] text-[#2563EB]'; // soft blue
                    } elseif ($role === 'Delivery Staff') {
                    $roleClass = 'bg-[#DCFCE7] text-[#16A34A]'; // soft green
                    } elseif ($role === 'Ironing Staff' || $role === 'Presser') {
                    $roleClass = 'bg-[#E0E7FF] text-[#4F46E5]'; // soft indigo
                    }

                    $status = $emp->status ?? 'Active';
                    $statusClass = $status === 'Active'
                    ? 'bg-[#DCFCE7] text-[#16A34A]'
                    : 'bg-[#FEE2E2] text-[#DC2626]';

                    $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($emp->name) . '&background=2563EB&color=fff';
                    $avatarUrl = $emp->avatar ?: $defaultAvatar;
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition employee-row"
                        id="employee-row-{{ $emp->id }}"
                        data-name="{{ strtolower($emp->name ?? '') }}"
                        data-role="{{ $emp->role ?? '' }}"
                        data-phone="{{ $emp->phone ?? '' }}"
                        data-email="{{ strtolower($emp->email ?? '') }}"
                        data-status="{{ $emp->status ?? '' }}">

                        <!-- # Column -->
                        <td class="py-4 px-6 text-gray-500 font-medium text-xs">
                            {{ $loop->iteration ?? ($index + 1) }}
                        </td>

                        <!-- Name Column with Avatar -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-3.5">
                                <img src="{{ $avatarUrl }}" alt="{{ $emp->name }}" onerror="this.src='{{ $defaultAvatar }}'"
                                    class="w-10 h-10 rounded-full object-cover border border-gray-100 shadow-2xs">
                                <span class="font-bold text-gray-900 text-sm whitespace-nowrap">{{ $emp->name }}</span>
                            </div>
                        </td>

                        <!-- Role Column -->
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $roleClass }}">
                                {{ $emp->role }}
                            </span>
                        </td>

                        <!-- Phone Column -->
                        <td class="py-4 px-6 text-gray-600 font-medium whitespace-nowrap">
                            {{ $emp->phone }}
                        </td>

                        <!-- Email Column -->
                        <td class="py-4 px-6 text-gray-600 whitespace-nowrap">
                            {{ $emp->email }}
                        </td>

                        <!-- Status Column -->
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="inline-block px-3.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ $emp->status }}
                            </span>
                        </td>

                        <!-- Actions Column: 3 colored icons (Eye, Pencil, Trash) -->
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- View / Watch Button (Cyan/Sky) -->
                                <button type="button"
                                    data-id="{{ $emp->employee_id ?? $emp->id }}"
                                    data-employee-id="{{ $emp->formatted_id ?? ('EMP' . str_pad((string)($emp->employee_id ?? $emp->id), 3, '0', STR_PAD_LEFT)) }}"
                                    data-name="{{ $emp->name }}"
                                    data-role="{{ $emp->role }}"
                                    data-phone="{{ $emp->phone }}"
                                    data-email="{{ $emp->email }}"
                                    data-address="{{ $emp->address ?? 'Phnom Penh, Cambodia' }}"
                                    data-status="{{ $emp->status }}"
                                    data-schedule="{{ $emp->work_schedule ?? 'Full Time (08:00 AM - 05:00 PM)' }}"
                                    data-details="{{ $emp->job_role_details ?? '' }}"
                                    data-avatar="{{ $avatarUrl }}"
                                    data-attendance="{{ $emp->attendance_status ?? ($emp->status === 'Active' ? 'Present' : 'Absent') }}"
                                    onclick="handleViewEmployeeClick(this)"
                                    class="w-8 h-8 rounded-lg bg-[#2563EB] hover:bg-blue-700 text-white flex items-center justify-center shadow-2xs transition active:scale-90 cursor-pointer"
                                    title="View Details">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>

                                <!-- Edit Button (Emerald Green) -->
                                <button type="button"
                                    data-id="{{ $emp->employee_id ?? $emp->id }}"
                                    data-employee-id="{{ $emp->formatted_id ?? ('EMP' . str_pad((string)($emp->employee_id ?? $emp->id), 3, '0', STR_PAD_LEFT)) }}"
                                    data-name="{{ $emp->name }}"
                                    data-role="{{ $emp->role }}"
                                    data-phone="{{ $emp->phone }}"
                                    data-email="{{ $emp->email }}"
                                    data-address="{{ $emp->address ?? 'Phnom Penh, Cambodia' }}"
                                    data-status="{{ $emp->status }}"
                                    data-schedule="{{ $emp->work_schedule ?? 'Full Time (08:00 AM - 05:00 PM)' }}"
                                    data-details="{{ $emp->job_role_details ?? '' }}"
                                    data-avatar="{{ $avatarUrl }}"
                                    onclick="handleEditEmployeeClick(this)"
                                    class="w-8 h-8 rounded-lg bg-[#10B981] hover:bg-emerald-600 text-white flex items-center justify-center shadow-2xs transition active:scale-90 cursor-pointer"
                                    title="Edit Employee">
                                    <i class="fa-solid fa-pencil text-xs"></i>
                                </button>

                                <!-- Delete Button (Rose Red) -->
                                <button type="button"
                                    data-id="{{ $emp->employee_id ?? $emp->id }}"
                                    data-name="{{ $emp->name }}"
                                    onclick="handleDeleteEmployeeClick(this)"
                                    class="w-8 h-8 rounded-lg bg-[#EF4444] hover:bg-rose-600 text-white flex items-center justify-center shadow-2xs transition active:scale-90 cursor-pointer"
                                    title="Delete Employee">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow">
                        <td colspan="7" class="py-12 text-center text-gray-400 font-medium">
                            <i class="fa-solid fa-user-slash text-3xl mb-3 text-gray-300 block"></i>
                            No employees found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer & Pagination -->
        <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-gray-500">
            <div id="tableInfoText">
                Showing 1 - {{ count($employees) }} of {{ $stats['total'] ?? count($employees) }} employees
            </div>

            <div class="flex items-center space-x-2">
                <button type="button" class="text-gray-400 hover:text-gray-700 font-medium px-2 py-1 transition cursor-pointer">
                    &lt; Previous
                </button>
                <button type="button" class="w-7 h-7 rounded-lg bg-[#2563EB] text-white font-bold flex items-center justify-center text-xs shadow-2xs">
                    1
                </button>
                <button type="button" class="text-gray-400 hover:text-gray-700 font-medium px-2 py-1 transition cursor-pointer">
                    Next &gt;
                </button>
            </div>
        </div>

    </div>

</div>

<!-- ========================================== -->
<!-- 1. ADD NEW EMPLOYEE MODAL                  -->
<!-- ========================================== -->
<div id="addEmployeeModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-5 sm:p-7 shadow-2xl animate-fade-in border border-gray-100 my-auto max-h-250 flex flex-col">

        <!-- Modal Header -->
        <div class="flex items-start justify-between pb-3 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#2563EB] flex items-center justify-center text-lg flex-shrink-0">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-[#0F172A] tracking-tight leading-tight">Add New Employee</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Fill in the information below to add a new employee.</p>
                </div>
            </div>
            <button type="button" onclick="closeAddEmployeeModal()" class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center text-xl transition cursor-pointer">
                &times;
            </button>
        </div>

        <!-- Form -->
        <form id="addEmployeeForm" action="" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden mt-3">
            @csrf

            <!-- Scrollable Body with 2-Column Responsive Layout -->
            <div class="overflow-y-auto flex-1 pr-1 sm:pr-2 space-y-3.5 text-left">

                <!-- Profile Photo Upload Section -->
                <div class="flex items-center space-x-4 pb-3 border-b border-gray-100">
                    <div class="relative group">
                        <img id="addEmpAvatarPreview"
                            src="https://ui-avatars.com/api/?name=New+User&background=2563EB&color=fff"
                            alt="Avatar Preview"
                            class="w-14 h-14 rounded-full object-cover border-2 border-blue-500/20 shadow-xs">
                        <label for="addEmpAvatarInput"
                            class="absolute inset-0 bg-black/40 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                            <i class="fa-solid fa-camera text-xs"></i>
                        </label>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1">
                            Profile Photo
                        </label>
                        <div class="flex items-center space-x-2">
                            
                            <input type="file"
                                id="addEmpAvatarInput"
                                name="avatar"
                                accept="image/*"
                                class="hidden"
                                onchange="previewModalAvatar(this, 'addEmpAvatarPreview')">
                        </div>
                    </div>
                </div>

                <!-- Row 1: Full Name & Role -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- 1. Full Name -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-regular fa-user text-sm"></i>
                            </div>
                            <input type="text"
                                name="name"
                                required
                                placeholder="Enter full name"
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                        </div>
                    </div>

                    <!-- 2. Role -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Role <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-users text-xs"></i>
                            </div>
                            <select name="role"
                                required
                                class="appearance-none w-full pl-10 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition cursor-pointer">
                                <option value="" disabled selected>Select role</option>
                                <option value="Admin">Admin</option>
                                <option value="Receptionist">Receptionist</option>
                                <option value="Laundry Staff">Laundry Staff</option>
                                <option value="Delivery Staff">Delivery Staff</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                                <i class="fa-solid fa-chevron-down text-[11px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Phone & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- 3. Phone Number -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Phone Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </div>
                            <input type="text"
                                name="phone"
                                required
                                placeholder="Enter phone number"
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                        </div>
                    </div>

                    <!-- 4. Email -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Email <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-regular fa-envelope text-xs"></i>
                            </div>
                            <input type="email"
                                name="email"
                                required
                                placeholder="Enter email address"
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                        </div>
                    </div>
                </div>

                <!-- Row 3: Address -->
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">
                        Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-location-dot text-xs"></i>
                        </div>
                        <input type="text"
                            name="address"
                            placeholder="Enter address"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    </div>
                </div>

                <!-- Row 4: Password & Confirm Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- 6. Password -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </div>
                            <input type="password"
                                id="addEmpPassword"
                                name="password"
                                placeholder="Enter password"
                                class="w-full pl-10 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                            <button type="button"
                                onclick="togglePasswordVisibility('addEmpPassword', this)"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer">
                                <i class="fa-regular fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- 7. Confirm Password -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </div>
                            <input type="password"
                                id="addEmpConfirmPassword"
                                name="confirm_password"
                                placeholder="Confirm password"
                                class="w-full pl-10 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                            <button type="button"
                                onclick="togglePasswordVisibility('addEmpConfirmPassword', this)"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer">
                                <i class="fa-regular fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Row 5: Status & Work Schedule -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- 8. Status (Radio Buttons) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center space-x-6 h-[42px] px-3.5 bg-gray-50/70 border border-gray-200/80 rounded-xl">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="status" value="Active" checked class="w-4 h-4 text-[#2563EB] focus:ring-blue-500 border-gray-300">
                                <span class="text-xs font-medium text-gray-700">Active</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="status" value="Inactive" class="w-4 h-4 text-[#2563EB] focus:ring-blue-500 border-gray-300">
                                <span class="text-xs font-medium text-gray-700">Inactive</span>
                            </label>
                        </div>
                    </div>

                    <!-- 9. Work Schedule -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Work Schedule <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-regular fa-calendar-days text-xs"></i>
                            </div>
                            <select name="work_schedule"
                                required
                                class="appearance-none w-full pl-10 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition cursor-pointer">
                                <option value="" disabled selected>Select work schedule</option>
                                <option value="Morning Shift (07:00 AM - 03:00 PM)">Morning Shift (07:00 AM - 03:00 PM)</option>
                                <option value="Afternoon Shift (01:00 PM - 09:00 PM)">Afternoon Shift (01:00 PM - 09:00 PM)</option>
                                <option value="Night Shift (09:00 PM - 05:00 AM)">Night Shift (09:00 PM - 05:00 AM)</option>
                                <option value="Full Time (08:00 AM - 05:00 PM)">Full Time (08:00 AM - 05:00 PM)</option>
                                <option value="Flexible Shift">Flexible Shift</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                                <i class="fa-solid fa-chevron-down text-[11px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 6: Job Role Details (optional) -->
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">
                        Job Role Details <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <div class="relative">
                        <textarea name="job_role_details"
                            rows="2"
                            maxlength="500"
                            oninput="document.getElementById('addRoleNotesCount').innerText = this.value.length + '/500'"
                            placeholder="Add notes (optional)..."
                            class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition resize-none"></textarea>
                        <div class="text-right text-[11px] text-gray-400 mt-0.5" id="addRoleNotesCount">
                            0/500
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer Buttons (Always Visible at bottom) -->
            <div class="flex items-center justify-end pt-3 mt-2 border-t border-gray-100 gap-3 flex-shrink-0">
                <button type="button"
                    onclick="closeAddEmployeeModal()"
                    class="py-2.5 px-5 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-sm font-semibold transition cursor-pointer text-center">
                    Cancel
                </button>
                <button type="submit"
                    class="py-2.5 px-6 rounded-xl bg-[#2563EB] hover:bg-blue-700 text-white text-sm font-semibold shadow-xs transition flex items-center justify-center space-x-2 cursor-pointer active:scale-95">
                    <i class="fa-solid fa-bookmark text-xs"></i>
                    <span>Save Employee</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================== -->
<!-- 2. EDIT / UPDATE EMPLOYEE MODAL           -->
<!-- ========================================== -->
<div id="editEmployeeModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-5 sm:p-7 shadow-2xl animate-fade-in border border-gray-100 my-auto max-h-[92vh] flex flex-col">

        <!-- Modal Header -->
        <div class="flex items-start justify-between pb-3.5 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-[#0F172A] tracking-tight leading-tight">Edit Employee</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Update staff details, assigned role and shift schedule.</p>
                </div>
            </div>
            <button type="button" onclick="closeEditEmployeeModal()" class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center text-xl transition cursor-pointer">
                &times;
            </button>
        </div>

        <!-- Edit Form -->
        <form id="editEmployeeForm" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden mt-3">
            @csrf
            @method('PUT')

            <!-- Scrollable Body with 2-Column Responsive Layout -->
            <div class="overflow-y-auto flex-1 pr-1 sm:pr-2 space-y-3.5 text-left">

                <!-- Edit Profile Photo Upload Section -->
                <div class="flex items-center space-x-4 pb-3 border-b border-gray-100">
                    <div class="relative group">
                        <img id="editEmpAvatarPreview"
                            src="https://ui-avatars.com/api/?name=User&background=2563EB&color=fff"
                            alt="Avatar Preview"
                            class="w-14 h-14 rounded-full object-cover border-2 border-emerald-500/20 shadow-xs">
                        <label for="editEmpAvatarInput"
                            class="absolute inset-0 bg-black/40 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                            <i class="fa-solid fa-camera text-xs"></i>
                        </label>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1">
                            Profile Photo <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <div class="flex items-center space-x-2">
                            <label for="editEmpAvatarInput"
                                class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 text-xs font-semibold rounded-lg cursor-pointer transition flex items-center space-x-1.5">
                                <i class="fa-solid fa-upload text-[11px]"></i>
                                <span>Change Photo</span>
                            </label>
                            <input type="file"
                                id="editEmpAvatarInput"
                                name="avatar"
                                accept="image/*"
                                class="hidden"
                                onchange="previewModalAvatar(this, 'editEmpAvatarPreview')">
                            <span id="editEmpAvatarFileName" class="text-xs text-gray-400 truncate max-w-[160px]"></span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-0.5">PNG, JPG, or WEBP (Max 2MB)</p>
                    </div>
                </div>

                <!-- Row 1: Full Name & Role -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-regular fa-user text-sm"></i>
                            </div>
                            <input type="text"
                                id="editEmpName"
                                name="name"
                                required
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                        </div>
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Role <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-users text-xs"></i>
                            </div>
                            <select id="editEmpRole"
                                name="role"
                                required
                                class="appearance-none w-full pl-10 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition cursor-pointer">
                                <option value="Admin">Admin</option>
                                <option value="Receptionist">Receptionist</option>
                                <option value="Laundry Staff">Laundry Staff</option>
                                <option value="Ironing Staff">Ironing Staff</option>
                                <option value="Delivery Staff">Delivery Staff</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                                <i class="fa-solid fa-chevron-down text-[11px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Phone Number & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Phone Number -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Phone Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </div>
                            <input type="text"
                                id="editEmpPhone"
                                name="phone"
                                required
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Email <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-regular fa-envelope text-xs"></i>
                            </div>
                            <input type="email"
                                id="editEmpEmail"
                                name="email"
                                required
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                        </div>
                    </div>
                </div>

                <!-- Row 3: Address -->
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">
                        Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-location-dot text-xs"></i>
                        </div>
                        <input type="text"
                            id="editEmpAddress"
                            name="address"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    </div>
                </div>

                <!-- Row 4: Status & Work Schedule -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Status (Radio) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center space-x-6 h-[42px] px-3.5 bg-gray-50/70 border border-gray-200/80 rounded-xl">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" id="editEmpStatusActive" name="status" value="Active" class="w-4 h-4 text-[#2563EB] focus:ring-blue-500 border-gray-300">
                                <span class="text-xs font-medium text-gray-700">Active</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" id="editEmpStatusInactive" name="status" value="Inactive" class="w-4 h-4 text-[#2563EB] focus:ring-blue-500 border-gray-300">
                                <span class="text-xs font-medium text-gray-700">Inactive</span>
                            </label>
                        </div>
                    </div>

                    <!-- Work Schedule -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Work Schedule <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-regular fa-calendar-days text-xs"></i>
                            </div>
                            <select id="editEmpSchedule"
                                name="work_schedule"
                                required
                                class="appearance-none w-full pl-10 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition cursor-pointer">
                                <option value="Morning Shift (07:00 AM - 03:00 PM)">Morning Shift (07:00 AM - 03:00 PM)</option>
                                <option value="Afternoon Shift (01:00 PM - 09:00 PM)">Afternoon Shift (01:00 PM - 09:00 PM)</option>
                                <option value="Night Shift (09:00 PM - 05:00 AM)">Night Shift (09:00 PM - 05:00 AM)</option>
                                <option value="Full Time (08:00 AM - 05:00 PM)">Full Time (08:00 AM - 05:00 PM)</option>
                                <option value="Flexible Shift">Flexible Shift</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                                <i class="fa-solid fa-chevron-down text-[11px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 5: Job Role Details -->
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">
                        Job Role Details <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <div class="relative">
                        <textarea id="editEmpJobDetails"
                            name="job_role_details"
                            rows="2"
                            maxlength="500"
                            oninput="document.getElementById('editRoleNotesCount').innerText = this.value.length + '/500'"
                            class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition resize-none"></textarea>
                        <div class="text-right text-[11px] text-gray-400 mt-0.5" id="editRoleNotesCount">
                            0/500
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer Buttons (Always Visible at bottom) -->
            <div class="flex items-center justify-end pt-3 mt-2 border-t border-gray-100 gap-3 flex-shrink-0">
                <button type="button"
                    onclick="closeEditEmployeeModal()"
                    class="py-2.5 px-5 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-sm font-semibold transition cursor-pointer text-center">
                    Cancel
                </button>
                <button type="submit"
                    class="py-2.5 px-6 rounded-xl bg-[#2563EB] hover:bg-blue-700 text-white text-sm font-semibold shadow-xs transition flex items-center justify-center space-x-2 cursor-pointer active:scale-95">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Update Employee</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================== -->
<!-- 3. VIEW / WATCH EMPLOYEE PROFILE & ATTENDANCE -->
<!-- ========================================== -->
<div id="viewEmployeeModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl animate-fade-in border border-gray-100 my-8">

        <!-- Header -->
        <div class="flex items-start justify-between pb-4 border-b border-gray-100">
            <div class="flex items-center space-x-4">
                <img id="viewEmpAvatar"
                    src=""
                    alt=""
                    class="w-14 h-14 rounded-full object-cover border-2 border-blue-500/20 shadow-xs">
                <div>
                    <h3 id="viewEmpName" class="text-xl font-extrabold text-gray-900 leading-tight"></h3>
                    <div class="flex items-center space-x-2 mt-1">
                        <span id="viewEmpRole" class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                        <span id="viewEmpStatus" class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="closeViewEmployeeModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-light leading-none cursor-pointer">
                &times;
            </button>
        </div>

        <!-- Body Details -->
        <div class="py-4 space-y-4 text-sm">
            <div class="grid grid-cols-2 gap-3 bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100">
                <div>
                    <span class="text-xs text-gray-400 block font-medium">Employee ID</span>
                    <span id="viewEmpId" class="font-mono font-bold text-gray-800 text-sm"></span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block font-medium">Phone Number</span>
                    <span id="viewEmpPhone" class="font-semibold text-gray-800 text-sm"></span>
                </div>
                <div class="col-span-2">
                    <span class="text-xs text-gray-400 block font-medium">Email Address</span>
                    <span id="viewEmpEmail" class="font-medium text-gray-800 text-sm"></span>
                </div>
                <div class="col-span-2">
                    <span class="text-xs text-gray-400 block font-medium">Address</span>
                    <span id="viewEmpAddress" class="font-medium text-gray-800 text-sm"></span>
                </div>
            </div>

            <!-- Work Schedule & Assigned Role -->
            <div class="bg-blue-50/50 p-3.5 rounded-2xl border border-blue-100/60 space-y-2">
                <div class="flex items-center space-x-2 text-blue-800 font-semibold text-xs">
                    <i class="fa-regular fa-clock"></i>
                    <span>Shift Schedule & Role Assignment</span>
                </div>
                <div class="text-gray-700 text-xs">
                    <span class="font-bold text-gray-900 block" id="viewEmpSchedule"></span>
                    <p class="text-gray-600 mt-1" id="viewEmpDetails"></p>
                </div>
            </div>

            <!-- Attendance Management Section -->
            <div class="bg-emerald-50/50 p-3.5 rounded-2xl border border-emerald-100/60">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-xs text-emerald-800 block font-bold">Shift Attendance</span>
                        <span id="viewEmpAttendanceStatus" class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            Present
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="button"
                            onclick="updateAttendance('Present')"
                            class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-2xs cursor-pointer">
                            Present
                        </button>
                        <button type="button"
                            onclick="updateAttendance('Late')"
                            class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs transition shadow-2xs cursor-pointer">
                            Late
                        </button>
                        <button type="button"
                            onclick="updateAttendance('Absent')"
                            class="px-3 py-1.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-semibold text-xs transition shadow-2xs cursor-pointer">
                            Absent
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-400">Live attendance & roster sync</span>
            <div class="flex items-center space-x-2">
                <button type="button"
                    id="viewModalEditBtn"
                    class="px-5 py-2.5 rounded-xl bg-[#2563EB] hover:bg-blue-700 text-white font-semibold text-sm transition shadow-2xs cursor-pointer">
                    Edit Employee
                </button>
                <button type="button"
                    onclick="closeViewEmployeeModal()"
                    class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 font-semibold text-sm transition cursor-pointer">
                    Close
                </button>
            </div>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- 4. DELETE CONFIRMATION MODAL               -->
<!-- ========================================== -->
<div id="deleteEmployeeModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl text-center border border-gray-100 animate-fade-in">
        <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Delete Employee</h3>
        <p class="text-xs text-gray-500 mb-6">Are you sure you want to remove <span id="deleteEmpNameSpan" class="font-bold text-gray-800"></span>? This action cannot be undone.</p>

        <form id="deleteEmployeeForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-center space-x-3">
                <button type="button" onclick="closeDeleteEmployeeModal()" class="w-1/2 py-2.5 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 text-sm font-semibold transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="w-1/2 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold transition shadow-2xs cursor-pointer">
                    Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JAVASCRIPT LOGIC -->
<script>
    let currentViewingEmployee = null;

    /**
     * Resolves the exact employee endpoint relative to current browser URL.
     * Works seamlessly on localhost, XAMPP subdirectories, and artisan serve.
     */
    function getAppEndpoint(subpath = '') {
        const cleanPath = window.location.pathname.replace(/\/+$/, '');
        let base = cleanPath;
        const match = cleanPath.match(/^(.*?\/(?:employee|employees))(?:\/.*)?$/);
        if (match) {
            base = match[1];
        }
        if (subpath) {
            return (base + '/' + subpath).replace(/\/+/g, '/');
        }
        return base;
    }

    // Extract employee data safely from element attributes
    function getEmployeeFromButton(btn) {
        return {
            id: btn.getAttribute('data-id') || '',
            employee_id: btn.getAttribute('data-employee-id') || '',
            name: btn.getAttribute('data-name') || '',
            role: btn.getAttribute('data-role') || 'Laundry Staff',
            phone: btn.getAttribute('data-phone') || '',
            email: btn.getAttribute('data-email') || '',
            address: btn.getAttribute('data-address') || 'Phnom Penh, Cambodia',
            status: btn.getAttribute('data-status') || 'Active',
            work_schedule: btn.getAttribute('data-schedule') || 'Full Time (08:00 AM - 05:00 PM)',
            job_role_details: btn.getAttribute('data-details') || '',
            avatar: btn.getAttribute('data-avatar') || '',
            attendance_status: btn.getAttribute('data-attendance') || 'Present'
        };
    }

    function handleViewEmployeeClick(btn) {
        const emp = getEmployeeFromButton(btn);
        openViewEmployeeModal(emp);
    }

    function handleEditEmployeeClick(btn) {
        const emp = getEmployeeFromButton(btn);
        openEditEmployeeModal(emp);
    }

    function handleDeleteEmployeeClick(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        confirmDeleteEmployee(id, name);
    }

    // Toggle Password Visibility
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Preview uploaded avatar image
    function previewModalAvatar(input, previewImgId) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewImgId);
                if (img) img.src = e.target.result;
            };
            reader.readAsDataURL(file);

            const fileNameSpanId = previewImgId === 'addEmpAvatarPreview' ? 'addEmpAvatarFileName' : 'editEmpAvatarFileName';
            const span = document.getElementById(fileNameSpanId);
            if (span) {
                span.innerText = file.name;
            }
        }
    }

    // Modal Handlers: Add
    function openAddEmployeeModal() {
        const form = document.getElementById('addEmployeeForm');
        if (form) {
            form.action = getAppEndpoint();
            form.reset();
        }
        const preview = document.getElementById('addEmpAvatarPreview');
        if (preview) preview.src = 'https://ui-avatars.com/api/?name=New+User&background=2563EB&color=fff';
        const fileSpan = document.getElementById('addEmpAvatarFileName');
        if (fileSpan) fileSpan.innerText = 'No file chosen';

        document.getElementById('addEmployeeModal').classList.remove('hidden');
    }

    function closeAddEmployeeModal() {
        document.getElementById('addEmployeeModal').classList.add('hidden');
    }

    // Modal Handlers: Edit
    function openEditEmployeeModal(emp) {
        document.getElementById('editEmpName').value = emp.name || '';
        document.getElementById('editEmpRole').value = emp.role || 'Laundry Staff';
        document.getElementById('editEmpPhone').value = emp.phone || '';
        document.getElementById('editEmpEmail').value = emp.email || '';
        document.getElementById('editEmpAddress').value = emp.address || 'Phnom Penh, Cambodia';
        document.getElementById('editEmpSchedule').value = emp.work_schedule || 'Full Time (08:00 AM - 05:00 PM)';
        document.getElementById('editEmpJobDetails').value = emp.job_role_details || '';
        document.getElementById('editRoleNotesCount').innerText = (emp.job_role_details ? emp.job_role_details.length : 0) + '/500';

        // Avatar preview
        const editPreview = document.getElementById('editEmpAvatarPreview');
        if (editPreview) {
            editPreview.src = emp.avatar || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(emp.name) + '&background=2563EB&color=fff');
        }
        const editFileSpan = document.getElementById('editEmpAvatarFileName');
        if (editFileSpan) editFileSpan.innerText = '';
        const editFileInput = document.getElementById('editEmpAvatarInput');
        if (editFileInput) editFileInput.value = '';

        if (emp.status === 'Inactive') {
            document.getElementById('editEmpStatusInactive').checked = true;
        } else {
            document.getElementById('editEmpStatusActive').checked = true;
        }

        const editForm = document.getElementById('editEmployeeForm');
        if (editForm) {
            editForm.action = getAppEndpoint(emp.id);
        }
        document.getElementById('editEmployeeModal').classList.remove('hidden');
    }

    function closeEditEmployeeModal() {
        document.getElementById('editEmployeeModal').classList.add('hidden');
    }

    // Modal Handlers: View Profile & Attendance
    function openViewEmployeeModal(emp) {
        currentViewingEmployee = emp;
        document.getElementById('viewEmpName').innerText = emp.name;
        document.getElementById('viewEmpId').innerText = emp.employee_id || ('EMP' + String(emp.id).padStart(3, '0'));
        document.getElementById('viewEmpPhone').innerText = emp.phone;
        document.getElementById('viewEmpEmail').innerText = emp.email;
        document.getElementById('viewEmpAddress').innerText = emp.address || 'Phnom Penh, Cambodia';
        document.getElementById('viewEmpSchedule').innerText = emp.work_schedule || 'Standard Shift';
        document.getElementById('viewEmpDetails').innerText = emp.job_role_details || 'Assigned to active laundry station duties.';

        const avatarImg = document.getElementById('viewEmpAvatar');
        avatarImg.src = emp.avatar || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(emp.name) + '&background=2563EB&color=fff');

        // Role pill
        const roleSpan = document.getElementById('viewEmpRole');
        roleSpan.innerText = emp.role;
        roleSpan.className = 'inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold ';
        if (emp.role === 'Manager') roleSpan.className += 'bg-[#F3E8FF] text-[#9333EA]';
        else if (emp.role === 'Receptionist') roleSpan.className += 'bg-[#DBEAFE] text-[#2563EB]';
        else if (emp.role === 'Delivery Staff') roleSpan.className += 'bg-[#DCFCE7] text-[#16A34A]';
        else if (emp.role === 'Ironing Staff') roleSpan.className += 'bg-[#E0E7FF] text-[#4F46E5]';
        else roleSpan.className += 'bg-[#FEF3C7] text-[#D97706]';

        // Status pill
        const statusSpan = document.getElementById('viewEmpStatus');
        statusSpan.innerText = emp.status;
        statusSpan.className = 'inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold ' +
            (emp.status === 'Active' ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-[#FEE2E2] text-[#DC2626]');

        // Attendance status
        const attStatus = emp.attendance_status || (emp.status === 'Active' ? 'Present' : 'Absent');
        setAttendanceBadge(attStatus);

        // Connect Edit button in View Modal
        document.getElementById('viewModalEditBtn').onclick = function() {
            closeViewEmployeeModal();
            openEditEmployeeModal(emp);
        };

        document.getElementById('viewEmployeeModal').classList.remove('hidden');
    }

    function closeViewEmployeeModal() {
        document.getElementById('viewEmployeeModal').classList.add('hidden');
    }

    function setAttendanceBadge(status) {
        const attSpan = document.getElementById('viewEmpAttendanceStatus');
        attSpan.innerText = status;
        if (status === 'Present') {
            attSpan.className = 'inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
        } else if (status === 'Late') {
            attSpan.className = 'inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800';
        } else {
            attSpan.className = 'inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800';
        }
    }

    // Update Attendance AJAX
    function updateAttendance(status) {
        if (!currentViewingEmployee) return;
        const empId = currentViewingEmployee.id;
        const token = document.querySelector('input[name="_token"]')?.value || '';

        setAttendanceBadge(status);
        currentViewingEmployee.attendance_status = status;

        fetch(getAppEndpoint(empId + '/attendance'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    attendance_status: status
                })
            })
            .then(res => res.json())
            .then(data => {
                setAttendanceBadge(data.attendance_status || status);
                // Also update row in table if present
                const rowBtn = document.querySelector(`button[data-id="${empId}"][title="View Details"]`);
                if (rowBtn) {
                    rowBtn.setAttribute('data-attendance', data.attendance_status || status);
                }
            })
            .catch(err => {
                setAttendanceBadge(status);
            });
    }

    // Modal Handlers: Delete Confirmation
    function confirmDeleteEmployee(id, name) {
        document.getElementById('deleteEmpNameSpan').innerText = name;
        const deleteForm = document.getElementById('deleteEmployeeForm');
        if (deleteForm) {
            deleteForm.action = getAppEndpoint(id);
        }
        document.getElementById('deleteEmployeeModal').classList.remove('hidden');
    }

    function closeDeleteEmployeeModal() {
        document.getElementById('deleteEmployeeModal').classList.add('hidden');
    }

    // Client-side Instant Filter & Search Table
    function filterEmployeeTable() {
        const searchVal = (document.getElementById('employeeSearchInput').value || '').trim().toLowerCase();
        const roleVal = document.getElementById('roleFilterSelect').value;
        const statusVal = document.getElementById('statusFilterSelect').value;

        const rows = document.querySelectorAll('.employee-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const role = row.getAttribute('data-role') || '';
            const phone = row.getAttribute('data-phone') || '';
            const email = row.getAttribute('data-email') || '';
            const status = row.getAttribute('data-status') || '';

            const matchesSearch = !searchVal ||
                name.includes(searchVal) ||
                phone.includes(searchVal) ||
                email.includes(searchVal) ||
                role.toLowerCase().includes(searchVal);

            const matchesRole = (roleVal === 'All Roles' || !roleVal) || (role === roleVal);
            const matchesStatus = (statusVal === 'All Status' || !statusVal) || (status === statusVal);

            if (matchesSearch && matchesRole && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const infoText = document.getElementById('tableInfoText');
        if (infoText) {
            infoText.innerText = `Showing 1 - ${visibleCount} of ${visibleCount} employees`;
        }
    }

    // Form Event Submissions with AJAX & Native Fail-Safe
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Add Employee Form
        const addForm = document.getElementById('addEmployeeForm');
        if (addForm) {
            addForm.action = getAppEndpoint();
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalHtml = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Saving...</span>';
                }

                const formData = new FormData(this);
                fetch(getAppEndpoint(), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json().then(data => ({
                        ok: res.ok,
                        status: res.status,
                        data
                    })))
                    .then(result => {
                        if (result.ok && result.data.success) {
                            closeAddEmployeeModal();
                            window.location.reload();
                        } else {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalHtml;
                            }
                            if (result.status === 401 || (result.data && result.data.message === 'Unauthenticated.')) {
                                alert('Session របស់អ្នកបានផុតកំណត់ ឬអ្នកត្រូវបាន Logout។ សូម Login ចូលម្តងទៀត!');
                                window.location.href = '{{ route("login") }}';
                                return;
                            }
                            const msg = result.data.message || (result.data.errors ? Object.values(result.data.errors).flat().join('\n') : 'Failed to save employee.');
                            alert(msg);
                        }
                    })
                    .catch(err => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalHtml;
                        }
                        addForm.submit();
                    });
            });
        }

        // 2. Edit Employee Form
        const editForm = document.getElementById('editEmployeeForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalHtml = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Updating...</span>';
                }

                const formData = new FormData(this);
                const targetAction = this.action || getAppEndpoint();

                fetch(targetAction, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json().then(data => ({
                        ok: res.ok,
                        status: res.status,
                        data
                    })))
                    .then(result => {
                        if (result.ok && result.data.success) {
                            closeEditEmployeeModal();
                            window.location.reload();
                        } else {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalHtml;
                            }
                            if (result.status === 401 || (result.data && result.data.message === 'Unauthenticated.')) {
                                alert('Session របស់អ្នកបានផុតកំណត់ ឬអ្នកត្រូវបាន Logout។ សូម Login ចូលម្តងទៀត!');
                                window.location.href = '{{ route("login") }}';
                                return;
                            }
                            const msg = result.data.message || (result.data.errors ? Object.values(result.data.errors).flat().join('\n') : 'Failed to update employee.');
                            alert(msg);
                        }
                    })
                    .catch(err => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalHtml;
                        }
                        editForm.submit();
                    });
            });
        }

        // 3. Delete Employee Form
        const deleteForm = document.getElementById('deleteEmployeeForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Deleting...</span>';
                }

                const formData = new FormData(this);
                const targetAction = this.action || getAppEndpoint();

                fetch(targetAction, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json().then(data => ({
                        ok: res.ok,
                        status: res.status,
                        data
                    })))
                    .then(result => {
                        if (result.ok && result.data.success) {
                            closeDeleteEmployeeModal();
                            window.location.reload();
                        } else {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = 'Yes, Delete';
                            }
                            if (result.status === 401 || (result.data && result.data.message === 'Unauthenticated.')) {
                                alert('Session របស់អ្នកបានផុតកំណត់ ឬអ្នកត្រូវបាន Logout។ សូម Login ចូលម្តងទៀត!');
                                window.location.href = '{{ route("login") }}';
                                return;
                            }
                            const msg = result.data.message || 'Failed to delete employee.';
                            alert(msg);
                        }
                    })
                    .catch(err => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = 'Yes, Delete';
                        }
                        deleteForm.submit();
                    });
            });
        }
    });
</script>
@endsection