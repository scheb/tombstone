<?php

declare(strict_types=1);

use Scheb\Tombstone\Formatter\JsonFormatter;
use Scheb\Tombstone\GraveyardBuilder;
use Scheb\Tombstone\Handler\AnalyzerLogHandler;
use Scheb\Tombstone\Handler\StreamHandler;
use Scheb\Tombstone\TestApplication\App\Application;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/logger/tombstone-function.php';
require_once __DIR__.'/src/functions.php';

$analyzerLogHandler = new AnalyzerLogHandler(__DIR__.'/logs');
$jsonLogHandler = new StreamHandler(__DIR__.'/logs/tombstones.json');
$jsonLogHandler->setFormatter(new JsonFormatter());

$graveyard = (new GraveyardBuilder())
    ->stackTraceDepth(3)
    ->rootDir(__DIR__)
    ->withHandler($analyzerLogHandler)
    ->withHandler($jsonLogHandler)
    ->autoRegister()
    ->build();

(new Application())->run();
