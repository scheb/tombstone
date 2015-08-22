<?php
namespace Scheb\Tombstone;

use Scheb\Tombstone\Handler\HandlerInterface;
use Scheb\Tombstone\Tracing\TraceProvider;

class Graveyard
{

    /**
     * @var HandlerInterface[]
     */
    private $handlers;

    /**
     * @param HandlerInterface[] $handlers
     */
    public function __construct(array $handlers = array())
    {
        $this->handlers = $handlers;
        $this->traceProvider = new TraceProvider(1);
    }

    /**
     * @param HandlerInterface $handler
     */
    public function addHandler(HandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param string $date
     * @param string $author
     * @param string|null $label
     * @param array $trace
     */
    public function tombstone($date, $author, $label, array $trace)
    {
        $vampire = Vampire::createFromCall($date, $author, $label, $trace);
        foreach ($this->handlers as $handler) {
            $handler->log($vampire);
        }
    }

    public function flush()
    {
        foreach ($this->handlers as $handler) {
            $handler->flush();
        }
    }
}
