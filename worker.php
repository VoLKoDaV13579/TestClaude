<?php

/**
 * RoadRunner Laravel Worker Entry Point
 *
 * This file bootstraps the Laravel application and processes incoming
 * HTTP requests via the RoadRunner application server using the
 * spiral/roadrunner-laravel bridge.
 */

declare(strict_types=1);

use Spiral\RoadRunnerLaravel\Worker;

// Register the Composer autoloader
require __DIR__ . '/vendor/autoload.php';

// Bootstrap the Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Start the RoadRunner worker
// The worker will continuously accept and process HTTP requests
// through the RoadRunner relay protocol (pipes).
Worker::start($app);
