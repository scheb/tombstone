<?php

namespace Scheb\Tombstone\Tests;

use Scheb\Tombstone\GraveyardProvider;

class GraveyardProviderTest extends TestCase
{
    /**
     * @test
     */
    public function getGraveyard_notSet_returnDefaultGraveyard()
    {
        $returnValue = GraveyardProvider::getGraveyard();
        $this->assertInstanceOf('Scheb\Tombstone\Graveyard', $returnValue);
    }

    /**
     * @test
     */
    public function setGraveyard_newGraveyard_exchangeGraveyard()
    {
        $graveyard = $this->createMock('Scheb\Tombstone\Graveyard');
        GraveyardProvider::setGraveyard($graveyard);
        $returnValue = GraveyardProvider::getGraveyard();
        $this->assertSame($graveyard, $returnValue);
    }
}
