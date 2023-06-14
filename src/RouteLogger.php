<?php

namespace Dora38\LogExt;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class RouteLogger
{
    private string $logLevel;

    /** @var string[] $includes */
    private array $includes = [];

    /** @var string[] $excludes */
    private array $excludes = [];

    public function setupListeners(string $logLevel, array $includes, array $excludes): void
    {
        $this->logLevel = $logLevel;
        $this->includes = $includes;
        $this->excludes = $excludes;

        Event::listen(
            RouteMatched::class,
            function (RouteMatched $event): void {
                $path = $event->request->path();
                if (! $this->shouldBeIncluded($path) or $this->shouldBeExcluded($path)) {
                    return;
                }

                Log::log($this->logLevel, "Route: request({$event->request->getRequestUri()}) ip({$event->request->ip()}) route({$event->route->uri}) name({$event->route->getName()}) action({$event->route->getActionName()})");
            }
        );
    }

    private function shouldBeIncluded(string $path): bool
    {
        if (empty($this->includes)) {
            return true;
        }

        foreach ($this->includes as $include) {
            if ($path === $include or str_starts_with($path, "$include/")) {
                return true;
            }
        }

        return false;
    }

    private function shouldBeExcluded(string $path): bool
    {
        foreach ($this->excludes as $exclude) {
            if ($path === $exclude or str_starts_with($path, "$exclude/")) {
                return true;
            }
        }

        return false;
    }
}
