<?php
namespace Scheb\Tombstone\Tests;

use Scheb\Tombstone\Tests\Fixtures\TraceFixture;
use Scheb\Tombstone\Vampire;

class VampireTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createFromCall_dataGiven_returnCorrectlyConstructedVampire()
    {
        $stackTrace = TraceFixture::getTraceFixture();
        $vampire = Vampire::createFromCall('2015-08-19', 'author', 'label', $stackTrace);

        $this->assertInstanceOf('Scheb\Tombstone\Vampire', $vampire);
        $this->assertInstanceOf('Scheb\Tombstone\Tombstone', $vampire->getTombstone());
        $this->assertEquals('2015-08-19', $vampire->getTombstoneDate());
        $this->assertEquals('author', $vampire->getAuthor());
        $this->assertEquals('label', $vampire->getLabel());
        $this->assertEquals('/path/to/file1.php', $vampire->getFile());
        $this->assertEquals(11, $vampire->getLine());
        $this->assertEquals('containingMethodName', $vampire->getMethod());
        $this->assertEquals('/path/to/file1.php:11', $vampire->getPosition());
        $this->assertEquals('invokerMethodName', $vampire->getInvoker());

        $invocationDate = strtotime($vampire->getInvocationDate());
        $this->assertEquals(time(), $invocationDate, null, 5);
    }
}
