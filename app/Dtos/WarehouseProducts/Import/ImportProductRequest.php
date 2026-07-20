<?php

namespace App\Dtos\WarehouseProducts\Import;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ImportProductRequest extends Data
{
    #[DataCollectionOf(ProductsRequest::class)]
    public DataCollection $products;

}