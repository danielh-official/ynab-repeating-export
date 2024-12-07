<?php

namespace App\Providers;

use App\Contracts\YnabAccessTokenServiceInterface;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use App\Services\YnabAccessTokenService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(YnabAccessTokenServiceInterface::class, function () {
            return match (config('ynab-access-token-service.driver')) {
                'session' => app(YnabAccessTokenService::class),
                default => throw new \InvalidArgumentException('Invalid YNAB access token service driver.'),
            };
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @codeCoverageIgnore
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
