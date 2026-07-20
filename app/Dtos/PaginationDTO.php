<?php

namespace App\Dtos;

use Illuminate\Contracts\Pagination\Paginator;
use Spatie\LaravelData\Data;

class PaginationDTO extends Data
{
    public function __construct(
        ?Paginator $paginator = null,
        public int $current_page = 0,
        public int $per_page = 0,
        public bool $has_more = false,
        public int $items_count = 0,
        public ?int $total_count = null,
        public ?int $pages_count = null,
    ) {
        if (! is_null($paginator)) {
            $this->current_page = $paginator->currentPage();
            $this->per_page = $paginator->perPage();
            $this->has_more = $paginator->hasMorePages();
            $this->items_count = $paginator->count();
            $this->total_count = $paginator->total() ?? null;
            $this->pages_count = $paginator->lastPage() ?? null;
        }
    }
}
