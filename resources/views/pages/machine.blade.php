@extends('layouts.navbar')

@section('title', 'Machines')

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
    @endif

    <!-- ERROR NOTIFICATION -->
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 font-bold text-lg leading-none cursor-pointer">&times;</button>
        </div>
    @endif

    <!-- TOP ACTION BAR: REGISTER MACHINE BUTTON -->
    <div class="flex justify-end items-center">
        <button type="button" 
                onclick="openAddMachineModal()" 
                class="bg-[#2563EB] hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-xs transition flex items-center space-x-2 text-sm cursor-pointer active:scale-95">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Register Machine</span>
        </button>
    </div>

    <!-- 4 SUMMARY METRIC CARDS (EXACT MATCHING SCREENSHOT) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Machines (Soft Blue) -->
        <div class="bg-[#F0F5FF] border border-blue-100/80 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-xs flex items-center space-x-4 sm:space-x-5">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-blue-100/80 text-[#2563EB] flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="2" width="18" height="20" rx="3"></rect>
                    <circle cx="12" cy="13" r="5"></circle>
                    <circle cx="12" cy="13" r="2"></circle>
                    <circle cx="6" cy="6" r="1" fill="currentColor"></circle>
                    <circle cx="9" cy="6" r="1" fill="currentColor"></circle>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Machines</p>
                <h3 class="text-xl sm:text-xl font-bold text-gray-900 mt-0.5 tracking-tight" id="cardTotalMachines">{{ $stats['total'] ?? count($machines) }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">All machines</p>
            </div>
        </div>

        <!-- Card 2: Available (Soft Mint/Green) -->
        <div class="bg-[#F0FDF4] border border-emerald-100/80 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-xs flex items-center space-x-4 sm:space-x-5">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-emerald-100/80 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="4"></rect>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Available</p>
                <h3 class="text-xl sm:text-xl font-bold text-emerald-600 mt-0.5 tracking-tight" id="cardAvailableCount">{{ $stats['available'] ?? 0 }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">Ready to use</p>
            </div>
        </div>

        <!-- Card 3: In Use (Soft Amber/Yellow) -->
        <div class="bg-[#FFFBEB] border border-amber-100/80 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-xs flex items-center space-x-4 sm:space-x-5">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-amber-100/80 text-amber-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">In Use</p>
                <h3 class="text-xl sm:text-xl font-bold text-amber-600 mt-0.5 tracking-tight" id="cardInUseCount">{{ $stats['in_use'] ?? 0 }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">Currently running</p>
            </div>
        </div>

        <!-- Card 4: Under Maintenance (Soft Rose) -->
        <div class="bg-[#FFF1F2] border border-rose-100/80 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-xs flex items-center space-x-4 sm:space-x-5">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-rose-100/80 text-rose-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Under Maintenance</p>
                <h3 class="text-xl sm:text-xl font-bold text-rose-500 mt-0.5 tracking-tight" id="cardMaintenanceCount">{{ $stats['maintenance'] ?? 0 }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">Needs service</p>
            </div>
        </div>
    </div>

    <!-- FILTER TOOLBAR (SEARCH + ALL STATUS + ALL TYPES + SEARCH BTN + RESET BTN) -->
    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-4 sm:p-5 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.03)] flex flex-col md:flex-row items-center justify-between gap-3 sm:gap-4">
        <!-- Search Input -->
        <div class="relative w-full md:flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <input type="text" 
                   id="machineSearchInput" 
                   oninput="filterMachinesTable()" 
                   placeholder="Search by machine name, ID, or model..." 
                   value="{{ $search ?? '' }}"
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
        </div>

        <!-- Filter Dropdowns & Buttons -->
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full md:w-auto">
            <!-- All Status Dropdown -->
            <div class="relative w-full sm:w-36">
                <select id="statusFilterSelect" 
                        onchange="filterMachinesTable()"
                        class="appearance-none w-full pl-4 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#334155] font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer shadow-2xs">
                    <option value="All Status" {{ ($statusFilter ?? '') === 'All Status' ? 'selected' : '' }}>All Status</option>
                    <option value="Available" {{ ($statusFilter ?? '') === 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="In Use" {{ ($statusFilter ?? '') === 'In Use' ? 'selected' : '' }}>In Use</option>
                    <option value="Under Maintenance" {{ ($statusFilter ?? '') === 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-[#64748B]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <!-- All Types Dropdown -->
            <div class="relative w-full sm:w-36">
                <select id="typeFilterSelect" 
                        onchange="filterMachinesTable()"
                        class="appearance-none w-full pl-4 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#334155] font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer shadow-2xs">
                    <option value="All Types" {{ ($typeFilter ?? '') === 'All Types' ? 'selected' : '' }}>All Types</option>
                    <option value="Front Load">Front Load</option>
                    <option value="Top Load">Top Load</option>
                    <option value="Dryer">Dryer</option>
                    <option value="Ironing">Ironing</option>
                    <option value="Steam">Steam</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-[#64748B]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <!-- Search Button -->
            <button type="button" 
                    onclick="filterMachinesTable()" 
                    class="bg-[#2563EB] hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-xs transition flex items-center space-x-1.5 text-sm cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <span>Search</span>
            </button>

            <!-- Reset Button -->
            <button type="button" 
                    onclick="resetFilterToolbar()" 
                    class="border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold px-4 py-2.5 rounded-xl shadow-2xs transition flex items-center space-x-1.5 text-sm cursor-pointer">
                <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Reset</span>
            </button>
        </div>
    </div>

    <!-- MAIN MACHINE LIST CARD CONTAINER -->
    <div class="bg-white border border-[#E2E8F0] rounded-2xl sm:rounded-3xl shadow-[0_4px_25px_-5px_rgba(0,0,0,0.03)] overflow-hidden">
        
        <!-- CARD HEADER: MACHINE LIST + BADGE -->
        <div class="p-6 pb-4 flex items-center space-x-3">
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Machine List</h2>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-[#DBEAFE] text-[#1D4ED8]" id="badgeMachineCount">
                {{ count($machines) }} machines
            </span>
        </div>

        <!-- FULL-WIDTH TABLE SECTION -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] border-y border-[#E2E8F0]/80 text-[11px] sm:text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-4 px-6 w-12 font-bold text-gray-500">#</th>
                        <th class="py-4 px-6 font-bold text-gray-500">MACHINE ID</th>
                        <th class="py-4 px-6 font-bold text-gray-500">MACHINE NAME</th>
                        <th class="py-4 px-6 font-bold text-gray-500">TYPE</th>
                        <th class="py-4 px-6 font-bold text-gray-500">CAPACITY (KG)</th>
                        <th class="py-4 px-6 font-bold text-gray-500">STATUS</th>
                        <th class="py-4 px-6 font-bold text-gray-500">LAST MAINTENANCE</th>
                        <th class="py-4 px-6 font-bold text-gray-500 text-center w-36">ACTION</th>
                    </tr>
                </thead>
                <tbody id="machinesTableBody" class="divide-y divide-[#F1F5F9] text-sm bg-white">
                    @forelse($machines as $index => $item)
                    @php
                        $status = $item->status ?? 'Available';
                        $statusBadgeClass = match($status) {
                            'Available' => 'bg-[#DCFCE7] text-[#16A34A]',
                            'In Use' => 'bg-[#FEF3C7] text-[#D97706]',
                            'Under Maintenance' => 'bg-[#FEE2E2] text-[#DC2626]',
                            default => 'bg-gray-100 text-gray-700'
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition machine-row"
                        id="machine-row-{{ $item->id }}"
                        data-id="{{ $item->id }}"
                        data-machine-id="{{ $item->machine_id }}"
                        data-name="{{ strtolower($item->name ?? '') }}"
                        data-raw-name="{{ $item->name ?? '' }}"
                        data-type="{{ $item->type ?? '' }}"
                        data-capacity="{{ $item->capacity ?? '-' }}"
                        data-model="{{ $item->model ?? '' }}"
                        data-location="{{ $item->location ?? 'Laundry Room 1' }}"
                        data-status="{{ $item->status ?? 'Available' }}"
                        data-last-maint="{{ $item->last_maintenance ?? '2025-09-10' }}"
                        data-next-maint="{{ $item->next_maintenance ?? '2025-10-10' }}"
                        data-notes="{{ $item->notes ?? '' }}"
                        data-history="{{ json_encode($item->maintenance_history ?? []) }}">
                        
                        <!-- # Column -->
                        <td class="py-4 px-6 text-gray-900 font-bold text-sm machine-index-num">
                            {{ $loop->iteration ?? ($index + 1) }}.
                        </td>

                        <!-- Machine ID Column -->
                        <td class="py-4 px-6 font-extrabold text-gray-900 text-sm whitespace-nowrap">
                            {{ $item->machine_id }}
                        </td>

                        <!-- Machine Name Column -->
                        <td class="py-4 px-6 text-gray-600 font-medium text-sm whitespace-nowrap">
                            {{ $item->name }}
                        </td>

                        <!-- Type Column -->
                        <td class="py-4 px-6 text-gray-600 font-medium text-sm whitespace-nowrap">
                            {{ $item->type }}
                        </td>

                        <!-- Capacity Column -->
                        <td class="py-4 px-6 text-gray-600 font-medium text-sm whitespace-nowrap">
                            {{ $item->capacity ?? '-' }}
                        </td>

                        <!-- Status Column -->
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $statusBadgeClass }}">
                                {{ $item->status }}
                            </span>
                        </td>

                        <!-- Last Maintenance Column -->
                        <td class="py-4 px-6 text-gray-600 font-medium text-sm whitespace-nowrap">
                            {{ $item->last_maintenance ?? '-' }}
                        </td>

                        <!-- Actions Column: 3 Colored Buttons (View Eye, Edit Pencil, Delete Trash) -->
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- View Details Button (Cyan) -->
                                <button type="button" 
                                        data-id="{{ $item->id }}"
                                        onclick="handleViewMachineClick(this)"
                                        class="w-8 h-8 rounded-lg bg-[#0EA5E9] hover:bg-sky-600 text-white flex items-center justify-center shadow-2xs transition active:scale-90 cursor-pointer" 
                                        title="View Details">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>

                                <!-- Edit Button (Emerald) -->
                                <button type="button" 
                                        data-id="{{ $item->id }}"
                                        onclick="handleEditMachineClick(this)"
                                        class="w-8 h-8 rounded-lg bg-[#10B981] hover:bg-emerald-600 text-white flex items-center justify-center shadow-2xs transition active:scale-90 cursor-pointer" 
                                        title="Edit Machine">
                                    <i class="fa-solid fa-pencil text-xs"></i>
                                </button>

                                <!-- Delete Button (Rose) -->
                                <button type="button" 
                                        data-id="{{ $item->id }}"
                                        data-machine-id="{{ $item->machine_id }}"
                                        data-name="{{ $item->name }}"
                                        onclick="handleDeleteMachineClick(this)"
                                        class="w-8 h-8 rounded-lg bg-[#EF4444] hover:bg-rose-600 text-white flex items-center justify-center shadow-2xs transition active:scale-90 cursor-pointer" 
                                        title="Delete Machine">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow">
                        <td colspan="8" class="py-12 text-center text-gray-400 font-medium">
                            <i class="fa-solid fa-cubes text-3xl mb-3 text-gray-300 block"></i>
                            No machines found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer & Pagination Matching Screenshot -->
        <div class="p-6 pt-5 border-t border-[#F1F5F9] flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
            <div id="tableInfoText" class="text-xs sm:text-sm font-medium text-gray-500">
                Showing {{ count($machines) > 0 ? '1' : '0' }} - {{ count($machines) }} of {{ $stats['total'] ?? count($machines) }} machines
            </div>

            <div class="flex items-center space-x-1 sm:space-x-1.5">
                <button type="button" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-400 hover:text-gray-700 bg-white flex items-center justify-center transition cursor-pointer shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button type="button" class="w-8 h-8 rounded-lg bg-[#2563EB] text-white font-bold flex items-center justify-center text-xs shadow-2xs">
                    1
                </button>
                <button type="button" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-400 hover:text-gray-700 bg-white flex items-center justify-center transition cursor-pointer shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>

    </div>

</div>

<!-- ======================================================= -->
<!-- 1. VIEW MACHINE DETAILS MODAL (MATCHES SCREENSHOT 2)    -->
<!-- ======================================================= -->
<div id="viewMachineModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl animate-fade-in border border-gray-100 my-8 space-y-4">
        
        <!-- Header: Title & Close -->
        <div class="flex items-start justify-between pb-1">
            <h2 class="text-2xl font-extrabold text-[#0F172A] tracking-tight">Machine Details</h2>
            <button type="button" onclick="closeViewMachineModal()" class="text-gray-400 hover:text-gray-600 text-3xl font-light leading-none -mt-2 cursor-pointer">
                &times;
            </button>
        </div>

        <!-- Top Machine Hero Card -->
        <div class="flex items-center space-x-4 pt-1">
            <div class="w-16 h-16 rounded-2xl bg-[#E0F2FE] text-[#0284C7] flex items-center justify-center flex-shrink-0">
                <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="2" width="18" height="20" rx="3"></rect>
                    <circle cx="12" cy="13" r="5"></circle>
                    <circle cx="12" cy="13" r="2"></circle>
                    <circle cx="6" cy="6" r="1" fill="currentColor"></circle>
                    <circle cx="9" cy="6" r="1" fill="currentColor"></circle>
                </svg>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h3 id="viewHeroMachineId" class="text-2xl font-black text-[#0F172A] leading-tight"></h3>
                    <span id="viewHeroStatusBadge" class="inline-block px-3 py-0.5 rounded-full text-xs font-bold"></span>
                </div>
                <p id="viewHeroSubName" class="text-gray-600 font-medium text-sm mt-0.5"></p>
                <p id="viewHeroCapacity" class="text-gray-500 font-medium text-sm"></p>
            </div>
        </div>

        <!-- Box 1: Machine Information -->
        <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-4 sm:p-5 space-y-3">
            <div class="flex items-center space-x-2 text-gray-900 font-bold text-sm">
                <i class="fa-solid fa-users-gear text-blue-600 text-sm"></i>
                <span>Machine Information</span>
            </div>

            <div class="grid grid-cols-2 gap-y-2.5 text-xs sm:text-sm">
                <div class="flex items-center space-x-2 text-gray-500">
                    <i class="fa-regular fa-bell text-gray-400 w-4"></i>
                    <span>Machine ID</span>
                </div>
                <div id="viewInfoMachineId" class="font-bold text-gray-900"></div>

                <div class="flex items-center space-x-2 text-gray-500">
                    <i class="fa-solid fa-tag text-gray-400 w-4"></i>
                    <span>Machine Name</span>
                </div>
                <div id="viewInfoName" class="font-bold text-gray-900"></div>

                <div class="flex items-center space-x-2 text-gray-500">
                    <i class="fa-regular fa-circle-check text-gray-400 w-4"></i>
                    <span>Type</span>
                </div>
                <div id="viewInfoType" class="font-bold text-gray-900"></div>

                <div class="flex items-center space-x-2 text-gray-500">
                    <i class="fa-regular fa-circle text-gray-400 w-4"></i>
                    <span>Capacity</span>
                </div>
                <div id="viewInfoCapacity" class="font-bold text-gray-900"></div>

                <div class="flex items-center space-x-2 text-gray-500">
                    <i class="fa-solid fa-microchip text-gray-400 w-4"></i>
                    <span>Model</span>
                </div>
                <div id="viewInfoModel" class="font-bold text-gray-900"></div>

                <div class="flex items-center space-x-2 text-gray-500">
                    <i class="fa-solid fa-location-dot text-gray-400 w-4"></i>
                    <span>Location</span>
                </div>
                <div id="viewInfoLocation" class="font-bold text-gray-900"></div>
            </div>
        </div>

        <!-- Box 2: Status -->
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-4 sm:p-5 space-y-2">
            <div class="flex items-center space-x-2 text-gray-900 font-bold text-sm">
                <i class="fa-regular fa-clock text-blue-600"></i>
                <span>Status</span>
            </div>
            <div>
                <span id="viewStatusBadgeLarge" class="inline-block px-4 py-1.5 rounded-full text-xs font-bold"></span>
            </div>
            <div class="flex items-center space-x-2 text-xs sm:text-sm font-medium text-gray-600 pt-0.5">
                <span id="viewStatusDot" class="w-2.5 h-2.5 rounded-full inline-block"></span>
                <span id="viewStatusDescription"></span>
            </div>
        </div>

        <!-- Box 3: Last Maintenance -->
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-4 sm:p-5 space-y-2">
            <div class="flex items-center space-x-2 text-gray-900 font-bold text-sm">
                <i class="fa-regular fa-calendar-check text-blue-600"></i>
                <span>Last Maintenance</span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-xs sm:text-sm pt-1">
                <div>
                    <span class="text-gray-400 text-xs block font-medium">Date</span>
                    <span id="viewMaintLastDate" class="text-sm sm:text-base font-extrabold text-gray-900 mt-0.5 block"></span>
                </div>
                <div>
                    <span class="text-gray-400 text-xs block font-medium">Next Maintenance</span>
                    <span id="viewMaintNextDate" class="text-sm sm:text-base font-extrabold text-gray-900 mt-0.5 block"></span>
                </div>
            </div>
        </div>

        <!-- Box 4: Maintenance History -->
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-4 sm:p-5 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 text-gray-900 font-bold text-sm">
                    <i class="fa-solid fa-cube text-blue-600"></i>
                    <span>Maintenance History</span>
                </div>
                <button type="button" onclick="openAddLogForm()" class="text-[#2563EB] hover:text-blue-800 text-xs font-bold transition cursor-pointer">
                    + Add Log
                </button>
            </div>

            <!-- Inline Log Form (Hidden by default) -->
            <form id="addMaintLogForm" class="hidden bg-blue-50/60 p-3 rounded-xl border border-blue-100 space-y-2 text-xs">
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" id="newLogDate" value="{{ date('Y-m-d') }}" class="px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs">
                    <select id="newLogType" class="px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs">
                        <option value="Routine Check">Routine Check</option>
                        <option value="Cleaning">Cleaning</option>
                        <option value="Repair">Repair</option>
                        <option value="Inspection">Inspection</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="text" id="newLogNote" placeholder="Maintenance note..." class="flex-1 px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs">
                    <button type="button" onclick="submitQuickLog()" class="bg-[#2563EB] text-white px-3 py-1.5 rounded-lg font-bold">Save</button>
                </div>
            </form>

            <!-- History Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-gray-400 font-bold border-b border-gray-100 pb-1.5">
                            <th class="pb-2 font-bold">Date</th>
                            <th class="pb-2 font-bold">Type</th>
                            <th class="pb-2 font-bold">Note</th>
                        </tr>
                    </thead>
                    <tbody id="viewMaintenanceHistoryBody" class="divide-y divide-gray-50 text-gray-700">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Action Buttons: Edit Machine & Delete Machine -->
        <div class="flex items-center justify-between gap-3 pt-2">
            <!-- Edit Button (White with Blue Border) -->
            <button type="button" 
                    id="modalEditBtn"
                    class="w-1/2 py-3 px-4 rounded-2xl border-2 border-[#2563EB] text-[#2563EB] hover:bg-blue-50 font-bold text-sm transition flex items-center justify-center space-x-2 cursor-pointer shadow-2xs">
                <i class="fa-solid fa-pencil text-xs"></i>
                <span>Edit Machine</span>
            </button>

            <!-- Delete Button (Solid Red) -->
            <button type="button" 
                    id="modalDeleteBtn"
                    class="w-1/2 py-3 px-4 rounded-2xl bg-[#EF4444] hover:bg-rose-600 text-white font-bold text-sm transition flex items-center justify-center space-x-2 cursor-pointer shadow-2xs">
                <i class="fa-solid fa-trash-can text-xs"></i>
                <span>Delete Machine</span>
            </button>
        </div>

    </div>
</div>

<!-- ======================================================= -->
<!-- 2. REGISTER NEW MACHINE MODAL                           -->
<!-- ======================================================= -->
<div id="addMachineModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl animate-fade-in border border-gray-100 my-8">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between pb-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-[#2563EB] flex items-center justify-center text-xl flex-shrink-0">
                    <svg class="w-6 h-6 text-[#2563EB]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="2" width="18" height="20" rx="3"></rect>
                        <circle cx="12" cy="13" r="5"></circle>
                        <circle cx="12" cy="13" r="2"></circle>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-[#0F172A] tracking-tight leading-tight">Register Washing Machine</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Add a new machine unit to shop inventory.</p>
                </div>
            </div>
            <button type="button" onclick="closeAddMachineModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-light leading-none -mt-1 cursor-pointer">
                &times;
            </button>
        </div>

        <!-- Add Form -->
        <form id="addMachineForm" method="POST" action="{{ route('machines.store') }}" class="space-y-3.5 mt-4">
            @csrf

            <!-- Machine ID & Name Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Machine ID</label>
                    <input type="text" 
                           name="machine_id" 
                           placeholder="Auto (e.g. WM-013)" 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Machine Name <span class="text-rose-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           required 
                           placeholder="e.g. Washer 6, Dryer 4" 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>

            <!-- Type & Capacity Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Type <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <select name="type" required class="appearance-none w-full px-4 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition cursor-pointer">
                            <option value="Front Load">Front Load</option>
                            <option value="Top Load">Top Load</option>
                            <option value="Dryer">Dryer</option>
                            <option value="Ironing">Ironing</option>
                            <option value="Steam">Steam</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                            <i class="fa-solid fa-chevron-down text-[11px]"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Capacity (Kg)</label>
                    <input type="text" 
                           name="capacity" 
                           placeholder="e.g. 15 or -" 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>

            <!-- Model & Location Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Model</label>
                    <input type="text" 
                           name="model" 
                           placeholder="e.g. LG WD-10, SpeedQueen" 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Location</label>
                    <input type="text" 
                           name="location" 
                           placeholder="e.g. Laundry Room 1" 
                           value="Laundry Room 1"
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">Machine Status <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <select name="status" required class="appearance-none w-full px-4 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition cursor-pointer">
                        <option value="Available" selected>Available (Ready to use)</option>
                        <option value="In Use">In Use (Currently running)</option>
                        <option value="Under Maintenance">Under Maintenance (Needs service)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                        <i class="fa-solid fa-chevron-down text-[11px]"></i>
                    </div>
                </div>
            </div>

            <!-- Maintenance Schedule Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Last Maintenance</label>
                    <input type="date" 
                           name="last_maintenance" 
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Next Maintenance</label>
                    <input type="date" 
                           name="next_maintenance" 
                           value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">Notes</label>
                <textarea name="notes" rows="2" placeholder="Operational details, specifications..." class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition resize-none"></textarea>
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 gap-3">
                <button type="button" 
                        onclick="closeAddMachineModal()" 
                        class="w-1/2 py-2.5 px-4 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-sm font-semibold transition cursor-pointer text-center">
                    Cancel
                </button>
                <button type="submit" 
                        class="w-1/2 py-2.5 px-4 rounded-xl bg-[#2563EB] hover:bg-blue-700 text-white text-sm font-semibold shadow-xs transition flex items-center justify-center space-x-2 cursor-pointer active:scale-95">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Register Machine</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- 3. EDIT MACHINE MODAL                                   -->
<!-- ======================================================= -->
<div id="editMachineModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl animate-fade-in border border-gray-100 my-8">
        
        <!-- Header -->
        <div class="flex items-start justify-between pb-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-[#0F172A] tracking-tight leading-tight">Update Machine Information</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Modify machine details, change status, or reschedule maintenance.</p>
                </div>
            </div>
            <button type="button" onclick="closeEditMachineModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-light leading-none -mt-1 cursor-pointer">
                &times;
            </button>
        </div>

        <!-- Edit Form -->
        <form id="editMachineForm" method="POST" action="" class="space-y-3.5 mt-4">
            @csrf

            <!-- Machine ID & Name Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Machine ID</label>
                    <input type="text" 
                           id="editMachineId" 
                           name="machine_id" 
                           readonly 
                           class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Machine Name <span class="text-rose-500">*</span></label>
                    <input type="text" 
                           id="editMachineName" 
                           name="name" 
                           required 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>

            <!-- Type & Capacity Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Type <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <select id="editMachineType" name="type" required class="appearance-none w-full px-4 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition cursor-pointer">
                            <option value="Front Load">Front Load</option>
                            <option value="Top Load">Top Load</option>
                            <option value="Dryer">Dryer</option>
                            <option value="Ironing">Ironing</option>
                            <option value="Steam">Steam</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                            <i class="fa-solid fa-chevron-down text-[11px]"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Capacity (Kg)</label>
                    <input type="text" 
                           id="editMachineCapacity" 
                           name="capacity" 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>

            <!-- Model & Location Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Model</label>
                    <input type="text" 
                           id="editMachineModel" 
                           name="model" 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Location</label>
                    <input type="text" 
                           id="editMachineLocation" 
                           name="location" 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>

            <!-- Status Dropdown (Available / In Use / Under Maintenance) -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">Machine Status <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <select id="editMachineStatus" name="status" required class="appearance-none w-full px-4 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition cursor-pointer">
                        <option value="Available">Available (Ready to use)</option>
                        <option value="In Use">In Use (Currently running)</option>
                        <option value="Under Maintenance">Under Maintenance (Needs service)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                        <i class="fa-solid fa-chevron-down text-[11px]"></i>
                    </div>
                </div>
            </div>

            <!-- Maintenance Schedule Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Last Maintenance</label>
                    <input type="date" 
                           id="editLastMaintenance" 
                           name="last_maintenance" 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">Next Maintenance</label>
                    <input type="date" 
                           id="editNextMaintenance" 
                           name="next_maintenance" 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">Notes</label>
                <textarea id="editMachineNotes" name="notes" rows="2" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition resize-none"></textarea>
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 gap-3">
                <button type="button" 
                        onclick="closeEditMachineModal()" 
                        class="w-1/2 py-2.5 px-4 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-sm font-semibold transition cursor-pointer text-center">
                    Cancel
                </button>
                <button type="submit" 
                        class="w-1/2 py-2.5 px-4 rounded-xl bg-[#10B981] hover:bg-emerald-600 text-white text-sm font-semibold shadow-xs transition flex items-center justify-center space-x-2 cursor-pointer active:scale-95">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- 4. DELETE MACHINE CONFIRMATION MODAL                    -->
<!-- ======================================================= -->
<div id="deleteMachineModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl text-center border border-gray-100 animate-fade-in">
        <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Delete Machine Unit</h3>
        <p class="text-xs text-gray-500 mb-6">Are you sure you want to remove <span id="deleteMachineNameSpan" class="font-bold text-gray-800"></span>? This will permanently delete its record and maintenance history.</p>
        
        <form id="deleteMachineForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-center space-x-3">
                <button type="button" onclick="closeDeleteMachineModal()" class="w-1/2 py-2.5 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 text-sm font-semibold transition cursor-pointer">
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
    let currentViewingMachine = null;

    /**
     * Resolves the exact machine endpoint relative to current browser URL.
     * Works seamlessly on localhost, XAMPP subdirectories, and artisan serve.
     */
    function getAppEndpoint(subpath = '') {
        const cleanPath = window.location.pathname.replace(/\/+$/, '');
        let base = cleanPath;
        const match = cleanPath.match(/^(.*?\/(?:machine|machines))(?:\/.*)?$/);
        if (match) {
            base = match[1];
        }
        if (subpath) {
            return (base + '/' + subpath).replace(/\/+/g, '/');
        }
        return base;
    }

    // Extract machine data safely from table row
    function getMachineFromElement(el) {
        const row = el.closest('.machine-row');
        if (!row) return null;
        let history = [];
        try {
            history = JSON.parse(row.getAttribute('data-history') || '[]');
        } catch(e) {
            history = [];
        }
        return {
            id: row.getAttribute('data-id') || '',
            machine_id: row.getAttribute('data-machine-id') || '',
            name: row.getAttribute('data-raw-name') || '',
            type: row.getAttribute('data-type') || 'Front Load',
            capacity: row.getAttribute('data-capacity') || '-',
            model: row.getAttribute('data-model') || '',
            location: row.getAttribute('data-location') || 'Laundry Room 1',
            status: row.getAttribute('data-status') || 'Available',
            last_maintenance: row.getAttribute('data-last-maint') || '2025-09-10',
            next_maintenance: row.getAttribute('data-next-maint') || '2025-10-10',
            notes: row.getAttribute('data-notes') || '',
            maintenance_history: history
        };
    }

    // 1. View Machine Details Modal
    function handleViewMachineClick(btn) {
        const machine = getMachineFromElement(btn);
        if (!machine) return;
        openViewMachineModal(machine);
    }

    function openViewMachineModal(machine) {
        currentViewingMachine = machine;

        // Hero info
        document.getElementById('viewHeroMachineId').innerText = machine.machine_id;
        document.getElementById('viewHeroSubName').innerText = `${machine.name} (${machine.type})`;
        document.getElementById('viewHeroCapacity').innerText = `Capacity: ${machine.capacity} Kg`;

        // Hero badge
        const heroBadge = document.getElementById('viewHeroStatusBadge');
        heroBadge.innerText = machine.status;
        heroBadge.className = 'inline-block px-3 py-0.5 rounded-full text-xs font-bold ' + getStatusClasses(machine.status);

        // Information Grid
        document.getElementById('viewInfoMachineId').innerText = machine.machine_id;
        document.getElementById('viewInfoName').innerText = machine.name;
        document.getElementById('viewInfoType').innerText = machine.type;
        document.getElementById('viewInfoCapacity').innerText = `${machine.capacity} Kg`;
        document.getElementById('viewInfoModel').innerText = machine.model || 'Standard Model';
        document.getElementById('viewInfoLocation').innerText = machine.location || 'Laundry Room 1';

        // Status Card
        const statusBadgeLarge = document.getElementById('viewStatusBadgeLarge');
        statusBadgeLarge.innerText = machine.status;
        statusBadgeLarge.className = 'inline-block px-4 py-1.5 rounded-full text-xs font-bold ' + getStatusClasses(machine.status);

        const statusDot = document.getElementById('viewStatusDot');
        const statusDesc = document.getElementById('viewStatusDescription');
        if (machine.status === 'In Use') {
            statusDot.className = 'w-2.5 h-2.5 rounded-full inline-block bg-amber-500';
            statusDesc.innerText = 'Currently running';
        } else if (machine.status === 'Available') {
            statusDot.className = 'w-2.5 h-2.5 rounded-full inline-block bg-emerald-500';
            statusDesc.innerText = 'Ready to use';
        } else {
            statusDot.className = 'w-2.5 h-2.5 rounded-full inline-block bg-rose-500';
            statusDesc.innerText = 'Needs service';
        }

        // Maintenance Schedule Card
        document.getElementById('viewMaintLastDate').innerText = machine.last_maintenance || '-';
        document.getElementById('viewMaintNextDate').innerText = machine.next_maintenance || '-';

        // Maintenance History Table
        renderHistoryRows(machine.maintenance_history || []);

        // Hook Edit & Delete Buttons inside view modal
        document.getElementById('modalEditBtn').onclick = function() {
            closeViewMachineModal();
            openEditMachineModal(machine);
        };
        document.getElementById('modalDeleteBtn').onclick = function() {
            closeViewMachineModal();
            confirmDeleteMachine(machine.id, `${machine.machine_id} - ${machine.name}`);
        };

        // Hide inline log form if open
        const form = document.getElementById('addMaintLogForm');
        if (form) form.classList.add('hidden');

        document.getElementById('viewMachineModal').classList.remove('hidden');
    }

    function closeViewMachineModal() {
        document.getElementById('viewMachineModal').classList.add('hidden');
    }

    function getStatusClasses(status) {
        switch(status) {
            case 'Available':
                return 'bg-[#DCFCE7] text-[#16A34A]';
            case 'In Use':
                return 'bg-[#FEF3C7] text-[#D97706]';
            case 'Under Maintenance':
                return 'bg-[#FEE2E2] text-[#DC2626]';
            default:
                return 'bg-gray-100 text-gray-700';
        }
    }

    function renderHistoryRows(history) {
        const tbody = document.getElementById('viewMaintenanceHistoryBody');
        tbody.innerHTML = '';

        if (!history || history.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="py-3 text-center text-gray-400">No maintenance records logged yet.</td></tr>';
            return;
        }

        history.forEach(item => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50 transition';
            tr.innerHTML = `
                <td class="py-2.5 font-medium text-gray-600 whitespace-nowrap">${item.date || '-'}</td>
                <td class="py-2.5 font-bold text-[#2563EB] whitespace-nowrap">${item.type || 'Routine Check'}</td>
                <td class="py-2.5 text-gray-700">${item.note || 'Normal'}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function openAddLogForm() {
        const form = document.getElementById('addMaintLogForm');
        if (form) {
            form.classList.toggle('hidden');
        }
    }

    function submitQuickLog() {
        if (!currentViewingMachine) return;
        const date = document.getElementById('newLogDate').value;
        const type = document.getElementById('newLogType').value;
        const note = document.getElementById('newLogNote').value;

        if (!note.trim()) {
            alert('Please enter a note for the maintenance log.');
            return;
        }

        const endpoint = getAppEndpoint(currentViewingMachine.id);
        const token = document.querySelector('input[name="_token"]')?.value || '';

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                log_date: date,
                log_type: type,
                log_note: note
            })
        })
        .then(res => res.json())
        .then(data => {
            window.location.reload();
        })
        .catch(err => {
            window.location.reload();
        });
    }

    // 2. Add Machine Modal
    function openAddMachineModal() {
        const form = document.getElementById('addMachineForm');
        if (form) {
            form.action = '{{ route('machines.store') }}';
        }
        document.getElementById('addMachineModal').classList.remove('hidden');
    }
    function closeAddMachineModal() {
        document.getElementById('addMachineModal').classList.add('hidden');
    }

    // 3. Edit Machine Modal
    function handleEditMachineClick(btn) {
        const machine = getMachineFromElement(btn);
        if (!machine) return;
        openEditMachineModal(machine);
    }

    function openEditMachineModal(machine) {
        document.getElementById('editMachineId').value = machine.machine_id;
        document.getElementById('editMachineName').value = machine.name;
        document.getElementById('editMachineType').value = machine.type;
        document.getElementById('editMachineCapacity').value = machine.capacity;
        document.getElementById('editMachineModel').value = machine.model || '';
        document.getElementById('editMachineLocation').value = machine.location || 'Laundry Room 1';
        document.getElementById('editMachineStatus').value = machine.status;
        document.getElementById('editLastMaintenance').value = machine.last_maintenance;
        document.getElementById('editNextMaintenance').value = machine.next_maintenance;
        document.getElementById('editMachineNotes').value = machine.notes || '';

        const editForm = document.getElementById('editMachineForm');
        if (editForm) {
            editForm.action = getAppEndpoint(machine.id);
        }
        document.getElementById('editMachineModal').classList.remove('hidden');
    }
    function closeEditMachineModal() {
        document.getElementById('editMachineModal').classList.add('hidden');
    }

    // 4. Delete Machine Modal
    function handleDeleteMachineClick(btn) {
        const id = btn.getAttribute('data-id');
        const mid = btn.getAttribute('data-machine-id');
        const name = btn.getAttribute('data-name');
        confirmDeleteMachine(id, `${mid} - ${name}`);
    }

    function confirmDeleteMachine(id, name) {
        document.getElementById('deleteMachineNameSpan').innerText = name;
        const deleteForm = document.getElementById('deleteMachineForm');
        if (deleteForm) {
            deleteForm.action = getAppEndpoint(id);
        }
        document.getElementById('deleteMachineModal').classList.remove('hidden');
    }
    function closeDeleteMachineModal() {
        document.getElementById('deleteMachineModal').classList.add('hidden');
    }

    // 5. Instant Filtering & Search
    function filterMachinesTable() {
        const searchVal = (document.getElementById('machineSearchInput').value || '').trim().toLowerCase();
        const statusVal = document.getElementById('statusFilterSelect').value;
        const typeVal = document.getElementById('typeFilterSelect').value;

        const rows = document.querySelectorAll('.machine-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const mid = (row.getAttribute('data-machine-id') || '').toLowerCase();
            const model = (row.getAttribute('data-model') || '').toLowerCase();
            const type = (row.getAttribute('data-type') || '').toLowerCase();
            const status = row.getAttribute('data-status') || '';

            const matchesSearch = !searchVal || 
                name.includes(searchVal) || 
                mid.includes(searchVal) || 
                model.includes(searchVal) || 
                type.includes(searchVal);

            const matchesStatus = (statusVal === 'All Status' || !statusVal) || (status === statusVal);
            const matchesType = (typeVal === 'All Types' || !typeVal) || (type === typeVal.toLowerCase());

            if (matchesSearch && matchesStatus && matchesType) {
                row.style.display = '';
                visibleCount++;
                const indexCell = row.querySelector('.machine-index-num');
                if (indexCell) {
                    indexCell.innerText = `${visibleCount}.`;
                }
            } else {
                row.style.display = 'none';
            }
        });

        // Toggle Empty state
        const noResults = document.getElementById('noResultsRow');
        if (noResults) {
            noResults.style.display = (visibleCount === 0) ? '' : 'none';
        }

        // Update Info text & badge
        const infoText = document.getElementById('tableInfoText');
        if (infoText) {
            infoText.innerText = visibleCount > 0 
                ? `Showing 1 - ${visibleCount} of ${visibleCount} machines`
                : `Showing 0 machines`;
        }
        const badge = document.getElementById('badgeMachineCount');
        if (badge) {
            badge.innerText = `${visibleCount} machines`;
        }
    }

    function resetFilterToolbar() {
        document.getElementById('machineSearchInput').value = '';
        document.getElementById('statusFilterSelect').value = 'All Status';
        document.getElementById('typeFilterSelect').value = 'All Types';
        filterMachinesTable();
    }

    // 6. Form submissions with AJAX & Native Fail-Safe
    document.addEventListener('DOMContentLoaded', function() {
        // Add Form
        const addForm = document.getElementById('addMachineForm');
        if (addForm) {
            addForm.action = '{{ route('machines.store') }}';
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                const origHtml = btn ? btn.innerHTML : '';
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Registering...</span>';
                }
                const formData = new FormData(this);
                fetch(this.action || '{{ route('machines.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    return res.json().then(data => {
                        if (!res.ok || !data.success) {
                            throw new Error(data.message || 'Server error occurred (' + res.status + ')');
                        }
                        return data;
                    });
                })
                .then(data => {
                    closeAddMachineModal();
                    window.location.reload();
                })
                .catch(err => {
                    console.error('Registration failed:', err);
                    alert('Register Machine failed: ' + err.message);
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = origHtml || '<i class="fa-solid fa-check text-xs"></i><span>Register Machine</span>';
                    }
                });
            });
        }

        // Edit Form
        const editForm = document.getElementById('editMachineForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Saving...</span>';
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
                .then(res => res.json())
                .then(data => {
                    closeEditMachineModal();
                    window.location.reload();
                })
                .catch(err => {
                    editForm.submit();
                });
            });
        }

        // Delete Form
        const deleteForm = document.getElementById('deleteMachineForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Deleting...</span>';
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
                .then(res => res.json())
                .then(data => {
                    closeDeleteMachineModal();
                    window.location.reload();
                })
                .catch(err => {
                    deleteForm.submit();
                });
            });
        }
    });
</script>
@endsection