<?php

namespace Dora38\LogExt;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Psr\Log\LogLevel;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/log-ext.php', 'log-ext');
    }

    public function boot(
        CommandLogger $commandLogger,
        RouteLogger $routeLogger,
        SqlLogger $sqlLogger,
    ): void
    {
        $this->publishes([__DIR__ . '/../config/log-ext.php' => config_path('log-ext.php')]);

        if (
            $this->checkLogLevel('log-ext.command_log_level') and
            config('log-ext.command_log_enable') and $this->app->runningInConsole()
        ) {
            $commandLogger->setupListeners(
                strtolower(config('log-ext.command_log_level')),
                config('log-ext.command_log_exclude')
            );
        }

        if (
            $this->checkLogLevel('log-ext.route_log_level') and
            config('log-ext.route_log_enable') and ! $this->app->runningInConsole()
        ) {
            $routeLogger->setupListeners(
                strtolower(config('log-ext.route_log_level')),
                config('log-ext.route_log_include'),
                config('log-ext.route_log_exclude')
            );
        }

        if (
            $this->checkLogLevel('log-ext.sql_log_level') and
            (
                (config('log-ext.sql_log_console') and $this->app->runningInConsole()) or
                (config('log-ext.sql_log_http') and ! $this->app->runningInConsole())
            )
        ) {
            $sqlLogger->setupListeners(strtolower(config('log-ext.sql_log_level')));
        }
    }

    private function checkLogLevel(string $key): bool
    {
        $logLevel = config($key);
        if (
            ! in_array(strtolower($logLevel), [
                LogLevel::EMERGENCY,
                LogLevel::ALERT,
                LogLevel::CRITICAL,
                LogLevel::ERROR,
                LogLevel::WARNING,
                LogLevel::NOTICE,
                LogLevel::INFO,
                LogLevel::DEBUG,
            ])
        ) {
            Log::notice("invalid {$key}={$logLevel}");

            return false;
        }

        return true;
    }
}
