<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @var string|null
     */
    protected static ?string $resolvedPrimaryKey = null;

    /**
     * Get the primary key for the model dynamically.
     *
     * @return string
     */
    public function getKeyName(): string
    {
        if (static::$resolvedPrimaryKey !== null) {
            return static::$resolvedPrimaryKey;
        }

        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'user_id')) {
                return static::$resolvedPrimaryKey = 'user_id';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'id')) {
                return static::$resolvedPrimaryKey = 'id';
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return static::$resolvedPrimaryKey = (isset($this->attributes['user_id']) ? 'user_id' : 'id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
        'username',
        'full_name',
        'password_hash',
        'phone',
        'role_id',
        'profile_image_url',
        'status',
        'last_login',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the password for the user (supports both password and password_hash columns).
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password ?? $this->password_hash;
    }

    /**
     * Accessor for name to match full_name or name.
     */
    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? ($this->attributes['full_name'] ?? 'Admin User');
    }

    /**
     * Accessor for full_name to match name.
     */
    public function getFullNameAttribute()
    {
        return $this->attributes['full_name'] ?? ($this->attributes['name'] ?? 'Admin User');
    }

    /**
     * Accessor for username.
     */
    public function getUsernameAttribute()
    {
        return $this->attributes['username'] ?? (isset($this->attributes['email']) ? explode('@', $this->attributes['email'])[0] : 'admin');
    }

    /**
     * Mutator for password to ensure hashing.
     *
     * @param string|null $value
     * @return void
     */
    public function setPasswordAttribute(?string $value): void
    {
        if ($value === null) {
            return;
        }
        $hash = \Illuminate\Support\Facades\Hash::needsRehash($value)
            ? \Illuminate\Support\Facades\Hash::make($value)
            : $value;
        $this->attributes['password'] = $hash;
        $this->attributes['password_hash'] = $hash;
    }

    /**
     * Accessor for id.
     */
    public function getIdAttribute()
    {
        return $this->attributes['id'] ?? ($this->attributes['user_id'] ?? null);
    }

    /**
     * Accessor for user_id.
     */
    public function getUserIdAttribute()
    {
        return $this->attributes['user_id'] ?? ($this->attributes['id'] ?? null);
    }

    // getCurrentRole
    public function getCurrentRole()
    {

        // 1. ករណី User ប្តូរ Role តាម Switcher នៅលើ Navbar
        if (session()->has('active_role')) {
            return strtolower(session('active_role'));
        }
        // 2. Map លេខ role_id ទាំង ៤ ទៅតាមឈ្មោះ Role
        $roleMap = [
            1 => 'admin',
            2 => 'staff',
            3 => 'delivery',
            4 => 'receptionist',
        ];
        if (isset($roleMap[$this->role_id])) {
            return $roleMap[$this->role_id];
        }
        // 3. ករណី role_id ក្នុង database ផ្ទុកជា Text ស្រាប់ (ឧ. 'admin', 'staff')
        if (!empty($this->role_id)) {
            return strtolower((string) $this->role_id);
        }
        return 'admin';
    }

    // Admin
    public function isAdmin()
    {
        return $this->getCurrentRole() === 'admin';
    }

    // Recieptionist
    public function isRecieptionist()
    {
        return $this->getCurrentRole() === 'recieptionist';
    }

    // Staff
    public function isStaff()
    {
        return $this->getCurrentRole() === 'staff';
    }

    // Delivery
    public function isDelivery()
    {
        return $this->getCurrentRole() === 'delivery';
    }

    /**
     * Check if user has a specific role.
     *
     * @param string|null $roleName
     * @return bool
     */
    public function hasRole(?string $roleName): bool
    {
        if ($roleName === null) {
            return false;
        }
        $current = strtolower($this->getCurrentRole());
        $target = strtolower($roleName);
        // ការពារករណីសរសេរច្រឡំ receptionist / recieptionist
        if ($target === 'recieptionist') $target = 'receptionist';
        if ($current === 'recieptionist') $current = 'receptionist';
        return $current === $target;
    }
}