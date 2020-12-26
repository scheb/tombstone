Custom Log Providers
====================

You may not want to use the default analyzer log format, but instead write logs in a custom format to a custom logging
system. In the tombstone logger package this is supported by implementing a custom log handler.

The analyzer requires a "log provider" to read log data from your custom log storage. Implement a class with the
interface `Scheb\Tombstone\Analyzer\Log\LogProviderInterface` for that purpose:

```php
<?php
namespace Acme\Tombstone;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Analyzer\Log\LogProviderInterface;
use Scheb\Tombstone\Core\Model\Vampire;

class LogProvider implements LogProviderInterface
{
    /**
     * @param array $config All config options from the YAML file. Additional config options are passed through as-is.
     * @param ConsoleOutputInterface $consoleOutput Can be used to write output to the console.
     */
    public static function create(array $config, ConsoleOutputInterface $consoleOutput): LogProviderInterface
    {
        return new self();
    }

    /**
     * Must return an iterable (array or \Traversable) of Vampire objects.
     *
     * @return iterable<int, Vampire>
     */
    public function getVampires(): iterable
    {
        // Here goes the logic to retrieve log data
    }
}
```

The static `create()` function is there to create an instance of your log provider. You can read configuration data from
the YAML configuration via the `$config` array. Any additional config options from that file, that aren't used by the
analyzer, are passed through as-is, allowing you to pass custom configuration to your implementation.

`getVampires` is the method to retrieve the tombstone log data from your logging system. It has to be an iterable
(`array` or `\Traversable`) of `Scheb\Tombstone\Core\Model\Vampire` objects.

Once you have implemented your custom log provider, configure it in the analyzer's YAML config file:

```yaml
logs:
    custom:
        class: Acme\Tombstone\CustomLogProvider

        # Optional, in case the autoloader doesn't automatically find the class file
        file: src/tombstone/CustomLogProvider.php
```

When you have a custom log provider configured, it is no longer necessary to have a logs `directory` configured.
