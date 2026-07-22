<?php

namespace App\Filters;

class AdminsFilter extends BaseQueryFilter
{
    public function search($value)
    {
        if ($value === null) {
            return $this->builder;
        }

        return $this->builder->where(function ($query) use ($value) {
            $query->orWhereRaw('name ilike ?', ["%$value%"])
                ->orWhereRaw('email ilike ?', ["%$value%"]);
        });
    }
}
