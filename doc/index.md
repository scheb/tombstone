scheb/tombstone
===============

Implements the concept of **tombstones for dead code detection in PHP**.

The library provides you with a toolbox to place, track and evaluate tombstones in your code.

<p align="center"><img alt="Logo" src="tombstone-logo.svg" width="300" /></p>

It consists of multiple components:

scheb/tombstone-logger
----------------------

This package provides the infrastructure to place tombstones in your codebase and log invocations.

- [Installation](logger/installation.md)
- [Documentation Overview](logger/index.md)

scheb/tombstone-analyzer
----------------------

This package takes log data from `scheb/tombstone-logger` and generates reports in various formats. For example an HTML
report:

[![Dashboard view](analyzer/dashboard-small.png)](analyzer/dashboard.png) [![Code view](analyzer/code-small.png)](analyzer/code.png)

- [Installation](analyzer/installation.md)
- [Documentation Overview](analyzer/index.md)
