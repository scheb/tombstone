Installation
============

## Step 1: Install with Composer

```bash
composer require scheb/tombstone-logger
```

## Step 2: Define a tombstone function

You have to define a function `tombstone(string ...$arguments): void` in the global scope, which is sending tombstone
calls to the graveyard.

The library is shipped with a standard implementation. Include `tombstone-function.php` from the logger's directory in
your bootstrap. It is recommended to define this function as early as possible, so that executed code (which potentially
can contain tombstones) doesn't run into "Call to undefined function" errors.

```php
<?php
require 'vendor/scheb/tombstone-logger/tombstone-function.php';
```

## Step 3: Create a graveyard

The graveyard is the central component responsible for logging calls to tombstones. The simplest way to create a
graveyard is this. Include the code in your bootstrap right after defining the tombstone function.

```php
<?php

use Scheb\Tombstone\Logger\Graveyard\GraveyardBuilder;

(new GraveyardBuilder())
    // Absolute path to the directory containing all the code that can have tombstones.
    ->rootDirectory(__DIR__.'/src')
    ->autoRegister()
    ->build();
```

## Step 4: Register a handler

By default, the graveyard isn't doing anything with the tombstone calls. You have to register a
handler.

What you usually want is the `StreamHandler`, which writes formatted log records to a file:

```php
<?php

use Scheb\Tombstone\Logger\Handler\StreamHandler;

$streamHandler = new StreamHandler('logs/tombstones.log');
```

If you want to [generate reports](../analyzer/index.md) you need the `AnalyzerLogHandler`:

```php
<?php

use Scheb\Tombstone\Logger\Handler\AnalyzerLogHandler;

$analyzerLogHandler = new AnalyzerLogHandler('logs/tombstones');
```

Register handlers to the graveyard via the `GraveyardBuilder`:

```php
<?php

use Scheb\Tombstone\Logger\Graveyard\GraveyardBuilder;

(new GraveyardBuilder())
    ->rootDirectory(__DIR__.'/src')
    ->autoRegister()
    ->withHandler($streamHandler)
    ->withHandler($analyzerLogHandler)  // You can add as many as you want
    ->build();
```

### Further Steps

See [all configuration options](graveyard.md) of the `GraveyardBuilder`.

Read more how to configure [handlers and formatters](handlers_formatters.md) or how to write your own
ones.
