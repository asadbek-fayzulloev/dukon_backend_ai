<?php

namespace App\Services;

use App\Enums\OtpType;
use App\Models\Otp;
use App\Models\UserDevice;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Throwable;

class OtpService
{
    const SETTINGS_KEY_PREFIX = 'otp_';
    public ?OtpType $type;
    public bool $sent = false;
    private $generator_func = null;
    private ?Otp $model = null;
    private ?UserDevice $device = null;

    public function __construct(
        string|OtpType        $type,
        private array         $replaces = [],
        public string         $phone = '',
        private readonly bool $is_new = true
    )
    {
        set_if($type instanceof OtpType, $this->type, $type, fn() => OtpType::tryFrom($type));
        error_if($this->type === null, OtpRespCode::OTP_OR_PHONE_NOT_FOUND);
        set_if(empty($this->phone) && is_user(), $this->phone, fn() => user()->phone);

        $this->device = UserDevice::currentDevice();

        if ($this->is_new) {
            $model = new Otp();
            $model->id = ulid();
            $model->type = $this->type->value;
            $model->phone = $this->phone;
            $model->user_device_id = $this->device->id;
            $model->expires_at = now()->addSeconds($this->type->expiry());

            $this->model = $model;
        }
    }

    public static function loadBySessionId(string|null $session_id, OtpType|string $type): self
    {
        $otp = new self($type, is_new: false);
        if ($otp->enabled()) {
            $otp->model = Otp::findBySid($session_id);
            error_if($otp->model->retries > 3, OtpRespCode::NOT_FOUND_OR_SESSION_EXPIRED);

            $replaces = $otp->model->details['__replaces__'] ?? [];
            $otp->type = OtpType::tryFrom($otp->model->type);
            $otp->phone = $otp->model->phone;
            $otp->device = UserDevice::currentDevice();
            $otp->model->user_device_id = $otp->device->id ?? 0;
            $otp->setReplaces($replaces);
        }

        return $otp;
    }

    public function enabled(): bool
    {
        return $this->type?->enabled() ?? false;
    }

    public function setReplaces(array $replaces): OtpService
    {
        $this->replaces = array_merge($this->replaces, $replaces);
        return $this;
    }

    public function setGenerator(callable $generator_func): OtpService
    {
        $this->generator_func = $generator_func;
        return $this;
    }

    public function check(string|null $otp, $phone = null): OtpService
    {
        if ($this->enabled()) {
            $result = $this->checkOnly($otp, $phone);
            error_unless($result, OtpRespCode::INCORRECT_CODE);
        }
        return $this;
    }

    public function checkOnly(string|null $otp, $phone = null): bool
    {
        if ($this->enabled() && $otp !== null) {
            $this->model->retries = $this->model->retries + 1;
            $this->model->save();

            return $this->model->code === $otp && $this->model->notExpired() && ($phone === null || $this->model->phone === $phone);
        }
        return false;
    }

    public function send(): OtpService
    {
        error_if($this->sent, 'OTP already sent');
        error_if($this->model->resends > 2, OtpRespCode::NOT_FOUND_OR_SESSION_EXPIRED);

        if ($this->enabled()) {
            if (!$this->is_new) {
                $this->model->expires_at = now()->addSeconds($this->type->expiry());
                $this->model->resends = $this->model->resends + 1;
            }
            $this->generateCode();
            //$this->sendBySms();
            $this->sendByEmail();
            $this->sendByTelegram();
            $this->writeDb();
        }

        return $this;
    }

    private function generateCode(): void
    {
        $testers = setting('tester_static_otp_codes', []);

        if (is_string($testers)) {
            $testers = json_decode($testers, true) ?: [];
        }

        if (is_array($testers) && $testers) {
            $tester = collect($testers)->firstWhere('phone', $this->phone);
            if ($tester && isset($tester['code']) && strlen($tester['code']) > 2) {
                $this->model->code = date($tester['code']);
                return;
            }
        }

        $code = '';
        if (is_callable($this->generator_func)) {
            $code = (string)call_user_func($this->generator_func);
        }

        $this->model->code = strlen($code) > 2 ? $code : str_pad((string)rescue(fn() => random_int(0, 999999),
            fn() => rand(0, 999999)), 6, '0', STR_PAD_LEFT);
    }

    private function sendBySms(): void
    {
        error_if(empty($this->phone), OtpRespCode::OTP_OR_PHONE_NOT_FOUND);

        $main_provider = setting('otp_provider', 'playmobile');
        $providers = collect(config('sms.providers', []))->keys()->filter(fn($k) => $k !== $this->model->provider);

        $this->model->provider = empty($this->model->provider) ? $main_provider : $providers->first() ?? $main_provider;

        sms_send($this->phone, $this->messageContent(), $this->model->provider);
        $this->model->sent_at = now();
        $this->sent = true;
    }

    private function messageContent(): string
    {
        $device = device();
        $type = $device->type;
        set_if(is_string($this->device) && in_array($this->device, UserDevice::TYPES), $type, $this->device);
        set_if(!$this->device instanceof UserDevice, $this->device, $device);
        $device->type = $type;
        $text = $this->type->message($this->device->type);
        set_if(strlen($text) < 1, $text, 'Verification code is :code');
        error_if(empty($text), OtpRespCode::MESSAGE_NOT_FOUND);

        $shouldReplace = [];

        foreach ($this->getReplaces() as $key => $value) {
            $shouldReplace[':' . Str::ucfirst($key ?? '')] = Str::ucfirst($value ?? '');
            $shouldReplace[':' . Str::upper($key ?? '')] = Str::upper($value ?? '');
            $shouldReplace[':' . $key] = $value;
        }

        return strtr($text, $shouldReplace);
    }

    public function getReplaces(): array
    {
        $replace = $this->replaces;
        $replace['ip'] ??= $this->device->last_ip;
        $this->model?->setDetails(['__replaces__' => $replace]);

        $replace['code'] ??= $this->otp();
        $replace['phone'] ??= phone_mask($this->phone);
        $replace['token'] ??= self::smsToken();
        $replace['ip'] ??= $this->device->last_ip;

        return $replace;
    }

    public function setDetails(string|array $key, $value = null): OtpService
    {
        $this->model->setDetails($key, $value);
        return $this;
    }

    private function otp(): ?string
    {
        if (empty($this->model->code)) {
            $this->generateCode();
        }
        return $this->model->code;
    }

    public static function smsToken(): string
    {
        return setting(OtpService::SETTINGS_KEY_PREFIX . 'token', '');
    }

    private function sendByEmail(): void
    {

    }

    /**
     * @throws Throwable
     */
    private function sendByTelegram(): void
    {
        $message = view('components.otp-code',
            ['phone' => $this->phone, 'message' => $this->messageContent()])->render();
        telegram_bot_send(message: $message, parse_mode: 'html');
    }

    private function writeDb(): void
    {
        //TODO: remove static provider name
        $this->model->provider = 'eskiz';
        $this->model?->save();
    }

    public function getDetails(): array
    {
        return $this->model->details;
    }

    public function toResponseFull(): array
    {
        return [
            'otp' => $this->toResponse(),
            'otp_enabled' => $this->enabled(),
        ];
    }

    public function toResponse(): ?array
    {
        if ($this->enabled()) {
            $phone = phone_mask($this->phone);
            return [
                'phone' => $phone,
                'message' => __('auth.sent_to_number', ['phone' => $phone]),
                'expires' => $this->type->expiry(),
                'code_length' => strlen($this->otp()),
                'input_type' => is_numeric($this->otp()) ? 'number' : 'text',
                'session_id' => $this->model->id,
                'type' => $this->type->value ?? 'unknown'
            ];
        }
        return null;
    }

    public function toDb(): array
    {
        return $this->enabled() ? [
            'enabled' => true, 'phone' => $this->phone, 'otp' => $this->otp()
        ] : ['enabled' => false];
    }

    public function prolong(int|Carbon $duration): OtpService
    {
        $this->model->expires_at = $duration instanceof Carbon ? $duration : $duration->diffInSeconds($duration);
        $this->model->save();
        return $this;
    }

    public function remove(): OtpService
    {
        if ($this->enabled()) {
            $this->model->verified_at = now();
            $this->model->save();
        }
        return $this;
    }

    private function detail($key, $default = null)
    {
        return Arr::get($this->model->details, $key, $default);
    }

    private function cacheKey(): string
    {
        return $this->type->value . ':' . ($this->model->id);
    }
}
