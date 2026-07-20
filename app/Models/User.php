<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $phone
 * @property string $name
 *
 */
class User extends Model
{
    protected $fillable = ['name', 'phone'];
}
