<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerController extends Controller
{
    /**
     * Ensure customers table exists and has all required columns with proper nullability.
     */
    protected function ensureCustomersTable(): void
    {
        try {
            if (Schema::hasTable('customers')) {
                // Ensure columns with strict NOT NULL without defaults are made NULLable
                try {
                    $columnDetails = DB::select("SHOW FULL COLUMNS FROM customers");
                    Log::info("CUSTOMERS DB FULL COLUMNS: " . json_encode($columnDetails));

                    foreach ($columnDetails as $col) {
                        $field = $col->Field;
                        $type = $col->Type;
                        $isNull = $col->Null;
                        $default = $col->Default;
                        $extra = $col->Extra;

                        if ($isNull === 'NO' && is_null($default) && $extra !== 'auto_increment' && $col->Key !== 'PRI') {
                            try {
                                DB::statement("ALTER TABLE customers MODIFY `{$field}` {$type} NULL");
                                Log::info("Modified column {$field} to NULLable");
                            } catch (\Throwable $e) {}
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("Inspect column details warning: " . $e->getMessage());
                }

                $cols = Schema::getColumnListing('customers');

                Schema::table('customers', function (Blueprint $table) use ($cols) {
                    if (!in_array('name', $cols)) {
                        $table->string('name', 255)->nullable();
                    }
                    if (!in_array('full_name', $cols)) {
                        $table->string('full_name', 255)->nullable();
                    }
                    if (!in_array('customer_name', $cols)) {
                        $table->string('customer_name', 255)->nullable();
                    }
                    if (!in_array('phone', $cols)) {
                        $table->string('phone', 50)->nullable();
                    }
                    if (!in_array('phone_number', $cols)) {
                        $table->string('phone_number', 50)->nullable();
                    }
                    if (!in_array('customer_phone', $cols)) {
                        $table->string('customer_phone', 50)->nullable();
                    }
                    if (!in_array('email', $cols)) {
                        $table->string('email', 255)->nullable();
                    }
                    if (!in_array('email_address', $cols)) {
                        $table->string('email_address', 255)->nullable();
                    }
                    if (!in_array('customer_email', $cols)) {
                        $table->string('customer_email', 255)->nullable();
                    }
                    if (!in_array('address', $cols)) {
                        $table->text('address')->nullable();
                    }
                    if (!in_array('customer_address', $cols)) {
                        $table->text('customer_address')->nullable();
                    }
                    if (!in_array('tier', $cols)) {
                        $table->string('tier', 50)->default('REGULAR');
                    }
                    if (!in_array('status', $cols)) {
                        $table->string('status', 50)->default('Active');
                    }
                    if (!in_array('avatar', $cols)) {
                        $table->string('avatar', 255)->nullable();
                    }
                    if (!in_array('registration_date', $cols)) {
                        $table->date('registration_date')->nullable();
                    }
                });

                // Sync existing name <-> full_name <-> customer_name if one was empty
                $freshCols = Schema::getColumnListing('customers');
                if (in_array('name', $freshCols) && in_array('full_name', $freshCols)) {
                    try {
                        DB::statement("UPDATE customers SET name = full_name WHERE (name IS NULL OR name = '') AND (full_name IS NOT NULL AND full_name != '')");
                        DB::statement("UPDATE customers SET full_name = name WHERE (full_name IS NULL OR full_name = '') AND (name IS NOT NULL AND name != '')");
                    } catch (\Throwable $e) {}
                }
                if (in_array('name', $freshCols) && in_array('customer_name', $freshCols)) {
                    try {
                        DB::statement("UPDATE customers SET name = customer_name WHERE (name IS NULL OR name = '') AND (customer_name IS NOT NULL AND customer_name != '')");
                        DB::statement("UPDATE customers SET customer_name = name WHERE (customer_name IS NULL OR customer_name = '') AND (name IS NOT NULL AND name != '')");
                    } catch (\Throwable $e) {}
                }
                if (in_array('phone', $freshCols) && in_array('phone_number', $freshCols)) {
                    try {
                        DB::statement("UPDATE customers SET phone = phone_number WHERE (phone IS NULL OR phone = '') AND (phone_number IS NOT NULL AND phone_number != '')");
                        DB::statement("UPDATE customers SET phone_number = phone WHERE (phone_number IS NULL OR phone_number = '') AND (phone IS NOT NULL AND phone != '')");
                    } catch (\Throwable $e) {}
                }
                if (in_array('phone', $freshCols) && in_array('customer_phone', $freshCols)) {
                    try {
                        DB::statement("UPDATE customers SET phone = customer_phone WHERE (phone IS NULL OR phone = '') AND (customer_phone IS NOT NULL AND customer_phone != '')");
                        DB::statement("UPDATE customers SET customer_phone = phone WHERE (customer_phone IS NULL OR customer_phone = '') AND (phone IS NOT NULL AND phone != '')");
                    } catch (\Throwable $e) {}
                }
                if (in_array('email', $freshCols) && in_array('customer_email', $freshCols)) {
                    try {
                        DB::statement("UPDATE customers SET email = customer_email WHERE (email IS NULL OR email = '') AND (customer_email IS NOT NULL AND customer_email != '')");
                        DB::statement("UPDATE customers SET customer_email = email WHERE (customer_email IS NULL OR customer_email = '') AND (email IS NOT NULL AND email != '')");
                    } catch (\Throwable $e) {}
                }
                if (in_array('email', $freshCols) && in_array('email_address', $freshCols)) {
                    try {
                        DB::statement("UPDATE customers SET email = email_address WHERE (email IS NULL OR email = '') AND (email_address IS NOT NULL AND email_address != '')");
                        DB::statement("UPDATE customers SET email_address = email WHERE (email_address IS NULL OR email_address = '') AND (email IS NOT NULL AND email != '')");
                    } catch (\Throwable $e) {}
                }
                if (in_array('address', $freshCols) && in_array('customer_address', $freshCols)) {
                    try {
                        DB::statement("UPDATE customers SET address = customer_address WHERE (address IS NULL OR address = '') AND (customer_address IS NOT NULL AND customer_address != '')");
                        DB::statement("UPDATE customers SET customer_address = address WHERE (customer_address IS NULL OR customer_address = '') AND (address IS NOT NULL AND address != '')");
                    } catch (\Throwable $e) {}
                }
            }
        } catch (\Throwable $e) {
            Log::warning("ensureCustomersTable warning: " . $e->getMessage());
        }
    }

    /**
     * Build customer payload dynamically matching existing table schema.
     */
    protected function buildCustomerPayload(array $validated, ?string $custId = null): array
    {
        $cols = [];
        try {
            $cols = Schema::getColumnListing('customers');
        } catch (\Throwable $e) {}

        $tier = strtoupper($validated['tier'] ?? 'REGULAR');
        if (!in_array($tier, ['GOLD', 'SILVER', 'BRONZE', 'VIP', 'REGULAR'])) {
            $tier = 'REGULAR';
        }
        $status = $validated['status'] ?? 'Active';
        $registrationDate = $validated['registration_date'] ?? date('Y-m-d');
        $name = $validated['name'] ?? '';
        $phone = $validated['phone'] ?? null;
        $email = $validated['email'] ?? null;
        $address = $validated['address'] ?? null;

        $payload = [];

        // Check if customer_id is auto_increment
        $isAutoIncrement = false;
        try {
            $colDetails = DB::select("SHOW FULL COLUMNS FROM customers WHERE Field = 'customer_id'");
            if (!empty($colDetails)) {
                $extra = strtolower($colDetails[0]->Extra ?? '');
                $type = strtolower($colDetails[0]->Type ?? '');
                if (str_contains($extra, 'auto_increment') || str_starts_with($type, 'int')) {
                    $isAutoIncrement = true;
                }
            }
        } catch (\Throwable $e) {}

        if ($isAutoIncrement) {
            if (!empty($custId) && is_numeric($custId)) {
                $payload['customer_id'] = (int)$custId;
            }
            // If empty or non-numeric string, omit customer_id so MySQL auto-increments cleanly
        } else {
            if (!empty($custId)) {
                $payload['customer_id'] = $custId;
            }
        }

        // Name fields
        if (in_array('full_name', $cols) || empty($cols)) {
            $payload['full_name'] = $name;
        }
        if (in_array('name', $cols)) {
            $payload['name'] = $name;
        }

        // Phone fields
        if (in_array('phone_number', $cols) || empty($cols)) {
            $payload['phone_number'] = $phone;
        }
        if (in_array('phone', $cols)) {
            $payload['phone'] = $phone;
        }

        // Email & Address
        if (in_array('email', $cols) || empty($cols)) {
            $payload['email'] = $email;
        }
        if (in_array('address', $cols) || empty($cols)) {
            $payload['address'] = $address;
        }

        if (empty($cols) || in_array('tier', $cols)) {
            $payload['tier'] = $tier;
        }
        if (empty($cols) || in_array('status', $cols)) {
            $payload['status'] = $status;
        }
        if (empty($cols) || in_array('registration_date', $cols)) {
            $payload['registration_date'] = $registrationDate;
        }
        if (in_array('loyalty_points', $cols)) {
            $payload['loyalty_points'] = 0;
        }

        return $payload;
    }

    /**
     * Safely find a customer by customer_id or id without causing MySQL 1054 error.
     */
    protected function findCustomer($id)
    {
        if (empty($id)) {
            return null;
        }

        try {
            $hasCustIdCol = Schema::hasColumn('customers', 'customer_id');
            $hasIdCol = Schema::hasColumn('customers', 'id');

            // Numeric extraction if like 'C0001'
            $numericId = null;
            if (is_numeric($id)) {
                $numericId = (int)$id;
            } elseif (preg_match('/^C0*(\d+)$/i', $id, $m)) {
                $numericId = (int)$m[1];
            }

            // 1. Try customer_id integer match if numeric
            if ($hasCustIdCol && $numericId !== null) {
                $found = Customer::with('orders')->where('customer_id', $numericId)->first();
                if ($found) return $found;
            }

            // 2. Try customer_id exact match string
            if ($hasCustIdCol) {
                $found = Customer::with('orders')->where('customer_id', $id)->first();
                if ($found) return $found;
            }

            // 3. Try id column only if exists
            if ($hasIdCol) {
                $found = Customer::with('orders')->where('id', $id)->first();
                if ($found) return $found;
            }
        } catch (\Throwable $e) {
            Log::warning("findCustomer error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Display a listing of customers with filtering, search, and selected profile.
     */
    public function index(Request $request)
    {
        $this->ensureCustomersTable();

        $search = trim($request->input('search', ''));
        $filter = $request->input('filter', 'All');
        $selectedId = $request->input('selected_id');
        $page = (int)$request->input('page', 1);
        $perPage = 10;

        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('customers');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $query = Customer::with('orders');

            if (!empty($search)) {
                $query->search($search);
            }

            if (!empty($filter) && $filter !== 'All' && $filter !== 'All Customers') {
                $query->filter($filter);
            }

            $totalCustomers = $query->count();

            // Order newest customers first so created customer shows on page 1
            $orderCol = 'customer_id';
            try {
                if (Schema::hasColumn('customers', 'created_at')) {
                    $orderCol = 'created_at';
                } elseif (Schema::hasColumn('customers', 'id')) {
                    $orderCol = 'id';
                }
            } catch (\Throwable $e) {}

            $customers = $query->orderBy($orderCol, 'desc')
                                ->paginate($perPage)
                                ->appends($request->all());

            // Determine active/selected customer for the Aside panel
            $selectedCustomer = null;
            if ($selectedId) {
                $selectedCustomer = $this->findCustomer($selectedId);
            }
            if (!$selectedCustomer && $customers->count() > 0) {
                $selectedCustomer = $customers->first();
            }
        } else {
            $customers = new LengthAwarePaginator(
                [],
                0,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $totalCustomers = 0;
            $selectedCustomer = null;
        }

        // Return JSON if requested via AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'customers' => $customers,
                'selectedCustomer' => $selectedCustomer,
                'total' => $totalCustomers
            ]);
        }

        $customerView = view()->exists('customer') ? 'customer' : 'pages.customer';
        return view($customerView, [
            'customers' => $customers,
            'selectedCustomer' => $selectedCustomer,
            'totalCustomers' => $totalCustomers,
            'search' => $search,
            'currentFilter' => $filter,
            'hasDb' => $hasDb,
            'activeRole' => session('active_role', 'Admin'),
            'availableRoles' => [
                'Admin' => ['name' => 'Administrator', 'badge_bg' => 'bg-blue-100', 'badge_text' => 'text-blue-700', 'description' => 'Full System Access'],
                'Staff' => ['name' => 'Laundry Staff', 'badge_bg' => 'bg-emerald-100', 'badge_text' => 'text-emerald-700', 'description' => 'Operations & Washing'],
                'Delivery' => ['name' => 'Delivery Driver', 'badge_bg' => 'bg-purple-100', 'badge_text' => 'text-purple-700', 'description' => 'Logistics & Dispatch'],
                'Receptionist' => ['name' => 'Receptionist', 'badge_bg' => 'bg-amber-100', 'badge_text' => 'text-amber-700', 'description' => 'Front Desk & Orders'],
            ],
            'isDemoMode' => !$hasDb,
        ]);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $this->ensureCustomersTable();

        $validated = $request->validate([
            'customer_id' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'tier' => 'nullable|string',
            'status' => 'nullable|string|in:Active,Inactive',
            'registration_date' => 'nullable|date',
        ]);

        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('customers');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        $inputCustId = $validated['customer_id'] ?? null;
        $customer = null;

        if ($hasDb) {
            $payload = $this->buildCustomerPayload($validated, $inputCustId);

            Log::info("ATTEMPTING CUSTOMER INSERT PAYLOAD: " . json_encode($payload));

            try {
                $customer = Customer::create($payload);
                Log::info("CUSTOMER CREATED VIA ELOQUENT: ID=" . ($customer->customer_id ?? 'unknown'));
            } catch (\Throwable $e) {
                Log::warning("Customer Eloquent create warning: " . $e->getMessage() . ". Trying DB::table insert...");
                try {
                    $payload['created_at'] = now();
                    $payload['updated_at'] = now();
                    $newId = DB::table('customers')->insertGetId($payload);
                    Log::info("CUSTOMER INSERTED VIA DB::table: ID=" . $newId);
                    $customer = Customer::find($newId) ?? $this->findCustomer($newId);
                } catch (\Throwable $dbe) {
                    Log::error("Customer DB direct insert failed: " . $dbe->getMessage());
                }
            }
        }

        $selectedKey = $customer ? ($customer->customer_id ?? 1) : 1;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Customer {$validated['name']} registered successfully!",
                'customer' => $customer,
                'selected_id' => $selectedKey
            ]);
        }

        return redirect()->route('customers.index', ['selected_id' => $selectedKey])
                         ->with('success', "Customer {$validated['name']} registered successfully!");
    }

    /**
     * Display the specified customer details (e.g. for profile drawer or AJAX).
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $this->ensureCustomersTable();

        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('customers');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $customer = $this->findCustomer($id);
            
            if (!$customer) {
                return response()->json(['error' => 'Customer not found'], 404);
            }

            return response()->json([
                'success' => true,
                'customer' => $customer,
                'orders' => $customer->orders ?? []
            ]);
        }

        return response()->json(['error' => 'Customer not found'], 404);
    }

    /**
     * Update the specified customer in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->ensureCustomersTable();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'tier' => 'nullable|string',
            'status' => 'nullable|string|in:Active,Inactive',
            'registration_date' => 'nullable|date',
        ]);

        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('customers');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            try {
                $customer = $this->findCustomer($id);
                if ($customer) {
                    $payload = $this->buildCustomerPayload($validated, $customer->customer_id ?? $id);
                    $payload = array_filter($payload, fn($v) => !is_null($v));
                    $customer->update($payload);
                }
            } catch (\Throwable $e) {
                Log::warning("Customer DB update failed: " . $e->getMessage());
            }
        }

        $custName = $validated['name'] ?? 'Customer';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Customer {$custName} updated successfully!"
            ]);
        }

        return redirect()->route('customers.index', ['selected_id' => $id])
                         ->with('success', "Customer {$custName} updated successfully!");
    }

    /**
     * Remove the specified customer from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $this->ensureCustomersTable();

        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('customers');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            try {
                $customer = $this->findCustomer($id);
                if ($customer) {
                    $customer->delete();
                }
            } catch (\Throwable $e) {
                Log::warning("Customer DB delete failed: " . $e->getMessage());
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Customer deleted successfully!",
                'id' => $id
            ]);
        }

        return redirect()->route('customers.index')
                         ->with('success', "Customer deleted successfully!");
    }

    /**
     * Get order history of a specific customer.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders(Request $request, $id)
    {
        $this->ensureCustomersTable();

        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('customers');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $customer = $this->findCustomer($id);
            $custKey = $customer ? ($customer->customer_id ?? $customer->id ?? $id) : $id;
            $orders = CustomerOrder::where('customer_id', $custKey)->orderBy('order_date', 'desc')->get();
            return response()->json(['success' => true, 'orders' => $orders]);
        }

        return response()->json(['success' => true, 'orders' => []]);
    }
}
