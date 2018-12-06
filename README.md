scheb/tombstone
===============

Implements the concept of **Tombstones for PHP**.

Report generation provided by [scheb/tombstone-analyzer](https://github.com/scheb/tombstone-analyzer).

[![Build Status](https://travis-ci.org/scheb/tombstone.svg?branch=master)](https://travis-ci.org/scheb/tombstone)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/scheb/tombstone/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/scheb/scheb/tombstone/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/scheb/tombstone/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/scheb/tombstone/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/scheb/tombstone/v/stable.svg)](https://packagist.org/packages/scheb/tombstone)
[![Total Downloads](https://poser.pugx.org/scheb/tombstone/downloads)](https://packagist.org/packages/scheb/tombstone)
[![License](https://poser.pugx.org/scheb/tombstone/license.svg)](https://packagist.org/packages/scheb/tombstone)

<a href="http://www.youtube.com/watch?feature=player_embedded&v=29UXzfQWOhQ" target="_blank"><img src="http://img.youtube.com/vi/29UXzfQWOhQ/0.jpg" alt="Tombstone Youtube Video" width="240" height="180" border="10" /></a>

Inspired by: http://devblog.nestoria.com/post/115930183873/tombstones-for-dead-code

Installation
------------

1) Install via composer

```bash
$ composer require scheb/tombstone
```

2) Define a `tombstone()` function

You have to define a function `tombstone($date, $author, $label = null)`. The library is shipped with a default
implementation. Simply include `tombstone.php` from the libraries root directory in your bootstrap.

If you don't like the default implementation, you can implement the function on your own, as long as the signature is
the same (important for code analysis).

3) Configure the graveyard

All tombstones are sent to a "graveyard". By default the graveyard isn't doing anything with the tombstones. You have to
register a handler. What you usually want is the `StreamHandler`, which writes human-readable information to a log file.

```php
use Scheb\Tombstone\GraveyardProvider;
use Scheb\Tombstone\Handler\StreamHandler;

$streamHandler = new StreamHandler("$logDir/tombstones.log");
GraveyardProvider::getGraveyard()->addHandler($streamHandler);
```

[Read more about handlers and formatters below](#handlers-formatters).

### Relative Path Logs

By default the absolute file path is used in the logs. This can be in problem in some environments (e.g. when your 
deployment process creates a new directory for every release). If you tell the source root to the graveyard, it will log
the relative file path instead. This makes log files exchangeable between different servers/environments/installation
paths.

```php
GraveyardProvider::getGraveyard()->setSourceDir('/path/to/src');
```

It is especially useful, if you want to collect logs files from multiple production servers to run an overall analysis
on a central server.

Usage
-----

Place tombstones in your application where you suspect dead code. A tombstone takes a date (every date format
`strtotime()` understands), author and an optional label.

```php
<?php
class SomeClass {

    public function suspectedDeadCode() {
        tombstone('2015-08-18', 'scheb');
        // ... some code follows
    }
}
```

<a name="handlers-formatters"></a>Handlers and Formatters
-----------------------

Handlers write the information that was received from a tombstone to a target system (e.g. file system). Formatters are
used to serialize that information.

The bundle comes with some pre-defined handlers and formatters. You can create your own handlers and formatters by
implementing `Scheb\Tombstone\Handler\HandlerInterface` and `Scheb\Tombstone\Formatter\FormatterInterface`.

### Handlers

- `AnalyzerLogHandler`: Writes multiple log files to a directory. This format is used by default for
  [report generation](https://github.com/scheb/tombstone-analyzer), but if you don't like, you can have your own handler.
- `PsrLoggerHandler`: Connects a PSR-3 logger with the bundle.
- `StreamHandler`: Take a stream resource, stream identifier (`file://`, `ftp://`, etc.) or a file path as the target.

The `AnalyzerLogHandler` takes an optional size limit to pretend log files from taking too much space. In the end, it
doesn't make a difference if a tombstone was called 100 or a million times.

### Formatters

- `AnalyzerLogFormatter`: Machine-readable log-format, which is by default understood by the
  [report generator](https://github.com/scheb/tombstone-analyzer).
- `JsonFormatter`: Compact JSON string.
- `LineFormatter`: Human-readable log entry.

Handlers have a default formatter. The formatter can be changed by calling `setFormatter()`. 

Report Generation
-----------------

[scheb/tombstone-analyzer](https://github.com/scheb/tombstone-analyzer) is a library to analyze the code and the log
files written by `AnalyzerLogHandler`. The data is used to generate reports about dead and undead code in your project.

[Read more about report generation](https://github.com/scheb/tombstone-analyzer/blob/master/README.md).

Acknowledgments
---------------

Thanks to [Jordi Boggiano](https://github.com/Seldaek) for creating [Monolog](https://github.com/Seldaek/monolog), from
where I lend the handler/formatter concept.

License
-------
This library is available under the [MIT license](LICENSE).
