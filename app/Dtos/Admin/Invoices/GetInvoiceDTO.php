<?php

namespace App\Dtos\Admin\Invoices;

use App\Models\Admin;
use App\Models\Warehouse;
use DateTime;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Data;

class GetInvoiceDTO extends Data
{
    public int $id;
    public string $type;
    public ?string $number;
    #[LoadRelation]
    public ?Warehouse $warehouse;
    #[LoadRelation]
    public ?Admin $admin;
    public ?float $total_amount;
    public ?string $note;
    public ?DateTime $created_at;
    #[LoadRelation]
    public ?Collection $histories;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'number' => $this->number,
            'warehouse_name' => $this->warehouse?->name,
            'admin_name' => $this->admin?->name,
            'total_amount' => $this->total_amount,
            'note' => $this->note,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'items' => $this->histories?->map(fn ($h) => [
                'id' => $h->id,
                'product_id' => $h->product_id,
                'product_name' => $h->product?->name,
                'quantity' => (float) $h->quantity,
                'price' => (float) $h->price,
                'net_price' => (float) $h->net_price,
            ])->values()->all() ?? [],
        ];
    }
}
