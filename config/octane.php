<?php

declare(strict_types=1);

return [

    'server' => env('OCTANE_SERVER', 'roadrunner'),

    'https' => env('OCTANE_HTTPS', false),

    'listeners' => [],

    'warm' => [],

    'flush' => [],

    'cache' => [
        'rows' => 1000,
        'bytes' => 10000,
    ],

    'tables' => [],

    'garbage' => 50,

    'max_execution_time' => 30,

];
