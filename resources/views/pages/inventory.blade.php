@extends('layouts.navbar')

@section('title', 'Inventorys')

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

    <!-- TOP BAR: + ADD ITEM BUTTON -->
    <div class="flex justify-end items-center">
        <button type="button" 
                onclick="openAddItemModal()" 
                class="bg-[#2563EB] hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-xs transition flex items-center space-x-2 text-sm cursor-pointer active:scale-95">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Add Item</span>
        </button>
    </div>

    <!-- SUMMARY METRIC CARDS (3 CARDS EXACTLY AS SCREENSHOT) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Total Items (Soft Blue Card with 3D Blue Box) -->
        <div class="bg-[#F0F5FF] border border-blue-100/80 rounded-3xl p-6 shadow-xs flex items-center space-x-5">
            <div class="w-14 h-14 rounded-2xl bg-blue-100/70 flex items-center justify-center flex-shrink-0">
                <!-- 3D Isometric Blue Cube -->
                <svg class="w-9 h-9" viewBox="0 0 48 48" fill="none">
                    <path d="M24 4L42 14.5V33.5L24 44L6 33.5V14.5L24 4Z" fill="#3B82F6"/>
                    <path d="M24 4L42 14.5L24 25L6 14.5L24 4Z" fill="#60A5FA"/>
                    <path d="M24 25V44L6 33.5V14.5L24 25Z" fill="#2563EB"/>
                    <path d="M24 25L42 14.5V33.5L24 44V25Z" fill="#1D4ED8"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Items</p>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5 tracking-tight" id="cardTotalCount">{{ $stats['total'] ?? count($items) }}</h3>
            </div>
        </div>

        <!-- Card 2: In Stock (Soft Blush/Pink Card with 3D Green Box) -->
        <div class="bg-[#FFF1F2] border border-rose-100/80 rounded-3xl p-6 shadow-xs flex items-center space-x-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-100/70 flex items-center justify-center flex-shrink-0">
                <!-- 3D Isometric Green Cube -->
                <svg class="w-9 h-9" viewBox="0 0 48 48" fill="none">
                    <path d="M24 4L42 14.5V33.5L24 44L6 33.5V14.5L24 4Z" fill="#10B981"/>
                    <path d="M24 4L42 14.5L24 25L6 14.5L24 4Z" fill="#34D399"/>
                    <path d="M24 25V44L6 33.5V14.5L24 25Z" fill="#059669"/>
                    <path d="M24 25L42 14.5V33.5L24 44V25Z" fill="#047857"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">In Stock</p>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5 tracking-tight" id="cardInStockCount">{{ $stats['in_stock'] ?? 4 }}</h3>
            </div>
        </div>

        <!-- Card 3: Low Stock (Soft Mint/Cyan Card with Yellow Warning Triangle) -->
        <div class="bg-[#F0FDF4] border border-emerald-100/80 rounded-3xl p-6 shadow-xs flex items-center space-x-5">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <!-- Triangle Warning Icon -->
                <div class="w-10 h-10 rounded-full bg-amber-100/80 flex items-center justify-center text-amber-500 text-xl font-bold">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Low Stock</p>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5 tracking-tight" id="cardLowStockCount">{{ $stats['low_stock'] ?? 2 }}</h3>
            </div>
        </div>
    </div>

    <!-- SECTION TITLE: ALL ITEMS (EXACT BLUE TEXT FROM SCREENSHOT) -->
    <div class="pt-1">
        <h2 class="text-lg sm:text-xl font-bold text-[#2563EB] tracking-tight">All Items</h2>
    </div>

    <!-- MAIN TABLE CARD (CONTAINING SEARCH, FILTERS, TABLE, AND PAGINATION) -->
    <div class="bg-white border border-[#E2E8F0] rounded-2xl sm:rounded-3xl shadow-[0_4px_25px_-5px_rgba(0,0,0,0.03)] overflow-hidden">
        
        <!-- SEARCH & FILTER TOOLBAR -->
        <div class="p-6 pb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Search Input -->
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
                <input type="text" 
                       id="inventorySearchInput" 
                       oninput="filterInventoryTable()" 
                       placeholder="Search item..." 
                       value="{{ $search ?? '' }}"
                       class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg sm:rounded-xl text-sm placeholder-gray-400 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
            </div>

            <!-- Filter Dropdowns -->
            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <!-- All Categories Dropdown -->
                <div class="relative w-1/2 sm:w-44">
                    <select id="categoryFilterSelect" 
                            onchange="filterInventoryTable()"
                            class="appearance-none w-full pl-4 pr-9 py-2 bg-white border border-gray-200 rounded-lg sm:rounded-xl text-sm text-[#334155] font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer shadow-2xs">
                        <option value="All Categories">All Categories</option>
                        <option value="Cleaning" {{ ($categoryFilter ?? '') === 'Cleaning' ? 'selected' : '' }}>Cleaning</option>
                        <option value="Packaging" {{ ($categoryFilter ?? '') === 'Packaging' ? 'selected' : '' }}>Packaging</option>
                        <option value="Accessories" {{ ($categoryFilter ?? '') === 'Accessories' ? 'selected' : '' }}>Accessories</option>
                        <option value="Chemicals" {{ ($categoryFilter ?? '') === 'Chemicals' ? 'selected' : '' }}>Chemicals</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-[#64748B]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <!-- All Status Dropdown -->
                <div class="relative w-1/2 sm:w-36">
                    <select id="statusFilterSelect" 
                            onchange="filterInventoryTable()"
                            class="appearance-none w-full pl-4 pr-9 py-2 bg-white border border-gray-200 rounded-lg sm:rounded-xl text-sm text-[#334155] font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer shadow-2xs">
                        <option value="All Status">All Status</option>
                        <option value="In Stock" {{ ($statusFilter ?? '') === 'In Stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="Low Stock" {{ ($statusFilter ?? '') === 'Low Stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="Out of Stock" {{ ($statusFilter ?? '') === 'Out of Stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-[#64748B]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- FULL-WIDTH TABLE SECTION WITH #F4F6FD HEADER -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F4F6FD] border-y border-[#E2E8F0]/80 text-[13px] font-bold text-[#1E293B]">
                        <th class="py-3.5 px-6 w-16 font-bold text-[#1E293B]">#</th>
                        <th class="py-3.5 px-6 font-bold text-[#1E293B]">Item</th>
                        <th class="py-3.5 px-6 font-bold text-[#1E293B]">Category</th>
                        <th class="py-3.5 px-6 font-bold text-[#1E293B]">Stock</th>
                        <th class="py-3.5 px-6 font-bold text-[#1E293B]">Unit</th>
                        <th class="py-3.5 px-6 font-bold text-[#1E293B]">Status</th>
                        <th class="py-3.5 px-6 font-bold text-[#1E293B] text-center w-36">Actions</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody" class="divide-y divide-[#F1F5F9] text-sm bg-white">
                    @forelse($items as $index => $item)
                    @php
                        // Specific pastel icon boxes matching screenshot
                        $name = $item->name ?? '';
                        $cat = $item->category ?? '';
                        $status = $item->status ?? 'In Stock';
                        
                        // Default styles
                        $iconBox = 'bg-[#EFF6FF] text-[#2563EB] border border-[#DBEAFE]';
                        $iconSvg = '<svg class="w-5 h-5 text-[#2563EB]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2h4v3h-4z"></path><path d="M10 5v2a3 3 0 0 1-.8 2L6.5 11.5a2 2 0 0 0-.5 1.3V19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-6.2a2 2 0 0 0-.5-1.3L14.8 9A3 3 0 0 1 14 7V5"></path><circle cx="12" cy="15" r="1.5"></circle></svg>';

                        if (stripos($name, 'Detergent') !== false) {
                            $iconBox = 'bg-[#EFF6FF] text-[#2563EB] border border-[#DBEAFE]';
                            $iconSvg = '<svg class="w-5 h-5 text-[#2563EB]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2h4v3h-4z"></path><path d="M10 5v2a3 3 0 0 1-.8 2L6.5 11.5a2 2 0 0 0-.5 1.3V19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-6.2a2 2 0 0 0-.5-1.3L14.8 9A3 3 0 0 1 14 7V5"></path><circle cx="12" cy="15" r="1.5"></circle></svg>';
                        } elseif (stripos($name, 'Softener') !== false) {
                            $iconBox = 'bg-[#FDF2F8] text-[#EC4899] border border-[#FCE7F3]';
                            $iconSvg = '<svg class="w-5 h-5 text-[#EC4899]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2h4v3h-4z"></path><path d="M10 5v2a3 3 0 0 1-.8 2L6.5 11.5a2 2 0 0 0-.5 1.3V19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-6.2a2 2 0 0 0-.5-1.3L14.8 9A3 3 0 0 1 14 7V5"></path><path d="M12 13.5a1.8 1.8 0 1 0 0 3.6 1.8 1.8 0 0 0 0-3.6z"></path></svg>';
                        } elseif (stripos($name, 'Bleach') !== false) {
                            $iconBox = 'bg-[#F0F9FF] text-[#0284C7] border border-[#E0F2FE]';
                            $iconSvg = '<svg class="w-5 h-5 text-[#0284C7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2h4v3H8z"></path><path d="M9 5v2a2 2 0 0 1-.6 1.4L6 11v8a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2V5"></path><path d="M15 9h2a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-2"></path></svg>';
                        } elseif (stripos($name, 'Bag') !== false) {
                            $iconBox = 'bg-[#FFFBEB] text-[#D97706] border border-[#FEF3C7]';
                            $iconSvg = '<svg class="w-5 h-5 text-[#D97706]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>';
                        } elseif (stripos($name, 'Hanger') !== false) {
                            $iconBox = 'bg-[#FFFBEB] text-[#D97706] border border-[#FEF3C7]';
                            $iconSvg = '<svg class="w-5 h-5 text-[#D97706]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a3 3 0 0 0-3 3c0 .87.37 1.66.97 2.21L3 17h18L14.03 8.21c.6-.55.97-1.34.97-2.21a3 3 0 0 0-3-3z"></path></svg>';
                        } elseif (stripos($name, 'Chemical') !== false) {
                            $iconBox = 'bg-[#EFF6FF] text-[#2563EB] border border-[#DBEAFE]';
                            $iconSvg = '<svg class="w-5 h-5 text-[#2563EB]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2h3v3h-3z"></path><path d="M7 5h9l-1 2H8L7 5z"></path><path d="M10 7v3"></path><path d="M13 7v2a1 1 0 0 1-1 1h-2a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h5a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-1"></path><path d="M6 7l-2 2"></path></svg>';
                        }

                        $statusBadgeClass = ($status === 'In Stock')
                            ? 'bg-[#DCFCE7] text-[#16A34A]'
                            : 'bg-[#FEE2E2] text-[#DC2626]';
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition inventory-row"
                        id="inventory-row-{{ $item->id }}"
                        data-id="{{ $item->id }}"
                        data-name="{{ strtolower($item->name ?? '') }}"
                        data-category="{{ $item->category ?? '' }}"
                        data-stock="{{ $item->stock ?? 0 }}"
                        data-unit="{{ $item->unit ?? 'L' }}"
                        data-status="{{ $item->status ?? 'In Stock' }}">
                        
                        <!-- # Column -->
                        <td class="py-3.5 px-6 text-[#475569] font-medium text-sm item-row-index">
                            {{ $loop->iteration ?? ($index + 1) }}
                        </td>

                        <!-- Item Column with Pastel Box Icon -->
                        <td class="py-3.5 px-6">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $iconBox }}">
                                    {!! $iconSvg !!}
                                </div>
                                <span class="font-bold text-[#0F172A] text-sm whitespace-nowrap">{{ $item->name }}</span>
                            </div>
                        </td>

                        <!-- Category Column -->
                        <td class="py-3.5 px-6 text-[#64748B] text-sm font-normal whitespace-nowrap">
                            {{ $item->category }}
                        </td>

                        <!-- Stock Column -->
                        <td class="py-3.5 px-6 text-[#0F172A] font-bold text-sm whitespace-nowrap">
                            {{ $item->stock }}
                        </td>

                        <!-- Unit Column -->
                        <td class="py-3.5 px-6 text-[#64748B] text-sm font-normal whitespace-nowrap">
                            {{ $item->unit }}
                        </td>

                        <!-- Status Column -->
                        <td class="py-3.5 px-6 whitespace-nowrap">
                            <span class="inline-block px-3 py-0.5 rounded-full text-xs font-semibold {{ $statusBadgeClass }}">
                                {{ $item->status }}
                            </span>
                        </td>

                        <!-- Actions Column: 3 Colored Buttons (View Eye, Edit Pencil, Delete Trash) -->
                        <td class="py-3.5 px-6 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- View / Watch Button (Cyan/Sky) -->
                                <button type="button" 
                                        data-id="{{ $item->id }}"
                                        data-name="{{ $item->name }}"
                                        data-category="{{ $item->category }}"
                                        data-stock="{{ $item->stock }}"
                                        data-unit="{{ $item->unit }}"
                                        data-status="{{ $item->status }}"
                                        data-min-stock="{{ $item->min_stock ?? 15 }}"
                                        data-supplier="{{ $item->supplier ?? 'General Supplier' }}"
                                        data-price="{{ $item->unit_price ?? 0 }}"
                                        data-notes="{{ $item->notes ?? '' }}"
                                        data-restocked="{{ $item->last_restocked ?? date('Y-m-d') }}"
                                        onclick="handleViewItemClick(this)"
                                        class="w-8 h-8 rounded-lg bg-[#2563EB] hover:bg-blue-700 text-white flex items-center justify-center shadow-2xs transition active:scale-90 cursor-pointer" 
                                        title="View Details">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>

                                <!-- Edit Button (Emerald Green) -->
                                <button type="button" 
                                        data-id="{{ $item->id }}"
                                        data-name="{{ $item->name }}"
                                        data-category="{{ $item->category }}"
                                        data-stock="{{ $item->stock }}"
                                        data-unit="{{ $item->unit }}"
                                        data-status="{{ $item->status }}"
                                        data-min-stock="{{ $item->min_stock ?? 15 }}"
                                        data-supplier="{{ $item->supplier ?? 'General Supplier' }}"
                                        data-price="{{ $item->unit_price ?? 0 }}"
                                        data-notes="{{ $item->notes ?? '' }}"
                                        onclick="handleEditItemClick(this)"
                                        class="w-8 h-8 rounded-lg bg-[#10B981] hover:bg-emerald-600 text-white flex items-center justify-center shadow-2xs transition active:scale-90 cursor-pointer" 
                                        title="Edit Item">
                                    <i class="fa-solid fa-pencil text-xs"></i>
                                </button>

                                <!-- Delete Button (Rose Red) -->
                                <button type="button" 
                                        data-id="{{ $item->id }}"
                                        data-name="{{ $item->name }}"
                                        onclick="handleDeleteItemClick(this)"
                                        class="w-8 h-8 rounded-lg bg-[#EF4444] hover:bg-rose-600 text-white flex items-center justify-center shadow-2xs transition active:scale-90 cursor-pointer" 
                                        title="Delete Item">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow">
                        <td colspan="7" class="py-12 text-center text-gray-400 font-medium">
                            <i class="fa-solid fa-box-open text-3xl mb-3 text-gray-300 block"></i>
                            No inventory items found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer & Pagination Matching Screenshot -->
        <div class="p-6 pt-5 border-t border-[#F1F5F9] flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
            <div id="tableInfoText" class="text-sm font-semibold text-[#2563EB]">
                Showing 1 to {{ count($items) }} of {{ $stats['total'] ?? count($items) }} items
            </div>

            <div class="flex items-center space-x-2">
                <button type="button" class="text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 font-medium px-4 py-2 rounded-xl transition cursor-pointer shadow-2xs flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    <span>Previous</span>
                </button>
                <button type="button" class="w-9 h-9 rounded-xl bg-[#2563EB] text-white font-bold flex items-center justify-center text-xs shadow-2xs">
                    1
                </button>
                <button type="button" class="text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 font-medium px-4 py-2 rounded-xl transition cursor-pointer shadow-2xs flex items-center space-x-1">
                    <span>Next</span>
                    <svg class="w-3.5 h-3.5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>

    </div>

</div>

<!-- ========================================== -->
<!-- 1. ADD NEW INVENTORY ITEM MODAL            -->
<!-- ========================================== -->
<div id="addItemModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl animate-fade-in border border-gray-100 my-8">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between pb-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#2563EB] flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-[#0F172A] tracking-tight leading-tight">Add Laundry Supplies</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Enter item information and starting stock quantity.</p>
                </div>
            </div>
            <button type="button" onclick="closeAddItemModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-light leading-none -mt-1 cursor-pointer">
                &times;
            </button>
        </div>

        <!-- Add Form -->
        <form id="addItemForm" method="POST" action="" class="space-y-3.5 mt-2">
            @csrf

            <!-- Item Name -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">
                    Item Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       required 
                       placeholder="e.g. Detergent, Bleach, Softener" 
                       class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">
                    Category <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select name="category" 
                            required 
                            class="appearance-none w-full px-4 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition cursor-pointer">
                        <option value="" disabled selected>Select category</option>
                        <option value="Cleaning">Cleaning</option>
                        <option value="Packaging">Packaging</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Chemicals">Chemicals</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                        <i class="fa-solid fa-chevron-down text-[11px]"></i>
                    </div>
                </div>
            </div>

            <!-- Stock & Unit Grid -->
            <div class="grid grid-cols-2 gap-3">
                <!-- Starting Stock -->
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">
                        Stock Quantity <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           name="stock" 
                           min="0" 
                           required 
                           placeholder="e.g. 50" 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>

                <!-- Unit -->
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">
                        Unit <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="unit" 
                                required 
                                class="appearance-none w-full px-4 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition cursor-pointer">
                            <option value="L">L (Liters)</option>
                            <option value="Pcs">Pcs (Pieces)</option>
                            <option value="Kg">Kg (Kilograms)</option>
                            <option value="Bottles">Bottles</option>
                            <option value="Boxes">Boxes</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                            <i class="fa-solid fa-chevron-down text-[11px]"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Threshold Alert -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">
                    Low Stock Alert Threshold
                </label>
                <div class="relative">
                    <input type="number" 
                           name="min_stock" 
                           min="1" 
                           value="15" 
                           placeholder="e.g. 15" 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-gray-400 pointer-events-none">alert below</span>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">Automatically marks item as "Low Stock" when inventory drops below this number.</p>
            </div>

            <!-- Supplier -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">
                    Supplier / Brand <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <input type="text" 
                       name="supplier" 
                       placeholder="e.g. EcoChem Supplies Co." 
                       class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">
                    Description & Storage Notes <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <textarea name="notes" 
                          rows="2" 
                          placeholder="Storage location, usage guidelines or warnings..." 
                          class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition resize-none"></textarea>
            </div>

            <!-- Modal Footer Buttons -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 gap-3">
                <button type="button" 
                        onclick="closeAddItemModal()" 
                        class="w-1/2 py-2.5 px-4 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-sm font-semibold transition cursor-pointer text-center">
                    Cancel
                </button>
                <button type="submit" 
                        class="w-1/2 py-2.5 px-4 rounded-xl bg-[#2563EB] hover:bg-blue-700 text-white text-sm font-semibold shadow-xs transition flex items-center justify-center space-x-2 cursor-pointer active:scale-95">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Save Item</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================== -->
<!-- 2. EDIT INVENTORY ITEM MODAL               -->
<!-- ========================================== -->
<div id="editItemModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl animate-fade-in border border-gray-100 my-8">
        
        <!-- Header -->
        <div class="flex items-start justify-between pb-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-[#0F172A] tracking-tight leading-tight">Edit Inventory Item</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Update supplies details and thresholds.</p>
                </div>
            </div>
            <button type="button" onclick="closeEditItemModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-light leading-none -mt-1 cursor-pointer">
                &times;
            </button>
        </div>

        <!-- Edit Form -->
        <form id="editItemForm" method="POST" action="" class="space-y-3.5 mt-2">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">
                    Item Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="editItemName" 
                       name="name" 
                       required 
                       class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">
                    Category <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select id="editItemCategory" 
                            name="category" 
                            required 
                            class="appearance-none w-full px-4 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition cursor-pointer">
                        <option value="Cleaning">Cleaning</option>
                        <option value="Packaging">Packaging</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Chemicals">Chemicals</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                        <i class="fa-solid fa-chevron-down text-[11px]"></i>
                    </div>
                </div>
            </div>

            <!-- Stock & Unit -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">
                        Stock Quantity <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           id="editItemStock" 
                           name="stock" 
                           min="0" 
                           required 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">
                        Unit <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="editItemUnit" 
                                name="unit" 
                                required 
                                class="appearance-none w-full px-4 pr-9 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition cursor-pointer">
                            <option value="L">L (Liters)</option>
                            <option value="Pcs">Pcs (Pieces)</option>
                            <option value="Kg">Kg (Kilograms)</option>
                            <option value="Bottles">Bottles</option>
                            <option value="Boxes">Boxes</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                            <i class="fa-solid fa-chevron-down text-[11px]"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Min Stock Alert -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">
                    Low Stock Alert Threshold
                </label>
                <div class="relative">
                    <input type="number" 
                           id="editItemMinStock" 
                           name="min_stock" 
                           min="1" 
                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-gray-400 pointer-events-none">alert below</span>
                </div>
            </div>

            <!-- Supplier -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">
                    Supplier / Brand
                </label>
                <input type="text" 
                       id="editItemSupplier" 
                       name="supplier" 
                       class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">
                    Notes
                </label>
                <textarea id="editItemNotes" 
                          name="notes" 
                          rows="2" 
                          class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition resize-none"></textarea>
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 gap-3">
                <button type="button" 
                        onclick="closeEditItemModal()" 
                        class="w-1/2 py-2.5 px-4 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-sm font-semibold transition cursor-pointer text-center">
                    Cancel
                </button>
                <button type="submit" 
                        class="w-1/2 py-2.5 px-4 rounded-xl bg-[#10B981] hover:bg-emerald-600 text-white text-sm font-semibold shadow-xs transition flex items-center justify-center space-x-2 cursor-pointer active:scale-95">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Update Item</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================== -->
<!-- 3. VIEW ITEM & QUICK STOCK IN / OUT MODAL  -->
<!-- ========================================== -->
<div id="viewItemModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl animate-fade-in border border-gray-100 my-8">
        
        <!-- Header -->
        <div class="flex items-start justify-between pb-4 border-b border-gray-100">
            <div class="flex items-center space-x-3.5">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#2563EB] flex items-center justify-center text-2xl flex-shrink-0">
                    <i class="fa-solid fa-cubes-stacked"></i>
                </div>
                <div>
                    <h3 id="viewItemName" class="text-xl font-extrabold text-gray-900 leading-tight"></h3>
                    <div class="flex items-center space-x-2 mt-1">
                        <span id="viewItemCategory" class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700"></span>
                        <span id="viewItemStatus" class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="closeViewItemModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-light leading-none cursor-pointer">
                &times;
            </button>
        </div>

        <!-- Body Details -->
        <div class="py-4 space-y-4 text-sm">
            <!-- Metrics Box -->
            <div class="grid grid-cols-3 gap-3 bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 text-center">
                <div>
                    <span class="text-xs text-gray-400 block font-medium">Current Stock</span>
                    <span id="viewItemStock" class="text-2xl font-extrabold text-gray-900"></span>
                    <span id="viewItemUnit" class="text-xs font-bold text-gray-500"></span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block font-medium">Min Threshold</span>
                    <span id="viewItemMinStock" class="text-lg font-bold text-amber-600 mt-1 block"></span>
                    <span class="text-[10px] text-gray-400">alert trigger</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block font-medium">Last Restocked</span>
                    <span id="viewItemRestocked" class="text-xs font-bold text-gray-700 mt-2 block"></span>
                </div>
            </div>

            <!-- Notes & Supplier -->
            <div class="bg-blue-50/50 p-3.5 rounded-2xl border border-blue-100/60 space-y-1">
                <span class="text-xs text-blue-800 font-bold block">Supplier & Storage Info</span>
                <p id="viewItemSupplier" class="text-xs font-semibold text-gray-800"></p>
                <p id="viewItemNotes" class="text-xs text-gray-600 mt-0.5"></p>
            </div>

            <!-- QUICK STOCK IN & STOCK OUT CONTROLS (FUNCTIONS: Stock In & Stock Out) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <!-- Stock In Box -->
                <div class="p-3.5 bg-emerald-50/50 rounded-2xl border border-emerald-100/60 space-y-2">
                    <div class="flex items-center space-x-1.5 text-emerald-800 font-bold text-xs">
                        <i class="fa-solid fa-arrow-down-long"></i>
                        <span>Stock In (Add)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="number" 
                               id="stockInQty" 
                               value="10" 
                               min="1" 
                               class="w-20 px-2.5 py-1.5 bg-white border border-emerald-200 rounded-lg text-sm text-center font-bold text-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <button type="button" 
                                onclick="executeStockAction('in')" 
                                class="flex-1 py-1.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg transition shadow-2xs cursor-pointer">
                            + Stock In
                        </button>
                    </div>
                </div>

                <!-- Stock Out Box -->
                <div class="p-3.5 bg-rose-50/50 rounded-2xl border border-rose-100/60 space-y-2">
                    <div class="flex items-center space-x-1.5 text-rose-800 font-bold text-xs">
                        <i class="fa-solid fa-arrow-up-long"></i>
                        <span>Stock Out (Use)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="number" 
                               id="stockOutQty" 
                               value="5" 
                               min="1" 
                               class="w-20 px-2.5 py-1.5 bg-white border border-rose-200 rounded-lg text-sm text-center font-bold text-rose-900 focus:outline-none focus:ring-2 focus:ring-rose-400">
                        <button type="button" 
                                onclick="executeStockAction('out')" 
                                class="flex-1 py-1.5 px-3 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-lg transition shadow-2xs cursor-pointer">
                            - Stock Out
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-400">Live inventory synchronization</span>
            <div class="flex items-center space-x-2">
                <button type="button" 
                        id="viewModalEditBtn"
                        class="px-5 py-2.5 rounded-xl bg-[#2563EB] hover:bg-blue-700 text-white font-semibold text-sm transition shadow-2xs cursor-pointer">
                    Edit Item
                </button>
                <button type="button" 
                        onclick="closeViewItemModal()" 
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
<div id="deleteItemModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl text-center border border-gray-100 animate-fade-in">
        <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Delete Supplies Item</h3>
        <p class="text-xs text-gray-500 mb-6">Are you sure you want to remove <span id="deleteItemNameSpan" class="font-bold text-gray-800"></span>? This will remove it from store inventory records.</p>
        
        <form id="deleteItemForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-center space-x-3">
                <button type="button" onclick="closeDeleteItemModal()" class="w-1/2 py-2.5 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 text-sm font-semibold transition cursor-pointer">
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
    let currentViewingItem = null;

    /**
     * Resolves the exact inventory endpoint relative to current browser URL.
     * Works seamlessly on localhost, XAMPP subdirectories, and artisan serve.
     */
    function getAppEndpoint(subpath = '') {
        const cleanPath = window.location.pathname.replace(/\/+$/, '');
        let base = cleanPath;
        const match = cleanPath.match(/^(.*?\/(?:inventory|inventories))(?:\/.*)?$/);
        if (match) {
            base = match[1];
        }
        if (subpath) {
            return (base + '/' + subpath).replace(/\/+/g, '/');
        }
        return base;
    }

    // Extract item data safely from button element
    function getItemFromButton(btn) {
        return {
            id: btn.getAttribute('data-id') || '',
            name: btn.getAttribute('data-name') || '',
            category: btn.getAttribute('data-category') || 'Cleaning',
            stock: btn.getAttribute('data-stock') || '0',
            unit: btn.getAttribute('data-unit') || 'L',
            status: btn.getAttribute('data-status') || 'In Stock',
            min_stock: btn.getAttribute('data-min-stock') || '15',
            supplier: btn.getAttribute('data-supplier') || 'General Supplier',
            unit_price: btn.getAttribute('data-price') || '0',
            notes: btn.getAttribute('data-notes') || '',
            last_restocked: btn.getAttribute('data-restocked') || '2026-10-01'
        };
    }

    function handleViewItemClick(btn) {
        const item = getItemFromButton(btn);
        openViewItemModal(item);
    }

    function handleEditItemClick(btn) {
        const item = getItemFromButton(btn);
        openEditItemModal(item);
    }

    function handleDeleteItemClick(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        confirmDeleteItem(id, name);
    }

    // Modal: Add
    function openAddItemModal() {
        const form = document.getElementById('addItemForm');
        if (form) {
            form.action = getAppEndpoint();
        }
        document.getElementById('addItemModal').classList.remove('hidden');
    }
    function closeAddItemModal() {
        document.getElementById('addItemModal').classList.add('hidden');
    }

    // Modal: Edit
    function openEditItemModal(item) {
        document.getElementById('editItemName').value = item.name || '';
        document.getElementById('editItemCategory').value = item.category || 'Cleaning';
        document.getElementById('editItemStock').value = item.stock || '0';
        document.getElementById('editItemUnit').value = item.unit || 'L';
        document.getElementById('editItemMinStock').value = item.min_stock || '15';
        document.getElementById('editItemSupplier').value = item.supplier || '';
        document.getElementById('editItemNotes').value = item.notes || '';

        const editForm = document.getElementById('editItemForm');
        if (editForm) {
            editForm.action = getAppEndpoint(item.id);
        }
        document.getElementById('editItemModal').classList.remove('hidden');
    }
    function closeEditItemModal() {
        document.getElementById('editItemModal').classList.add('hidden');
    }

    // Modal: View & Stock In/Out
    function openViewItemModal(item) {
        currentViewingItem = item;
        document.getElementById('viewItemName').innerText = item.name;
        document.getElementById('viewItemCategory').innerText = item.category;
        document.getElementById('viewItemStock').innerText = item.stock;
        document.getElementById('viewItemUnit').innerText = ' ' + item.unit;
        document.getElementById('viewItemMinStock').innerText = item.min_stock + ' ' + item.unit;
        document.getElementById('viewItemSupplier').innerText = item.supplier || 'Standard Supplier';
        document.getElementById('viewItemNotes').innerText = item.notes || 'Standard laundry supplies.';
        document.getElementById('viewItemRestocked').innerText = item.last_restocked || 'Recent';

        const statusSpan = document.getElementById('viewItemStatus');
        statusSpan.innerText = item.status;
        statusSpan.className = 'inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold ' + 
            (item.status === 'In Stock' ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-[#FEE2E2] text-[#DC2626]');

        // Connect Edit button in View Modal
        document.getElementById('viewModalEditBtn').onclick = function() {
            closeViewItemModal();
            openEditItemModal(item);
        };

        document.getElementById('viewItemModal').classList.remove('hidden');
    }
    function closeViewItemModal() {
        document.getElementById('viewItemModal').classList.add('hidden');
    }

    // Execute Stock In (+) or Stock Out (-)
    function executeStockAction(type) {
        if (!currentViewingItem) return;
        const itemId = currentViewingItem.id;
        const qtyInput = document.getElementById(type === 'in' ? 'stockInQty' : 'stockOutQty');
        const qty = parseInt(qtyInput.value) || (type === 'in' ? 10 : 5);
        const token = document.querySelector('input[name="_token"]')?.value || '';
        const actionUrl = getAppEndpoint(itemId + (type === 'in' ? '/stock-in' : '/stock-out'));

        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: qty })
        })
        .then(res => res.json())
        .then(data => {
            window.location.reload();
        })
        .catch(err => {
            window.location.reload();
        });
    }

    // Modal: Delete
    function confirmDeleteItem(id, name) {
        document.getElementById('deleteItemNameSpan').innerText = name;
        const deleteForm = document.getElementById('deleteItemForm');
        if (deleteForm) {
            deleteForm.action = getAppEndpoint(id);
        }
        document.getElementById('deleteItemModal').classList.remove('hidden');
    }
    function closeDeleteItemModal() {
        document.getElementById('deleteItemModal').classList.add('hidden');
    }

    // Client-side Instant Filter & Search Table
    function filterInventoryTable() {
        const searchVal = (document.getElementById('inventorySearchInput').value || '').trim().toLowerCase();
        const catVal = document.getElementById('categoryFilterSelect').value;
        const statusVal = document.getElementById('statusFilterSelect').value;

        const rows = document.querySelectorAll('.inventory-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const category = row.getAttribute('data-category') || '';
            const status = row.getAttribute('data-status') || '';

            const matchesSearch = !searchVal || 
                name.includes(searchVal) || 
                category.toLowerCase().includes(searchVal) || 
                status.toLowerCase().includes(searchVal);

            const matchesCat = (catVal === 'All Categories' || !catVal) || (category.toLowerCase() === catVal.toLowerCase());
            const matchesStatus = (statusVal === 'All Status' || !statusVal) || (status === statusVal);

            if (matchesSearch && matchesCat && matchesStatus) {
                row.style.display = '';
                visibleCount++;
                const indexCell = row.querySelector('.item-row-index');
                if (indexCell) {
                    indexCell.innerText = visibleCount;
                }
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide empty state
        const noResultsRow = document.getElementById('noResultsRow');
        if (noResultsRow) {
            noResultsRow.style.display = (visibleCount === 0) ? '' : 'none';
        }

        const infoText = document.getElementById('tableInfoText');
        if (infoText) {
            infoText.innerText = visibleCount > 0 
                ? `Showing 1 to ${visibleCount} of ${visibleCount} items`
                : `Showing 0 items`;
        }
    }

    // Form Event Submissions with AJAX & Native Fail-Safe
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Add Form
        const addForm = document.getElementById('addItemForm');
        if (addForm) {
            addForm.action = getAppEndpoint();
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
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
                .then(res => res.json())
                .then(data => {
                    closeAddItemModal();
                    window.location.reload();
                })
                .catch(err => {
                    addForm.submit();
                });
            });
        }

        // 2. Edit Form
        const editForm = document.getElementById('editItemForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
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
                .then(res => res.json())
                .then(data => {
                    closeEditItemModal();
                    window.location.reload();
                })
                .catch(err => {
                    editForm.submit();
                });
            });
        }

        // 3. Delete Form
        const deleteForm = document.getElementById('deleteItemForm');
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
                .then(res => res.json())
                .then(data => {
                    closeDeleteItemModal();
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