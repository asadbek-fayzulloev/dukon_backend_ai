<?php

namespace App\Filters;

class WarehouseProductFilter extends BaseQueryFilter
{
    public function low_stock($value)
    {
        if ($value) {
            return $this->builder->whereHas('product', function ($query) {
                $query->whereColumn('quantity', '<=', 'notify_limit');
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
