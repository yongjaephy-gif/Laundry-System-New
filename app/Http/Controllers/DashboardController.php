<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Machine;

class DashboardController extends Controller
{
    /**
     * Role definitions and metadata.
     */
    protected array $roles = [
        'Admin' => [
            'id' => 1,
            'name' => 'Admin',
            'icon' => 'shield-check',
            'color' => 'text-blue-600',
            'badge_bg' => 'bg-blue-100',
            'badge_text' => 'text-blue-700',
            'description' => 'Full Administrative Access',
        ],
        'Staff' => [
            'id' => 2,
            'name' => 'Staff',
            'icon' => 'users',
            'color' => 'text-indigo-600',
            'badge_bg' => 'bg-indigo-100',
            'badge_text' => 'text-indigo-700',
            'description' => 'Laundry & Machine Operations',
        ],
        'Delivery' => [
            'id' => 3,
            'name' => 'Delivery',
            'icon' => 'truck',
            'color' => 'text-rose-600',
            'badge_bg' => 'bg-rose-100',
            'badge_text' => 'text-rose-700',
            'description' => 'Routes & Fleet Logistics',
        ],
        'Receptionist' => [
            'id' => 4,
            'name' => 'Receptionist',
            'icon' => 'concierge-bell',
            'color' => 'text-amber-600',
            'badge_bg' => 'bg-amber-100',
            'badge_text' => 'text-amber-700',
            'description' => 'Front Desk & Customer In-take',
        ],
    ];

    /**
     * Check if free role switching is permitted.
     */
    private function isDemoSwitcherEnabled(): bool
    {
        return env('ROLE_SWITCHER', true) || app()->environment('local');
    }

    /**
     * Check if a user possesses a specific role.
     *
     * @param \App\Models\User|null $user
     * @param string $role
     * @return bool
     */
    private function userHasRole(?\App\Models\User $user, string $role): bool
    {
        if (!$user) {
            return false;
        }

        // Admin has access to all roles
        if ($user->role_id == 1 || strtolower($user->name) === 'admin' || strtolower($user->email ?? '') === 'admin@gmail.com') {
            return true;
        }

        $roleMap = [
            1 => 'Admin',
            2 => 'Staff',
            3 => 'Delivery',
            4 => 'Receptionist',
        ];

        return isset($roleMap[$user->role_id]) && strtolower($roleMap[$user->role_id]) === strtolower($role);
    }

    /**
     * Determine active role for current request.
     */
    private function resolveActiveRole(Request $request): string
    {
        $isDemo = $this->isDemoSwitcherEnabled();

        // 1. Direct query parameter change
        if ($request->has('role')) {
            $requested = ucfirst(strtolower($request->query('role')));
            if (isset($this->roles[$requested])) {
                if ($isDemo || (Auth::check() && $this->userHasRole(Auth::user(), $requested))) {
                    session(['active_role' => $requested]);
                    return $requested;
                }
                abort(403, 'Unauthorized role access.');
            }
        }

        // 2. Stored in session
        if (session()->has('active_role') && isset($this->roles[session('active_role')])) {
            $sessRole = session('active_role');
            if ($isDemo || (Auth::check() && $this->userHasRole(Auth::user(), $sessRole))) {
                return $sessRole;
            }
        }

        // 3. User default role
        if (Auth::check() && Auth::user()->role_id) {
            $map = [1 => 'Admin', 2 => 'Staff', 3 => 'Delivery', 4 => 'Receptionist'];
            $userRole = $map[Auth::user()->role_id] ?? 'Admin';
            session(['active_role' => $userRole]);
            return $userRole;
        }

        // 4. Default to Admin
        session(['active_role' => 'Admin']);
        return 'Admin';
    }

    /**
     * Get available roles for the current user.
     */
    private function getAvailableRoles(): array
    {
        $isDemo = $this->isDemoSwitcherEnabled();

        if ($isDemo) {
            return $this->roles;
        }

        $user = Auth::user();
        if (!$user) {
            return ['Admin' => $this->roles['Admin']];
        }

        // Admin has access to all roles
        if ($user->role_id == 1) {
            return $this->roles;
        }

        // Filter to only roles the user possesses
        $filtered = [];
        foreach ($this->roles as $key => $data) {
            if ($this->userHasRole($user, $key)) {
                $filtered[$key] = $data;
            }
        }

        return !empty($filtered) ? $filtered : ['Staff' => $this->roles['Staff']];
    }

    /**
     * Eight Stat Cards data generator.
     */
    private function getStatCards(string $role): array
    {
        $hasOrders = false;
        $hasCustomers = false;
        $hasPayments = false;
        $hasMachines = false;
        try {
            $hasOrders = Schema::hasTable('orders');
            $hasCustomers = Schema::hasTable('customers');
            $hasPayments = Schema::hasTable('payments');
            $hasMachines = Schema::hasTable('machines');
        } catch (\Throwable $e) {
            //
        }

        $totalCustomers = $hasCustomers ? Customer::count() : 0;
        $totalOrders = $hasOrders ? Order::count() : 0;
        $pendingOrders = $hasOrders ? Order::whereIn('status', ['Received', 'Pending'])->count() : 0;
        $washingOrders = $hasOrders ? Order::where('status', 'Washing')->count() : 0;
        $readyPickup = $hasOrders ? Order::where('status', 'Ready for Pickup')->count() : 0;
        $deliveredOrders = $hasOrders ? Order::whereIn('status', ['Delivered', 'Completed', 'Out for Delivery'])->count() : 0;
        $todaysRevenue = $hasOrders ? Order::whereDate('created_at', today())->sum('total_price') : 0.00;
        $monthlyRevenue = $hasOrders ? Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_price') : 0.00;

        // Default / Admin Cards (Dynamic Real Database Values)
        $adminCards = [
            [
                'id' => 'total_customers',
                'label' => 'Total Customers',
                'value' => (string) $totalCustomers,
                'trend_pct' => '12%',
                'trend_direction' => 'up',
                'trend_text' => 'compared to last month',
                'icon' => 'users',
                'icon_fa' => 'fa-solid fa-users',
                'icon_color' => '#2563EB', // Blue
                'card_bg' => 'bg-[#EEF4FF]',
                'icon_box_bg' => 'bg-[#3B82F6]',
            ],
            [
                'id' => 'total_laundry_orders',
                'label' => 'Total Laundry Orders',
                'value' => (string) $totalOrders,
                'trend_pct' => '8%',
                'trend_direction' => 'up',
                'trend_text' => 'compared to last month',
                'icon' => 'clipboard-list',
                'icon_fa' => 'fa-solid fa-clipboard-list',
                'icon_color' => '#4F46E5', // Indigo
                'card_bg' => 'bg-[#EEF2FF]',
                'icon_box_bg' => 'bg-[#4F46E5]',
            ],
            [
                'id' => 'pending_orders',
                'label' => 'Pending Orders',
                'value' => (string) $pendingOrders,
                'trend_pct' => '5%',
                'trend_direction' => 'up',
                'trend_text' => 'compared to yesterday',
                'icon' => 'history',
                'icon_fa' => 'fa-solid fa-clock-rotate-left',
                'icon_color' => '#F59E0B', // Amber
                'card_bg' => 'bg-[#FFFBEB]',
                'icon_box_bg' => 'bg-[#F59E0B]',
            ],
            [
                'id' => 'washing_orders',
                'label' => 'Washing Orders',
                'value' => (string) $washingOrders,
                'trend_pct' => '6%',
                'trend_direction' => 'up',
                'trend_text' => 'compared to yesterday',
                'icon' => 'washing-machine',
                'icon_fa' => 'fa-solid fa-soap',
                'icon_color' => '#0284C7', // Sky Blue
                'card_bg' => 'bg-[#F0F9FF]',
                'icon_box_bg' => 'bg-[#0284C7]',
            ],
            [
                'id' => 'ready_for_pickup',
                'label' => 'Ready for Pickup',
                'value' => (string) $readyPickup,
                'trend_pct' => '10%',
                'trend_direction' => 'up',
                'trend_text' => 'compared to yesterday',
                'icon' => 'package',
                'icon_fa' => 'fa-solid fa-box',
                'icon_color' => '#10B981', // Green
                'card_bg' => 'bg-[#ECFDF5]',
                'icon_box_bg' => 'bg-[#10B981]',
            ],
            [
                'id' => 'delivered_orders',
                'label' => 'Delivered Orders',
                'value' => (string) $deliveredOrders,
                'trend_pct' => '7%',
                'trend_direction' => 'up',
                'trend_text' => 'compared to yesterday',
                'icon' => 'truck',
                'icon_fa' => 'fa-solid fa-truck',
                'icon_color' => '#F43F5E', // Rose
                'card_bg' => 'bg-[#FFF1F2]',
                'icon_box_bg' => 'bg-[#F43F5E]',
            ],
            [
                'id' => 'todays_revenue',
                'label' => "Today's Revenue",
                'value' => '$ ' . number_format($todaysRevenue, 2),
                'trend_pct' => '15%',
                'trend_direction' => 'up',
                'trend_text' => 'compared to yesterday',
                'icon' => 'dollar-sign',
                'icon_fa' => 'fa-solid fa-coins',
                'icon_color' => '#0D9488', // Teal
                'card_bg' => 'bg-[#F0FDFA]',
                'icon_box_bg' => 'bg-[#0D9488]',
            ],
            [
                'id' => 'monthly_revenue',
                'label' => 'Monthly Revenue',
                'value' => '$ ' . number_format($monthlyRevenue, 2),
                'trend_pct' => '12%',
                'trend_direction' => 'up',
                'trend_text' => 'compared to last month',
                'icon' => 'calendar',
                'icon_fa' => 'fa-solid fa-calendar-days',
                'icon_color' => '#8B5CF6', // Violet
                'card_bg' => 'bg-[#F5F3FF]',
                'icon_box_bg' => 'bg-[#8B5CF6]',
            ],
        ];

        if ($role === 'Staff') {
            // Staff customized cards
            $activeWashers = $hasMachines ? Machine::where('status', 'Available')->count() : 0;
            $adminCards[0]['label'] = 'Shift Capacity';
            $adminCards[0]['value'] = '100%';
            $adminCards[6]['label'] = 'Units Washed';
            $adminCards[6]['value'] = (string) $deliveredOrders . ' Orders';
            $adminCards[7]['label'] = 'Active Washers';
            $adminCards[7]['value'] = (string) $activeWashers . ' Units';
        } elseif ($role === 'Delivery') {
            // Delivery customized cards
            $adminCards[0]['label'] = 'Assigned Routes';
            $adminCards[0]['value'] = '0 Routes';
            $adminCards[6]['label'] = 'Van Fuel Efficiency';
            $adminCards[6]['value'] = '100%';
            $adminCards[7]['label'] = 'Avg Dispatch Time';
            $adminCards[7]['value'] = '15 Mins';
        } elseif ($role === 'Receptionist') {
            // Receptionist customized cards
            $walkinCash = 0.00;
            $digitalPayments = 0.00;
            if ($hasPayments) {
                try {
                    $cols = Schema::getColumnListing('payments');
                    $amtCol = in_array('amount', $cols) ? 'amount' : (in_array('total_amount', $cols) ? 'total_amount' : (in_array('paid_amount', $cols) ? 'paid_amount' : null));
                    if ($amtCol) {
                        $walkinCash = (float) Payment::where('payment_method', 'Cash')->sum($amtCol);
                        $digitalPayments = (float) Payment::where('payment_method', '!=', 'Cash')->sum($amtCol);
                    }
                } catch (\Throwable $e) {
                    $walkinCash = 0.00;
                    $digitalPayments = 0.00;
                }
            }
            $adminCards[6]['label'] = 'Walk-in Cash';
            $adminCards[6]['value'] = '$ ' . number_format($walkinCash, 2);
            $adminCards[7]['label'] = 'Digital Payments';
            $adminCards[7]['value'] = '$ ' . number_format($digitalPayments, 2);
        }

        return $adminCards;
    }

    /**
     * Order Status Donut Chart data generator.
     */
    private function getOrderStatusData(): array
    {
        $hasOrders = false;
        try {
            $hasOrders = Schema::hasTable('orders');
        } catch (\Throwable $e) {
            //
        }

        $pendingCount = $hasOrders ? Order::whereIn('status', ['Received', 'Pending'])->count() : 0;
        $washingCount = $hasOrders ? Order::where('status', 'Washing')->count() : 0;
        $readyCount = $hasOrders ? Order::where('status', 'Ready for Pickup')->count() : 0;
        $deliveredCount = $hasOrders ? Order::whereIn('status', ['Delivered', 'Completed'])->count() : 0;
        $cancelledCount = $hasOrders ? Order::where('status', 'Cancelled')->count() : 0;

        $categories = [
            ['name' => 'Pending', 'count' => $pendingCount, 'color' => '#F59E0B'],
            ['name' => 'Washing', 'count' => $washingCount, 'color' => '#3B82F6'],
            ['name' => 'Ready for Pickup', 'count' => $readyCount, 'color' => '#10B981'],
            ['name' => 'Delivered', 'count' => $deliveredCount, 'color' => '#8B5CF6'],
            ['name' => 'Cancelled', 'count' => $cancelledCount, 'color' => '#EF4444'],
        ];

        $total = array_sum(array_column($categories, 'count'));

        // Calculate dynamic percentages
        foreach ($categories as &$cat) {
            $cat['percentage'] = $total > 0 ? (int) round(($cat['count'] / $total) * 100) : 0;
        }

        return [
            'total' => $total,
            'period' => 'This Week',
            'categories' => $categories,
        ];
    }

    /**
     * Revenue Overview Grouped Bar Chart data generator.
     */
    private function getRevenueOverviewData(): array
    {
        return [
            'daily' => [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                'dates' => ['Sep 15', 'Sep 16', 'Sep 17', 'Sep 18', 'Sep 19', 'Sep 20'],
                'today_series' => [260, 340, 340, 380, 390, 480],
                'last_week_series' => [220, 280, 300, 350, 330, 420],
            ],
            'weekly' => [
                'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                'dates' => ['Aug 25-31', 'Sep 01-07', 'Sep 08-14', 'Sep 15-21'],
                'today_series' => [1850, 2120, 2340, 2580],
                'last_week_series' => [1650, 1900, 2050, 2280],
            ],
            'monthly' => [
                'labels' => ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
                'dates' => ['2026', '2026', '2026', '2026', '2026', '2026'],
                'today_series' => [3800, 4100, 4350, 4500, 4720, 4860],
                'last_week_series' => [3400, 3750, 3900, 4100, 4250, 4400],
            ],
        ];
    }

    /**
     * Display the main Dashboard page.
     */
    public function index(Request $request)
    {
        $activeRole = $this->resolveActiveRole($request);
        $availableRoles = $this->getAvailableRoles();
        $isDemoMode = $this->isDemoSwitcherEnabled();

        $statCards = $this->getStatCards($activeRole);
        $orderStatus = $this->getOrderStatusData();
        $revenueOverview = $this->getRevenueOverviewData();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'activeRole' => $activeRole,
                'statCards' => $statCards,
                'orderStatus' => $orderStatus,
                'revenueOverview' => $revenueOverview,
            ]);
        }

        return view('pages.dashboard', [
            'activeRole' => $activeRole,
            'availableRoles' => $availableRoles,
            'isDemoMode' => $isDemoMode,
            'statCards' => $statCards,
            'orderStatus' => $orderStatus,
            'revenueOverview' => $revenueOverview,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Switch active role and redirect to dashboard.
     */
    public function switchRole(Request $request, string $role)
    {
        $roleName = ucfirst(strtolower($role));
        $isDemo = $this->isDemoSwitcherEnabled();

        // check if the role is valid
        if (!isset($this->roles[$roleName])) {
            return redirect()->route('dashboard')->with('error', 'Invalid role selected.');
        }

        // // check if the user has permission to switch to this role
        // if (!$isDemo && Auth::check() && !$this->userHasRole(Auth::user(), $roleName)) {
        //     abort(403, 'You do not have permission to switch to this role.');
        // }

        // If the requested role is already active, just redirect to dashboard
        if (session('active_role') === $roleName) {
            return redirect()->route('dashboard');
        }

        // Log out the user to clear session and regenerate token
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session(['active_role' => $roleName]);

        return redirect()->route('login', ['role' => $roleName])
            ->with('success', "Please Login  {$roleName}!");
    }
}
