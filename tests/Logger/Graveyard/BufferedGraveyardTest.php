<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Logger\Graveyard;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Logger\Graveyard\BufferedGraveyard;
use Scheb\Tombstone\Logger\Graveyard\Graveyard;
use Scheb\Tombstone\Logger\Graveyard\GraveyardInterface;
use Scheb\Tombstone\Tests\TestCase;

class BufferedGraveyardTest extends TestCase
{
    /**
     * @var MockObject|GraveyardInterface
     */
    private $innerGraveyard;

    /**
     * @var Graveyard
     */
    private $graveyard;

    protected function setUp(): void
    {
        $this->innerGraveyard = $this->createMock(GraveyardInterface::class);
        $this->graveyard = new BufferedGraveyard($this->innerGraveyard);
    }

    /**
     * @test
     */
    public function tombstone_tombstoneInvoked_notAddToInnerGraveyard(): void
    {
        $this->innerGraveyard
            ->expects($this->never())
            ->method($this->anything());

        $this->graveyard->tombstone(['args'], ['trace1'], ['metaField' => 'metaValue']);
    }

    /**
     * @test
     */
    public function tombstone_autoFlushEnabled_directlyAddToInnerGraveyard(): void
    {
        $this->innerGraveyard
            ->expects($this->once())
            ->method('tombstone')
            ->with(['args'], ['trace1'], ['metaField' => 'metaValue']);

        $this->graveyard->setAutoFlush(true);
        $this->graveyard->tombstone(['args'], ['trace1'], ['metaField' => 'metaValue']);
    }

    /**
     * @test
     */
    public function flush_tombstonesBuffered_addBufferedTombstonesAndFlush(): void
    {
        $this->innerGraveyard
            ->expects($this->exactly(2))
            ->method('tombstone')
            ->withConsecutive(
                [['args'], ['trace1'], ['metaField' => 'metaValue1']],
                [['args'], ['trace2'], ['metaField' => 'metaValue2']]
            );

        $this->innerGraveyard
            ->expects($this->exactly(2))
            ->method('flush');

        $this->graveyard->tombstone(['args'], ['trace1'], ['metaField' => 'metaValue1']);
        $this->graveyard->flush();

        $this->graveyard->tombstone(['args'], ['trace2'], ['metaField' => 'metaValue2']);
        $this->graveyard->flush();
    }
}
