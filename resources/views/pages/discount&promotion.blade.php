@extends('layouts.navbar')

@section('title', 'Discount & Promotion')

@section('content')
<div class="space-y-6 w-full mx-auto">

    <!-- Flash Notifications -->
    @if(session('success'))
        <div id="flashSuccessAlert" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs transition-all duration-500 animate-fade-in">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <script>
            setTimeout(function() {
                window.location.reload();
            }, 200);
        </script>
        <script>
            setTimeout(function() {
                const el = document.getElementById('flashSuccessAlert');
                if (el) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-6px)';
                    setTimeout(() => el.remove(), 400);
                }
            }, 3500);
        </script>
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

    <!-- Top Action Bar: Add Promotion Button -->
    <div class="flex items-center justify-end">
        <button type="button" onclick="openAddPromotionModal()" class="bg-[#2563EB] hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-xl shadow-xs text-sm flex items-center space-x-2 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Add Promotion</span>
        </button>
    </div>

    <!-- 4 STATS CARDS (Matching media_1791123021073.png) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Card 1: Total Promotions -->
        <div class="bg-[#F0F5FF] border border-blue-100/70 rounded-2xl p-5 flex items-center space-x-4 shadow-[0_2px_10px_rgba(37,99,235,0.03)] hover:shadow-md transition">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center shrink-0 text-[#2563EB]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-500 font-medium">Total Promotions</p>
                <h3 class="text-xl font-bold text-gray-900 leading-tight mt-0.5" id="stat-total">{{ $stats['total'] ?? 6 }}</h3>
                <p class="text-[11.5px] font-semibold text-emerald-600 mt-0.5 flex items-center">
                    <span>&uarr; 1 new this month</span>
                </p>
            </div>
        </div>

        <!-- Card 2: Active Promotions -->
        <div class="bg-[#F0FDF4] border border-emerald-100/70 rounded-2xl p-5 flex items-center space-x-4 shadow-[0_2px_10px_rgba(16,185,129,0.03)] hover:shadow-md transition">
            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-500 font-medium">Active Promotions</p>
                <h3 class="text-xl font-bold text-gray-900 leading-tight mt-0.5" id="stat-active">{{ $stats['active'] ?? 4 }}</h3>
                <p class="text-[11.5px] font-semibold text-emerald-600 mt-0.5 flex items-center">
                    <span>&uarr; 1 more than last month</span>
                </p>
            </div>
        </div>

        <!-- Card 3: Expired Promotions -->
        <div class="bg-[#FFFBEB] border border-amber-100/70 rounded-2xl p-5 flex items-center space-x-4 shadow-[0_2px_10px_rgba(245,158,11,0.03)] hover:shadow-md transition">
            <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center shrink-0 text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-500 font-medium">Expired Promotions</p>
                <h3 class="text-xl font-bold text-gray-900 leading-tight mt-0.5" id="stat-expired">{{ $stats['expired'] ?? 2 }}</h3>
                <p class="text-[11.5px] font-semibold text-rose-500 mt-0.5 flex items-center">
                    <span>&darr; 1 less than last month</span>
                </p>
            </div>
        </div>

        <!-- Card 4: Total Coupon Codes -->
        <div class="bg-[#FAF5FF] border border-purple-100/70 rounded-2xl p-5 flex items-center space-x-4 shadow-[0_2px_10px_rgba(168,85,247,0.03)] hover:shadow-md transition">
            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center shrink-0 text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-500 font-medium">Total Coupon Codes</p>
                <h3 class="text-xl font-bold text-gray-900 leading-tight mt-0.5" id="stat-coupons">{{ $stats['coupon_codes'] ?? 8 }}</h3>
                <p class="text-[11.5px] font-semibold text-emerald-600 mt-0.5 flex items-center">
                    <span>&uarr; 2 new this month</span>
                </p>
            </div>
        </div>
    </div>

    <!-- SEARCH & FILTER TOOLBAR (Matching media_1791123021073.png) -->
    <form id="filterForm" method="GET" action="{{ route('promotions.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
        <!-- Search Input with icon -->
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
                placeholder="Search promotion by name, code, or type..." 
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
                <option value="Percentage Discount" {{ ($typeFilter ?? '') === 'Percentage Discount' ? 'selected' : '' }}>Percentage Discount</option>
                <option value="Fixed Amount" {{ ($typeFilter ?? '') === 'Fixed Amount' ? 'selected' : '' }}>Fixed Amount</option>
                <option value="Loyalty Discount" {{ ($typeFilter ?? '') === 'Loyalty Discount' ? 'selected' : '' }}>Loyalty Discount</option>
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
                <option value="Active" {{ ($statusFilter ?? '') === 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Expired" {{ ($statusFilter ?? '') === 'Expired' ? 'selected' : '' }}>Expired</option>
                <option value="Inactive" {{ ($statusFilter ?? '') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
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

    <!-- PROMOTION LIST CARD (Matching media_1791123021073.png) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.03)] overflow-hidden">
        <!-- Card Header -->
        <div class="p-5 sm:p-6 border-b border-gray-100 flex items-center space-x-3">
            <h2 class="text-base sm:text-lg font-bold text-gray-900">Promotion List</h2>
            <span id="promotionsCountBadge" class="bg-blue-50 text-blue-600 rounded-full px-2.5 py-0.5 text-xs font-semibold">
                {{ count($promotions) }} promotions
            </span>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="promotionsTable">
                <thead>
                    <tr class="bg-[#F3F4FD] border-b border-gray-100 text-[12px] text-gray-500 font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center w-14">#</th>
                        <th class="py-3.5 px-4 text-left">Promotion Name</th>
                        <th class="py-3.5 px-4 text-left">Type</th>
                        <th class="py-3.5 px-4 text-left">Discount Value</th>
                        <th class="py-3.5 px-4 text-left">Valid Period</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm" id="promotionsTableBody">
                    @forelse($promotions as $index => $promo)
                        @php
                            // Format discount representation
                            $val = (float)($promo->discount_value ?? 0);
                            $unit = $promo->discount_unit ?? '%';
                            if ($unit === '$' || $unit === 'USD') {
                                $displayDiscount = '$' . number_format($val, 2) . ' OFF';
                            } else {
                                $displayDiscount = rtrim(rtrim(number_format($val, 2), '0'), '.') . '% OFF';
                            }

                            // Type pill colors
                            $typeClass = 'bg-blue-50 text-blue-600';
                            if ($promo->type === 'Fixed Amount') {
                                $typeClass = 'bg-fuchsia-50 text-fuchsia-600';
                            } elseif ($promo->type === 'Loyalty Discount') {
                                $typeClass = 'bg-amber-50 text-amber-600';
                            }

                            // Status pill colors
                            $statusClass = 'bg-emerald-50 text-emerald-600';
                            if ($promo->status === 'Expired') {
                                $statusClass = 'bg-gray-100 text-gray-600';
                            } elseif ($promo->status === 'Inactive') {
                                $statusClass = 'bg-rose-50 text-rose-600';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors promo-row" data-id="{{ $promo->id }}">
                            <!-- Index -->
                            <td class="py-4 px-4 text-center text-gray-500 font-medium">
                                {{ $index + 1 }}
                            </td>

                            <!-- Promotion Name -->
                            <td class="py-4 px-4 font-semibold text-gray-900">
                                <span>{{ $promo->name }}</span>
                                @if(!empty($promo->coupon_code))
                                    <span class="block text-[11px] text-gray-400 font-normal tracking-wide">Code: {{ $promo->coupon_code }}</span>
                                @endif
                            </td>

                            <!-- Type -->
                            <td class="py-4 px-4">
                                <span class="{{ $typeClass }} px-3 py-1 rounded-full text-xs font-semibold inline-block">
                                    {{ $promo->type }}
                                </span>
                            </td>

                            <!-- Discount Value -->
                            <td class="py-4 px-4 font-bold text-gray-900">
                                {{ $displayDiscount }}
                            </td>

                            <!-- Valid Period -->
                            <td class="py-4 px-4 text-xs text-gray-500 font-medium">
                                {{ $promo->start_date }} &rarr; {{ $promo->end_date }}
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4 text-center">
                                <span class="{{ $statusClass }} px-3 py-1 rounded-full text-xs font-semibold inline-block">
                                    {{ $promo->status }}
                                </span>
                            </td>

                            <!-- Action -->
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit Button (Blue with pencil) -->
                                    <button 
                                        type="button" 
                                        data-promo="{{ base64_encode(json_encode($promo)) }}" 
                                        onclick="handleEditPromotionClick(this)" 
                                        class="w-8 h-8 rounded-lg bg-[#2563EB] hover:bg-blue-700 text-white flex items-center justify-center transition shadow-xs cursor-pointer"
                                        title="Edit Promotion"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>

                                    <!-- Delete Button (Red with trash) -->
                                    <button 
                                        type="button" 
                                        data-id="{{ $promo->id }}" 
                                        data-name="{{ $promo->name }}" 
                                        onclick="handleDeletePromotionClick(this)" 
                                        class="w-8 h-8 rounded-lg bg-[#EF4444] hover:bg-rose-600 text-white flex items-center justify-center transition shadow-xs cursor-pointer"
                                        title="Delete Promotion"
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
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <p class="text-sm font-medium">No promotions found matching your filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer (Matching media_1791123021073.png) -->
        <div class="p-4 sm:p-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
            <div>
                Showing 1 - {{ count($promotions) }} of {{ count($promotions) }} promotions
            </div>
            <div class="flex items-center space-x-1.5">
                <button type="button" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 flex items-center justify-center transition disabled:opacity-50" disabled>
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </button>
                <button type="button" class="w-8 h-8 rounded-lg bg-[#2563EB] text-white font-bold flex items-center justify-center shadow-xs">
                    1
                </button>
                <button type="button" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 flex items-center justify-center transition disabled:opacity-50" disabled>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- RECENT PROMOTIONS CARD (Bottom card matching media_1791123021073.png) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.03)] p-5 sm:p-6">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div class="flex items-center space-x-2.5">
                <svg class="w-5 h-5 text-[#2563EB]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <h3 class="text-base font-bold text-gray-900">Recent Promotions</h3>
            </div>
            <a href="javascript:void(0)" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="text-xs font-semibold text-[#2563EB] hover:underline">
                View All
            </a>
        </div>

        <!-- Mini Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F3F4FD] border-b border-gray-100 text-[12px] text-gray-500 font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-left">Promotion Name</th>
                        <th class="py-3.5 px-4 text-left">Type</th>
                        <th class="py-3.5 px-4 text-left">Discount</th>
                        <th class="py-3.5 px-4 text-left">Valid Period</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($recentPromotions as $rPromo)
                        @php
                            $rVal = (float)($rPromo->discount_value ?? 0);
                            $rUnit = $rPromo->discount_unit ?? '%';
                            $rDisplay = ($rUnit === '$' || $rUnit === 'USD') ? '$' . number_format($rVal, 2) . ' OFF' : rtrim(rtrim(number_format($rVal, 2), '0'), '.') . '% OFF';

                            $rTypeClass = 'bg-blue-50 text-blue-600';
                            if ($rPromo->type === 'Fixed Amount') {
                                $rTypeClass = 'bg-fuchsia-50 text-fuchsia-600';
                            } elseif ($rPromo->type === 'Loyalty Discount') {
                                $rTypeClass = 'bg-amber-50 text-amber-600';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5 px-4 font-semibold text-gray-900">
                                {{ $rPromo->name }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="{{ $rTypeClass }} px-3 py-1 rounded-full text-xs font-semibold inline-block">
                                    {{ $rPromo->type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-gray-900">
                                {{ $rDisplay }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-gray-500 font-medium">
                                {{ $rPromo->start_date }} &rarr; {{ $rPromo->end_date }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full text-xs font-semibold inline-block">
                                    {{ $rPromo->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-400 text-xs">
                                No recent active promotions.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- ADD PROMOTION MODAL -->
<!-- ========================================== -->
<div id="addPromotionModal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden scrollbar-hide">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-4 sm:p-5 relative max-h-250 overflow-y-auto">
        <!-- Close Button -->
        <button type="button" onclick="closeAddPromotionModal()" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Title -->
        <h2 class="text-xl font-bold text-gray-900 mb-5">Add Promotion</h2>

        <!-- Form -->
        <form id="addPromotionForm" action="{{ route('promotions.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- 1. Promotion Name * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Promotion Name <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    required 
                    placeholder="Enter promotion name" 
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                >
            </div>

            <!-- 2. Promotion Type * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Promotion Type <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select 
                        name="type" 
                        id="addPromotionType" 
                        required 
                        class="w-full appearance-none px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer pr-10"
                    >
                        <option value="" disabled selected>Select promotion type</option>
                        <option value="Percentage Discount">Percentage Discount</option>
                        <option value="Fixed Amount">Fixed Amount</option>
                        <option value="Loyalty Discount">Loyalty Discount</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3.5 pointer-events-none text-gray-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- 3. Discount Value * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Discount Value <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center space-x-2.5">
                    <input 
                        type="number" 
                        step="0.01" 
                        name="discount_value" 
                        id="addDiscountValue" 
                        required 
                        placeholder="Enter discount value" 
                        class="flex-1 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                    >
                    <div class="relative w-20">
                        <select 
                            name="discount_unit" 
                            id="addDiscountUnit" 
                            class="w-full appearance-none px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer pr-7"
                        >
                            <option value="%">%</option>
                            <option value="$">$</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2.5 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Valid Period * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Valid Period <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center space-x-2.5">
                    <div class="relative flex-1">
                        <input 
                            type="date" 
                            name="start_date" 
                            id="addStartDate" 
                            value="{{ date('Y-m-d') }}" 
                            required 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                        >
                    </div>
                    <span class="text-gray-400 font-medium text-sm">&rarr;</span>
                    <div class="relative flex-1">
                        <input 
                            type="date" 
                            name="end_date" 
                            id="addEndDate" 
                            value="{{ date('Y-m-d', strtotime('+30 days')) }}" 
                            required 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                        >
                    </div>
                </div>
            </div>

            <!-- 5. Coupon Code (Optional) -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Coupon Code <span class="text-gray-400 font-normal">(Optional)</span>
                </label>
                <input 
                    type="text" 
                    name="coupon_code" 
                    id="addCouponCode" 
                    placeholder="ENTER COUPON CODE" 
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm uppercase placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition tracking-wider"
                >
            </div>

            <!-- 6. Description -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Description
                </label>
                <textarea 
                    name="description" 
                    id="addDescription" 
                    maxlength="500" 
                    rows="3" 
                    placeholder="Enter promotion description..." 
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition resize-none"
                    oninput="document.getElementById('addCharCount').innerText = this.value.length"
                ></textarea>
                <div class="text-right text-xs text-gray-400 mt-1">
                    <span id="addCharCount">0</span>/500
                </div>
            </div>

            <!-- 7. Status * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Status <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                        <input type="radio" name="status" value="Active" checked class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                    <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                        <input type="radio" name="status" value="Inactive" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Inactive</span>
                    </label>
                </div>
            </div>

            <!-- Actions: Cancel & Save Promotion -->
            <div class="flex items-center space-x-3 pt-3">
                <button 
                    type="button" 
                    onclick="closeAddPromotionModal()" 
                    class="flex-1 py-3 px-5 border border-gray-200 rounded-xl text-blue-600 font-semibold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="flex-1 py-3 px-5 bg-[#2563EB] hover:bg-blue-700 text-white font-semibold text-sm rounded-xl flex items-center justify-center space-x-2 shadow-xs transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span>Save Promotion</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- EDIT PROMOTION MODAL -->
<!-- ========================================== -->
<div id="editPromotionModal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-7 relative max-h-[92vh] overflow-y-auto">
        <!-- Close Button -->
        <button type="button" onclick="closeEditPromotionModal()" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Title -->
        <h2 class="text-xl font-bold text-gray-900 mb-5">Edit Promotion</h2>

        <!-- Form -->
        <form id="editPromotionForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editPromoId">

            <!-- 1. Promotion Name * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Promotion Name <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="editPromoName" 
                    required 
                    placeholder="Enter promotion name" 
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                >
            </div>

            <!-- 2. Promotion Type * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Promotion Type <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select 
                        name="type" 
                        id="editPromoType" 
                        required 
                        class="w-full appearance-none px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer pr-10"
                    >
                        <option value="Percentage Discount">Percentage Discount</option>
                        <option value="Fixed Amount">Fixed Amount</option>
                        <option value="Loyalty Discount">Loyalty Discount</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3.5 pointer-events-none text-gray-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- 3. Discount Value * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Discount Value <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center space-x-2.5">
                    <input 
                        type="number" 
                        step="0.01" 
                        name="discount_value" 
                        id="editPromoValue" 
                        required 
                        placeholder="Enter discount value" 
                        class="flex-1 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                    >
                    <div class="relative w-20">
                        <select 
                            name="discount_unit" 
                            id="editPromoUnit" 
                            class="w-full appearance-none px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer pr-7"
                        >
                            <option value="%">%</option>
                            <option value="$">$</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2.5 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Valid Period * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Valid Period <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center space-x-2.5">
                    <div class="relative flex-1">
                        <input 
                            type="date" 
                            name="start_date" 
                            id="editPromoStartDate" 
                            required 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                        >
                    </div>
                    <span class="text-gray-400 font-medium text-sm">&rarr;</span>
                    <div class="relative flex-1">
                        <input 
                            type="date" 
                            name="end_date" 
                            id="editPromoEndDate" 
                            required 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                        >
                    </div>
                </div>
            </div>

            <!-- 5. Coupon Code (Optional) -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Coupon Code <span class="text-gray-400 font-normal">(Optional)</span>
                </label>
                <input 
                    type="text" 
                    name="coupon_code" 
                    id="editPromoCouponCode" 
                    placeholder="ENTER COUPON CODE" 
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm uppercase placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition tracking-wider"
                >
            </div>

            <!-- 6. Description -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Description
                </label>
                <textarea 
                    name="description" 
                    id="editPromoDescription" 
                    maxlength="500" 
                    rows="3" 
                    placeholder="Enter promotion description..." 
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition resize-none"
                    oninput="document.getElementById('editCharCount').innerText = this.value.length"
                ></textarea>
                <div class="text-right text-xs text-gray-400 mt-1">
                    <span id="editCharCount">0</span>/500
                </div>
            </div>

            <!-- 7. Status * -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Status <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                        <input type="radio" name="status" id="editStatusActive" value="Active" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                    <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                        <input type="radio" name="status" id="editStatusExpired" value="Expired" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Expired</span>
                    </label>
                    <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                        <input type="radio" name="status" id="editStatusInactive" value="Inactive" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Inactive</span>
                    </label>
                </div>
            </div>

            <!-- Actions: Cancel & Save Changes -->
            <div class="flex items-center space-x-3 pt-3">
                <button 
                    type="button" 
                    onclick="closeEditPromotionModal()" 
                    class="flex-1 py-3 px-5 border border-gray-200 rounded-xl text-blue-600 font-semibold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="flex-1 py-3 px-5 bg-[#2563EB] hover:bg-blue-700 text-white font-semibold text-sm rounded-xl flex items-center justify-center space-x-2 shadow-xs transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- DELETE CONFIRMATION MODAL -->
<!-- ========================================== -->
<div id="deletePromotionModal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 text-center relative animate-scale-up">
        <div class="w-14 h-14 rounded-full bg-rose-50 text-rose-500 mx-auto flex items-center justify-center mb-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
        </div>

        <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Promotion</h3>
        <p class="text-sm text-gray-500 mb-4">
            Are you sure you want to delete <span id="deletePromoNameSpan" class="font-bold text-gray-800"></span>? This action cannot be undone.
        </p>

        <form id="deletePromotionForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center space-x-3">
                <button 
                    type="button" 
                    onclick="closeDeletePromotionModal()" 
                    class="flex-1 py-2.5 px-4 border border-gray-200 rounded-xl text-gray-600 font-semibold text-sm hover:bg-gray-50 transition cursor-pointer"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="flex-1 py-2.5 px-4 bg-[#EF4444] hover:bg-rose-600 text-white font-semibold text-sm rounded-xl transition cursor-pointer shadow-xs"
                >
                    Delete Promotion
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT LOGIC -->
<!-- ========================================== -->
<script>
    // Base endpoints
    const PROMOTIONS_BASE_URL = "{{ url('/promotions') }}";

    // 1. Add Modal Handlers
    function openAddPromotionModal() {
        document.getElementById('addPromotionForm').reset();
        document.getElementById('addCharCount').innerText = '0';
        document.getElementById('addPromotionModal').classList.remove('hidden');
    }

    function closeAddPromotionModal() {
        document.getElementById('addPromotionModal').classList.add('hidden');
    }

    // 2. Edit Modal Handlers
    function openEditPromotionModal(promo) {
        if (!promo) return;

        const form = document.getElementById('editPromotionForm');
        form.action = `${PROMOTIONS_BASE_URL}/${promo.id}`;
        
        document.getElementById('editPromoId').value = promo.id;
        document.getElementById('editPromoName').value = promo.name || '';
        document.getElementById('editPromoType').value = promo.type || 'Percentage Discount';
        document.getElementById('editPromoValue').value = promo.discount_value || 0;
        document.getElementById('editPromoUnit').value = promo.discount_unit || '%';
        document.getElementById('editPromoStartDate').value = promo.start_date || '';
        document.getElementById('editPromoEndDate').value = promo.end_date || '';
        document.getElementById('editPromoCouponCode').value = promo.coupon_code || '';
        document.getElementById('editPromoDescription').value = promo.description || '';
        document.getElementById('editCharCount').innerText = (promo.description || '').length;

        // Set radio status
        if (promo.status === 'Active') {
            document.getElementById('editStatusActive').checked = true;
        } else if (promo.status === 'Expired') {
            document.getElementById('editStatusExpired').checked = true;
        } else {
            document.getElementById('editStatusInactive').checked = true;
        }

        document.getElementById('editPromotionModal').classList.remove('hidden');
    }

    function closeEditPromotionModal() {
        document.getElementById('editPromotionModal').classList.add('hidden');
    }

    function handleEditPromotionClick(btn) {
        try {
            const raw = btn.getAttribute('data-promo');
            if (raw) {
                const promo = JSON.parse(atob(raw));
                openEditPromotionModal(promo);
            }
        } catch (e) {
            console.error('Error parsing promotion:', e);
        }
    }

    // 3. Delete Modal Handlers
    function openDeletePromotionModal(id, name) {
        const form = document.getElementById('deletePromotionForm');
        form.action = `${PROMOTIONS_BASE_URL}/${id}`;
        document.getElementById('deletePromoNameSpan').innerText = `"${name}"`;
        document.getElementById('deletePromotionModal').classList.remove('hidden');
    }

    function handleDeletePromotionClick(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        openDeletePromotionModal(id, name);
    }

    function closeDeletePromotionModal() {
        document.getElementById('deletePromotionModal').classList.add('hidden');
    }

    // 4. Reset Filters
    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('typeFilter').value = 'All Types';
        document.getElementById('statusFilter').value = 'All Status';
        window.location.href = "{{ route('promotions.index') }}";
    }

    // 5. Automatic Unit Syncer for Promotion Type
    document.addEventListener('DOMContentLoaded', function() {
        const addTypeSelect = document.getElementById('addPromotionType');
        const addUnitSelect = document.getElementById('addDiscountUnit');

        if (addTypeSelect && addUnitSelect) {
            addTypeSelect.addEventListener('change', function() {
                if (this.value === 'Fixed Amount') {
                    addUnitSelect.value = '$';
                } else {
                    addUnitSelect.value = '%';
                }
            });
        }

        const editTypeSelect = document.getElementById('editPromoType');
        const editUnitSelect = document.getElementById('editPromoUnit');

        if (editTypeSelect && editUnitSelect) {
            editTypeSelect.addEventListener('change', function() {
                if (this.value === 'Fixed Amount') {
                    editUnitSelect.value = '$';
                } else {
                    editUnitSelect.value = '%';
                }
            });
        }

        // Live instant filtering as user types
        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const statusFilter = document.getElementById('statusFilter');
        const tableBody = document.getElementById('promotionsTableBody');

        function applyClientFilter() {
            const query = (searchInput.value || '').toLowerCase().trim();
            const type = typeFilter.value;
            const status = statusFilter.value;

            const rows = tableBody.querySelectorAll('tr.promo-row');
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

            const badge = document.getElementById('promotionsCountBadge');
            if (badge) {
                badge.innerText = `${visibleCount} promotions`;
            }
        }

        if (searchInput) searchInput.addEventListener('input', applyClientFilter);
        if (typeFilter) typeFilter.addEventListener('change', applyClientFilter);
        if (statusFilter) statusFilter.addEventListener('change', applyClientFilter);

        // AJAX submit for Add Form
        const addForm = document.getElementById('addPromotionForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Saving...</span>';
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
                    closeAddPromotionModal();
                    window.location.reload();
                })
                .catch(err => {
                    addForm.submit();
                });
            });
        }

        // AJAX submit for Edit Form
        const editForm = document.getElementById('editPromotionForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Saving...</span>';
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
                    closeEditPromotionModal();
                    window.location.reload();
                })
                .catch(err => {
                    editForm.submit();
                });
            });
        }

        // AJAX submit for Delete Form
        const deleteForm = document.getElementById('deletePromotionForm');
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
                    closeDeletePromotionModal();
                    window.location.reload();
                })
                .catch(err => {
                    deleteForm.submit();
                });
            });
        }

        // Close modal when clicking outside
        ['addPromotionModal', 'editPromotionModal', 'deletePromotionModal'].forEach(id => {
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
