<?php

namespace App\Models;

use App\Enums\ResponseCodes\OtpRespCode;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{

    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'otps';
    protected $casts = [
        'details' => 'array',
        'expires_at' => 'datetime',
        'sent_at' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public static function findBySid(string $id): static
    {
        $otp = self::where('created_at', '>', now()->subDay())->where('id', $id)->first();

        error_if($otp === null, OtpRespCode::NOT_FOUND_OR_SESSION_EXPIRED);
        return $otp;
    }

    public function userDevice(): BelongsTo
    {
        return $this->belongsTo(UserDevice::class);
    }

    public function setDetails(string|array $key, $value = null): static
    {
        if (empty($this->details)) $this->details = [];
        if (is_array($key))
            $this->details = array_merge($this->details, $key);
        else
            $this->details[$key] = $value;

        return $this;
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(set: fn() => now());
    }

    public function notExpired(): bool
    {
        return !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }
}
