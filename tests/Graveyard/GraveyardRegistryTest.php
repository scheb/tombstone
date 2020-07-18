<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Graveyard;

use Scheb\Tombstone\Graveyard\Graveyard;
use Scheb\Tombstone\Graveyard\GraveyardInterface;
use Scheb\Tombstone\Graveyard\GraveyardRegistry;
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
