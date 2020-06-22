<?php

declare(strict_types=1);

use Scheb\Tombstone\TestApplication\App\Application;
use Scheb\Tombstone\GraveyardProvider;
use Scheb\Tombstone\Handler\AnalyzerLogHandler;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/logger/tombstone-function.php';
require_once __DIR__.'/src/functions.php';

$streamHandler = new AnalyzerLogHandler(__DIR__.'/logs');
GraveyardProvider::getGraveyard()->addHandler($streamHandler);
GraveyardProvider::getGraveyard()->setRootDir(__DIR__);

(new Application())->run();
