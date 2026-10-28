<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'id',
        'inventory_id',
        'item_id',
        'name',
        'item_name',
        'category',
        'category_name',
        'stock',
        'quantity',
        'qty',
        'unit',
        'unit_of_measure',
        'uom',
        'status',
        'min_stock',
        'minimum_stock',
        'reorder_level',
        'notes',
        'description',
        'supplier',
        'supplier_name',
        'unit_price',
        'price',
        'cost',
        'last_restocked',
        'restocked_at',
    ];

    /**
     * Dynamically resolve table name.
     */
    public function getTable()
    {
        try {
            if (Schema::hasTable('inventories')) {
                return 'inventories';
            }
            if (Schema::hasTable('inventory')) {
                return 'inventory';
            }
            if (Schema::hasTable('inventory_items')) {
                return 'inventory_items';
            }
        } catch (\Throwable $e) {}
        return 'inventories';
    }

    /**
     * Dynamically resolve primary key.
     */
    public function getKeyName()
    {
        try {
            $table = $this->getTable();
            if (Schema::hasColumn($table, 'inventory_id')) {
                return 'inventory_id';
            }
            if (Schema::hasColumn($table, 'item_id')) {
                return 'item_id';
            }
        } catch (\Throwable $e) {}
        return 'id';
    }

    /**
     * Handle timestamps safely if updated_at is absent.
     */
    public function usesTimestamps()
    {
        try {
            $table = $this->getTable();
            return Schema::hasColumn($table, 'updated_at') && Schema::hasColumn($table, 'created_at');
        } catch (\Throwable $e) {
            return false;
        }
    }

    // Accessors for smooth UI bindings
    public function getIdAttribute()
    {
        return $this->attributes['id'] ?? ($this->attributes['inventory_id'] ?? ($this->attributes['item_id'] ?? null));
    }

    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? ($this->attributes['item_name'] ?? '');
    }

    public function getCategoryAttribute()
    {
        return $this->attributes['category'] ?? ($this->attributes['category_name'] ?? 'Cleaning');
    }

    public function getStockAttribute()
    {
        return (int)($this->attributes['stock'] ?? ($this->attributes['quantity'] ?? ($this->attributes['qty'] ?? 0)));
    }

    public function getUnitAttribute()
    {
        return $this->attributes['unit'] ?? ($this->attributes['unit_of_measure'] ?? ($this->attributes['uom'] ?? 'L'));
    }

    public function getMinStockAttribute()
    {
        return (int)($this->attributes['min_stock'] ?? ($this->attributes['minimum_stock'] ?? ($this->attributes['reorder_level'] ?? 15)));
    }

    public function getNotesAttribute()
    {
        return $this->attributes['notes'] ?? ($this->attributes['description'] ?? '');
    }

    public function getSupplierAttribute()
    {
        return $this->attributes['supplier'] ?? ($this->attributes['supplier_name'] ?? 'Standard Supplier');
    }

    public function getUnitPriceAttribute()
    {
        return (float)($this->attributes['unit_price'] ?? ($this->attributes['price'] ?? ($this->attributes['cost'] ?? 0.00)));
    }

    public function getLastRestockedAttribute()
    {
        return $this->attributes['last_restocked'] ?? ($this->attributes['restocked_at'] ?? now()->toDateString());
    }

    /**
     * Auto evaluate and update status based on stock level.
     */
    public function updateStatusBasedOnStock()
    {
        $min = $this->min_stock ?: 15;
        $stock = $this->stock;
        if ($stock <= 0) {
            $newStatus = 'Out of Stock';
        } elseif ($stock <= $min) {
            $newStatus = 'Low Stock';
        } else {
            $newStatus = 'In Stock';
        }

        $table = $this->getTable();
        $updates = [];
        if (Schema::hasColumn($table, 'status')) {
            $updates['status'] = $newStatus;
        }
        $this->update($updates);
        $this->status = $newStatus;
        return $newStatus;
    }
}
