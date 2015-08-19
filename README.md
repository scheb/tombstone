scheb/tombstone
===============

Implements the concept of **Tombstones for PHP**.

<a href="http://www.youtube.com/watch?feature=player_embedded&v=29UXzfQWOhQ" target="_blank"><img src="http://img.youtube.com/vi/29UXzfQWOhQ/0.jpg" alt="Tombstone Youtube Video" width="240" height="180" border="10" /></a>

Inspired by: http://devblog.nestoria.com/post/115930183873/tombstones-for-dead-code

WARNING: The library is still work in progress. BC breaks will certainly happen as long as there is no stable release.

Installation
------------

1) Install via composer

```bash
$ composer require scheb/tombstone
```

2) Define a `tombstone()` function

You have to define a function `tombstone($date, $author, $label = null)`. The library is shipped with a default
implementation. Simply include `tombstone.php` in your bootstrap.

If you don't like the default implementation, you can implement the function on your own, as long as the signature is
the same (important for code analysis).

3) Configure the graveyard

All tombstones are sent to a "graveyard". By default the graveyard isn't doing anything with the tombstones. You have to
register a handler. What you usually want is the `StreamHandler` , which it writes human-readable information to a log file.

```php
$streamHandler = new \Scheb\Tombstone\Handler\StreamHandler("$logDir/tombstones.log");
\Scheb\Tombstone\GraveyardProvider::getGraveyard()->addHandler($streamHandler);
```

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

License
-------
This library is available under the [MIT license](LICENSE).
