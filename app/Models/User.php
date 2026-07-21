<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $phone
 * @property string $name
 *
 */
class User extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'phone'];
}
