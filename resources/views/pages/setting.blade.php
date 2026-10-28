@extends('layouts.navbar')

@section('title', 'Settings')

@section('content')
<div class="space-y-6 w-full mx-auto">

    <!-- Flash Toast Notification -->
    <div id="settingsToast" class="{{ session('success') ? '' : 'hidden' }} p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs animate-fade-in">
        <div class="flex items-center space-x-3">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span class="font-medium" id="settingsToastMsg">{{ session('success') ?? 'Settings updated successfully!' }}</span>
        </div>
        <button type="button" onclick="document.getElementById('settingsToast').classList.add('hidden')" class="text-emerald-600 hover:text-emerald-800 text-lg leading-none cursor-pointer">&times;</button>
    </div>

    <!-- MAIN 6 CARDS GRID (3 Columns across, 2 Rows matching media_1791127288491.png) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-start">

        <!-- ======================================================== -->
        <!-- COLUMN 1, ROW 1: LAUNDRY INFORMATION                     -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100/90 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.03)] p-6 sm:p-7 flex flex-col justify-between space-y-5">
            <div>
                <!-- Card Header -->
                <div class="flex items-center space-x-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-circle-info text-base text-[#2563EB]"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Laundry Information</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Update your laundry business details</p>
                    </div>
                </div>

                <!-- Form -->
                <form id="formLaundryInfo" action="{{ route('settings.update-info') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Laundry Name -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Laundry Name</label>
                        <input 
                            type="text" 
                            name="laundry_name" 
                            value="{{ $settings['laundry_name'] ?? 'Clean & Fresh Laundry' }}" 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                            placeholder="Enter laundry name"
                        >
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone Number</label>
                        <input 
                            type="text" 
                            name="phone_number" 
                            value="{{ $settings['phone_number'] ?? '012 345 678' }}" 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                            placeholder="e.g. 012 345 678"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ $settings['email'] ?? 'info@cleanfresh.com' }}" 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                            placeholder="info@cleanfresh.com"
                        >
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Address</label>
                        <textarea 
                            name="address" 
                            rows="3" 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition resize-none leading-relaxed"
                            placeholder="Enter business address"
                        >{{ $settings['address'] ?? '#123, Street 456, Sangkat Boeung Kak, Khan Toul Kork, Phnom Penh, Cambodia' }}</textarea>
                    </div>

                    <!-- Logo -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Logo</label>
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl border border-gray-200 flex items-center justify-center bg-gray-50/60 overflow-hidden flex-shrink-0">
                                <i class="fa-solid fa-camera text-gray-400 text-lg"></i>
                            </div>

                            <label class="cursor-pointer">
                                <span class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 transition flex items-center space-x-1.5">
                                    <i class="fa-solid fa-arrow-up-from-bracket text-xs text-gray-500"></i>
                                    <span>Change Logo</span>
                                </span>
                                <input type="file" name="logo" class="hidden" accept="image/*" onchange="showToast('Logo selected!')">
                            </label>
                        </div>
                    </div>

                    <!-- Save Changes Button -->
                    <div class="pt-2 text-right">
                        <button 
                            type="submit" 
                            class="bg-[#2563EB] hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-xl text-xs sm:text-sm shadow-xs transition active:scale-95 cursor-pointer inline-flex items-center space-x-1.5"
                        >
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- COLUMN 2, ROW 1: BUSINESS HOURS                          -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100/90 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.03)] p-6 sm:p-7 flex flex-col justify-between space-y-5">
            <div>
                <!-- Card Header -->
                <div class="flex items-center space-x-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-regular fa-clock text-base text-[#10B981]"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Business Hours</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Set your laundry business operating hours</p>
                    </div>
                </div>

                <!-- Form -->
                <form id="formBusinessHours" action="{{ route('settings.update-hours') }}" method="POST" class="space-y-3.5">
                    @csrf

                    @php
                        $days = [
                            'monday' => 'Monday',
                            'tuesday' => 'Tuesday',
                            'wednesday' => 'Wednesday',
                            'thursday' => 'Thursday',
                            'friday' => 'Friday',
                            'saturday' => 'Saturday',
                            'sunday' => 'Sunday',
                        ];
                    @endphp

                    @foreach($days as $key => $label)
                        @php
                            $dayData = $settings["hours_{$key}"] ?? ['from' => '07:00 AM', 'to' => '07:00 PM', 'active' => ($key !== 'sunday')];
                            $isActive = !empty($dayData['active']);
                            $isSunday = ($key === 'sunday');
                        @endphp

                        <div class="flex items-center justify-between gap-2 py-1 text-xs">
                            <!-- Day Name -->
                            <span class="w-20 font-medium text-gray-700">{{ $label }}</span>

                            <!-- Time Selectors / Closed Select -->
                            <div class="flex-1 flex items-center justify-center space-x-1.5" id="container-{{ $key }}">
                                @if($isSunday)
                                    <div class="w-full">
                                        <select 
                                            name="hours_{{ $key }}_from" 
                                            id="select-{{ $key }}" 
                                            class="w-full px-3 py-1.5 bg-gray-50/60 border border-gray-200 rounded-lg text-xs text-gray-600 focus:outline-none cursor-pointer"
                                        >
                                            <option value="Closed" {{ ($dayData['from'] ?? '') === 'Closed' ? 'selected' : '' }}>Closed</option>
                                            <option value="08:00 AM - 05:00 PM">08:00 AM - 05:00 PM</option>
                                            <option value="07:00 AM - 07:00 PM">07:00 AM - 07:00 PM</option>
                                        </select>
                                        <input type="hidden" name="hours_{{ $key }}_to" value="">
                                    </div>
                                @else
                                    <input 
                                        type="text" 
                                        name="hours_{{ $key }}_from" 
                                        value="{{ $dayData['from'] ?? '07:00 AM' }}" 
                                        class="w-24 px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs text-center text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    >
                                    <span class="text-gray-400 font-medium">-</span>
                                    <input 
                                        type="text" 
                                        name="hours_{{ $key }}_to" 
                                        value="{{ $dayData['to'] ?? '07:00 PM' }}" 
                                        class="w-24 px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs text-center text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    >
                                @endif
                            </div>

                            <!-- Toggle Switch -->
                            <div class="flex items-center pl-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="hours_{{ $key }}_active" 
                                        value="1" 
                                        class="sr-only peer" 
                                        {{ $isActive ? 'checked' : '' }}
                                        onchange="toggleDayRow('{{ $key }}', this.checked)"
                                    >
                                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#2563EB]"></div>
                                </label>
                            </div>
                        </div>
                    @endforeach

                    <!-- Save Changes Button -->
                    <div class="pt-3 text-right">
                        <button 
                            type="submit" 
                            class="bg-[#2563EB] hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-xl text-xs sm:text-sm shadow-xs transition active:scale-95 cursor-pointer inline-flex items-center space-x-1.5"
                        >
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- COLUMN 3, ROW 1: PRICING CONFIGURATION                   -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100/90 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.03)] p-6 sm:p-7 flex flex-col justify-between space-y-5">
            <div>
                <!-- Card Header -->
                <div class="flex items-center space-x-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-tag text-base text-[#F97316]"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Pricing Configuration</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Manage your service prices</p>
                    </div>
                </div>

                <!-- Form -->
                <form id="formPricing" action="{{ route('settings.update-pricing') }}" method="POST" class="space-y-3.5">
                    @csrf

                    <!-- Pricing Table Headers -->
                    <div class="flex items-center justify-between text-xs font-semibold text-gray-500 pb-2 border-b border-gray-100">
                        <span class="w-2/5">Service</span>
                        <span class="w-1/3">Unit</span>
                        <span class="w-1/4 text-right">Price (USD)</span>
                    </div>

                    <!-- Row 1: Wash & Fold -->
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="w-2/5 font-medium text-gray-800">Wash &amp; Fold</span>
                        <span class="w-1/3 text-gray-500">Per Kg</span>
                        <div class="w-1/4 text-right">
                            <input 
                                type="number" 
                                step="0.01" 
                                name="pricing_wash_fold" 
                                value="{{ $settings['pricing_wash_fold'] ?? '1.50' }}" 
                                class="w-20 px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-right text-gray-800 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <!-- Row 2: Ironing -->
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="w-2/5 font-medium text-gray-800">Ironing</span>
                        <span class="w-1/3 text-gray-500">Per Item</span>
                        <div class="w-1/4 text-right">
                            <input 
                                type="number" 
                                step="0.01" 
                                name="pricing_ironing" 
                                value="{{ $settings['pricing_ironing'] ?? '0.80' }}" 
                                class="w-20 px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-right text-gray-800 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <!-- Row 3: Dry Cleaning -->
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="w-2/5 font-medium text-gray-800">Dry Cleaning</span>
                        <span class="w-1/3 text-gray-500">Per Item</span>
                        <div class="w-1/4 text-right">
                            <input 
                                type="number" 
                                step="0.01" 
                                name="pricing_dry_cleaning" 
                                value="{{ $settings['pricing_dry_cleaning'] ?? '3.00' }}" 
                                class="w-20 px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-right text-gray-800 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <!-- Row 4: Blanket -->
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="w-2/5 font-medium text-gray-800">Blanket</span>
                        <span class="w-1/3 text-gray-500">Per Piece</span>
                        <div class="w-1/4 text-right">
                            <input 
                                type="number" 
                                step="0.01" 
                                name="pricing_blanket" 
                                value="{{ $settings['pricing_blanket'] ?? '4.00' }}" 
                                class="w-20 px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-right text-gray-800 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <!-- Row 5: Curtain -->
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="w-2/5 font-medium text-gray-800">Curtain</span>
                        <span class="w-1/3 text-gray-500">Per Kg</span>
                        <div class="w-1/4 text-right">
                            <input 
                                type="number" 
                                step="0.01" 
                                name="pricing_curtain" 
                                value="{{ $settings['pricing_curtain'] ?? '2.00' }}" 
                                class="w-20 px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-right text-gray-800 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <!-- Row 6: Shoes Cleaning -->
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="w-2/5 font-medium text-gray-800">Shoes Cleaning</span>
                        <span class="w-1/3 text-gray-500">Per Pair</span>
                        <div class="w-1/4 text-right">
                            <input 
                                type="number" 
                                step="0.01" 
                                name="pricing_shoes_cleaning" 
                                value="{{ $settings['pricing_shoes_cleaning'] ?? '2.50' }}" 
                                class="w-20 px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-right text-gray-800 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <!-- Save Changes Button -->
                    <div class="pt-3 text-right">
                        <button 
                            type="submit" 
                            class="bg-[#2563EB] hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-xl text-xs sm:text-sm shadow-xs transition active:scale-95 cursor-pointer inline-flex items-center space-x-1.5"
                        >
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- COLUMN 1, ROW 2: TAX SETTINGS                            -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100/90 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.03)] p-6 sm:p-7 flex flex-col justify-between space-y-5">
            <div>
                <!-- Card Header -->
                <div class="flex items-center space-x-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-receipt text-base text-[#8B5CF6]"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Tax Settings</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Configure tax and VAT settings</p>
                    </div>
                </div>

                <!-- Form -->
                <form id="formTax" action="{{ route('settings.update-tax') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Tax Name -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tax Name</label>
                        <input 
                            type="text" 
                            name="tax_name" 
                            value="{{ $settings['tax_name'] ?? 'Value Added Tax (VAT)' }}" 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                            placeholder="e.g. Value Added Tax (VAT)"
                        >
                    </div>

                    <!-- Tax Rate (%) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tax Rate (%)</label>
                        <div class="relative">
                            <input 
                                type="number" 
                                step="0.1" 
                                name="tax_rate" 
                                value="{{ $settings['tax_rate'] ?? '10' }}" 
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition pr-8"
                                placeholder="10"
                            >
                            <span class="absolute right-3.5 top-2.5 text-xs text-gray-400 font-bold">%</span>
                        </div>
                    </div>

                    <!-- Tax Type -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tax Type</label>
                        <select 
                            name="tax_type" 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer"
                        >
                            <option value="Inclusive" {{ ($settings['tax_type'] ?? 'Inclusive') === 'Inclusive' ? 'selected' : '' }}>Inclusive</option>
                            <option value="Exclusive" {{ ($settings['tax_type'] ?? '') === 'Exclusive' ? 'selected' : '' }}>Exclusive</option>
                        </select>
                    </div>

                    <!-- Apply Tax On -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Apply Tax On</label>
                        <select 
                            name="apply_tax_on" 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer"
                        >
                            <option value="All Services" {{ ($settings['apply_tax_on'] ?? 'All Services') === 'All Services' ? 'selected' : '' }}>All Services</option>
                            <option value="Wash & Fold Only" {{ ($settings['apply_tax_on'] ?? '') === 'Wash & Fold Only' ? 'selected' : '' }}>Wash &amp; Fold Only</option>
                            <option value="Dry Cleaning Only" {{ ($settings['apply_tax_on'] ?? '') === 'Dry Cleaning Only' ? 'selected' : '' }}>Dry Cleaning Only</option>
                            <option value="Selected Services" {{ ($settings['apply_tax_on'] ?? '') === 'Selected Services' ? 'selected' : '' }}>Selected Services</option>
                        </select>
                    </div>

                    <!-- Save Changes Button -->
                    <div class="pt-2 text-right">
                        <button 
                            type="submit" 
                            class="bg-[#2563EB] hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-xl text-xs sm:text-sm shadow-xs transition active:scale-95 cursor-pointer inline-flex items-center space-x-1.5"
                        >
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- COLUMN 2, ROW 2: BACKUP DATABASE                         -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100/90 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.03)] p-6 sm:p-7 flex flex-col justify-between space-y-5">
            <div>
                <!-- Card Header -->
                <div class="flex items-center space-x-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-database text-base text-[#2563EB]"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Backup Database</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Backup your database regularly</p>
                    </div>
                </div>

                <!-- Blue Inner Callout Card -->
                <div class="bg-[#F1F6FE] border border-blue-100/80 rounded-2xl p-4.5 space-y-3.5">
                    <div class="flex items-start space-x-3">
                        <i class="fa-solid fa-circle-info text-blue-600 text-sm mt-0.5"></i>
                        <div>
                            <h3 class="text-xs font-bold text-gray-900 leading-tight">Last Backup</h3>
                            <p class="text-xs text-gray-600 mt-0.5">{{ $settings['last_backup_date'] ?? 'Never' }}</p>
                            @if(!empty($settings['last_backup_by']) && $settings['last_backup_by'] !== 'None')
                                <p class="text-[11px] text-gray-400">(By {{ $settings['last_backup_by'] }})</p>
                            @endif
                        </div>
                    </div>

                    <!-- Backup Now Button -->
                    <button 
                        type="button" 
                        onclick="triggerBackup()" 
                        id="btnBackupNow" 
                        class="w-full bg-[#2563EB] hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl text-xs sm:text-sm flex items-center justify-center space-x-2 shadow-xs transition active:scale-95 cursor-pointer"
                    >
                        <i class="fa-solid fa-arrow-down-to-bracket text-xs"></i>
                        <span>Backup Now</span>
                    </button>
                </div>

                <!-- Recent Backups Table -->
                <div class="mt-4 pt-3 border-t border-gray-100">
                    <div class="flex items-center justify-between text-[11px] font-semibold text-gray-500 pb-2">
                        <span class="w-2/5">Date &amp; Time</span>
                        <span class="w-2/5">File Name</span>
                        <span class="w-1/6 text-right">Size</span>
                        <span class="w-10 text-right">Actions</span>
                    </div>

                    <div class="divide-y divide-gray-50 text-[11px]" id="backupsListContainer">
                        @forelse($backups as $b)
                            <div class="flex items-center justify-between py-2 text-gray-700 hover:bg-gray-50/50 rounded-lg px-1 transition">
                                <span class="w-2/5 text-gray-600 font-medium whitespace-nowrap">{{ $b['date'] ?? '' }}</span>
                                <span class="w-2/5 text-gray-800 font-medium truncate" title="{{ $b['filename'] }}">{{ $b['filename'] }}</span>
                                <span class="w-1/6 text-right text-gray-500">{{ $b['filesize'] ?? '' }}</span>
                                <div class="w-10 text-right">
                                    <a 
                                        href="{{ route('settings.download-backup', ['filename' => $b['filename']]) }}" 
                                        class="text-blue-600 hover:text-blue-800 p-1 inline-block transition" 
                                        title="Download SQL Backup"
                                    >
                                        <i class="fa-solid fa-download text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="py-3 text-center text-gray-400 text-xs">
                                No backups recorded yet.
                            </div>
                        @endforelse
                    </div>

                    <p class="text-[11px] text-gray-400 text-center mt-3 pt-2">
                        Backups are stored securely and can be downloaded.
                    </p>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- COLUMN 3, ROW 2: RESTORE DATABASE                        -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100/90 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.03)] p-6 sm:p-7 flex flex-col justify-between space-y-5">
            <div>
                <!-- Card Header -->
                <div class="flex items-center space-x-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-rotate text-base text-[#10B981]"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Restore Database</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Restore your database from backup</p>
                    </div>
                </div>

                <!-- Warning Callout Box -->
                <div class="bg-[#FFFBEB] border border-amber-200/90 rounded-2xl p-4 flex items-start space-x-3 mb-4.5">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-base flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h3 class="text-xs font-bold text-amber-900 leading-tight">Warning</h3>
                        <p class="text-[11px] text-amber-800 leading-relaxed mt-0.5">
                            Restoring database will overwrite current data. Please make sure to backup first.
                        </p>
                    </div>
                </div>

                <!-- Form -->
                <form id="formRestore" action="{{ route('settings.restore') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Select Backup File</label>
                        <select 
                            name="backup_file" 
                            id="selectBackupFile" 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer"
                        >
                            <option value="">Select a backup file</option>
                            @foreach($backups as $b)
                                <option value="{{ $b['filename'] }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $b['filename'] }} ({{ $b['filesize'] }})
                                </option>
                            @endforeach
                            <option value="custom_upload">Upload custom .sql file...</option>
                        </select>
                    </div>

                    <!-- Hidden file upload input if custom chosen -->
                    <div id="customBackupUploadBox" class="hidden">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Choose SQL File</label>
                        <input type="file" name="custom_backup_file" accept=".sql" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <!-- Restore Button -->
                    <button 
                        type="button" 
                        onclick="confirmRestore()" 
                        id="btnRestoreSubmit" 
                        class="w-full bg-[#16A34A] hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl text-xs sm:text-sm flex items-center justify-center space-x-2 shadow-xs transition active:scale-95 cursor-pointer"
                    >
                        <i class="fa-solid fa-rotate text-xs"></i>
                        <span>Restore Database</span>
                    </button>

                    <p class="text-[11px] text-gray-400 text-left mt-2">
                        Note: This action cannot be undone.
                    </p>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- ======================================================== -->
<!-- CONFIRM RESTORE MODAL                                    -->
<!-- ======================================================== -->
<div id="confirmRestoreModal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-7 relative text-center">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
        </div>

        <h3 class="text-lg font-bold text-gray-900">Are you sure?</h3>
        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
            Restoring will overwrite current database records with the selected backup file.
        </p>

        <div class="flex items-center space-x-3 mt-6">
            <button 
                type="button" 
                onclick="document.getElementById('confirmRestoreModal').classList.add('hidden')" 
                class="flex-1 py-2.5 px-4 border border-gray-200 rounded-xl text-xs sm:text-sm font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer"
            >
                Cancel
            </button>
            <button 
                type="button" 
                onclick="executeRestore()" 
                id="btnConfirmRestoreAction" 
                class="flex-1 py-2.5 px-4 bg-[#16A34A] hover:bg-emerald-700 text-white rounded-xl text-xs sm:text-sm font-semibold transition cursor-pointer"
            >
                Yes, Restore
            </button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- JAVASCRIPT INTERACTIONS                                 -->
<!-- ======================================================== -->
<script>
    // 1. Toast Notification Helper
    function showToast(msg) {
        const toast = document.getElementById('settingsToast');
        const text = document.getElementById('settingsToastMsg');
        if (toast && text) {
            text.innerText = msg;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 4000);
        }
    }

    // 2. Business Hours Toggle Switch logic
    function toggleDayRow(day, isChecked) {
        const container = document.getElementById('container-' + day);
        if (!container) return;

        if (day === 'sunday') {
            const select = document.getElementById('select-sunday');
            if (select) {
                if (isChecked) {
                    select.value = '08:00 AM - 05:00 PM';
                    select.classList.remove('bg-gray-50/60', 'text-gray-600');
                    select.classList.add('bg-white', 'text-gray-900', 'border-blue-300');
                } else {
                    select.value = 'Closed';
                    select.classList.remove('bg-white', 'text-gray-900', 'border-blue-300');
                    select.classList.add('bg-gray-50/60', 'text-gray-600');
                }
            }
        } else {
            const inputs = container.querySelectorAll('input');
            inputs.forEach(inp => {
                if (isChecked) {
                    inp.disabled = false;
                    inp.classList.remove('bg-gray-100', 'text-gray-400');
                    inp.classList.add('bg-white', 'text-gray-700');
                } else {
                    inp.disabled = true;
                    inp.classList.remove('bg-white', 'text-gray-700');
                    inp.classList.add('bg-gray-100', 'text-gray-400');
                }
            });
        }
    }

    // 3. Database Backup Trigger
    function triggerBackup() {
        const btn = document.getElementById('btnBackupNow');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Backing up...</span>';

        fetch("{{ route('settings.backup') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-arrow-down-to-bracket text-xs"></i><span>Backup Now</span>';
            showToast(data.message || 'Database backup created successfully!');

            // Automatically download backup file
            if (data.download_url) {
                window.location.href = data.download_url;
            }

            // Reload page to reflect updated table
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-arrow-down-to-bracket text-xs"></i><span>Backup Now</span>';
            showToast('Backup complete!');
            window.location.reload();
        });
    }

    // 4. Restore Database Flow
    function confirmRestore() {
        document.getElementById('confirmRestoreModal').classList.remove('hidden');
    }

    function executeRestore() {
        const btn = document.getElementById('btnConfirmRestoreAction');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Restoring...</span>';

        const form = document.getElementById('formRestore');
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
            document.getElementById('confirmRestoreModal').classList.add('hidden');
            btn.disabled = false;
            btn.innerText = 'Yes, Restore';
            showToast(data.message || 'Database restored successfully!');
        })
        .catch(err => {
            document.getElementById('confirmRestoreModal').classList.add('hidden');
            form.submit();
        });
    }

    // 5. Custom SQL File select listener
    document.addEventListener('DOMContentLoaded', function() {
        const sel = document.getElementById('selectBackupFile');
        const customBox = document.getElementById('customBackupUploadBox');
        if (sel && customBox) {
            sel.addEventListener('change', function() {
                if (this.value === 'custom_upload') {
                    customBox.classList.remove('hidden');
                } else {
                    customBox.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection
