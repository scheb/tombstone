scheb/tombstone-analyzer
========================

**Report generation** for the **Tombstones** created with the [scheb/tombstone](https://github.com/scheb/tombstone) library.

[![Build Status](https://travis-ci.org/scheb/tombstone-analyzer.svg?branch=master)](https://travis-ci.org/scheb/tombstone-analyzer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/scheb/tombstone-analyzer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/scheb/tombstone-analyzer/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/scheb/tombstone-analyzer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/scheb/tombstone-analyzer/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/scheb/tombstone-analyzer/v/stable.svg)](https://packagist.org/packages/scheb/tombstone-analyzer)
[![Total Downloads](https://poser.pugx.org/scheb/tombstone-analyzer/downloads)](https://packagist.org/packages/scheb/tombstone-analyzer)
[![License](https://poser.pugx.org/scheb/tombstone-analyzer/license.svg)](https://packagist.org/packages/scheb/tombstone-analyzer)

<a href="http://www.youtube.com/watch?feature=player_embedded&v=29UXzfQWOhQ" target="_blank"><img src="http://img.youtube.com/vi/29UXzfQWOhQ/0.jpg" alt="Tombstone Youtube Video" width="240" height="180" border="10" /></a>

Inspired by: http://devblog.nestoria.com/post/115930183873/tombstones-for-dead-code

WARNING: The library is still work in progress. BC breaks will certainly happen as long as there is no stable release.


Installation
------------

Install via composer

```bash
$ composer require scheb/tombstone-analyzer
```

Composer automatically creates an executable binary `vendor/bin/tombstone`.

Usage
-----

Execute the tool on the command line to show the help dialog:

```bash
$ tombstone
```

Basic usage:

```bash
$ tombstone /path/to/php/sources /path/to/tombstone/logs
```

Generate a HTML report:

```bash
$ tombstone /path/to/php/sources /path/to/tombstone/logs --report-html=/report/target/directory
```

Contribute
----------
You're welcome to [contribute](https://github.com/scheb/tombstone-analyzer/graphs/contributors) to this library by
creating a pull requests or feature request in the issues section. For pull requests, please follow these guidelines:

- Symfony code style
- PHP7.1 type hints for everything (including: return types, `void`, nullable types)
- Please add/update test cases
- Test methods should be named `[method]_[scenario]_[expected result]`

To run the test suite install the dependencies with `composer install` and then execute `bin/phpunit`.

Acknowledgments
---------------

Thanks to [Sebastian Bergmann](https://github.com/sebastianbergmann) for letting me re-use parts of his code and the template files.

License
-------
This library is available under the [MIT license](LICENSE).
