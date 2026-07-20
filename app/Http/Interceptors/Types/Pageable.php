<?php

namespace App\Http\Interceptors\Types;

use App\Dtos\PaginationDto;
use App\Http\Interceptors\Contracts\BaseType;
use App\Http\Interceptors\Contracts\InterceptorType;
use Illuminate\Contracts\Pagination\Paginator;

class Pageable extends BaseType
{
    public function transform(): InterceptorType
    {
        $paginator = $this->getData();
        if (! $paginator instanceof Paginator) {
            return $this;
        }

        $this->setData($paginator->items());
        $this->setMeta([
            'pagination' => PaginationDto::from(['paginator' => $paginator]),
        ]);

        return $this;
    }
}
