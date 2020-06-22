<?php

declare(strict_types=1);

use Scheb\Tombstone\Analyzer\TestApplication\App\Application;
use Scheb\Tombstone\GraveyardProvider;
use Scheb\Tombstone\Handler\AnalyzerLogHandler;

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../vendor/scheb/tombstone/tombstone.php';

require_once __DIR__.'/src/functions.php';
require_once __DIR__.'/src/App/Application.php';
require_once __DIR__.'/src/App/DeletedTombstoneClass.php';
require_once __DIR__.'/src/App/SampleClass.php';

$streamHandler = new AnalyzerLogHandler(__DIR__.'/../_logs');
GraveyardProvider::getGraveyard()->addHandler($streamHandler);
GraveyardProvider::getGraveyard()->setRootDir(__DIR__);

(new Application())->run();
