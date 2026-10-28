<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $fillable = [
        'id',
        'report_id',
        'name',
        'type',
        'category',
        'date_range',
        'date',
        'generated_by',
        'generated_on',
        'status',
        'description',
        'details',
    ];

    /**
     * Dynamically resolve primary key.
     */
    public function getKeyName()
    {
        try {
            if (Schema::hasColumn('reports', 'id')) {
                return 'id';
            }
            if (Schema::hasColumn('reports', 'report_id')) {
                return 'report_id';
            }
        } catch (\Throwable $e) {}
        return 'id';
    }

    /**
     * Check timestamps safely.
     */
    public function usesTimestamps()
    {
        try {
            return Schema::hasColumn('reports', 'updated_at') && Schema::hasColumn('reports', 'created_at');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Accessor so $item->id always returns the report ID for Blade views.
     */
    public function getIdAttribute()
    {
        return $this->attributes['id'] ?? ($this->attributes['report_id'] ?? null);
    }

    /**
     * Accessor for date_range fallback to date column.
     */
    public function getDateRangeAttribute()
    {
        return $this->attributes['date_range'] ?? ($this->attributes['date'] ?? 'Current');
    }

    /**
     * Pill badge color by type.
     */
    public function getTypeBadgeAttribute(): string
    {
        return match (strtolower($this->type ?? 'sales')) {
            'sales' => 'bg-emerald-50 text-emerald-600',
            'order' => 'bg-blue-50 text-blue-600',
            'payment' => 'bg-teal-50 text-teal-600',
            'inventory' => 'bg-amber-50 text-amber-600',
            'employee', 'staff' => 'bg-indigo-50 text-indigo-600',
            'machine' => 'bg-sky-50 text-sky-600',
            'delivery' => 'bg-green-50 text-green-600',
            'service' => 'bg-rose-50 text-rose-600',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
