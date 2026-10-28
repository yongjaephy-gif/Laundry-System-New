@extends('layouts.navbar')
@section('title', 'Payments')

@php
    /** @var array $payments */
    /** @var array $pagination */
    /** @var array $availableOrders */
    /** @var array $kpis */
    /** @var array $statusChart */
    /** @var array $methodBreakdown */
    $payments = $payments ?? [];
    $pagination = $pagination ?? [];
    $availableOrders = $availableOrders ?? [];
@endphp

@section('content')
<div class="space-y-6 w-full mx-auto pb-12">

    <!-- Flash Messages (Success & Error Alerts) -->
    @if(session('success'))
        <div id="flash-success" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-2xs transition-all duration-500 animate-in fade-in duration-200">
            <div class="flex items-center space-x-3">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('flash-success').remove()" class="text-emerald-600 hover:text-emerald-800 text-lg leading-none cursor-pointer">&times;</button>
        </div>
        <script>
            setTimeout(function() {
                const el = document.getElementById('flash-success');
                if (el) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-6px)';
                    setTimeout(() => el.remove(), 400);
                }
            }, 3500);
        </script>
    @endif

    @if(session('error'))
        <div id="flash-error" class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-2xs animate-in fade-in duration-200">
            <div class="flex items-center space-x-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('flash-error').remove()" class="text-rose-500 hover:text-rose-700 p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 space-y-1">
            <div class="flex items-center space-x-2 font-bold text-xs mb-1">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                <span>Please correct the following errors:</span>
            </div>
            @foreach($errors->all() as $err)
                <p class="text-xs font-semibold pl-6">• {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <!-- Top Action Bar: Record Payment Button (Aligned Right matching Screenshot 1) -->
    <div class="flex items-center justify-end pt-1">
        <button type="button" 
                onclick="openAddPaymentModal()"
                class="bg-[#2042E3] hover:bg-[#1A38C2] active:scale-[0.98] text-white font-semibold text-sm px-5 py-2.5 rounded-xl flex items-center space-x-2 shadow-xs transition cursor-pointer">
            <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
            </svg>
            <span>Record Payment</span>
        </button>
    </div>

    <!-- 1. Top 4 Metric Cards (Exact Match to Screenshot 1) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Total Payments (Blue tint) -->
        <div class="bg-[#F0F7FF] border border-[#E0EDFD] rounded-2xl p-5 shadow-2xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-[#2563EB] text-white flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-6 h-6 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <rect x="2" y="5" width="20" height="14" rx="3"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Payments</p>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5 tracking-tight">{{ $kpis['total_payments'] ?? '$0.00' }}</h3>
                <p class="text-xs font-semibold text-emerald-600 flex items-center mt-0.5">
                    <span>&uarr; {{ $kpis['total_payments_growth'] ?? '0% vs. last month' }}</span>
                </p>
            </div>
        </div>

        <!-- Card 2: Today's Revenue (Green tint) -->
        <div class="bg-[#F0FDF4] border border-[#DCFCE7] rounded-2xl p-5 shadow-2xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-[#10B981] text-white flex items-center justify-center flex-shrink-0 text-xl font-bold shadow-sm">
                $
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Today's Revenue</p>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5 tracking-tight">{{ $kpis['todays_revenue'] ?? '$0.00' }}</h3>
                <p class="text-xs font-semibold text-emerald-600 flex items-center mt-0.5">
                    <span>&uarr; {{ $kpis['todays_revenue_growth'] ?? '0% vs. yesterday' }}</span>
                </p>
            </div>
        </div>

        <!-- Card 3: Outstanding Balance (Red/Rose tint) -->
        <div class="bg-[#FFF1F2] border border-[#FFE4E6] rounded-2xl p-5 shadow-2xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-[#EF4444] text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-6 h-6 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Outstanding Balance</p>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5 tracking-tight">{{ $kpis['outstanding_balance'] ?? '$0.00' }}</h3>
                <p class="text-xs font-semibold text-rose-600 flex items-center mt-0.5">
                    <span>&uarr; {{ $kpis['outstanding_balance_growth'] ?? '0% vs. last month' }}</span>
                </p>
            </div>
        </div>

        <!-- Card 4: Refunds (Purple tint) -->
        <div class="bg-[#F5F3FF] border border-[#EDE9FE] rounded-2xl p-5 shadow-2xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-[#8B5CF6] text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-6 h-6 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Refunds</p>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5 tracking-tight">{{ $kpis['refunds'] ?? '$0.00' }}</h3>
                <p class="text-xs font-semibold text-emerald-600 flex items-center mt-0.5">
                    <span>&uarr; {{ $kpis['refunds_growth'] ?? '0% vs. last month' }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- 2. Search & Filter Bar (Matching Screenshot 1) -->
    <div class="bg-white rounded-2xl border border-gray-200/90 p-3.5 shadow-2xs">
        <form method="GET" action="{{ route('payment.index') }}" id="paymentFilterForm" class="flex flex-col md:flex-row items-center gap-3">
            
            <!-- Search Input -->
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="text" 
                       name="search" 
                       id="paymentSearchInput"
                       value="{{ $search ?? '' }}"
                       placeholder="Search by order number, customer name, payment ID..." 
                       class="w-full bg-white text-gray-800 placeholder-gray-400 text-sm rounded-xl pl-10 pr-4 py-2 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
            </div>

            <!-- All Payment Status Dropdown -->
            <div class="w-full md:w-48">
                <select name="status" 
                        id="statusFilterSelect"
                        class="w-full bg-white text-gray-700 text-sm rounded-xl px-3.5 py-2 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition appearance-none cursor-pointer bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%236b7280%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1rem] bg-[right_0.8rem_center] bg-no-repeat pr-8">
                    <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>All Payment Status</option>
                    <option value="COMPLETED" {{ ($statusFilter ?? '') === 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                    <option value="PENDING" {{ ($statusFilter ?? '') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                    <option value="REFUNDED" {{ ($statusFilter ?? '') === 'REFUNDED' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <!-- All Payment Methods Dropdown -->
            <div class="w-full md:w-48">
                <select name="method" 
                        id="methodFilterSelect"
                        class="w-full bg-white text-gray-700 text-sm rounded-xl px-3.5 py-2 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition appearance-none cursor-pointer bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%236b7280%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1rem] bg-[right_0.8rem_center] bg-no-repeat pr-8">
                    <option value="all" {{ ($methodFilter ?? 'all') === 'all' ? 'selected' : '' }}>All Payment Methods</option>
                    <option value="Cash" {{ ($methodFilter ?? '') === 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Credit Card" {{ ($methodFilter ?? '') === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                    <option value="Bank Transfer" {{ ($methodFilter ?? '') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="Mobile Payment" {{ ($methodFilter ?? '') === 'Mobile Payment' ? 'selected' : '' }}>Mobile Payment</option>
                </select>
            </div>

            <!-- Buttons: Search & Reset -->
            <div class="flex items-center space-x-2 w-full md:w-auto">
                <button type="submit" 
                        class="flex-1 md:flex-none bg-[#2042E3] hover:bg-[#1A38C2] text-white font-semibold text-sm px-5 py-2 rounded-xl flex items-center justify-center space-x-1.5 shadow-2xs transition cursor-pointer">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>Search</span>
                </button>

                <a href="{{ route('payment.index') }}" 
                   class="flex-1 md:flex-none border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-sm px-4 py-2 rounded-xl flex items-center justify-center space-x-1.5 transition cursor-pointer">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- 3. Payment List Table Card (Matching Screenshot 1) -->
    <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs overflow-hidden">
        
        <!-- Table Header Bar -->
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <h3 class="text-base font-bold text-gray-900 tracking-tight">Payment List</h3>
                <span class="bg-[#EFF6FF] text-[#2563EB] text-xs font-semibold px-2.5 py-0.5 rounded-full">
                    {{ $pagination['total_items'] ?? count($payments) }} payments
                </span>
            </div>
            
            <div class="flex items-center space-x-3 text-gray-400">
                <!-- Grid icon -->
                <button type="button" class="hover:text-gray-600 transition p-1" title="Table View">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </button>
                <!-- Print icon -->
                <a href="{{ route('payment.export') }}" class="hover:text-gray-600 transition p-1" title="Export CSV / Print">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Table Responsive Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">PAYMENT ID</th>
                        <th class="py-3 px-4">ORDER NUMBER</th>
                        <th class="py-3 px-4">CUSTOMER NAME</th>
                        <th class="py-3 px-4">AMOUNT</th>
                        <th class="py-3 px-4">PAYMENT METHOD</th>
                        <th class="py-3 px-4">PAYMENT STATUS</th>
                        <th class="py-3 px-4">PAYMENT DATE</th>
                        <th class="py-3 px-4 text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($payments as $index => $payment)
                    @php /** @var array $payment */ /** @var int $index */ @endphp
                    <tr class="hover:bg-blue-50/20 transition-colors group">
                        
                        <!-- Index -->
                        <td class="py-3.5 px-4 text-center text-xs text-gray-400 font-medium">
                            {{ ($pagination['from'] ?? 1) + $index }}
                        </td>

                        <!-- Payment ID -->
                        <td class="py-3.5 px-4">
                            <button type="button" 
                                    data-id="{{ $payment['payment_id'] }}"
                                    onclick="viewPaymentById(this.dataset.id)"
                                    class="text-[#2563EB] font-bold hover:underline cursor-pointer">
                                {{ $payment['payment_id'] }}
                            </button>
                        </td>

                        <!-- Order Number -->
                        <td class="py-3.5 px-4 text-gray-600 font-medium text-xs">
                            {{ $payment['order_number'] }}
                        </td>

                        <!-- Customer Name (with Initials Avatar) -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold {{ $payment['avatar_color'] ?? 'bg-blue-100 text-blue-700' }} flex-shrink-0">
                                    {{ $payment['customer_initials'] ?? 'CU' }}
                                </div>
                                <span class="font-bold text-gray-900 text-xs tracking-tight">
                                    {{ $payment['customer_name'] }}
                                </span>
                            </div>
                        </td>

                        <!-- Amount -->
                        <td class="py-3.5 px-4 font-bold text-gray-900 text-xs">
                            ${{ number_format($payment['amount'], 2) }}
                        </td>

                        <!-- Payment Method with Icon -->
                        <td class="py-3.5 px-4 text-xs font-medium text-gray-700">
                            <div class="flex items-center space-x-2">
                                @if(stripos($payment['payment_method'] ?? '', 'Cash') !== false)
                                    <svg class="w-4 h-4 text-emerald-600 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                                        <circle cx="12" cy="12" r="2"></circle>
                                        <path d="M6 12h.01M18 12h.01"></path>
                                    </svg>
                                @elseif(stripos($payment['payment_method'] ?? '', 'Credit') !== false)
                                    <svg class="w-4 h-4 text-blue-600 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                        <line x1="1" y1="10" x2="23" y2="10"></line>
                                    </svg>
                                @elseif(stripos($payment['payment_method'] ?? '', 'Mobile') !== false)
                                    <svg class="w-4 h-4 text-amber-500 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-purple-600 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path d="M3 21h18M3 10h18M5 10v11M9 10v11M15 10v11M19 10v11M12 2L2 7h20L12 2z"></path>
                                    </svg>
                                @endif
                                <span>{{ $payment['payment_method'] }}</span>
                            </div>
                        </td>

                        <!-- Payment Status Pill -->
                        <td class="py-3.5 px-4">
                            @if(strtoupper($payment['status'] ?? '') === 'COMPLETED')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#DCFCE7] text-[#15803D] uppercase tracking-wider">
                                    COMPLETED
                                </span>
                            @elseif(strtoupper($payment['status'] ?? '') === 'PENDING')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF3C7] text-[#D97706] uppercase tracking-wider">
                                    PENDING
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#F3E8FF] text-[#9333EA] uppercase tracking-wider">
                                    REFUNDED
                                </span>
                            @endif
                        </td>

                        <!-- Payment Date -->
                        <td class="py-3.5 px-4 text-xs text-gray-400 font-mono">
                            {{ $payment['payment_date'] }}
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-1.5">
                                
                                <!-- 1. View Details (Blue Eye Button - Matching System Style) -->
                                <button type="button" 
                                        data-id="{{ $payment['payment_id'] }}"
                                        onclick="viewPaymentById(this.dataset.id)"
                                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-[#2563EB] hover:bg-blue-700 text-white flex items-center justify-center transition shadow-2xs cursor-pointer" 
                                        title="View Details">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>

                                <!-- 2. Generate / Print Invoice (Green Receipt Button) -->
                                <button type="button" 
                                        data-id="{{ $payment['payment_id'] }}"
                                        onclick="openInvoiceById(this.dataset.id)"
                                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-[#10B981] hover:bg-emerald-600 text-white flex items-center justify-center transition shadow-2xs cursor-pointer" 
                                        title="Generate Invoice / Print Receipt">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                        <rect x="6" y="14" width="12" height="8" rx="1"></rect>
                                    </svg>
                                </button>

                                <!-- 3. Delete Record (Rose / Red Trash Button) -->
                                <form method="POST" action="{{ route('payment.destroy', $payment['payment_id']) }}" onsubmit="return confirm('Delete this payment transaction?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-[#EF4444] hover:bg-rose-600 text-white flex items-center justify-center transition shadow-2xs cursor-pointer" 
                                            title="Delete Record">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>

                                <!-- 4. Action Menu Dropdown Button (Kebab for Refund & Extra Options) -->
                                <div class="relative inline-block text-left">
                                    <button type="button" 
                                            data-menu="menu-{{ $payment['id'] ?? $payment['payment_id'] }}"
                                            onclick="toggleRowMenu(this.dataset.menu, event)"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition shadow-2xs cursor-pointer" 
                                            title="More Actions">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                            <circle cx="12" cy="12" r="1.5"></circle>
                                            <circle cx="12" cy="5" r="1.5"></circle>
                                            <circle cx="12" cy="19" r="1.5"></circle>
                                        </svg>
                                    </button>

                                    <!-- Kebab Dropdown Content -->
                                    <div id="menu-{{ $payment['id'] ?? $payment['payment_id'] }}" 
                                         class="hidden absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-30 text-left">
                                        <button type="button" 
                                                data-id="{{ $payment['payment_id'] }}"
                                                onclick="viewPaymentById(this.dataset.id)"
                                                class="w-full px-3 py-1.5 text-xs text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center space-x-2">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            <span>View Details</span>
                                        </button>
                                        
                                        <button type="button" 
                                                data-id="{{ $payment['payment_id'] }}"
                                                onclick="openInvoiceById(this.dataset.id)"
                                                class="w-full px-3 py-1.5 text-xs text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 flex items-center space-x-2">
                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                            <span>Invoice / Receipt</span>
                                        </button>

                                        @if(strtoupper($payment['status'] ?? '') === 'COMPLETED')
                                        <form method="POST" action="{{ route('payment.refund', $payment['payment_id']) }}" onsubmit="return confirm('Process refund for this payment?');">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-1.5 text-xs text-purple-700 hover:bg-purple-50 flex items-center space-x-2">
                                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                                <span>Process Refund</span>
                                            </button>
                                        </form>
                                        @endif

                                        <form method="POST" action="{{ route('payment.destroy', $payment['payment_id']) }}" onsubmit="return confirm('Delete this payment transaction?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 flex items-center space-x-2 border-t border-gray-50 mt-1">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                <span>Delete Record</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <svg class="w-10 h-10 text-gray-300 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                                <p class="text-sm font-semibold">No payment records found.</p>
                                <p class="text-xs text-gray-400">Try adjusting your search criteria or record a new payment.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Pagination Footer (Matching Screenshot 1) -->
        <div class="p-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
            <div>
                Showing <span class="font-bold text-gray-800">{{ $pagination['from'] ?? 1 }} - {{ $pagination['to'] ?? count($payments) }}</span> of <span class="font-bold text-gray-800">{{ $pagination['total_items'] ?? count($payments) }}</span> payments
            </div>

            <div class="flex items-center space-x-1.5">
                <!-- Previous Button -->
                @if(($pagination['current_page'] ?? 1) > 1)
                    <a href="{{ route('payment.index', array_merge(request()->all(), ['page' => ($pagination['current_page'] - 1)])) }}" 
                       class="w-7 h-7 rounded-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition cursor-pointer">
                        &lt;
                    </a>
                @else
                    <span class="w-7 h-7 rounded-lg border border-gray-100 flex items-center justify-center text-gray-300 cursor-not-allowed">
                        &lt;
                    </span>
                @endif

                <!-- Page 1 -->
                <a href="{{ route('payment.index', array_merge(request()->all(), ['page' => 1])) }}" 
                   class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs transition {{ ($pagination['current_page'] ?? 1) == 1 ? 'bg-[#2563EB] text-white' : 'border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                    1
                </a>

                <!-- Page 2 -->
                @if(($pagination['total_pages'] ?? 1) >= 2)
                <a href="{{ route('payment.index', array_merge(request()->all(), ['page' => 2])) }}" 
                   class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs transition {{ ($pagination['current_page'] ?? 1) == 2 ? 'bg-[#2563EB] text-white' : 'border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                    2
                </a>
                @endif

                <!-- Next Button -->
                @if(($pagination['current_page'] ?? 1) < ($pagination['total_pages'] ?? 1))
                    <a href="{{ route('payment.index', array_merge(request()->all(), ['page' => ($pagination['current_page'] + 1)])) }}" 
                       class="w-7 h-7 rounded-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition cursor-pointer">
                        &gt;
                    </a>
                @else
                    <span class="w-7 h-7 rounded-lg border border-gray-100 flex items-center justify-center text-gray-300 cursor-not-allowed">
                        &gt;
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- 4. Bottom Section: Breakdown & Status Donut Chart (Matching Screenshot 1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Payment Method Breakdown (2 columns width) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200/90 p-5 shadow-2xs flex flex-col justify-between">
            <div>
                <!-- Card Header -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Payment Method Breakdown</h3>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Gross settlement split across verified payment channels.</p>
                    </div>
                    <span class="bg-[#EFF6FF] text-[#2563EB] text-xs font-bold px-3 py-1 rounded-lg">
                        Total: {{ $kpis['total_payments'] ?? '$0.00' }}
                    </span>
                </div>

                <!-- 4 Method Items Grid (2x2) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
                    
                    <!-- 1. Cash -->
                    <div class="bg-gray-50/70 rounded-xl p-4 border border-gray-100/80">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                                        <circle cx="12" cy="12" r="2"></circle>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">Cash</h4>
                                    <p class="text-[10px] text-gray-400">{{ $methodBreakdown['cash']['orders_text'] ?? '0 orders processed' }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-emerald-600">{{ $methodBreakdown['cash']['percentage'] ?? 0 }}%</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-base font-extrabold text-gray-900">{{ $methodBreakdown['cash']['amount'] ?? '$0.00' }}</span>
                        </div>
                        <div class="w-full bg-gray-200/60 rounded-full h-1.5 mt-2 overflow-hidden">
                            <div class="bg-[#10B981] h-1.5 rounded-full" style="<?php echo 'width: ' . ($methodBreakdown['cash']['percentage'] ?? 0) . '%;'; ?>"></div>
                        </div>
                    </div>

                    <!-- 2. Credit Card -->
                    <div class="bg-gray-50/70 rounded-xl p-4 border border-gray-100/80">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                        <line x1="1" y1="10" x2="23" y2="10"></line>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">Credit Card</h4>
                                    <p class="text-[10px] text-gray-400">{{ $methodBreakdown['credit_card']['orders_text'] ?? '0 orders processed' }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-blue-600">{{ $methodBreakdown['credit_card']['percentage'] ?? 0 }}%</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-base font-extrabold text-gray-900">{{ $methodBreakdown['credit_card']['amount'] ?? '$0.00' }}</span>
                        </div>
                        <div class="w-full bg-gray-200/60 rounded-full h-1.5 mt-2 overflow-hidden">
                            <div class="bg-[#2563EB] h-1.5 rounded-full" style="<?php echo 'width: ' . ($methodBreakdown['credit_card']['percentage'] ?? 0) . '%;'; ?>"></div>
                        </div>
                    </div>

                    <!-- 3. Bank Transfer -->
                    <div class="bg-gray-50/70 rounded-xl p-4 border border-gray-100/80">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path d="M3 21h18M3 10h18M5 10v11M9 10v11M15 10v11M19 10v11M12 2L2 7h20L12 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">Bank Transfer</h4>
                                    <p class="text-[10px] text-gray-400">{{ $methodBreakdown['bank_transfer']['orders_text'] ?? '0 transactions' }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-purple-600">{{ $methodBreakdown['bank_transfer']['percentage'] ?? 0 }}%</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-base font-extrabold text-gray-900">{{ $methodBreakdown['bank_transfer']['amount'] ?? '$0.00' }}</span>
                        </div>
                        <div class="w-full bg-gray-200/60 rounded-full h-1.5 mt-2 overflow-hidden">
                            <div class="bg-[#8B5CF6] h-1.5 rounded-full" style="<?php echo 'width: ' . ($methodBreakdown['bank_transfer']['percentage'] ?? 0) . '%;'; ?>"></div>
                        </div>
                    </div>

                    <!-- 4. Mobile Payment -->
                    <div class="bg-gray-50/70 rounded-xl p-4 border border-gray-100/80">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">Mobile Payment</h4>
                                    <p class="text-[10px] text-gray-400">{{ $methodBreakdown['mobile_payment']['orders_text'] ?? '0 transactions' }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-amber-500">{{ $methodBreakdown['mobile_payment']['percentage'] ?? 0 }}%</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-base font-extrabold text-gray-900">{{ $methodBreakdown['mobile_payment']['amount'] ?? '$0.00' }}</span>
                        </div>
                        <div class="w-full bg-gray-200/60 rounded-full h-1.5 mt-2 overflow-hidden">
                            <div class="bg-[#F59E0B] h-1.5 rounded-full" style="<?php echo 'width: ' . ($methodBreakdown['mobile_payment']['percentage'] ?? 0) . '%;'; ?>"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Sync Row -->
            <div class="pt-5 mt-5 border-t border-gray-100 flex items-center justify-between text-xs">
                <div class="flex items-center space-x-2 text-gray-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>POS Terminal &amp; Gateway auto-synchronized</span>
                </div>
                <button type="button" onclick="alert('Account reconciliation verified: All channels in sync with settlement ledger.')" class="text-[#2563EB] font-semibold hover:underline cursor-pointer">
                    Reconcile All Accounts
                </button>
            </div>
        </div>

        <!-- Right: Payment Status Donut Chart (1 column width) -->
        <div class="bg-white rounded-2xl border border-gray-200/90 p-5 shadow-2xs flex flex-col justify-between">
            <div>
                <!-- Header -->
                <div class="flex items-center justify-between pb-3">
                    <h3 class="text-base font-bold text-gray-900 tracking-tight">Payment Status</h3>
                    <div class="text-gray-400">
                        <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                            <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Donut Chart Container with Center Text -->
                <div class="relative w-48 h-48 mx-auto my-3 flex items-center justify-center">
                    <canvas id="paymentStatusChart" 
                            data-completed="{{ (int)($statusChart['completed_count'] ?? 0) }}"
                            data-pending="{{ (int)($statusChart['pending_count'] ?? 0) }}"
                            data-refunded="{{ (int)($statusChart['refunded_count'] ?? 0) }}"
                            data-total="{{ (int)($statusChart['total'] ?? 0) }}"
                            class="w-full h-full"></canvas>
                    
                    <!-- Center Overlay Text -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-extrabold text-gray-900 leading-tight">{{ $statusChart['total'] ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">TOTAL INVOICES</span>
                    </div>
                </div>

                <!-- Legend List matching Screenshot 1 -->
                <div class="space-y-2.5 mt-4 pt-2 border-t border-gray-100 text-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></span>
                            <span class="text-gray-700 font-medium">Completed</span>
                        </div>
                        <span class="font-bold text-gray-900">{{ $statusChart['completed_count'] ?? 0 }} <span class="text-gray-400 font-normal">({{ $statusChart['completed_pct'] ?? 0 }}%)</span></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#F59E0B]"></span>
                            <span class="text-gray-700 font-medium">Pending</span>
                        </div>
                        <span class="font-bold text-gray-900">{{ $statusChart['pending_count'] ?? 0 }} <span class="text-gray-400 font-normal">({{ $statusChart['pending_pct'] ?? 0 }}%)</span></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8B5CF6]"></span>
                            <span class="text-gray-700 font-medium">Refunded</span>
                        </div>
                        <span class="font-bold text-gray-900">{{ $statusChart['refunded_count'] ?? 0 }} <span class="text-gray-400 font-normal">({{ $statusChart['refunded_pct'] ?? 0 }}%)</span></span>
                    </div>
                </div>
            </div>

            <!-- Footer Link -->
            <div class="pt-4 mt-4 border-t border-gray-100 text-center">
                <a href="#paymentFilterForm" onclick="document.getElementById('statusFilterSelect').value='REFUNDED'; document.getElementById('paymentFilterForm').submit();" class="text-xs font-semibold text-[#2563EB] hover:underline inline-flex items-center space-x-1">
                    <span>View Dispute Resolutions</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

    </div>

</div>

<!-- ======================================================== -->
<!-- MODAL: ADD PAYMENT                                     -->
<!-- ======================================================== -->
<div id="addPaymentModal" onclick="if(event.target === this) closeAddPaymentModal()" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 transform transition-all relative my-auto max-h-[92vh] flex flex-col">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 shrink-0">
            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Add Payment</h3>
            <button type="button" onclick="closeAddPaymentModal()" class="text-gray-400 hover:text-gray-600 text-lg transition cursor-pointer">
                ✕
            </button>
        </div>

        <form action="{{ route('payment.record') }}" method="POST" id="addPaymentForm" class="mt-3.5 space-y-3 overflow-y-auto pr-1 flex-1">
            @csrf

            <!-- Quick Order Selector (Allows selecting existing order or entering custom) -->
            <div>
                @php 
                    $firstOrder = !empty($availableOrders) ? $availableOrders[0] : null;
                    $initOrderNo = $firstOrder['order_number'] ?? '';
                    $initCustomer = $firstOrder['customer_name'] ?? '';
                    $initAmount = isset($firstOrder['total_amount']) ? number_format($firstOrder['total_amount'], 2, '.', '') : '0.00';
                    $initDate = $firstOrder['order_date'] ?? date('Y-m-d');
                @endphp
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Select Order <span class="text-gray-400 font-normal text-[11px]">(Auto-fills Order Information)</span>
                </label>
                <select id="modalOrderSelector" onchange="onOrderSelected(this.value)" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    @if(empty($availableOrders))
                        <option value="" disabled selected>No orders available (enter manually)</option>
                    @endif
                    @foreach($availableOrders as $ord)
                        <option value="{{ $ord['order_number'] }}" 
                                data-customer="{{ $ord['customer_name'] }}"
                                data-amount="{{ number_format($ord['total_amount'], 2, '.', '') }}"
                                data-date="{{ $ord['order_date'] }}"
                                {{ $loop->first ? 'selected' : '' }}>
                            {{ $ord['order_number'] }} - {{ $ord['customer_name'] }} (${{ number_format($ord['total_amount'], 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 1. Order Information Box (Compact 4-column summary) -->
            <div class="bg-[#F0F7FF] border border-[#E0EDFD] rounded-2xl p-2.5 text-xs">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <div>
                        <span class="text-gray-500 block text-[10px] uppercase font-semibold">Order No</span>
                        <span id="displayOrderNo" class="font-bold text-gray-900">{{ $initOrderNo ?: 'None' }}</span>
                        <input type="hidden" name="order_number" id="inputOrderNo" value="{{ $initOrderNo }}">
                    </div>
                    <div>
                        <span class="text-gray-500 block text-[10px] uppercase font-semibold">Customer</span>
                        <span id="displayCustomer" class="font-bold text-gray-900 truncate block">{{ $initCustomer ?: 'None' }}</span>
                        <input type="hidden" name="customer_name" id="inputCustomerName" value="{{ $initCustomer }}">
                    </div>
                    <div>
                        <span class="text-gray-500 block text-[10px] uppercase font-semibold">Total Amount</span>
                        <span id="displayTotalAmount" class="font-bold text-blue-600">${{ $initAmount }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block text-[10px] uppercase font-semibold">Order Date</span>
                        <span id="displayOrderDate" class="font-bold text-gray-900">{{ $initDate }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Payment Details Section (Compact 2 columns per row) -->
            <div class="grid grid-cols-2 gap-3">
                <!-- Payment Method * -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <select name="payment_method" 
                            id="inputPaymentMethod"
                            required
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                        <option value="Cash" selected>Cash</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Mobile Payment">Mobile Payment</option>
                    </select>
                </div>

                <!-- Amount * -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Amount ($) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="amount" 
                           id="inputAmount"
                           value="{{ $initAmount }}" 
                           required 
                           placeholder="0.00" 
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-blue-600 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- Payment Status * -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Payment Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" 
                            id="inputPaymentStatus"
                            required
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                        <option value="COMPLETED" selected>Completed</option>
                        <option value="PENDING">Pending</option>
                        <option value="REFUNDED">Refunded</option>
                    </select>
                </div>

                <!-- Payment Date * -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Payment Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="payment_date" 
                           id="inputPaymentDate"
                           value="{{ date('Y-m-d') }}" 
                           required 
                           class="w-full px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Transaction ID / Reference No. -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Transaction ID / Reference No.
                </label>
                <input type="text" 
                       name="reference_no" 
                       id="inputReferenceNo"
                       placeholder="Enter transaction ID / reference no." 
                       class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Notes (Optional) with counter -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-semibold text-gray-700">Notes <span class="text-gray-400 font-normal text-[11px]">(Optional)</span></label>
                    <span class="text-[10px] text-gray-400"><span id="noteCharCounter">0</span>/500</span>
                </div>
                <textarea name="notes" 
                          id="inputNotes"
                          rows="1.5" 
                          maxlength="500" 
                          oninput="updateNoteCounter(this)"
                          placeholder="Enter notes..." 
                          class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>

            <!-- Footer Buttons: Cancel & Save Payment -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end space-x-2 shrink-0">
                <button type="button" 
                        onclick="closeAddPaymentModal()"
                        class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 bg-[#2042E3] hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center space-x-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>Save Payment</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: VIEW PAYMENT DETAILS -->
<!-- ======================================================== -->
<div id="viewPaymentModal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-4 backdrop-blur-xs">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        
        <div class="p-6 pb-4 flex items-center justify-between border-b border-gray-100">
            <div class="flex items-center space-x-2">
                <h3 class="text-xl font-bold text-gray-900 tracking-tight" id="viewModalTitle">Payment Details</h3>
                <span id="viewStatusBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase"></span>
            </div>
            <button type="button" onclick="closeViewPaymentModal()" class="text-gray-400 hover:text-gray-600 transition p-1">
                <svg class="w-5 h-5 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="p-6 space-y-4 text-sm">
            <div class="bg-blue-50/50 rounded-2xl p-4 border border-blue-100/70 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium">Payment ID</p>
                    <p class="text-lg font-bold text-blue-700" id="viewPaymentId">PAY-001</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 font-medium">Total Paid</p>
                    <p class="text-2xl font-black text-gray-900" id="viewPaymentAmount">$0.00</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-xl">
                    <p class="text-[11px] text-gray-400 uppercase font-semibold">Order Number</p>
                    <p class="font-bold text-gray-900 mt-0.5" id="viewOrderNumber">-</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-xl">
                    <p class="text-[11px] text-gray-400 uppercase font-semibold">Payment Method</p>
                    <p class="font-bold text-gray-900 mt-0.5" id="viewPaymentMethod">-</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-xl">
                    <p class="text-[11px] text-gray-400 uppercase font-semibold">Customer</p>
                    <p class="font-bold text-gray-900 mt-0.5" id="viewCustomerName">-</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-xl">
                    <p class="text-[11px] text-gray-400 uppercase font-semibold">Payment Date</p>
                    <p class="font-mono text-xs text-gray-700 mt-0.5" id="viewPaymentDate">-</p>
                </div>
            </div>

            <div class="p-3 bg-gray-50 rounded-xl">
                <p class="text-[11px] text-gray-400 uppercase font-semibold">Reference / Transaction ID</p>
                <p class="font-mono text-xs text-gray-800 mt-0.5" id="viewReferenceNo">-</p>
            </div>

            <div class="p-3 bg-gray-50 rounded-xl">
                <p class="text-[11px] text-gray-400 uppercase font-semibold">Notes</p>
                <p class="text-xs text-gray-700 mt-0.5" id="viewNotes">-</p>
            </div>
        </div>

        <div class="p-4 bg-gray-50/70 border-t border-gray-100 flex items-center justify-end space-x-3">
            <button type="button" onclick="closeViewPaymentModal()" class="px-5 py-2 rounded-xl bg-gray-200 text-gray-700 font-semibold text-xs hover:bg-gray-300 transition">
                Close
            </button>
            <button type="button" id="viewPrintInvoiceBtn" class="px-5 py-2 rounded-xl bg-[#2042E3] text-white font-semibold text-xs hover:bg-[#1A38C2] transition flex items-center space-x-1.5">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Print Invoice</span>
            </button>
        </div>

    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: GENERATE INVOICE / RECEIPT -->
<!-- ======================================================== -->
<div id="invoiceModal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-4 backdrop-blur-xs overflow-y-auto">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden my-8 animate-in fade-in zoom-in-95 duration-150">
        
        <div class="p-6 pb-4 flex items-center justify-between border-b border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Invoice Receipt</h3>
            <button type="button" onclick="closeInvoiceModal()" class="text-gray-400 hover:text-gray-600 transition p-1">
                <svg class="w-5 h-5 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Printable Invoice Content -->
        <div id="printableInvoiceArea" class="p-6 space-y-5 text-sm bg-white">
            
            <!-- Shop Branding -->
            <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg">
                        L
                    </div>
                    <div>
                        <h4 class="font-extrabold text-gray-900 text-base">Laundry 6 Plus</h4>
                        <p class="text-[11px] text-gray-500">Premium Fabric Care &amp; Dry Cleaning</p>
                    </div>
                </div>
                <div class="text-right">
                    <span id="invoiceBadgeStatus" class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full uppercase bg-emerald-100 text-emerald-700">PAID</span>
                    <p class="text-xs font-mono text-gray-500 mt-1" id="invoiceNumber">INV-00482</p>
                </div>
            </div>

            <!-- Invoice Metadata Grid -->
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <p class="text-gray-400 font-semibold uppercase">Billed To</p>
                    <p class="font-bold text-gray-900 text-sm mt-0.5" id="invCustomerName">Va Sovan</p>
                    <p class="text-gray-500" id="invCustomerAddress">Siem Reap, Cambodia</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-400 font-semibold uppercase">Payment Details</p>
                    <p class="text-gray-700 mt-0.5">Order: <span class="font-bold text-gray-900" id="invOrderNo">ORD-0010</span></p>
                    <p class="text-gray-700">Method: <span class="font-bold text-gray-900" id="invMethod">Cash</span></p>
                    <p class="text-gray-500 font-mono" id="invDate">2025-09-09</p>
                </div>
            </div>

            <!-- Itemized Table -->
            <div class="border border-gray-200 rounded-xl overflow-hidden mt-3">
                <table class="w-full text-xs text-left">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold uppercase">
                        <tr>
                            <th class="py-2.5 px-3">Description</th>
                            <th class="py-2.5 px-3 text-center">Qty</th>
                            <th class="py-2.5 px-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-2.5 px-3 font-medium text-gray-800" id="invItemDesc">Professional Laundry Wash &amp; Care</td>
                            <td class="py-2.5 px-3 text-center text-gray-600">1</td>
                            <td class="py-2.5 px-3 text-right font-bold text-gray-900" id="invItemPrice">$20.00</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 font-medium text-gray-800">Eco-friendly Detergent &amp; Fabric Softener</td>
                            <td class="py-2.5 px-3 text-center text-gray-600">1</td>
                            <td class="py-2.5 px-3 text-right font-bold text-emerald-600">Included</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold border-t border-gray-200">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 text-right text-gray-600">Total Amount:</td>
                            <td class="py-2.5 px-3 text-right text-base text-gray-900" id="invTotalAmount">$20.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="p-3 bg-gray-50 rounded-xl text-center text-[11px] text-gray-400">
                Thank you for choosing Laundry 6 Plus! Please retain this invoice for your laundry records.
            </div>

        </div>

        <!-- Footer Actions -->
        <div class="p-4 bg-gray-50/70 border-t border-gray-100 flex items-center justify-end space-x-3">
            <button type="button" onclick="closeInvoiceModal()" class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition">
                Close
            </button>
            <button type="button" onclick="printInvoiceNow()" class="px-5 py-2.5 rounded-xl bg-[#2042E3] hover:bg-[#1A38C2] text-white font-semibold text-xs flex items-center space-x-2 transition">
                <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Print Invoice</span>
            </button>
        </div>

    </div>
</div>

<!-- ======================================================== -->
<!-- SCRIPTS & CLIENT-SIDE LOGIC -->
<!-- ======================================================== -->
<script>
    // Initialize Lucide Icons
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        initPaymentStatusChart();
    });

    // Handle Order Selection inside Add Payment Modal
    function onOrderSelected(orderNo) {
        const select = document.getElementById('modalOrderSelector');
        const selectedOpt = select.options[select.selectedIndex];
        
        if (!selectedOpt) return;

        const customer = selectedOpt.getAttribute('data-customer') || 'Customer';
        const amount = selectedOpt.getAttribute('data-amount') || '0.00';
        const todayStr = new Date().toISOString().split('T')[0];
        const date = selectedOpt.getAttribute('data-date') || todayStr;

        document.getElementById('displayOrderNo').textContent = orderNo;
        document.getElementById('inputOrderNo').value = orderNo;

        document.getElementById('displayCustomer').textContent = customer;
        document.getElementById('inputCustomerName').value = customer;

        document.getElementById('displayTotalAmount').textContent = '$' + parseFloat(amount).toFixed(2);
        document.getElementById('inputAmount').value = parseFloat(amount).toFixed(2);

        document.getElementById('displayOrderDate').textContent = date;
        document.getElementById('inputPaymentDate').value = todayStr;
    }

    // Modal Control: Add Payment
    function openAddPaymentModal() {
        const modal = document.getElementById('addPaymentModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        const selector = document.getElementById('modalOrderSelector');
        if (selector && selector.options.length > 0) {
            if (!selector.value || selector.selectedIndex < 0) {
                selector.selectedIndex = 0;
            }
            onOrderSelected(selector.value);
        }
    }

    function closeAddPaymentModal() {
        const modal = document.getElementById('addPaymentModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    // Character Counter for Notes
    function updateNoteCounter(textarea) {
        const count = textarea.value.length;
        document.getElementById('noteCharCounter').textContent = count;
    }

    // Toggle Kebab Row Menu
    function toggleRowMenu(menuId, event) {
        event.stopPropagation();
        const menu = document.getElementById(menuId);
        const allMenus = document.querySelectorAll('[id^="menu-"]');
        allMenus.forEach(m => {
            if (m.id !== menuId) m.classList.add('hidden');
        });
        menu.classList.toggle('hidden');
    }

    document.addEventListener('click', function() {
        const allMenus = document.querySelectorAll('[id^="menu-"]');
        allMenus.forEach(m => m.classList.add('hidden'));
    });
</script>

<script type="application/json" id="paymentsJsonData">
{!! json_encode($payments ?? []) !!}
</script>

<script>
    // Cache payments list for quick ID lookup
    const paymentsJsonEl = document.getElementById('paymentsJsonData');
    const allPaymentsList = paymentsJsonEl ? JSON.parse(paymentsJsonEl.textContent || '[]') : [];

    function viewPaymentById(paymentId) {
        let payment = (Array.isArray(allPaymentsList) ? allPaymentsList : Object.values(allPaymentsList)).find(p => p.payment_id == paymentId);
        if (!payment) {
            payment = {
                id: 1,
                payment_id: paymentId,
                order_number: 'ORD-' + String(paymentId).replace(/\D/g, ''),
                customer_name: 'Customer',
                amount: 0.00,
                payment_method: 'Cash',
                status: 'COMPLETED',
                payment_date: new Date().toLocaleString(),
                reference_no: 'TRX-' + Math.floor(10000 + Math.random() * 90000),
                notes: 'Recorded transaction'
            };
        }
        viewPaymentDetails(payment);
    }

    function openInvoiceById(paymentId) {
        let payment = (Array.isArray(allPaymentsList) ? allPaymentsList : Object.values(allPaymentsList)).find(p => p.payment_id == paymentId);
        if (!payment) {
            payment = {
                id: 1,
                payment_id: paymentId,
                order_number: 'ORD-' + String(paymentId).replace(/\D/g, ''),
                customer_name: 'Customer',
                amount: 0.00,
                payment_method: 'Cash',
                status: 'COMPLETED',
                payment_date: new Date().toLocaleString(),
                reference_no: 'TRX-' + Math.floor(10000 + Math.random() * 90000),
                notes: 'Recorded transaction'
            };
        }
        openInvoiceModal(payment);
    }

    // View Payment Details Modal
    let currentViewingPayment = null;

    function viewPaymentDetails(payment) {
        if (typeof payment === 'string') {
            return viewPaymentById(payment);
        }
        currentViewingPayment = payment;

        document.getElementById('viewPaymentId').textContent = payment.payment_id;
        document.getElementById('viewPaymentAmount').textContent = '$' + parseFloat(payment.amount).toFixed(2);
        document.getElementById('viewOrderNumber').textContent = payment.order_number;
        document.getElementById('viewCustomerName').textContent = payment.customer_name;
        document.getElementById('viewPaymentMethod').textContent = payment.payment_method;
        document.getElementById('viewPaymentDate').textContent = payment.payment_date;
        document.getElementById('viewReferenceNo').textContent = payment.reference_no || 'N/A';
        document.getElementById('viewNotes').textContent = payment.notes || 'None';

        const statusBadge = document.getElementById('viewStatusBadge');
        statusBadge.textContent = payment.status;
        if (payment.status === 'COMPLETED') {
            statusBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full uppercase bg-emerald-100 text-emerald-700';
        } else if (payment.status === 'PENDING') {
            statusBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full uppercase bg-amber-100 text-amber-700';
        } else {
            statusBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full uppercase bg-purple-100 text-purple-700';
        }

        document.getElementById('viewPrintInvoiceBtn').onclick = function() {
            closeViewPaymentModal();
            openInvoiceModal(payment);
        };

        const viewModal = document.getElementById('viewPaymentModal');
        viewModal.classList.remove('hidden');
        viewModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeViewPaymentModal() {
        const viewModal = document.getElementById('viewPaymentModal');
        viewModal.classList.add('hidden');
        viewModal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    // Invoice Modal
    function openInvoiceModal(payment) {
        if (!payment) {
            payment = {
                id: 10,
                payment_id: 'PAY-010',
                order_number: 'ORD-0010',
                customer_name: 'Va Sovan',
                amount: 20.00,
                payment_method: 'Mobile Payment',
                status: 'COMPLETED',
                payment_date: '2025-09-09 11:35 AM'
            };
        }

        document.getElementById('invoiceNumber').textContent = 'INV-' + String(payment.id || 1).padStart(5, '0');
        document.getElementById('invCustomerName').textContent = payment.customer_name;
        document.getElementById('invOrderNo').textContent = payment.order_number;
        document.getElementById('invMethod').textContent = payment.payment_method;
        document.getElementById('invDate').textContent = payment.payment_date;
        document.getElementById('invTotalAmount').textContent = '$' + parseFloat(payment.amount).toFixed(2);
        document.getElementById('invItemPrice').textContent = '$' + parseFloat(payment.amount).toFixed(2);

        const badge = document.getElementById('invoiceBadgeStatus');
        badge.textContent = payment.status;
        if (payment.status === 'COMPLETED') {
            badge.className = 'inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full uppercase bg-emerald-100 text-emerald-700';
        } else if (payment.status === 'PENDING') {
            badge.className = 'inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full uppercase bg-amber-100 text-amber-700';
        } else {
            badge.className = 'inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full uppercase bg-purple-100 text-purple-700';
        }

        const invModal = document.getElementById('invoiceModal');
        invModal.classList.remove('hidden');
        invModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeInvoiceModal() {
        const invModal = document.getElementById('invoiceModal');
        invModal.classList.add('hidden');
        invModal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function printInvoiceNow() {
        const printContents = document.getElementById('printableInvoiceArea').innerHTML;
        const originalContents = document.body.innerHTML;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Invoice Receipt - Laundry 6 Plus</title>
                    <script src="https://cdn.tailwindcss.com"><\/script>
                    <style>
                        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 24px; }
                        @media  print { body { padding: 0; } }
                    </style>
                </head>
                <body onload="window.print(); window.close();">
                    <div style="max-width: 600px; margin: 0 auto;">
                        ${printContents}
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
    }

    // Chart.js Donut Chart for Payment Status (Dynamic Database Match)
    function initPaymentStatusChart() {
        const canvas = document.getElementById('paymentStatusChart');
        if (!canvas) return;

        const completedCount = parseInt(canvas.dataset.completed || '0', 10);
        const pendingCount = parseInt(canvas.dataset.pending || '0', 10);
        const refundedCount = parseInt(canvas.dataset.refunded || '0', 10);
        const totalCount = parseInt(canvas.dataset.total || '0', 10);

        const chartData = (totalCount > 0) ? [completedCount, pendingCount, refundedCount] : [0, 0, 0];

        // Colors matching: Emerald Green (Completed), Amber (Pending), Purple (Refunded)
        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending', 'Refunded'],
                datasets: [{
                    data: chartData,
                    backgroundColor: [
                        '#10B981', // Emerald
                        '#F59E0B', // Amber
                        '#8B5CF6'  // Purple
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '76%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.parsed;
                                const total = totalCount > 0 ? totalCount : 1;
                                const pct = Math.round((val / total) * 100);
                                return ` ${context.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection