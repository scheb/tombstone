<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests;

use Scheb\Tombstone\Graveyard;
use Scheb\Tombstone\GraveyardInterface;
use Scheb\Tombstone\GraveyardRegistry;

class GraveyardProviderTest extends TestCase
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
