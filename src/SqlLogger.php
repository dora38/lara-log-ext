<?php

namespace Dora38\LogExt;

use Barryvdh\Debugbar\DataCollector\QueryCollector;
use Barryvdh\Debugbar\DataFormatter\QueryFormatter;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class SqlLogger
{
    private string $logLevel;
    private QueryCollector $queryCollector;

    public function setupListeners(string $logLevel): void
    {
        $this->logLevel = $logLevel;

        // use the Laravel DebugBar's QueryCollector as a SQL formatter.
        // see: https://www.casleyconsulting.co.jp/blog/engineer/6097/
        $this->queryCollector = new QueryCollector();
        $this->queryCollector->setDataFormatter(new QueryFormatter());
        $this->queryCollector->setRenderSqlWithParams(true);

        Event::listen(
            QueryExecuted::class,
            function (QueryExecuted $query): void {
                $this->queryCollector->addQuery($query->sql, $query->bindings, $query->time, $query->connection);
                foreach ($this->queryCollector->collect()['statements'] as $statement) {
                    Log::log($this->logLevel, <<<EOL
SQL[{$statement['duration_str']}]:
{$statement['sql']}
EOL
                    );
                }
                $this->queryCollector->reset();
            }
        );

        // duration_str does not exist in Transaction events.
        Event::listen(
            TransactionBeginning::class,
            function (TransactionBeginning $event): void {
                Log::log($this->logLevel, "SQL:\nSTART TRANSACTION");
            }
        );

        Event::listen(
            TransactionCommitted::class,
            function (TransactionCommitted $event): void {
                Log::log($this->logLevel, "SQL:\nCOMMIT");
            }
        );

        Event::listen(
            TransactionRolledBack::class,
            function (TransactionRolledBack $event): void {
                Log::log($this->logLevel, "SQL:\nROLLBACK");
            }
        );
    }
}
