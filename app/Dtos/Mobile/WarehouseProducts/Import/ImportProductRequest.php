<?php

namespace App\Dtos\Mobile\WarehouseProducts\Import;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ImportProductRequest extends Data
{
    #[DataCollectionOf(ProductsRequest::class)]
    public DataCollection $products;

}