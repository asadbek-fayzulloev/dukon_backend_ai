<?php

namespace App\Filters;

use Carbon\Carbon;

class OrderFilter extends BaseQueryFilter
{
    public function user_id($value)
    {
        if ($value === null) {
            return $this->builder;
        }
        return $this->builder->where('user_id', $value);
    }

    public function payment_type($value)
    {
        if ($value === null) {
            return $this->builder;
        }
        return $this->builder->whereHas('payments', function ($query) use ($value) {
            return $query->where('payment_type', $value);
        });
    }

    public function from_date($value)
    {
        if ($value === null) {
            return $this->builder;
        }
        return $this->builder->where('created_at', '>', Carbon::parse($value)->endOfDay());
    }

    public function to_date($value)
    {
        if ($value === null) {
            return $this->builder;
        }
        return $this->builder->whereDate('created_at', '<', Carbon::parse($value)->endOfDay());
    }
}