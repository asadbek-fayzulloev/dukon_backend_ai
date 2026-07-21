<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use App\Rules\PhoneNumber;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Exception;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected array $custom_rules = [
        PhoneNumber::class,
    ];

    public function boot(): void
    {
        $this->addRouteMacros();
        $this->configureRateLimiting();

        FilamentShield::configurePermissionIdentifierUsing(
            fn($resource) => str($resource::getModel())
                ->afterLast('\\')
                ->lower()
                ->toString());
//        Order::observe(OrderObserver::class);
//        Product::observe(ProductObserver::class);
//        OrderPayment::observe(OrderPaymentObserver::class);

    }

    private function addRouteMacros(): void
    {
        Route::macro('module', function (string $name, $callback = null): RouteRegistrar {
            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
            $file = end($bt)['file'] ?? null;

            if (!blank($file) && file_exists($file)) {
                // Normalize both sides to forward slashes before diffing
                $normalizedFile = str_replace('\\', '/', dirname($file));
                $normalizedBase = str_replace('\\', '/', base_path('routes/api'));
                $dir = trim(str_replace($normalizedBase, '', $normalizedFile), '/');

                if (is_null($callback)) {
                    $callback = base_path("routes/api/{$dir}/{$name}.php");
                }

                if (is_string($callback)) {
                    $alternativeCallbackPath = base_path("routes/api/{$dir}/{$name}.php");

                    if (!file_exists($callback) && file_exists($alternativeCallbackPath)) {
                        $callback = $alternativeCallbackPath;
                    } elseif (!file_exists($callback)) {
                        throw new Exception("Route file {$callback} not found!");
                    }
                }

                return Route::prefix($name)->as("{$name}.")->group($callback);
            }
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->header('X-Real-IP',
                $request->header('X-DEVICE-ID', $request->ip())));
        });

        RateLimiter::for('sms', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->header('X-Real-IP',
                $request->header('X-DEVICE-ID', $request->ip())));
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->header('X-Real-IP',
                $request->header('X-DEVICE-ID', $request->ip())));
        });
    }

    public function register(): void
    {
        $this->app->singleton(
            LoginResponseContract::class,
            LoginResponse::class
        );
    }

    private function registerValidationRules(): void
    {
        if (!app()->runningInConsole()) {
            foreach ($this->custom_rules as $class) {
                $alias = (string)(new $class);
                if ($alias && strlen($alias) > 0) {
                    Validator::extend($alias, $class . '@passes', (new $class)->message());
                }
            }
        }
    }
}
