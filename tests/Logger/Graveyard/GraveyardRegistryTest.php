<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Logger\Graveyard;

use Scheb\Tombstone\Logger\Graveyard\Graveyard;
use Scheb\Tombstone\Logger\Graveyard\GraveyardInterface;
use Scheb\Tombstone\Logger\Graveyard\GraveyardRegistry;
use Scheb\Tombstone\Tests\TestCase;

class GraveyardRegistryTest extends TestCase
{
    /**
     * @test
     */
    public function getGraveyard_notSet_returnDefaultGraveyard(): void
    {
        $returnValue = GraveyardRegistry::getGraveyard();
        $this->assertInstanceOf(Graveyard::class, $returnValue);
    }

    /**
     * @test
     */
    public function setGraveyard_newGraveyard_exchangeGraveyard(): void
    {
        $graveyard = $this->createMock(GraveyardInterface::class);
        GraveyardRegistry::setGraveyard($graveyard);
        $returnValue = GraveyardRegistry::getGraveyard();
        $this->assertSame($graveyard, $returnValue);
    }
}
