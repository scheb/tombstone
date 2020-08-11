Graveyard
=========

The graveyard is the central component responsible for logging calls to tombstones.

Configuration
-------------

The graveyard is created with the `GraveyardBuilder`. The graveyard has to be registered to the `GraveyardRegistry`
class, which makes it globally accessible. The builder has the following options available:

```php
<?php

use Scheb\Tombstone\Logger\Graveyard\GraveyardBuilder;
use Scheb\Tombstone\Logger\Graveyard\GraveyardRegistry;

$graveyard = (new GraveyardBuilder())
    // Required. Absolute path to the directory containing all the code that can have tombstones.
    ->rootDirectory(__DIR__.'/src')

    // See documentation "Handlers and Formatters". Add a handler to the graveyard to log tombstone
    // calls. You should have at least one handler. The method can be called multiple times to add
    // more than one handler.
    ->withHandler($someHandler)

    // Per default the calling method is logged. If you want to store a stack trace for tombstone calls
    // to get a better idea where the a call is coming from, this is how many frames of the stack trace
    // are logged (default: 0). Be aware that this can significantly increase the size of logs.
    ->stackTraceDepth(5)

    // Make it a buffered graveyard. Read more about this feature below.
    ->buffered()

    // Add a PSR logger to the graveyard to log exceptions.
    ->withLogger($psrLogger)

    // Automatically register the new graveyard in the GraveyardRegistry once it is built.
    ->autoRegister()

    // Returns the graveyard instance.
    ->build();

// This is only necessary when you haven't used the "autoRegister" option.
GraveyardRegistry::getGraveyard($graveyard);
```

Setting the `rootDirectory` is necessary to store a relative file path in the logs. This reduces log size and makes the
data exchangeable between different servers/environments/installations, as they're all logging the same relative paths,
instead of absolute paths that might differ.

Choose the `rootDirectory` so that all the source code, which potentially can contain tombstones, is located under that
path. Then, when you run the [analyzer](../analyzer/index.md) to generate a report, point it to the same folder so that
it can map relative paths accordingly.

Buffering
---------

For best results, tombstones need to be executed on the production system. Since performance is crucial in a production
environment, you want to spend as least time as possible with the logging of tombstones.

The default `Graveyard` class is immediately passing tombstones to the handlers. That means, when a tombstone is called,
you will spend the time for logging that tombstone. If you want to delay slow I/O operations to a later point in time
(e.g. once the response is sent to the client), you can make use of graveyard buffering.

It can be configured via the `GraveyardBuilder`:

```php
<?php

use Scheb\Tombstone\Logger\Graveyard\BufferedGraveyard;
use Scheb\Tombstone\Logger\Graveyard\GraveyardBuilder;

/** @var BufferedGraveyard $graveyard */
$graveyard = (new GraveyardBuilder())
    ->rootDirectory(__DIR__.'/src')
    ->buffered()  // Makes it return a BufferedGraveyard
    ->build();

// Log some tombstone calls ...

// You can flush manually any time
$graveyard->flush();

// Final flush in the shutdown handler
register_shutdown_function(function () use ($graveyard): void {
    $graveyard->flush();
    // Once you've flushed for the final time, you should enable auto-flushing, so any additional
    // tombstone calls coming after this will log immediately (just like the unbuffered graveyard).
    // Otherwise these calls might not be flushed at all and therefore lost.
    $graveyard->setAutoFlush(true);
});
```
