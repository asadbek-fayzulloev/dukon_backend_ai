<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class BaseQueryFilter
{
    public function __construct(protected Builder $builder)
    {
    }

    public function apply(): Builder
    {
        foreach (request()->all() as $name => $value) {
            if (method_exists($this, $name)) {
                $this->$name($value);
            }
        }

        return $this->builder;
    }
}
