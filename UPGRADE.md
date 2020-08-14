Upgrading
=========

Here's an overview if you have to do any work when upgrading.

0.x to 1.x
----------

### Packages

The `scheb/tombstone` repository is now the main repository, containing all code related to the
tombstone project. Please replace the package `scheb/tombstone` with `scheb/tombstone-logger`.

- `scheb/tombstone-logger` provides the tombstone logging library
- `scheb/tombstone-analyzer` provides the command line tool for report generation

### Tombstone Logger (previously `scheb/tombstone`)

Tombstone logs written by version 0.x are not compatible with 1.x. So when you upgrade, please purge your tombstone logs
and wait for some new data to be logged.

The classes of this package have been moved to the `Scheb\Tombstone\Logger` namespace.

Handlers and formatters have been moved respectively to the `Scheb\Tombstone\Logger\Handler` and
`Scheb\Tombstone\Logger\Formatter` namespace.

All the `Graveyard`-related classes are now located in the namespace `Scheb\Tombstone\Logger\Graveyard`.

`Scheb\Tombstone\GraveyardProvider` was renamed to `Scheb\Tombstone\Logger\Graveyard\GraveyardRegistry`.

The constructor of the `Graveyard` class changed. Please use the more convenient
`Scheb\Tombstone\Logger\Graveyard\GraveyardBuilder` to configure and create a graveyard instance.

The `Scheb\Tombstone\Logger\Graveyard\GraveyardInterface` has changed, please update your own implementations.

File `tombstone.php`, providing the `tombstone()` and located in the root directory of the `scheb/tombstone-logger`
package, was renamed to `tombstone-function.php`.

### Tombstone Analyzer

If you used the YAML configuration before (since `scheb/tombstone-analyzer` version 0.4.0), you need to update your
configuration file. If you used an older version, you need to create such a config file. See the
[Configuration Reference](doc/analyzer/configuration.md).
