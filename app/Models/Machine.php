<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Machine extends Model
{
    use HasFactory;

    protected $table = 'machines';

    protected $fillable = [
        'id',
        'machine_id',
        'name',
        'machine_name',
        'type',
        'machine_type',
        'capacity',
        'model',
        'location',
        'status',
        'last_maintenance',
        'next_maintenance',
        'maintenance_history',
        'notes',
    ];

    protected $casts = [
        'maintenance_history' => 'array',
    ];

    /**
     * Dynamically resolve primary key.
     */
    public function getKeyName()
    {
        try {
            if (Schema::hasColumn('machines', 'id')) {
                return 'id';
            }
            if (Schema::hasColumn('machines', 'machine_id')) {
                return 'machine_id';
            }
        } catch (\Throwable $e) {}
        return 'id';
    }

    /**
     * Non-incrementing string key if primary key is machine_id.
     */
    public function getIncrementing()
    {
        try {
            if (Schema::hasColumn('machines', 'id')) {
                return true;
            }
            if (Schema::hasColumn('machines', 'machine_id')) {
                $cols = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM machines WHERE Field = 'machine_id'");
                if (!empty($cols)) {
                    return str_contains(strtolower($cols[0]->Extra ?? ''), 'auto_increment')
                        || str_contains(strtolower($cols[0]->Type ?? ''), 'int');
                }
            }
        } catch (\Throwable $e) {}
        return true;
    }

    /**
     * Key type.
     */
    public function getKeyType()
    {
        try {
            if (Schema::hasColumn('machines', 'id')) {
                return 'int';
            }
            if (Schema::hasColumn('machines', 'machine_id')) {
                $cols = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM machines WHERE Field = 'machine_id'");
                if (!empty($cols)) {
                    return str_contains(strtolower($cols[0]->Type ?? ''), 'int') ? 'int' : 'string';
                }
            }
        } catch (\Throwable $e) {}
        return 'int';
    }

    /**
     * Check timestamps safely.
     */
    public function usesTimestamps()
    {
        try {
            return Schema::hasColumn('machines', 'updated_at') && Schema::hasColumn('machines', 'created_at');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Accessor so $item->id always returns the machine identifier for Blade views.
     */
    public function getIdAttribute()
    {
        return $this->attributes['id'] ?? ($this->attributes['machine_id'] ?? null);
    }

    /**
     * Accessor so $item->name works whether column is 'name' or 'machine_name'.
     */
    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? ($this->attributes['machine_name'] ?? '');
    }

    /**
     * Accessor so $item->type works whether column is 'type' or 'machine_type'.
     */
    public function getTypeAttribute()
    {
        return $this->attributes['type'] ?? ($this->attributes['machine_type'] ?? 'Front Load');
    }

    /**
     * Accessor so $item->machine_id always formats nicely as WM-001 if integer.
     */
    public function getMachineIdAttribute()
    {
        $val = $this->attributes['machine_id'] ?? ($this->attributes['id'] ?? null);
        if ($val !== null && is_numeric($val)) {
            return sprintf('WM-%03d', (int)$val);
        }
        return $val ?: 'WM-001';
    }

    /**
     * Default status badge styling helper
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Available' => 'bg-[#DCFCE7] text-[#16A34A] border border-emerald-200/50',
            'In Use' => 'bg-[#FEF3C7] text-[#D97706] border border-amber-200/50',
            'Under Maintenance' => 'bg-[#FEE2E2] text-[#DC2626] border border-rose-200/50',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
