scheb/tombstone
===============

[![Build Status](https://travis-ci.org/scheb/tombstone.svg?branch=master)](https://travis-ci.org/scheb/tombstone)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/scheb/tombstone/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/scheb/tombstone/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/scheb/tombstone/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/scheb/tombstone/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/scheb/tombstone/v/stable.svg)](https://packagist.org/packages/scheb/tombstone)
[![Total Downloads](https://poser.pugx.org/scheb/tombstone/downloads)](https://packagist.org/packages/scheb/tombstone)
[![License](https://poser.pugx.org/scheb/tombstone/license.svg)](https://packagist.org/packages/scheb/tombstone)

Implements the concept of **tombstones for dead code detection in PHP**. If you're not familiar with that concept, read below.

This library provides you with a toolbox to place and track tombstones in your code. [scheb/tombstone-analyzer](https://github.com/scheb/tombstone-analyzer) can be used for report generation.

What are Tombstones?
--------------------

To get the basic idea, watch David Schnepper's 5 minute talk from Velocity Santa Clara 2014.

<a href="http://www.youtube.com/watch?feature=player_embedded&v=29UXzfQWOhQ" target="_blank"><img src="http://img.youtube.com/vi/29UXzfQWOhQ/0.jpg" alt="Tombstone Youtube Video" width="240" height="180" border="10" /></a>

When you want to identify and clean-up dead code in a project, static code analysis tools are the weapon of choice. But these tools have some limitions, especially in a dynamic language like PHP:

- They can only tell you, if a piece of code is referenced, not if it's actually used.
- They cannot resolve dynamic or generated call paths.

Tombstones provide a way to track if a piece of code is actually invoked. **They are executable markers in your code**, that you can place where you suspect dead code. Then, you collect tombstone invocations on production. After a while, the logs will tell you, which tombstones are dead and which ones aren't (the so called "vampires").

Installation
------------

1) Install via composer

```bash
$ composer require scheb/tombstone
```

2) Define a `tombstone()` function

You have to define a function `tombstone(string ...$arguments): void`. The library is shipped with a default
implementation. Include `tombstone.php` from the libraries root directory in your bootstrap. If you don't like the
default implementation, you can implement the function on your own. The only requirement is to have strings as arguments
(important for code analysis in the tombstone-analyzer library).

3) Configure the graveyard

All tombstones are sent to a "graveyard". By default the graveyard isn't doing anything with the tombstones. You have to
register a handler. What you usually want is the `StreamHandler`, which writes human-readable information to a log file,
or `AnalyzerLogHandler`, which writes log files compatible to
[scheb/tombstone-analyzer](https://github.com/scheb/tombstone-analyzer).

```php
use Scheb\Tombstone\GraveyardProvider;
use Scheb\Tombstone\Handler\StreamHandler;

$streamHandler = new StreamHandler("$logDir/tombstones.log");
GraveyardProvider::getGraveyard()->addHandler($streamHandler);

$analyzerLogHandler = new AnalyzerLogHandler($logDir);
GraveyardProvider::getGraveyard()->addHandler($analyzerLogHandler);
```

[Read more about handlers and formatters below](#handlers-and-formatters).

### Buffered graveyard

The default `Graveyard` class is writing tombstones to the handlers right away. If you want to delay the write - e.g.
for performance reasons - and flush the tombstones at a later point, use the `BufferedGraveyard`.

```php
use Scheb\Tombstone\BufferedGraveyard;
use Scheb\Tombstone\GraveyardProvider;

$bufferedGraveyard = new BufferedGraveyard(GraveyardProvider::getGraveyard());
GraveyardProvider::setGraveyard($bufferedGraveyard);

// Call the flush manually
$bufferedGraveyard->flush();

// Or flush in the shutdown handler
register_shutdown_function(function () use ($bufferedGraveyard) {
    $bufferedGraveyard->flush();
});
```

### Relative Path Logs

By default the absolute file path is used in the logs. This can be in problem in some environments (e.g. when your 
deployment process creates a new directory for every release). If you tell the root path to the graveyard, it will log
the relative file path instead. This makes log files exchangeable between different servers/environments/installation
paths.

```php
GraveyardProvider::getGraveyard()->setRootDir('/path/to/src');
```

It is especially useful, if you want to collect logs files from multiple production servers to run an overall analysis
on a central server.

Usage
-----

Place tombstones in your application where you suspect dead code. A tombstone can take any list of string arguments, but
it's recommended to stick to some pattern within your application. A good practise is to set the date when the tombstone
was added and an identifier who added it.

Please note, that the arguments are used by tombstone-analyzer to match tombstones in the code. Therefore, once set, you
should not change the values of a tombstone. The more unique the combination is within a file/method, the more reliable
the matching will be.

The first string that can be interpreted as a date (every date format `strtotime()` understands) will be taken as the
tombstones date, which is used to calculate the age of a tombstone. 

```php
<?php
class SomeClass {

    public function suspectedDeadCode() {
        tombstone('2015-08-18', 'scheb');
        // ... some code follows
    }
}
```

Handlers and Formatters
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

Contribute
----------
You're welcome to [contribute](https://github.com/scheb/tombstone/graphs/contributors) to this library by creating a
pull requests or feature request in the issues section. For pull requests, please follow these guidelines:

- Symfony code style (use `php_cs.xml` to configure the code style in your IDE)
- PHP7.1 type hints for everything (including: return types, `void`, nullable types)
- `declare(strict_types=1)` must be used
- Please add/update test cases
- Test methods should be named `[method]_[scenario]_[expected result]`

To run the test suite install the dependencies with `composer install` and then execute `bin/phpunit`.

Acknowledgments
---------------

The library is heavly inspired by [Nestoria.com's implementation](http://devblog.nestoria.com/post/115930183873/tombstones-for-dead-code) of the tombstone concept.

Thanks to [Jordi Boggiano](https://github.com/Seldaek) for creating [Monolog](https://github.com/Seldaek/monolog), from
where I lend the handler/formatter concept.

License
-------
This library is available under the [MIT license](LICENSE).
