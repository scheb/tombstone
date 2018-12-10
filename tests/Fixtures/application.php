<?php

use Scheb\Tombstone\Analyzer\Test\Fixtures\SampleClass;
use Scheb\Tombstone\GraveyardProvider;
use Scheb\Tombstone\Handler\AnalyzerLogHandler;

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../vendor/scheb/tombstone/tombstone.php';
require_once __DIR__.'/functions.php';

$streamHandler = new AnalyzerLogHandler(__DIR__.'/../_logs');
GraveyardProvider::getGraveyard()->addHandler($streamHandler);
GraveyardProvider::getGraveyard()->setSourceDir(__DIR__);

deadCodeFunction();
(new SampleClass())->publicMethod();
