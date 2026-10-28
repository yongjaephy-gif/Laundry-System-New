<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Ensure table exists and has necessary columns in MySQL.
     */
    protected function ensureNotificationsTable(): void
    {
        try {
            if (!Schema::hasTable('notifications')) {
                Schema::create('notifications', function ($t) {
                    $t->id();
                    $t->string('type')->default('Order Ready')->nullable();
                    $t->string('title')->default('Order Ready')->nullable();
                    $t->text('message')->nullable();
                    $t->string('target')->default('All Customers')->nullable();
                    $t->string('scheduled_date')->nullable();
                    $t->string('status')->default('Sent')->nullable();
                    $t->timestamps();
                });
            } else {
                $cols = Schema::getColumnListing('notifications');
                Schema::table('notifications', function ($t) use ($cols) {
                    if (!in_array('type', $cols) && !in_array('notification_type', $cols)) {
                        $t->string('type')->default('Order Ready')->nullable();
                    }
                    if (!in_array('title', $cols)) {
                        $t->string('title')->default('Order Ready')->nullable();
                    }
                    if (!in_array('message', $cols)) {
                        $t->text('message')->nullable();
                    }
                    if (!in_array('target', $cols)) {
                        $t->string('target')->default('All Customers')->nullable();
                    }
                    if (!in_array('scheduled_date', $cols)) {
                        $t->string('scheduled_date')->nullable();
                    }
                    if (!in_array('status', $cols)) {
                        $t->string('status')->default('Sent')->nullable();
                    }
                });
            }
        } catch (\Throwable $e) {
            Log::error('Notifications schema check error: ' . $e->getMessage());
        }
    }

    /**
     * Cleanup demo notifications from database so users can manage their real notifications.
     */
    protected function cleanupDemoNotifications(): void
    {
        try {
            if (Schema::hasTable('notifications')) {
                $cols = Schema::getColumnListing('notifications');
                $msgCol = in_array('message', $cols) ? 'message' : null;
                if ($msgCol) {
                    Notification::where('message', 'like', '%#ORD-%')
                        ->orWhere('message', 'like', '%Get 20% OFF%')
                        ->orWhere('message', 'like', '%Happy New Month%')
                        ->delete();
                }
            }
        } catch (\Throwable $e) {
            Log::error('Cleanup demo notifications error: ' . $e->getMessage());
        }
    }

    /**
     * Safely find notification by id or notification_id.
     */
    protected function findNotification($id)
    {
        if (is_object($id)) {
            $id = $id->id ?? ($id->notification_id ?? 1);
        }

        try {
            $cols = Schema::getColumnListing('notifications');
            $query = Notification::query();

            if (in_array('id', $cols) && in_array('notification_id', $cols)) {
                $query->where('id', $id)->orWhere('notification_id', $id);
            } elseif (in_array('id', $cols)) {
                $query->where('id', $id);
            } elseif (in_array('notification_id', $cols)) {
                $query->where('notification_id', $id);
            }

            return $query->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Map request input to matching table columns.
     */
    protected function buildNotificationPayload(Request $request): array
    {
        $cols = Schema::getColumnListing('notifications');
        $payload = [];

        $type = trim($request->input('type', 'Order Ready'));
        if ($type === 'Promotion Notification') {
            $type = 'Promotion';
        }

        $title = trim($request->input('title', $type));
        $message = trim($request->input('message', ''));
        $target = trim($request->input('target') ?? $request->input('recipient_name') ?? 'All Customers');

        $datePart = $request->input('scheduled_date', date('Y-m-d'));
        $timePart = $request->input('scheduled_time', date('h:i A'));
        $scheduledDate = trim("{$datePart} {$timePart}");

        $statusInput = $request->input('status', 'Send Now');
        $status = in_array($statusInput, ['Sent', 'Pending', 'Scheduled', 'Failed']) 
            ? $statusInput 
            : (($statusInput === 'Schedule Later') ? 'Scheduled' : 'Sent');

        if (in_array('type', $cols)) $payload['type'] = $type;
        if (in_array('notification_type', $cols)) $payload['notification_type'] = $type;

        if (in_array('title', $cols)) $payload['title'] = $title;
        if (in_array('name', $cols)) $payload['name'] = $title;

        if (in_array('message', $cols)) $payload['message'] = $message;
        if (in_array('description', $cols)) $payload['description'] = $message;

        if (in_array('target', $cols)) $payload['target'] = $target;
        if (in_array('recipient', $cols)) $payload['recipient'] = $target;

        if (in_array('scheduled_date', $cols)) $payload['scheduled_date'] = $scheduledDate;
        if (in_array('status', $cols)) $payload['status'] = $status;

        return $payload;
    }

    /**
     * Display the Notification Management Page from MySQL Database.
     */
    public function index(Request $request)
    {
        $this->ensureNotificationsTable();
        $this->cleanupDemoNotifications();

        $search = trim($request->input('search', ''));
        $typeFilter = trim($request->input('type', ''));
        $statusFilter = trim($request->input('status', ''));
        $page = max(1, (int)$request->input('page', 1));
        $perPage = 10;

        $cols = Schema::getColumnListing('notifications');
        $query = Notification::query();

        // 1. Search Filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search, $cols) {
                $hasWhere = false;
                if (in_array('title', $cols)) {
                    $q->where('title', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('type', $cols)) {
                    $hasWhere ? $q->orWhere('type', 'like', "%{$search}%") : $q->where('type', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('notification_type', $cols)) {
                    $hasWhere ? $q->orWhere('notification_type', 'like', "%{$search}%") : $q->where('notification_type', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('target', $cols)) {
                    $hasWhere ? $q->orWhere('target', 'like', "%{$search}%") : $q->where('target', 'like', "%{$search}%");
                    $hasWhere = true;
                }
                if (in_array('message', $cols)) {
                    $hasWhere ? $q->orWhere('message', 'like', "%{$search}%") : $q->where('message', 'like', "%{$search}%");
                }
            });
        }

        // 2. Type Filter
        if (!empty($typeFilter) && $typeFilter !== 'All Types') {
            $typeCol = in_array('type', $cols) ? 'type' : (in_array('notification_type', $cols) ? 'notification_type' : null);
            if ($typeCol) {
                if ($typeFilter === 'Promotion Notification') {
                    $query->whereIn($typeCol, ['Promotion', 'Promotion Notification']);
                } else {
                    $query->where($typeCol, $typeFilter);
                }
            }
        }

        // 3. Status Filter
        if (!empty($statusFilter) && $statusFilter !== 'All Status') {
            if (in_array('status', $cols)) {
                $query->where('status', $statusFilter);
            }
        }

        // Safe order by primary key or created_at
        $pk = in_array('id', $cols) ? 'id' : (in_array('notification_id', $cols) ? 'notification_id' : null);
        if ($pk) {
            $query->orderBy($pk, 'desc');
        }

        $allCount = Notification::count();
        $totalFiltered = (clone $query)->count();
        $totalPages = max(1, (int)ceil($totalFiltered / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;
        $paginatedItems = $query->skip($offset)->take($perPage)->get();

        // Safe Database Stats without hardcoded column assumptions
        $typeCol = in_array('type', $cols) ? 'type' : (in_array('notification_type', $cols) ? 'notification_type' : null);

        $stats = [
            'total' => $allCount,
            'order_ready' => $typeCol ? Notification::where($typeCol, 'Order Ready')->count() : 0,
            'pickup_reminder' => $typeCol ? Notification::where($typeCol, 'Pickup Reminder')->count() : 0,
            'delivery_reminder' => $typeCol ? Notification::where($typeCol, 'Delivery Reminder')->count() : 0,
            'payment_reminder' => $typeCol ? Notification::where($typeCol, 'Payment Reminder')->count() : 0,
            'promotion' => $typeCol ? Notification::whereIn($typeCol, ['Promotion', 'Promotion Notification'])->count() : 0,
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'items' => $paginatedItems,
                'stats' => $stats,
                'total' => $totalFiltered,
                'page' => $page,
                'totalPages' => $totalPages,
            ]);
        }

        return view('pages.notification', [
            'notifications' => $paginatedItems,
            'allCount' => $allCount,
            'filteredCount' => $totalFiltered,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'stats' => $stats,
            'search' => $search,
            'typeFilter' => $typeFilter ?: 'All Types',
            'statusFilter' => $statusFilter ?: 'All Status',
        ]);
    }

    /**
     * Show notification details.
     */
    public function show($id, $optionalId = null)
    {
        if ($id instanceof Request) {
            $id = $optionalId ?? $id->route('id') ?? $id->route('notification');
        }

        $notif = $this->findNotification($id);

        if (!$notif) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        return response()->json(['success' => true, 'notification' => $notif]);
    }

    /**
     * Store newly created notification directly in MySQL.
     */
    public function store(Request $request)
    {
        $this->ensureNotificationsTable();

        $target = $request->input('target') ?? $request->input('recipient_name') ?? 'All Customers';
        $request->merge(['target' => $target]);

        $request->validate([
            'type' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target' => 'required|string|max:255',
        ]);

        $payload = $this->buildNotificationPayload($request);
        $notification = Notification::create($payload);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Notification '{$notification->title}' created successfully!",
                'notification' => $notification,
            ]);
        }

        return redirect()->route('notifications.index')->with('success', "Notification '{$notification->title}' created successfully!");
    }

    /**
     * Update an existing notification in MySQL database.
     */
    public function update(Request $request, $id)
    {
        $notification = $this->findNotification($id);

        if (!$notification) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
            }
            return redirect()->route('notifications.index')->with('error', 'Notification not found.');
        }

        $cols = Schema::getColumnListing('notifications');
        $updates = [];

        if ($request->filled('title')) {
            if (in_array('title', $cols)) $updates['title'] = trim($request->input('title'));
            if (in_array('name', $cols)) $updates['name'] = trim($request->input('title'));
        }
        if ($request->filled('type')) {
            $type = trim($request->input('type'));
            $val = ($type === 'Promotion Notification') ? 'Promotion' : $type;
            if (in_array('type', $cols)) $updates['type'] = $val;
            if (in_array('notification_type', $cols)) $updates['notification_type'] = $val;
        }
        if ($request->filled('message')) {
            if (in_array('message', $cols)) $updates['message'] = trim($request->input('message'));
            if (in_array('description', $cols)) $updates['description'] = trim($request->input('message'));
        }
        if ($request->filled('target')) {
            if (in_array('target', $cols)) $updates['target'] = trim($request->input('target'));
            if (in_array('recipient', $cols)) $updates['recipient'] = trim($request->input('target'));
        }
        if ($request->filled('scheduled_date') && in_array('scheduled_date', $cols)) {
            $updates['scheduled_date'] = trim($request->input('scheduled_date'));
        }
        if ($request->filled('status') && in_array('status', $cols)) {
            $status = $request->input('status');
            if (in_array($status, ['Sent', 'Pending', 'Scheduled', 'Failed'])) {
                $updates['status'] = $status;
            }
        }

        $notification->update($updates);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Notification updated successfully in database!",
                'notification' => $notification,
            ]);
        }

        return redirect()->route('notifications.index')->with('success', "Notification updated successfully!");
    }

    /**
     * Delete a notification from MySQL database.
     */
    public function destroy(Request $request, $id)
    {
        $notification = $this->findNotification($id);

        $deletedTitle = '';
        if ($notification) {
            $deletedTitle = $notification->title ?? "ID #{$id}";
            $notification->delete();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Notification '{$deletedTitle}' deleted successfully from database!",
            ]);
        }

        return redirect()->route('notifications.index')->with('success', "Notification '{$deletedTitle}' deleted successfully!");
    }

    /**
     * Clear all notifications in MySQL database.
     */
    public function reset(Request $request)
    {
        try {
            if (Schema::hasTable('notifications')) {
                Notification::truncate();
            }
        } catch (\Throwable $e) {
            Log::error('Reset notifications error: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifications table has been cleared in database.',
            ]);
        }

        return redirect()->route('notifications.index')->with('success', 'Notifications table has been cleared in database.');
    }
}
