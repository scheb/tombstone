scheb/tombstone-analyzer
========================

**Report generation** for the **Tombstones** created with the [scheb/tombstone](https://github.com/scheb/tombstone) library.

[![Build Status](https://travis-ci.org/scheb/tombstone-analyzer.svg?branch=master)](https://travis-ci.org/scheb/tombstone-analyzer)
[![PHP 7 ready](http://php7ready.timesplinter.ch/scheb/tombstone-analyzer/badge.svg)](https://travis-ci.org/scheb/tombstone-analyzer)
[![HHVM Status](http://hhvm.h4cc.de/badge/scheb/tombstone-analyzer.svg)](http://hhvm.h4cc.de/package/scheb/tombstone-analyzer)
[![Coverage Status](https://coveralls.io/repos/scheb/tombstone-analyzer/badge.svg?branch=master&service=github)](https://coveralls.io/github/scheb/tombstone-analyzer?branch=master)
[![Latest Stable Version](https://poser.pugx.org/scheb/tombstone-analyzer/v/stable.svg)](https://packagist.org/packages/scheb/tombstone-analyzer)
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

Acknowledgments
---------------

Thanks to [Sebastian Bergmann](https://github.com/sebastianbergmann) for letting me re-use parts of his code and the template files.

License
-------
This library is available under the [MIT license](LICENSE).
