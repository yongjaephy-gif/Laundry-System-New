@extends('layouts.navbar')

@section('title', 'Pickup & Delivery')

@section('content')
<div class="space-y-6 w-full mx-auto pb-12 pt-4">

    <!-- Top Action Row: Create Pickup / Delivery Button -->
    <div class="flex items-center justify-end">
        <button type="button" 
                onclick="openCreateModal()"
                class="bg-[#2563EB] hover:bg-blue-700 active:scale-[0.98] text-white px-5 py-2.5 rounded-xl font-semibold text-sm flex items-center space-x-2 shadow-sm transition cursor-pointer">
            <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Create Pickup /Delivery</span>
        </button>
    </div>

    <!-- 1. Statistics Cards Row (4 KPI Cards Matching Screenshot) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Pickups -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-[#E0F2FE] text-[#0284C7] flex items-center justify-center shrink-0">
                <!-- Delivery Van Icon -->
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 7h-3V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h1a3 3 0 0 0 6 0h4a3 3 0 0 0 6 0h1a2 2 0 0 0 2-2v-5l-5-4zM7 19a1 1 0 1 1 1-1 1 1 0 0 1-1 1zm11 0a1 1 0 1 1 1-1 1 1 0 0 1-1 1zm1-8h-3V8.5l3.5 2.5z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Total Pickups</p>
                <div class="flex items-baseline space-x-2 mt-0.5">
                    <h3 class="text-xl font-bold text-gray-900 leading-tight">{{ $stats['total_pickups'] ?? 12 }}</h3>
                    <span class="text-xs font-semibold text-[#16A34A] flex items-center">
                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        2 today
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Deliveries -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-[#DCFCE7] text-[#16A34A] flex items-center justify-center shrink-0">
                <!-- Green Delivery Van Icon -->
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 7h-3V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h1a3 3 0 0 0 6 0h4a3 3 0 0 0 6 0h1a2 2 0 0 0 2-2v-5l-5-4zM7 19a1 1 0 1 1 1-1 1 1 0 0 1-1 1zm11 0a1 1 0 1 1 1-1 1 1 0 0 1-1 1zm1-8h-3V8.5l3.5 2.5z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Total Deliveries</p>
                <div class="flex items-baseline space-x-2 mt-0.5">
                    <h3 class="text-xl font-bold text-gray-900 leading-tight">{{ $stats['total_deliveries'] ?? 15 }}</h3>
                    <span class="text-xs font-semibold text-[#16A34A] flex items-center">
                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        3 today
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 3: In Progress -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-[#FEF3C7] text-[#D97706] flex items-center justify-center shrink-0">
                <!-- Clock Icon -->
                <svg class="w-6 h-6 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <circle cx="12" cy="12" r="9"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">In Progress</p>
                <div class="flex items-baseline space-x-2 mt-0.5">
                    <h3 class="text-xl font-bold text-gray-900 leading-tight">{{ $stats['in_progress'] ?? 5 }}</h3>
                    <span class="text-xs font-semibold text-[#16A34A] flex items-center">
                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        1 today
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 4: Completed -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-[#F3E8FF] text-[#9333EA] flex items-center justify-center shrink-0">
                <!-- Checkmark in circle -->
                <svg class="w-6 h-6 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Completed</p>
                <div class="flex items-baseline space-x-2 mt-0.5">
                    <h3 class="text-xl font-bold text-gray-900 leading-tight">{{ $stats['completed'] ?? 20 }}</h3>
                    <span class="text-xs font-semibold text-[#16A34A] flex items-center">
                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        4 today
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Tabs Row Matching Screenshot Requirements -->
    <div class="flex items-center space-x-3 overflow-x-auto pb-1">
        <!-- Tab 1: Scheduled Pickups -->
        <a href="{{ route('pickup-and-delivery.index', array_merge(request()->query(), ['tab' => 'pickups', 'page' => 1])) }}" 
           class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold flex items-center space-x-2 transition cursor-pointer whitespace-nowrap {{ ($activeTab ?? 'pickups') === 'pickups' ? 'bg-[#2563EB] text-white shadow-sm' : 'bg-white text-gray-600 hover:text-gray-900 border border-gray-200' }}">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                <path d="M19 7h-3V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h1a3 3 0 0 0 6 0h4a3 3 0 0 0 6 0h1a2 2 0 0 0 2-2v-5l-5-4zM7 19a1 1 0 1 1 1-1 1 1 0 0 1-1 1zm11 0a1 1 0 1 1 1-1 1 1 0 0 1-1 1zm1-8h-3V8.5l3.5 2.5z"/>
            </svg>
            <span>Scheduled Pickups</span>
        </a>

        <!-- Tab 2: Scheduled Deliveries -->
        <a href="{{ route('pickup-and-delivery.index', array_merge(request()->query(), ['tab' => 'deliveries', 'page' => 1])) }}" 
           class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold flex items-center space-x-2 transition cursor-pointer whitespace-nowrap {{ ($activeTab ?? '') === 'deliveries' ? 'bg-[#2563EB] text-white shadow-sm' : 'bg-white text-gray-600 hover:text-gray-900 border border-gray-200' }}">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                <path d="M19 7h-3V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h1a3 3 0 0 0 6 0h4a3 3 0 0 0 6 0h1a2 2 0 0 0 2-2v-5l-5-4zM7 19a1 1 0 1 1 1-1 1 1 0 0 1-1 1zm11 0a1 1 0 1 1 1-1 1 1 0 0 1-1 1zm1-8h-3V8.5l3.5 2.5z"/>
            </svg>
            <span>Scheduled Deliveries</span>
        </a>

        <!-- Tab 3: In Progress (Track Status) -->
        <a href="{{ route('pickup-and-delivery.index', array_merge(request()->query(), ['tab' => 'in_progress', 'page' => 1])) }}" 
           class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold flex items-center space-x-2 transition cursor-pointer whitespace-nowrap {{ ($activeTab ?? '') === 'in_progress' ? 'bg-[#2563EB] text-white shadow-sm' : 'bg-white text-gray-600 hover:text-gray-900 border border-gray-200' }}">
            <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>
            <span>In Progress</span>
        </a>

        <!-- Tab 4: History (Delivery History) -->
        <a href="{{ route('pickup-and-delivery.index', array_merge(request()->query(), ['tab' => 'history', 'page' => 1])) }}" 
           class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold flex items-center space-x-2 transition cursor-pointer whitespace-nowrap {{ ($activeTab ?? '') === 'history' ? 'bg-[#2563EB] text-white shadow-sm' : 'bg-white text-gray-600 hover:text-gray-900 border border-gray-200' }}">
            <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <circle cx="12" cy="12" r="9"></circle>
                <polyline points="12 7 12 12 15 15"></polyline>
            </svg>
            <span>History</span>
        </a>
    </div>

    <!-- 3. Search and Filter Bar Row Matching Screenshot -->
    <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
        <form method="GET" action="{{ route('pickup-and-delivery.index') }}" class="flex flex-col md:flex-row items-center gap-3">
            <input type="hidden" name="tab" value="{{ $activeTab ?? 'pickups' }}">

            <!-- Search input -->
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="text" 
                       name="search" 
                       value="{{ request('search', $search ?? '') }}"
                       placeholder="Search by order number, customer name, or phone..." 
                       class="w-full text-xs sm:text-sm bg-gray-50/70 border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
            </div>

            <!-- Status Dropdown Filter -->
            <div class="w-full sm:w-auto min-w-38">
                <select name="status" 
                        onchange="this.form.submit()" 
                        class="w-full text-xs sm:text-sm bg-gray-50/70 border border-gray-200 rounded-xl px-3.5 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer">
                    <option value="">All Status</option>
                    <option value="Scheduled" {{ request('status') === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="Assigned" {{ request('status') === 'Assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="In Transit" {{ request('status') === 'In Transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Delivered" {{ request('status') === 'Delivered' ? 'selected' : '' }}>Delivered</option>
                </select>
            </div>

            <!-- Date Dropdown Filter -->
            <div class="w-full sm:w-auto min-w-33">
                <select name="date" 
                        onchange="this.form.submit()" 
                        class="w-full text-xs sm:text-sm bg-gray-50/70 border border-gray-200 rounded-xl px-3.5 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer">
                    <option value="">Date</option>
                    <option value="Today" {{ request('date') === 'Today' ? 'selected' : '' }}>Today</option>
                    <option value="This Week" {{ request('date') === 'This Week' ? 'selected' : '' }}>This Week</option>
                    <option value="This Month" {{ request('date') === 'This Month' ? 'selected' : '' }}>This Month</option>
                </select>
            </div>

            <!-- Action Buttons: Search & Reset -->
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <button type="submit" 
                        class="bg-[#2563EB] hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm flex items-center space-x-2 shadow-xs transition cursor-pointer flex-1 sm:flex-none justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>Search</span>
                </button>

                <a href="{{ route('pickup-and-delivery.index', ['tab' => $activeTab ?? 'pickups']) }}" 
                   class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-4 py-2.5 rounded-xl font-semibold text-xs sm:text-sm flex items-center space-x-2 transition cursor-pointer flex-1 sm:flex-none justify-center">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- 4. Main Tasks Table Card Matching Screenshot -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-semibold text-gray-700 border-b border-gray-100 bg-gray-50/50">
                        <th class="py-3.5 px-4 w-12 text-center">#</th>
                        <th class="py-3.5 px-4 font-semibold text-gray-700">Order No.</th>
                        <th class="py-3.5 px-4 font-semibold text-gray-700">Customer</th>
                        <th class="py-3.5 px-4 font-semibold text-gray-700">Service Type</th>
                        <th class="py-3.5 px-4 font-semibold text-gray-700">Pickup Date & Time</th>
                        <th class="py-3.5 px-4 font-semibold text-gray-700">Delivery Date & Time</th>
                        <th class="py-3.5 px-4 font-semibold text-gray-700 text-center">Status</th>
                        <th class="py-3.5 px-4 font-semibold text-gray-700 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                    @forelse($tasks as $index => $task)
                    @php
                        $t = is_array($task) ? (object)$task : $task;
                        $rowIndex = ($tasks->firstItem() ?? 1) + $index;
                        $pickupDT = trim(($t->pickup_date instanceof \DateTimeInterface ? $t->pickup_date->format('Y-m-d') : ($t->pickup_date ?? '')) . ' ' . ($t->pickup_time ?? ''));
                        $deliveryDT = trim(($t->delivery_date instanceof \DateTimeInterface ? $t->delivery_date->format('Y-m-d') : ($t->delivery_date ?? '')) . ' ' . ($t->delivery_time ?? ''));
                        $statusText = $t->status ?? 'Scheduled';
                        $statusBadgeClass = match(strtolower(trim($statusText))) {
                            'scheduled' => 'bg-[#E0F2FE] text-[#0284C7]',
                            'assigned' => 'bg-[#FEF3C7] text-[#D97706]',
                            'in transit', 'picked up' => 'bg-[#F3E8FF] text-[#9333EA]',
                            'pending' => 'bg-[#FEE2E2] text-[#DC2626]',
                            'delivered', 'completed' => 'bg-[#DCFCE7] text-[#16A34A]',
                            default => 'bg-gray-100 text-gray-700',
                        };
                    @endphp
                    <tr class="hover:bg-blue-50/30 transition items-row">
                        <!-- # Row index -->
                        <td class="py-4 px-4 text-center font-bold text-gray-800">{{ $rowIndex }}</td>

                        <!-- Order No -->
                        <td class="py-4 px-4 font-medium text-gray-700 whitespace-nowrap">{{ $t->order_number }}</td>

                        <!-- Customer -->
                        <td class="py-4 px-4 font-medium text-gray-800 whitespace-nowrap">{{ $t->customer_name }}</td>

                        <!-- Service Type -->
                        <td class="py-4 px-4 text-gray-600 whitespace-nowrap">{{ $t->service_type }}</td>

                        <!-- Pickup Date & Time -->
                        <td class="py-4 px-4 text-gray-600 whitespace-nowrap">{{ $pickupDT }}</td>

                        <!-- Delivery Date & Time -->
                        <td class="py-4 px-4 text-gray-600 whitespace-nowrap">{{ $deliveryDT }}</td>

                        <!-- Status Badge -->
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <span class="inline-block {{ $statusBadgeClass }} text-[11px] font-semibold px-3 py-1 rounded-full">
                                {{ $statusText }}
                            </span>
                        </td>

                        <!-- Actions (View, Edit, Delete Matching Screenshot) -->
                        <td class="py-4 px-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-1.5">
                                <!-- View Details Action (Blue Eye Button) -->
                                <button type="button" 
                                        data-task-id="{{ $t->id }}"
                                        onclick="openTaskFromButton(this)"
                                        class="w-7 h-7 rounded-lg bg-[#2563EB] hover:bg-blue-700 text-white flex items-center justify-center transition cursor-pointer shadow-2xs"
                                        title="View Details">
                                    <svg class="w-3.5 h-3.5 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>

                                <!-- Edit Details Action (Blue Pencil Button) -->
                                <button type="button" 
                                        data-task-id="{{ $t->id }}"
                                        onclick="editTaskFromButton(this)"
                                        class="w-7 h-7 rounded-lg bg-[#2563EB] hover:bg-blue-700 text-white flex items-center justify-center transition cursor-pointer shadow-2xs"
                                        title="Edit Task">
                                    <svg class="w-3.5 h-3.5 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>

                                <!-- Delete Action (Red Trash Button) -->
                                <button type="button" 
                                        data-task-id="{{ $t->id }}"
                                        data-order-number="{{ $t->order_number }}"
                                        onclick="confirmDeleteFromButton(this)"
                                        class="w-7 h-7 rounded-lg bg-[#EF4444] hover:bg-red-600 text-white flex items-center justify-center transition cursor-pointer shadow-2xs"
                                        title="Delete Task">
                                    <svg class="w-3.5 h-3.5 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-gray-500">
                            <p class="font-medium text-base text-gray-700">No pickup & delivery records found.</p>
                            <p class="text-xs text-gray-400 mt-1">Try adjusting your filters or click "+ Create Pickup /Delivery" to schedule one.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 5. Pagination Bar Matching Screenshot -->
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm text-gray-500">
            <div>
                Showing {{ $tasks->firstItem() ?? 1 }} - {{ $tasks->lastItem() ?? $tasks->count() }} of {{ $tasks->total() ?? $tasks->count() }} pickups
            </div>

            <!-- Numbered Pagination -->
            @if($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator && $tasks->lastPage() > 1)
            <div class="flex items-center space-x-1">
                <!-- Previous Button -->
                @if($tasks->onFirstPage())
                    <span class="w-8 h-8 rounded-lg border border-gray-200 text-gray-300 flex items-center justify-center cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </span>
                @else
                    <a href="{{ $tasks->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 flex items-center justify-center transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                @endif

                <!-- Page Links -->
                @foreach(range(1, $tasks->lastPage()) as $p)
                    @if($p == $tasks->currentPage())
                        <span class="w-8 h-8 rounded-lg bg-[#2563EB] text-white font-bold flex items-center justify-center shadow-xs">{{ $p }}</span>
                    @else
                        <a href="{{ $tasks->url($p) }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 flex items-center justify-center transition">{{ $p }}</a>
                    @endif
                @endforeach

                <!-- Next Button -->
                @if($tasks->hasMorePages())
                    <a href="{{ $tasks->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 flex items-center justify-center transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                @else
                    <span class="w-8 h-8 rounded-lg border border-gray-200 text-gray-300 flex items-center justify-center cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                @endif
            </div>
            @endif
        </div>
    </div>

</div>

<!-- ================= MODAL: PICKUP & DELIVERY DETAILS ================= -->
<div id="drawerOverlay" onclick="if(event.target === this) closeTaskDrawer()" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div id="taskDrawer" class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 transform transition-all relative my-auto max-h-[92vh] flex flex-col">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 shrink-0">
            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Pickup & Delivery Details</h3>
            <button type="button" onclick="closeTaskDrawer()" class="text-gray-400 hover:text-gray-600 text-lg transition cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Body Form -->
        <form id="drawerDetailsForm" onsubmit="submitDrawerUpdate(event)" class="mt-3.5 space-y-3 overflow-y-auto pr-1 flex-1 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
            @csrf
            @method('PUT')
            <input type="hidden" id="drawerTaskId" name="id" value="">

            <!-- 1. Order Information Box (Compact 4-column summary) -->
            <div class="bg-blue-50/50 rounded-2xl p-2.5 border border-blue-100/70 text-xs">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <div>
                        <span class="text-gray-500 block text-[10px] uppercase font-semibold">Order No.</span>
                        <strong id="drawerOrderNo" class="font-bold text-gray-900 block mt-0.5">ORD-0015</strong>
                    </div>
                    <div>
                        <span class="text-gray-500 block text-[10px] uppercase font-semibold">Customer</span>
                        <strong id="drawerCustomerName" class="font-bold text-gray-900 block mt-0.5 truncate">Neang Pheaktra</strong>
                    </div>
                    <div>
                        <span class="text-gray-500 block text-[10px] uppercase font-semibold">Service Type</span>
                        <strong id="drawerServiceType" class="font-bold text-gray-900 block mt-0.5">Wash & Fold</strong>
                    </div>
                    <div>
                        <span class="text-gray-500 block text-[10px] uppercase font-semibold">Total Amount</span>
                        <strong id="drawerTotalAmount" class="font-bold text-blue-600 block mt-0.5">$14.00</strong>
                    </div>
                </div>
            </div>

            <!-- 2. Pickup & 3. Delivery Schedule (Side by Side 2 Columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <!-- Pickup Box -->
                <div class="p-2.5 bg-blue-50/40 rounded-2xl border border-blue-100/70 space-y-1.5">
                    <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wide block">1. Pickup Details</span>
                    <div class="grid grid-cols-2 gap-1.5">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Date</label>
                            <input type="date" id="drawerPickupDate" name="pickup_date" value="2025-09-17" class="w-full px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Time</label>
                            <input type="text" id="drawerPickupTime" name="pickup_time" value="08:00 AM" placeholder="08:00 AM" class="w-full px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Pickup Address</label>
                        <input type="text" id="drawerPickupAddress" name="pickup_address" value="#12, Street 63, Siem Reap" placeholder="Enter address" class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Delivery Box -->
                <div class="p-2.5 bg-emerald-50/40 rounded-2xl border border-emerald-100/70 space-y-1.5">
                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wide block">2. Delivery Details</span>
                    <div class="grid grid-cols-2 gap-1.5">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Date</label>
                            <input type="date" id="drawerDeliveryDate" name="delivery_date" value="2025-09-18" class="w-full px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Time</label>
                            <input type="text" id="drawerDeliveryTime" name="delivery_time" value="12:00 PM" placeholder="12:00 PM" class="w-full px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Delivery Address</label>
                        <input type="text" id="drawerDeliveryAddress" name="delivery_address" value="#45, Pokambor Ave, Siem Reap" placeholder="Enter address" class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- 4. Delivery Staff -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Assign Delivery Staff</label>
                <select id="drawerDeliveryStaff" 
                        name="delivery_staff_id" 
                        onchange="syncStaffName(this)"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    <option value="">Unassigned</option>
                    @foreach($staffList as $staff)
                    <option value="{{ $staff['id'] }}" data-name="{{ $staff['name'] }}" {{ $staff['name'] === 'Sok Chanthy' ? 'selected' : '' }}>
                        {{ $staff['name'] }} ({{ $staff['role'] }})
                    </option>
                    @endforeach
                </select>
                <input type="hidden" id="drawerDeliveryStaffName" name="delivery_staff_name" value="Sok Chanthy">
            </div>

            <!-- 5. Status Stepper -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Status Tracking</label>
                <input type="hidden" id="drawerStatusInput" name="status" value="Scheduled">
                
                <!-- Horizontal Progress Stepper -->
                <div class="relative flex items-center justify-between px-3 pt-1 pb-1">
                    <div class="absolute top-3.5 left-6 right-6 h-0.5 bg-gray-200 z-0">
                        <div id="stepperProgressLine" class="h-full bg-[#2563EB] transition-all duration-300" style="width: 0%;"></div>
                    </div>

                    <!-- Step 1: Scheduled -->
                    <div class="relative z-10 flex flex-col items-center cursor-pointer group" onclick="setStepperStatus('Scheduled')">
                        <div id="stepDot-Scheduled" class="w-3.5 h-3.5 rounded-full border-2 border-[#2563EB] bg-[#2563EB] ring-4 ring-blue-100 transition-all"></div>
                        <span id="stepLabel-Scheduled" class="text-[10px] font-bold text-[#2563EB] mt-1.5">Scheduled</span>
                    </div>

                    <!-- Step 2: Picked Up -->
                    <div class="relative z-10 flex flex-col items-center cursor-pointer group" onclick="setStepperStatus('Picked Up')">
                        <div id="stepDot-PickedUp" class="w-3.5 h-3.5 rounded-full border-2 border-gray-300 bg-white group-hover:border-blue-400 transition-all"></div>
                        <span id="stepLabel-PickedUp" class="text-[10px] font-medium text-gray-500 mt-1.5">Picked Up</span>
                    </div>

                    <!-- Step 3: In Transit -->
                    <div class="relative z-10 flex flex-col items-center cursor-pointer group" onclick="setStepperStatus('In Transit')">
                        <div id="stepDot-InTransit" class="w-3.5 h-3.5 rounded-full border-2 border-gray-300 bg-white group-hover:border-blue-400 transition-all"></div>
                        <span id="stepLabel-InTransit" class="text-[10px] font-medium text-gray-500 mt-1.5">In Transit</span>
                    </div>

                    <!-- Step 4: Delivered -->
                    <div class="relative z-10 flex flex-col items-center cursor-pointer group" onclick="setStepperStatus('Delivered')">
                        <div id="stepDot-Delivered" class="w-3.5 h-3.5 rounded-full border-2 border-gray-300 bg-white group-hover:border-blue-400 transition-all"></div>
                        <span id="stepLabel-Delivered" class="text-[10px] font-medium text-gray-500 mt-1.5">Delivered</span>
                    </div>
                </div>
            </div>

            <!-- 6. Notes (Optional) -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-semibold text-gray-700">Notes <span class="text-gray-400 font-normal text-[11px]">(Optional)</span></label>
                    <span id="drawerNotesCount" class="text-[10px] text-gray-400 font-mono">0/500</span>
                </div>
                <textarea id="drawerNotes" 
                          name="notes" 
                          rows="1.5" 
                          maxlength="500"
                          oninput="updateCharCount(this)"
                          placeholder="Add notes..." 
                          class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition resize-none"></textarea>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end space-x-2 shrink-0">
                <button type="button" 
                        onclick="closeTaskDrawer()" 
                        class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 bg-[#2563EB] hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center space-x-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>Update Task</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= CREATE PICKUP / DELIVERY MODAL ================= -->
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-4 sm:p-4 shadow-2xl animate-fade-in border border-gray-100 my-auto max-h-250 flex flex-col">
        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Schedule Pickup & Delivery</h3>
                <p class="text-xs text-gray-500 mt-0.5">Enter details to schedule laundry pickup and delivery logistics.</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="w-8 h-8 text-gray-400 hover:text-gray-700 rounded-lg flex items-center justify-center hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="createTaskForm" onsubmit="submitCreateTask(event)" class="mt-1 space-y-2">
            @csrf

            <!-- Customer & Service -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Customer Name *</label>
                    <input type="text" name="customer_name" required placeholder="e.g. Sokha Dara" class="w-full text-xs sm:text-sm rounded-xl border border-gray-200 px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Phone Number</label>
                    <input type="text" name="customer_phone" placeholder="e.g. 012 345 678" class="w-full text-xs sm:text-sm rounded-xl border border-gray-200 px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Service Type *</label>
                    <select name="service_type" required class="w-full text-xs sm:text-sm rounded-xl border border-gray-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                        <option value="Wash & Dry">Wash & Dry</option>
                        <option value="Wash & Fold">Wash & Fold</option>
                        <option value="Wash Only">Wash Only</option>
                        <option value="Dry Cleaning">Dry Cleaning</option>
                        <option value="Ironing">Ironing</option>
                        <option value="Blanket Cleaning">Blanket Cleaning</option>
                        <option value="Curtain Cleaning">Curtain Cleaning</option>
                        <option value="Shoe Cleaning">Shoe Cleaning</option>
                        <option value="Carpet Cleaning">Carpet Cleaning</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Total Amount ($)</label>
                    <input type="number" step="0.01" name="total_amount" value="15.00" class="w-full text-xs sm:text-sm rounded-xl border border-gray-200 px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>

            <!-- Schedule Pickup Fields (Requirement 1) -->
            <div class="p-3.5 bg-blue-50/40 rounded-xl border border-blue-100">
                <span class="text-xs font-bold text-blue-700 uppercase tracking-wider block mb-2">1. Schedule Pickup</span>
                <div class="grid grid-cols-2 gap-3 mb-2">
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-1">Pickup Date</label>
                        <input type="date" name="pickup_date" value="{{ date('Y-m-d') }}" class="w-full text-xs rounded-lg border border-gray-200 px-2.5 py-1.5 bg-white">
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-1">Pickup Time</label>
                        <input type="text" name="pickup_time" value="10:00 AM" placeholder="10:00 AM" class="w-full text-xs rounded-lg border border-gray-200 px-2.5 py-1.5 bg-white">
                    </div>
                </div>
                <input type="text" name="pickup_address" placeholder="Pickup Address (e.g. #12, Street 63, Siem Reap)" class="w-full text-xs rounded-lg border border-gray-200 px-3 py-1.5 bg-white">
            </div>

            <!-- Schedule Delivery Fields (Requirement 2) -->
            <div class="p-3.5 bg-emerald-50/40 rounded-xl border border-emerald-100">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block mb-2">2. Schedule Delivery</span>
                <div class="grid grid-cols-2 gap-3 mb-2">
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-1">Delivery Date</label>
                        <input type="date" name="delivery_date" value="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full text-xs rounded-lg border border-gray-200 px-2.5 py-1.5 bg-white">
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-1">Delivery Time</label>
                        <input type="text" name="delivery_time" value="02:00 PM" placeholder="02:00 PM" class="w-full text-xs rounded-lg border border-gray-200 px-2.5 py-1.5 bg-white">
                    </div>
                </div>
                <input type="text" name="delivery_address" placeholder="Delivery Address (e.g. #45, Pokambor Ave, Siem Reap)" class="w-full text-xs rounded-lg border border-gray-200 px-3 py-1.5 bg-white">
            </div>

            <!-- Assign Staff (Requirement 3) & Initial Status (Requirement 4) -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Assign Delivery Staff</label>
                    <select name="delivery_staff_id" class="w-full text-xs sm:text-sm rounded-xl border border-gray-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                        <option value="">Leave Unassigned</option>
                        @foreach($staffList as $staff)
                        <option value="{{ $staff['id'] }}">{{ $staff['name'] }} ({{ $staff['role'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Initial Status</label>
                    <select name="status" class="w-full text-xs sm:text-sm rounded-xl border border-gray-200 px-3 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                        <option value="Scheduled">Scheduled</option>
                        <option value="Assigned">Assigned</option>
                        <option value="In Transit">In Transit</option>
                        <option value="Pending">Pending</option>
                        <option value="Delivered">Delivered</option>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Notes</label>
                <textarea name="notes" rows="2" placeholder="Special delivery instructions..." class="w-full text-xs sm:text-sm rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-xs sm:text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="bg-[#2563EB] hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition shadow-xs">
                    Schedule Task
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= DELETE CONFIRMATION MODAL ================= -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl relative animate-in fade-in zoom-in duration-200 text-center">
        <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Delete Pickup/Delivery?</h3>
        <p class="text-xs text-gray-500 mb-5">Are you sure you want to delete order <strong id="deleteOrderNumber"></strong>? This action cannot be undone.</p>

        <form id="deleteTaskForm" onsubmit="submitDeleteTask(event)">
            @csrf
            @method('DELETE')
            <input type="hidden" id="deleteTaskId" name="task_id">
            <div class="flex items-center justify-center space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl text-xs font-semibold shadow-xs transition">
                    Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Alert -->
<div id="toastAlert" class="hidden fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-4 py-3 rounded-xl shadow-xl flex items-center space-x-2 text-xs sm:text-sm transition-all duration-300 transform translate-y-2 opacity-0">
    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span id="toastMessage" class="font-medium"></span>
</div>

<!-- Raw Task Data for JS Interaction -->
<script type="application/json" id="tasks-json">
    {!! json_encode($tasks->items()) !!}
</script>

@push('scripts')
<script>
    // Task data registry loaded safely from JSON container
    let taskRegistry = [];
    try {
        const tasksJsonEl = document.getElementById('tasks-json');
        if (tasksJsonEl && tasksJsonEl.textContent) {
            taskRegistry = JSON.parse(tasksJsonEl.textContent);
        }
    } catch (err) {
        taskRegistry = [];
    }

    function openTaskFromButton(btn) {
        if (!btn) return;
        const id = btn.getAttribute('data-task-id');
        const task = taskRegistry.find(item => String(item.id) === String(id));
        if (task) {
            openTaskDrawer(task);
        }
    }

    function editTaskFromButton(btn) {
        openTaskFromButton(btn);
    }

    function confirmDeleteFromButton(btn) {
        if (!btn) return;
        const id = btn.getAttribute('data-task-id');
        const orderNo = btn.getAttribute('data-order-number') || '';
        confirmDeleteTask(id, orderNo);
    }
    // Header dropdown toggle
    function toggleRoleMenu() {
        const menu = document.getElementById('role-menu');
        const chevron = document.getElementById('role-chevron');
        if (menu) {
            menu.classList.toggle('hidden');
            if (chevron) {
                chevron.classList.toggle('rotate-180');
            }
        }
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('role-switcher-container');
        const menu = document.getElementById('role-menu');
        if (container && menu && !container.contains(e.target)) {
            menu.classList.add('hidden');
            const chevron = document.getElementById('role-chevron');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    });

    // Toast Alert
    function showToast(message) {
        const toast = document.getElementById('toastAlert');
        const text = document.getElementById('toastMessage');
        text.innerText = message;
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.remove('opacity-0', 'translate-y-2');
            toast.classList.add('opacity-100', 'translate-y-0');
        }, 10);
        setTimeout(() => {
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }, 3200);
    }

    // Drawer Management
    function openTaskDrawer(task) {
        if (!task) return;
        
        document.getElementById('drawerTaskId').value = task.id || '';
        document.getElementById('drawerOrderNo').innerText = task.order_number || '';
        document.getElementById('drawerCustomerName').innerText = task.customer_name || '';
        document.getElementById('drawerServiceType').innerText = task.service_type || '';
        
        const amt = parseFloat(task.total_amount || 0).toFixed(2);
        document.getElementById('drawerTotalAmount').innerText = '$' + amt;

        // Pickup
        let pDate = task.pickup_date ? (task.pickup_date.substring(0, 10)) : '2025-09-17';
        document.getElementById('drawerPickupDate').value = pDate;
        document.getElementById('drawerPickupTime').value = task.pickup_time || '08:00 AM';
        document.getElementById('drawerPickupAddress').value = task.pickup_address || '#12, Street 63, Siem Reap';

        // Delivery
        let dDate = task.delivery_date ? (task.delivery_date.substring(0, 10)) : '2025-09-18';
        document.getElementById('drawerDeliveryDate').value = dDate;
        document.getElementById('drawerDeliveryTime').value = task.delivery_time || '12:00 PM';
        document.getElementById('drawerDeliveryAddress').value = task.delivery_address || '#45, Pokambor Ave, Siem Reap';

        // Staff
        const staffSelect = document.getElementById('drawerDeliveryStaff');
        let staffMatched = false;
        if (staffSelect) {
            for (let i = 0; i < staffSelect.options.length; i++) {
                if (staffSelect.options[i].value == task.delivery_staff_id || staffSelect.options[i].getAttribute('data-name') === task.delivery_staff_name) {
                    staffSelect.selectedIndex = i;
                    staffMatched = true;
                    break;
                }
            }
            if (!staffMatched) {
                staffSelect.selectedIndex = 0;
            }
        }
        document.getElementById('drawerDeliveryStaffName').value = task.delivery_staff_name || '';

        // Status Stepper
        setStepperStatus(task.status || 'Scheduled');

        // Notes
        const notesEl = document.getElementById('drawerNotes');
        notesEl.value = task.notes || '';
        updateCharCount(notesEl);

        // Open Modal
        document.getElementById('drawerOverlay').classList.remove('hidden');
    }

    function editTask(task) {
        openTaskDrawer(task);
    }

    function closeTaskDrawer() {
        document.getElementById('drawerOverlay').classList.add('hidden');
    }

    // Status Stepper Controller (Requirement 4: Track Status)
    function setStepperStatus(statusName) {
        document.getElementById('drawerStatusInput').value = statusName;

        const stages = ['Scheduled', 'Picked Up', 'In Transit', 'Delivered'];
        let activeIdx = stages.indexOf(statusName);
        if (activeIdx === -1) {
            // Map Assigned or Pending to Scheduled
            activeIdx = 0;
        }

        // Update Line Progress
        const percent = (activeIdx / (stages.length - 1)) * 100;
        document.getElementById('stepperProgressLine').style.width = percent + '%';

        // Update Dots & Labels
        stages.forEach((stage, idx) => {
            const key = stage.replace(/\s+/g, '');
            const dot = document.getElementById('stepDot-' + key);
            const label = document.getElementById('stepLabel-' + key);

            if (!dot || !label) return;

            if (idx <= activeIdx) {
                dot.className = 'w-4 h-4 rounded-full border-2 border-[#2563EB] bg-[#2563EB] ring-4 ring-blue-100 transition-all';
                label.className = 'text-[11px] font-bold text-[#2563EB] mt-2';
            } else {
                dot.className = 'w-4 h-4 rounded-full border-2 border-gray-300 bg-white group-hover:border-blue-400 transition-all';
                label.className = 'text-[11px] font-medium text-gray-500 mt-2';
            }
        });
    }

    function syncStaffName(select) {
        const selectedOption = select.options[select.selectedIndex];
        const staffName = selectedOption ? selectedOption.getAttribute('data-name') : '';
        document.getElementById('drawerDeliveryStaffName').value = staffName || '';
    }

    function updateCharCount(textarea) {
        const len = textarea.value.length;
        document.getElementById('drawerNotesCount').innerText = len + '/500';
    }

    // Submit Modal Update (Handles Schedule Pickup, Schedule Delivery, Assign Staff, Track Status)
    async function submitDrawerUpdate(e) {
        e.preventDefault();
        const taskId = document.getElementById('drawerTaskId').value;
        if (!taskId) return;

        const form = document.getElementById('drawerDetailsForm');
        const formData = new FormData(form);
        if (!formData.has('_method')) {
            formData.append('_method', 'PUT');
        }

        try {
            const res = await fetch(`/pickup-and-delivery/${taskId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                closeTaskDrawer();
                showToast(data.message || 'Updated successfully!');
                setTimeout(() => window.location.reload(), 600);
            } else {
                alert(data.message || 'Failed to update.');
            }
        } catch (err) {
            console.error(err);
            // Fallback reload
            window.location.reload();
        }
    }

    // Create Modal
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    async function submitCreateTask(e) {
        e.preventDefault();
        const form = document.getElementById('createTaskForm');
        const formData = new FormData(form);

        try {
            const res = await fetch('{{ route("pickup-and-delivery.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                closeCreateModal();
                showToast(data.message || 'Task scheduled successfully!');
                setTimeout(() => window.location.reload(), 600);
            } else {
                alert(data.message || 'Error creating task.');
            }
        } catch (err) {
            console.error(err);
            window.location.reload();
        }
    }

    // Delete Modal
    function confirmDeleteTask(id, orderNo) {
        document.getElementById('deleteTaskId').value = id;
        document.getElementById('deleteOrderNumber').innerText = orderNo;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    async function submitDeleteTask(e) {
        e.preventDefault();
        const id = document.getElementById('deleteTaskId').value;
        if (!id) return;

        try {
            const res = await fetch(`/pickup-and-delivery/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            if (data.success) {
                closeDeleteModal();
                showToast(data.message || 'Deleted successfully!');
                setTimeout(() => window.location.reload(), 600);
            }
        } catch (err) {
            console.error(err);
            window.location.reload();
        }
    }
</script>
@endpush
@endsection