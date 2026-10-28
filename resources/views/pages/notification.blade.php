@extends('layouts.navbar')

@section('title', 'Notifications')

@section('content')
<div class="space-y-6 w-full mx-auto">

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs animate-fade-in">
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

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-xs animate-fade-in">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-lg leading-none cursor-pointer">&times;</button>
        </div>
    @endif

    <!-- Top Action Bar: Create Notification Button (Matching media_1791125092242.png) -->
    <div class="flex items-center justify-end">
        <button type="button" onclick="openCreateNotificationModal()" class="bg-[#2563EB] hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-xl shadow-xs text-sm flex items-center space-x-2 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Create Notification</span>
        </button>
    </div>

    <!-- 5 STATS CARDS (Matching media_1791125092242.png exactly) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Order Ready -->
        <div class="bg-[#F0F5FF] border border-blue-100/70 rounded-2xl p-5 flex flex-col justify-between shadow-[0_2px_10px_rgba(37,99,235,0.03)] hover:shadow-md transition">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-[#2563EB] text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-800">Order Ready</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl sm:text-xl font-bold text-gray-900 leading-none" id="stat-order-ready">{{ $stats['order_ready'] ?? 0 }}</h3>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-1 border-t border-blue-100/40">
                <span class="text-gray-400 font-medium">Today's notifications</span>
                <span class="font-semibold text-emerald-600">&uarr; 20%</span>
            </div>
        </div>

        <!-- Card 2: Pickup Reminder -->
        <div class="bg-[#F0FDF4] border border-emerald-100/70 rounded-2xl p-5 flex flex-col justify-between shadow-[0_2px_10px_rgba(16,185,129,0.03)] hover:shadow-md transition">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-800">Pickup Reminder</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl sm:text-xl font-bold text-gray-900 leading-none" id="stat-pickup">{{ $stats['pickup_reminder'] ?? 0 }}</h3>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-1 border-t border-emerald-100/40">
                <span class="text-gray-400 font-medium">Today's notifications</span>
                <span class="font-semibold text-emerald-600">&uarr; 14%</span>
            </div>
        </div>

        <!-- Card 3: Delivery Reminder -->
        <div class="bg-[#FFFBEB] border border-amber-100/70 rounded-2xl p-5 flex flex-col justify-between shadow-[0_2px_10px_rgba(245,158,11,0.03)] hover:shadow-md transition">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75" />
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-800">Delivery Reminder</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl sm:text-xl font-bold text-gray-900 leading-none" id="stat-delivery">{{ $stats['delivery_reminder'] ?? 0 }}</h3>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-1 border-t border-amber-100/40">
                <span class="text-gray-400 font-medium">Today's notifications</span>
                <span class="font-semibold text-emerald-600">&uarr; 50%</span>
            </div>
        </div>

        <!-- Card 4: Payment Reminder -->
        <div class="bg-[#FAF5FF] border border-purple-100/70 rounded-2xl p-5 flex flex-col justify-between shadow-[0_2px_10px_rgba(168,85,247,0.03)] hover:shadow-md transition">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-800">Payment Reminder</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl sm:text-xl font-bold text-gray-900 leading-none" id="stat-payment">{{ $stats['payment_reminder'] ?? 0 }}</h3>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-1 border-t border-purple-100/40">
                <span class="text-gray-400 font-medium">Today's notifications</span>
                <span class="font-semibold text-emerald-600">&uarr; 33%</span>
            </div>
        </div>

        <!-- Card 5: Promotion Notification -->
        <div class="bg-[#FFF1F2] border border-rose-100/70 rounded-2xl p-5 flex flex-col justify-between shadow-[0_2px_10px_rgba(244,63,94,0.03)] hover:shadow-md transition">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.111-1.53-.435a18.064 18.064 0 01-1.425-4.239m3.09-9.18c.253-.962.584-1.892.985-2.783.247-.55.06-1.21-.463-1.511l-.657-.38c-.551-.318-1.26-.111-1.53.435a18.064 18.064 0 00-1.425 4.239m3.09 9.18a44.603 44.603 0 010-9.18m0 9.18c2.81 0 5.49-.408 8.01-1.168.653-.197 1.15-.724 1.233-1.398a18.52 18.52 0 000-4.068c-.083-.674-.58-1.201-1.233-1.398A28.435 28.435 0 0010.34 6.66" />
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-800">Promotion Notification</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl sm:text-xl font-bold text-gray-900 leading-none" id="stat-promotion">{{ $stats['promotion'] ?? 0 }}</h3>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-1 border-t border-rose-100/40">
                <span class="text-gray-400 font-medium">Today's notifications</span>
                <span class="font-semibold text-gray-500">&uarr; 0%</span>
            </div>
        </div>
    </div>

    <!-- SEARCH & FILTER TOOLBAR (Matching media_1791125092242.png) -->
    <form id="filterForm" method="GET" action="{{ route('notifications.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
        <!-- Search Input with Magnifier -->
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input 
                type="text" 
                name="search" 
                id="searchInput"
                value="{{ $search ?? '' }}"
                placeholder="Search by title, type, customer name, or message..." 
                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-gray-800"
            >
        </div>

        <!-- Filter: All Types -->
        <div class="relative w-full lg:w-48">
            <select 
                name="type" 
                id="typeFilter" 
                class="w-full appearance-none bg-white border border-gray-200 rounded-xl px-4 py-2.5 pr-9 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer"
            >
                <option value="All Types" {{ ($typeFilter ?? '') === 'All Types' ? 'selected' : '' }}>All Types</option>
                <option value="Order Ready" {{ ($typeFilter ?? '') === 'Order Ready' ? 'selected' : '' }}>Order Ready</option>
                <option value="Pickup Reminder" {{ ($typeFilter ?? '') === 'Pickup Reminder' ? 'selected' : '' }}>Pickup Reminder</option>
                <option value="Delivery Reminder" {{ ($typeFilter ?? '') === 'Delivery Reminder' ? 'selected' : '' }}>Delivery Reminder</option>
                <option value="Payment Reminder" {{ ($typeFilter ?? '') === 'Payment Reminder' ? 'selected' : '' }}>Payment Reminder</option>
                <option value="Promotion" {{ in_array(($typeFilter ?? ''), ['Promotion', 'Promotion Notification']) ? 'selected' : '' }}>Promotion</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-400">
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </div>
        </div>

        <!-- Filter: All Status -->
        <div class="relative w-full lg:w-40">
            <select 
                name="status" 
                id="statusFilter" 
                class="w-full appearance-none bg-white border border-gray-200 rounded-xl px-4 py-2.5 pr-9 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer"
            >
                <option value="All Status" {{ ($statusFilter ?? '') === 'All Status' ? 'selected' : '' }}>All Status</option>
                <option value="Sent" {{ ($statusFilter ?? '') === 'Sent' ? 'selected' : '' }}>Sent</option>
                <option value="Pending" {{ ($statusFilter ?? '') === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Scheduled" {{ ($statusFilter ?? '') === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="Failed" {{ ($statusFilter ?? '') === 'Failed' ? 'selected' : '' }}>Failed</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-400">
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </div>
        </div>

        <!-- Buttons: Search & Reset -->
        <div class="flex items-center space-x-2">
            <button type="submit" class="bg-[#2563EB] hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-xl text-sm flex items-center justify-center space-x-1.5 transition cursor-pointer shadow-xs flex-1 lg:flex-initial">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Search</span>
            </button>

            <button type="button" onclick="resetFilters()" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-medium px-4 py-2.5 rounded-xl text-sm flex items-center justify-center space-x-1.5 transition cursor-pointer flex-1 lg:flex-initial">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Reset</span>
            </button>
        </div>
    </form>

    <!-- NOTIFICATION LIST CARD (Matching media_1791125092242.png) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.03)] overflow-hidden">
        <!-- Card Header -->
        <div class="p-5 sm:p-6 border-b border-gray-100 flex items-center space-x-3">
            <h2 class="text-base sm:text-lg font-bold text-gray-900">Notification List</h2>
            <span id="notificationsCountBadge" class="bg-blue-50 text-blue-600 rounded-full px-2.5 py-0.5 text-xs font-semibold">
                {{ $filteredCount ?? count($notifications) }} notifications
            </span>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="notificationsTable">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-gray-100 text-[11px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center w-12">#</th>
                        <th class="py-3.5 px-4 text-left">TYPE</th>
                        <th class="py-3.5 px-4 text-left">TITLE</th>
                        <th class="py-3.5 px-4 text-left">MESSAGE</th>
                        <th class="py-3.5 px-4 text-left">TARGET</th>
                        <th class="py-3.5 px-4 text-left">SCHEDULED DATE</th>
                        <th class="py-3.5 px-4 text-center">STATUS</th>
                        <th class="py-3.5 px-4 text-center w-28">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm" id="notificationsTableBody">
                    @forelse($notifications as $index => $notif)
                        @php
                            $rowNum = ($page - 1) * $perPage + $index + 1;

                            // Icon and color badge per type
                            $typeIconClass = 'bg-blue-100 text-blue-600';
                            $typeSvg = 'file';
                            if ($notif->type === 'Pickup Reminder') {
                                $typeIconClass = 'bg-emerald-100 text-emerald-600';
                                $typeSvg = 'bag';
                            } elseif ($notif->type === 'Delivery Reminder') {
                                $typeIconClass = 'bg-amber-100 text-amber-600';
                                $typeSvg = 'truck';
                            } elseif ($notif->type === 'Payment Reminder') {
                                $typeIconClass = 'bg-purple-100 text-purple-600';
                                $typeSvg = 'card';
                            } elseif ($notif->type === 'Promotion' || $notif->type === 'Promotion Notification') {
                                $typeIconClass = 'bg-rose-100 text-rose-600';
                                $typeSvg = 'bullhorn';
                            }

                            // Status pill class
                            $statusClass = 'bg-emerald-50 text-emerald-600';
                            if ($notif->status === 'Pending') {
                                $statusClass = 'bg-amber-50 text-amber-600';
                            } elseif ($notif->status === 'Scheduled') {
                                $statusClass = 'bg-blue-50 text-blue-600';
                            } elseif ($notif->status === 'Failed') {
                                $statusClass = 'bg-rose-50 text-rose-600';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors notif-row" data-id="{{ $notif->id }}">
                            <!-- Index -->
                            <td class="py-4 px-4 text-center text-gray-500 font-medium">
                                {{ $rowNum }}
                            </td>

                            <!-- Type with Icon -->
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded-md {{ $typeIconClass }} flex items-center justify-center shrink-0 text-xs">
                                        @if($typeSvg === 'file')
                                            <i class="fa-regular fa-file-lines text-[11px]"></i>
                                        @elseif($typeSvg === 'bag')
                                            <i class="fa-solid fa-bag-shopping text-[11px]"></i>
                                        @elseif($typeSvg === 'truck')
                                            <i class="fa-solid fa-truck-fast text-[11px]"></i>
                                        @elseif($typeSvg === 'card')
                                            <i class="fa-solid fa-credit-card text-[11px]"></i>
                                        @else
                                            <i class="fa-solid fa-bullhorn text-[11px]"></i>
                                        @endif
                                    </div>
                                    <span class="font-medium text-gray-800 text-xs sm:text-sm whitespace-nowrap">{{ $notif->type }}</span>
                                </div>
                            </td>

                            <!-- Title -->
                            <td class="py-4 px-4 font-semibold text-gray-900 text-xs sm:text-sm whitespace-nowrap">
                                {{ $notif->title }}
                            </td>

                            <!-- Message -->
                            <td class="py-4 px-4 text-xs sm:text-sm text-gray-600 max-w-xs truncate" title="{{ $notif->message }}">
                                {{ $notif->message }}
                            </td>

                            <!-- Target -->
                            <td class="py-4 px-4 font-semibold text-gray-900 text-xs sm:text-sm whitespace-nowrap">
                                {{ $notif->target }}
                            </td>

                            <!-- Scheduled Date -->
                            <td class="py-4 px-4 text-xs text-gray-500 font-medium whitespace-nowrap">
                                {{ $notif->scheduled_date }}
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <span class="{{ $statusClass }} px-3 py-1 rounded-full text-xs font-semibold inline-block">
                                    {{ $notif->status }}
                                </span>
                            </td>

                            <!-- Action (View Eye + Delete Trash) -->
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- View/Details Button (Blue) -->
                                    <button 
                                        type="button" 
                                        data-notif="{{ base64_encode(json_encode($notif)) }}" 
                                        onclick="handleViewNotificationClick(this)" 
                                        class="w-8 h-8 rounded-lg bg-[#2563EB] hover:bg-blue-700 text-white flex items-center justify-center transition shadow-xs cursor-pointer"
                                        title="View Details"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>

                                    <!-- Delete Button (Red) -->
                                    <button 
                                        type="button" 
                                        data-id="{{ $notif->id }}" 
                                        data-title="{{ $notif->title }}" 
                                        onclick="handleDeleteNotificationClick(this)" 
                                        class="w-8 h-8 rounded-lg bg-[#EF4444] hover:bg-rose-600 text-white flex items-center justify-center transition shadow-xs cursor-pointer"
                                        title="Delete Notification"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                    </svg>
                                    <p class="text-sm font-medium">No notifications found matching your filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer (Matching media_1791125092242.png: ← 1 2 3 4 →) -->
        <div class="p-4 sm:p-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
            <div>
                Showing {{ $filteredCount > 0 ? (($page - 1) * $perPage + 1) : 0 }} - {{ min($page * $perPage, $filteredCount) }} of {{ $filteredCount }} notifications
            </div>
            <div class="flex items-center space-x-1.5">
                <!-- Prev Button -->
                @if($page > 1)
                    <a href="{{ route('notifications.index', array_merge(request()->query(), ['page' => $page - 1])) }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 flex items-center justify-center transition">
                        &larr;
                    </a>
                @else
                    <button type="button" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-300 flex items-center justify-center transition cursor-not-allowed" disabled>
                        &larr;
                    </button>
                @endif

                <!-- Page numbers -->
                @for($p = 1; $p <= $totalPages; $p++)
                    @if($p == $page)
                        <button type="button" class="w-8 h-8 rounded-lg bg-[#2563EB] text-white font-bold flex items-center justify-center shadow-xs">
                            {{ $p }}
                        </button>
                    @else
                        <a href="{{ route('notifications.index', array_merge(request()->query(), ['page' => $p])) }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center justify-center transition font-medium">
                            {{ $p }}
                        </a>
                    @endif
                @endfor

                <!-- Next Button -->
                @if($page < $totalPages)
                    <a href="{{ route('notifications.index', array_merge(request()->query(), ['page' => $page + 1])) }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 flex items-center justify-center transition">
                        &rarr;
                    </a>
                @else
                    <button type="button" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-300 flex items-center justify-center transition cursor-not-allowed" disabled>
                        &rarr;
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- CREATE NOTIFICATION MODAL (Matching media_1791125150907.png) -->
<!-- ========================================== -->
<div id="createNotificationModal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-4 sm:p-5 relative max-h-250 overflow-y-auto">
        <!-- Close Button -->
        <button type="button" onclick="closeCreateNotificationModal()" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Title -->
        <h2 class="text-xl font-bold text-gray-900 mb-5">Create Notification</h2>

        <!-- Form -->
        <form id="createNotificationForm" action="{{ route('notifications.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- 1. Notification Type * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Notification Type <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </div>
                    <select 
                        name="type" 
                        id="newNotifType" 
                        required 
                        class="w-full appearance-none pl-10 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer"
                        onchange="handleTypeChange(this.value)"
                    >
                        <option value="" disabled selected>Select notification type</option>
                        <option value="Order Ready Notification">Order Ready Notification</option>
                        <option value="Pickup Reminder">Pickup Reminder</option>
                        <option value="Delivery Reminder">Delivery Reminder</option>
                        <option value="Payment Reminder">Payment Reminder</option>
                        <option value="Promotion Notification">Promotion Notification</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3.5 pointer-events-none text-gray-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- 2. Title * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Title <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 font-bold text-sm">
                        A
                    </div>
                    <input 
                        type="text" 
                        name="title" 
                        id="newNotifTitle" 
                        required 
                        placeholder="Enter notification title" 
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                    >
                </div>
            </div>

            <!-- 3. Message * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Message <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <textarea 
                        name="message" 
                        id="newNotifMessage" 
                        required 
                        maxlength="500" 
                        rows="3" 
                        placeholder="Enter message content..." 
                        class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition resize-none"
                        oninput="document.getElementById('createCharCount').innerText = this.value.length"
                    ></textarea>
                </div>
                <div class="text-right text-xs text-gray-400 mt-1">
                    <span id="createCharCount">0</span>/500
                </div>
            </div>

            <!-- 4. Target Audience * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Target Audience <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <select 
                        name="target" 
                        id="newNotifTarget" 
                        required 
                        class="w-full appearance-none pl-10 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer"
                    >
                        <option value="" disabled selected>Select target audience</option>
                        <option value="All Customers">All Customers</option>
                        <option value="Sokha Dara">Sokha Dara (#ORD-0010)</option>
                        <option value="Chan Sreyneath">Chan Sreyneath (#ORD-0009)</option>
                        <option value="Heng Channa">Heng Channa (#ORD-0008)</option>
                        <option value="Lim Vannak">Lim Vannak (#ORD-0007)</option>
                        <option value="Kim Sovannary">Kim Sovannary (#ORD-0006)</option>
                        <option value="Thon Sreynich">Thon Sreynich (#ORD-0005)</option>
                        <option value="Pich Sothey">Pich Sothey (#ORD-0004)</option>
                        <option value="Va Sovan">Va Sovan (#ORD-0003)</option>
                        <option value="Meng Chhay">Meng Chhay (#ORD-0002)</option>
                        <option value="Keo Pisey">Keo Pisey (#ORD-0001)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3.5 pointer-events-none text-gray-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- 5. Scheduled Date & Time -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Scheduled Date & Time
                </label>
                <div class="flex items-center space-x-2.5">
                    <!-- Date Input -->
                    <div class="relative flex-1">
                        <input 
                            type="date" 
                            name="scheduled_date" 
                            id="newNotifDate" 
                            value="{{ date('Y-m-d') }}" 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                        >
                    </div>
                    <!-- Time Input -->
                    <div class="relative flex-1">
                        <input 
                            type="text" 
                            name="scheduled_time" 
                            id="newNotifTime" 
                            value="10:00 AM" 
                            placeholder="10:00 AM"
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                        >
                    </div>
                </div>
            </div>

            <!-- 6. Status (Send Now / Schedule Later) -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Status
                </label>
                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                        <input type="radio" name="status" value="Send Now" checked class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Send Now</span>
                    </label>
                    <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                        <input type="radio" name="status" value="Schedule Later" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Schedule Later</span>
                    </label>
                </div>
            </div>

            <!-- 7. Automatic Event Notice Box (Matching media_1791125150907.png) -->
            <div class="bg-blue-50/70 border border-blue-100 rounded-2xl p-4 text-xs text-blue-900 flex items-start space-x-3">
                <div class="text-[#2563EB] shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="leading-relaxed">
                    You can also send <strong class="font-bold">notifications automatically</strong> based on <strong class="font-bold">specific events</strong> (e.g., order status changes, payment received, etc.).
                </div>
            </div>

            <!-- 8. Actions: Cancel & Send Notification -->
            <div class="flex items-center space-x-3 pt-2">
                <button 
                    type="button" 
                    onclick="closeCreateNotificationModal()" 
                    class="flex-1 py-3 px-5 border border-gray-200 rounded-xl text-blue-600 font-semibold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="flex-1 py-3 px-5 bg-[#2563EB] hover:bg-blue-700 text-white font-semibold text-sm rounded-xl flex items-center justify-center space-x-2 shadow-xs transition cursor-pointer"
                >
                    <svg class="w-4 h-4 transform rotate-45 -mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                    <span>Send Notification</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- VIEW DETAILS MODAL -->
<!-- ========================================== -->
<div id="viewNotificationModal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 relative animate-scale-up">
        <button type="button" onclick="closeViewNotificationModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="flex items-center space-x-3 mb-4">
            <div id="viewNotifIconContainer" class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                <i id="viewNotifIcon" class="fa-regular fa-file-lines text-base"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 leading-tight" id="viewNotifTitle">Notification Details</h3>
                <span id="viewNotifType" class="text-xs font-semibold text-gray-500">Order Ready</span>
            </div>
        </div>

        <div class="space-y-3.5 text-sm bg-gray-50/70 p-4 rounded-2xl border border-gray-100">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-0.5">Message</span>
                <p id="viewNotifMessage" class="text-gray-800 leading-relaxed font-medium">Your order #ORD-0010 is ready for pickup.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-200/50">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-0.5">Target Audience</span>
                    <span id="viewNotifTarget" class="font-bold text-gray-800 text-xs">Sokha Dara</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-0.5">Status</span>
                    <span id="viewNotifStatus" class="bg-emerald-50 text-emerald-600 px-2.5 py-0.5 rounded-full text-xs font-semibold inline-block">Sent</span>
                </div>
            </div>
            <div class="pt-2 border-t border-gray-200/50">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-0.5">Scheduled Date & Time</span>
                <span id="viewNotifDate" class="text-gray-600 text-xs font-medium">2025-09-15 10:24 AM</span>
            </div>
        </div>

        <div class="mt-5">
            <button type="button" onclick="closeViewNotificationModal()" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition cursor-pointer">
                Close
            </button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- DELETE CONFIRMATION MODAL -->
<!-- ========================================== -->
<div id="deleteNotificationModal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 text-center relative animate-scale-up">
        <div class="w-14 h-14 rounded-full bg-rose-50 text-rose-500 mx-auto flex items-center justify-center mb-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
        </div>

        <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Notification</h3>
        <p class="text-sm text-gray-500 mb-4">
            Are you sure you want to delete <span id="deleteNotifTitleSpan" class="font-bold text-gray-800"></span>? This action cannot be undone.
        </p>

        <form id="deleteNotificationForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center space-x-3">
                <button 
                    type="button" 
                    onclick="closeDeleteNotificationModal()" 
                    class="flex-1 py-2.5 px-4 border border-gray-200 rounded-xl text-gray-600 font-semibold text-sm hover:bg-gray-50 transition cursor-pointer"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="flex-1 py-2.5 px-4 bg-[#EF4444] hover:bg-rose-600 text-white font-semibold text-sm rounded-xl transition cursor-pointer shadow-xs"
                >
                    Delete Notification
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT LOGIC -->
<!-- ========================================== -->
<script>
    const NOTIFICATIONS_BASE_URL = "{{ url('/notifications') }}";

    // 1. Create Modal Handlers
    function openCreateNotificationModal() {
        document.getElementById('createNotificationForm').reset();
        document.getElementById('createCharCount').innerText = '0';
        document.getElementById('createNotificationModal').classList.remove('hidden');
    }

    function closeCreateNotificationModal() {
        document.getElementById('createNotificationModal').classList.add('hidden');
    }

    // Auto-fill template when Type changes
    function handleTypeChange(val) {
        const titleInput = document.getElementById('newNotifTitle');
        const messageInput = document.getElementById('newNotifMessage');

        if (val === 'Order Ready Notification' || val === 'Order Ready') {
            titleInput.value = 'Order Ready';
            messageInput.value = 'Your laundry order is ready for pickup! Please bring your order receipt.';
        } else if (val === 'Pickup Reminder') {
            titleInput.value = 'Pickup Reminder';
            messageInput.value = 'Friendly reminder: Please pick up your ready laundry order today.';
        } else if (val === 'Delivery Reminder') {
            titleInput.value = 'Delivery Reminder';
            messageInput.value = 'Your clean laundry is out for delivery. Our driver will arrive shortly!';
        } else if (val === 'Payment Reminder') {
            titleInput.value = 'Payment Reminder';
            messageInput.value = 'Your invoice payment is currently pending. Please click or visit the counter to settle.';
        } else if (val === 'Promotion Notification' || val === 'Promotion') {
            titleInput.value = 'Promotion';
            messageInput.value = 'Special offer: Enjoy 20% OFF on all laundry and washing services this week!';
        }
        document.getElementById('createCharCount').innerText = messageInput.value.length;
    }

    // 2. View Modal Handlers
    function openViewNotificationModal(notif) {
        if (!notif) return;

        document.getElementById('viewNotifTitle').innerText = notif.title || 'Notification';
        document.getElementById('viewNotifType').innerText = notif.type || 'System';
        document.getElementById('viewNotifMessage').innerText = notif.message || '';
        document.getElementById('viewNotifTarget').innerText = notif.target || 'All Customers';
        document.getElementById('viewNotifDate').innerText = notif.scheduled_date || 'N/A';

        // Status badge
        const statusSpan = document.getElementById('viewNotifStatus');
        statusSpan.innerText = notif.status || 'Sent';
        if (notif.status === 'Sent') {
            statusSpan.className = 'bg-emerald-50 text-emerald-600 px-2.5 py-0.5 rounded-full text-xs font-semibold inline-block';
        } else if (notif.status === 'Pending') {
            statusSpan.className = 'bg-amber-50 text-amber-600 px-2.5 py-0.5 rounded-full text-xs font-semibold inline-block';
        } else if (notif.status === 'Scheduled') {
            statusSpan.className = 'bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full text-xs font-semibold inline-block';
        } else {
            statusSpan.className = 'bg-rose-50 text-rose-600 px-2.5 py-0.5 rounded-full text-xs font-semibold inline-block';
        }

        // Icon
        const iconElem = document.getElementById('viewNotifIcon');
        const iconCont = document.getElementById('viewNotifIconContainer');
        if (notif.type === 'Pickup Reminder') {
            iconCont.className = 'w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-solid fa-bag-shopping text-base';
        } else if (notif.type === 'Delivery Reminder') {
            iconCont.className = 'w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-solid fa-truck-fast text-base';
        } else if (notif.type === 'Payment Reminder') {
            iconCont.className = 'w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-solid fa-credit-card text-base';
        } else if (notif.type === 'Promotion' || notif.type === 'Promotion Notification') {
            iconCont.className = 'w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-solid fa-bullhorn text-base';
        } else {
            iconCont.className = 'w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-regular fa-file-lines text-base';
        }

        document.getElementById('viewNotificationModal').classList.remove('hidden');
    }

    function closeViewNotificationModal() {
        document.getElementById('viewNotificationModal').classList.add('hidden');
    }

    function handleViewNotificationClick(btn) {
        try {
            const raw = btn.getAttribute('data-notif');
            if (raw) {
                const notif = JSON.parse(atob(raw));
                openViewNotificationModal(notif);
            }
        } catch (e) {
            console.error('Error parsing notification:', e);
        }
    }

    // 3. Delete Modal Handlers
    function openDeleteNotificationModal(id, title) {
        const form = document.getElementById('deleteNotificationForm');
        form.action = `${NOTIFICATIONS_BASE_URL}/${id}`;
        document.getElementById('deleteNotifTitleSpan').innerText = `"${title}"`;
        document.getElementById('deleteNotificationModal').classList.remove('hidden');
    }

    function handleDeleteNotificationClick(btn) {
        const id = btn.getAttribute('data-id');
        const title = btn.getAttribute('data-title');
        openDeleteNotificationModal(id, title);
    }

    function closeDeleteNotificationModal() {
        document.getElementById('deleteNotificationModal').classList.add('hidden');
    }

    // 4. Reset Filters
    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('typeFilter').value = 'All Types';
        document.getElementById('statusFilter').value = 'All Status';
        window.location.href = "{{ route('notifications.index') }}";
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Live client-side instant filtering as user types
        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const statusFilter = document.getElementById('statusFilter');
        const tableBody = document.getElementById('notificationsTableBody');

        function applyClientFilter() {
            const query = (searchInput.value || '').toLowerCase().trim();
            const type = typeFilter.value;
            const status = statusFilter.value;

            const rows = tableBody.querySelectorAll('tr.notif-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const matchesQuery = !query || text.includes(query);
                
                let matchesType = true;
                if (type !== 'All Types') {
                    matchesType = text.includes(type.toLowerCase());
                }

                let matchesStatus = true;
                if (status !== 'All Status') {
                    matchesStatus = text.includes(status.toLowerCase());
                }

                if (matchesQuery && matchesType && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const badge = document.getElementById('notificationsCountBadge');
            if (badge) {
                badge.innerText = `${visibleCount} notifications`;
            }
        }

        if (searchInput) searchInput.addEventListener('input', applyClientFilter);
        if (typeFilter) typeFilter.addEventListener('change', applyClientFilter);
        if (statusFilter) statusFilter.addEventListener('change', applyClientFilter);

        // AJAX submit for Create Form
        const createForm = document.getElementById('createNotificationForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Sending...</span>';
                }
                const formData = new FormData(this);
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    closeCreateNotificationModal();
                    window.location.reload();
                })
                .catch(err => {
                    createForm.submit();
                });
            });
        }

        // AJAX submit for Delete Form
        const deleteForm = document.getElementById('deleteNotificationForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Deleting...</span>';
                }
                const formData = new FormData(this);
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    closeDeleteNotificationModal();
                    window.location.reload();
                })
                .catch(err => {
                    deleteForm.submit();
                });
            });
        }

        // Close modal when clicking outside
        ['createNotificationModal', 'viewNotificationModal', 'deleteNotificationModal'].forEach(id => {
            const modal = document.getElementById(id);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            }
        });
    });
</script>
@endsection