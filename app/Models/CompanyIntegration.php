<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyIntegration extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'provider', 'url', 'username', 'password', 'connected_at'];

    protected $casts = [
        'password' => 'encrypted',
        'connected_at' => 'datetime',
    ];

    protected $hidden = ['password'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
