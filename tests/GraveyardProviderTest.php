<?php
namespace Scheb\Tombstone\Tests;

use Scheb\Tombstone\GraveyardProvider;

class GraveyardProviderTest extends \PHPUnit_Framework_TestCase
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
        $graveyard = $this->getMockBuilder('Scheb\Tombstone\Graveyard')->getMock();
        GraveyardProvider::setGraveyard($graveyard);
        $returnValue = GraveyardProvider::getGraveyard();
        $this->assertSame($graveyard, $returnValue);
    }
}
