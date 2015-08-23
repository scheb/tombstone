scheb/tombstone-analyzer
========================

**Report generation** for the **Tombstones** created with the [https://github.com/scheb/tombstone](scheb/tombstone) library.

<a href="http://www.youtube.com/watch?feature=player_embedded&v=29UXzfQWOhQ" target="_blank"><img src="http://img.youtube.com/vi/29UXzfQWOhQ/0.jpg" alt="Tombstone Youtube Video" width="240" height="180" border="10" /></a>

Inspired by: http://devblog.nestoria.com/post/115930183873/tombstones-for-dead-code

WARNING: The library is still work in progress. BC breaks will certainly happen as long as there is no stable release.


Installation
------------

Install via composer

```bash
$ composer require scheb/tombstone-analyzer
```

Usage
-----

Execute the tool on the command line to show the help dialog:

```bash
$ vendor/bin/tombstone
```

Basic usage:

```bash
$ vendor/bin/tombstone /path/to/php/sources /path/to/tombstone/logs
```

License
-------
This library is available under the [MIT license](LICENSE).
