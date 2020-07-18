Upgrading
=========

Here's an overview if you have to do any work when upgrading.

0.x to 1.x
----------

Packages
--------

The `scheb/tombstone`now is the main repository, containing all code related to the tombstone project. Please replace
the package `scheb/tombstone` with `scheb/tombstone-logger`.

- `scheb/tombstone-logger` provides the tombstone logging library
- `scheb/tombstone-analyzer` provides the command line tool for report generation

Tombstone Logger
----------------

The `Graveyard`-related classes are now located in the namespace `Scheb\Tombstone\Graveyard`:

- `Scheb\Tombstone\Graveyard\BufferedGraveyard`
- `Scheb\Tombstone\Graveyard\Graveyard`
- `Scheb\Tombstone\Graveyard\GraveyardInterface`
- `Scheb\Tombstone\Graveyard\GraveyardRegistry`

Please note that `Scheb\Tombstone\GraveyardProvider` was renamed to `Scheb\Tombstone\Graveyard\GraveyardRegistry`.

The constructor of the `Graveyard` class changed. Please use the more convenient
`Scheb\Tombstone\Graveyard\GraveyardBuilder` to configure and create a graveyard instance.

File `tombstone.php`, providing the `tombstone()` and located in the root directory of the `scheb/tombstone-logger`
package, was renamed to `tombstone-function.php`.
