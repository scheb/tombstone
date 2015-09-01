scheb/tombstone
===============

Implements the concept of **Tombstones for PHP**.

Report generation provided by [scheb/tombstone-analyzer](https://github.com/scheb/tombstone-analyzer).

[![Build Status](https://travis-ci.org/scheb/tombstone.svg?branch=master)](https://travis-ci.org/scheb/tombstone)
[![PHP 7 ready](http://php7ready.timesplinter.ch/scheb/tombstone/badge.svg)](https://travis-ci.org/scheb/tombstone)
[![HHVM Status](http://hhvm.h4cc.de/badge/scheb/tombstone.svg)](http://hhvm.h4cc.de/package/scheb/tombstone)
[![Coverage Status](https://coveralls.io/repos/scheb/tombstone/badge.svg?branch=master&service=github)](https://coveralls.io/github/scheb/tombstone?branch=master)
[![Latest Stable Version](https://poser.pugx.org/scheb/tombstone/v/stable.svg)](https://packagist.org/packages/scheb/tombstone)
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

The bundle comes withe some pre-defined handlers and formatters. You can create your own handlers and formatters by
implementing `Scheb\Tombstone\Handler\HandlerInterface` and `Scheb\Tombstone\Formatter\FormatterInterface`.

### Handlers

- `AnalyzerLogHandler`: Writes multiple log files to a directory, that are used for [report generation](https://github.com/scheb/tombstone-analyzer).
- `PsrLoggerHandler`: Connects a PSR-3 logger with the bundle.
- `StreamHandler`: Take a stream resource, stream identifier (`file://`, `ftp://`, etc.) or a file path as the target.

The `AnalyzerLogHandler` takes an optional size limit to pretend log files from taking too much space. In the end it doesn't make a difference if a tombstone was called 100 or a million times.

### Formatters

- `AnalyzerLogFormatter`: Machine-readable log-format, which is used for [report generation](https://github.com/scheb/tombstone-analyzer).
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

Thanks to [Jordi Boggiano](https://github.com/Seldaek) for creating [Monolog](https://github.com/Seldaek/monolog), from where I lend the handler/formatter concept.

License
-------
This library is available under the [MIT license](LICENSE).
