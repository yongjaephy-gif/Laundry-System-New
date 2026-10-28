<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class MachineController extends Controller
{
    /**
     * Ensure all necessary columns exist on the machines table.
     */
    protected function ensureTableStructure(): void
    {
        try {
            if (!Schema::hasTable('machines')) {
                return;
            }

            $cols = Schema::getColumnListing('machines');

            Schema::table('machines', function (\Illuminate\Database\Schema\Blueprint $table) use (&$cols) {
                if (!in_array('name', $cols) && !in_array('machine_name', $cols)) {
                    $table->string('name')->nullable();
                }
                if (!in_array('type', $cols) && !in_array('machine_type', $cols)) {
                    $table->string('type', 100)->nullable();
                }
                if (!in_array('capacity', $cols)) {
                    $table->string('capacity', 50)->nullable()->default('-');
                }
                if (!in_array('model', $cols)) {
                    $table->string('model', 100)->nullable();
                }
                if (!in_array('location', $cols)) {
                    $table->string('location', 100)->nullable()->default('Laundry Room 1');
                }
                if (!in_array('status', $cols)) {
                    $table->string('status', 50)->default('Available');
                }
                if (!in_array('last_maintenance', $cols)) {
                    $table->string('last_maintenance', 50)->nullable();
                }
                if (!in_array('next_maintenance', $cols)) {
                    $table->string('next_maintenance', 50)->nullable();
                }
                if (!in_array('maintenance_history', $cols)) {
                    $table->json('maintenance_history')->nullable();
                }
                if (!in_array('notes', $cols)) {
                    $table->text('notes')->nullable();
                }
            });
        } catch (\Throwable $e) {
            Log::warning('ensureTableStructure warning: ' . $e->getMessage());
        }
    }

    /**
     * Safely find machine by id or machine_id without column not found errors.
     */
    protected function findMachine($id)
    {
        if (is_object($id)) {
            $id = $id->machine_id ?? ($id->id ?? 1);
        }

        try {
            $cols = Schema::getColumnListing('machines');
            $query = Machine::query();

            if (in_array('id', $cols) && in_array('machine_id', $cols)) {
                $query->where('id', $id)->orWhere('machine_id', $id);
            } elseif (in_array('machine_id', $cols)) {
                $query->where('machine_id', $id);
            } elseif (in_array('id', $cols)) {
                $query->where('id', $id);
            }

            return $query->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Map request input to matching table columns.
     */
    protected function buildMachinePayload(Request $request, string $machineId, ?array $history = null): array
    {
        $cols = Schema::getColumnListing('machines');
        $payload = [];

        $nowDate = $request->input('last_maintenance', date('Y-m-d')) ?: date('Y-m-d');
        $nextDate = $request->input('next_maintenance', date('Y-m-d', strtotime('+30 days'))) ?: date('Y-m-d', strtotime('+30 days'));

        // Detect if machine_id in MySQL is integer or auto_increment
        $isMachineIdInt = false;
        $isAutoIncrement = false;
        try {
            $colInfo = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM machines WHERE Field = 'machine_id'");
            if (!empty($colInfo)) {
                $colType = strtolower($colInfo[0]->Type ?? '');
                $extra = strtolower($colInfo[0]->Extra ?? '');
                if (str_contains($colType, 'int')) {
                    $isMachineIdInt = true;
                }
                if (str_contains($extra, 'auto_increment')) {
                    $isAutoIncrement = true;
                }
            }
        } catch (\Throwable $e) {}

        if (in_array('machine_id', $cols)) {
            if ($isAutoIncrement) {
                // If machine_id is an AUTO_INCREMENT column, omit it so MySQL assigns it automatically
            } elseif ($isMachineIdInt) {
                // If it's an INT column (not auto_increment), store numeric value
                $numId = intval(preg_replace('/\D/', '', $machineId));
                if ($numId <= 0) {
                    $numId = (int) (Machine::max('machine_id') ?? 0) + 1;
                }
                $payload['machine_id'] = $numId;
            } else {
                // Standard VARCHAR machine_id (WM-001, etc.)
                $payload['machine_id'] = $machineId;
            }
        }

        $machineName = trim($request->input('name') ?: $request->input('machine_name', ''));
        if (in_array('name', $cols)) $payload['name'] = $machineName;
        if (in_array('machine_name', $cols)) $payload['machine_name'] = $machineName;

        $machineType = trim($request->input('type') ?: $request->input('machine_type', 'Front Load'));
        if (in_array('type', $cols)) $payload['type'] = $machineType;
        if (in_array('machine_type', $cols)) $payload['machine_type'] = $machineType;

        if (in_array('capacity', $cols)) $payload['capacity'] = trim($request->input('capacity', '-')) ?: '-';
        if (in_array('model', $cols)) $payload['model'] = trim($request->input('model', 'Standard Model')) ?: 'Standard Model';
        if (in_array('location', $cols)) $payload['location'] = trim($request->input('location', 'Laundry Room 1')) ?: 'Laundry Room 1';

        $status = in_array($request->status, ['Available', 'In Use', 'Under Maintenance']) ? $request->status : 'Available';
        if (in_array('status', $cols)) $payload['status'] = $status;

        if (in_array('last_maintenance', $cols)) $payload['last_maintenance'] = $nowDate;
        if (in_array('next_maintenance', $cols)) $payload['next_maintenance'] = $nextDate;
        if (in_array('maintenance_history', $cols) && $history !== null) $payload['maintenance_history'] = $history;
        if (in_array('notes', $cols)) $payload['notes'] = trim($request->input('notes', 'Registered laundry unit.'));

        return $payload;
    }

    /**
     * Display a listing of machines from MySQL Database.
     */
    public function index(Request $request)
    {
        $this->ensureTableStructure();

        $search = trim($request->input('search', ''));
        $statusFilter = trim($request->input('status', ''));
        $typeFilter = trim($request->input('type', ''));

        $query = Machine::query();
        $cols = Schema::getColumnListing('machines');

        // 1. Search Filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search, $cols) {
                $hasWhere = false;
                if (in_array('name', $cols)) {
                    $q->where('name', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('machine_name', $cols)) {
                    $hasWhere ? $q->orWhere('machine_name', 'like', "%{$search}%") : $q->where('machine_name', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('machine_id', $cols)) {
                    $hasWhere ? $q->orWhere('machine_id', 'like', "%{$search}%") : $q->where('machine_id', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('model', $cols)) {
                    $hasWhere ? $q->orWhere('model', 'like', "%{$search}%") : $q->where('model', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('type', $cols)) {
                    $hasWhere ? $q->orWhere('type', 'like', "%{$search}%") : $q->where('type', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('location', $cols)) {
                    $hasWhere ? $q->orWhere('location', 'like', "%{$search}%") : $q->where('location', 'like', "%{$search}%");
                }
            });
        }

        // 2. Status Filter
        if (!empty($statusFilter) && $statusFilter !== 'All Status') {
            if (in_array('status', $cols)) {
                $query->where('status', $statusFilter);
            }
        }

        // 3. Type Filter
        if (!empty($typeFilter) && $typeFilter !== 'All Types') {
            if (in_array('type', $cols)) {
                $query->where('type', $typeFilter);
            } elseif (in_array('machine_type', $cols)) {
                $query->where('machine_type', $typeFilter);
            }
        }

        // Safe order by primary key without hardcoding 'id'
        $pk = in_array('machine_id', $cols) ? 'machine_id' : (in_array('id', $cols) ? 'id' : null);
        if ($pk) {
            $query->orderBy($pk, 'asc');
        }

        $items = $query->get();

        // Real Database Stats
        $stats = [
            'total' => Machine::count(),
            'available' => Machine::where('status', 'Available')->count(),
            'in_use' => Machine::where('status', 'In Use')->count(),
            'maintenance' => Machine::where('status', 'Under Maintenance')->count(),
        ];

        // Machine types for filter dropdown
        $dbTypes = [];
        if (in_array('type', $cols)) {
            $dbTypes = Machine::select('type')->distinct()->whereNotNull('type')->where('type', '!=', '')->pluck('type')->toArray();
        }
        $defaultTypes = ['Dryer', 'Front Load', 'Ironing', 'Steam', 'Top Load'];
        $types = array_values(array_unique(array_merge($defaultTypes, $dbTypes)));
        sort($types);

        // AJAX JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'items' => $items,
                'stats' => $stats,
                'count' => count($items),
            ]);
        }

        return view('pages.machine', [
            'machines' => $items,
            'stats' => $stats,
            'types' => $types,
            'search' => $search,
            'statusFilter' => $statusFilter ?: 'All Status',
            'typeFilter' => $typeFilter ?: 'All Types',
        ]);
    }

    /**
     * Get a specific machine's details.
     */
    public function show($id, $optionalId = null)
    {
        if ($id instanceof Request) {
            $id = $optionalId ?? $id->route('id') ?? $id->route('machine');
        }

        $machine = $this->findMachine($id);

        if (!$machine) {
            return response()->json([
                'success' => false,
                'message' => 'Machine not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'machine' => $machine,
        ]);
    }

    /**
     * Store a newly created machine in MySQL database.
     */
    public function store(Request $request)
    {
        $this->ensureTableStructure();

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'capacity' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:Available,In Use,Under Maintenance',
            'last_maintenance' => 'nullable|string|max:50',
            'next_maintenance' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $machineId = trim($request->input('machine_id', ''));
        if (empty($machineId)) {
            $count = Machine::count();
            $maxNum = 0;
            try {
                $existing = Machine::all();
                foreach ($existing as $ex) {
                    $mVal = $ex->getRawOriginal('machine_id') ?? ($ex->getRawOriginal('id') ?? '');
                    $num = intval(preg_replace('/\D/', '', (string)$mVal));
                    if ($num > $maxNum) {
                        $maxNum = $num;
                    }
                }
            } catch (\Throwable $e) {}
            $nextNum = max($count + 1, $maxNum + 1);
            $machineId = sprintf('WM-%03d', $nextNum);
        }

        $nowDate = $request->input('last_maintenance', date('Y-m-d')) ?: date('Y-m-d');
        $history = [
            [
                'date' => $nowDate,
                'type' => 'Initial Setup',
                'note' => 'Machine registered into system',
            ],
        ];

        $payload = $this->buildMachinePayload($request, $machineId, $history);
        
        try {
            $machine = Machine::create($payload);
        } catch (\Throwable $e) {
            Log::error('Machine registration error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to register machine: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to register machine: ' . $e->getMessage())->withInput();
        }

        $displayName = $machine->name ?? ($payload['name'] ?? ($payload['machine_name'] ?? 'Machine'));
        $displayId = $machine->machine_id ?? $machineId;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Machine {$displayId} ({$displayName}) registered successfully!",
                'machine' => $machine,
            ]);
        }

        return redirect()->route('machines.index')->with('success', "Machine {$displayId} registered successfully!");
    }

    /**
     * Update the specified machine in MySQL database.
     */
    public function update(Request $request, $id)
    {
        $machine = $this->findMachine($id);

        if (!$machine) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Machine not found.'], 404);
            }
            return redirect()->route('machines.index')->with('error', 'Machine not found.');
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:100',
        ]);

        $oldStatus = $machine->status ?? 'Available';
        $newStatus = $request->input('status', $oldStatus);

        $cols = Schema::getColumnListing('machines');
        $updates = [];

        if (in_array('name', $cols)) $updates['name'] = trim($request->input('name', $machine->name));
        if (in_array('machine_name', $cols)) $updates['machine_name'] = trim($request->input('name', $machine->name));

        if (in_array('type', $cols)) $updates['type'] = trim($request->input('type', $machine->type));
        if (in_array('machine_type', $cols)) $updates['machine_type'] = trim($request->input('type', $machine->type));

        if (in_array('capacity', $cols)) $updates['capacity'] = trim($request->input('capacity', $machine->capacity ?? '-')) ?: '-';
        if (in_array('model', $cols)) $updates['model'] = trim($request->input('model', $machine->model ?? ''));
        if (in_array('location', $cols)) $updates['location'] = trim($request->input('location', $machine->location ?? 'Laundry Room 1'));

        if (in_array('status', $cols)) {
            $updates['status'] = in_array($newStatus, ['Available', 'In Use', 'Under Maintenance']) ? $newStatus : $oldStatus;
        }

        if (in_array('last_maintenance', $cols)) {
            $updates['last_maintenance'] = $request->input('last_maintenance', $machine->last_maintenance ?? date('Y-m-d'));
        }
        if (in_array('next_maintenance', $cols)) {
            $updates['next_maintenance'] = $request->input('next_maintenance', $machine->next_maintenance ?? date('Y-m-d', strtotime('+30 days')));
        }
        if (in_array('notes', $cols)) {
            $updates['notes'] = trim($request->input('notes', $machine->notes ?? ''));
        }

        if (in_array('maintenance_history', $cols)) {
            $history = is_array($machine->maintenance_history) ? $machine->maintenance_history : [];

            // If status changed to Under Maintenance, automatically log it in maintenance history
            if ($newStatus === 'Under Maintenance' && $oldStatus !== 'Under Maintenance') {
                array_unshift($history, [
                    'date' => date('Y-m-d'),
                    'type' => 'Maintenance Scheduled',
                    'note' => 'Status changed to Under Maintenance: ' . ($request->input('notes') ?: 'Routine service required'),
                ]);
                $updates['maintenance_history'] = $history;
            }

            // If user submitted an extra maintenance log entry
            if ($request->filled('log_type') && $request->filled('log_note')) {
                array_unshift($history, [
                    'date' => $request->input('log_date', date('Y-m-d')),
                    'type' => trim($request->input('log_type')),
                    'note' => trim($request->input('log_note')),
                ]);
                $updates['maintenance_history'] = $history;
            }
        }

        $machine->update($updates);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Machine {$machine->machine_id} updated successfully in database!",
                'machine' => $machine,
            ]);
        }

        return redirect()->route('machines.index')->with('success', "Machine {$machine->machine_id} updated successfully!");
    }

    /**
     * Remove the specified machine from MySQL database.
     */
    public function destroy(Request $request, $id)
    {
        $machine = $this->findMachine($id);

        $deletedName = '';
        if ($machine) {
            $deletedName = "{$machine->machine_id} - {$machine->name}";
            $machine->delete();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Machine {$deletedName} deleted successfully from database!",
            ]);
        }

        return redirect()->route('machines.index')->with('success', "Machine {$deletedName} deleted successfully!");
    }

    /**
     * Quick status update endpoint.
     */
    public function updateStatus(Request $request, $id)
    {
        $status = $request->input('status');
        if (!in_array($status, ['Available', 'In Use', 'Under Maintenance'])) {
            return response()->json(['success' => false, 'message' => 'Invalid status'], 422);
        }

        $machine = $this->findMachine($id);

        if ($machine) {
            $machine->update(['status' => $status]);

            return response()->json([
                'success' => true,
                'message' => "Machine status updated to {$status} in database",
                'machine' => $machine,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Machine not found'], 404);
    }

    /**
     * Clear all machines in MySQL database.
     */
    public function reset(Request $request)
    {
        try {
            if (Schema::hasTable('machines')) {
                Machine::truncate();
            }
        } catch (\Throwable $e) {
            Log::error('Reset machines error: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Machines table has been cleared in database.',
            ]);
        }

        return redirect()->route('machines.index')->with('success', 'Machines table has been cleared in database.');
    }
}
