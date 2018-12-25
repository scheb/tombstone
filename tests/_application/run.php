<?php

use Scheb\Tombstone\Analyzer\TestApplication\App\Application;
use Scheb\Tombstone\GraveyardProvider;
use Scheb\Tombstone\Handler\AnalyzerLogHandler;

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../vendor/scheb/tombstone/tombstone.php';
require_once __DIR__.'/functions.php';
require_once __DIR__.'/App/Application.php';
require_once __DIR__.'/App/SampleClass.php';

$streamHandler = new AnalyzerLogHandler(__DIR__.'/../_logs');
GraveyardProvider::getGraveyard()->addHandler($streamHandler);
GraveyardProvider::getGraveyard()->setSourceDir(__DIR__);

(new Application())->run();
