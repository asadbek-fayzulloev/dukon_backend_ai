<?php

namespace App\Filters;

class ProductFilter extends BaseQueryFilter
{
    public function low_stock($value)
    {
        if ($value) {
            return $this->builder->whereColumn('quantity', '<=', 'notify_limit');
        }
        return $this->builder;
    }

    public function search($value)
    {
        if ($value === null) {
            return $this->builder;
        }
        $dinos = $this->builder;

        $words = explode(' ', $value);
        foreach ($words as $word) {
            $dinos->where('name', 'LIKE', '%' . $word . '%');
        }
        return $dinos;
        return $this->builder->where('name', 'ilike', '%' . $value.'%');
    }

    public function category_id($value)
    {
        if ($value === null) {
            return $this->builder;
        }
        return $this->builder->where('category_id', '=', $value);
    }
}