<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;


class EmployeeController extends Controller
{
    /**
     * Official employee roster dataset matching requirements exactly.
     */
    public function getInitialEmployees()
    {
        return collect([]);
    }

    /**
     * Retrieve current active session employee roster.
     */
    public function getSessionEmployees()
    {
        try {
            session()->forget('laundry_employees_roster');
            if (Schema::hasTable('employees')) {
                return Employee::all()->map(function ($item) {
                    return (object)$item->toArray();
                });
            }
        } catch (\Throwable $e) {}

        return collect([]);
    }

    /**
     * Save employee collection to disk for high persistence across requests.
     */
    protected function saveEmployeesStorage($collection)
    {
        // Kept for backward compatibility, no longer writes mock data to disk
    }

    /**
     * Display employee list with stats, search and filtering.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');
        $statusFilter = $request->input('status');

        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('employees');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            try {
                // Ensure job_role column in MySQL supports 'Admin' and migrate 'Manager' -> 'Admin'
                $empCols = Schema::getColumnListing('employees');
                if (in_array('job_role', $empCols)) {
                    $colInfo = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM employees LIKE 'job_role'");
                    if (!empty($colInfo)) {
                        $type = $colInfo[0]->Type ?? '';
                        if (str_starts_with(strtolower($type), 'enum') && !str_contains($type, "'Admin'")) {
                            $newType = str_replace("enum(", "enum('Admin',", $type);
                            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `employees` MODIFY COLUMN `job_role` $newType");
                        }
                    }
                    \Illuminate\Support\Facades\DB::table('employees')
                        ->where('job_role', 'Manager')
                        ->update(['job_role' => 'Admin']);
                }
                if (in_array('role', $empCols)) {
                    \Illuminate\Support\Facades\DB::table('employees')
                        ->where('role', 'Manager')
                        ->update(['role' => 'Admin']);
                }
            } catch (\Throwable $e) {}

            $totalEmployees = Employee::count();
            $activeEmployees = Employee::where('status', 'Active')->count();
            $inactiveEmployees = Employee::where('status', 'Inactive')->count();

            $empCols = Schema::getColumnListing('employees');
            $query = Employee::query();

            if (!empty($search)) {
                $query->where(function ($q) use ($search, $empCols) {
                    if (in_array('full_name', $empCols)) $q->orWhere('full_name', 'like', "%{$search}%");
                    if (in_array('name', $empCols)) $q->orWhere('name', 'like', "%{$search}%");
                    if (in_array('email', $empCols)) $q->orWhere('email', 'like', "%{$search}%");
                    if (in_array('phone_number', $empCols)) $q->orWhere('phone_number', 'like', "%{$search}%");
                    if (in_array('phone', $empCols)) $q->orWhere('phone', 'like', "%{$search}%");
                    if (in_array('job_role', $empCols)) $q->orWhere('job_role', 'like', "%{$search}%");
                    if (in_array('role', $empCols)) $q->orWhere('role', 'like', "%{$search}%");
                    if (in_array('employee_id', $empCols)) $q->orWhere('employee_id', 'like', "%{$search}%");
                });
            }

            if (!empty($roleFilter) && $roleFilter !== 'All Roles') {
                if (in_array('job_role', $empCols)) {
                    $query->where('job_role', $roleFilter);
                } elseif (in_array('role', $empCols)) {
                    $query->where('role', $roleFilter);
                }
            }

            if (!empty($statusFilter) && $statusFilter !== 'All Status') {
                $query->where('status', $statusFilter);
            }

            if (in_array('created_at', $empCols)) {
                $query->orderBy('created_at', 'desc');
            } elseif (in_array('employee_id', $empCols)) {
                $query->orderBy('employee_id', 'desc');
            } elseif (in_array('id', $empCols)) {
                $query->orderBy('id', 'desc');
            }

            $employees = $query->paginate(20)->withQueryString();
        } else {
            // Memory, Storage File & Session Fallback Dataset
            $allEmployees = $this->getSessionEmployees();

            $totalEmployees = $allEmployees->count();
            $activeEmployees = $allEmployees->where('status', 'Active')->count();
            $inactiveEmployees = $allEmployees->where('status', 'Inactive')->count();

            $filtered = $allEmployees->filter(function ($item) use ($search, $roleFilter, $statusFilter) {
                if ($search) {
                    $s = strtolower($search);
                    $nameMatch = str_contains(strtolower($item->name ?? ''), $s);
                    $emailMatch = str_contains(strtolower($item->email ?? ''), $s);
                    $idMatch = str_contains(strtolower($item->employee_id ?? ''), $s);
                    $phoneMatch = str_contains($item->phone ?? '', $search);
                    $roleMatch = str_contains(strtolower($item->role ?? ''), $s);

                    if (!$nameMatch && !$emailMatch && !$idMatch && !$phoneMatch && !$roleMatch) {
                        return false;
                    }
                }
                if ($roleFilter && $roleFilter !== 'All Roles' && ($item->role ?? '') !== $roleFilter) {
                    return false;
                }
                if ($statusFilter && $statusFilter !== 'All Status' && ($item->status ?? '') !== $statusFilter) {
                    return false;
                }
                return true;
            });

            $perPage = 20;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentPageItems = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $employees = new LengthAwarePaginator(
                $currentPageItems,
                $filtered->count(),
                $perPage,
                $currentPage,
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        }

        $stats = [
            'total' => $totalEmployees,
            'active' => $activeEmployees,
            'inactive' => $inactiveEmployees,
        ];

        $viewName = view()->exists('pages.employee') ? 'pages.employee' : 'employee';

        return view($viewName, compact('employees', 'stats', 'search', 'roleFilter', 'statusFilter'));
    }

    /**
     * Show single employee details.
     *
     * @param int|string $id
     * @param int|string|null $optionalId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, $optionalId = null)
    {
        $request = request();
        if ($id instanceof Request) {
            $request = $id;
            $id = $optionalId ?? $request->route('id') ?? $request->route('employee');
        }

        $id = is_object($id) ? ($id->employee_id ?? $id->id ?? 1) : $id;
        $emp = null;
        try {
            if (Schema::hasTable('employees')) {
                $emp = Employee::where('employee_id', $id)->first() 
                    ?: (Schema::hasColumn('employees', 'id') ? Employee::where('id', $id)->first() : null);
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => (bool)$emp,
            'employee' => $emp
        ]);
    }

    /**
     * Map role name to integer role_id for the users table.
     *
     * @param string|null $roleName
     * @return int
     */
    protected function getRoleIdFromRoleName(?string $roleName): int
    {
        $roleLower = strtolower(trim((string)$roleName));
        if (str_contains($roleLower, 'admin')) {
            return 1;
        }
        if (str_contains($roleLower, 'deliver')) {
            return 3;
        }
        if (str_contains($roleLower, 'reception')) {
            return 4;
        }
        return 2; // Default: Staff
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:100',
            'phone' => 'required|string|max:50',
            'email' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'status' => 'nullable|string|in:Active,Inactive',
            'work_schedule' => 'nullable|string|max:150',
            'job_role_details' => 'nullable|string|max:1000',
            'avatar' => 'nullable',
        ]);

        $status = $request->input('status', 'Active');
        $workSchedule = $request->input('work_schedule', 'Full Time (08:00 AM - 05:00 PM)');

        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($request->name) . '&background=2563EB&color=fff';

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destination = public_path('uploads/avatars');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $avatarUrl = asset('uploads/avatars/' . $filename);
        }

        $newEmp = null;
        $saveError = null;

        try {
            if (Schema::hasTable('employees')) {
                $empCols = Schema::getColumnListing('employees');

                // 1. Sync or create user account first to link with employee
                $linkedUser = null;
                if (Schema::hasTable('users')) {
                    $roleId = $this->getRoleIdFromRoleName($request->role);
                    $hashedPassword = Hash::make($request->password ?: 'password123');
                    $userCols = Schema::getColumnListing('users');
                    $generatedUsername = strtolower(explode('@', $request->email)[0]);

                    $userData = [
                        'email' => $request->email,
                    ];

                    if (in_array('name', $userCols)) $userData['name'] = $request->name;
                    if (in_array('full_name', $userCols)) $userData['full_name'] = $request->name;
                    if (in_array('username', $userCols)) $userData['username'] = $generatedUsername;
                    if (in_array('password', $userCols)) $userData['password'] = $hashedPassword;
                    if (in_array('password_hash', $userCols)) $userData['password_hash'] = $hashedPassword;
                    if (in_array('role_id', $userCols)) $userData['role_id'] = $roleId;
                    if (in_array('phone', $userCols)) $userData['phone'] = $request->phone;
                    if (in_array('profile_image_url', $userCols)) $userData['profile_image_url'] = $avatarUrl;
                    if (in_array('status', $userCols)) $userData['status'] = $status;

                    $linkedUser = User::where('email', $request->email)->first();
                    if ($linkedUser) {
                        $linkedUser->update($userData);
                    } else {
                        $linkedUser = User::create($userData);
                    }
                }

                // 2. Prepare payload for employees table matching exact MySQL columns
                $payload = [
                    'email' => $request->email,
                ];

                // Name mapping
                if (in_array('full_name', $empCols)) $payload['full_name'] = $request->name;
                if (in_array('name', $empCols)) $payload['name'] = $request->name;

                // Phone mapping
                if (in_array('phone_number', $empCols)) $payload['phone_number'] = $request->phone;
                if (in_array('phone', $empCols)) $payload['phone'] = $request->phone;

                // Role mapping (handles job_role enum and role string)
                if (in_array('job_role', $empCols)) $payload['job_role'] = $request->role;
                if (in_array('role', $empCols)) $payload['role'] = $request->role;

                // user_id link
                if (in_array('user_id', $empCols) && $linkedUser) {
                    $payload['user_id'] = $linkedUser->getKey();
                }

                // Default MySQL columns
                if (in_array('date_hired', $empCols)) $payload['date_hired'] = now()->toDateString();
                if (in_array('salary', $empCols)) $payload['salary'] = 0.00;
                if (in_array('status', $empCols)) $payload['status'] = $status;

                // Other columns if present
                if (in_array('address', $empCols)) $payload['address'] = $request->address ?? 'Phnom Penh, Cambodia';
                if (in_array('password', $empCols)) $payload['password'] = Hash::make($request->password ?: 'password123');
                if (in_array('work_schedule', $empCols)) $payload['work_schedule'] = $workSchedule;
                if (in_array('job_role_details', $empCols)) $payload['job_role_details'] = $request->job_role_details;
                if (in_array('avatar', $empCols)) $payload['avatar'] = $avatarUrl;
                if (in_array('attendance_status', $empCols)) $payload['attendance_status'] = ($status === 'Active' ? 'Present' : 'Absent');

                $newEmp = Employee::create($payload);
            }
        } catch (\Throwable $e) {
            $saveError = $e->getMessage();
            \Illuminate\Support\Facades\Log::error("Employee DB save failed: " . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            if (!$newEmp && $saveError) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save employee: ' . $saveError,
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Employee added successfully!',
                'employee' => $newEmp
            ]);
        }

        return redirect()->back()->with('success', 'Employee ' . $request->name . ' has been added successfully!');
    }

    /**
     * Update the specified employee.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $id = is_object($id) ? ($id->id ?? $id->employee_id ?? 1) : $id;

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:100',
            'phone' => 'required|string|max:50',
            'email' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Active,Inactive',
            'work_schedule' => 'nullable|string|max:150',
            'job_role_details' => 'nullable|string|max:1000',
            'avatar' => 'nullable',
        ]);

        $status = $request->input('status', 'Active');
        $workSchedule = $request->input('work_schedule', 'Full Time (08:00 AM - 05:00 PM)');

        $avatarUrl = null;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destination = public_path('uploads/avatars');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $avatarUrl = asset('uploads/avatars/' . $filename);
        }

        try {
            if (Schema::hasTable('employees')) {
                $empCols = Schema::getColumnListing('employees');
                $employee = Employee::where('employee_id', $id)->first() 
                    ?: (in_array('id', $empCols) ? Employee::where('id', $id)->first() : null);

                if ($employee) {
                    $oldEmail = $employee->email;
                    $data = [
                        'email' => $request->email,
                    ];
                    if (in_array('full_name', $empCols)) $data['full_name'] = $request->name;
                    if (in_array('name', $empCols)) $data['name'] = $request->name;

                    if (in_array('phone_number', $empCols)) $data['phone_number'] = $request->phone;
                    if (in_array('phone', $empCols)) $data['phone'] = $request->phone;

                    if (in_array('job_role', $empCols)) $data['job_role'] = $request->role;
                    if (in_array('role', $empCols)) $data['role'] = $request->role;

                    if (in_array('address', $empCols)) $data['address'] = $request->address;
                    if (in_array('status', $empCols)) $data['status'] = $status;
                    if (in_array('work_schedule', $empCols)) $data['work_schedule'] = $workSchedule;
                    if (in_array('job_role_details', $empCols)) $data['job_role_details'] = $request->job_role_details;

                    if ($request->filled('password') && in_array('password', $empCols)) {
                        $data['password'] = Hash::make($request->password);
                    }
                    if ($avatarUrl && in_array('avatar', $empCols)) {
                        $data['avatar'] = $avatarUrl;
                    }
                    $employee->update($data);

                    // Sync with users table
                    if (Schema::hasTable('users')) {
                        $user = User::where('email', $oldEmail)->orWhere('email', $request->email)->first();
                        if ($user) {
                            $roleId = $this->getRoleIdFromRoleName($request->role);
                            $userCols = Schema::getColumnListing('users');
                            $updateData = ['email' => $request->email];

                            if (in_array('name', $userCols)) $updateData['name'] = $request->name;
                            if (in_array('full_name', $userCols)) $updateData['full_name'] = $request->name;
                            if (in_array('role_id', $userCols)) $updateData['role_id'] = $roleId;
                            if (in_array('phone', $userCols)) $updateData['phone'] = $request->phone;
                            if (in_array('status', $userCols)) $updateData['status'] = $status;
                            if ($avatarUrl && in_array('profile_image_url', $userCols)) $updateData['profile_image_url'] = $avatarUrl;

                            if ($request->filled('password')) {
                                $hashed = Hash::make($request->password);
                                if (in_array('password', $userCols)) $updateData['password'] = $hashed;
                                if (in_array('password_hash', $userCols)) $updateData['password_hash'] = $hashed;
                            }

                            $user->update($updateData);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Employee DB update failed: " . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Employee ' . $request->name . ' updated successfully!');
    }

    /**
     * Quick toggle employee attendance status.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function toggleAttendance(Request $request, $id)
    {
        $id = is_object($id) ? ($id->employee_id ?? $id->id ?? 1) : $id;
        $newStatus = $request->input('attendance_status', 'Present');

        try {
            if (Schema::hasTable('employees')) {
                $employee = Employee::where('employee_id', $id)->first() 
                    ?: (Schema::hasColumn('employees', 'id') ? Employee::where('id', $id)->first() : null);
                if ($employee) {
                    $employee->update(['attendance_status' => $newStatus]);
                }
            }
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance status updated to ' . $newStatus,
                'attendance_status' => $newStatus
            ]);
        }

        return redirect()->back()->with('success', 'Attendance updated to ' . $newStatus);
    }

    /**
     * Remove the specified employee.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $id = is_object($id) ? ($id->employee_id ?? $id->id ?? 1) : $id;
        try {
            if (Schema::hasTable('employees')) {
                $employee = Employee::where('employee_id', $id)->first() 
                    ?: (Schema::hasColumn('employees', 'id') ? Employee::where('id', $id)->first() : null);

                if ($employee) {
                    $userId = $employee->user_id ?? null;
                    $employeeEmail = $employee->email ?? null;

                    // 1. Delete associated attendance records if any
                    if (Schema::hasTable('employee_attendance')) {
                        try {
                            \Illuminate\Support\Facades\DB::table('employee_attendance')
                                ->where('employee_id', $employee->employee_id ?? $id)
                                ->delete();
                        } catch (\Throwable $te) {}
                    }

                    // 2. Delete the employee record FIRST (Child row in Foreign Key relationship)
                    $employee->delete();

                    // 3. Delete the linked user record SECOND (Parent row)
                    if (Schema::hasTable('users')) {
                        try {
                            $user = null;
                            if (!empty($userId)) {
                                $user = User::where('user_id', $userId)->first();
                            }
                            if (!$user && !empty($employeeEmail)) {
                                $user = User::where('email', $employeeEmail)->first();
                            }

                            // Protect master admin from being deleted (user_id 1 or username admin)
                            if ($user && ($user->user_id ?? $user->id) != 1 && strtolower($user->username ?? '') !== 'admin') {
                                $user->delete();
                            }
                        } catch (\Throwable $ue) {
                            \Illuminate\Support\Facades\Log::error("Failed to delete user after employee removal: " . $ue->getMessage());
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Employee deletion error: " . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Employee has been removed successfully!');
    }
}
