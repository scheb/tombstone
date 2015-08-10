<?php
// Run this with CLI.
// In this example the tombstone is invoked, which displays a message on the console and writes a log file.

require(__DIR__.'/../vendor/autoload.php');

class Testing {
    public function methodCall() {
        callTombstone();
    }
}

function callTombstone() {
    global $tombstone;
    $tombstone->register('2015-08-10', 'scheb');
}

$echoHandler = new \Scheb\Tombstone\Handlers\EchoHandler();
$fileHandler = new \Scheb\Tombstone\Handlers\StreamHandler(__DIR__ . '/logs/tombstone.log');
$tombstone = new \Scheb\Tombstone\Tombstone([$echoHandler, $fileHandler]);

$test = new Testing();
$test->methodCall();
