<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * Compute item status from stock and min_stock.
     */
    protected function determineStatus($stock, $minStock = 15): string
    {
        $stock = (int)$stock;
        $minStock = (int)$minStock;
        if ($stock <= 0) {
            return 'Out of Stock';
        }
        if ($stock <= $minStock) {
            return 'Low Stock';
        }
        return 'In Stock';
    }

    /**
     * Ensure database table exists without injecting mock data.
     */
    protected function ensureTableExists(): void
    {
        try {
            $model = new Inventory();
            $table = $model->getTable();

            // Create table if neither inventories, inventory nor inventory_items exist
            if (!Schema::hasTable($table)) {
                Schema::create('inventories', function ($t) {
                    $t->id();
                    $t->string('name');
                    $t->string('category')->default('Cleaning');
                    $t->integer('stock')->default(0);
                    $t->string('unit')->default('L');
                    $t->string('status')->default('In Stock');
                    $t->integer('min_stock')->default(15);
                    $t->text('notes')->nullable();
                    $t->string('supplier')->default('Standard Supplier');
                    $t->decimal('unit_price', 10, 2)->default(0.00);
                    $t->date('last_restocked')->nullable();
                    $t->timestamps();
                });
            }
        } catch (\Throwable $e) {
            Log::error('Inventory table check error: ' . $e->getMessage());
        }
    }

    /**
     * Map request data to matching table columns.
     */
    protected function buildItemPayload(Request $request, string $table, ?int $existingStock = null): array
    {
        $cols = Schema::getColumnListing($table);
        $payload = [];

        $name = trim($request->input('name') ?? $request->input('item_name', ''));
        $category = trim($request->input('category', 'Cleaning'));
        $stock = (int)($request->input('stock') ?? $request->input('quantity', $existingStock ?? 0));
        $unit = trim($request->input('unit') ?? $request->input('unit_of_measure', 'L'));
        $minStock = (int)$request->input('min_stock', 15);
        $status = $this->determineStatus($stock, $minStock);
        $notes = $request->input('notes') ?? $request->input('description', '');
        $supplier = $request->input('supplier') ?? $request->input('supplier_name', 'Standard Supplier');
        $unitPrice = (float)($request->input('unit_price') ?? $request->input('price', 0.00));

        if (in_array('name', $cols)) $payload['name'] = $name;
        if (in_array('item_name', $cols)) $payload['item_name'] = $name;

        if (in_array('category', $cols)) $payload['category'] = $category;
        if (in_array('category_name', $cols)) $payload['category_name'] = $category;

        if (in_array('stock', $cols)) $payload['stock'] = $stock;
        if (in_array('quantity', $cols)) $payload['quantity'] = $stock;
        if (in_array('qty', $cols)) $payload['qty'] = $stock;

        if (in_array('unit', $cols)) $payload['unit'] = $unit;
        if (in_array('unit_of_measure', $cols)) $payload['unit_of_measure'] = $unit;
        if (in_array('uom', $cols)) $payload['uom'] = $unit;

        if (in_array('status', $cols)) $payload['status'] = $status;

        if (in_array('min_stock', $cols)) $payload['min_stock'] = $minStock;
        if (in_array('minimum_stock', $cols)) $payload['minimum_stock'] = $minStock;
        if (in_array('reorder_level', $cols)) $payload['reorder_level'] = $minStock;

        if (in_array('notes', $cols)) $payload['notes'] = $notes;
        if (in_array('description', $cols)) $payload['description'] = $notes;

        if (in_array('supplier', $cols)) $payload['supplier'] = $supplier;
        if (in_array('supplier_name', $cols)) $payload['supplier_name'] = $supplier;

        if (in_array('unit_price', $cols)) $payload['unit_price'] = $unitPrice;
        if (in_array('price', $cols)) $payload['price'] = $unitPrice;
        if (in_array('cost', $cols)) $payload['cost'] = $unitPrice;

        if (in_array('last_restocked', $cols)) $payload['last_restocked'] = now()->toDateString();
        if (in_array('restocked_at', $cols)) $payload['restocked_at'] = now()->toDateString();

        return $payload;
    }

    /**
     * Display inventory dashboard list directly from MySQL database.
     */
    public function index(Request $request)
    {
        $this->ensureTableExists();

        $search = trim($request->input('search', ''));
        $categoryFilter = $request->input('category', 'all');
        $statusFilter = $request->input('status', 'all');

        $model = new Inventory();
        $table = $model->getTable();
        $cols = Schema::getColumnListing($table);

        $query = Inventory::query();

        // 1. Search Filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search, $cols) {
                $hasWhere = false;
                if (in_array('name', $cols)) {
                    $q->where('name', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('item_name', $cols)) {
                    $hasWhere ? $q->orWhere('item_name', 'like', "%{$search}%") : $q->where('item_name', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('category', $cols)) {
                    $hasWhere ? $q->orWhere('category', 'like', "%{$search}%") : $q->where('category', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('unit', $cols)) {
                    $hasWhere ? $q->orWhere('unit', 'like', "%{$search}%") : $q->where('unit', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('supplier', $cols)) {
                    $hasWhere ? $q->orWhere('supplier', 'like', "%{$search}%") : $q->where('supplier', 'like', "%{$search}%");
                }
            });
        }

        // 2. Category Filter
        if (!empty($categoryFilter) && $categoryFilter !== 'all' && $categoryFilter !== 'All Categories') {
            if (in_array('category', $cols)) {
                $query->where('category', $categoryFilter);
            } elseif (in_array('category_name', $cols)) {
                $query->where('category_name', $categoryFilter);
            }
        }

        // 3. Status Filter
        if (!empty($statusFilter) && $statusFilter !== 'all' && $statusFilter !== 'All Status') {
            if (in_array('status', $cols)) {
                $query->where('status', $statusFilter);
            }
        }

        // Live Real Database Metrics
        $totalItems = Inventory::count();
        $inStockCount = Inventory::where('status', 'In Stock')->count();
        $lowStockCount = Inventory::whereIn('status', ['Low Stock', 'Out of Stock'])->count();

        $stats = [
            'total' => $totalItems,
            'in_stock' => $inStockCount,
            'low_stock' => $lowStockCount,
        ];

        $pk = $model->getKeyName();
        $items = $query->orderBy($pk, 'asc')->get();

        $categories = ['Cleaning', 'Packaging', 'Accessories', 'Chemicals'];
        $statuses = ['In Stock', 'Low Stock', 'Out of Stock'];

        $viewName = view()->exists('pages.inventory') ? 'pages.inventory' : 'inventory';

        return view($viewName, [
            'items' => $items,
            'stats' => $stats,
            'categories' => $categories,
            'statuses' => $statuses,
            'search' => $search,
            'categoryFilter' => $categoryFilter,
            'statusFilter' => $statusFilter,
        ]);
    }

    /**
     * Show single inventory item details.
     */
    public function show($id, $optionalId = null)
    {
        $request = request();
        if ($id instanceof Request) {
            $request = $id;
            $id = $optionalId ?? $request->route('id') ?? $request->route('inventory');
        }

        $id = is_object($id) ? ($id->id ?? $id->inventory_id ?? 1) : $id;
        $model = new Inventory();
        $pk = $model->getKeyName();

        $item = Inventory::where($pk, $id)->first() ?: Inventory::find($id);

        return response()->json([
            'success' => (bool)$item,
            'item' => $item
        ]);
    }

    /**
     * Add Laundry Supplies / Create new item directly into MySQL.
     */
    public function store(Request $request)
    {
        $name = $request->input('name') ?? $request->input('item_name');
        $stock = $request->input('stock') ?? $request->input('quantity', 0);
        $request->merge([
            'name' => $name,
            'stock' => $stock
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'min_stock' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'supplier' => 'nullable|string|max:255',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $this->ensureTableExists();

        $model = new Inventory();
        $table = $model->getTable();
        $payload = $this->buildItemPayload($request, $table);

        $newItem = Inventory::create($payload);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplies added successfully to database!',
                'item' => $newItem
            ]);
        }

        return redirect()->back()->with('success', 'Supplies item "' . $request->name . '" added successfully!');
    }

    /**
     * Update Inventory Item directly in MySQL.
     */
    public function update(Request $request, $id)
    {
        $id = is_object($id) ? ($id->id ?? $id->inventory_id ?? 1) : $id;

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'min_stock' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'supplier' => 'nullable|string|max:255',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $model = new Inventory();
        $pk = $model->getKeyName();
        $item = Inventory::where($pk, $id)->first() ?: Inventory::find($id);

        if ($item) {
            $table = $model->getTable();
            $payload = $this->buildItemPayload($request, $table, $item->stock);
            $item->update($payload);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplies updated successfully in database!',
                'item' => $item
            ]);
        }

        return redirect()->back()->with('success', 'Supplies updated successfully!');
    }

    /**
     * Stock In function: Adds quantity directly into MySQL database.
     */
    public function stockIn(Request $request, $id)
    {
        $id = is_object($id) ? ($id->id ?? $id->inventory_id ?? 1) : $id;
        $qty = abs((int)$request->input('quantity', 1));

        $model = new Inventory();
        $pk = $model->getKeyName();
        $item = Inventory::where($pk, $id)->first() ?: Inventory::find($id);

        if ($item) {
            $table = $model->getTable();
            $cols = Schema::getColumnListing($table);
            $stockCol = in_array('stock', $cols) ? 'stock' : (in_array('quantity', $cols) ? 'quantity' : 'qty');

            $currentStock = (int)($item->{$stockCol} ?? 0);
            $newStock = $currentStock + $qty;
            $minStock = $item->min_stock ?: 15;
            $newStatus = $this->determineStatus($newStock, $minStock);

            $updates = [$stockCol => $newStock];
            if (in_array('status', $cols)) {
                $updates['status'] = $newStatus;
            }
            if (in_array('last_restocked', $cols)) {
                $updates['last_restocked'] = now()->toDateString();
            }

            $item->update($updates);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Stock in of +{$qty} successfully recorded in database!",
                'item' => $item
            ]);
        }

        return redirect()->back()->with('success', "Stock in of +{$qty} recorded successfully!");
    }

    /**
     * Stock Out function: Deducts quantity directly from MySQL database.
     */
    public function stockOut(Request $request, $id)
    {
        $id = is_object($id) ? ($id->id ?? $id->inventory_id ?? 1) : $id;
        $qty = abs((int)$request->input('quantity', 1));

        $model = new Inventory();
        $pk = $model->getKeyName();
        $item = Inventory::where($pk, $id)->first() ?: Inventory::find($id);

        if ($item) {
            $table = $model->getTable();
            $cols = Schema::getColumnListing($table);
            $stockCol = in_array('stock', $cols) ? 'stock' : (in_array('quantity', $cols) ? 'quantity' : 'qty');

            $currentStock = (int)($item->{$stockCol} ?? 0);
            $newStock = max(0, $currentStock - $qty);
            $minStock = $item->min_stock ?: 15;
            $newStatus = $this->determineStatus($newStock, $minStock);

            $updates = [$stockCol => $newStock];
            if (in_array('status', $cols)) {
                $updates['status'] = $newStatus;
            }

            $item->update($updates);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Stock out of -{$qty} recorded in database!",
                'item' => $item
            ]);
        }

        return redirect()->back()->with('success', "Stock out of -{$qty} recorded successfully!");
    }

    /**
     * Delete Inventory Item directly from MySQL database.
     */
    public function destroy(Request $request, $id)
    {
        $id = is_object($id) ? ($id->id ?? $id->inventory_id ?? 1) : $id;

        $model = new Inventory();
        $pk = $model->getKeyName();
        $item = Inventory::where($pk, $id)->first() ?: Inventory::find($id);

        if ($item) {
            $item->delete();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Inventory item deleted successfully from database!'
            ]);
        }

        return redirect()->back()->with('success', 'Inventory item removed successfully!');
    }

    /**
     * Clear all items in MySQL database table.
     */
    public function reset(Request $request)
    {
        $model = new Inventory();
        $table = $model->getTable();

        if (Schema::hasTable($table)) {
            DB::table($table)->truncate();
        }

        return redirect()->back()->with('success', 'Inventory table has been cleared.');
    }
}
