Upgrading
=========

Here's an overview if you have to do any work when upgrading.

0.x to 1.x
----------

### Packages

The `scheb/tombstone`now is the main repository, containing all code related to the tombstone project. Please replace
the package `scheb/tombstone` with `scheb/tombstone-logger`.

- `scheb/tombstone-logger` provides the tombstone logging library
- `scheb/tombstone-analyzer` provides the command line tool for report generation

### Tombstone Logger (previously `scheb/tombstone`)

The classes of the library have been moved to the `Scheb\Tombstone\Logger` namespace.

That means the `Graveyard`-related classes are now located in the namespace `Scheb\Tombstone\Logger\Graveyard`. Please
note that`Scheb\Tombstone\GraveyardProvider` was renamed to `Scheb\Tombstone\Logger\Graveyard\GraveyardRegistry`.

Handlers and formatters have been moved respectively to the `Scheb\Tombstone\Logger\Handler` and
`Scheb\Tombstone\Logger\Formatter` namespace.

The constructor of the `Graveyard` class changed. Please use the more convenient
`Scheb\Tombstone\Logger\Graveyard\GraveyardBuilder` to configure and create a graveyard instance.

File `tombstone.php`, providing the `tombstone()` and located in the root directory of the `scheb/tombstone-logger`
package, was renamed to `tombstone-function.php`.
