<?php

namespace Dora38\LogExt;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class CommandLogger
{
    private string $logLevel;

    /** @var string[] $excludes */
    private array $excludes = [];

    public function setupListeners(string $logLevel, array $excludes): void
    {
        $this->logLevel = $logLevel;
        $this->excludes = $excludes;

        Event::listen(
            CommandStarting::class,
            function (CommandStarting $event): void {
                if ($this->shouldBeExcluded($event->command)) {
                    return;
                }

                Log::log($this->logLevel, "{$event->command} starting.");
            }
        );

        Event::listen(
            CommandFinished::class,
            function (CommandFinished $event): void {
                if ($this->shouldBeExcluded($event->command)) {
                    return;
                }

                Log::log($this->logLevel, "{$event->command} finished. exit_code({$event->exitCode}).");
            }
        );
    }

    private function shouldBeExcluded(string $command): bool
    {
        return in_array($command, $this->excludes);
    }
}
