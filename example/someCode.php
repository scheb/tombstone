<?php
require(__DIR__.'/../vendor/autoload.php');
require(__DIR__.'/../tombstone.php');

use Scheb\Tombstone\Graveyard;
use Scheb\Tombstone\GraveyardProvider;
use Scheb\Tombstone\Handler\LogHandler;

$logHandler = new LogHandler(__DIR__ . '/logs/tombstone.log');
$graveyard = new Graveyard([$logHandler]);
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
}

run();
