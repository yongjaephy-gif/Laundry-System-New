<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\PickupDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderController extends Controller
{
    /**
     * Get baseline mock orders matching screenshots exactly.
     */
    protected function getBaselineOrders()
    {
        return [];
    }

    /**
     * Retrieve mock list from session or baseline.
     */
    protected function getSessionOrders()
    {
        session()->forget('mock_orders');
        return [];
    }

    /**
     * Main Order Management Dashboard View.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $statusFilter = $request->input('status', 'All Status');
        $serviceFilter = $request->input('service_type', 'All Service Types');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $page = (int)$request->input('page', 1);
        $perPage = 10;

        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('orders');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $baseQuery = Order::query();

            // Total statistics (real database counts)
            $stats = [
                'total_orders' => Order::count(),
                'pending' => Order::whereIn('status', ['Received', 'Pending'])->count(),
                'washing' => Order::where('status', 'Washing')->count(),
                'ready_pickup' => Order::where('status', 'Ready for Pickup')->count(),
                'out_delivery' => Order::where('status', 'Out for Delivery')->count(),
                'completed' => Order::where('status', 'Completed')->count(),
            ];

            $query = Order::with('items');

            if (!empty($search)) {
                $query->search($search);
            }

            if (!empty($statusFilter) && $statusFilter !== 'All' && $statusFilter !== 'All Status') {
                $query->filterStatus($statusFilter);
            }

            if (!empty($serviceFilter) && $serviceFilter !== 'All' && $serviceFilter !== 'All Service Types') {
                $query->filterServiceType($serviceFilter);
            }

            if (!empty($fromDate) || !empty($toDate)) {
                $query->dateRange($fromDate, $toDate);
            }

            $totalOrdersCount = $query->count();
            $orders = $query->orderBy('order_date', 'desc')
                            ->orderBy('id', 'desc')
                            ->paginate($perPage)
                            ->appends($request->all());

        } else {
            // Memory/Session collection fallback
            $allData = collect($this->getSessionOrders());

            $stats = [
                'total_orders' => 0,
                'pending' => 0,
                'washing' => 0,
                'ready_pickup' => 0,
                'out_delivery' => 0,
                'completed' => 0,
            ];

            // Search filter
            if (!empty($search)) {
                $searchLower = strtolower($search);
                $allData = $allData->filter(function ($item) use ($searchLower) {
                    return str_contains(strtolower($item['order_number']), $searchLower)
                        || str_contains(strtolower($item['customer_name']), $searchLower)
                        || str_contains(strtolower($item['customer_phone'] ?? ''), $searchLower)
                        || str_contains(strtolower($item['service_type']), $searchLower);
                });
            }

            // Status filter
            if (!empty($statusFilter) && $statusFilter !== 'All' && $statusFilter !== 'All Status') {
                $allData = $allData->filter(function ($item) use ($statusFilter) {
                    return strcasecmp($item['status'], $statusFilter) === 0;
                });
            }

            // Service type filter
            if (!empty($serviceFilter) && $serviceFilter !== 'All' && $serviceFilter !== 'All Service Types') {
                $allData = $allData->filter(function ($item) use ($serviceFilter) {
                    return strcasecmp($item['service_type'], $serviceFilter) === 0;
                });
            }

            // Date range filter
            if (!empty($fromDate)) {
                $allData = $allData->filter(function ($item) use ($fromDate) {
                    return substr($item['order_date'], 0, 10) >= $fromDate;
                });
            }
            if (!empty($toDate)) {
                $allData = $allData->filter(function ($item) use ($toDate) {
                    return substr($item['order_date'], 0, 10) <= $toDate;
                });
            }

            $totalOrdersCount = $allData->count();
            $offset = ($page - 1) * $perPage;
            $itemsForCurrentPage = $allData->slice($offset, $perPage)->values();

            $orders = new LengthAwarePaginator(
                $itemsForCurrentPage,
                $totalOrdersCount,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        // Return JSON if requested via AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'orders' => $orders,
                'stats' => $stats,
                'total' => $totalOrdersCount
            ]);
        }

        $viewName = view()->exists('order') ? 'order' : 'pages.order';
        return view($viewName, [
            'orders' => $orders,
            'stats' => $stats,
            'search' => $search,
            'selectedStatus' => $statusFilter,
            'selectedService' => $serviceFilter,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'hasDb' => $hasDb,
            'totalCount' => $totalOrdersCount,
            'availableStatuses' => [
                'Received', 'Washing', 'Drying', 'Ironing',
                'Ready for Pickup', 'Out for Delivery', 'Completed', 'Cancelled'
            ],
            'availableServices' => [
                'Wash & Fold', 'Dry Cleaning', 'Ironing', 'Wash Only', 'Express Wash', 'Premium Wash'
            ],
        ]);
    }

    /**
     * Store a new laundry order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'nullable|string|max:50',
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|max:50',
            'customer_email' => 'nullable|email|max:150',
            'customer_address' => 'nullable|string|max:255',
            'service_type' => 'required|string|max:100',
            'weight' => 'required|numeric|min:0.1',
            'items_count' => 'required|integer|min:1',
            'order_date' => 'nullable|string',
            'pickup_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'total_price' => 'required|numeric|min:0',
            'status' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('orders');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        $status = $validated['status'] ?? 'Received';
        $orderDate = !empty($validated['order_date']) ? date('Y-m-d H:i:s', strtotime($validated['order_date'])) : date('Y-m-d H:i:s');
        $pickupDate = $validated['pickup_date'] ?? date('Y-m-d', strtotime('+1 day'));
        $deliveryDate = $validated['delivery_date'] ?? date('Y-m-d', strtotime('+2 days'));

        $order = null;
        $orderNumber = $validated['order_number'] ?? null;

        if ($hasDb) {
            try {
                if (empty($orderNumber)) {
                    $nextNum = 1;
                    if (Schema::hasColumn('orders', 'id')) {
                        $lastOrder = Order::orderBy('id', 'desc')->first();
                        $nextNum = $lastOrder ? ($lastOrder->id + 1) : 1;
                    } else {
                        $nextNum = Order::count() + 1;
                    }
                    $orderNumber = 'ORD-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
                }

                // Find or link customer
                $customerId = null;
                try {
                    $customer = Customer::where('phone', $validated['customer_phone'])->first();
                    $customerId = $customer ? ($customer->id ?? null) : null;
                } catch (\Throwable $ce) {
                    $customerId = null;
                }

                $order = Order::create([
                    'order_number' => $orderNumber,
                    'customer_id' => $customerId,
                    'customer_name' => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'],
                    'customer_email' => $validated['customer_email'] ?? null,
                    'customer_address' => $validated['customer_address'] ?? null,
                    'service_type' => $validated['service_type'],
                    'weight' => $validated['weight'],
                    'items_count' => $validated['items_count'],
                    'order_date' => $orderDate,
                    'pickup_date' => $pickupDate,
                    'delivery_date' => $deliveryDate,
                    'total_price' => $validated['total_price'],
                    'status' => $status,
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Add default main service item
                if (Schema::hasTable('order_items')) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'service_name' => $validated['service_type'],
                        'quantity' => $validated['weight'] . ' kg',
                        'unit_price' => round($validated['total_price'] / max(1, $validated['weight']), 2),
                        'subtotal' => $validated['total_price'],
                    ]);
                }

                // Sync to Pickup & Delivery
                if (Schema::hasTable('pickup_deliveries')) {
                    $pickupStatus = match(strtolower(trim($status))) {
                        'out for delivery' => 'In Transit',
                        'completed' => 'Delivered',
                        'ready for pickup' => 'Picked Up',
                        default => 'Scheduled',
                    };

                    PickupDelivery::updateOrCreate(
                        ['order_number' => $order->order_number],
                        [
                            'customer_name' => $order->customer_name,
                            'customer_phone' => $order->customer_phone,
                            'service_type' => $order->service_type,
                            'total_amount' => $order->total_price,
                            'pickup_date' => $order->pickup_date,
                            'pickup_time' => '09:00 AM',
                            'pickup_address' => $order->customer_address ?: 'Customer Location',
                            'delivery_date' => $order->delivery_date,
                            'delivery_time' => '02:00 PM',
                            'delivery_address' => $order->customer_address ?: 'Customer Location',
                            'status' => $pickupStatus,
                            'notes' => $order->notes,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                Log::warning("Order DB store failed, continuing with session: " . $e->getMessage());
                $order = null;
            }
        }

        // Always sync in session orders
        $list = $this->getSessionOrders();
        $maxId = collect($list)->max('id') ?? 0;
        $newId = $order && isset($order->id) ? $order->id : ($maxId + 1);
        if (empty($orderNumber)) {
            $orderNumber = 'ORD-' . str_pad($newId, 4, '0', STR_PAD_LEFT);
        }

        $sessionOrder = [
            'id' => $newId,
            'order_number' => $orderNumber,
            'customer_id' => null,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? 'customer@example.com',
            'customer_address' => $validated['customer_address'] ?? 'Siem Reap, Cambodia',
            'service_type' => $validated['service_type'],
            'weight' => (float)$validated['weight'],
            'items_count' => (int)$validated['items_count'],
            'order_date' => $orderDate,
            'pickup_date' => $pickupDate,
            'delivery_date' => $deliveryDate,
            'total_price' => (float)$validated['total_price'],
            'status' => $status,
            'notes' => $validated['notes'] ?? '',
            'items' => [
                [
                    'id' => 1,
                    'service_name' => $validated['service_type'],
                    'quantity' => $validated['weight'] . ' kg',
                    'unit_price' => round($validated['total_price'] / max(1, $validated['weight']), 2),
                    'subtotal' => (float)$validated['total_price'],
                ]
            ]
        ];

        array_unshift($list, $sessionOrder);
        session(['mock_orders' => $list]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Order {$orderNumber} created successfully for {$validated['customer_name']}!",
                'order' => $order ?? $sessionOrder
            ]);
        }

        return redirect()->route('orders.index')
                         ->with('success', "Order {$orderNumber} created successfully for {$validated['customer_name']}!");
    }

    /**
     * Show order details for modal view.
     *
     * @param int|string $id
     * @param int|string|null $optionalId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, $optionalId = null)
    {
        if ($id instanceof \Illuminate\Http\Request) {
            $id = $optionalId ?? $id->route('id') ?? $id->route('order');
        }

        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('orders');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $order = Order::with(['items', 'customer'])->find($id)
                ?? Order::with(['items', 'customer'])->where('order_number', $id)->first();

            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }

            return response()->json([
                'success' => true,
                'order' => $order,
                'items' => $order->items
            ]);
        } else {
            $list = $this->getSessionOrders();
            $order = collect($list)->firstWhere('id', (int)$id)
                ?? collect($list)->firstWhere('order_number', $id);

            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }

            return response()->json([
                'success' => true,
                'order' => $order,
                'items' => $order['items'] ?? []
            ]);
        }
    }

    /**
     * Update order details & status.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|max:50',
            'customer_email' => 'nullable|email|max:150',
            'customer_address' => 'nullable|string|max:255',
            'service_type' => 'required|string|max:100',
            'weight' => 'required|numeric|min:0.1',
            'items_count' => 'required|integer|min:1',
            'status' => 'required|string',
            'pickup_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'total_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('orders');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $order = Order::find($id) ?? Order::where('order_number', $id)->firstOrFail();
            $order->update($validated);

            // Sync to Pickup & Delivery
            if (Schema::hasTable('pickup_deliveries')) {
                $pickupStatus = match(strtolower(trim($validated['status'] ?? ''))) {
                    'out for delivery' => 'In Transit',
                    'completed' => 'Delivered',
                    'ready for pickup' => 'Picked Up',
                    default => 'Scheduled',
                };

                PickupDelivery::updateOrCreate(
                    ['order_number' => $order->order_number],
                    [
                        'customer_name' => $order->customer_name,
                        'customer_phone' => $order->customer_phone,
                        'service_type' => $order->service_type,
                        'total_amount' => $order->total_price,
                        'pickup_date' => $order->pickup_date,
                        'delivery_date' => $order->delivery_date,
                        'delivery_address' => $order->customer_address,
                        'status' => $pickupStatus,
                        'notes' => $order->notes,
                    ]
                );
            }

            return redirect()->route('orders.index')
                             ->with('success', "Order {$order->order_number} updated successfully!");
        } else {
            $list = $this->getSessionOrders();
            $updated = false;

            foreach ($list as &$item) {
                if ($item['id'] == $id || $item['order_number'] == $id) {
                    $item['customer_name'] = $validated['customer_name'];
                    $item['customer_phone'] = $validated['customer_phone'];
                    if (isset($validated['customer_email'])) $item['customer_email'] = $validated['customer_email'];
                    if (isset($validated['customer_address'])) $item['customer_address'] = $validated['customer_address'];
                    $item['service_type'] = $validated['service_type'];
                    $item['weight'] = (float)$validated['weight'];
                    $item['items_count'] = (int)$validated['items_count'];
                    $item['status'] = $validated['status'];
                    if (isset($validated['pickup_date'])) $item['pickup_date'] = $validated['pickup_date'];
                    if (isset($validated['delivery_date'])) $item['delivery_date'] = $validated['delivery_date'];
                    $item['total_price'] = (float)$validated['total_price'];
                    if (isset($validated['notes'])) $item['notes'] = $validated['notes'];
                    $updated = true;
                    break;
                }
            }
            unset($item);

            if ($updated) {
                session(['mock_orders' => $list]);
                return redirect()->route('orders.index')
                                 ->with('success', "Order {$id} updated successfully!");
            }

            return redirect()->route('orders.index')->with('error', 'Order not found.');
        }
    }

    /**
     * Cancel an order.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request, $id)
    {
        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('orders');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $order = Order::find($id) ?? Order::where('order_number', $id)->firstOrFail();
            $order->update(['status' => 'Cancelled']);

            return redirect()->route('orders.index')
                             ->with('success', "Order {$order->order_number} has been cancelled.");
        } else {
            $list = $this->getSessionOrders();
            $cancelled = false;

            foreach ($list as &$item) {
                if ($item['id'] == $id || $item['order_number'] == $id) {
                    $item['status'] = 'Cancelled';
                    $cancelled = true;
                    break;
                }
            }
            unset($item);

            if ($cancelled) {
                session(['mock_orders' => $list]);
                return redirect()->route('orders.index')
                                 ->with('success', "Order {$id} has been cancelled.");
            }

            return redirect()->route('orders.index')->with('error', 'Order not found.');
        }
    }

    /**
     * Delete an order.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('orders');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $order = Order::find($id) ?? Order::where('order_number', $id)->first();
            if ($order) {
                $orderNum = $order->order_number;
                $order->delete();
                return redirect()->route('orders.index')
                                 ->with('success', "Order {$orderNum} deleted successfully.");
            }
            return redirect()->route('orders.index')->with('error', 'Order not found.');
        } else {
            $list = $this->getSessionOrders();
            $filtered = collect($list)->reject(function ($item) use ($id) {
                return $item['id'] == $id || $item['order_number'] == $id;
            })->values()->all();

            session(['mock_orders' => $filtered]);
            return redirect()->route('orders.index')
                             ->with('success', "Order deleted successfully.");
        }
    }

    /**
     * Printable Order Receipt.
     *
     * @param int|string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function printReceipt($id)
    {
        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('orders');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $order = Order::with(['items', 'customer'])->find($id)
                ?? Order::with(['items', 'customer'])->where('order_number', $id)->firstOrFail();
            $items = $order->items;
        } else {
            $list = $this->getSessionOrders();
            $order = collect($list)->firstWhere('id', (int)$id)
                ?? collect($list)->firstWhere('order_number', $id);

            if (!$order) {
                abort(404, 'Order not found');
            }
            $items = $order['items'] ?? [];
        }

        return view('pages.receipt', [
            'order' => $order,
            'items' => $items,
        ]);
    }
}
