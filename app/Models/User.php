<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $phone
 * @property string $name
 *
 */
class User extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'phone'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class, 'user_id');
    }
}
