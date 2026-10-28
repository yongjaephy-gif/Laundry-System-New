@extends('layouts.navbar')

@section('title', 'Reports')

@section('content')
<div class="space-y-7 w-full mx-auto">

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

    <!-- SECTION 1: GENERATE REPORTS (Matching media_1791126441199.png) -->
    <div class="space-y-4">
        <!-- Section Header with 3 Export Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight">Generate Reports</h2>

            <div class="flex items-center space-x-2.5">
                <!-- 1. Print Button -->
                <button
                    type="button"
                    onclick="handleGlobalExport('print')"
                    class="bg-[#4F46E5] hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center space-x-1.5 transition shadow-xs cursor-pointer active:scale-95"
                    title="Print Report">
                    <i class="fa-solid fa-print text-xs"></i>
                    <span>Print</span>
                </button>

                <!-- 2. PDF Button -->
                <button
                    type="button"
                    onclick="handleGlobalExport('pdf')"
                    class="bg-[#EF4444] hover:bg-rose-600 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center space-x-1.5 transition shadow-xs cursor-pointer active:scale-95"
                    title="Export as PDF">
                    <i class="fa-solid fa-file-pdf text-xs"></i>
                    <span>PDF</span>
                </button>

                <!-- 3. Excel Button -->
                <button
                    type="button"
                    onclick="handleGlobalExport('excel')"
                    class="bg-[#10B981] hover:bg-emerald-600 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center space-x-1.5 transition shadow-xs cursor-pointer active:scale-95"
                    title="Export as Excel">
                    <i class="fa-solid fa-file-excel text-xs"></i>
                    <span>Excel</span>
                </button>
            </div>
        </div>

        <!-- 10 REPORT CARDS GRID (5 columns on desktop matching teacher screenshot) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 sm:gap-4">
            <!-- 1. Customer Report -->
            <div onclick="openGenerateModal('Customer Report', 'Customer', 'View customer information and statistics')"
                class="bg-white border border-gray-100/90 rounded-2xl p-4 sm:p-4.5 flex flex-col justify-between shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer group min-h-26">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-users text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">Customer Report</h3>
                        <p class="text-[11px] text-gray-400 mt-1 leading-snug line-clamp-2">View customer information and statistics</p>
                    </div>
                </div>
                <div class="text-right mt-1">
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-blue-500 transition-colors"></i>
                </div>
            </div>

            <!-- 2. Laundry Order Report -->
            <div onclick="openGenerateModal('Laundry Order Report', 'Order', 'View all laundry orders and status summary')"
                class="bg-white border border-gray-100/90 rounded-2xl p-4 sm:p-4.5 flex flex-col justify-between shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer group min-h-26">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <i class="fa-regular fa-clipboard-check text-base"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 leading-tight group-hover:text-emerald-600 transition-colors">Laundry Order Report</h3>
                        <p class="text-[11px] text-gray-400 mt-1 leading-snug line-clamp-2">View all laundry orders and status summary</p>
                    </div>
                </div>
                <div class="text-right mt-1">
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-emerald-500 transition-colors"></i>
                </div>
            </div>

            <!-- 3. Daily Sales Report -->
            <div onclick="openGenerateModal('Daily Sales Report', 'Sales', 'View daily sales summary and total revenue')"
                class="bg-white border border-gray-100/90 rounded-2xl p-4 sm:p-4.5 flex flex-col justify-between shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer group min-h-26">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-receipt text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 leading-tight group-hover:text-purple-600 transition-colors">Daily Sales Report</h3>
                        <p class="text-[11px] text-gray-400 mt-1 leading-snug line-clamp-2">View daily sales summary and total revenue</p>
                    </div>
                </div>
                <div class="text-right mt-1">
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-purple-500 transition-colors"></i>
                </div>
            </div>

            <!-- 4. Monthly Sales Report -->
            <div onclick="openGenerateModal('Monthly Sales Report', 'Sales', 'View monthly sales summary and revenue analysis')"
                class="bg-white border border-gray-100/90 rounded-2xl p-4 sm:p-4.5 flex flex-col justify-between shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer group min-h-26">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <i class="fa-regular fa-calendar-days text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 leading-tight group-hover:text-amber-600 transition-colors">Monthly Sales Report</h3>
                        <p class="text-[11px] text-gray-400 mt-1 leading-snug line-clamp-2">View monthly sales summary and...</p>
                    </div>
                </div>
                <div class="text-right mt-1">
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-amber-500 transition-colors"></i>
                </div>
            </div>

            <!-- 5. Payment Report -->
            <div onclick="openGenerateModal('Payment Report', 'Payment', 'View all payments and transaction history')"
                class="bg-white border border-gray-100/90 rounded-2xl p-4 sm:p-4.5 flex flex-col justify-between shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer group min-h-26">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 group-hover:bg-teal-600 group-hover:text-white transition-colors">
                        <i class="fa-regular fa-credit-card text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 leading-tight group-hover:text-teal-600 transition-colors">Payment Report</h3>
                        <p class="text-[11px] text-gray-400 mt-1 leading-snug line-clamp-2">View all payments and transaction history</p>
                    </div>
                </div>
                <div class="text-right mt-1">
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-teal-500 transition-colors"></i>
                </div>
            </div>

            <!-- 6. Employee Report -->
            <div onclick="openGenerateModal('Employee Report', 'Employee', 'View employee information and attendance')"
                class="bg-white border border-gray-100/90 rounded-2xl p-4 sm:p-4.5 flex flex-col justify-between shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer group min-h-26">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <i class="fa-regular fa-user text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 leading-tight group-hover:text-indigo-600 transition-colors">Employee Report</h3>
                        <p class="text-[11px] text-gray-400 mt-1 leading-snug line-clamp-2">View employee information and...</p>
                    </div>
                </div>
                <div class="text-right mt-1">
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-indigo-500 transition-colors"></i>
                </div>
            </div>

            <!-- 7. Inventory Report -->
            <div onclick="openGenerateModal('Inventory Report', 'Inventory', 'View inventory levels and stock summary')"
                class="bg-white border border-gray-100/90 rounded-2xl p-4 sm:p-4.5 flex flex-col justify-between shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer group min-h-26">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-box text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 leading-tight group-hover:text-orange-600 transition-colors">Inventory Report</h3>
                        <p class="text-[11px] text-gray-400 mt-1 leading-snug line-clamp-2">View inventory levels and stock summary</p>
                    </div>
                </div>
                <div class="text-right mt-1">
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-orange-500 transition-colors"></i>
                </div>
            </div>

            <!-- 8. Machine Usage Report -->
            <div onclick="openGenerateModal('Machine Usage Report', 'Machine', 'View machine usage and performance')"
                class="bg-white border border-gray-100/90 rounded-2xl p-4 sm:p-4.5 flex flex-col justify-between shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer group min-h-26">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 group-hover:bg-sky-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-gear text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 leading-tight group-hover:text-sky-600 transition-colors">Machine Usage Repor</h3>
                        <p class="text-[11px] text-gray-400 mt-1 leading-snug line-clamp-2">View machine usage and performance</p>
                    </div>
                </div>
                <div class="text-right mt-1">
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-sky-500 transition-colors"></i>
                </div>
            </div>

            <!-- 9. Delivery Report -->
            <div onclick="openGenerateModal('Delivery Report', 'Delivery', 'View delivery activities and performance')"
                class="bg-white border border-gray-100/90 rounded-2xl p-4 sm:p-4.5 flex flex-col justify-between shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer group min-h-26">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shrink-0 group-hover:bg-green-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-truck-fast text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 leading-tight group-hover:text-green-600 transition-colors">Delivery Report</h3>
                        <p class="text-[11px] text-gray-400 mt-1 leading-snug line-clamp-2">View delivery activities and performance</p>
                    </div>
                </div>
                <div class="text-right mt-1">
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-green-500 transition-colors"></i>
                </div>
            </div>

            <!-- 10. Service Popularity Report -->
            <div onclick="openGenerateModal('Service Popularity Report', 'Service', 'View popular services and trends')"
                class="bg-white border border-gray-100/90 rounded-2xl p-4 sm:p-4.5 flex flex-col justify-between shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer group min-h-26">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-chart-line text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 leading-tight group-hover:text-rose-600 transition-colors">Service Popularity Re</h3>
                        <p class="text-[11px] text-gray-400 mt-1 leading-snug line-clamp-2">View popular services and trends</p>
                    </div>
                </div>
                <div class="text-right mt-1">
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-rose-500 transition-colors"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: RECENT REPORTS TABLE (Matching media_1791126441199.png) -->
    <div class="space-y-3.5">
        <h3 class="text-base font-bold text-gray-900">Recent Reports</h3>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.03)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="reportsTable">
                    <thead>
                        <tr class="bg-[#F4F6FD] border-b border-gray-100 text-[12px] font-semibold text-gray-500">
                            <th class="py-3.5 px-6 text-left">Report Name</th>
                            <th class="py-3.5 px-6 text-left">Type</th>
                            <th class="py-3.5 px-6 text-left">Date Range</th>
                            <th class="py-3.5 px-6 text-left">Generated By</th>
                            <th class="py-3.5 px-6 text-left">Generated On</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm" id="reportsTableBody">
                        @forelse($reports as $r)
                        @php
                        $typeBadgeClass = 'bg-emerald-50 text-emerald-600';
                        if (strtolower($r->type) === 'order') {
                        $typeBadgeClass = 'bg-blue-50 text-blue-600';
                        } elseif (strtolower($r->type) === 'payment') {
                        $typeBadgeClass = 'bg-teal-50 text-teal-600';
                        } elseif (strtolower($r->type) === 'inventory') {
                        $typeBadgeClass = 'bg-amber-50 text-amber-600';
                        } elseif (strtolower($r->type) === 'employee' || strtolower($r->type) === 'staff') {
                        $typeBadgeClass = 'bg-indigo-50 text-indigo-600';
                        } elseif (strtolower($r->type) === 'machine') {
                        $typeBadgeClass = 'bg-sky-50 text-sky-600';
                        } elseif (strtolower($r->type) === 'delivery') {
                        $typeBadgeClass = 'bg-green-50 text-green-600';
                        } elseif (strtolower($r->type) === 'service') {
                        $typeBadgeClass = 'bg-rose-50 text-rose-600';
                        }
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <!-- Report Name -->
                            <td class="py-4 px-6 font-semibold text-gray-900 whitespace-nowrap">
                                {{ $r->name }}
                            </td>

                            <!-- Type -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="{{ $typeBadgeClass }} px-3 py-0.5 rounded-full text-xs font-semibold inline-block">
                                    {{ $r->type }}
                                </span>
                            </td>

                            <!-- Date Range -->
                            <td class="py-4 px-6 text-xs text-gray-600 whitespace-nowrap font-medium">
                                {{ $r->date_range }}
                            </td>

                            <!-- Generated By -->
                            <td class="py-4 px-6 text-xs text-gray-600 whitespace-nowrap font-medium">
                                {{ $r->generated_by }}
                            </td>

                            <!-- Generated On -->
                            <td class="py-4 px-6 text-xs text-gray-600 whitespace-nowrap font-medium">
                                {{ $r->generated_on }}
                            </td>

                            <!-- Actions: 3 Mini Square Outline Buttons Matching Screenshot -->
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- Print Button (Blue outline) -->
                                    <button
                                        type="button"
                                        data-id="{{ $r->id }}"
                                        data-name="{{ $r->name }}"
                                        onclick="handlePrintReportRow(this)"
                                        class="w-7 h-7 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                        title="Print">
                                        <i class="fa-solid fa-print text-[11px]"></i>
                                    </button>

                                    <!-- PDF Button (Red outline) -->
                                    <button
                                        type="button"
                                        data-id="{{ $r->id }}"
                                        data-name="{{ $r->name }}"
                                        data-format="pdf"
                                        onclick="handleExportReportRow(this)"
                                        class="w-7 h-7 rounded-lg border border-rose-200 text-rose-500 hover:bg-rose-50 flex items-center justify-center transition shadow-2xs cursor-pointer font-bold text-[10px]"
                                        title="Export PDF">
                                        <i class="fa-regular fa-file-pdf text-[11px]"></i>
                                    </button>

                                    <!-- Excel Button (Green outline) -->
                                    <button
                                        type="button"
                                        data-id="{{ $r->id }}"
                                        data-name="{{ $r->name }}"
                                        data-format="excel"
                                        onclick="handleExportReportRow(this)"
                                        class="w-7 h-7 rounded-lg border border-emerald-200 text-emerald-600 hover:bg-emerald-50 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                        title="Export Excel">
                                        <i class="fa-regular fa-file-lines text-[11px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-400 text-xs">
                                No reports found. Click any report above to generate.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer (Matching media_1791126441199.png: Showing 1 to 5 of 25 reports) -->
            <div class="p-4 sm:p-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
                <div>
                    Showing {{ $totalCount > 0 ? (($page - 1) * $perPage + 1) : 0 }} to {{ min($page * $perPage, $totalCount) }} of {{ $totalCount }} reports
                </div>
                <div class="flex items-center space-x-1.5">
                    <!-- Previous Button -->
                    @if($page > 1)
                    <a href="{{ route('reports.index', ['page' => $page - 1]) }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 flex items-center space-x-1 transition font-medium">
                        <span>&lsaquo; Previous</span>
                    </a>
                    @else
                    <button type="button" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-300 flex items-center space-x-1 cursor-not-allowed" disabled>
                        <span>&lsaquo; Previous</span>
                    </button>
                    @endif

                    <!-- Page Numbers (1 to 5) -->
                    @for($p = 1; $p <= $totalPages; $p++)
                        @if($p==$page)
                        <button type="button" class="w-8 h-8 rounded-lg bg-[#4F46E5] text-white font-bold flex items-center justify-center shadow-xs">
                        {{ $p }}
                        </button>
                        @else
                        <a href="{{ route('reports.index', ['page' => $p]) }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center justify-center transition font-medium">
                            {{ $p }}
                        </a>
                        @endif
                        @endfor

                        <!-- Next Button -->
                        @if($page < $totalPages)
                            <a href="{{ route('reports.index', ['page' => $page + 1]) }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 flex items-center space-x-1 transition font-medium">
                            <span>Next &rsaquo;</span>
                            </a>
                            @else
                            <button type="button" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-300 flex items-center space-x-1 cursor-not-allowed" disabled>
                                <span>Next &rsaquo;</span>
                            </button>
                            @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- GENERATE REPORT MODAL (Click card or OK) -->
<!-- ========================================== -->
<div id="generateReportModal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-7 relative max-h-[92vh] overflow-y-auto">
        <!-- Close Button -->
        <button type="button" onclick="closeGenerateModal()" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="flex items-center space-x-3 mb-5">
            <div id="modalReportIconBox" class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i id="modalReportIcon" class="fa-solid fa-chart-pie text-base"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900 leading-tight" id="modalReportTitle">Generate Report</h3>
                <p class="text-xs text-gray-400 mt-0.5" id="modalReportDesc">Choose date range and export format</p>
            </div>
        </div>

        <!-- Form -->
        <form id="generateReportForm" action="{{ route('reports.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="name" id="formReportName" value="Daily Sales Report">
            <input type="hidden" name="type" id="formReportType" value="Sales">

            <!-- 1. Report Name (Readonly display) -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Selected Report
                </label>
                <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-800" id="displayReportName">
                    Daily Sales Report
                </div>
            </div>

            <!-- 2. Date Range -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Date Range
                </label>
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <span class="text-[11px] text-gray-400 block mb-1">From:</span>
                        <input
                            type="date"
                            name="start_date"
                            id="reportStartDate"
                            value="{{ date('Y-m-01') }}"
                            class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    </div>
                    <div>
                        <span class="text-[11px] text-gray-400 block mb-1">To:</span>
                        <input
                            type="date"
                            name="end_date"
                            id="reportEndDate"
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    </div>
                </div>
                <input type="hidden" name="date_range" id="formDateRange" value="{{ date('d M Y') }}">
            </div>

            <!-- 3. Export Format Selection -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                    Export Format
                </label>
                <div class="grid grid-cols-3 gap-2.5">
                    <label class="flex flex-col items-center justify-center p-3 border-2 border-[#4F46E5] bg-indigo-50/50 rounded-xl cursor-pointer hover:bg-indigo-50 transition format-option" id="label-format-print">
                        <input type="radio" name="format" value="print" checked class="hidden" onchange="highlightFormat('print')">
                        <i class="fa-solid fa-print text-[#4F46E5] text-base mb-1"></i>
                        <span class="text-xs font-bold text-gray-800">Print</span>
                    </label>

                    <label class="flex flex-col items-center justify-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition format-option" id="label-format-pdf">
                        <input type="radio" name="format" value="pdf" class="hidden" onchange="highlightFormat('pdf')">
                        <i class="fa-solid fa-file-pdf text-[#EF4444] text-base mb-1"></i>
                        <span class="text-xs font-bold text-gray-800">PDF</span>
                    </label>

                    <label class="flex flex-col items-center justify-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition format-option" id="label-format-excel">
                        <input type="radio" name="format" value="excel" class="hidden" onchange="highlightFormat('excel')">
                        <i class="fa-solid fa-file-excel text-[#10B981] text-base mb-1"></i>
                        <span class="text-xs font-bold text-gray-800">Excel</span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-3 pt-3">
                <button
                    type="button"
                    onclick="closeGenerateModal()"
                    class="flex-1 py-3 px-5 border border-gray-200 rounded-xl text-gray-700 font-semibold text-sm hover:bg-gray-50 transition cursor-pointer text-center">
                    Cancel
                </button>
                <button
                    type="submit"
                    id="btnSubmitModal"
                    class="flex-1 py-3 px-5 bg-[#4F46E5] hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl flex items-center justify-center space-x-2 shadow-xs transition cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>OK / Generate</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT LOGIC -->
<!-- ========================================== -->
<script>
    // 1. Open Generate Modal for a specific card
    function openGenerateModal(reportName, reportType, desc) {
        document.getElementById('formReportName').value = reportName;
        document.getElementById('formReportType').value = reportType;
        document.getElementById('displayReportName').innerText = reportName;
        document.getElementById('modalReportTitle').innerText = reportName;
        document.getElementById('modalReportDesc').innerText = desc || 'Select date range and format to export';

        // Icon update
        const iconCont = document.getElementById('modalReportIconBox');
        const iconElem = document.getElementById('modalReportIcon');
        if (reportType === 'Customer') {
            iconCont.className = 'w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-solid fa-users text-base';
        } else if (reportType === 'Order') {
            iconCont.className = 'w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-regular fa-clipboard-check text-base';
        } else if (reportType === 'Sales') {
            iconCont.className = 'w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-solid fa-receipt text-base';
        } else if (reportType === 'Payment') {
            iconCont.className = 'w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-regular fa-credit-card text-base';
        } else if (reportType === 'Employee') {
            iconCont.className = 'w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-regular fa-user text-base';
        } else if (reportType === 'Inventory') {
            iconCont.className = 'w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-solid fa-box text-base';
        } else if (reportType === 'Machine') {
            iconCont.className = 'w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-solid fa-gear text-base';
        } else if (reportType === 'Delivery') {
            iconCont.className = 'w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-solid fa-truck-fast text-base';
        } else {
            iconCont.className = 'w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0';
            iconElem.className = 'fa-solid fa-chart-line text-base';
        }

        document.getElementById('generateReportModal').classList.remove('hidden');
    }

    function closeGenerateModal() {
        document.getElementById('generateReportModal').classList.add('hidden');
    }

    function highlightFormat(fmt) {
        ['print', 'pdf', 'excel'].forEach(f => {
            const el = document.getElementById('label-format-' + f);
            if (f === fmt) {
                el.className = 'flex flex-col items-center justify-center p-3 border-2 border-[#4F46E5] bg-indigo-50/50 rounded-xl cursor-pointer hover:bg-indigo-50 transition format-option';
            } else {
                el.className = 'flex flex-col items-center justify-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition format-option';
            }
        });
    }

    // 2. Global Export from Top Bar buttons (Print / PDF / Excel)
    function handleGlobalExport(format) {
        if (format === 'print') {
            window.open("{{ route('reports.print') }}", '_blank');
        } else if (format === 'pdf') {
            window.open("{{ route('reports.print') }}?format=pdf", '_blank');
        } else if (format === 'excel') {
            window.location.href = "{{ url('/reports/export/excel') }}?name=Laundry_All_Reports";
        }
    }

    // 3. Row Actions: Print, PDF, Excel for a specific report row
    function printSpecificReport(id, name) {
        window.open(`{{ route('reports.print') }}?id=${id}&name=${encodeURIComponent(name)}`, '_blank');
    }

    function exportSpecificReport(id, name, format) {
        if (format === 'pdf') {
            window.open(`{{ route('reports.print') }}?id=${id}&name=${encodeURIComponent(name)}&format=pdf`, '_blank');
        } else if (format === 'excel') {
            window.location.href = `{{ url('/reports/export/excel') }}?id=${id}&name=${encodeURIComponent(name)}`;
        }
    }

    function handlePrintReportRow(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        printSpecificReport(id, name);
    }

    function handleExportReportRow(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const format = btn.getAttribute('data-format') || 'pdf';
        exportSpecificReport(id, name, format);
    }

    // 4. Modal Form Submit Handler
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('generateReportForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const startDate = document.getElementById('reportStartDate').value;
                const endDate = document.getElementById('reportEndDate').value;
                let formattedRange = startDate;
                if (startDate && endDate && startDate !== endDate) {
                    formattedRange = `${startDate} - ${endDate}`;
                }
                document.getElementById('formDateRange').value = formattedRange;

                const selectedFormat = document.querySelector('input[name="format"]:checked').value;
                const reportName = document.getElementById('formReportName').value;

                const submitBtn = document.getElementById('btnSubmitModal');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Generating...</span>';

                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        closeGenerateModal();

                        // Immediately trigger export based on format
                        if (selectedFormat === 'excel') {
                            window.location.href = `{{ url('/reports/export/excel') }}?name=${encodeURIComponent(reportName)}`;
                        } else if (selectedFormat === 'pdf') {
                            window.open(`{{ route('reports.print') }}?name=${encodeURIComponent(reportName)}&format=pdf`, '_blank');
                        } else {
                            window.open(`{{ route('reports.print') }}?name=${encodeURIComponent(reportName)}`, '_blank');
                        }

                        // Reload table after slight delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    })
                    .catch(err => {
                        form.submit();
                    });
            });
        }

        // Close modal on click outside
        const modal = document.getElementById('generateReportModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeGenerateModal();
                }
            });
        }
    });
</script>
@endsection