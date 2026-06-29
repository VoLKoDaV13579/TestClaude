<?php

declare(strict_types=1);

namespace Modules\Shared\Infrastructure;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connection;

final readonly class EventStoreConnection
{
    public function __construct(
        private string $connectionName = 'pgsql',
    ) {}

    public function connection(): Connection
    {
        return DB::connection($this->connectionName);
    }

    public function transactional(callable $callback): mixed
    {
        return $this->connection()->transaction($callback);
    }

    public function getStoredEventsTable(): string
    {
        return config('event-sourcing.stored_events_table', 'stored_events');
    }

    public function getSnapshotsTable(): string
    {
        return config('event-sourcing.snapshots_table', 'snapshots');
    }
}
