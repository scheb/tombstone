scheb/tombstone-analyzer
========================

This package takes log data from `scheb/tombstone-logger` and generates reports in various formats.

Index
-----

- [Installation](installation.md)
- [Custom Log Providers](log_providers.md)
- [Configuration Reference](configuration.md)
- [Report Formats](report_formats.md)

Usage
----

You have to provide a YAML configuration file with the minimum configuration. See the
[Configuration Reference](configuration.md) to create such a file.

Run `tombstone-analyzer` from your Composer `bin` directory:

```bash
# Looks for a configuration file named tombstone.yml
vendor/bin/tombstone-analyzer

# Alternatively, explicitly set the path to the configuration file
vendor/bin/tombstone-analyzer -c path/to/your-config-file.yml
```

