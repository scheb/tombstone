Configuration Reference
=======================

You have to provide at least the minimum configuration in a YAML file:

```yaml
source_code:
    # Where your source files are located. This should point to the same folder as the rootDirectory
    # that you have configured for the tombstone-logger component.
    root_directory: ./src
logs:
    # Where your tombstone log files are located
    directory: ./logs/tombstones
```

Relative paths in the configuration file are resolved relative to the file's location.

This is an overview of all the configuration options available:

```yaml
# Where your source code is located
source_code:
    # Where your source files are located. This should point to the same folder as the rootDirectory
    # that you have configured for the tombstone-logger component.
    root_directory: ./src

# How the analyzer gets the list of active tombstones
tombstones:
    # Use PHP parser to extract all currently configured tombstones from the source code
    parser:
        # Exclude directories within the source directory (see symfony/finder's "exclude" option)
        # Default: none
        excludes:
          - tests

        # Name pattern of files to be included (see symfony/finder's "name" option)
        # Default: *.php
        names:
          - "*.php7"  # For example

        # Name pattern of files to be excluded (see symfony/finder's "notName" option)
        # Default: none
        not_names:
          - "*Test.php"  # For example

        # Fully qualified name of the tombstone functions
        # If not set, defaults to "tombstone"
        function_names:
          - tombstone                 # A function in the global scope
          - Acme\Tombstone\tombstone  # A function in the Acme\Tombstone namespace

# How the analyzer gets the tombstone logs
logs:
    # This is the directory where log files from AnalyzerLogHandler are located. All *.tombstone files
    # including sub-directories will be processed.
    directory: logs

    # Alternatively, if you can't write/read logs in the analyzer file format, you can configure a
    # custom log provider, that allows you read read logs from any source in any format.
    custom:
        class: Acme\Tombstone\CustomLogProvider

        # Optional, in case the autoloader doesn't automatically find the class file
        file: src/tombstone/CustomLogProvider.php

# Report generation options. See the "Report Formats" documentation for more details on this.
report:
    php: report/tombstone-report.php   # Generate a PHP dump of the result in this file
    checkstyle: report/checkstyle.xml  # Generate a Checkstyle report in this file
    html: report   # Generate a HTML report in this folder
    console: true  # Display report on the console (default: false)
```

The tool uses [symfony/finder](https://symfony.com/doc/current/components/finder.html) to scan the source directory, so
have a look at its documentation to understand the filter options.
