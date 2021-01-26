Installation
============

## Step 1: Install with Composer

```bash
composer require scheb/tombstone-analyzer
```

If you don't need the analyzer on a production system (e.g. you have a separate CI system for that purpose), you may
want to install it as a dev dependency:

```bash
composer require scheb/tombstone-analyzer --dev
```

## Step 2: Configuration file

Create a YAML configuration file that contains at least the minimum configuration:

```yaml
# tombstone.yml
source_code:
    # Where your source files are located. This should point to the same folder as the rootDirectory
    # that you have configured for the tombstone-logger component
    root_directory: ./src
logs:
    # Where your tombstone log files are located
    directory: ./logs/tombstones
```

Relative paths in the configuration file are resolved relative to the file's location.

## Step 3: Run the analyzer

Run `tombstone-analyzer` from your Composer `bin` directory:

```bash
# Looks for a configuration file named tombstone.yml
vendor/bin/tombstone-analyzer

# Alternatively, explicitly set the path to the configuration file
vendor/bin/tombstone-analyzer -c path/to/your-config-file.yml
```

## Further steps

The analyzer doesn't generate a report yet. See available [Report Formats](report_formats.md) and how to configure them.

You may also want to have a look at the full [Configuration Reference](configuration.md) to refine your configuration.
