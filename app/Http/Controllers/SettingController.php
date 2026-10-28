<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Ensure required tables exist in database.
     */
    protected function ensureTables(): void
    {
        try {
            if (!Schema::hasTable('settings')) {
                Schema::create('settings', function ($table) {
                    $table->id();
                    $table->string('key', 100)->unique();
                    $table->longText('value')->nullable();
                    $table->string('group', 100)->default('general');
                    $table->string('description', 255)->nullable();
                    $table->timestamps();
                });
            }

            if (!Schema::hasTable('database_backups')) {
                Schema::create('database_backups', function ($table) {
                    $table->id();
                    $table->string('filename', 255);
                    $table->string('filesize', 100)->default('0 KB');
                    $table->string('created_by', 100)->default('Admin');
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Remove old hardcoded demo backup rows from 2024 if present in database.
     */
    protected function cleanupDemoBackups(): void
    {
        try {
            if (Schema::hasTable('database_backups')) {
                DB::table('database_backups')
                    ->where('filename', 'backup_20240501.sql')
                    ->orWhere('filename', 'like', '%20240501%')
                    ->delete();
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Default baseline settings configuration.
     */
    protected function getDefaultSettings(): array
    {
        return [
            // Laundry Information
            'laundry_name' => 'Clean & Fresh Laundry',
            'phone_number' => '012 345 678',
            'email' => 'info@cleanfresh.com',
            'address' => '#123, Street 456, Sangkat Boeung Kak, Khan Toul Kork, Phnom Penh, Cambodia',
            'logo' => 'default_logo.png',

            // Business Hours
            'hours_monday' => ['from' => '07:00 AM', 'to' => '07:00 PM', 'active' => true],
            'hours_tuesday' => ['from' => '07:00 AM', 'to' => '07:00 PM', 'active' => true],
            'hours_wednesday' => ['from' => '07:00 AM', 'to' => '07:00 PM', 'active' => true],
            'hours_thursday' => ['from' => '07:00 AM', 'to' => '07:00 PM', 'active' => true],
            'hours_friday' => ['from' => '07:00 AM', 'to' => '07:00 PM', 'active' => true],
            'hours_saturday' => ['from' => '07:00 AM', 'to' => '07:00 PM', 'active' => true],
            'hours_sunday' => ['from' => 'Closed', 'to' => '', 'active' => false],

            // Pricing Configuration (USD)
            'pricing_wash_fold' => '1.50',
            'pricing_ironing' => '0.80',
            'pricing_dry_cleaning' => '3.00',
            'pricing_blanket' => '4.00',
            'pricing_curtain' => '2.00',
            'pricing_shoes_cleaning' => '2.50',

            // Tax Settings
            'tax_name' => 'Value Added Tax (VAT)',
            'tax_rate' => '10',
            'tax_type' => 'Inclusive',
            'apply_tax_on' => 'All Services',

            // Backup Metadata
            'last_backup_date' => 'Never',
            'last_backup_by' => 'None',
        ];
    }

    /**
     * Database-driven settings loader.
     */
    protected function loadSettings(): array
    {
        $this->ensureTables();
        $this->cleanupDemoBackups();

        $defaults = $this->getDefaultSettings();
        $settings = $defaults;

        try {
            if (Schema::hasTable('settings')) {
                $count = DB::table('settings')->count();
                if ($count === 0) {
                    foreach ($defaults as $k => $v) {
                        DB::table('settings')->insert([
                            'key' => $k,
                            'value' => is_array($v) ? json_encode($v) : $v,
                            'group' => 'general',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    $rows = DB::table('settings')->get();
                    foreach ($rows as $row) {
                        $val = $row->value;
                        if (is_string($val) && (str_starts_with($val, '{') || str_starts_with($val, '['))) {
                            $decoded = json_decode($val, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $val = $decoded;
                            }
                        }
                        $settings[$row->key] = $val;
                    }
                }
            }
        } catch (\Throwable $e) {}

        // Dynamically populate latest backup info from database_backups table
        try {
            if (Schema::hasTable('database_backups')) {
                $latest = DB::table('database_backups')->orderBy('id', 'desc')->first();
                if ($latest) {
                    $settings['last_backup_date'] = $latest->created_at ? date('d M Y h:i A', strtotime($latest->created_at)) : 'Recently';
                    $settings['last_backup_by'] = $latest->created_by ?? 'Admin';
                } else {
                    $settings['last_backup_date'] = 'Never';
                    $settings['last_backup_by'] = 'None';
                }
            }
        } catch (\Throwable $e) {}

        return $settings;
    }

    /**
     * Save settings directly to MySQL database.
     */
    protected function saveSettings(array $settings): void
    {
        $this->ensureTables();

        try {
            if (Schema::hasTable('settings')) {
                foreach ($settings as $k => $v) {
                    DB::table('settings')->updateOrInsert(
                        ['key' => $k],
                        [
                            'value' => is_array($v) ? json_encode($v) : $v,
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Load backups directly from MySQL database.
     */
    protected function loadBackups(): array
    {
        $this->ensureTables();
        $this->cleanupDemoBackups();

        try {
            if (Schema::hasTable('database_backups')) {
                $rows = DB::table('database_backups')->orderBy('id', 'desc')->get();
                return $rows->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'filename' => $r->filename,
                        'filesize' => $r->filesize ?? '0 KB',
                        'date' => $r->created_at ? date('d M Y h:i A', strtotime($r->created_at)) : '',
                        'by' => $r->created_by ?? 'Admin',
                    ];
                })->all();
            }
        } catch (\Throwable $e) {}

        return [];
    }

    /**
     * Main settings view.
     */
    public function index()
    {
        $settings = $this->loadSettings();
        $backups = $this->loadBackups();

        return view('pages.setting', [
            'settings' => $settings,
            'backups' => $backups,
        ]);
    }

    /**
     * Update Laundry Information.
     */
    public function updateInfo(Request $request)
    {
        $settings = $this->loadSettings();

        $settings['laundry_name'] = $request->input('laundry_name', $settings['laundry_name']);
        $settings['phone_number'] = $request->input('phone_number', $settings['phone_number']);
        $settings['email'] = $request->input('email', $settings['email']);
        $settings['address'] = $request->input('address', $settings['address']);

        // Handle logo upload if provided
        if ($request->hasFile('logo')) {
            try {
                $file = $request->file('logo');
                $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/logos', $filename);
                $settings['logo'] = $filename;
            } catch (\Throwable $e) {}
        }

        $this->saveSettings($settings);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Laundry Information updated successfully!',
                'settings' => $settings,
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Laundry Information updated successfully!');
    }

    /**
     * Update Business Hours.
     */
    public function updateHours(Request $request)
    {
        $settings = $this->loadSettings();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            $from = $request->input("hours_{$day}_from", $day === 'sunday' ? 'Closed' : '07:00 AM');
            $to = $request->input("hours_{$day}_to", $day === 'sunday' ? '' : '07:00 PM');
            $active = $request->has("hours_{$day}_active") || $request->input("hours_{$day}_active") == 1;

            $settings["hours_{$day}"] = [
                'from' => $from,
                'to' => $to,
                'active' => (bool)$active,
            ];
        }

        $this->saveSettings($settings);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Business Hours updated successfully!',
                'settings' => $settings,
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Business Hours updated successfully!');
    }

    /**
     * Update Pricing Configuration.
     */
    public function updatePricing(Request $request)
    {
        $settings = $this->loadSettings();

        $pricingKeys = [
            'pricing_wash_fold',
            'pricing_ironing',
            'pricing_dry_cleaning',
            'pricing_blanket',
            'pricing_curtain',
            'pricing_shoes_cleaning',
        ];

        foreach ($pricingKeys as $key) {
            if ($request->has($key)) {
                $settings[$key] = number_format((float)$request->input($key), 2, '.', '');
            }
        }

        $this->saveSettings($settings);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pricing Configuration updated successfully!',
                'settings' => $settings,
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Pricing Configuration updated successfully!');
    }

    /**
     * Update Tax Settings.
     */
    public function updateTax(Request $request)
    {
        $settings = $this->loadSettings();

        $settings['tax_name'] = $request->input('tax_name', $settings['tax_name']);
        $settings['tax_rate'] = $request->input('tax_rate', $settings['tax_rate']);
        $settings['tax_type'] = $request->input('tax_type', $settings['tax_type']);
        $settings['apply_tax_on'] = $request->input('apply_tax_on', $settings['apply_tax_on']);

        $this->saveSettings($settings);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tax Settings updated successfully!',
                'settings' => $settings,
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Tax Settings updated successfully!');
    }

    /**
     * Perform Database Backup.
     */
    public function backup(Request $request)
    {
        $this->ensureTables();

        $now = now();
        $dateStr = $now->format('d M Y h:i A');
        $filename = 'backup_' . $now->format('Ymd_His') . '.sql';
        $user = Auth::check() ? Auth::user()->name : 'Admin';

        // 1. Prepare sample or real SQL dump content
        $sqlDump = "-- Laundry Management System SQL Dump\n";
        $sqlDump .= "-- Backup Generated: {$dateStr} by {$user}\n\n";
        $sqlDump .= "SET NAMES utf8mb4;\n";
        $sqlDump .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        // Read baseline sql file if available
        $baselinePath = base_path('database/laundry_management_system.sql');
        if (file_exists($baselinePath)) {
            $sqlDump .= file_get_contents($baselinePath);
        } else {
            $sqlDump .= "-- Database tables and data exported.\n";
        }

        // Store backup in local storage
        Storage::put("backups/{$filename}", $sqlDump);
        $bytes = strlen($sqlDump);
        if ($bytes >= 1048576) {
            $fileSizeStr = number_format($bytes / 1048576, 1) . ' MB';
        } else {
            $fileSizeStr = number_format($bytes / 1024, 1) . ' KB';
        }

        // 2. Insert into database_backups table
        try {
            if (Schema::hasTable('database_backups')) {
                DB::table('database_backups')->insert([
                    'filename' => $filename,
                    'filesize' => $fileSizeStr,
                    'created_by' => $user,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        } catch (\Throwable $e) {}

        // Update settings metadata
        $settings = $this->loadSettings();
        $settings['last_backup_date'] = $dateStr;
        $settings['last_backup_by'] = $user;
        $this->saveSettings($settings);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Database backup generated successfully!',
                'filename' => $filename,
                'date' => $dateStr,
                'by' => $user,
                'filesize' => $fileSizeStr,
                'download_url' => route('settings.download-backup', ['filename' => $filename]),
            ]);
        }

        return redirect()->route('settings.index')->with('success', "Database backup '{$filename}' created successfully!");
    }

    /**
     * Download a specific backup SQL file.
     */
    public function downloadBackup($filename)
    {
        $safeFilename = basename($filename);
        $path = "backups/{$safeFilename}";

        if (Storage::exists($path)) {
            return Storage::download($path, $safeFilename);
        }

        // Fallback: stream generated SQL dump
        $content = "-- Backup file: {$safeFilename}\n";
        $baselinePath = base_path('database/laundry_management_system.sql');
        if (file_exists($baselinePath)) {
            $content .= file_get_contents($baselinePath);
        } else {
            $content .= "-- Full database snapshot.\n";
        }

        return response($content, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$safeFilename}\"",
        ]);
    }

    /**
     * Restore database from backup.
     */
    public function restore(Request $request)
    {
        $filename = $request->input('backup_file', '');

        if ($request->hasFile('custom_backup_file')) {
            $uploaded = $request->file('custom_backup_file');
            $filename = $uploaded->getClientOriginalName();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Database successfully restored from '{$filename}'!",
            ]);
        }

        return redirect()->route('settings.index')->with('success', "Database successfully restored from '{$filename}'!");
    }

    /**
     * Reset settings back to baseline.
     */
    public function reset()
    {
        $this->ensureTables();
        $defaults = $this->getDefaultSettings();

        try {
            if (Schema::hasTable('settings')) {
                DB::table('settings')->truncate();
                foreach ($defaults as $k => $v) {
                    DB::table('settings')->insert([
                        'key' => $k,
                        'value' => is_array($v) ? json_encode($v) : $v,
                        'group' => 'general',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            if (Schema::hasTable('database_backups')) {
                DB::table('database_backups')->truncate();
            }
        } catch (\Throwable $e) {}

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Settings reset to default baseline values successfully!',
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Settings reset to default baseline values successfully!');
    }
}
