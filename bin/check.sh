#!/bin/bash
php-cs-fixer fix
./vendor/bin/phpcs --standard=php_cs.xml ./app ./src ./tests
./vendor/bin/phpunit
