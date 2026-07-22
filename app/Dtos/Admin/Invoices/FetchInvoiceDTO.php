<?php

namespace App\Dtos\Admin\Invoices;

use App\Models\Admin;
use App\Models\Warehouse;
use DateTime;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class FetchInvoiceDTO extends Data
{
    public int $id;
    public string $type;
    public ?string $number;
    #[LoadRelation]
    public ?Warehouse $warehouse;
    #[LoadRelation]
    public ?Admin $admin;
    public ?float $total_amount;
    public ?int $histories_count;
    public ?DateTime $created_at;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'number' => $this->number,
            'warehouse_name' => $this->warehouse?->name,
            'admin_name' => $this->admin?->name,
            'total_amount' => $this->total_amount,
            'items_count' => $this->histories_count ?? 0,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
