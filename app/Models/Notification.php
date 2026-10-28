<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'id',
        'notification_id',
        'type',
        'notification_type',
        'title',
        'message',
        'target',
        'scheduled_date',
        'status',
    ];

    /**
     * Dynamically resolve primary key.
     */
    public function getKeyName()
    {
        try {
            if (Schema::hasColumn('notifications', 'id')) {
                return 'id';
            }
            if (Schema::hasColumn('notifications', 'notification_id')) {
                return 'notification_id';
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
            return Schema::hasColumn('notifications', 'updated_at') && Schema::hasColumn('notifications', 'created_at');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Accessor so $item->id always returns the notification ID for Blade views.
     */
    public function getIdAttribute()
    {
        return $this->attributes['id'] ?? ($this->attributes['notification_id'] ?? null);
    }

    public function getTypeAttribute()
    {
        return $this->attributes['type'] ?? ($this->attributes['notification_type'] ?? 'Order Ready');
    }

    public function getTitleAttribute()
    {
        return $this->attributes['title'] ?? ($this->attributes['name'] ?? ($this->type ?? 'Notification'));
    }

    public function getMessageAttribute()
    {
        return $this->attributes['message'] ?? ($this->attributes['description'] ?? '');
    }

    public function getTargetAttribute()
    {
        return $this->attributes['target'] ?? ($this->attributes['recipient'] ?? 'All Customers');
    }

    /**
     * Get badge background class by status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status ?? 'Sent') {
            'Sent' => 'bg-emerald-50 text-emerald-600',
            'Pending' => 'bg-amber-50 text-amber-600',
            'Scheduled' => 'bg-blue-50 text-blue-600',
            'Failed' => 'bg-rose-50 text-rose-600',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}
