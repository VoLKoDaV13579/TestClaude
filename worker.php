<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

(new \Laravel\Octane\Worker(
    new \Laravel\Octane\ApplicationFactory($app->basePath()),
    new \Laravel\Octane\RoadRunner\RoadRunnerClient(
        new \Spiral\RoadRunner\Http\HttpWorker(
            \Spiral\RoadRunner\Worker::create()
        )
    ),
))->run();
