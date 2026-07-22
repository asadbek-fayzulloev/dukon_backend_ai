<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property int $unit_id
 * @property float|null $notify_limit
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'unit_id', 'notify_limit','category_id','code', 'company_id'];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function warehouseProducts(): HasMany
    {
        return $this->hasMany(WarehouseProduct::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
