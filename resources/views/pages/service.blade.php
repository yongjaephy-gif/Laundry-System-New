@extends('layouts.navbar')

@section('title', 'Services')

@section('content')
@php
// Service types configuration matching screenshot styling and SVG icons
$typeConfigs = [
'Wash Only' => [
'key' => 'Wash Only',
'name' => 'Wash Only',
'color' => '#2563EB',
'bg_light' => 'bg-[#EFF6FF]',
'border' => 'border-[#BFDBFE]',
'text' => 'text-[#2563EB]',
'active_bg' => 'bg-[#2563EB] text-white border-[#2563EB] shadow-md shadow-blue-500/20',
'badge_bg' => 'bg-blue-50 text-blue-600',
'icon_svg' => '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <rect x="3" y="3" width="18" height="18" rx="4" />
    <circle cx="12" cy="13" r="5" />
    <path d="M12 10a3 3 0 0 0 0 6" />
    <circle cx="7" cy="7" r="1" fill="currentColor" />
</svg>'
],
'Wash & Dry' => [
'key' => 'Wash & Dry',
'name' => 'Wash & Dry',
'color' => '#16A34A',
'bg_light' => 'bg-[#F0FDF4]',
'border' => 'border-[#BBF7D0]',
'text' => 'text-[#16A34A]',
'active_bg' => 'bg-[#16A34A] text-white border-[#16A34A] shadow-md shadow-green-500/20',
'badge_bg' => 'bg-emerald-50 text-emerald-600',
'icon_svg' => '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z" />
</svg>'
],
'Wash, Dry & Fold' => [
'key' => 'Wash, Dry & Fold',
'name' => 'Wash, Dry & Fold',
'color' => '#9333EA',
'bg_light' => 'bg-[#FAF5FF]',
'border' => 'border-[#E9D5FF]',
'text' => 'text-[#9333EA]',
'active_bg' => 'bg-[#9333EA] text-white border-[#9333EA] shadow-md shadow-purple-500/20',
'badge_bg' => 'bg-purple-50 text-purple-600',
'icon_svg' => '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z" />
    <path d="m22 12.5-8.58 3.91a2 2 0 0 1-1.66 0L2 12.5" />
    <path d="m22 17.5-8.58 3.91a2 2 0 0 1-1.66 0L2 17.5" />
</svg>'
],
'Dry Cleaning' => [
'key' => 'Dry Cleaning',
'name' => 'Dry Cleaning',
'color' => '#E11D48',
'bg_light' => 'bg-[#FFF1F2]',
'border' => 'border-[#FECDD3]',
'text' => 'text-[#E11D48]',
'active_bg' => 'bg-[#E11D48] text-white border-[#E11D48] shadow-md shadow-rose-500/20',
'badge_bg' => 'bg-rose-50 text-rose-600',
'icon_svg' => '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 2a3 3 0 0 0-3 3c0 .8.3 1.5.8 2.1L2.6 15.6A2 2 0 0 0 4 19h16a2 2 0 0 0 1.4-3.4L14.2 7.1c.5-.6.8-1.3.8-2.1a3 3 0 0 0-3-3Z" />
</svg>'
],
'Ironing' => [
'key' => 'Ironing',
'name' => 'Ironing',
'color' => '#D97706',
'bg_light' => 'bg-[#FEFCE8]',
'border' => 'border-[#FEF08A]',
'text' => 'text-[#D97706]',
'active_bg' => 'bg-[#D97706] text-white border-[#D97706] shadow-md shadow-amber-500/20',
'badge_bg' => 'bg-amber-50 text-amber-600',
'icon_svg' => '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M4 18h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2H9L4 13v3a2 2 0 0 0 2 2Z" />
    <path d="M16 7V4a2 2 0 0 0-2-2h-3a2 2 0 0 0-2 2v3" />
</svg>'
],
'Blanket Cleaning' => [
'key' => 'Blanket Cleaning',
'name' => 'Blanket Cleaning',
'color' => '#0D9488',
'bg_light' => 'bg-[#F0FDFA]',
'border' => 'border-[#99F6E4]',
'text' => 'text-[#0D9488]',
'active_bg' => 'bg-[#0D9488] text-white border-[#0D9488] shadow-md shadow-teal-500/20',
'badge_bg' => 'bg-teal-50 text-teal-600',
'icon_svg' => '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <rect x="3" y="6" width="18" height="12" rx="2" />
    <line x1="3" y1="11" x2="21" y2="11" />
    <line x1="7" y1="6" x2="7" y2="11" />
    <line x1="17" y1="6" x2="17" y2="11" />
</svg>'
],
'Curtain Cleaning' => [
'key' => 'Curtain Cleaning',
'name' => 'Curtain Cleaning',
'color' => '#7C3AED',
'bg_light' => 'bg-[#FAF5FF]',
'border' => 'border-[#DDD6FE]',
'text' => 'text-[#7C3AED]',
'active_bg' => 'bg-[#7C3AED] text-white border-[#7C3AED] shadow-md shadow-violet-500/20',
'badge_bg' => 'bg-violet-50 text-violet-600',
'icon_svg' => '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M3 4h18" />
    <path d="M4 4v16" />
    <path d="M20 4v16" />
    <path d="M9 4c0 4-3 6-3 10v6" />
    <path d="M15 4c0 4 3 6 3 10v6" />
</svg>'
],
'Shoe Cleaning' => [
'key' => 'Shoe Cleaning',
'name' => 'Shoe Cleaning',
'color' => '#0284C7',
'bg_light' => 'bg-[#F0F9FF]',
'border' => 'border-[#BAE6FD]',
'text' => 'text-[#0284C7]',
'active_bg' => 'bg-[#0284C7] text-white border-[#0284C7] shadow-md shadow-sky-500/20',
'badge_bg' => 'bg-sky-50 text-sky-600',
'icon_svg' => '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M3 14h18v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4Z" />
    <path d="M6 14V8a3 3 0 0 1 3-3h1a3 3 0 0 1 3 3v6" />
</svg>'
],
'Carpet Cleaning' => [
'key' => 'Carpet Cleaning',
'name' => 'Carpet Cleaning',
'color' => '#16A34A',
'bg_light' => 'bg-[#F0FDF4]',
'border' => 'border-[#BBF7D0]',
'text' => 'text-[#16A34A]',
'active_bg' => 'bg-[#16A34A] text-white border-[#16A34A] shadow-md shadow-green-500/20',
'badge_bg' => 'bg-emerald-50 text-emerald-600',
'icon_svg' => '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <rect x="3" y="4" width="18" height="16" rx="2" />
    <rect x="7" y="8" width="10" height="8" rx="1" />
    <circle cx="12" cy="12" r="1.5" fill="currentColor" />
</svg>'
],
];

// Helper closure to lookup type configuration
$getTypeConfig = function($typeName) use ($typeConfigs) {
return $typeConfigs[$typeName] ?? $typeConfigs['Wash Only'];
};
@endphp

<div class="w-full mx-auto space-y-6">

    <!-- Flash Notifications -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm animate-fade-in">
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
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-sm animate-fade-in">
        <div class="flex items-center space-x-3">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-base leading-none cursor-pointer">&times;</button>
    </div>
    @endif

    <!-- Top Action Row: Add New Service Button -->
    <div class="flex justify-end items-center">
        <button type="button"
            onclick="openAddServiceModal()"
            class="bg-[#2563EB] hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:shadow transition-all duration-150 inline-flex items-center space-x-2 text-sm cursor-pointer group">
            <i class="fa-solid fa-plus text-xs group-hover:rotate-90 transition-transform duration-200"></i>
            <span>Add New Service</span>
        </button>
    </div>

    <!-- 9 Service Types Cards Filter Grid (Matching Screenshot 1) -->
    <div class="space-y-3.5">
        <!-- Top Row (5 cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4">
            @foreach(array_slice($typeConfigs, 0, 5, true) as $key => $cfg)
            @php
            // Check if this type is currently the selected filter
            $isSelected = ($selectedType === $key) || (empty($selectedType) && $key === 'Wash Only' && !request()->has('type') && !request()->has('search'));
            @endphp
            <button type="button"
                data-type="{{ $key }}"
                onclick="handleFilterTypeClick(this)"
                class="p-4 sm:p-5 rounded-2xl border transition-all duration-200 flex flex-col items-center justify-center space-y-2 text-center cursor-pointer min-h-[96px] {{ $isSelected ? $cfg['active_bg'] : $cfg['bg_light'] . ' ' . $cfg['border'] . ' ' . $cfg['text'] . ' hover:shadow-sm hover:scale-[1.01]' }}">
                <div class="flex items-center justify-center">
                    {!! $cfg['icon_svg'] !!}
                </div>
                <span class="text-xs sm:text-sm font-bold tracking-tight leading-tight">{{ $cfg['name'] }}</span>
            </button>
            @endforeach
        </div>

        <!-- Bottom Row (4 cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
            @foreach(array_slice($typeConfigs, 5, 4, true) as $key => $cfg)
            @php
            $isSelected = ($selectedType === $key);
            @endphp
            <button type="button"
                data-type="{{ $key }}"
                onclick="handleFilterTypeClick(this)"
                class="p-4 sm:p-5 rounded-2xl border transition-all duration-200 flex flex-col items-center justify-center space-y-2 text-center cursor-pointer min-h-[96px] {{ $isSelected ? $cfg['active_bg'] : $cfg['bg_light'] . ' ' . $cfg['border'] . ' ' . $cfg['text'] . ' hover:shadow-sm hover:scale-[1.01]' }}">
                <div class="flex items-center justify-center">
                    {!! $cfg['icon_svg'] !!}
                </div>
                <span class="text-xs sm:text-sm font-bold tracking-tight leading-tight">{{ $cfg['name'] }}</span>
            </button>
            @endforeach
        </div>
    </div>

    <!-- Search & Filter Bar (Matching Screenshot 1) -->
    <form id="serviceFilterForm" method="GET" action="{{ route('services.index') }}" class="bg-white p-3 sm:p-3.5 rounded-2xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex flex-col md:flex-row items-center gap-3">
        <!-- Search service name input -->
        <div class="relative flex-1 w-full">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </span>
            <input type="text"
                name="search"
                id="searchInput"
                value="{{ $search ?? '' }}"
                placeholder="Search service name..."
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
        </div>

        <!-- Service Type Dropdown Select -->
        <div class="w-full md:w-64">
            <div class="relative">
                <select name="type"
                    id="typeFilterSelect"
                    onchange="document.getElementById('serviceFilterForm').submit()"
                    class="w-full pl-4 pr-10 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-sm text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer">
                    <option value="">All Service Types</option>
                    @foreach($serviceTypes as $t)
                    <option value="{{ $t }}" {{ ($selectedType ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>
        </div>

        <!-- Action Buttons: Search & Reset -->
        <div class="flex items-center space-x-2 w-full md:w-auto">
            <button type="submit"
                class="flex-1 md:flex-none px-5 py-2.5 bg-[#2563EB] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold inline-flex items-center justify-center space-x-2 shadow-sm transition cursor-pointer">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Search</span>
            </button>
            <a href="{{ route('services.index') }}"
                class="flex-1 md:flex-none px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-sm font-semibold inline-flex items-center justify-center space-x-1.5 transition cursor-pointer">
                <i class="fa-solid fa-rotate-right text-xs text-gray-500"></i>
                <span>Reset</span>
            </a>
        </div>
    </form>

    <!-- Service List Table Container (Matching Screenshot 1) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-[0_2px_15px_rgba(0,0,0,0.03)] overflow-hidden">

        <!-- Table Header Title & Counter Badge -->
        <div class="p-5 sm:p-6 pb-3 sm:pb-4 flex items-center space-x-3">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight">Service List</h2>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                {{ $totalServices ?? (is_countable($services) ? count($services) : 0) }} services
            </span>
        </div>

        <!-- Responsive Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-[12px] font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6 w-12 text-center">#</th>
                        <th class="py-3.5 px-6">Service Name</th>
                        <th class="py-3.5 px-6">Service Type</th>
                        <th class="py-3.5 px-6">Price/Kg</th>
                        <th class="py-3.5 px-6">Price/Item</th>
                        <th class="py-3.5 px-6">Est. Time</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($services as $index => $service)
                    @php
                    $cfg = $getTypeConfig($service->service_type ?? $service->name ?? 'Wash Only');
                    $priceKg = isset($service->price_per_kg) && $service->price_per_kg !== null ? number_format((float)$service->price_per_kg, 2) : (isset($service->price) ? number_format((float)$service->price, 2) : '1.50');
                    $priceItem = (isset($service->price_per_item) && $service->price_per_item !== null && (float)$service->price_per_item > 0) ? '$' . number_format((float)$service->price_per_item, 2) : '-';
                    $estTime = $service->estimated_time ?? ($service->estimated_completion_hrs ? ($service->estimated_completion_hrs <= 24 ? '1–2 days' : '2–3 days' ) : '1–2 days' );
                        $status=$service->status ?? 'Active';
                        $rowNum = method_exists($services, 'firstItem') && $services->firstItem() ? ($services->firstItem() + $index) : ($index + 1);
                        @endphp
                        <tr class="hover:bg-blue-50/20 transition-colors duration-150">
                            <!-- # Row Index -->
                            <td class="py-4 px-6 text-center text-gray-400 font-medium">
                                {{ $rowNum }}
                            </td>

                            <!-- Service Name (Icon Box + Text) -->
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 {{ $cfg['bg_light'] }} {{ $cfg['text'] }} border {{ $cfg['border'] }}">
                                        <div class="scale-90 flex items-center justify-center">
                                            {!! $cfg['icon_svg'] !!}
                                        </div>
                                    </div>
                                    <span class="font-bold text-gray-900 text-sm leading-tight">{{ $service->name ?? $service->service_name }}</span>
                                </div>
                            </td>

                            <!-- Service Type Badge -->
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $cfg['badge_bg'] }}">
                                    {{ $service->service_type ?? $service->name ?? 'Wash Only' }}
                                </span>
                            </td>

                            <!-- Price/Kg -->
                            <td class="py-4 px-6 font-bold text-gray-900">
                                ${{ $priceKg }}
                            </td>

                            <!-- Price/Item -->
                            <td class="py-4 px-6 text-gray-400 font-medium">
                                {{ $priceItem }}
                            </td>

                            <!-- Est. Time -->
                            <td class="py-4 px-6 text-gray-600 font-medium text-sm">
                                {{ $estTime }}
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ strtolower($status) === 'inactive' ? 'bg-gray-100 text-gray-600' : 'bg-emerald-50 text-emerald-600' }}">
                                    {{ $status }}
                                </span>
                            </td>

                            <!-- Action Buttons: Pencil (Edit) & Trash (Delete) -->
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Blue Pencil Edit Button (pencil can edit) -->
                                    <button type="button"
                                        data-service='@json($service)'
                                        onclick="openEditServiceModal(JSON.parse(this.getAttribute('data-service')))"
                                        class="w-8 h-8 rounded-lg bg-[#10B981] hover:bg-emerald-600 text-white inline-flex items-center justify-center transition shadow-sm cursor-pointer"
                                        title="Edit Service">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>

                                    <!-- Red Trash Delete Button (trush can delete) -->
                                    <button type="button"
                                        data-id="{{ $service->id ?? ($service->service_id ?? 0) }}"
                                        data-name="{{ $service->name ?? ($service->service_name ?? 'Service') }}"
                                        onclick="openDeleteServiceModal(this.getAttribute('data-id'), this.getAttribute('data-name'))"
                                        class="w-8 h-8 rounded-lg bg-rose-500 hover:bg-rose-600 text-white inline-flex items-center justify-center transition shadow-sm cursor-pointer"
                                        title="Delete Service">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 px-6 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                        <i class="fa-solid fa-ban text-lg"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-600">No services found matching your criteria.</p>
                                    <a href="{{ route('services.index') }}" class="text-xs text-blue-600 hover:underline">Reset Filters</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer: Pagination & Service Count Range -->
        <div class="p-4 sm:p-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm text-gray-500">
            <div>
                @if(method_exists($services, 'total') && $services->total() > 0)
                Showing {{ $services->firstItem() }} – {{ $services->lastItem() }} of {{ $services->total() }} services
                @else
                Showing 1 – {{ count($services) }} of {{ count($services) }} services
                @endif
            </div>

            <!-- Pagination Links -->
            <div>
                @if(method_exists($services, 'links'))
                {{ $services->links('pagination::tailwind') }}
                @else
                <div class="inline-flex items-center space-x-1">
                    <button class="w-8 h-8 rounded-lg border border-gray-200 text-gray-400 inline-flex items-center justify-center cursor-not-allowed" disabled>
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <button class="w-8 h-8 rounded-lg bg-[#2563EB] text-white font-bold inline-flex items-center justify-center text-xs">
                        1
                    </button>
                    <button class="w-8 h-8 rounded-lg border border-gray-200 text-gray-400 inline-flex items-center justify-center cursor-not-allowed" disabled>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: ADD / EDIT SERVICE (Screenshot 2)   -->
<!-- ========================================== -->
<div id="serviceModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all animate-scale-up max-h-[92vh] flex flex-col">

        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 tracking-tight">Add New Service</h3>
            <button type="button" onclick="closeServiceModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Modal Form Body -->
        <form id="serviceForm" method="POST" action="{{ route('services.store') }}" class="p-6 space-y-5 overflow-y-auto flex-1">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="service_type" id="serviceTypeInput" value="Wash Only">

            <!-- 1. Service Type Section (3x3 grid matching Screenshot 2) -->
            <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">Service Type</label>
                <div class="grid grid-cols-3 gap-2.5 sm:gap-3">
                    @foreach($typeConfigs as $key => $cfg)
                    <button type="button"
                        data-type="{{ $key }}"
                        onclick="handleSelectModalTypeClick(this)"
                        id="modal-type-btn-{{ Str::slug($key) }}"
                        class="modal-type-btn p-3 rounded-2xl border transition-all duration-150 flex flex-col items-center justify-center space-y-1.5 text-center cursor-pointer min-h-[82px] {{ $key === 'Wash Only' ? 'border-2 border-blue-500 bg-blue-50/50 ' . $cfg['text'] : $cfg['bg_light'] . ' ' . $cfg['border'] . ' ' . $cfg['text'] . ' hover:border-gray-300' }}">
                        <div class="flex items-center justify-center scale-90">
                            {!! $cfg['icon_svg'] !!}
                        </div>
                        <span class="text-[11px] sm:text-xs font-bold leading-tight">{{ $cfg['name'] }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- 2. Service Information Section -->
            <div class="space-y-4 pt-1">
                <h4 class="text-sm font-bold text-gray-900 tracking-tight">Service Information</h4>

                <!-- Service Name Field -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                        Service Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                        name="name"
                        id="serviceNameInput"
                        required
                        placeholder="Enter service name"
                        class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <!-- Price per Kg & Price per Item (2-Column Grid) -->
                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <!-- Price per Kg -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">
                            Price per Kg <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <input type="number"
                                step="0.01"
                                min="0"
                                name="price_per_kg"
                                id="pricePerKgInput"
                                required
                                placeholder="e.g. 1.50"
                                class="w-full pl-3.5 pr-8 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            <span class="absolute right-3.5 text-gray-400 font-semibold text-sm pointer-events-none">$</span>
                        </div>
                    </div>

                    <!-- Price per Item -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">
                            Price per Item
                        </label>
                        <div class="relative flex items-center">
                            <input type="number"
                                step="0.01"
                                min="0"
                                name="price_per_item"
                                id="pricePerItemInput"
                                placeholder="e.g. 5.00"
                                class="w-full pl-3.5 pr-8 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            <span class="absolute right-3.5 text-gray-400 font-semibold text-sm pointer-events-none">$</span>
                        </div>
                    </div>
                </div>

                <!-- Estimated Completion Time -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                        Estimated Completion Time <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="estimated_time"
                            id="estimatedTimeInput"
                            required
                            class="w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer">
                            <option value="" disabled selected>Select time</option>
                            <option value="Same day">Same day (4–6 hours)</option>
                            <option value="24 hours">24 hours (1 day)</option>
                            <option value="1–2 days">1–2 days</option>
                            <option value="2–3 days">2–3 days</option>
                            <option value="2–4 days">2–4 days</option>
                            <option value="3–5 days">3–5 days</option>
                            <option value="5–7 days">5–7 days</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Description Field with 0/500 Character Counter -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                        Description <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description"
                        id="descriptionInput"
                        rows="3"
                        maxlength="500"
                        required
                        oninput="updateCharCount()"
                        placeholder="Enter service description..."
                        class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition resize-none"></textarea>
                    <div class="text-right text-[11px] text-gray-400 mt-1 font-medium">
                        <span id="charCount">0</span>/500
                    </div>
                </div>

                <!-- Status (Active / Inactive) -->
                <div class="hidden" id="statusContainer">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Status</label>
                    <select name="status" id="statusInput" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Modal Footer Buttons -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                <button type="button"
                    onclick="closeServiceModal()"
                    class="flex-1 py-2.5 px-4 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 px-4 bg-[#2563EB] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold inline-flex items-center justify-center space-x-2 shadow-sm transition cursor-pointer">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span id="submitBtnText">Save Service</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: DELETE CONFIRMATION                 -->
<!-- ========================================== -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden p-6 transform transition-all animate-scale-up space-y-4">

        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 leading-tight">Delete Service</h3>
                <p class="text-xs text-gray-500 mt-0.5">Are you sure you want to remove this service?</p>
            </div>
        </div>

        <p class="text-sm text-gray-600 bg-gray-50 p-3.5 rounded-xl border border-gray-100">
            You are about to delete <span id="deleteServiceName" class="font-bold text-gray-900"></span>. This action cannot be undone.
        </p>

        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-end space-x-2.5 pt-2">
                <button type="button"
                    onclick="closeDeleteModal()"
                    class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-semibold inline-flex items-center space-x-1.5 shadow-sm transition cursor-pointer">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Delete Service</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT CONTROLLERS & INTERACTIONS      -->
<script id="type-configs-data" type="application/json">
    @json($typeConfigs)
</script>
<script>
    // Configuration dictionary for type configs
    const TYPE_CONFIGS = JSON.parse(document.getElementById('type-configs-data').textContent || '{}');

    function handleFilterTypeClick(el) {
        const type = el.getAttribute('data-type');
        if (type) filterByType(type);
    }

    function handleSelectModalTypeClick(el) {
        const type = el.getAttribute('data-type');
        if (type) selectModalType(type);
    }

    // Filter table by clicking category cards at the top
    function filterByType(type) {
        const select = document.getElementById('typeFilterSelect');
        const form = document.getElementById('serviceFilterForm');

        // If already selected, reset to all
        if (select.value === type) {
            select.value = '';
        } else {
            select.value = type;
        }
        form.submit();
    }

    // Modal Type Selector in Add/Edit Modal
    function selectModalType(typeName) {
        document.getElementById('serviceTypeInput').value = typeName;

        // Remove active state from all type buttons
        document.querySelectorAll('.modal-type-btn').forEach(btn => {
            btn.classList.remove('border-2', 'border-blue-500', 'bg-blue-50/50');
        });

        // Add active state to selected button
        const slug = typeName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        const activeBtn = document.getElementById('modal-type-btn-' + slug);
        if (activeBtn) {
            activeBtn.classList.add('border-2', 'border-blue-500', 'bg-blue-50/50');
        }

        // Auto-fill Service Name if currently empty or matching default types
        const nameInput = document.getElementById('serviceNameInput');
        if (!nameInput.value.trim() || Object.keys(TYPE_CONFIGS).includes(nameInput.value.trim())) {
            nameInput.value = typeName;
        }
    }

    // Update description character counter (0/500)
    function updateCharCount() {
        const textarea = document.getElementById('descriptionInput');
        const countSpan = document.getElementById('charCount');
        if (textarea && countSpan) {
            countSpan.textContent = textarea.value.length;
        }
    }

    // Open Add Service Modal
    function openAddServiceModal() {
        const modal = document.getElementById('serviceModal');
        const form = document.getElementById('serviceForm');

        document.getElementById('modalTitle').textContent = 'Add New Service';
        document.getElementById('submitBtnText').textContent = 'Save Service';
        document.getElementById('formMethod').value = 'POST';
        form.action = "{{ route('services.store') }}";

        // Reset form inputs
        form.reset();
        document.getElementById('serviceNameInput').value = 'Wash Only';
        document.getElementById('pricePerKgInput').value = '1.50';
        document.getElementById('pricePerItemInput').value = '';
        document.getElementById('estimatedTimeInput').value = '1–2 days';
        document.getElementById('descriptionInput').value = '';
        document.getElementById('statusContainer').classList.add('hidden');

        selectModalType('Wash Only');
        updateCharCount();

        modal.classList.remove('hidden');
    }

    // Open Edit Service Modal (pencil can edit)
    function openEditServiceModal(service) {
        const modal = document.getElementById('serviceModal');
        const form = document.getElementById('serviceForm');

        document.getElementById('modalTitle').textContent = 'Edit Service';
        document.getElementById('submitBtnText').textContent = 'Update Service';
        document.getElementById('formMethod').value = 'PUT';

        const serviceId = service.id || service.service_id;
        form.action = `/services/${serviceId}`;

        const serviceType = service.service_type || service.name || 'Wash Only';
        selectModalType(serviceType);

        document.getElementById('serviceNameInput').value = service.name || service.service_name || '';
        document.getElementById('pricePerKgInput').value = service.price_per_kg !== undefined && service.price_per_kg !== null ? service.price_per_kg : (service.price || '');
        document.getElementById('pricePerItemInput').value = service.price_per_item || '';

        // Estimated completion time
        const estTimeSelect = document.getElementById('estimatedTimeInput');
        if (service.estimated_time) {
            estTimeSelect.value = service.estimated_time;
        } else {
            estTimeSelect.value = '1–2 days';
        }

        document.getElementById('descriptionInput').value = service.description || '';
        document.getElementById('statusContainer').classList.remove('hidden');
        document.getElementById('statusInput').value = service.status || 'Active';

        updateCharCount();
        modal.classList.remove('hidden');
    }

    // Close Service Modal
    function closeServiceModal() {
        document.getElementById('serviceModal').classList.add('hidden');
    }

    // Open Delete Service Modal (trush can delete)
    function openDeleteServiceModal(id, name) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');

        document.getElementById('deleteServiceName').textContent = `"${name}"`;
        form.action = `/services/${id}`;

        modal.classList.remove('hidden');
    }

    // Close Delete Modal
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modals on clicking outside or ESC key
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeServiceModal();
            closeDeleteModal();
        }
    });

    document.getElementById('serviceModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeServiceModal();
        }
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endsection