Handlers and Formatters
=======================

Handlers write the information that was received from a tombstone to a logging system (e.g. a log file on the disk).
Formatters are used to serialize the information from the log record. The library comes with some pre-defined handlers
and formatters.

Handlers
--------

Handlers are registered to the graveyard when it is built:

```php
<?php

use Scheb\Tombstone\Logger\Graveyard\GraveyardBuilder;
use Scheb\Tombstone\Logger\Handler\HandlerInterface;

/** @var HandlerInterface $someHandler */
/** @var HandlerInterface $anotherHandler */

$graveyard = (new GraveyardBuilder())
    ->rootDirectory('src')
    ->withHandler($someHandler)
    ->withHandler($anotherHandler)
    ->build();
```

These are the handler classes located in `Scheb\Tombstone\Logger\Handler`:

### `StreamHandler`

Takes a stream resource, stream identifier (`file://`, `ftp://`, etc.) or a file path as the target. This is the
standard handler for writing log files. For example, if you want to write a JSON log file, this is how you would
configure the handler with a formatter:

```php
<?php

use Scheb\Tombstone\Logger\Formatter\JsonFormatter;
use Scheb\Tombstone\Logger\Handler\StreamHandler;

$jsonLogHandler = new StreamHandler('/logs/tombstones.json');
$jsonLogHandler->setFormatter(new JsonFormatter());
```

### `PsrLoggerHandler`

Connects a PSR-3 logger and writes log messages with a certain severity level.

```php
<?php

use Psr\Log\LoggerInterface;
use Scheb\Tombstone\Logger\Handler\PsrLoggerHandler;

/** @var LoggerInterface $psrLogger */

// Log tombstone calls to $psrLogger with "warn" level
$psrLoggerHandler = new PsrLoggerHandler($psrLogger, 'warn');
```

### `AnalyzerLogHandler`

This format is used to exchange data for [report generation](../analyzer/index.md).

It writes multiple log files to a directory, one file for each tombstone and day. The naming pattern is
`[tombstone-id]-[date].tombstone`, for example `1325626564-20200101.tombstone`. From the date part you can easily
identify log data that has become too old and delete it.

The `AnalyzerLogHandler` takes an optional size limit to prevent individual log files from taking too much space with
repetitive data. In the end, it doesn't make so much of a difference if a tombstone was called 100 or a million times.

```php
<?php

use Scheb\Tombstone\Logger\Handler\AnalyzerLogHandler;

// Write log files to the "logs/tombstones" directory and stop logging to a file when it reaches 100kB
$analyzerLogHandler = new AnalyzerLogHandler('logs/tombstones', 102400);
```

Formatters
----------

All handlers have a default formatter. For some handlers, such as the `StreamHandler`, the formatter can be changed by
calling `setFormatter()`.

These are the formatter classes located in `Scheb\Tombstone\Logger\Formatter`:

- `JsonFormatter`: JSON log record
- `LineFormatter`: Human-readable log record
- `AnalyzerLogFormatter`: A compact, machine-readable JSON string, which is used by the
  [report generator](../analyzer/index.md)

Custom Handlers and Formatters
------------------------------

You can create your own handlers and formatters by implementing the respective interfaces
`Scheb\Tombstone\Logger\Handler\HandlerInterface` or `Scheb\Tombstone\Logger\Formatter\FormatterInterface`.
