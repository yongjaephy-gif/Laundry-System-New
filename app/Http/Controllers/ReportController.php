<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Ensure table exists and has necessary columns in MySQL.
     */
    protected function ensureReportsTable(): void
    {
        try {
            if (!Schema::hasTable('reports')) {
                Schema::create('reports', function ($t) {
                    $t->id();
                    $t->string('name');
                    $t->string('category', 100)->default('daily_sale');
                    $t->string('type', 100)->default('Sales');
                    $t->string('date_range', 100)->nullable();
                    $t->string('date', 50)->nullable();
                    $t->string('generated_by', 100)->default('Admin');
                    $t->string('generated_on', 100)->nullable();
                    $t->text('description')->nullable();
                    $t->longText('details')->nullable();
                    $t->string('status', 50)->default('Completed');
                    $t->timestamps();
                });
            } else {
                $cols = Schema::getColumnListing('reports');
                Schema::table('reports', function ($t) use ($cols) {
                    if (!in_array('name', $cols)) {
                        $t->string('name')->default('General Report');
                    }
                    if (!in_array('type', $cols)) {
                        $t->string('type', 100)->default('Sales');
                    }
                    if (!in_array('date_range', $cols) && !in_array('date', $cols)) {
                        $t->string('date_range', 100)->nullable();
                    }
                    if (!in_array('generated_by', $cols)) {
                        $t->string('generated_by', 100)->default('Admin');
                    }
                    if (!in_array('generated_on', $cols)) {
                        $t->string('generated_on', 100)->nullable();
                    }
                    if (!in_array('status', $cols)) {
                        $t->string('status', 50)->default('Completed');
                    }
                });
            }
        } catch (\Throwable $e) {
            Log::error('Reports table ensure error: ' . $e->getMessage());
        }
    }

    /**
     * Cleanup demo reports from database so users can start fresh.
     */
    protected function cleanupDemoReports(): void
    {
        try {
            if (Schema::hasTable('reports')) {
                $cols = Schema::getColumnListing('reports');
                $dateCol = in_array('date_range', $cols) ? 'date_range' : (in_array('date', $cols) ? 'date' : null);
                if ($dateCol) {
                    Report::where($dateCol, 'like', '%2024%')->delete();
                }
            }
        } catch (\Throwable $e) {
            Log::error('Cleanup demo reports error: ' . $e->getMessage());
        }
    }

    /**
     * Safely find report by id or report_id.
     */
    protected function findReport($id)
    {
        if (is_object($id)) {
            $id = $id->id ?? ($id->report_id ?? 1);
        }

        try {
            $cols = Schema::getColumnListing('reports');
            $query = Report::query();

            if (in_array('id', $cols) && in_array('report_id', $cols)) {
                $query->where('id', $id)->orWhere('report_id', $id);
            } elseif (in_array('id', $cols)) {
                $query->where('id', $id);
            } elseif (in_array('report_id', $cols)) {
                $query->where('report_id', $id);
            }

            return $query->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Map request input to matching table columns.
     */
    protected function buildReportPayload(Request $request): array
    {
        $cols = Schema::getColumnListing('reports');
        $payload = [];

        $type = trim($request->input('type') ?? $request->input('report_type') ?? 'Sales');
        $name = trim($request->input('name') ?? (ucwords(str_replace('_', ' ', $type)) . ' Report'));
        $dateRange = trim($request->input('date_range') ?? (date('d M Y')));
        $userName = Auth::check() ? (Auth::user()->full_name ?? Auth::user()->name ?? 'Admin') : 'Admin';
        $nowFormatted = date('d M Y h:i A');

        if (in_array('name', $cols)) $payload['name'] = $name;
        if (in_array('type', $cols)) $payload['type'] = $type;
        if (in_array('category', $cols)) $payload['category'] = strtolower(str_replace(' ', '_', $type));

        if (in_array('date_range', $cols)) $payload['date_range'] = $dateRange;
        if (in_array('date', $cols)) $payload['date'] = $dateRange;

        if (in_array('generated_by', $cols)) $payload['generated_by'] = $userName;
        if (in_array('generated_on', $cols)) $payload['generated_on'] = $nowFormatted;

        if (in_array('status', $cols)) $payload['status'] = 'Completed';
        if (in_array('description', $cols)) $payload['description'] = "Generated {$type} report for {$dateRange}";

        return $payload;
    }

    /**
     * Display Report Management view from MySQL Database.
     */
    public function index(Request $request)
    {
        $this->ensureReportsTable();
        $this->cleanupDemoReports();

        $cols = Schema::getColumnListing('reports');
        $query = Report::query();

        // Safe order by primary key or created_at
        $pk = in_array('id', $cols) ? 'id' : (in_array('report_id', $cols) ? 'report_id' : null);
        if ($pk) {
            $query->orderBy($pk, 'desc');
        }

        $totalCount = (clone $query)->count();
        $perPage = 5; // 5 per page
        $page = max(1, (int)$request->input('page', 1));
        $totalPages = max(1, (int)ceil($totalCount / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;
        $paginatedItems = $query->skip($offset)->take($perPage)->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'items' => $paginatedItems,
                'total' => $totalCount,
                'page' => $page,
                'totalPages' => $totalPages,
            ]);
        }

        return view('pages.report', [
            'reports' => $paginatedItems,
            'totalCount' => $totalCount,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Generate / Store a new report directly in MySQL.
     */
    public function store(Request $request)
    {
        $this->ensureReportsTable();

        $type = trim($request->input('type') ?? $request->input('report_type') ?? 'Sales');
        $name = trim($request->input('name') ?? (ucwords(str_replace('_', ' ', $type)) . ' Report'));
        $dateRange = trim($request->input('date_range') ?? (date('d M Y')));
        $format = strtolower(trim($request->input('format', 'print')));

        $request->merge([
            'name' => $name,
            'type' => $type,
            'date_range' => $dateRange
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $payload = $this->buildReportPayload($request);
        $report = Report::create($payload);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Report '{$name}' generated successfully!",
                'report' => $report,
                'format' => $format,
            ]);
        }

        return redirect()->route('reports.index')->with('success', "Report '{$name}' generated successfully!");
    }

    /**
     * Export reports directly from MySQL (Excel/CSV or PDF or Print).
     */
    public function export(Request $request, $format = 'excel')
    {
        $this->ensureReportsTable();
        $reportName = $request->input('name', 'Laundry_Business_Report');

        $cols = Schema::getColumnListing('reports');
        $query = Report::query();
        $pk = in_array('id', $cols) ? 'id' : (in_array('report_id', $cols) ? 'report_id' : null);
        if ($pk) {
            $query->orderBy($pk, 'desc');
        }
        $reports = $query->get();

        if (strtolower($format) === 'excel' || strtolower($format) === 'csv') {
            $filename = str_replace(' ', '_', $reportName) . '_' . date('Ymd_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($reports, $reportName) {
                $file = fopen('php://output', 'w');
                // UTF-8 BOM for Excel compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                fputcsv($file, ['LAUNDRY 6 PLUS - ' . strtoupper($reportName)]);
                fputcsv($file, ['Generated On:', date('d M Y h:i A')]);
                fputcsv($file, ['Generated By:', Auth::check() ? Auth::user()->name : 'Admin']);
                fputcsv($file, []);

                // Header
                fputcsv($file, ['#', 'Report Name', 'Type', 'Date Range', 'Generated By', 'Generated On', 'Status']);

                foreach ($reports as $index => $r) {
                    fputcsv($file, [
                        $index + 1,
                        $r->name,
                        $r->type,
                        $r->date_range,
                        $r->generated_by,
                        $r->generated_on,
                        $r->status ?? 'Completed',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // PDF / Print View
        return view('pages.report_print', [
            'reports' => $reports,
            'reportName' => $reportName,
            'autoPrint' => (strtolower($format) === 'print'),
        ]);
    }

    /**
     * Show dedicated print view directly from MySQL.
     */
    public function printView(Request $request)
    {
        $this->ensureReportsTable();
        $reportName = $request->input('name', 'General Report');
        $id = $request->input('id');

        if ($id) {
            $report = $this->findReport($id);
            $selectedReports = $report ? [$report] : [];
            if ($report) {
                $reportName = $report->name;
            }
        } else {
            $cols = Schema::getColumnListing('reports');
            $query = Report::query();
            $pk = in_array('id', $cols) ? 'id' : (in_array('report_id', $cols) ? 'report_id' : null);
            if ($pk) {
                $query->orderBy($pk, 'desc');
            }
            $selectedReports = $query->get();
        }

        return view('pages.report_print', [
            'reports' => $selectedReports,
            'reportName' => $reportName,
            'autoPrint' => true,
        ]);
    }

    /**
     * Show single report details.
     */
    public function show($id, $optionalId = null)
    {
        if ($id instanceof Request) {
            $id = $optionalId ?? $id->route('id') ?? $id->route('report');
        }

        $report = $this->findReport($id);

        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
        }

        return response()->json(['success' => true, 'report' => $report]);
    }

    /**
     * Update an existing report in MySQL database.
     */
    public function update(Request $request, $id)
    {
        $report = $this->findReport($id);

        if (!$report) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
            }
            return redirect()->route('reports.index')->with('error', 'Report not found.');
        }

        $cols = Schema::getColumnListing('reports');
        $updates = [];

        if ($request->filled('name') && in_array('name', $cols)) {
            $updates['name'] = trim($request->input('name'));
        }
        if ($request->filled('type') && in_array('type', $cols)) {
            $updates['type'] = trim($request->input('type'));
        }
        if ($request->filled('date_range') && in_array('date_range', $cols)) {
            $updates['date_range'] = trim($request->input('date_range'));
        }
        if ($request->filled('date') && in_array('date', $cols)) {
            $updates['date'] = trim($request->input('date'));
        }
        if ($request->filled('status') && in_array('status', $cols)) {
            $updates['status'] = trim($request->input('status'));
        }

        $report->update($updates);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Report updated successfully in database!",
                'report' => $report,
            ]);
        }

        return redirect()->route('reports.index')->with('success', "Report updated successfully!");
    }

    /**
     * Delete report from MySQL database.
     */
    public function destroy($id)
    {
        $report = $this->findReport($id);

        $deletedName = '';
        if ($report) {
            $deletedName = $report->name ?? "ID #{$id}";
            $report->delete();
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Report '{$deletedName}' deleted successfully from database!"]);
        }

        return redirect()->route('reports.index')->with('success', "Report '{$deletedName}' deleted successfully!");
    }

    /**
     * Clear all reports in MySQL database.
     */
    public function reset()
    {
        try {
            if (Schema::hasTable('reports')) {
                Report::truncate();
            }
        } catch (\Throwable $e) {
            Log::error('Reset reports error: ' . $e->getMessage());
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Reports table has been cleared in database.']);
        }

        return redirect()->route('reports.index')->with('success', 'Reports table has been cleared in database.');
    }
}
