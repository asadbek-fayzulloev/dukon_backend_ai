<?php

use App\Contracts\ResponseCodeContract;
use App\Exceptions\ErrorResponse;
use App\Http\Interceptors\Interceptor;
use App\Models\User;
use App\Models\UserDevice;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

if (!function_exists('sms_send')) {
    function sms_send(string $phone, string $message, $provider = null, ?string $session_id = null): mixed
    {
        $providers = config('sms.providers', []);
        $class = '';
        if ($provider && isset($providers[$provider])) {
            $class = $providers[$provider];
        }
        if (!class_exists($class)) {
            $main_provider = config('sms.main_provider');
            $class = config('sms.providers')[$main_provider];
        }

        if (strlen($message) > 0) {
            if ($session_id) {
                $ok = $class::send($phone, $message, $session_id);
            } else {
                $ok = $class::send($phone, $message);
            }

            return app()->isLocal() ? true : $ok;
        }

        return false;
    }
}

if (!function_exists('error_response')) {
    /** @throws null */
    function error_response(string|ResponseCodeContract $message, string|int $code = 0, ?Throwable $previous = null)
    {
        if ($message instanceof ResponseCodeContract) {
            $code = $message->value ?? $code;
            $message = $message->message();
        }
        if (is_null($previous)) {
            $backfiles = debug_backtrace();
            $previous = new Exception("Error in {$backfiles[1]['file']} on line {$backfiles[1]['line']}" . PHP_EOL . "Error in {$backfiles[0]['file']} on line {$backfiles[0]['line']}");
        }

        throw new ErrorResponse($message, $code, $previous);
    }
}

if (!function_exists('error_unless')) {
    function error_unless(bool|callable $condition, string|ResponseCodeContract $message, string|int $code = 0): void
    {
        $backfiles = debug_backtrace();
        $previous = new Exception('Error in ' . $backfiles[0]['file'] . ' on line ' . $backfiles[0]['line']);
        $condition = is_callable($condition) ? $condition() : $condition;
        if ($condition === false) {
            error_response($message, $code, $previous);
        }
    }
}

if (!function_exists('error')) {
    function error(string|ResponseCodeContract $message): Closure
    {
        return fn() => error_response($message);
    }
}

if (!function_exists('error_if')) {
    function error_if(bool|callable $condition, string|ResponseCodeContract $message, string|int $code = 0): void
    {
        $backfiles = debug_backtrace();
        $previous = new Exception('Error in ' . $backfiles[0]['file'] . ' on line ' . $backfiles[0]['line']);

        $condition = is_callable($condition) ? $condition() : $condition;
        if ($condition === true) {
            error_response($message, $code, $previous);
        }
    }
}

/**
 * @param string $message
 * @param string $code
 * @return JsonResponse
 */
if (!function_exists('success_response')) {
    function success_response(string $message, string|int $code, int $http_code = 200): JsonResponse
    {
        return response()->json(compact('message', 'code'), $http_code);
    }
}

if (!function_exists('phone_mask')) {
    function phone_mask(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        return strlen($phone) === 12 ? '+' . mask($phone, 5, 4) : mask($phone, 4, 3);
    }
}

if (!function_exists('mask')) {
    /**
     * Phone/Card number mask
     */
    function mask(
        string $number,
        int    $mask_length = -1,
        int    $left_pad = -1,
        string $mask_char = '*',
        int    $mask_char_length = -1
    ): string
    {
        $length = strlen($number);
        $left_pad = max($left_pad, 0);
        $mask_length = $mask_length < 0 ? $length : min($mask_length, $length - $left_pad);
        $mask_char_length = $mask_char_length < 0 ? max($mask_length, 0) : $mask_char_length;
        $mask = str_repeat($mask_char, $mask_char_length);

        return preg_match('/^(.{' . $left_pad . '})(.{' . $mask_length . '})(.*?)$/', trim($number),
            $matches) ? $matches[1] . $mask . $matches[3] : $number;
    }
}
if (!function_exists('user')) {
    function user(): User|Authenticatable|null
    {
        error_unless(auth('api')->check(), 'Unauthorized.', 401);

        return auth('api')->user();
    }
}

if (!function_exists('is_user')) {
    function is_user(): bool
    {
        return auth('api')->check();
    }
}

if (!function_exists('is_guest')) {
    function is_guest(): bool
    {
        return auth('api')->guest();
    }
}

if (!function_exists('ulid')) {
    function ulid($long = false): string
    {
        return $long ? Str::ulid()->toBase32() : Str::ulid()->toBase58();
    }
}

if (!function_exists('uulid')) {
    function uulid(): string
    {
        return Str::ulid()->toRfc4122();
    }
}


if (!function_exists('input')) {
    /**
     * Get request input value
     */
    function input(string|array|null $key = null, $default = null, string|callable|null $filter = null): mixed
    {
        $val = $key === null ? $default : (is_array($key) ? request()->only($key) : request()->input($key, $default));
        if ($val !== null && $filter !== null) {
            if (is_callable($filter)) {
                $val = $filter($val);
            } elseif (is_string($filter)) {
                foreach (explode('|', $filter) as $func) {
                    if (function_exists($func)) {
                        $val = $func($val);
                    }
                }
            }
        }

        return $val;
    }
}

if (!function_exists('images_url')) {
    function images_url($path, ?string $type = null): string
    {
        $path = ($type ? trim($type, '/') . '/' : '') . trim($path, '/');
        if (!(str_ends_with($path, '.png') || str_ends_with($path, '.jpg') || str_ends_with($path,
                '.jpeg') || str_ends_with($path, '.gif'))) {
            $path .= '.png';
        }

        return trim(config('app.images_url'), '/') . '/' . $path;
    }
}

if (!function_exists('date_formatted')) {
    function date_formatted($date, string $name = '', string $format = 'd.m.Y', bool $value_only = false): array|string
    {
        // Attempt to convert a string input to a DateTime object, or validate a DateTime object
        if (is_string($date)) {
            try {
                $dateObj = new DateTime($date);
            } catch (Exception) {
                return $value_only ? '' : [];
            }
        } elseif ($date instanceof DateTime) {
            $dateObj = $date;
        } else {
            return $value_only ? '' : [];
        }

        // Once $dateObj is confirmed to be a DateTime object, proceed with formatting
        $formattedDate = $dateObj->format($format);

        // Return based on the $value_only flag
        return $value_only ? $formattedDate : [
            $name => $dateObj->format('Y-m-d H:i:s'),
            $name . '_formatted' => $formattedDate,
        ];
    }
}

if (!function_exists('is_dev')) {
    function is_dev(): bool
    {
        return !is_prod();
    }
}
if (!function_exists('minio_url')) {
    function minio_url(
        string|null     $path,
        int|Carbon|null $expiryMinutes = null,
        ?callable       $not_found_cb = null
    ): string|null
    {
        if (!empty($path)) {
            $disk = Storage::disk(config('filesystems.default'));
            if ($expiryMinutes) {
                $expired_at = is_int($expiryMinutes) ? now()->addMinutes($expiryMinutes) : $expiryMinutes;

                try {
                    return $disk->temporaryUrl($path, $expired_at);
                } catch (Exception) {
                }
            } else {
                try {
                    return $disk->url($path);
                } catch (Exception) {
                }
            }
        }

        return is_callable($not_found_cb) ? $not_found_cb() : null;
    }
}
if (!function_exists('is_prod')) {
    function is_prod(): bool
    {
        return config('app.env') === 'production';
    }
}

if (!function_exists('file_upload')) {
    function file_upload($file, $path): false|string|null
    {
        if ($file instanceof UploadedFile) {
            return $file->store($path, 'minio_assets');
        }

        return null;
    }
}

if (!function_exists('formatted')) {
    function formatted(
        array|int|float|string|null $amount,
        string                      $name = '',
                                    $tiyin = true,
                                    $decimals = 2,
                                    $append = '',
                                    $prepend = '',
                                    $no_zero = false,
                                    $value_only = false,
        ?array                      &$to_change = null
    ): array|string
    {
        if ($amount === null) {
            $amount = 0;
        }

        if (is_string($amount)) {
            $amount = (int)preg_replace('/\s+/', '', $amount);
        }

        if (is_array($amount)) {
            $arr = [];
            foreach ($amount as $key => $value) {
                $arr = [...$arr, ...formatted($value, $key, $tiyin, $decimals, $append, $prepend, $no_zero)];
            }

            if ($to_change !== null) {
                $to_change = array_merge($to_change, $arr);
            }

            return $arr;
        } else {
            if (strlen($name) > 0 || $value_only) {
                $fmtd = number_format($tiyin ? $amount / 100 : $amount, $decimals, '.', ' ');
                if ($no_zero) {
                    $fmtd = rtrim($fmtd, '0');
                }
                $fmtd = $prepend . $fmtd . $append;

                if ($value_only) {
                    return $fmtd;
                }

                $arr = [
                    $name => $amount,
                    $name . '_formatted' => $fmtd,
                ];

                if ($to_change !== null) {
                    $to_change = array_merge($to_change, $arr);
                }

                return $arr;
            }
        }

        return [];
    }
}

if (!function_exists('safe_explode')) {
    function safe_explode(string $separator, ?string $string, ?string $must_char = null): array
    {
        if ($string === null) {
            return [];
        }

        return array_values(
            array_filter(array_filter(explode($separator, $string)),
                static fn($item) => (empty($must_char) || str_contains($item, $must_char))),
        );
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        return auth('web')->check();
    }
}

if (!function_exists('admin')) {
    function admin(): Authenticatable|null
    {
        abort_unless(auth('web')->check(), 401);
        $admin = auth('web')->user();
        //        abort_unless($admin->is_active, 401);

        return $admin;
    }
}

if (!function_exists('can')) {
    function can(iterable|string $permission, $arguments = []): bool
    {
        if (is_admin()) {
            return admin()->can($permission, $arguments);
        }

        return false;
    }
}

if (!function_exists('set_if')) {
    function set_if(bool|callable|null $condition, &$variable, $value, $elseValue = "\0")
    {
        if ($condition === null) {
            $condition = true;
        }

        if (is_callable($condition)) {
            $condition = $condition();
        }

        if ($condition === true) {
            $variable = $value instanceof Closure ? $value() : $value;
        } elseif ($elseValue !== chr(0)) {
            $variable = is_callable($elseValue) ? $elseValue() : $elseValue;
        }

        return $variable;
    }
}

if (!function_exists('r')) {
    function r($any)
    {
        return (new Interceptor())->run($any);
    }
}

if (!function_exists('tn')) {
    // Telegram notification to "Finq Notifications" channel
    function tn(string $message, $chat_id = null): void
    {
        //TelegramNotifyJob::dispatch($message, $chat_id);
    }
}

if (!function_exists('telegram_notify')) {
    // Alias for tn()
    function telegram_notify(): void
    {
        tn(...func_get_args());
    }
}

function is_method_defined_in_class($className, $methodName): bool
{
    try {
        $reflectionClass = new ReflectionClass($className);

        if ($reflectionClass->hasMethod($methodName)) {
            $reflectionMethod = $reflectionClass->getMethod($methodName);

            return $reflectionMethod->getDeclaringClass()->getName() === $className;
        }
    } catch (Throwable) {
    }

    return false;
}

function getLastFiveTraceEntries(Throwable $exception): string
{
    $traceArray = $exception->getTrace();
    $lastFiveTraces = array_slice($traceArray, 0, 5);
    $traceString = '';

    foreach ($lastFiveTraces as $index => $trace) {
        $traceString .= "#{$index} " .
            ($trace['file'] ?? '[internal function]') .
            ':' . ($trace['line'] ?? 'N/A') . ' - ' .
            (isset($trace['class']) ? $trace['class'] . $trace['type'] : '') .
            $trace['function'] . '()' . "\n";
    }

    return $traceString;
}

if (!function_exists('get_latest_commit_sha')) {
    function get_latest_commit_sha(): string
    {
        return cache()->driver('file')->remember('latest_commit_sha', 5,
            fn() => trim(shell_exec('git rev-parse --short HEAD')));
    }
}

if (!function_exists('crb')) {
    function crb($date = null): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        return $date ? new Carbon($date) : null;
    }
}

if (!function_exists('trim_zeros')) {
    function trim_zeros(float $num): string
    {
        return rtrim(rtrim(sprintf('%.10f', $num), '0'), '.');
    }
}

if (!function_exists('r')) {
    function r($any): array
    {
        return (new Interceptor())->run($any);
    }
}

if (!function_exists('device')) {
    function device(): UserDevice
    {
        return UserDevice::currentDevice();
    }
}

if (!function_exists('format_permissions')) {
    function format_permissions(array $permissions): array
    {
        $newPermissions = [];

        foreach ($permissions as $module_name => $module_permissions) {
            foreach ($module_permissions as $permission_name => $permission_value) {
                if ($permission_value) {
                    $newPermissions[] = $module_name . '.' . $permission_name;
                }
            }
        }
        return $newPermissions;
    }
}

if (!function_exists('telegram_bot_send')) {
    function telegram_bot_send($message, $chat_id = null, $parse_mode = 'Markdown'): void
    {
        $token = config('services.telegram.token');
        if (empty($token)) {
            return;
        }
        $chat_id = $chat_id ?? config('services.telegram.chat_id');
        if (empty($chat_id)) {
            return;
        }

        $req = Http::baseUrl("https://api.telegram.org")->post("/bot{$token}/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => $parse_mode,
        ]);

        error_unless($req->ok(), 'Telegram bot send error');
    }
}
if (!function_exists('random_file_name')) {
    function random_file_name()
    {
        return now()->format('Y-m-d_H-i-s') . '_' . mt_rand(100, 999);
    }
}
if (!function_exists('folder_name')) {

    function folder_name(string $id, $forPinfl = false)
    {
        if ($forPinfl) {
            $step = 2;
            $parent = 'verification/pinfl';
        } else {
            $step = 1;
            $parent = 'verification/userid';
        }
        if (strlen($id) < 5) {
            $id = 0 . $id;
        }

        $dirs = [];
        $dirs[] = $parent;

        for ($i = 0; $i < 3; $i++) {
            $dirs[] = substr($id, $step * $i, $step);
        }

        $dirs[] = $id;

        return implode('/', $dirs);
    }
}
if (!function_exists('get_currency')) {
    function get_currency(): array
    {
        return array_values(Http::get('https://cbu.uz/uz/arkhiv-kursov-valyut/json/')->json());
    }
}
if (!function_exists('printToXprinter')) {

    function printToXprinter()
    {
        try {
            $printerName = getFirstXprinter(); // Function to find the first Xprinter
            if (!$printerName) {
                throw new Exception("No Xprinter found on the system.");
            }

            $connector = new WindowsPrintConnector($printerName);
            $printer = new Printer($connector);

            // Print test message
            $printer->text("Hello from Laravel!\n");
            $printer->cut();
            $printer->close();

            echo "Print sent to: $printerName";

        } catch (Exception $e) {
            echo "Print Error: " . $e->getMessage();
        }
    }
}
if (!function_exists('getFirstXprinter')) {

    function getFirstXprinter()
    {
        $output = shell_exec('wmic printer get name');
        $printers = explode("\n", trim($output));

        foreach ($printers as $printer) {
            if (stripos($printer, "Xprinter") !== false) {
                return trim($printer);
            }
        }
        return null;
    }
}
if (!function_exists('s3_file_upload')) {
    function s3_file_upload(UploadedFile|string $file, $path, ?string $disk = null): false|string|null
    {
        try {
            $disk ??= config('filesystems.disks.public');

            if ($file instanceof UploadedFile) {
                return $file->store($path, $disk);
            } else {
                return Storage::disk($disk)->put($path, $file);
            }
        } catch (Exception) {
            return null;
        }
    }
}
