@extends('layouts.navbar')

@section('title', 'Dashboard')


@section('content')
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
        <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-base leading-none">&times;</button>
    </div>
    @endif

    <!-- 8 STAT CARDS GRID (4x2 layout exactly matching design) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        @foreach($statCards as $card)
        <div class="{{ $card['card_bg'] }} rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 border border-white/70 flex flex-col justify-between min-h-28">
            <!-- Top Row: Icon Box + (Label & Big Bold Value) -->
            <div class="flex items-center space-x-3.5">
                <!-- 54px rounded icon box in solid color -->
                <div class="{{ $card['icon_box_bg'] }} w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm text-white">
                    <i class="{{ $card['icon_fa'] }} text-2xl"></i>
                </div>
                <!-- Label and Metric Value -->
                <div class="flex-1 min-w-0">
                    <div class="text-xs sm:text-sm text-gray-500 font-medium truncate leading-none">{{ $card['label'] }}</div>
                    <div class="text-xl sm:text-xl font-bold text-gray-900 leading-tight tracking-tight mt-1.5">{{ $card['value'] }}</div>
                </div>
            </div>

            <!-- Bottom Row: Trend Line with green arrow and comparison text -->
            <div class="mt-3.5 flex items-center text-xs font-semibold text-[#10B981] leading-none">
                <span class="inline-flex items-center">↑ {{ $card['trend_pct'] }}</span>
                <span class="text-gray-400 font-normal ml-1.5">{{ $card['trend_text'] }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- CHARTS SECTION: Order Status Donut Chart & Revenue Overview Grouped Bar Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        <!-- Left: Order Status Chart Card (lg:col-span-5) -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
            <!-- Header: Title & Dropdown -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-5 h-5 flex items-center justify-center text-gray-900">
                        <i class="fa-solid fa-chart-simple text-lg"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Order Status Chart</h3>
                </div>

                <!-- This Week Dropdown -->
                <div class="relative">
                    <button type="button" id="period-dropdown-btn" onclick="togglePeriodDropdown()" class="inline-flex items-center space-x-2 px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm focus:outline-none">
                        <span id="selected-period-text">{{ $orderStatus['period'] ?? 'This Week' }}</span>
                        <i id="period-chevron" class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-150"></i>
                    </button>
                    <div id="period-dropdown-menu" class="hidden absolute right-0 mt-1.5 w-36 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-20 text-xs">
                        <button type="button" onclick="selectOrderPeriod('This Week')" class="w-full text-left px-3.5 py-1.5 hover:bg-blue-50 hover:text-blue-600 font-medium transition">This Week</button>
                        <button type="button" onclick="selectOrderPeriod('Last Week')" class="w-full text-left px-3.5 py-1.5 hover:bg-blue-50 hover:text-blue-600 font-medium transition">Last Week</button>
                        <button type="button" onclick="selectOrderPeriod('This Month')" class="w-full text-left px-3.5 py-1.5 hover:bg-blue-50 hover:text-blue-600 font-medium transition">This Month</button>
                    </div>
                </div>
            </div>

            <!-- Body: Donut Chart + Vertical Legend Alignment -->
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-6 flex-1">
                <!-- Donut Chart Container with Center Total Text -->
                <div class="relative w-48 h-48 sm:w-52 sm:h-52 shrink-0 flex items-center justify-center">
                    <canvas id="orderStatusChart" width="205" height="205"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center">
                        <span id="donutCenterTotal" class="text-2xl font-bold text-gray-900 leading-none">{{ $orderStatus['total'] }}</span>
                        <span class="text-xs text-gray-400 font-medium mt-1">Total Orders</span>
                    </div>
                </div>

                <!-- Vertical Legend Stack with Count and Dynamic Percentage -->
                <div id="orderStatusLegend" class="w-full sm:w-auto flex-1 max-w-xs space-y-3.5">
                    @foreach($orderStatus['categories'] as $cat)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" {!! 'style="background-color: ' . e($cat['color']) . ';"' !!}></span>
                            <span class="text-gray-600 font-medium">{{ $cat['name'] }}</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="font-bold text-gray-900 w-6 text-right">{{ $cat['count'] }}</span>
                            <span class="text-gray-400 w-8 text-right font-medium">{{ $cat['percentage'] }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Revenue Overview Card (lg:col-span-7) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
            <!-- Header: Title, Pill Tabs & Series Legend -->
            <div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-5 h-5 flex items-center justify-center text-gray-900">
                            <i class="fa-solid fa-arrow-trend-up text-lg"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Revenue Overview</h3>
                    </div>

                    <!-- Daily / Weekly / Monthly Segmented Pill Tabs -->
                    <div class="bg-gray-100/80 p-0.5 rounded-xl flex items-center space-x-0.5" role="tablist">
                        <button type="button" id="tab-daily" onclick="switchRevenueTab('daily')" class="px-3.5 py-1 text-xs font-semibold rounded-lg bg-[#2563EB] text-white shadow-sm transition-all duration-150">Daily</button>
                        <button type="button" id="tab-weekly" onclick="switchRevenueTab('weekly')" class="px-3.5 py-1 text-xs font-medium rounded-lg text-gray-500 hover:text-gray-900 transition-all duration-150">Weekly</button>
                        <button type="button" id="tab-monthly" onclick="switchRevenueTab('monthly')" class="px-3.5 py-1 text-xs font-medium rounded-lg text-gray-500 hover:text-gray-900 transition-all duration-150">Monthly</button>
                    </div>
                </div>

                <!-- Series Legend Dots -->
                <div class="flex items-center space-x-5 mt-2.5 text-xs font-medium text-gray-500">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#2563EB]"></span>
                        <span>Today's Revenue</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#93C5FD]"></span>
                        <span>Last Week</span>
                    </div>
                </div>
            </div>

            <!-- Grouped Bar Chart Canvas Container -->
            <div class="mt-4 relative w-full h-56">
                <canvas id="revenueOverviewChart"></canvas>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script id="order-status-data" type="application/json">
    @json($orderStatus)
</script>
<script id="revenue-overview-data" type="application/json">
    @json($revenueOverview)
</script>
<script>
    // ==========================================
    // 1. DATA FROM BACKEND (Passed cleanly from Controller)
    // ==========================================
    const orderStatusRaw = JSON.parse(document.getElementById('order-status-data').textContent || '{}');
    const revenueOverviewRaw = JSON.parse(document.getElementById('revenue-overview-data').textContent || '{}');

    // ==========================================
    // 2. ORDER STATUS DONUT CHART (Chart.js)
    // ==========================================
    let donutChartInstance = null;

    function initDonutChart(categories) {
        const ctx = document.getElementById('orderStatusChart');
        if (!ctx) return;

        const labels = categories.map(c => c.name);
        const data = categories.map(c => c.count);
        const backgroundColors = categories.map(c => c.color);

        if (donutChartInstance) {
            donutChartInstance.destroy();
        }

        donutChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '74%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        titleFont: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 11
                        },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.parsed;
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return ` ${context.label}: ${value} orders (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    }

    // Period switcher for donut chart
    function togglePeriodDropdown() {
        const menu = document.getElementById('period-dropdown-menu');
        const chevron = document.getElementById('period-chevron');
        if (menu) {
            menu.classList.toggle('hidden');
            if (chevron) {
                chevron.classList.toggle('rotate-180');
            }
        }
    }

    function selectOrderPeriod(periodName) {
        const label = document.getElementById('selected-period-text');
        if (label) label.innerText = periodName;

        // Toggle dropdown off
        togglePeriodDropdown();

        // Variations based on period selection
        let cats = JSON.parse(JSON.stringify(orderStatusRaw.categories));
        let totalCount = 245;

        if (periodName === 'Last Week') {
            cats[0].count = 28; // Pending
            cats[1].count = 72; // Washing
            cats[2].count = 41; // Ready
            cats[3].count = 54; // Delivered
            cats[4].count = 25; // Cancelled
            totalCount = 220;
        } else if (periodName === 'This Month') {
            cats[0].count = 110;
            cats[1].count = 312;
            cats[2].count = 180;
            cats[3].count = 240;
            cats[4].count = 85;
            totalCount = 927;
        }

        // Recalculate percentages
        cats.forEach(c => {
            c.percentage = totalCount > 0 ? Math.round((c.count / totalCount) * 100) : 0;
        });

        // Update center text
        const centerEl = document.getElementById('donutCenterTotal');
        if (centerEl) centerEl.innerText = totalCount;

        // Update Legend HTML
        const legendContainer = document.getElementById('orderStatusLegend');
        if (legendContainer) {
            legendContainer.innerHTML = cats.map(c => `
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: ${c.color};"></span>
                        <span class="text-gray-600 font-medium">${c.name}</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="font-bold text-gray-900 w-6 text-right">${c.count}</span>
                        <span class="text-gray-400 w-8 text-right font-medium">${c.percentage}%</span>
                    </div>
                </div>
            `).join('');
        }

        // Re-render chart
        initDonutChart(cats);
    }

    // ==========================================
    // 3. REVENUE OVERVIEW GROUPED BAR CHART
    // ==========================================
    let barChartInstance = null;
    let currentRevenueTab = 'daily';

    function initRevenueBarChart(periodKey = 'daily') {
        const ctx = document.getElementById('revenueOverviewChart');
        if (!ctx) return;

        const periodData = revenueOverviewRaw[periodKey] || revenueOverviewRaw['daily'];

        // Multi-line labels matching design: Top line day/label, bottom line date
        const multiLineLabels = periodData.labels.map((lbl, idx) => {
            const dateStr = periodData.dates && periodData.dates[idx] ? periodData.dates[idx] : '';
            return dateStr ? [lbl, dateStr] : lbl;
        });

        if (barChartInstance) {
            barChartInstance.destroy();
        }

        // Max scale calculation
        const maxVal = Math.max(...periodData.today_series, ...periodData.last_week_series);
        const yMax = periodKey === 'daily' ? 800 : (periodKey === 'weekly' ? 3000 : 6000);
        const yStep = periodKey === 'daily' ? 200 : (periodKey === 'weekly' ? 1000 : 2000);

        barChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: multiLineLabels,
                datasets: [{
                        label: "Today's Revenue",
                        data: periodData.today_series,
                        backgroundColor: '#2563EB', // Strong Royal Blue
                        borderRadius: 4,
                        borderSkipped: false,
                        barPercentage: 0.62,
                        categoryPercentage: 0.65
                    },
                    {
                        label: "Last Week",
                        data: periodData.last_week_series,
                        backgroundColor: '#93C5FD', // Light Sky Blue
                        borderRadius: 4,
                        borderSkipped: false,
                        barPercentage: 0.62,
                        categoryPercentage: 0.65
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Using custom header legend
                    },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        titleFont: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 11
                        },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ` ${context.dataset.label}: $ ${context.parsed.y.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: yMax,
                        ticks: {
                            stepSize: yStep,
                            color: '#9CA3AF',
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 11.5,
                                weight: 500
                            },
                            callback: function(value) {
                                return '$' + value;
                            }
                        },
                        grid: {
                            color: '#F3F4F6',
                            drawBorder: false
                        },
                        border: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#6B7280',
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 11,
                                weight: 600
                            }
                        }
                    }
                }
            }
        });
    }

    function switchRevenueTab(tabKey) {
        currentRevenueTab = tabKey;
        const tabs = ['daily', 'weekly', 'monthly'];

        tabs.forEach(t => {
            const btn = document.getElementById(`tab-${t}`);
            if (btn) {
                if (t === tabKey) {
                    btn.className = 'px-3.5 py-1 text-xs font-semibold rounded-lg bg-[#2563EB] text-white shadow-sm transition-all duration-150';
                } else {
                    btn.className = 'px-3.5 py-1 text-xs font-medium rounded-lg text-gray-500 hover:text-gray-900 transition-all duration-150';
                }
            }
        });

        initRevenueBarChart(tabKey);
    }

    // ==========================================
    // 4. USER ROLE DROPDOWN & ACCESSIBILITY
    // ==========================================
    function toggleRoleMenu() {
        const menu = document.getElementById('role-menu');
        const chevron = document.getElementById('role-chevron');
        const button = document.getElementById('role-menu-button');
        if (!menu) return;

        const isExpanded = menu.classList.contains('hidden');
        if (isExpanded) {
            menu.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
            if (button) button.setAttribute('aria-expanded', 'true');

            // Focus first item
            const firstItem = menu.querySelector('.role-menu-item');
            if (firstItem) firstItem.focus();
        } else {
            closeRoleMenu();
        }
    }

    function closeRoleMenu() {
        const menu = document.getElementById('role-menu');
        const chevron = document.getElementById('role-chevron');
        const button = document.getElementById('role-menu-button');
        if (!menu) return;

        menu.classList.add('hidden');
        if (chevron) chevron.classList.remove('rotate-180');
        if (button) {
            button.setAttribute('aria-expanded', 'false');
            button.focus();
        }
    }

    // Click outside to close menus
    document.addEventListener('click', function(e) {
        const roleContainer = document.getElementById('role-switcher-container');
        if (roleContainer && !roleContainer.contains(e.target)) {
            const menu = document.getElementById('role-menu');
            const chevron = document.getElementById('role-chevron');
            const button = document.getElementById('role-menu-button');
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
                if (button) button.setAttribute('aria-expanded', 'false');
            }
        }

        const periodBtn = document.getElementById('period-dropdown-btn');
        const periodMenu = document.getElementById('period-dropdown-menu');
        if (periodBtn && periodMenu && !periodBtn.contains(e.target) && !periodMenu.contains(e.target)) {
            periodMenu.classList.add('hidden');
            const chevron = document.getElementById('period-chevron');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    });

    // Keyboard navigation for role menu
    document.addEventListener('keydown', function(e) {
        const menu = document.getElementById('role-menu');
        if (!menu || menu.classList.contains('hidden')) return;

        const items = Array.from(menu.querySelectorAll('.role-menu-item'));
        const activeIdx = items.indexOf(document.activeElement);

        if (e.key === 'Escape') {
            closeRoleMenu();
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            const nextIdx = (activeIdx + 1) % items.length;
            items[nextIdx].focus();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prevIdx = (activeIdx - 1 + items.length) % items.length;
            items[prevIdx].focus();
        } else if (e.key === 'Home') {
            e.preventDefault();
            items[0]?.focus();
        } else if (e.key === 'End') {
            e.preventDefault();
            items[items.length - 1]?.focus();
        }
    });

    // ==========================================
    // 5. INITIALIZE ON DOM READY
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        // Init Donut Chart
        if (orderStatusRaw && orderStatusRaw.categories) {
            initDonutChart(orderStatusRaw.categories);
        }

        // Init Revenue Bar Chart (Default Daily)
        initRevenueBarChart('daily');
    });
</script>
@endpush