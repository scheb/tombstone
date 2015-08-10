<?php
// Run this with CLI.
// In this example the tombstone is invoked, which displays a message on the console.

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

$fileHandler = new \Scheb\Tombstone\Handlers\EchoHandler();
$tombstone = new \Scheb\Tombstone\Tombstone([$fileHandler]);

$test = new Testing();
$test->methodCall();
