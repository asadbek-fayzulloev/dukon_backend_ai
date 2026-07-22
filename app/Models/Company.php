<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }
}
