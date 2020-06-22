<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Test;

use Scheb\Tombstone\Graveyard;
use Scheb\Tombstone\GraveyardInterface;
use Scheb\Tombstone\GraveyardProvider;

class GraveyardProviderTest extends TestCase
{
    /**
     * @test
     */
    public function getGraveyard_notSet_returnDefaultGraveyard(): void
    {
        $returnValue = GraveyardProvider::getGraveyard();
        $this->assertInstanceOf(Graveyard::class, $returnValue);
    }

    /**
     * @test
     */
    public function setGraveyard_newGraveyard_exchangeGraveyard(): void
    {
        $graveyard = $this->createMock(GraveyardInterface::class);
        GraveyardProvider::setGraveyard($graveyard);
        $returnValue = GraveyardProvider::getGraveyard();
        $this->assertSame($graveyard, $returnValue);
    }
}
