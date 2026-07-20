<?php

namespace App\Dtos\Products;

use Spatie\LaravelData\Data;

class FetchProductRequest extends Data
{
    public ?bool $low_stock;
    public ?int $per_page;
    public ?int $page;


}