<?php

return [

    /*
     * These are the default projectors that will be used when
     * no projectors are specified in the aggregate root.
     */
    'projectors' => [
        Modules\User\Infrastructure\Projections\UserProjector::class,
        Modules\Order\Infrastructure\Projections\OrderProjector::class,
    ],

    /*
     * These are the default reactors that will be used when
     * no reactors are specified in the aggregate root.
     */
    'reactors' => [
    ],

    /*
     * The stored event model to use.
     */
    'stored_event_model' => Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent::class,

    /*
     * The stored event repository to use.
     */
    'stored_event_repository' => Spatie\EventSourcing\StoredEvents\Repositories\EloquentStoredEventRepository::class,

    /*
     * The default serializer for events.
     */
    'event_serializer' => Spatie\EventSourcing\EventSerializers\JsonEventSerializer::class,

    /*
     * The default database connection for storing events.
     */
    'database_connection' => env('EVENT_SOURCING_CONNECTION', env('DB_CONNECTION', 'pgsql')),

    /*
     * The default table name for stored events.
     */
    'stored_events_table' => 'stored_events',

    /*
     * The default table name for snapshots.
     */
    'snapshots_table' => 'snapshots',

    /*
     * Should the aggregate root be replayed when it is retrieved?
     */
    'cache_aggregate_roots' => true,

    /*
     * When replaying events, the replay chunk count determines
     * how many events will be fetched at once.
     */
    'replay_chunk_count' => 1000,

];
