scheb/tombstone-logger
======================

This package provides the infrastructure to place tombstones in your codebase and log invocations.

Index
-----

- [Installation](installation.md)
- [Graveyard Settings and Buffering](graveyard.md)
- [Handlers and Formatters](handlers_formatters.md)

Usage
-----

Place tombstones in your application where you suspect dead code. A tombstone can take any list of **string arguments**,
but it's recommended to stick to some convention within your application.

```php
<?php
class SomeClass {

    public function suspectedDeadCode() {
        tombstone('2015-08-12', 'scheb', 'method tombstone');
        // Some code follows
        if (/* condition */) {
            // This tombstone will only be called when the condition was true
            tombstone('2015-08-18', 'scheb', 'conditional tombstone');
        }
    }
}
```

The fully qualified name of the tombstone function and its arguments are used by the [analyzer](../analyzer/index.md) to
match tombstone calls to the source code. Therefore, once set, you must not change the arguments of a tombstone – except
you want it to be treated as a whole new tombstone. The more unique the combination of arguments, position (file/line)
and surrounding function/method is, the more reliable the matching will be.

As you can see in the example above, one of the tombstone arguments may be a date, which is intended to be the date when
the tombstone was introduced into the codebase. The library will take the first string that can be interpreted as a date
(every format `strtotime()` understands) as the tombstone's date. This date is used to calculate the age of a tombstone.
