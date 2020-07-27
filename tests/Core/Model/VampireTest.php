<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Core\Model;

use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Tests\TestCase;
use Scheb\Tombstone\Tests\VampireFixture;

class VampireTest extends TestCase
{
    /**
     * @test
     */
    public function withTombstone_differentTombstoneObjectGiven_returnDuplicateWithThatTombstone()
    {
        $tombstone = $this->createMock(Tombstone::class);
        $vampire = VampireFixture::getVampire();
        $newVampire = $vampire->withTombstone($tombstone);

        $this->assertSame($tombstone, $newVampire->getTombstone());
        $this->assertEquals($vampire->getInvocationDate(), $newVampire->getInvocationDate());
        $this->assertEquals($vampire->getInvoker(), $newVampire->getInvoker());
        $this->assertSame($vampire->getStackTrace(), $newVampire->getStackTrace());
        $this->assertEquals($vampire->getMetadata(), $newVampire->getMetadata());
    }
}
