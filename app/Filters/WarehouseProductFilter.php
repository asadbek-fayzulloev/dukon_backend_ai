<?php

namespace App\Filters;

class WarehouseProductFilter extends BaseQueryFilter
{
    public function low_stock($value)
    {
        if ($value) {
            // products.quantity moved to warehouse_products (per-warehouse batches),
            // so "low stock" compares the aggregated stock across warehouses.
            return $this->builder->whereHas('product', function ($query) {
                $query->whereRaw(
                    '(SELECT COALESCE(SUM(quantity), 0) FROM warehouse_products WHERE warehouse_products.product_id = products.id) <= notify_limit'
                );
            });
        }
        return $this->builder;
    }

    public function search($value)
    {
        if ($value === null) {
            return $this->builder;
        }
        return $this->builder->whereHas('product', function ($query) use ($value) {
            $query->where('name', 'ilike', '%' . $value . '%')
                ->orWhere('code', '=', $value);
        });
    }

    public function name($value)
    {
        return $this->search($value);
    }
    public function category_id($value)
    {
        if ($value === null) {
            return $this->builder;
        }
        return $this->builder->whereHas('product', function ($query) use ($value) {
            $query->where('category_id', '=', $value);
        });
    }
    public function code($value)
    {
        if ($value === null) {
            return $this->builder;
        }
        return $this->builder->whereHas('product', function ($query) use ($value) {
            $query->where('code', '=', $value);
        });
    }
}
