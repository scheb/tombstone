<?php
namespace Scheb\Tombstone\Example;

require(__DIR__.'/../vendor/autoload.php');
require(__DIR__.'/../tombstone.php');

use Scheb\Tombstone\Graveyard;
use Scheb\Tombstone\GraveyardProvider;
use Scheb\Tombstone\Handler\AnalyzerLogHandler;

$logHandler = new AnalyzerLogHandler(__DIR__ . '/logs');
$graveyard = new Graveyard(array($logHandler));
GraveyardProvider::setGraveyard($graveyard);

class Testing {

    public function publicMethod() {
        $this->assumedDeadMethod();
    }

    private function assumedDeadMethod() {
        tombstone('2015-08-10', 'scheb');
    }
}

function run() {
    $test = new Testing();
    $test->publicMethod();
    tombstone('2015-08-13', 'scheb');
}

run();
