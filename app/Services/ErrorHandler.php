<?php

namespace App\Services;

use App\Exceptions\ErrorResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class ErrorHandler
{
    public const DEFAULT_ERROR_CODE = -1;

    public const SUCCESS_KEY = 'success';

    public const DATA_KEY = 'data';

    public const ERROR_KEY = 'error';

    public const META_KEY = 'meta';

    public function maintenanceResponse(): JsonResponse
    {
        return response()->json([
            self::SUCCESS_KEY => false,
            self::DATA_KEY => null,
            self::ERROR_KEY => ['message' => 'Server is under maintenance.', 'code' => '503'],
        ], 503);
    }

    public function handleException(Throwable $e): JsonResponse
    {
        return match ($e::class) {
            HttpException::class,
            NotFoundHttpException::class,
            MethodNotAllowedHttpException::class,
            TooManyRequestsHttpException::class,
            ErrorResponse::class => $this->errorResponse($e),
            AuthenticationException::class => $this->errorResponse($e, 401, 401),
            ModelNotFoundException::class => $this->errorResponse($e->getMessage(), 200),
            ThrottleRequestsException::class => $this->errorResponse($e->getMessage(), 429),
            ValidationException::class => $this->validationErrorResponse($e, 422),
            default => $this->errorResponse($e, self::DEFAULT_ERROR_CODE, 500),
        };
    }

    public function errorResponse(
        string|array|Throwable $message,
                               $code = null,
                               $http_code = null
    ): JsonResponse
    {
        $exception = $message;
        if ($message instanceof Throwable) {
            $code ??= $message->getCode();
            $http_code ??= rescue(fn() => $message->getStatusCode()) ?? $message->getCode();
            $message = $message->getMessage();

            if ($code === self::DEFAULT_ERROR_CODE) {
                $this->sendToSentry($exception);
            }
        } elseif (is_array($message)) {
            [$message, $code, $http_code] = $message;
        }

        if (is_numeric($code) && $code >= 100 && $code <= 599) {
            $http_code ??= $code;
        }
        info('ErrorResponse', [$message, $code, $http_code]);

        $code ??= self::DEFAULT_ERROR_CODE;
        $http_code ??= 500;

        $out = [
            self::SUCCESS_KEY => false,
            self::DATA_KEY => null,
            self::ERROR_KEY => [
                'message' => mb_convert_encoding($message, 'UTF-8', 'UTF-8'),
                'code' => is_numeric($code) ? $code : mb_convert_encoding($code, 'UTF-8', 'UTF-8'),
            ],
        ];

        return response()->json($out, $http_code);
    }

    public function sendToSentry($e): void
    {
        return;
    }

    public function validationErrorResponse(ValidationException $e): JsonResponse
    {
        $out = [
            self::SUCCESS_KEY => false,
            self::DATA_KEY => null,
            self::ERROR_KEY => [
                'message' => 'Validation error',
                'code' => 422,
            ],
            self::META_KEY => [
                'validation_errors' => collect($e->validator->errors()->messages())->mapWithKeys(fn(
                    $errors,
                    $field
                ) => [$field => $errors[0]]),
            ],
        ];

        return response()->json($out, 422);
    }

    public function notify(Throwable $e): void
    {
        /* if (app()->bound('sentry')) {
             app('sentry')->captureException($e);
         }*/

        $messages = [];
        $messages[] = 'New error on ' . config('app.domain') . ' (' . config('app.env') . '):';
        $messages[] = 'Exception: ' . $e->getMessage() . PHP_EOL;
        if (auth()->check()) {
            $messages[] = 'User: ' . auth()->user()->id . ' (' . auth()->user()->phone . ')';
        }
        $messages[] = 'URL: ' . request()->fullUrl();
        $messages[] = 'IP: ' . request()->ip();
        $messages[] = 'Code: ' . $e->getCode();
        $messages[] = 'File: ' . $e->getFile();
        $messages[] = 'Line: ' . $e->getLine() . PHP_EOL;
        $messages[] = 'Trace: ' . getLastFiveTraceEntries($e);

        $message = implode(PHP_EOL, $messages);

        if (is_prod()) {
            tn($message);
        }
    }
}
