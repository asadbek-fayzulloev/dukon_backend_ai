<?php

namespace App\Filters;

class UsersFilter extends BaseQueryFilter
{
    public function search($value)
    {
        if ($value === null) {
            return $this->builder;
        }

        return $this->builder->orWhereRaw("name ilike ?", ["%$value%"])
            ->orWhereRaw("phone ilike ?", ["%$value%"]);
    }
}
