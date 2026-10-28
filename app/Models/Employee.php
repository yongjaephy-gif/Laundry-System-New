<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'employee_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'employee_id',
        'user_id',
        'full_name',
        'name',
        'phone_number',
        'phone',
        'email',
        'job_role',
        'role',
        'date_hired',
        'salary',
        'address',
        'password',
        'status',
        'work_schedule',
        'job_role_details',
        'avatar',
        'attendance_status',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Map name to full_name.
     */
    public function getNameAttribute()
    {
        return $this->attributes['full_name'] ?? ($this->attributes['name'] ?? '');
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['full_name'] ?? ($this->attributes['name'] ?? '');
    }

    /**
     * Map phone to phone_number.
     */
    public function getPhoneAttribute()
    {
        return $this->attributes['phone_number'] ?? ($this->attributes['phone'] ?? '');
    }

    public function getPhoneNumberAttribute()
    {
        return $this->attributes['phone_number'] ?? ($this->attributes['phone'] ?? '');
    }

    /**
     * Map role to job_role.
     */
    public function getRoleAttribute()
    {
        return $this->attributes['job_role'] ?? ($this->attributes['role'] ?? 'Staff');
    }

    public function getJobRoleAttribute()
    {
        return $this->attributes['job_role'] ?? ($this->attributes['role'] ?? 'Staff');
    }

    /**
     * Formatted Employee ID (e.g. EMP001).
     */
    public function getFormattedIdAttribute()
    {
        $id = $this->attributes['employee_id'] ?? ($this->attributes['id'] ?? 1);
        return 'EMP' . str_pad((string)$id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get avatar image URL, fallback to linked user profile_image_url or ui-avatars.
     */
    public function getAvatarAttribute()
    {
        if (!empty($this->attributes['avatar'])) {
            return $this->attributes['avatar'];
        }
        if (!empty($this->attributes['email'])) {
            try {
                $user = User::where('email', $this->attributes['email'])->first();
                if ($user && !empty($user->profile_image_url)) {
                    return $user->profile_image_url;
                }
            } catch (\Throwable $e) {}
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name ?? $this->name ?? 'User') . '&background=2563EB&color=fff';
    }
}
