<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

// phpcs:disable Symfony.NamingConventions.ValidClassName.InvalidAbstractName
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * Backwards compatibility for PHPUnit 7.5.
     */
    public function expectExceptionMessageMatches(string $regularExpression): void
    {
        if (method_exists(PHPUnitTestCase::class, 'expectExceptionMessageMatches')) {
            parent::expectExceptionMessageMatches($regularExpression);
        } else {
            parent::expectExceptionMessageRegExp($regularExpression);
        }
    }
}
