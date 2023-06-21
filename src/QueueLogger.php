<?php

namespace Dora38\LogExt;

use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class QueueLogger
{
    private string $logLevel;

    public function setupListeners(string $logLevel): void
    {
        $this->logLevel = $logLevel;

        Event::listen(JobQueued::class, function (JobQueued $event): void {
            $displayName = get_class($event->job);
            $jobName = "jobs.{$event->id}.{$displayName}";
            Log::log($this->logLevel, "{$jobName} queued.");
        });

        Event::listen(JobProcessing::class, function (JobProcessing $event): void {
            $payload = $event->job->payload();
            $displayName = $payload['displayName'] ?? '';
            $jobName = "jobs.{$event->job->getJobId()}.{$displayName}";
            Log::log($this->logLevel, "{$jobName} processing. payload({$event->job->getRawBody()})");
        });

        Event::listen(JobProcessed::class, function (JobProcessed $event): void {
            $payload = $event->job->payload();
            $displayName = $payload['displayName'] ?? '';
            $jobName = "jobs.{$event->job->getJobId()}.{$displayName}";
            Log::log($this->logLevel, "{$jobName} processed.");
        });

        Event::listen(JobExceptionOccurred::class, function (JobExceptionOccurred $event): void {
            $payload = $event->job->payload();
            $displayName = $payload['displayName'] ?? '';
            $jobName = "jobs.{$event->job->getJobId()}.{$displayName}";
            $exceptionName = get_class($event->exception);
            $exceptionMessage = $event->exception->getMessage();
            Log::log($this->logLevel, "{$jobName} exception occurred. {$exceptionName}: {$exceptionMessage}");
        });
    }
}
