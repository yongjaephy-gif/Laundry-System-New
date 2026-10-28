@extends('layouts.navbar')
@section('title', 'Orders')

@section('content')
<div class="space-y-6">

    <!-- Success & Error Alerts -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
        <div class="flex items-center space-x-3">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        <script>
            setTimeout(function() {
                window.location.reload();
            }, 400);
        </script>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm">
        <div class="flex items-center space-x-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
        <script>
            setTimeout(function() {
                window.location.reload();
            }, 200);
        </script>
    </div>
    @endif
    <div class="flex justify-end items-center">
        <button type="button" onclick="openCreateOrderModal()" class="px-3.5 py-1.5 bg-[#2563EB] hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 cursor-pointer">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>New Order</span>
        </button>
    </div>

    <!-- 6 KPI / Stat Cards (Matching Screenshot 1) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- 1. Total Orders -->
        <div class="bg-[#F0F6FF] rounded-2xl p-4 shadow-sm border border-blue-100/70 hover:shadow-md transition">
            <div class="w-8 h-8 rounded-xl bg-blue-100/80 text-blue-600 flex items-center justify-center mb-3">
                <i data-lucide="file-text" class="w-4 h-4"></i>
            </div>
            <p class="text-xs font-semibold text-gray-500">Total Orders</p>
            <p class="text-xl sm:text-xl font-bold text-gray-900 mt-0.5">{{ $stats['total_orders'] }}</p>
            <p class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center">
                <span>↑ 12%</span><span class="text-gray-400 font-normal ml-1">vs. last month</span>
            </p>
        </div>

        <!-- 2. Pending -->
        <div class="bg-[#FEFCE8] rounded-2xl p-4 shadow-sm border border-amber-100/70 hover:shadow-md transition">
            <div class="w-8 h-8 rounded-xl bg-amber-100/80 text-amber-600 flex items-center justify-center mb-3">
                <i data-lucide="clock" class="w-4 h-4"></i>
            </div>
            <p class="text-xs font-semibold text-gray-500">Pending</p>
            <p class="text-xl sm:text-xl font-bold text-gray-900 mt-0.5">{{ $stats['pending'] }}</p>
            <p class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center">
                <span>↑ 5%</span><span class="text-gray-400 font-normal ml-1">vs. last month</span>
            </p>
        </div>

        <!-- 3. Washing -->
        <div class="bg-[#F0F9FF] rounded-2xl p-4 shadow-sm border border-sky-100/70 hover:shadow-md transition">
            <div class="w-8 h-8 rounded-xl bg-sky-100/80 text-sky-600 flex items-center justify-center mb-3">
                <i data-lucide="washing-machine" class="w-4 h-4"></i>
            </div>
            <p class="text-xs font-semibold text-gray-500">Washing</p>
            <p class="text-xl sm:text-xl font-bold text-gray-900 mt-0.5">{{ $stats['washing'] }}</p>
            <p class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center">
                <span>↑ 6%</span><span class="text-gray-400 font-normal ml-1">vs. last month</span>
            </p>
        </div>

        <!-- 4. Ready for Pickup -->
        <div class="bg-[#FAF5FF] rounded-2xl p-4 shadow-sm border border-purple-100/70 hover:shadow-md transition">
            <div class="w-8 h-8 rounded-xl bg-purple-100/80 text-purple-600 flex items-center justify-center mb-3">
                <i data-lucide="shirt" class="w-4 h-4"></i>
            </div>
            <p class="text-xs font-semibold text-gray-500">Ready for Pickup</p>
            <p class="text-xl sm:text-xl font-bold text-gray-900 mt-0.5">{{ $stats['ready_pickup'] }}</p>
            <p class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center">
                <span>↑ 10%</span><span class="text-gray-400 font-normal ml-1">vs. last month</span>
            </p>
        </div>

        <!-- 5. Out for Delivery -->
        <div class="bg-[#F0FDF4] rounded-2xl p-4 shadow-sm border border-teal-100/70 hover:shadow-md transition">
            <div class="w-8 h-8 rounded-xl bg-teal-100/80 text-teal-600 flex items-center justify-center mb-3">
                <i data-lucide="truck" class="w-4 h-4"></i>
            </div>
            <p class="text-xs font-semibold text-gray-500">Out for Delivery</p>
            <p class="text-xl sm:text-xl font-bold text-gray-900 mt-0.5">{{ $stats['out_delivery'] }}</p>
            <p class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center">
                <span>↑ 8%</span><span class="text-gray-400 font-normal ml-1">vs. last month</span>
            </p>
        </div>

        <!-- 6. Completed -->
        <div class="bg-[#ECFDF5] rounded-2xl p-4 shadow-sm border border-emerald-100/70 hover:shadow-md transition">
            <div class="w-8 h-8 rounded-xl bg-emerald-100/80 text-emerald-600 flex items-center justify-center mb-3">
                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
            </div>
            <p class="text-xs font-semibold text-gray-500">Completed</p>
            <p class="text-xl sm:text-xl font-bold text-gray-900 mt-0.5">{{ $stats['completed'] }}</p>
            <p class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center">
                <span>↑ 15%</span><span class="text-gray-400 font-normal ml-1">vs. last month</span>
            </p>
        </div>
    </div>

    <!-- Search & Filter Bar (Matching Screenshot 1) -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('orders.index') }}" class="flex flex-wrap items-center gap-3">

            <!-- Search Text Input -->
            <div class="flex-1 min-w-0 relative" style="min-width: 260px;">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by order number, customer name, or service..." class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <!-- Status Dropdown -->
            <div class="w-36">
                <select name="status" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition cursor-pointer">
                    <option value="All Status" {{ $selectedStatus === 'All Status' ? 'selected' : '' }}>All Status</option>
                    @foreach($availableStatuses as $st)
                    <option value="{{ $st }}" {{ $selectedStatus === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Service Type Dropdown -->
            <div class="w-44">
                <select name="service_type" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition cursor-pointer">
                    <option value="All Service Types" {{ $selectedService === 'All Service Types' ? 'selected' : '' }}>All Service Types</option>
                    @foreach($availableServices as $srv)
                    <option value="{{ $srv }}" {{ $selectedService === $srv ? 'selected' : '' }}>{{ $srv }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range From - To -->
            <div class="flex items-center space-x-1.5 bg-white border border-gray-200 rounded-xl px-2.5 py-1.5">
                <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                <input type="date" name="from_date" value="{{ $fromDate }}" placeholder="From" class="w-28 text-xs text-gray-600 focus:outline-none bg-transparent">
                <span class="text-gray-400 text-xs">-</span>
                <input type="date" name="to_date" value="{{ $toDate }}" placeholder="To" class="w-28 text-xs text-gray-600 focus:outline-none bg-transparent">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="px-5 py-2 bg-[#2563EB] hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-sm transition flex items-center space-x-1.5 cursor-pointer">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    <span>Search</span>
                </button>
                <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs sm:text-sm font-medium rounded-xl transition flex items-center space-x-1.5 cursor-pointer" title="Reset Filters">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Reset</span>
                </a>
            </div>

        </form>
    </div>

    <!-- Table Card (Matching Screenshot 1) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Table Card Header -->
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <h2 class="text-base font-bold text-gray-900">Laundry Orders</h2>
                <span class="bg-blue-50 text-blue-600 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                    Total: {{ $stats['total_orders'] }}
                </span>
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-100 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                        <th class="py-3 px-3 text-center w-8">#</th>
                        <th class="py-3 px-3">ORDER NUMBER</th>
                        <th class="py-3 px-3">CUSTOMER</th>
                        <th class="py-3 px-3">SERVICE TYPE</th>
                        <th class="py-3 px-3">WEIGHT (KG)</th>
                        <th class="py-3 px-3">ITEMS</th>
                        <th class="py-3 px-3">ORDER DATE</th>
                        <th class="py-3 px-3">PICKUP DATE</th>
                        <th class="py-3 px-3">DELIVERY DATE</th>
                        <th class="py-3 px-3">TOTAL PRICE</th>
                        <th class="py-3 px-3 text-center">STATUS</th>
                        <th class="py-3 px-3 text-center w-28">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs text-gray-600">
                    @forelse($orders as $index => $order)
                    @php
                    $ordId = is_array($order) ? $order['id'] : $order->id;
                    $ordNum = is_array($order) ? $order['order_number'] : $order->order_number;
                    $custName = is_array($order) ? $order['customer_name'] : $order->customer_name;
                    $servType = is_array($order) ? $order['service_type'] : $order->service_type;
                    $weight = is_array($order) ? $order['weight'] : $order->weight;
                    $itemsCount = is_array($order) ? $order['items_count'] : $order->items_count;
                    $orderDate = is_array($order) ? $order['order_date'] : $order->order_date;
                    $pickupDate = is_array($order) ? $order['pickup_date'] : $order->pickup_date;
                    $delivDate = is_array($order) ? $order['delivery_date'] : $order->delivery_date;
                    $totalPrice = is_array($order) ? $order['total_price'] : $order->total_price;
                    $status = is_array($order) ? $order['status'] : $order->status;

                    // Pill style matching Screenshot 1
                    $badgeMap = [
                    'Received' => 'bg-gray-100 text-gray-700',
                    'Pending' => 'bg-amber-100/70 text-amber-700',
                    'Washing' => 'bg-sky-100 text-sky-700',
                    'Drying' => 'bg-amber-100 text-amber-700',
                    'Ironing' => 'bg-purple-100 text-purple-700',
                    'Ready for Pickup' => 'bg-purple-100 text-purple-700',
                    'Out for Delivery' => 'bg-teal-100 text-teal-700',
                    'Completed' => 'bg-emerald-100 text-emerald-700',
                    'Cancelled' => 'bg-rose-100 text-rose-700',
                    ];
                    $badgeCls = $badgeMap[$status] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <tr class="hover:bg-blue-50/30 transition">
                        <!-- Index -->
                        <td class="py-3 px-3 text-center text-gray-400">
                            {{ ($orders->currentPage() - 1) * $orders->perPage() + $index + 1 }}
                        </td>

                        <!-- Order Number -->
                        <td class="py-3 px-3 font-bold text-gray-900 whitespace-nowrap cursor-pointer hover:text-blue-600 transition" data-id="{{ $ordId }}" onclick="handleViewOrderClick(this)">
                            {{ $ordNum }}
                        </td>

                        <!-- Customer Name -->
                        <td class="py-3 px-3 font-medium text-gray-800">
                            {{ $custName }}
                        </td>

                        <!-- Service Type -->
                        <td class="py-3 px-3 text-gray-700 font-medium">
                            {{ $servType }}
                        </td>

                        <!-- Weight (Kg) -->
                        <td class="py-3 px-3 text-gray-600">
                            {{ number_format($weight, 1) }}
                        </td>

                        <!-- Items -->
                        <td class="py-3 px-3 text-gray-600">
                            {{ $itemsCount }}
                        </td>

                        <!-- Order Date -->
                        <td class="py-3 px-3 text-gray-500 whitespace-nowrap">
                            {{ date('Y-m-d', strtotime($orderDate)) }}
                        </td>

                        <!-- Pickup Date -->
                        <td class="py-3 px-3 text-gray-500 whitespace-nowrap">
                            {{ $pickupDate ? date('Y-m-d', strtotime($pickupDate)) : '-' }}
                        </td>

                        <!-- Delivery Date -->
                        <td class="py-3 px-3 text-gray-500 whitespace-nowrap">
                            {{ $delivDate ? date('Y-m-d', strtotime($delivDate)) : '-' }}
                        </td>

                        <!-- Total Price -->
                        <td class="py-3 px-3 font-bold text-gray-900 whitespace-nowrap">
                            ${{ number_format($totalPrice, 2) }}
                        </td>

                        <!-- Status Pill Badge -->
                        <td class="py-3 px-3 text-center whitespace-nowrap">
                            <span class="inline-block px-3 py-1 rounded-full text-[11px] font-semibold {{ $badgeCls }}">
                                {{ $status }}
                            </span>
                        </td>

                        <!-- Actions: [Eye (cyan)] [Pencil (green)] [Trash (red)] -->
                        <td class="py-3 px-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center space-x-1.5">
                                <!-- Eye icon: View Details (Cyan/Sky blue button) -->
                                <button type="button" data-id="{{ $ordId }}" onclick="handleViewOrderClick(this)" title="View Order Details" class="w-7 h-7 rounded-lg bg-[#2563EB] hover:bg-blue-700 text-white flex items-center justify-center transition shadow-sm cursor-pointer">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </button>

                                <!-- Pencil icon: Edit Order (Emerald green button) -->
                                <button type="button" data-id="{{ $ordId }}" onclick="handleEditOrderClick(this)" title="Update Order" class="w-7 h-7 rounded-lg bg-[#10B981] hover:bg-emerald-600 text-white flex items-center justify-center transition shadow-sm cursor-pointer">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>

                                <!-- Trash icon: Cancel / Delete (Rose/Red button) -->
                                <button type="button" data-id="{{ $ordId }}" data-num="{{ $ordNum }}" onclick="handleCancelOrderClick(this)" title="Cancel Order" class="w-7 h-7 rounded-lg bg-[#EF4444] hover:bg-rose-600 text-white flex items-center justify-center transition shadow-sm cursor-pointer">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="py-12 text-center text-gray-400">
                            <i data-lucide="package-x" class="w-10 h-10 mx-auto text-gray-300 mb-2"></i>
                            <p class="text-sm font-semibold text-gray-600">No laundry orders found.</p>
                            <p class="text-xs text-gray-400 mt-0.5">Try adjusting your filters or add a new order.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls (Matching Screenshot 1) -->
        <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
            <div>
                Showing <span class="font-bold text-gray-800">{{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }}</span> of <span class="font-bold text-gray-800">{{ $orders->total() }}</span> orders
            </div>
            <div class="flex items-center space-x-1">
                @if ($orders->onFirstPage())
                <span class="w-8 h-8 rounded-lg border border-gray-200 text-gray-300 flex items-center justify-center cursor-not-allowed">&lt;</span>
                @else
                <a href="{{ $orders->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 flex items-center justify-center transition">&lt;</a>
                @endif

                @for ($page = 1; $page <= min(4, $orders->lastPage()); $page++)
                    @if ($page == $orders->currentPage())
                    <span class="w-8 h-8 rounded-lg bg-[#2563EB] text-white font-bold flex items-center justify-center shadow-sm">{{ $page }}</span>
                    @else
                    <a href="{{ $orders->url($page) }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 flex items-center justify-center transition">{{ $page }}</a>
                    @endif
                    @endfor

                    @if ($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 flex items-center justify-center transition">&gt;</a>
                    @else
                    <span class="w-8 h-8 rounded-lg border border-gray-200 text-gray-300 flex items-center justify-center cursor-not-allowed">&gt;</span>
                    @endif
            </div>
        </div>
    </div>

</div>

<!-- ======================================================== -->
<!-- MODAL 1: VIEW ORDER DETAILS (MATCHING SCREENSHOT 2)      -->
<!-- ======================================================== -->
<div id="viewOrderModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full p-2 shadow-2xl border border-gray-100 transform transition-all relative">

        <!-- Modal Top Title & Close Button -->
        <div class="flex items-center justify-between pb-3">
            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Order Details</h3>
            <button type="button" onclick="closeOrderModal('viewOrderModal')" class="text-gray-400 hover:text-gray-600 text-lg transition cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Order Number & Status Badge Row -->
        <div class="flex items-center space-x-3 mt-1">
            <span id="modalOrderNumber" class="text-2xl font-extrabold text-gray-900">ORD-0001</span>
            <span id="modalStatusBadge" class="px-3 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-sky-700">Washing</span>
        </div>

        <!-- Order Date Subtitle -->
        <div class="mt-1.5 flex items-center text-xs text-gray-500 space-x-1.5">
            <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
            <span id="modalOrderDateTime">Order Date: 2025-09-15 10:24 AM</span>
        </div>

        <div class="mt-4 space-y-3.5 max-h-[72vh] overflow-y-auto pr-1">

            <!-- Box 1: Customer Information -->
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2 text-gray-900 font-bold text-sm">
                        <i data-lucide="user" class="w-4 h-4 text-gray-700"></i>
                        <span>Customer Information</span>
                    </div>
                    <a href="{{ route('customers.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">View Profile</a>
                </div>

                <div class="flex items-center space-x-3.5">
                    <!-- Dark Avatar Circle -->
                    <div class="w-12 h-12 rounded-full bg-[#1e293b] text-white flex items-center justify-center shrink-0">
                        <i data-lucide="user" class="w-6 h-6 text-gray-300"></i>
                    </div>
                    <div class="space-y-1">
                        <p id="modalCustomerName" class="font-bold text-gray-900 text-sm">Sokha Dara</p>
                        <p class="text-xs text-gray-600 flex items-center space-x-1.5">
                            <i data-lucide="phone" class="w-3.5 h-3.5 text-gray-400"></i>
                            <span id="modalCustomerPhone">012 345 678</span>
                        </p>
                        <p class="text-xs text-gray-600 flex items-center space-x-1.5">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400"></i>
                            <span id="modalCustomerEmail">sokha@example.com</span>
                        </p>
                        <p class="text-xs text-gray-600 flex items-center space-x-1.5">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400"></i>
                            <span id="modalCustomerAddress">Svay Dongkum, Siem Reap</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Box 2: Order Information -->
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs">
                <div class="flex items-center space-x-2 text-gray-900 font-bold text-sm mb-3">
                    <i data-lucide="calendar" class="w-4 h-4 text-gray-700"></i>
                    <span>Order Information</span>
                </div>

                <div class="grid grid-cols-2 gap-y-2 text-xs">
                    <div class="text-gray-500 font-medium">Service Type</div>
                    <div id="modalServiceType" class="text-gray-900 font-medium">Wash & Fold</div>

                    <div class="text-gray-500 font-medium">Weight (Kg)</div>
                    <div id="modalWeight" class="text-gray-900 font-medium">5.0 kg</div>

                    <div class="text-gray-500 font-medium">Number of Items</div>
                    <div id="modalItemsCount" class="text-gray-900 font-medium">3</div>

                    <div class="text-gray-500 font-medium">Order Date</div>
                    <div id="modalOrderDateVal" class="text-gray-900 font-medium">2025-09-15 10:24 AM</div>

                    <div class="text-gray-500 font-medium">Pickup Date</div>
                    <div id="modalPickupDate" class="text-gray-900 font-medium">2025-09-16</div>

                    <div class="text-gray-500 font-medium">Delivery Date</div>
                    <div id="modalDeliveryDate" class="text-gray-900 font-medium">2025-09-17</div>

                    <div class="text-gray-500 font-bold pt-1">Total Price</div>
                    <div id="modalTotalPrice" class="text-gray-900 font-extrabold text-sm pt-1">$12.50</div>
                </div>
            </div>

            <!-- Box 3: Order Status Stepper -->
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs">
                <div class="flex items-center space-x-2 text-gray-900 font-bold text-sm mb-3">
                    <i data-lucide="clock" class="w-4 h-4 text-gray-700"></i>
                    <span>Order Status</span>
                </div>

                <!-- Status Stepper Flow -->
                <div class="space-y-4">
                    <!-- Row 1: Received -> Washing -> Drying -> Ironing -->
                    <div class="flex items-center justify-between text-center px-2">
                        <!-- 1. Received -->
                        <div id="step-Received" class="flex flex-col items-center">
                            <div class="step-circle w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                ✓
                            </div>
                            <span class="mt-1 text-[11px] font-semibold text-gray-800">Received</span>
                        </div>

                        <!-- Arrow 1 -->
                        <span id="arrow-1" class="text-emerald-500 font-bold text-sm mb-4">→</span>

                        <!-- 2. Washing -->
                        <div id="step-Washing" class="flex flex-col items-center">
                            <div class="step-circle w-8 h-8 rounded-full bg-[#0066FF] text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                <i data-lucide="rotate-cw" class="w-4 h-4 animate-spin"></i>
                            </div>
                            <span class="mt-1 text-[11px] font-bold text-[#0066FF]">Washing</span>
                        </div>

                        <!-- Arrow 2 -->
                        <span id="arrow-2" class="text-gray-300 font-bold text-sm mb-4">→</span>

                        <!-- 3. Drying -->
                        <div id="step-Drying" class="flex flex-col items-center">
                            <div class="step-circle w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xs border border-gray-200">
                                <i data-lucide="wind" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="mt-1 text-[11px] font-medium text-gray-500">Drying</span>
                        </div>

                        <!-- Arrow 3 -->
                        <span id="arrow-3" class="text-gray-300 font-bold text-sm mb-4">→</span>

                        <!-- 4. Ironing -->
                        <div id="step-Ironing" class="flex flex-col items-center">
                            <div class="step-circle w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xs border border-gray-200">
                                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="mt-1 text-[11px] font-medium text-gray-500">Ironing</span>
                        </div>
                    </div>

                    <!-- Row 2: Ready for Pickup -> Out for Delivery -> Completed -->
                    <div class="flex items-center justify-between text-center px-4 pt-1">
                        <!-- 5. Ready for Pickup -->
                        <div id="step-ReadyForPickup" class="flex flex-col items-center">
                            <div class="step-circle w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xs border border-gray-200">
                                <i data-lucide="shirt" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="mt-1 text-[11px] font-medium text-gray-500">Ready for Pickup</span>
                        </div>

                        <!-- Line 1 -->
                        <div class="flex-1 h-0.5 bg-gray-200 mx-3 mb-4"></div>

                        <!-- 6. Out for Delivery -->
                        <div id="step-OutForDelivery" class="flex flex-col items-center">
                            <div class="step-circle w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xs border border-gray-200">
                                <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="mt-1 text-[11px] font-medium text-gray-500">Out for Delivery</span>
                        </div>

                        <!-- Line 2 -->
                        <div class="flex-1 h-0.5 bg-gray-200 mx-3 mb-4"></div>

                        <!-- 7. Completed -->
                        <div id="step-Completed" class="flex flex-col items-center">
                            <div class="step-circle w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xs border border-gray-200">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="mt-1 text-[11px] font-medium text-gray-500">Completed</span>
                        </div>
                    </div>
                </div>

                <!-- Cancelled Notice (if status is Cancelled) -->
                <div id="modalCancelledNotice" class="hidden mt-3 p-2 bg-rose-50 border border-rose-200 rounded-xl text-center text-xs text-rose-700 font-bold">
                    This order is marked as CANCELLED.
                </div>
            </div>

            <!-- Box 4: Service Items Table -->
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs">
                <div class="flex items-center space-x-2 text-gray-900 font-bold text-sm mb-3">
                    <i data-lucide="briefcase" class="w-4 h-4 text-gray-700"></i>
                    <span>Service Items</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-blue-50/60 text-gray-600 font-semibold border-b border-gray-100">
                                <th class="py-2 px-2.5 text-blue-600 w-8">#</th>
                                <th class="py-2 px-2.5">Service Name</th>
                                <th class="py-2 px-2.5 text-center">Qty</th>
                                <th class="py-2 px-2.5 text-right">Price</th>
                                <th class="py-2 px-2.5 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody id="modalItemsTableBody" class="divide-y divide-gray-100 text-gray-700">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                        <tfoot>
                            <tr class="font-bold border-t border-gray-100">
                                <td colspan="4" class="py-2.5 px-2.5 text-right text-gray-800">Total Price</td>
                                <td id="modalTableSummaryTotal" class="py-2.5 px-2.5 text-right text-gray-900 font-extrabold text-sm">$12.50</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>

        <!-- Modal Bottom Action Buttons (Matching Screenshot 2) -->
        <div class="mt-5 pt-3 border-t border-gray-100 grid grid-cols-3 gap-2.5">
            <!-- Left: Print Receipt -->
            <a id="modalPrintReceiptBtn" href="#" target="_blank" class="py-2.5 px-3 bg-white hover:bg-gray-50 border border-gray-200 text-blue-600 font-semibold text-xs rounded-xl transition flex items-center justify-center space-x-1.5 shadow-xs cursor-pointer">
                <i data-lucide="printer" class="w-4 h-4 text-blue-600"></i>
                <span>Print Receipt</span>
            </a>

            <!-- Middle: Update Order (Solid Blue) -->
            <button type="button" onclick="triggerEditFromModal()" class="py-2.5 px-3 bg-[#2563EB] hover:bg-blue-700 text-white font-semibold text-xs rounded-xl transition flex items-center justify-center space-x-1.5 shadow-sm cursor-pointer">
                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                <span>Update Order</span>
            </button>

            <!-- Right: Cancel Order (Solid Red) -->
            <button type="button" onclick="triggerCancelFromModal()" class="py-2.5 px-3 bg-[#FF3B5C] hover:bg-rose-600 text-white font-semibold text-xs rounded-xl transition flex items-center justify-center space-x-1.5 shadow-sm cursor-pointer">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                <span>Cancel Order</span>
            </button>
        </div>

    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 2: UPDATE / EDIT ORDER                             -->
<!-- ======================================================== -->
<div id="editOrderModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 transform transition-all relative">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-xl font-bold text-gray-900 tracking-tight">Update Order</h3>
                <p id="editOrderModalSub" class="text-xs text-blue-600 font-bold mt-0.5">ORD-0001</p>
            </div>
            <button type="button" onclick="closeOrderModal('editOrderModal')" class="text-gray-400 hover:text-gray-600 text-lg transition cursor-pointer">
                ✕
            </button>
        </div>

        <form id="editOrderForm" method="POST" action="" class="mt-4 space-y-3.5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Customer Name *</label>
                    <input type="text" name="customer_name" id="editCustomerName" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phone Number *</label>
                    <input type="text" name="customer_phone" id="editCustomerPhone" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="customer_email" id="editCustomerEmail" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Address</label>
                    <input type="text" name="customer_address" id="editCustomerAddress" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Service Type *</label>
                    <select name="service_type" id="editServiceType" required class="w-full px-2 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                        @foreach($availableServices as $srv)
                        <option value="{{ $srv }}">{{ $srv }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Weight (Kg) *</label>
                    <input type="number" step="0.1" name="weight" id="editWeight" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Items Count *</label>
                    <input type="number" name="items_count" id="editItemsCount" required min="1" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Order Status *</label>
                    <select name="status" id="editStatus" required class="w-full px-2 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-blue-700 focus:ring-2 focus:ring-blue-500">
                        @foreach($availableStatuses as $st)
                        <option value="{{ $st }}">{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Pickup Date</label>
                    <input type="date" name="pickup_date" id="editPickupDate" class="w-full px-2 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Delivery Date</label>
                    <input type="date" name="delivery_date" id="editDeliveryDate" class="w-full px-2 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Total Price ($) *</label>
                <input type="number" step="0.5" name="total_price" id="editTotalPrice" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-blue-600 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Order Notes</label>
                <textarea name="notes" id="editNotes" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeOrderModal('editOrderModal')" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-[#2563EB] hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 3: CREATE NEW ORDER                                -->
<!-- ======================================================== -->
<div id="createOrderModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 transform transition-all relative">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Create New Order</h3>
            <button type="button" onclick="closeOrderModal('createOrderModal')" class="text-gray-400 hover:text-gray-600 text-lg transition cursor-pointer">
                ✕
            </button>
        </div>

        <form method="POST" action="{{ route('orders.store') }}" class="mt-4 space-y-3.5">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Customer Name *</label>
                    <input type="text" name="customer_name" required placeholder="e.g. Sokha Dara" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phone Number *</label>
                    <input type="text" name="customer_phone" required placeholder="e.g. 012 345 678" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="customer_email" placeholder="customer@example.com" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Address</label>
                    <input type="text" name="customer_address" placeholder="Svay Dongkum, Siem Reap" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Service Type *</label>
                    <select name="service_type" required class="w-full px-2 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                        @foreach($availableServices as $srv)
                        <option value="{{ $srv }}">{{ $srv }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Weight (Kg) *</label>
                    <input type="number" step="0.1" name="weight" id="newOrderWeight" required value="5.0" oninput="calculateNewPrice()" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Items Count *</label>
                    <input type="number" name="items_count" required value="3" min="1" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-2 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                        @foreach($availableStatuses as $st)
                        <option value="{{ $st }}">{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Pickup Date</label>
                    <input type="date" name="pickup_date" value="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full px-2 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Delivery Date</label>
                    <input type="date" name="delivery_date" value="{{ date('Y-m-d', strtotime('+2 days')) }}" class="w-full px-2 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Total Price ($) *</label>
                    <input type="number" step="0.5" name="total_price" id="newOrderPrice" required value="12.50" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-blue-600 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Order Custom ID</label>
                    <input type="text" name="order_number" placeholder="Auto-generated if empty" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Order Notes</label>
                <textarea name="notes" rows="2" placeholder="Instructions..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeOrderModal('createOrderModal')" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-[#2563EB] hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    Create Order
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 4: CANCEL ORDER CONFIRMATION MODAL                 -->
<!-- ======================================================== -->
<div id="cancelOrderModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-gray-100">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-3 text-xl">
            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900">Cancel Laundry Order?</h3>
        <p class="text-xs text-gray-500 mt-1.5 font-medium leading-relaxed">
            Are you sure you want to cancel order <span id="cancelOrderModalNum" class="font-bold text-gray-800"></span>?<br>
            Its status will be updated to <span class="text-rose-600 font-semibold">Cancelled</span>.
        </p>

        <form id="cancelOrderForm" method="POST" action="" class="mt-5 flex items-center justify-center space-x-2.5">
            @csrf
            <button type="button" onclick="closeOrderModal('cancelOrderModal')" class="flex-1 py-2 px-3 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition cursor-pointer">
                Keep Order
            </button>
            <button type="submit" class="flex-1 py-2 px-3 bg-[#EF4444] hover:bg-rose-600 text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer">
                Yes, Cancel
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentActiveOrder = null;

    function openOrderModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        }
    }

    function closeOrderModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('hidden');
    }

    function openCreateOrderModal() {
        openOrderModal('createOrderModal');
    }

    function calculateNewPrice() {
        const weight = parseFloat(document.getElementById('newOrderWeight').value) || 0;
        document.getElementById('newOrderPrice').value = (weight * 2.5).toFixed(2);
    }

    // View Order Details
    function viewOrderDetails(orderId) {
        fetch(`{{ url('/orders') }}/${orderId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.order) {
                    alert('Order details could not be loaded.');
                    return;
                }

                const ord = data.order;
                const items = data.items || [];
                currentActiveOrder = ord;

                // Set Header details
                document.getElementById('modalOrderNumber').textContent = ord.order_number;
                const dtString = ord.order_date.includes(' ') ? ord.order_date : `${ord.order_date} 10:24 AM`;
                document.getElementById('modalOrderDateTime').textContent = `Order Date: ${dtString}`;

                // Header Status Pill
                const badge = document.getElementById('modalStatusBadge');
                badge.textContent = ord.status;
                badge.className = `px-3 py-0.5 rounded-full text-xs font-semibold ${getStatusBadgeClass(ord.status)}`;

                // Customer details
                document.getElementById('modalCustomerName').textContent = ord.customer_name;
                document.getElementById('modalCustomerPhone').textContent = ord.customer_phone || '012 345 678';
                document.getElementById('modalCustomerEmail').textContent = ord.customer_email || 'sokha@example.com';
                document.getElementById('modalCustomerAddress').textContent = ord.customer_address || 'Svay Dongkum, Siem Reap';

                // Order Specs Box
                document.getElementById('modalServiceType').textContent = ord.service_type;
                document.getElementById('modalWeight').textContent = `${parseFloat(ord.weight).toFixed(1)} kg`;
                document.getElementById('modalItemsCount').textContent = ord.items_count;
                document.getElementById('modalOrderDateVal').textContent = dtString;
                document.getElementById('modalPickupDate').textContent = ord.pickup_date ? ord.pickup_date.substring(0, 10) : '2025-09-16';
                document.getElementById('modalDeliveryDate').textContent = ord.delivery_date ? ord.delivery_date.substring(0, 10) : '2025-09-17';
                document.getElementById('modalTotalPrice').textContent = `$${parseFloat(ord.total_price).toFixed(2)}`;
                document.getElementById('modalTableSummaryTotal').textContent = `$${parseFloat(ord.total_price).toFixed(2)}`;

                // Print Receipt Link
                document.getElementById('modalPrintReceiptBtn').href = `{{ url('/orders') }}/${ord.id}/print`;

                // Stepper update
                updateStepperVisuals(ord.status);

                // Table items
                const tbody = document.getElementById('modalItemsTableBody');
                tbody.innerHTML = '';

                if (items && items.length > 0) {
                    items.forEach((it, idx) => {
                        const row = `
                        <tr>
                            <td class="py-2 px-2.5 text-blue-600 font-medium">${idx + 1}</td>
                            <td class="py-2 px-2.5 font-medium text-gray-800">${it.service_name}</td>
                            <td class="py-2 px-2.5 text-center text-gray-600">${it.quantity}</td>
                            <td class="py-2 px-2.5 text-right text-gray-600">$${parseFloat(it.unit_price).toFixed(2)}</td>
                            <td class="py-2 px-2.5 text-right font-bold text-gray-900">$${parseFloat(it.subtotal).toFixed(2)}</td>
                        </tr>
                    `;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                } else {
                    const fallback = `
                    <tr>
                        <td class="py-2 px-2.5 text-blue-600 font-medium">1</td>
                        <td class="py-2 px-2.5 font-medium text-gray-800">${ord.service_type}</td>
                        <td class="py-2 px-2.5 text-center text-gray-600">1.0 kg</td>
                        <td class="py-2 px-2.5 text-right text-gray-600">$2.50</td>
                        <td class="py-2 px-2.5 text-right font-bold text-gray-900">$2.50</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-2.5 text-blue-600 font-medium">2</td>
                        <td class="py-2 px-2.5 font-medium text-gray-800">${ord.service_type}</td>
                        <td class="py-2 px-2.5 text-center text-gray-600">2.0 kg</td>
                        <td class="py-2 px-2.5 text-right text-gray-600">$2.50</td>
                        <td class="py-2 px-2.5 text-right font-bold text-gray-900">$5.00</td>
                    </tr>
                `;
                    tbody.insertAdjacentHTML('beforeend', fallback);
                }

                openOrderModal('viewOrderModal');
            })
            .catch(err => {
                console.error(err);
                alert('Failed to load order details.');
            });
    }

    function getStatusBadgeClass(st) {
        switch (st) {
            case 'Washing':
                return 'bg-sky-100 text-sky-700';
            case 'Drying':
                return 'bg-amber-100 text-amber-700';
            case 'Ready for Pickup':
                return 'bg-purple-100 text-purple-700';
            case 'Out for Delivery':
                return 'bg-teal-100 text-teal-700';
            case 'Completed':
                return 'bg-emerald-100 text-emerald-700';
            case 'Received':
                return 'bg-gray-100 text-gray-700';
            case 'Cancelled':
                return 'bg-rose-100 text-rose-700';
            default:
                return 'bg-gray-100 text-gray-700';
        }
    }

    function updateStepperVisuals(status) {
        const stepOrder = [
            'Received',
            'Washing',
            'Drying',
            'Ironing',
            'ReadyForPickup',
            'OutForDelivery',
            'Completed'
        ];

        const statusMap = {
            'Received': 0,
            'Washing': 1,
            'Drying': 2,
            'Ironing': 3,
            'Ready for Pickup': 4,
            'ReadyForPickup': 4,
            'Out for Delivery': 5,
            'OutForDelivery': 5,
            'Completed': 6,
            'Cancelled': -1
        };

        const activeIndex = statusMap[status] !== undefined ? statusMap[status] : 1;
        const isCancelled = status === 'Cancelled';

        const cancelBox = document.getElementById('modalCancelledNotice');
        if (cancelBox) {
            if (isCancelled) cancelBox.classList.remove('hidden');
            else cancelBox.classList.add('hidden');
        }

        stepOrder.forEach((name, idx) => {
            const el = document.getElementById(`step-${name}`);
            if (!el) return;
            const circle = el.querySelector('.step-circle');
            const label = el.querySelector('span');

            if (isCancelled) {
                circle.className = 'step-circle w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xs border border-gray-200';
                label.className = 'mt-1 text-[11px] font-medium text-gray-400';
            } else if (idx < activeIndex) {
                circle.className = 'step-circle w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shadow-sm';
                circle.innerHTML = '✓';
                label.className = 'mt-1 text-[11px] font-semibold text-gray-800';
            } else if (idx === activeIndex) {
                circle.className = 'step-circle w-8 h-8 rounded-full bg-[#0066FF] text-white flex items-center justify-center font-bold text-xs shadow-sm ring-4 ring-blue-100';
                circle.innerHTML = '<i data-lucide="rotate-cw" class="w-4 h-4 animate-spin"></i>';
                label.className = 'mt-1 text-[11px] font-bold text-[#0066FF]';
            } else {
                circle.className = 'step-circle w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xs border border-gray-200';
                label.className = 'mt-1 text-[11px] font-medium text-gray-500';
            }
        });

        // Arrow 1 color
        const arr1 = document.getElementById('arrow-1');
        if (arr1) {
            arr1.className = activeIndex >= 1 ? 'text-emerald-500 font-bold text-sm mb-4' : 'text-gray-300 font-bold text-sm mb-4';
        }
        const arr2 = document.getElementById('arrow-2');
        if (arr2) {
            arr2.className = activeIndex >= 2 ? 'text-emerald-500 font-bold text-sm mb-4' : 'text-gray-300 font-bold text-sm mb-4';
        }

        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    }

    function triggerEditFromModal() {
        if (!currentActiveOrder) return;
        closeOrderModal('viewOrderModal');
        editOrder(currentActiveOrder.id);
    }

    function triggerCancelFromModal() {
        if (!currentActiveOrder) return;
        closeOrderModal('viewOrderModal');
        confirmCancelOrder(currentActiveOrder.id, currentActiveOrder.order_number);
    }

    function handleViewOrderClick(el) {
        const id = el.getAttribute('data-id');
        if (id) viewOrderDetails(id);
    }

    function handleEditOrderClick(el) {
        const id = el.getAttribute('data-id');
        if (id) editOrder(id);
    }

    function handleCancelOrderClick(el) {
        const id = el.getAttribute('data-id');
        const num = el.getAttribute('data-num') || '';
        if (id) confirmCancelOrder(id, num);
    }

    // Open Edit Order Modal
    function editOrder(orderId) {
        fetch(`{{ url('/orders') }}/${orderId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.order) {
                    alert('Order details could not be loaded for editing.');
                    return;
                }

                const ord = data.order;
                const form = document.getElementById('editOrderForm');
                form.action = `{{ url('/orders') }}/${ord.id}`;

                document.getElementById('editOrderModalSub').textContent = ord.order_number;
                document.getElementById('editCustomerName').value = ord.customer_name;
                document.getElementById('editCustomerPhone').value = ord.customer_phone || '';
                document.getElementById('editCustomerEmail').value = ord.customer_email || '';
                document.getElementById('editCustomerAddress').value = ord.customer_address || '';
                document.getElementById('editServiceType').value = ord.service_type;
                document.getElementById('editWeight').value = ord.weight;
                document.getElementById('editItemsCount').value = ord.items_count;
                document.getElementById('editStatus').value = ord.status;
                document.getElementById('editPickupDate').value = ord.pickup_date ? ord.pickup_date.substring(0, 10) : '';
                document.getElementById('editDeliveryDate').value = ord.delivery_date ? ord.delivery_date.substring(0, 10) : '';
                document.getElementById('editTotalPrice').value = ord.total_price;
                document.getElementById('editNotes').value = ord.notes || '';

                openOrderModal('editOrderModal');
            })
            .catch(err => {
                console.error(err);
                alert('Failed to load order for editing.');
            });
    }

    // Open Cancel Order Modal
    function confirmCancelOrder(orderId, orderNum) {
        document.getElementById('cancelOrderModalNum').textContent = orderNum;
        document.getElementById('cancelOrderForm').action = `{{ url('/orders') }}/${orderId}/cancel`;
        openOrderModal('cancelOrderModal');
    }

    // Modal background click close
    document.addEventListener('click', function(e) {
        ['viewOrderModal', 'createOrderModal', 'editOrderModal', 'cancelOrderModal'].forEach(id => {
            const m = document.getElementById(id);
            if (m && e.target === m) {
                closeOrderModal(id);
            }
        });
    });

    // Esc key close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['viewOrderModal', 'createOrderModal', 'editOrderModal', 'cancelOrderModal'].forEach(id => {
                closeOrderModal(id);
            });
        }
    });
</script>
@endpush