<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDevice extends Model
{
    use SoftDeletes;

    const X_APP_TYPE = 'x-app-type';
    const X_DEVICE_TYPE = 'x-device-type';
    const X_DEVICE_MODEL = 'x-device-model';
    const X_DEVICE_UID = 'x-device-uid';
    const X_APP_VERSION = 'x-app-version';
    const X_APP_BUILD = 'x-app-build';
    const X_APP_LANG = 'x-app-lang';
    const X_APP_ORGANIZATION = 'x-app-organization';
    const TYPE_IOS = 'ios';
    const TYPE_ANDROID = 'android';
    const TYPE_WEB = 'web';
    const TYPES = [self::TYPE_IOS, self::TYPE_ANDROID, self::TYPE_WEB];

    protected $fillable = [
        'type',
        'uid',
        'model',
        'app_version',
        'app_build',
        'lang',
        'is_active',
        'last_ip',
        'used_ips',
        'last_used_at',
        'last_logged_in_at',
        'user_id',
        'phone',
        'fcm_token'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'used_ips' => 'array',
        'last_used_at' => 'datetime',
        'last_logged_in_at' => 'datetime',
    ];

    public static function currentDevice(): UserDevice
    {
        return self::fromDb() ?? self::fromHeader();
    }

    public static function fromDb(?int $user_id = null): ?UserDevice
    {
        $result = self::where('uid', request()->header(self::X_DEVICE_UID));
        if ($user_id !== null) {
            $result->orWhere('user_id', $user_id);
        }
        return $result->first();
    }

    public static function fromHeader(): UserDevice
    {
        $device = new self();
        $device->fill([
            'type' => trim(strtolower(request()->header(self::X_DEVICE_TYPE, ''))),
            'uid' => trim(request()->header(self::X_DEVICE_UID, '')),
            'model' => trim(request()->header(self::X_DEVICE_MODEL, '')),
            'app_version' => trim(request()->header(self::X_APP_VERSION, '')),
            'app_build' => trim(request()->header(self::X_APP_BUILD, '')),
            'lang' => trim(strtolower(request()->header(self::X_APP_LANG, ''))),
            'last_ip' => request()->header('X-Real-IP', request()->ip()),
            'last_used_at' => now(),
        ]);
        return $device;
    }

    public static function syncDevice(): void
    {
        $device = self::where('uid', request()->header(self::X_DEVICE_UID))->first();
        if ($device === null) {
            $device = self::fromHeader();
            $device->is_active = true;
        } else {
            $device->fill(self::fromHeader()->getAttributes());
            if (is_user()) {
                $device->user_id = user()->id;
            }
            $device->used_ips = array_unique(array_merge($device->used_ips ?? [],
                [request()->header('X-Real-IP', request()->ip())]));
        }
        $device->save();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'uid' => $this->uid,
            'model' => $this->model,
            'os_version' => $this->os_version,
            'app_version' => $this->app_version,
            'app_build' => $this->app_build,
            'lang' => $this->app_lang,
            'ip' => $this->ip,
        ];
    }
}
