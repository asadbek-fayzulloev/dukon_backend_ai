<?php

namespace App\Filters;

class DebtsFilter extends BaseQueryFilter
{
    public function search($value)
    {
        if ($value === null) {
            return $this->builder;
        }

        return $this->builder->whereHas('user', function ($query) use ($value) {
            $query->where(function ($query) use ($value) {
                $query->whereRaw('name ilike ?', ["%$value%"])
                    ->orWhereRaw('phone ilike ?', ["%$value%"]);
            });
        });
    }

    public function status($value)
    {
        if ($value === null) {
            return $this->builder;
        }

        return $this->builder->where('status', $value);
    }
}
