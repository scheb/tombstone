<?php

// phpcs:ignoreFile
$var = 'foo';

// All valid arguments
tombstone('2020-01-01', 'author', 'label');

// Invalid arguments
tombstone(123, 1.23, false, $var, DIRECTORY_SEPARATOR, new \stdClass(), 'label');
