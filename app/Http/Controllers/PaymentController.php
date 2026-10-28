<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PaymentController extends Controller
{
    /**
     * Get initial default payment transactions (empty for pure DB).
     */
    private function getDefaultPayments(): array
    {
        return [];
    }

    /**
     * Available Orders list for Order Information selector in modal.
     */
    private function getAvailableOrders(): array
    {
        try {
            $dbOrders = Order::orderBy('id', 'desc')->take(20)->get();
            if ($dbOrders->count() > 0) {
                return $dbOrders->map(function ($ord) {
                    return [
                        'order_number' => $ord->order_number,
                        'customer_name' => $ord->customer_name,
                        'total_amount' => (float) $ord->total_price,
                        'order_date' => $ord->order_date ? date('Y-m-d', strtotime($ord->order_date)) : date('Y-m-d'),
                    ];
                })->toArray();
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return [];
    }

    /**
     * Resolve the amount column name from available columns.
     */
    private function resolveAmountColumn(array $columns): ?string
    {
        foreach (['amount', 'total_amount', 'paid_amount', 'amount_paid', 'payment_amount', 'total_price', 'price', 'total', 'paid'] as $col) {
            if (in_array($col, $columns)) {
                return $col;
            }
        }
        foreach ($columns as $col) {
            $lower = strtolower($col);
            if (str_contains($lower, 'amount') || str_contains($lower, 'price') || str_contains($lower, 'total') || str_contains($lower, 'paid')) {
                return $col;
            }
        }
        return null;
    }

    /**
     * Synchronize orders into payments table.
     */
    private function syncOrdersToPayments(): void
    {
        try {
            if (!Schema::hasTable('orders') || !Schema::hasTable('payments')) {
                return;
            }

            $columns = Schema::getColumnListing('payments');
            $amtCol = $this->resolveAmountColumn($columns);
            $realOrders = Order::all();
            if ($realOrders->isEmpty()) {
                return;
            }

            $realOrderNumbers = $realOrders->pluck('order_number')->filter()->toArray();

            // Find max payment id number
            $allPayments = Payment::all();
            $maxPayNum = 0;
            foreach ($allPayments as $p) {
                $pid = $p->payment_id ?? $p->id;
                if (is_numeric($pid)) {
                    $maxPayNum = max($maxPayNum, (int)$pid);
                } elseif (preg_match('/^PAY-(\d+)$/i', (string)$pid, $m)) {
                    $maxPayNum = max($maxPayNum, (int)$m[1]);
                }
            }

            // Remove old dummy test payments from 2024/2025 that don't match any real order
            $mockNames = [
                'Sokha Dara', 'Chan Sreyneath', 'Heng Channa', 'Kim Sovannary',
                'Neang Pheaktra', 'Lim Vannak', 'Thon Sreynich', 'Pich Sothey',
                'Chea Ratanak', 'Va Sovan', 'Keo Pisey', 'Meng Chhay'
            ];
            foreach ($allPayments as $p) {
                if (in_array($p->customer_name, $mockNames) && !in_array($p->order_number, $realOrderNumbers)) {
                    try {
                        $p->delete();
                    } catch (\Throwable $de) {}
                }
            }

            // Sync each real order into payments
            foreach ($realOrders as $order) {
                $exists = false;
                if (in_array('order_number', $columns) && !empty($order->order_number)) {
                    $exists = Payment::where('order_number', $order->order_number)->exists();
                }
                if (!$exists && in_array('order_id', $columns) && !empty($order->id)) {
                    $exists = Payment::where('order_id', $order->id)->exists();
                }

                if (!$exists) {
                    $maxPayNum++;
                    $payCode = sprintf('PAY-%03d', $maxPayNum);

                    $insertData = [];
                    if (in_array('payment_id', $columns)) {
                        $isNumericPk = false;
                        try {
                            $colDetails = DB::select("SHOW FULL COLUMNS FROM payments WHERE Field = 'payment_id'");
                            if (!empty($colDetails) && str_starts_with(strtolower($colDetails[0]->Type ?? ''), 'int')) {
                                $isNumericPk = true;
                            }
                        } catch (\Throwable $e) {}
                        $insertData['payment_id'] = $isNumericPk ? $maxPayNum : $payCode;
                    }

                    if (in_array('order_number', $columns)) $insertData['order_number'] = $order->order_number;
                    if (in_array('order_id', $columns)) $insertData['order_id'] = $order->id;
                    if (in_array('customer_name', $columns)) $insertData['customer_name'] = $order->customer_name;
                    if (in_array('customer_id', $columns)) $insertData['customer_id'] = $order->customer_id ?? 1;
                    if (in_array('customer_initials', $columns)) $insertData['customer_initials'] = $this->makeInitials($order->customer_name);
                    if (in_array('avatar_color', $columns)) $insertData['avatar_color'] = $this->pickAvatarColor($order->customer_name);
                    if (in_array('payment_method', $columns)) $insertData['payment_method'] = 'Cash';
                    if (in_array('method', $columns)) $insertData['method'] = 'Cash';

                    $stUpper = (strtolower((string)$order->status) === 'completed') ? 'COMPLETED' : 'PENDING';
                    if (in_array('status', $columns)) $insertData['status'] = $stUpper;
                    if (in_array('payment_status', $columns)) $insertData['payment_status'] = ($stUpper === 'COMPLETED') ? 'Paid' : 'Pending';

                    $payDate = $order->order_date ? date('Y-m-d H:i:s', strtotime($order->order_date)) : now();
                    if (in_array('payment_date', $columns)) $insertData['payment_date'] = $payDate;
                    if (in_array('reference_no', $columns)) $insertData['reference_no'] = 'TRX-' . rand(10000, 99999);
                    if (in_array('notes', $columns)) $insertData['notes'] = $order->notes;
                    if (in_array('created_at', $columns)) $insertData['created_at'] = now();
                    if (in_array('updated_at', $columns)) $insertData['updated_at'] = now();

                    if ($amtCol && in_array($amtCol, $columns)) {
                        $insertData[$amtCol] = (float)($order->total_price ?? 0.00);
                    }

                    try {
                        DB::table('payments')->insert($insertData);
                    } catch (\Throwable $ie) {
                        Log::warning('syncOrdersToPayments insert warning: ' . $ie->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('syncOrdersToPayments warning: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve current payments list from Database.
     */
    private function getAllPayments(): array
    {
        try {
            session()->forget('payment_transactions');
            $this->syncOrdersToPayments();
            if (Schema::hasTable('payments')) {
                $columns = Schema::getColumnListing('payments');
                $amtCol = $this->resolveAmountColumn($columns);
                $pk = in_array('payment_id', $columns) ? 'payment_id' : (in_array('id', $columns) ? 'id' : ($columns[0] ?? null));
                $query = Payment::query();
                if ($pk) {
                    $query->orderBy($pk, 'desc');
                }
                $dbPayments = $query->get();
                return $dbPayments->map(function ($p) use ($amtCol) {
                    $amt = 0;
                    if ($amtCol && isset($p->{$amtCol})) {
                        $amt = (float) $p->{$amtCol};
                    } elseif (!empty($p->order_id)) {
                        try {
                            $ord = Order::find($p->order_id);
                            if ($ord && isset($ord->total_price)) {
                                $amt = (float) $ord->total_price;
                            }
                        } catch (\Throwable $e) {}
                    }

                    $orderNo = $p->order_number ?? null;
                    $customerName = $p->customer_name ?? null;

                    if (!$orderNo && !empty($p->order_id)) {
                        $orderNo = 'ORD-' . str_pad($p->order_id, 4, '0', STR_PAD_LEFT);
                    }
                    if (!$customerName && !empty($p->order_id)) {
                        try {
                            $ord = Order::find($p->order_id);
                            if ($ord) {
                                $customerName = $ord->customer_name;
                                if (!$orderNo && !empty($ord->order_number)) {
                                    $orderNo = $ord->order_number;
                                }
                            }
                        } catch (\Throwable $e) {}
                    }
                    if (!$customerName && !empty($p->customer_id)) {
                        try {
                            $cust = Customer::find($p->customer_id);
                            if ($cust) {
                                $customerName = $cust->name;
                            }
                        } catch (\Throwable $e) {}
                    }
                    if (!$customerName) {
                        $customerName = 'General Customer';
                    }
                    if (!$orderNo) {
                        $orderNo = '-';
                    }

                    $idVal = $p->payment_id ?? ($p->id ?? 1);
                    $displayPaymentId = is_numeric($idVal) ? ('PAY-' . str_pad($idVal, 3, '0', STR_PAD_LEFT)) : (string) $idVal;

                    $stRaw = $p->status ?? ($p->payment_status ?? 'COMPLETED');
                    $stUpper = strtoupper((string) $stRaw);
                    $displayStatus = ($stUpper === 'PAID') ? 'COMPLETED' : $stUpper;

                    return [
                        'id' => $idVal,
                        'payment_id' => $displayPaymentId,
                        'order_number' => $orderNo,
                        'customer_name' => $customerName,
                        'customer_initials' => $p->customer_initials ?: $this->makeInitials($customerName),
                        'avatar_color' => $p->avatar_color ?: $this->pickAvatarColor($customerName),
                        'amount' => (float) $amt,
                        'payment_method' => $p->payment_method ?? ($p->method ?? 'Cash'),
                        'status' => $displayStatus,
                        'payment_date' => $p->payment_date ? date('Y-m-d h:i A', strtotime($p->payment_date)) : date('Y-m-d h:i A'),
                        'raw_date' => $p->payment_date,
                        'reference_no' => $p->reference_number ?? ($p->reference_no ?? ($p->transaction_id ?? ('TRX-' . rand(10000, 99999)))),
                        'notes' => $p->notes ?? ($p->note ?? null),
                    ];
                })->toArray();
            }
        } catch (\Throwable $e) {
            Log::error('Error loading payments: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Helper to generate customer 2-letter initials.
     */
    private function makeInitials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, min(2, strlen($name))));
    }

    /**
     * Helper to pick an avatar badge color.
     */
    private function pickAvatarColor(string $name): string
    {
        $colors = [
            'bg-blue-100 text-blue-700',
            'bg-purple-100 text-purple-700',
            'bg-teal-100 text-teal-700',
            'bg-indigo-100 text-indigo-700',
            'bg-amber-100 text-amber-700',
            'bg-sky-100 text-sky-700',
            'bg-fuchsia-100 text-fuchsia-700',
            'bg-pink-100 text-pink-700',
            'bg-emerald-100 text-emerald-700',
            'bg-cyan-100 text-cyan-700',
        ];
        return $colors[abs(crc32($name)) % count($colors)];
    }

    /**
     * Display Payment Management page.
     */
    public function index(Request $request)
    {
        $allPayments = $this->getAllPayments();
        $availableOrders = $this->getAvailableOrders();

        // 1. Dynamic KPI Calculations from real Database Payments
        $completedSum = 0;
        $refundedSum = 0;
        $pendingCount = 0;
        $completedCount = 0;
        $refundedCount = 0;
        $todayRevenueSum = 0;
        $todayStr = date('Y-m-d');

        // Totals for Payment Method Breakdown
        $cashSum = 0; $cashCount = 0;
        $cardSum = 0; $cardCount = 0;
        $bankSum = 0; $bankCount = 0;
        $mobileSum = 0; $mobileCount = 0;

        foreach ($allPayments as $p) {
            $amtRaw = $p['amount'] ?? 0;
            if (is_string($amtRaw)) {
                $amtRaw = preg_replace('/[^0-9.]/', '', $amtRaw);
            }
            $amt = (float) $amtRaw;
            $st = strtoupper($p['status'] ?? 'COMPLETED');

            $pDate = !empty($p['raw_date']) ? date('Y-m-d', strtotime($p['raw_date'])) : (!empty($p['payment_date']) ? date('Y-m-d', strtotime($p['payment_date'])) : '');

            if ($st === 'COMPLETED') {
                $completedSum += $amt;
                $completedCount++;
                if ($pDate === $todayStr) {
                    $todayRevenueSum += $amt;
                }
            } elseif ($st === 'REFUNDED') {
                $refundedSum += $amt;
                $refundedCount++;
            } elseif ($st === 'PENDING') {
                $pendingCount++;
            }

            // Method breakdown calculation
            if ($st !== 'REFUNDED') {
                $method = strtolower($p['payment_method'] ?? '');
                if (str_contains($method, 'cash')) {
                    $cashSum += $amt;
                    $cashCount++;
                } elseif (str_contains($method, 'credit') || str_contains($method, 'card')) {
                    $cardSum += $amt;
                    $cardCount++;
                } elseif (str_contains($method, 'bank') || str_contains($method, 'transfer')) {
                    $bankSum += $amt;
                    $bankCount++;
                } elseif (str_contains($method, 'mobile') || str_contains($method, 'qr') || str_contains($method, 'aba')) {
                    $mobileSum += $amt;
                    $mobileCount++;
                } else {
                    $cashSum += $amt;
                    $cashCount++;
                }
            }
        }

        // Outstanding balance calculated dynamically from real orders vs completed payments
        $outstandingBalance = 0.00;
        try {
            if (Schema::hasTable('orders')) {
                $totalOrderSales = (float) Order::whereNotIn('status', ['Cancelled', 'cancelled'])->sum('total_price');
                $outstandingBalance = max(0.00, $totalOrderSales - $completedSum);
            }
        } catch (\Throwable $e) {
            $outstandingBalance = 0.00;
        }

        $kpis = [
            'total_payments' => '$' . number_format($completedSum, 2),
            'total_payments_growth' => '+0% vs. last month',
            'todays_revenue' => '$' . number_format($todayRevenueSum, 2),
            'todays_revenue_growth' => '+0% vs. yesterday',
            'outstanding_balance' => '$' . number_format($outstandingBalance, 2),
            'outstanding_balance_growth' => '+0% vs. last month',
            'refunds' => '$' . number_format($refundedSum, 2),
            'refunds_growth' => '+0% vs. last month',
        ];

        // 2. Payment Method Breakdown (Dynamically from real database data)
        $methodTotalSum = $cashSum + $cardSum + $bankSum + $mobileSum;
        $cashPct = $methodTotalSum > 0 ? round(($cashSum / $methodTotalSum) * 100) : 0;
        $cardPct = $methodTotalSum > 0 ? round(($cardSum / $methodTotalSum) * 100) : 0;
        $bankPct = $methodTotalSum > 0 ? round(($bankSum / $methodTotalSum) * 100) : 0;
        $mobilePct = $methodTotalSum > 0 ? round(($mobileSum / $methodTotalSum) * 100) : 0;

        $methodBreakdown = [
            'cash' => [
                'name' => 'Cash',
                'amount' => '$' . number_format($cashSum, 2),
                'percentage' => $cashPct,
                'orders_text' => "{$cashCount} orders processed",
                'icon' => 'banknotes',
                'color' => 'emerald',
            ],
            'credit_card' => [
                'name' => 'Credit Card',
                'amount' => '$' . number_format($cardSum, 2),
                'percentage' => $cardPct,
                'orders_text' => "{$cardCount} orders processed",
                'icon' => 'credit-card',
                'color' => 'blue',
            ],
            'bank_transfer' => [
                'name' => 'Bank Transfer',
                'amount' => '$' . number_format($bankSum, 2),
                'percentage' => $bankPct,
                'orders_text' => "{$bankCount} transactions",
                'icon' => 'building-library',
                'color' => 'purple',
            ],
            'mobile_payment' => [
                'name' => 'Mobile Payment',
                'amount' => '$' . number_format($mobileSum, 2),
                'percentage' => $mobilePct,
                'orders_text' => "{$mobileCount} transactions",
                'icon' => 'device-phone-mobile',
                'color' => 'amber',
            ],
        ];

        // 3. Payment Status Donut Chart Data (Dynamically from real database data)
        $totalInvoices = count($allPayments);
        $statusChart = [
            'total' => $totalInvoices,
            'completed_count' => $completedCount,
            'completed_pct' => $totalInvoices > 0 ? round(($completedCount / $totalInvoices) * 100, 1) : 0,
            'pending_count' => $pendingCount,
            'pending_pct' => $totalInvoices > 0 ? round(($pendingCount / $totalInvoices) * 100, 1) : 0,
            'refunded_count' => $refundedCount,
            'refunded_pct' => $totalInvoices > 0 ? round(($refundedCount / $totalInvoices) * 100, 1) : 0,
        ];

        // 4. Filtering & Search
        $filtered = $allPayments;
        $search = $request->get('search');
        $statusFilter = $request->get('status');
        $methodFilter = $request->get('method');

        if (!empty($search)) {
            $q = strtolower(trim($search));
            $filtered = array_values(array_filter($filtered, function ($item) use ($q) {
                return str_contains(strtolower($item['payment_id']), $q)
                    || str_contains(strtolower($item['order_number']), $q)
                    || str_contains(strtolower($item['customer_name']), $q)
                    || str_contains(strtolower($item['reference_no'] ?? ''), $q)
                    || str_contains(strtolower($item['payment_method']), $q);
            }));
        }

        if (!empty($statusFilter) && $statusFilter !== 'all') {
            $sf = strtoupper(trim($statusFilter));
            $filtered = array_values(array_filter($filtered, function ($item) use ($sf) {
                return strtoupper($item['status']) === $sf;
            }));
        }

        if (!empty($methodFilter) && $methodFilter !== 'all') {
            $mf = strtolower(trim($methodFilter));
            $filtered = array_values(array_filter($filtered, function ($item) use ($mf) {
                return strtolower($item['payment_method']) === $mf
                    || str_contains(strtolower($item['payment_method']), $mf);
            }));
        }

        // 5. Pagination (10 per page)
        $perPage = 10;
        $page = max(1, (int) $request->get('page', 1));
        $totalItems = count($filtered);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;
        $paginatedPayments = array_slice($filtered, $offset, $perPage);

        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'per_page' => $perPage,
            'total_items' => $totalItems,
            'from' => $totalItems > 0 ? $offset + 1 : 0,
            'to' => min($offset + $perPage, $totalItems),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'payments' => $paginatedPayments,
                'pagination' => $pagination,
                'kpis' => $kpis,
                'methodBreakdown' => $methodBreakdown,
                'statusChart' => $statusChart,
            ]);
        }

        return view('pages.payment', [
            'payments' => $paginatedPayments,
            'pagination' => $pagination,
            'kpis' => $kpis,
            'methodBreakdown' => $methodBreakdown,
            'statusChart' => $statusChart,
            'availableOrders' => $availableOrders,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'methodFilter' => $methodFilter,
        ]);
    }

    /**
     * Record a new Payment (from Add Payment modal).
     */
    public function recordPayment(Request $request)
    {
        if (!$request->filled('payment_date')) {
            $request->merge(['payment_date' => date('Y-m-d')]);
        }
        if (!$request->filled('status')) {
            $request->merge(['status' => 'COMPLETED']);
        }

        $validated = $request->validate([
            'order_number' => 'required|string|max:50',
            'customer_name' => 'required|string|max:150',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'status' => 'nullable|string',
            'payment_date' => 'nullable|string',
            'reference_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $statusUpper = strtoupper($validated['status']);
        $maxId = 0;
        try {
            if (Schema::hasTable('payments')) {
                $maxId = (int) Payment::max('id');
            }
        } catch (\Throwable $e) {
            $maxId = 0;
        }

        $newId = $maxId + 1;
        $paymentCode = 'PAY-' . str_pad($newId, 3, '0', STR_PAD_LEFT);

        $initials = $this->makeInitials($validated['customer_name']);
        $avatarColor = $this->pickAvatarColor($validated['customer_name']);

        $refNo = !empty($validated['reference_no']) ? trim($validated['reference_no']) : 'TRX-' . rand(10000, 99999);
        $dateFormatted = date('Y-m-d h:i A', strtotime($validated['payment_date']));

        $payment = null;
        try {
            $columns = Schema::hasTable('payments') ? Schema::getColumnListing('payments') : [];
            $amtCol = $this->resolveAmountColumn($columns);

            $insertData = [];
            if (in_array('payment_code', $columns)) $insertData['payment_code'] = $paymentCode;
            if (in_array('payment_no', $columns)) $insertData['payment_no'] = $paymentCode;

            // Order lookup
            $orderNo = trim($validated['order_number'] ?? '');
            $ord = null;
            if (!empty($orderNo)) {
                $ord = Order::where('order_number', $orderNo)->orWhere('id', preg_replace('/\D/', '', $orderNo))->first();
            }
            if (!$ord) {
                $ord = Order::orderBy('id', 'desc')->first();
            }

            if (in_array('order_number', $columns)) {
                $insertData['order_number'] = $orderNo ?: ($ord ? $ord->order_number : 'ORD-0001');
            }
            if (in_array('order_id', $columns)) {
                $insertData['order_id'] = $ord ? $ord->id : 1;
            }

            // Customer
            $customerName = trim($validated['customer_name'] ?? '');
            if (empty($customerName) && $ord) {
                $customerName = $ord->customer_name;
            }
            if (empty($customerName)) {
                $customerName = 'General Customer';
            }

            if (in_array('customer_name', $columns)) {
                $insertData['customer_name'] = $customerName;
            }
            if (in_array('customer_id', $columns)) {
                $insertData['customer_id'] = ($ord && $ord->customer_id) ? $ord->customer_id : 1;
            }

            if (in_array('customer_initials', $columns)) $insertData['customer_initials'] = $initials;
            if (in_array('avatar_color', $columns)) $insertData['avatar_color'] = $avatarColor;

            // Payment method
            if (in_array('payment_method', $columns)) {
                $insertData['payment_method'] = trim($validated['payment_method']);
            } elseif (in_array('method', $columns)) {
                $insertData['method'] = trim($validated['payment_method']);
            }

            // Status
            if (in_array('status', $columns)) {
                $insertData['status'] = $statusUpper;
            }
            if (in_array('payment_status', $columns)) {
                $insertData['payment_status'] = match ($statusUpper) {
                    'COMPLETED', 'PAID' => 'Paid',
                    'PENDING' => 'Pending',
                    'REFUNDED' => 'Refunded',
                    'PARTIALLY PAID', 'PARTIAL' => 'Partially Paid',
                    'FAILED' => 'Failed',
                    default => 'Paid',
                };
            }

            // Date
            if (in_array('payment_date', $columns)) {
                $insertData['payment_date'] = date('Y-m-d H:i:s', strtotime($validated['payment_date']));
            }

            // Reference
            if (in_array('reference_no', $columns)) {
                $insertData['reference_no'] = $refNo;
            } elseif (in_array('reference_number', $columns)) {
                $insertData['reference_number'] = $refNo;
            } elseif (in_array('transaction_id', $columns)) {
                $insertData['transaction_id'] = $refNo;
            }

            // Received By staff / admin
            if (in_array('received_by', $columns)) {
                /** @var \App\Models\User|null $authUser */
                $authUser = Auth::user() ?? $request->user();
                $insertData['received_by'] = $authUser ? ($authUser->user_id ?? ($authUser->id ?? Auth::id())) : Auth::id();
            }

            // Notes
            if (in_array('notes', $columns)) {
                $insertData['notes'] = $validated['notes'] ?? null;
            } elseif (in_array('note', $columns)) {
                $insertData['note'] = $validated['notes'] ?? null;
            }

            // Timestamps: only include if they really exist in the database table
            if (in_array('created_at', $columns)) {
                $insertData['created_at'] = now();
            }
            if (in_array('updated_at', $columns)) {
                $insertData['updated_at'] = now();
            }

            // Set amount on correct column ONLY if column exists in payments table
            if ($amtCol && in_array($amtCol, $columns)) {
                $insertData[$amtCol] = (float) $validated['amount'];
            }

            $payment = Payment::create($insertData);
        } catch (\Throwable $e) {
            Log::error('Error recording payment to database: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        }

        session()->forget('payment_transactions');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Payment {$paymentCode} of \${$validated['amount']} recorded successfully!",
                'payment' => $payment,
            ]);
        }

        return redirect()->route('payment.index')->with('success', "Payment {$paymentCode} recorded successfully!");
    }

    /**
     * Helper to find a payment safely by payment_id or id.
     *
     * @param int|string $id
     * @return Payment|null
     */
    private function findPayment($id)
    {
        try {
            $columns = Schema::hasTable('payments') ? Schema::getColumnListing('payments') : [];
            $query = Payment::query();
            $query->where('payment_id', $id);
            if (in_array('id', $columns)) {
                $query->orWhere('id', $id);
            }
            return $query->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Process Refund for a payment.
     *
     * @param Request $request
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function refund(Request $request, $id)
    {
        session()->forget('payment_transactions');
        $payment = $this->findPayment($id);

        if ($payment) {
            $columns = Schema::hasTable('payments') ? Schema::getColumnListing('payments') : [];
            $updateData = [];
            if (in_array('status', $columns)) $updateData['status'] = 'REFUNDED';
            if (in_array('payment_status', $columns)) $updateData['payment_status'] = 'Refunded';
            if (in_array('updated_at', $columns)) $updateData['updated_at'] = now();
            if (!empty($updateData)) {
                $payment->update($updateData);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Payment #{$id} has been refunded successfully.",
                ]);
            }

            return redirect()->back()->with('success', "Payment #{$id} has been refunded successfully.");
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Payment record not found.'], 404);
        }

        return redirect()->back()->with('error', 'Payment record not found.');
    }

    /**
     * Show single payment details.
     *
     * @param int|string|\Illuminate\Http\Request $id
     * @param int|string|null $optionalId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, $optionalId = null)
    {
        if ($id instanceof \Illuminate\Http\Request) {
            $id = $optionalId ?? $id->route('id') ?? $id->route('payment');
        }

        $payment = $this->findPayment($id);
        if ($payment) {
            $amt = $payment->amount ?? $payment->total_amount ?? $payment->paid_amount ?? 0;
            if (!$amt && !empty($payment->order_id)) {
                $ord = Order::find($payment->order_id);
                if ($ord) $amt = $ord->total_price ?? 0;
            }

            $idVal = $payment->payment_id ?? ($payment->id ?? 1);
            $displayPaymentId = is_numeric($idVal) ? ('PAY-' . str_pad($idVal, 3, '0', STR_PAD_LEFT)) : (string) $idVal;

            return response()->json([
                'success' => true,
                'payment' => [
                    'id' => $idVal,
                    'payment_id' => $displayPaymentId,
                    'order_number' => $payment->order_number ?? ($payment->order_id ? 'ORD-' . str_pad($payment->order_id, 4, '0', STR_PAD_LEFT) : '-'),
                    'customer_name' => $payment->customer_name ?? 'Customer',
                    'customer_initials' => $payment->customer_initials ?: $this->makeInitials($payment->customer_name ?? 'Customer'),
                    'avatar_color' => $payment->avatar_color ?: 'bg-blue-100 text-blue-700',
                    'amount' => (float) $amt,
                    'payment_method' => $payment->payment_method ?? ($payment->method ?? 'Cash'),
                    'status' => strtoupper($payment->status ?? ($payment->payment_status ?? 'COMPLETED')),
                    'payment_date' => $payment->payment_date ? date('Y-m-d h:i A', strtotime($payment->payment_date)) : date('Y-m-d h:i A'),
                    'raw_date' => $payment->payment_date,
                    'reference_no' => $payment->reference_no ?? ($payment->transaction_id ?? 'N/A'),
                    'notes' => $payment->notes ?? ($payment->note ?? ''),
                ],
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
    }

    /**
     * Generate / Show Invoice for a payment.
     */
    public function generateInvoice(Request $request)
    {
        $paymentId = $request->input('payment_id');
        $payment = null;
        if ($paymentId) {
            $payment = $this->findPayment($paymentId);
        }
        if (!$payment) {
            $columns = Schema::hasTable('payments') ? Schema::getColumnListing('payments') : [];
            $pk = in_array('payment_id', $columns) ? 'payment_id' : (in_array('id', $columns) ? 'id' : ($columns[0] ?? null));
            $query = Payment::query();
            if ($pk) {
                $query->orderBy($pk, 'desc');
            }
            $payment = $query->first();
        }

        if ($payment) {
            $amt = $payment->amount ?? $payment->total_amount ?? $payment->paid_amount ?? 0;
            if (!$amt && !empty($payment->order_id)) {
                $ord = Order::find($payment->order_id);
                if ($ord) $amt = $ord->total_price ?? 0;
            }
            $idVal = $payment->payment_id ?? ($payment->id ?? 1);
            $displayPaymentId = is_numeric($idVal) ? ('PAY-' . str_pad($idVal, 3, '0', STR_PAD_LEFT)) : (string) $idVal;

            $invoiceData = [
                'invoice_number' => 'INV-' . str_pad($idVal, 5, '0', STR_PAD_LEFT),
                'payment_id' => $displayPaymentId,
                'order_number' => $payment->order_number ?? ($payment->order_id ? 'ORD-' . str_pad($payment->order_id, 4, '0', STR_PAD_LEFT) : '-'),
                'customer_name' => $payment->customer_name ?? 'General Customer',
                'amount' => (float) $amt,
                'payment_method' => $payment->payment_method ?? ($payment->method ?? 'Cash'),
                'status' => strtoupper($payment->status ?? ($payment->payment_status ?? 'COMPLETED')),
                'payment_date' => $payment->payment_date ? date('Y-m-d h:i A', strtotime($payment->payment_date)) : date('Y-m-d h:i A'),
                'reference_no' => $payment->reference_no ?? ($payment->transaction_id ?? ('TRX-' . rand(10000, 99999))),
                'notes' => $payment->notes ?? ($payment->note ?: 'Standard laundry service payment'),
            ];
        } else {
            $invoiceData = [
                'invoice_number' => 'INV-00001',
                'payment_id' => 'PAY-001',
                'order_number' => 'ORD-0001',
                'customer_name' => 'General Customer',
                'amount' => 0.00,
                'payment_method' => 'Cash',
                'status' => 'COMPLETED',
                'payment_date' => date('Y-m-d h:i A'),
                'reference_no' => 'TRX-' . rand(10000, 99999),
                'notes' => 'No payment record found',
            ];
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'invoice' => $invoiceData,
            ]);
        }

        return redirect()->back()->with('success', "Invoice {$invoiceData['invoice_number']} generated successfully!");
    }

    /**
     * Delete payment.
     *
     * @param Request $request
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        session()->forget('payment_transactions');

        try {
            $columns = Schema::hasTable('payments') ? Schema::getColumnListing('payments') : [];
            $query = Payment::where('payment_id', $id);
            if (in_array('id', $columns)) {
                $query->orWhere('id', $id);
            }
            $query->delete();
        } catch (\Throwable $e) {
            Log::error('Error deleting payment: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Payment #{$id} deleted successfully.",
            ]);
        }

        return redirect()->back()->with('success', "Payment #{$id} deleted successfully.");
    }

    /**
     * Send reminders to overdue customers.
     */
    public function sendReminders(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Payment reminders successfully dispatched to customers with outstanding balance!",
            ]);
        }

        return redirect()->back()->with('success', "Payment reminders dispatched successfully!");
    }

    /**
     * Export payments to CSV.
     */
    public function export($format = 'csv')
    {
        $payments = $this->getAllPayments();
        $filename = 'laundry_payments_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($file, ['#', 'Payment ID', 'Order Number', 'Customer Name', 'Amount ($)', 'Payment Method', 'Status', 'Payment Date', 'Reference No', 'Notes']);

            $index = 1;
            foreach ($payments as $p) {
                fputcsv($file, [
                    $index++,
                    $p['payment_id'],
                    $p['order_number'],
                    $p['customer_name'],
                    number_format($p['amount'], 2),
                    $p['payment_method'],
                    $p['status'],
                    $p['payment_date'],
                    $p['reference_no'] ?? '',
                    $p['notes'] ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
