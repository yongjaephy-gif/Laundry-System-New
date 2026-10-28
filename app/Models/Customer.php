<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    // Primary key configuration matching database (int auto_increment)
    protected $primaryKey = 'customer_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'customer_id',
        'full_name',
        'phone_number',
        'email',
        'address',
        'tier',
        'status',
        'avatar',
        'registration_date',
        'loyalty_points',
        'created_by',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'loyalty_points' => 'integer',
    ];

    /**
     * Dynamic primary key resolution.
     */
    public function getKeyName()
    {
        try {
            if (Schema::hasColumn($this->getTable(), 'id')) {
                return 'id';
            }
        } catch (\Throwable $e) {}

        return 'customer_id';
    }

    /**
     * Accessor for id to ensure backward compatibility with views.
     */
    public function getIdAttribute()
    {
        return $this->attributes['id'] ?? $this->attributes['customer_id'] ?? null;
    }

    /**
     * Accessor for customer_id to display as formatted C0001.
     */
    public function getCustomerIdAttribute()
    {
        $val = $this->attributes['customer_id'] ?? null;
        if (is_numeric($val)) {
            return 'C' . str_pad($val, 4, '0', STR_PAD_LEFT);
        }
        return $val ?? '';
    }

    /**
     * Accessor for name attribute matching full_name from database.
     */
    public function getNameAttribute()
    {
        return $this->attributes['full_name'] ?? $this->attributes['name'] ?? '';
    }

    /**
     * Accessor for phone attribute matching phone_number from database.
     */
    public function getPhoneAttribute()
    {
        return $this->attributes['phone_number'] ?? $this->attributes['phone'] ?? '';
    }

    /**
     * Accessor for email attribute.
     */
    public function getEmailAttribute()
    {
        return $this->attributes['email'] ?? '';
    }

    /**
     * Accessor for address attribute.
     */
    public function getAddressAttribute()
    {
        return $this->attributes['address'] ?? '';
    }

    /**
     * Relationship: A customer has many orders.
     */
    public function orders()
    {
        $localKey = $this->getKeyName();
        return $this->hasMany(CustomerOrder::class, 'customer_id', $localKey)->orderBy('order_date', 'desc');
    }

    /**
     * Scope for searching customers.
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        $numericId = null;
        if (preg_match('/^C0*(\d+)$/i', $search, $m)) {
            $numericId = (int)$m[1];
        }

        return $query->where(function ($q) use ($search, $numericId) {
            $q->where('customer_id', 'LIKE', "%{$search}%");
            if ($numericId !== null) {
                $q->orWhere('customer_id', $numericId);
            }

            try {
                if (Schema::hasColumn('customers', 'full_name')) {
                    $q->orWhere('full_name', 'LIKE', "%{$search}%");
                }
                if (Schema::hasColumn('customers', 'name')) {
                    $q->orWhere('name', 'LIKE', "%{$search}%");
                }
                if (Schema::hasColumn('customers', 'phone_number')) {
                    $q->orWhere('phone_number', 'LIKE', "%{$search}%");
                }
                if (Schema::hasColumn('customers', 'phone')) {
                    $q->orWhere('phone', 'LIKE', "%{$search}%");
                }
                if (Schema::hasColumn('customers', 'email')) {
                    $q->orWhere('email', 'LIKE', "%{$search}%");
                }
                if (Schema::hasColumn('customers', 'address')) {
                    $q->orWhere('address', 'LIKE', "%{$search}%");
                }
            } catch (\Throwable $e) {
                $q->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");
            }
        });
    }

    /**
     * Scope for filtering by tier or status.
     */
    public function scopeFilter($query, $filter)
    {
        if (empty($filter) || $filter === 'All' || $filter === 'All Customers') {
            return $query;
        }

        if (in_array(strtoupper($filter), ['GOLD', 'SILVER', 'BRONZE', 'REGULAR'])) {
            return $query->where('tier', strtoupper($filter));
        }

        if (in_array(ucfirst(strtolower($filter)), ['Active', 'Inactive'])) {
            return $query->where('status', ucfirst(strtolower($filter)));
        }

        return $query;
    }
}
